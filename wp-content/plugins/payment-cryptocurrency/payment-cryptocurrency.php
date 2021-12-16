<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// Exit if accessed directly
/**
 * Plugin Name: Pasarela de pago - criptomonedas
 * Plugin URI: #
 * Description: Pasarela de pago de moneda digital para E-Commerce
 * Version: 1.0.0
 * Author: PACMEC
 * Author URI: #
 * Developer: PACMEC
 * Developer URI: #
 * Text Domain: payment-cryptocurrency
 * Domain Path: /lang
 */
define( 'CWOO_VERSION', '1.0.0' );
define( 'CWOO_FILE', 'cryptocurrency-payment-gateway/cryptocurrency-payment-gateway.php' );
define( 'CWOO_PLUGIN_PATH', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) . '/' );
define( 'CWOO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CW_PAYMENT_METHOD_ID', 'cryptopay' ); // Lowercase, no special characters

// Determine log file directory
$log_dir = defined( 'WC_LOG_DIR' ) ? WC_LOG_DIR : trailingslashit( wp_upload_dir()['basedir'] ) . 'wc-logs/';
define( 'CW_LOG_DIR', $log_dir );

// Includes
require_once __DIR__ . '/includes/include-all.php';

// Remove Redux Demo Mode
function removeCryptoWooDemoModeLink() {
	if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
		remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks' ), null, 2 );
	}
	if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
		remove_action( 'admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );
	}
}
add_action( 'init', 'removeCryptoWooDemoModeLink' );

$admin_main = new CW_AdminMain();

register_activation_hook( __FILE__, array( $admin_main, 'cryptowoo_cron_activation_schedule' ) );

// Multisite compatible plugin activation hook
register_activation_hook( __FILE__, 'cw_multisite_activate' );

/**
 * Multisite compatible plugin activation
 *
 * @param $networkwide
 */
function cw_multisite_activate( $networkwide ) {
	global $wpdb;
	// Check if it is a multisite network
	if ( is_multisite() ) {
		// Check if the plugin has been activated on the network or on a single site
		if ( $networkwide ) {
			// TODO maybe add multisite activation routine that honors CWOO_MULTISITE
			deactivate_plugins( plugin_basename( __FILE__ ), true, true );
			wp_die( esc_html__( 'Network Activation failed: You have to activate CryptoPay on each site seperately.', 'cryptopay' ) );
		} else {
			// Activated on a single site, in a multi-site
			cryptowoo_plugin_activate( $wpdb->blogid );
		}
	} else {
		// Activated on a single site
		cryptowoo_plugin_activate( $wpdb->blogid );
	}
}

// Remove scheduled crontasks
register_deactivation_hook( __FILE__, 'cryptowoo_plugin_deactivate' );

// Internationalization
	// add_action('plugins_loaded', 'cryptowoo_textdomain');

// Plugin init
	add_action( 'plugins_loaded', 'woocommerce_cryptowoo_init', 1 );

// Plugin updates
	add_action( 'cryptowoo_cron_update_order_meta_txids', array( $admin_main, 'do_cryptowoo_cron_update_order_meta_txids' ) );

// Plugin requirements check
	add_action( 'admin_init', array( $admin_main, 'cw_is_woocommerce_active' ) );

// Misconfiguration notice
	add_action( 'admin_notices', array( $admin_main, 'cryptowoo_misconfig_notice' ) );

// Update handler
	add_action( 'admin_init', array( $admin_main, 'cw_handle_updates' ) );

// Custom WP cron schedules
	add_filter( 'cron_schedules', array( $admin_main, 'cron_add_schedules' ) );
	add_action( 'wp', array( $admin_main, 'cryptowoo_cron_activation_schedule' ) );

// Exchange rate update and payment processing function executed by cron
	add_action( 'cryptowoo_cron_action', array( $admin_main, 'do_cryptowoo_cron_action' ) );

// Display settings
	add_action( 'wp', array( 'CW_Formatting', 'display_currency_settings' ) );

	// Hook into WooCommerce Currency Switcher plugin
	add_filter( 'woocs_currency_data_manipulation', 'add_currencies_to_woocs', 9999, 1 );

// Woocs crypto amount formatting with dynamic decimals and ensure minimum positive value (e.g. 1 satoshi).
	add_filter( 'woocs_raw_woocommerce_price', 'format_woocs_crypto_amount' );

	// Hook into WooCommerce Multilingual (WCML)
	add_filter( 'wcml_exchange_rates', 'cwwcml_filter_exchange_rates', 10, 1 );

	// Add CryptoPay supported currencies to woocommerce currencies list
	add_filter( 'woocommerce_currencies', 'cw_add_woocommerce_currencies' );
	add_filter( 'woocommerce_sections_general', 'cw_remove_unsupported_woocommerce_currencies' );

// Checkout field validation & cart update handling
	add_action( 'woocommerce_checkout_process', array( 'CW_Formatting', 'payment_currency_checkout_validation' ) );
	add_action( 'woocommerce_checkout_process', array( 'CW_Formatting', 'payment_currency_checkout_maybe_force_store_currency' ) );
	add_action( 'woocommerce_checkout_order_processed', array( 'CW_Formatting', 'payment_currency_checkout_maybe_reset_store_currency' ) );
	add_action( 'woocommerce_checkout_update_order_meta', array( 'CW_Formatting', 'cryptowoo_payment_currency_update_order_meta' ) );

// Order details for cryptocurrency orders
	add_action( 'woocommerce_view_order', array( 'CW_Formatting', 'display_crypto_payment_info' ) );
	add_action( 'woocommerce_thankyou_' . CW_PAYMENT_METHOD_ID, array( 'CW_Formatting', 'display_crypto_payment_info' ) );

// Order details in WooCommerce emails
	add_action( 'woocommerce_email_order_details', array( 'CW_Formatting', 'display_order_email_info' ), 99, 4 );

// Order status update for payment page background polling
	add_action( 'wp_ajax_nopriv_poll', 'poll_callback' );
	add_action( 'wp_ajax_poll', 'poll_callback' );

// Order status update for button on payment page
	add_action( 'wp_ajax_nopriv_check_receipt', 'poll_callback' );
	add_action( 'wp_ajax_check_receipt', 'poll_callback' );

// API and Master Public Key integrity
	add_action( 'cryptowoo_integrity_check', array( $admin_main, 'do_cryptowoo_integrity_check' ) );

// Order table sorting
	add_filter( 'manage_edit-shop_order_columns', array( $admin_main, 'cryptowoo_order_data' ), 11 );
	add_action( 'manage_shop_order_posts_custom_column', array( $admin_main, 'cryptowoo_columns_values_function' ), 2 );
	add_filter( 'manage_edit-shop_order_sortable_columns', array( $admin_main, 'cryptowoo_columns_sort_function' ) );

// Scripts and styles
	add_action( 'admin_enqueue_scripts', 'cw_enqueue_admin_scripts' );
	add_action( 'wp_enqueue_scripts', 'cryptowoo_scripts' );

// Ajax functions for admin backend
	add_action( 'wp_ajax_cw_reset_error_counter', array( $admin_main, 'cw_reset_error_counter_callback' ) );
	add_action( 'wp_ajax_cw_reset_exchange_rate_table', array( $admin_main, 'cw_reset_exchange_rate_table_callback' ) );
	add_action( 'wp_ajax_cw_reset_payments_table', array( $admin_main, 'cw_reset_payments_table_callback' ) );
	add_action( 'wp_ajax_cw_update_exchange_rates', array( $admin_main, 'cw_exchange_rates_callback' ) );
	add_action( 'wp_ajax_cw_update_tx_details', array( $admin_main, 'cw_update_tx_details_callback' ) );
	// add_action( 'wp_ajax_cw_process_open_orders', array($admin_main,'cw_process_open_orders_callback'));

// Process open orders on timeout (not update tx details via API)
	add_action( 'wp_ajax_nopriv_cw_force_processing', array( $admin_main, 'cw_front_process_open_orders_callback' ) );
	add_action( 'wp_ajax_cw_force_processing', array( $admin_main, 'cw_front_process_open_orders_callback' ) );
	// Admin manual processing (has ouptput)
	add_action( 'wp_ajax_nopriv_cw_process_open_orders', array( $admin_main, 'cw_process_open_orders_callback' ) );
	add_action( 'wp_ajax_cw_process_open_orders', array( $admin_main, 'cw_process_open_orders_callback' ) );

// Delete addresses from list
	add_action( 'wp_ajax_cw_delete_address_list', array( $admin_main, 'cw_delete_address_list_callback' ) );

// Order cancellation
	add_action( 'woocommerce_cancelled_order', array( 'CW_Order_Processing', 'order_status_cancelled_action' ) );
	add_action( 'woocommerce_order_status_cancelled', array( 'CW_Order_Processing', 'order_status_cancelled_action' ) );

// Disable Woocommerce order timeout (hold stock setting)
	add_filter( 'woocommerce_cancel_unpaid_order', 'disable_woocommerce_hold_stock_timeout', 10, 2 );

// Order completion
	add_action( 'woocommerce_order_status_completed', array( 'CW_Order_Processing', 'order_status_completed_action' ), 10, 1 );

// Payment processing failures
	add_action( 'cryptowoo_api_error', array( 'CW_Block_Explorer_Processing', 'processing_api_error_action' ), 10, 1 );

// Force check payment status (Checks an order even if timed out)
	add_action( 'woocommerce_order_actions', array( 'CW_Formatting', 'cw_add_order_meta_box_action_force_check_order' ) );
	add_action( 'woocommerce_order_action_force_update_payment_status_action', array( 'CW_Order_Processing', 'force_update_payment_status' ) );
	add_filter( 'woocommerce_admin_order_actions', array( 'CW_Formatting', 'add_force_check_order_action' ), 10, 2 );
	add_action( 'wp_ajax_cw_force_update_payment_status', array( 'CW_Order_Processing', 'force_update_payment_status_handler' ) );

// Force accept payment
	add_action( 'woocommerce_order_actions', array( 'CW_Formatting', 'cw_add_order_meta_box_action_force_accept_payment' ) );
	add_action( 'woocommerce_order_action_force_accept_payment_action', array( 'CW_Order_Processing', 'force_accept_payment_action' ) );
	add_filter( 'woocommerce_admin_order_actions', array( 'CW_Formatting', 'add_force_accept_payment_action' ), 10, 2 );
	add_action( 'wp_ajax_cw_force_accept_payment', array( 'CW_Formatting', 'cw_force_accept_payment_handler' ) );
	add_action( 'admin_post_submit_cryptowoo_force_accept_payment_form', array( 'CW_Formatting', 'submit_cryptowoo_force_accept_payment_form' ) );

// Add custom query args for WooCommerce order queries
	add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'cw_handle_custom_query_var', 10, 2 );

	/**
	 * Handle custom query vars
	 *  - 'payment_currency' to get orders with the 'payment_currency' meta
	 *  - 'mpk_key_index' to get orders with the 'mpk_key_index' meta
	 *  - 'txids' to get orders with the given transaction ID in the 'txids' meta
	 *
	 * @param  array $query      - Args for WP_Query.
	 * @param  array $query_vars - Query vars from WC_Order_Query.
	 * @return array modified $query
	 */
function cw_handle_custom_query_var( $query, $query_vars ) {
	if ( isset( $query_vars['payment_currency'] ) && ! empty( $query_vars['payment_currency'] ) ) {
		$query['meta_query'][] = array(
			'key'   => 'payment_currency',
			'value' => esc_attr( $query_vars['payment_currency'] ),
		);
	}
	if ( isset( $query_vars['has_mpk_index'] ) ) {
		 $query_var = array(
			 'key'   => 'mpk_key_index',
			 'value' => '',
		 );
		 if ( true === $query_vars['has_mpk_index'] ) {
			 $query_var['compare'] = '!=';
		 }
		 $query['meta_query'][] = $query_var;
	}
	if ( isset( $query_vars['txids'] ) && ! empty( $query_vars['txids'] ) ) {
		$query['meta_query'][] = array(
			'key'     => 'txids',
			'value'   => esc_attr( $query_vars['txids'] ),
			'compare' => 'LIKE',
		);
	}

		return $query;
}

	/**
	 * Load cryptopay textdomain.
	 *
	 * @since 0.14.0
	 */
function cryptowoo_textdomain() {
	load_plugin_textdomain( 'cryptopay', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}

	/**
	 * Redirect the user to the setup page after installation
	 *
	 * @param $blog_id
	 */
function cryptowoo_plugin_activate( $blog_id ) {

	$admin_main = new CW_AdminMain();
	$admin_main->create_plugin_table( $blog_id );

	// Maybe rewrite old order meta
	/*
	$current_meta_ver = get_option('cryptowoo_order_meta_ver', '0.1');
	if(version_compare(CWOO_VERSION, $current_meta_ver, '>')) {
		$admin_main->cryptowoo_update_ordermeta_to_satoshi();
	} */

	// Only redirect to settings if WooCommerce is active
	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	if ( validate_file( 'woocommerce/woocommerce.php' ) || ! file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {

		// If WooCommerce is not installed then show installation notice
		add_action( 'admin_notices', 'cryptowoo_wc_notinstalled_notice' );
		return;
	} elseif ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		add_action( 'admin_notices', 'cryptowoo_wc_inactive_notice' );
		return;
	}
}


/**
 * Disable the woocommerce hold stock setting timeout when CryptoPay is the payment gateway
 *
 * @param string   $payment_gateway
 * @param WC_Order $order
 *
 * @return bool
 */
function disable_woocommerce_hold_stock_timeout( $payment_gateway, $order ) {
	if ( $order->get_payment_method() == CW_PAYMENT_METHOD_ID ) {
		return false;
	}

	return $payment_gateway;
}

/**
 *
 * Callback for polling payment status on checkout page.
 */
function poll_callback() {
	$wp_nonce  = ! empty( $_REQUEST['wp_nonce'] ) ? sanitize_key( $_REQUEST['wp_nonce'] ) : false;
	$order_key = ! empty( $_REQUEST['order_key'] ) ? sanitize_key( $_REQUEST['order_key'] ) : '';
	$order_id  = wc_get_order_id_by_order_key( $order_key );

	if ( ! wp_verify_nonce( $wp_nonce, 'cw_poll_callback' ) || ! $order_id ) {
		echo wp_json_encode(
			array(
				'received'    => CW_Formatting::fbits( 0 ),
				'unconfirmed' => CW_Formatting::fbits( 0 ),
				'redirect'    => false,
			)
		);
		die();
	}

	// Check order validity. If order is not valid, wc notice and redirect will occur immediately.
	$payment_details = CW_Database_CryptoWoo::get_payment_details_by_order_id( $order_id );
	CW_Order_Processing::instance( $order_id )->check_order_validity( $payment_details );

	// Get order data again to ensure it is up to date.
	$payment_details    = CW_Database_CryptoWoo::get_payment_details_by_order_id( $order_id );
	$amount_unconfirmed = $payment_details->get_received_unconfirmed();
	$amount_received    = $payment_details->get_received_confirmed();
	$is_paid            = $payment_details->get_is_paid();

	// Maybe redirect when we see an unconfirmed amount
	$redirect_on_unconfirmed = $amount_unconfirmed > 0 && cw_get_option( 'cw_redirect_on_unconfirmed' );

	// Update the current amount received confirmed and unconfirmed. Redirect to order-received if order is paid.
	echo wp_json_encode(
		array(
			'received'    => $amount_received > 0 ? CW_Formatting::fbits( $amount_received ) : '0.00',
			'unconfirmed' => $amount_unconfirmed > 0 ? CW_Formatting::fbits( $amount_unconfirmed ) : '0.00',
			'redirect'    => $is_paid || $redirect_on_unconfirmed ? wc_get_order( $order_id )->get_checkout_order_received_url() : false,
		)
	);

	die(); // This is required to terminate immediately and return a proper response.
}

/**
 * On deactivation, remove all functions from the scheduled action hook.
 */
function cryptowoo_plugin_deactivate() {
	wp_clear_scheduled_hook( 'cryptowoo_cron_action' );
	wp_clear_scheduled_hook( 'cryptowoo_integrity_check' );
	wp_clear_scheduled_hook( 'cryptowoo_archive_addresses' );
}

/**
 * Enqueue scripts
 */
function cryptowoo_scripts() {

	// Progress bar
	cw_enqueue_script( 'nanobar', CWOO_PLUGIN_PATH . 'assets/js/nanobar.js', array( 'jquery' ) );

	// davidshimjs QR Code
	cw_enqueue_script( 'QRCode', CWOO_PLUGIN_PATH . 'assets/js/qrcodejs-master/qrcode.js', array( 'jquery' ) );

	// Register payment page script
	cw_register_script( 'cw_polling', CWOO_PLUGIN_PATH . 'assets/js/polling.js', array( 'jquery' ) );

	// Plugin Styles
	cw_enqueue_style( 'cryptopay', CWOO_PLUGIN_PATH . 'assets/css/cryptopay-plugin.css' );

	// Cryptocurrency icon font
	cw_enqueue_style( 'cw-cryptocoins', CWOO_PLUGIN_PATH . 'assets/fonts/cw-coinfont.css' );

	// Fontawesome icon font
	if ( ! wp_script_is( 'fontawesome', 'enqueued' ) ) {
		cw_enqueue_style( 'fontawesome', CWOO_PLUGIN_PATH . 'assets/fontawesome-free-5.4.1-web/css/all.css', __FILE__ );
	}

	// Checkout page payment currency selector
	cw_register_script( 'cw_checkout', CWOO_PLUGIN_PATH . 'assets/js/checkout.js', array( 'jquery' ), CWOO_VERSION, true );

	if ( is_checkout() ) {
		wp_enqueue_script(
			'cryptopay-bch-addres-format',
			plugins_url( 'assets/js/change-address-format.js', __FILE__ ),
			array( 'wc-checkout', 'jquery' ),
			1
		);
		// https://github.com/bitcoincashjs/bchaddr by Emilio Almansi
		wp_enqueue_script( 'bchaddr', plugins_url( 'assets/js/bchaddrjs-0.2.1.min.js', __FILE__ ), array( 'wc-checkout', 'jquery' ), 1 );
	}
}

function cw_enqueue_admin_scripts() {
	// cw_enqueue_script('cw-admin-js', CWOO_PLUGIN_PATH.'assets/js/admin-js.js');

	// Enqueue address list admin script
	cw_register_script( 'cw_addr', CWOO_PLUGIN_PATH . 'assets/js/addrlist.js', array( 'jquery' ) );
	wp_localize_script( 'cw_addr', 'ajax_data', array( 'nonce' => wp_create_nonce( 'cw_delete_address_list' ) ) );
	cw_enqueue_script( 'cw_addr' );

	// Plugin Styles
	cw_enqueue_style( 'cryptopay', CWOO_PLUGIN_PATH . 'assets/css/cryptopay-plugin.css' );

	// Cryptocurrency icon font
	cw_enqueue_style( 'cw-cryptocoins', CWOO_PLUGIN_PATH . 'assets/fonts/cw-coinfont.css' );

	// Fontawesome icons
	if ( ! wp_script_is( 'fontawesome', 'enqueued' ) ) {
		cw_enqueue_style( 'fontawesome', CWOO_PLUGIN_PATH . 'assets/fontawesome-free-5.4.1-web/css/all.css', __FILE__ );
	}
}

/**
 * Import gateway class extends
 */
function woocommerce_cryptowoo_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	};

	/**
	 * Constructor for the gateway.
	 *
	 * @access public
	 * @return void
	 */
	class WC_CryptoWoo extends WC_Payment_Gateway {

		public function __construct() {
			$this->id           = CW_PAYMENT_METHOD_ID;
			$this->method_title = 'CryptoPay Payment';
			$this->title        = 'CryptoPay';
			$this->has_fields   = true;

			// Load the form fields.
			$this->init_form_fields();

			// Load the settings.
			$this->init_settings();

			// Define user set variables
			$this->enabled     = $this->get_option( 'enabled' );
			$this->title       = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );

			/*
			// Define user set variables
			$this->enabled = $redux['enabled'];
			$this->title = $redux['title'];
			$this->description = $redux['description'];
			*/

			add_action( 'woocommerce_api_wc_' . $this->id, array( $this, 'check_response' ) );
			add_action( 'woocommerce_receipt_' . $this->id, array( &$this, 'receipt_page' ) );

			if ( version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ) {
				/* 1.6.6 */
				add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
			} else {
				/* 2.0.0 */
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, 'cryptowoo_gateway_activation' );
		}

		/**
		 * get_icon - filter for woocommerce_gateway_icon to add the enabled currencies to the payment gateway title on the checkout page
		 *
		 * @return string
		 */
		public function get_icon() {
			$icon_html          = '';
			$enabled_currencies = cw_get_enabled_currencies();

			foreach ( $enabled_currencies as $enabled_currency => $nice_name ) {
				if ( ! strpos( $enabled_currency, 'TEST' ) ) {
					$icon_html .= CW_Formatting::get_coin_icon( $enabled_currency );
				}
			}
			return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
		}

		/**
		 * Integrity check file helper
		 */
		function cryptowoo_hash_keys() {
			  $this->do_cryptowoo_hash_keys();
		}

		/**
		 * Create integrity check file
		 *
		 * Create a hash containing API keys and master public keys
		 * Save to uploads directory
		 */
		private function do_cryptowoo_hash_keys() {

			$keys = array( NONCE_SALT );

			$foo = array( // @todo refactor
				'cryptowoo_btc_api',
				'cryptowoo_doge_api',
				'cryptowoo_ltc_api',
				'cryptowoo_btctest_api',
				'cryptowoo_dogetest_api',
				'cryptowoo_btc_mpk',
				'cryptowoo_doge_mpk',
				'cryptowoo_doge_mpk_xpub',
				'cryptowoo_ltc_mpk',
				'cryptowoo_ltc_mpk_xpub',
				'cryptowoo_btctest_mpk',
				'cryptowoo_dogetest_mpk',
				'cryptowoo_blk_mpk',
				'cryptowoo_blk_mpk_xpub',
				'safe_btc_address',
				'safe_ltc_address',
				'safe_doge_address',
			);

			for ( $i = 0; $i < count( $foo ); $i++ ) {
				$keys[] = cw_get_option( $foo[ $i ] ) ?: '0';
			}

			if ( ! cw_get_option( 'cw_filename' ) ) {
				$am = new CW_AdminMain();
				cw_update_option( 'cw_filename', sanitize_file_name( $am->get_integrity_check_filename() ) );
			}
			$filename = sanitize_file_name( cw_get_option( 'cw_filename' ) );

			if ( ! empty( $keys ) ) {
				// Write the hash of the current api keys to uploadsdir/$filename
				file_put_contents( trailingslashit( wp_upload_dir()['basedir'] ) . sanitize_file_name( $filename ), hash_hmac( 'sha256', print_r( $keys, true ), AUTH_SALT ) );
			}
		}

		public function check_response() {
			if ( isset( $_POST['customer_reference'] ) && isset( $_POST['responseCode'] ) ) :
				@ob_clean();
				$_POST = stripslashes_deep( $_POST );
				if ( ! empty( $_POST['customer_reference'] ) && ! empty( $_POST['responseCode'] ) ) :
					header( 'HTTP/1.1 200 OK' );
					$this->successful_request( $_POST );
			 else :
				 wp_die( 'Request Failure' );
			 endif;
			endif;
		}

		/**
		 * Validate Block.io API keys via get_my_addresses
		 *
		 * @param  $api_key
		 * @param  $currency
		 * @return bool
		 */
		public function cw_validate_api_keys( $api_key, $currency ) {

			$error  = null;
			$return = array( 'status' => false );
			if ( $api_key ) {
				$blockio = new BlockIo( $api_key, '' );
				try {
					$result = $blockio->get_my_addresses();
					if ( isset( $result->status ) && $result->status === 'success' && isset( $result->data->network ) && strcmp( $result->data->network, $currency ) === 0 ) {
						$return['status'] = true;
					}
				} catch ( Exception $e ) {
					$return['message'] = $e->getMessage();
					if ( WP_DEBUG ) {
						CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . __FILE__ . "\n cw_validate_api_keys: " . $return['message'], 'critical' );
					}
				}
			}
			return $return;
		}

		/**
		 * Check PHP requirements
		 *
		 * @return bool
		 */
		public static function check_php_requirements() {

			$version = phpversion();

			if ( version_compare( PHP_VERSION, '5.6.0', '<' ) ) {
				cw_update_option( 'enabled', false );
				$wc_cw_options            = get_option( 'woocommerce_cryptowoo_settings' );
				$wc_cw_options['enabled'] = false;
				update_option( 'woocommerce_cryptowoo_settings', $wc_cw_options );
				return esc_html__( 'Your current PHP Version is ' . $version . '.', 'cryptopay' );
			}
			if ( ! extension_loaded( 'gmp' ) ) {
				cw_update_option( 'enabled', false );
				$wc_cw_options            = get_option( 'woocommerce_cryptowoo_settings' );
				$wc_cw_options['enabled'] = false;
				update_option( 'woocommerce_cryptowoo_settings', $wc_cw_options );
				return sprintf( esc_html__( '%s extension seems not to be installed.', 'cryptopay' ), 'GMP' );
			}

			if ( ! extension_loaded( 'curl' ) ) {
				cw_update_option( 'enabled', false );
				$wc_cw_options            = get_option( 'woocommerce_cryptowoo_settings' );
				$wc_cw_options['enabled'] = false;
				update_option( 'woocommerce_cryptowoo_settings', $wc_cw_options );
				return sprintf( esc_html__( '%s extension seems not to be installed.', 'cryptopay' ), 'cURL' );
			}

			if ( ! extension_loaded( 'bcmath' ) ) {
				$wc_cw_options            = get_option( 'woocommerce_cryptowoo_settings' );
				$wc_cw_options['enabled'] = false;
				update_option( 'woocommerce_cryptowoo_settings', $wc_cw_options );
				cw_update_option( 'enabled', false );
				return sprintf( esc_html__( '%s extension seems not to be installed.', 'cryptopay' ), 'bcmath' );
			}

			return true;
		}

		/**
		 * Admin Panel Options
		 * - Options for bits like 'title' and availability on a country-by-country basis
		 *
		 * @since 1.0.0
		 */
		public function admin_options() {
			?>
			<h3>CryptoPay</h3>
			<span style="padding:1em;"><a class="button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=cryptopay' ) ); ?>" title="<?php echo esc_html__( 'CryptoPay Options', 'cryptopay' ); ?>"><?php echo esc_html__( 'CryptoPay Options', 'cryptopay' ); ?></a></span>
			<span style="padding:1em;"><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=cryptowoo_database_maintenance' ) ); ?>" title="<?php echo esc_html__( 'CryptoPay Database Maintenance', 'cryptopay' ); ?>"><?php echo esc_html__( 'CryptoPay Database Maintenance', 'cryptopay' ); ?></a></span>
			<?php
			 $check_req = self::check_php_requirements();
			if ( true !== $check_req ) {
				?>
				<div class="inline error"><p><strong><?php esc_html_e( 'Gateway Disabled', 'cryptopay' ); ?></strong>: <?php wp_kses_post( $check_req . '<br>You need to have at least PHP version 5.6 with <a href="http://php.net/manual/en/gmp.installation.php" target="_blank">GMP</a>, and <a href="http://php.net/manual/en/curl.installation.php" target="_blank">cURL</a> extensions enabled to use CryptoPay. <br>' ); // TODO: Add translation. ?></p></div>
				<?php
			}
			 wp_die();
		}

		/**
		 * Initialise WooCommerce Gateway Settings
		 *
		 * Actual settings via Redux Framework in options-init.php
		 *
		 * @access public
		 * @return void
		 */
		public function init_form_fields() {

			$this->form_fields = array(
			/*
			'general_section' => array
			(
			'title' => __('<i class="fa fa-wrench"></i> General Settings', 'cryptopay'),
			'type' => 'title',
			'class' => 'section-head',
			),
			'enabled' => array
			(
			'title' => __('Enable/Disable', 'cryptopay'),
			'type' => 'checkbox',
			'label' => __('Enable CryptoPay payment gateway.', 'cryptopay'),
			'default' => '0',
			),
			'title' => array
			(
			'title' => __('Title', 'cryptopay'),
			'type' => 'text',
			'description' => __('This is the title the customer can see when checking out.', 'cryptopay'),
			'default' => __('Digital Currencies', 'cryptopay'),
			),
			'description' => array
			(
			'title' => __('Description', 'cryptopay'),
			'type' => 'textarea',
			'description' => __('This is the description the customer can see when checking out. <strong>HTML</stong> markup allowed', 'cryptopay'),
			'default' => __("Pay with Bitcoin, Litecoin or Dogecoin?", 'cryptopay'),
			),
			*/
			);
		}

		/**
		 * Cryptocurrency Payment info and currency select form on checkout page
		 */
		function payment_fields() {
			CW_Formatting::cryptowoo_payment_currency_checkout_field( $this->description );
		}

		/**
		 * Validate checkout fields
		 *
		 * @return bool|void
		 */
		function validate_fields() {

			// Validation for order-pay page
			if ( isset( $_POST['cw_payment_currency'] ) && isset( $_POST['woocommerce_pay'] ) && sanitize_key( $_POST['woocommerce_pay'] ) ) {

				$payment_currencies = cw_get_enabled_currencies();
				$payment_currency   = sanitize_text_field( wp_unslash( $_POST['cw_payment_currency'] ) );

				if ( array_key_exists( $payment_currency, $payment_currencies ) ) {

					// The new currency is in our list of enabled currencies
					// try to get the order ID from the referrer
					$http_ref = isset( $_POST['_wp_http_referer'] ) ? esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) ) : '';
					$preg     = preg_match( '/checkout\/order-pay\/(.*)\/\?/', $http_ref, $order_id );
					$order    = wc_get_order( $order_id[1] );
					if ( $order ) {

						// We have the order let's compare the payment currency.
						$cwdb_woocommerce = CW_Database_Woocommerce::instance( $order );
						$current_currency = $cwdb_woocommerce->get_payment_currency();
						if ( $payment_currency !== $current_currency ) {
							// Customer selected a different payment currency than last time
							// -> update WC order meta
							CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $payment_currency, 'debug' );
							$cwdb_woocommerce->set_payment_currency( $payment_currency )->update();
						}
					}
				}
			}
		}

		/**
		 * Get WooCommerce Order Data
		 *
		 * @access public
		 * @param  mixed $order
		 * @return array
		 */
		public function get_cryptowoo_args( $order ) {

			$order_id                   = $order->get_id();
			$data                       = array();
			$data['customer_reference'] = $order_id . '-' . $order->get_order_key();
			$data['description']        = 'Payment for order id ' . $order_id;
			$data['amount']             = $order->get_total();
			$data['enabled_currencies'] = cw_get_enabled_currencies();
			$data['payment_currency']   = get_post_meta( $order_id, 'payment_currency', true );
			return $data;
		}

		/**
		 * Process the payment and return the result
		 *
		 * @access public
		 * @param  int $order_id
		 * @return array
		 */
		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );
			return array(
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url( true ),
			);
		}

		/**
		 * Validate key integrity before displaying payment page
		 *
		 * @param $order_id
		 */
		public function receipt_page( $order_id ) {
			$admin_main = new CW_AdminMain();
			// Validate API key and forwarding address integrity
			if ( ! $admin_main->do_cryptowoo_integrity_check() ) {
				CW_Order_Processing::instance( $order_id )->add_customer_notice_unexpected_error();
				CW_Order_Processing_Tools::instance()->redirect_to_cart();
			} else {
				$this->generate_cryptowoo_form( $order_id );
			}
		}

		/**
		 * Render order-pay page
		 *
		 * @param $order_id
		 */
		public function generate_cryptowoo_form( $order_id ) {

			global $woocommerce;
			$order = wc_get_order( $order_id );

			// Redirect to "order received" page if the order is already paid
			if ( $order->is_paid() ) {
				$redirect = $order->get_checkout_order_received_url();
				wp_safe_redirect( $redirect );
				exit;
			}

			$cryptowoo_args = $this->get_cryptowoo_args( $order );

			$customer_reference = $cryptowoo_args['customer_reference'];
			$payment_currency   = $cryptowoo_args['payment_currency'];

			// Detect lightning network payment
			$is_lightning     = false !== strpos( $payment_currency, 'lightning' );
			$payment_currency = str_replace( '-lightning', '', $payment_currency );

			// If it is not a lightning payment request now
			if ( ! $is_lightning ) {
				// Check if it was one before
				$switched_back_from_lightning = CW_Database_Woocommerce::instance( $order )->get_is_lightning();
			} else {
				$switched_back_from_lightning = false;
			}
			$order->add_meta_data( 'is_lightning', $is_lightning, true );

			$amount        = $cryptowoo_args['amount'];
			$message       = null;
			$crypto_amount = 0;

			// Get wallet config, use 1 satoshi dummy amount
			// We'll count the actual decimals after the crypto amount has been determined
			$wallet_config = CW_Address::get_wallet_config( $payment_currency, 1, false );

			if ( false !== strpos( $payment_currency, 'TEST' ) ) {
				$message .= sprintf( esc_html__( '%1$s%2$s currency override enabled. This payment gateway is in testnet mode. No orders shall be fulfilled.%3$s', 'cryptopay' ), '<p class="cryptopay-warning">', $payment_currency, '</p>' );
			}

			// Get payment details by order_id.
			$payment_details = CW_Database_CryptoWoo::get_payment_details_by_order_id( $order_id );

			$updated = $refresh_quote = false;
			if ( ! ( $payment_details->is_empty() ) ) {
				$payment_address  = $payment_details->get_address();
				$amount_payments  = $payment_details->get_fiat_amount();
				$crypto_amount    = $payment_details->get_crypto_amount_due();
				$payment_currency = $payment_details->get_payment_currency();

				// Check if we need to update the crypto amount
				if ( (int) $payment_details->get_timeout() === 4 || ( 'quote-refresh' === cw_get_option( 'timeout_action' ) && 'quote-refresh' === $order->get_status() ) ) {
					$updated       = true;
					$refresh_quote = true;
					CW_AdminMain::cryptowoo_log_data(
						0,
						__FUNCTION__,
						array(
							'updated'       => $updated,
							'refresh_quote' => $refresh_quote,
						),
						'debug'
					);
				}

				// Request a new payment address if the payment currency has changed.
				if ( ( $cryptowoo_args['payment_currency'] != $payment_currency && ! $is_lightning ) || $switched_back_from_lightning ) {
					$message .= sprintf( esc_html__( '%1$sMessage: Currency has changed from %2$s to %3$s. New address has been generated.%1$s', 'cryptopay' ), '<br>', $payment_currency, $cryptowoo_args['payment_currency'] );
					// delete_order_payment_request($order_id);
					$payment_currency = $cryptowoo_args['payment_currency'];
					$payment_address  = false;
					$updated          = true;
				}

				// Update payment request from table payments if the order amount has changed.
				if ( $amount_payments != $cryptowoo_args['amount'] ) {
					$message .= sprintf( esc_html__( '%1$sMessage: Amount has changed. Order has been updated%2$s', 'cryptopay' ), '<br>', '<br>' );
					// delete_order_payment_request($order_id);
					$updated = true;

				}

				// Log changing amount or currency and add order note
				if ( $message ) {
					CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, 'Order #' . $order_id . ' ' . $message, 'info' );
					$order = wc_get_order( $order_id );
					if ( $order ) {
						$order_note_message = $message;
						if ( ! $payment_address ) {
							// Add the old address and amount to the order note if we will create a new address now
							$order_note_message .= sprintf( 'Old address: %s<br>Old amount: %s %s', $payment_details->get_address(), CW_Formatting::fbits( $payment_details->get_crypto_amount_due() ), $payment_details->get_payment_currency() );
						}
						$order->add_order_note( $order_note_message );
					}
				}
				// DEBUG var_dump($cryptowoo_args['enabled_currencies']);
			} else {
				$payment_address = false;
			}
			$rate_error = false;

			// If there is no entry for that order_id calculate crypto_amount and generate address
			if ( empty( $payment_address ) || ! $payment_address || $updated ) {

				$currency = cw_get_woocommerce_currency();

				// Only convert price if the store currency is not BTC, LTC or DOGE
				if ( $payment_currency != $currency ) {

					// Get the exchange rate from the database
					$price = CW_ExchangeRates::processing()->get_exchange_rate( $wallet_config['request_coin'], false, $order->get_currency() );

					// Aelia currency switcher: Get the order amount in the active currency
					if ( CW_ExchangeRates::tools()->currency_is_fiat( $currency ) && cw_get_woocommerce_default_currency() !== $currency ) {
						$price = apply_filters( 'wc_aelia_cs_convert', $price, cw_get_woocommerce_default_currency(), $order->get_currency(), 8 );
					}

					// WooCommerce Multilingual (WCML): Get the order amount in the active currency
					$price = apply_filters( 'wcml_raw_price_amount', $price, $order->get_currency() );

					// Calculate order_total in cryptocurrency
					// TODO: Base units setting. Not all have max 8 decimals!
					$base_units          = 1e8;
					$crypto_amount_float = $amount * $wallet_config['multiplier'] / $price;
					$coin_decimals       = CW_Formatting::calculate_coin_decimals( $payment_currency, $crypto_amount_float * $base_units );
					$crypto_amount_float = round( $crypto_amount_float, $coin_decimals );
					$crypto_amount       = (int) max( 1, $crypto_amount_float * $base_units ); // Ensure price is minimum 1 satoshi.

				} else {
					$crypto_amount = CW_Order_Processing_Tools::cw_float_to_int( round( ( $amount * $wallet_config['multiplier'] ), $wallet_config['decimals'] ) );// Use unconverted store amount if store currency is BTC, LTC or DOGE
				}

				$crypto_amount = apply_filters( 'cw_crypto_amount_override', $crypto_amount, $payment_currency, $order );

				// Check if we have a valid crypto amount
				$amount_error = ! $rate_error && isset( $crypto_amount ) && is_numeric( $crypto_amount ) && (int) $crypto_amount > 0 ? false : true;
				$status       = array(); // init empty array so it's valid if loop below not executed
				if ( $amount_error ) {
					$status['amount_error'] = 'fail';
					CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . ' Amount calculation error: ' . var_export( $crypto_amount, true ) . ' Status: ' . var_export( $payment_details, true ), 'emergency' );
					// Redirect customer to checkout page so he can resubmit the order
					CW_Order_Processing::instance( $order_id )->decline_order_amount_error();
				}

				$nonce = time();
				$label = "{$customer_reference}-{$nonce}";

				if ( ! $refresh_quote || ! $payment_address ) { // skip new address generation if it's a quote renewal with the same currency
					// Do we have a block.io API key?
					$api_key = CW_AdminMain::get_blockio_api_key( $payment_currency );

					// Let's get a payment address
					if ( $is_lightning ) {

						// Create lightning invoice request in Electrum wallet
						$payment_address              = CW_Electrum::create_new_lightning_invoice( $payment_currency, CW_Formatting::fbits( $crypto_amount, true, 8, true, true ), $order_id, cw_get_options() );
						$wallet_config['coin_client'] = 'lightning'; // For BOLT11 payment URI

					} elseif ( CW_Validate::check_if_unset( 'electrum', $wallet_config ) ) {

						// Create payment request in Electrum wallet
						$payment_address = CW_Electrum::create_new_request( $payment_currency, CW_Formatting::fbits( $crypto_amount, true, 8, true, true ), $order_id, cw_get_options() );

					} elseif ( $wallet_config['hdwallet'] ) {

						// Get new payment address from master public key
						$hdwallet_return  = CW_HDwallet::create_address_from_mpk( $payment_currency );
						$payment_address  = $hdwallet_return['address'];
						$mpk_key_index    = $hdwallet_return['mpk_key_index'];
						$mpk_key_position = $hdwallet_return['mpk_key_position'];

					} elseif ( $api_key ) {

						// Get default MultiSig address via Block.io API
						$multisig_return = CW_Address::create_blockio_multisig_address( $api_key, $label );
						$payment_address = $multisig_return['address'];

					} elseif ( $wallet_config['use_address_list'] ) {

						// Get new address from list
						$payment_address = CW_AddressList::get_address_from_list( $payment_currency, $order_id );
						$msg             = sprintf( 'Order #%d: Using %s address from address list: %s', $order->get_id(), $payment_currency, $payment_address );
						CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $msg, 'debug' );

					} else {

						// Get payment address from filter
						$payment_address = apply_filters( "cw_create_payment_address_$payment_currency", $payment_address, $order, cw_get_options() );
						$msg             = sprintf( 'Order #%d: Creating address via filter: cw_create_payment_address_%s returned %s', $order->get_id(), $payment_currency, $payment_address );
						CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $msg, 'debug' );

					}

					$validate      = new CW_Validate();
					$address_valid = $validate->offline_validate_address( $payment_address, $payment_currency );

					if ( ! $address_valid ) {
						$status['address_error'] = 'fail';
						CW_Order_Processing::instance( $order_id )->decline_order_address_error();
					} else {
						$status['address_error'] = 'success';
					}
				}

				// Save or update payment details in table payments and WooCommerce order meta
				if ( ! in_array( 'fail', $status ) ) {

					if ( $updated == true ) {
						CW_OrderProcessing::cryptowoo_update_payment_details( $payment_address, $amount, $customer_reference, $payment_currency, $crypto_amount, $order_id );
						if ( $refresh_quote ) {

							   // Force update timeout value after updating the crypto amount
							   CW_Block_Explorer_Processing::requeue_order( $order );

							   // Make sure the order is set to "pending"
							   $order->update_status( 'pending', __( 'Refreshed cryptocurrency order total.', 'cryptopay' ) );

							   CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, sprintf( 'Quote for order %d refreshed - setting order status to pending payment', $order->get_id() ), 'info' );

						}
					} else {
						CW_OrderProcessing::cryptowoo_insert_payment_details( $payment_address, $amount, $customer_reference, $payment_currency, $crypto_amount, $order_id );
					}

					// Save exchange rate in woocs.
					include_once ABSPATH . 'wp-admin/includes/plugin.php';
					if ( is_plugin_active( 'woocommerce-currency-switcher/index.php' ) ) {
						$base_currency = cw_get_woocommerce_default_currency();
						if ( CW_ExchangeRates::tools()->currency_is_fiat( $currency ) && CW_ExchangeRates::tools()->currency_is_fiat( $base_currency ) ) {
							$fiat_rates = cw_get_fiat_currencies();
							$fiat_rate  = $fiat_rates[ $currency ]['rate'];
						} elseif ( 'BTC' === $base_currency && CW_ExchangeRates::tools()->currency_is_fiat( $currency ) ) {
									 $fiat_rate = CW_ExchangeRates::processing()->get_exchange_rate( $base_currency, false, $currency );
						} else {
							$fiat_rate = CW_ExchangeRates::processing()->get_exchange_rate( $currency );
						}
									update_post_meta( $order_id, '_woocs_order_currency', $currency );
									wc_add_order_item_meta( $order_id, '_woocs_order_currency', $currency, true );
									update_post_meta( $order_id, '_woocs_order_rate', $fiat_rate );
									wc_add_order_item_meta( $order_id, '_woocs_order_rate', $fiat_rate, true );
					}

					// Maybe add HD Wallet derivation info
					if ( $wallet_config['hdwallet'] && isset( $mpk_key_index ) ) {
						CW_Database_Woocommerce::instance( $order_id )
							->set_mpk_key_index( $mpk_key_index )
							->set_mpk_key_position( $mpk_key_position )
							->update();
					}
					do_action( 'cryptowoo_new_order', $order_id );
				} else {
					CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . ' Error creating order: ' . var_export( $status, true ) . ' Status: ' . var_export( $payment_details, true ), 'emergency' );
					CW_Order_Processing::instance( $order_id )->decline_order_unexpected_error();
				}
			}

			// Update the number of decimals
			$wallet_config['decimals'] = CW_Formatting::count_coin_decimals( (int) $crypto_amount );

			$label = rawurlencode( sprintf( '%s %s %s', get_bloginfo( 'name' ), esc_html__( 'Order', 'cryptopay' ), $order_id ) );

			$coin_protocols = $wallet_config['coin_protocols'];

			// Get payment details from database WHERE `order_id`= ?
			$payment_details = CW_Database_CryptoWoo::get_payment_details_by_order_id( $order_id );
			$timeout_value   = $payment_details->get_timeout_timestamp();
			$time_left       = isset( $timeout_value ) ? $timeout_value - time() : time() + 1800;

			// Prepare QR Code
			$qr_data = sprintf( '%s:%s?amount=%s&label=%s', $wallet_config['coin_client'], $payment_address, CW_Formatting::fbits( $crypto_amount, true, $wallet_config['decimals'], true, true ), $label );
			$qr_data = apply_filters( "cw_set_qr_data_$payment_currency", $qr_data, $payment_details ); // TODO: Remove when no more usage by addons.
			$qr_data = apply_filters( 'cw_set_qr_data', $qr_data, $payment_details->get_current_row(), $order, cw_get_options() );// TODO: change $payment_details->get_current_row() to $payment_details when xmr add-on supports PaymentDetails object. Keep as backwards compatibility for now.

			// Check basic order validity on page load
			CW_Order_Processing::instance( $order_id )->check_order_validity( $payment_details );

			// WooCommerce "Order Received" URL
			$redirect = $order->get_checkout_order_received_url();

			// if the payment has been received
			if ( $payment_details->get_crypto_amount_due() > 0 && $payment_details->get_is_paid() == 1 ) {
				// Empty cart and clear session
				$woocommerce->cart->empty_cart();
				// Redirect to "Order Received" URL
				wp_safe_redirect( $redirect );
				exit;
			}

			// Ajax url
			$admin_url = admin_url( 'admin-ajax.php' );

			// Trezor Connect
			if ( cw_get_option( 'cw_display_pay_with_trezor_button' ) && ! wp_script_is( 'trezor_connect', 'enqueued' ) ) {
				cw_enqueue_script( 'trezor_connect', 'https://connect.trezor.io/8/trezor-connect.js', array( 'jquery' ), CWOO_VERSION, true );
			}

			// Localize the script with new data
			$php_vars_array = array(
				'payment_address' => $payment_address,
				'show_countdown'  => (bool) cw_get_option( 'show_countdown' ),
				'total_time'      => cw_get_option( 'order_timeout_min' ) * 60, // Timeout in seconds
				'time_left'       => $time_left,
				'admin_url'       => $admin_url,
				'redirect'        => $redirect,
				'currency'        => $payment_currency,
				'lc_currency'     => strtolower( $payment_currency ),
				'amount'          => CW_Formatting::fbits( $crypto_amount, true, $wallet_config['decimals'], true, true ),
				'crypto_amount'   => $crypto_amount,
				'qr_data'         => $qr_data,
				'please_wait'     => esc_html__( 'Please wait...', 'cryptopay' ),
				'wp_nonce'        => wp_create_nonce( 'cw_poll_callback' ),
				'order_key'       => $order->get_order_key(),
			);
			wp_localize_script( 'cw_polling', 'CryptoPay', $php_vars_array );

			// Enqueued script with localized data.
			cw_enqueue_script( 'cw_polling' );

			// Then access all the vars like this
			/**
			 * CryptoPay.payment_address;
			 * CryptoPay.time_left;
			 * CryptoPay.admin_url;
			 * [...]
			 */

			// Decimal seperator
			$decimal_sep = wc_get_price_decimal_separator();

			// Block chain link
			$url = CW_Formatting::link_to_address( $payment_currency, $payment_address );

			// JavaScipt disabled message
			printf( esc_html__( '%1$sSome elements need JavaScript activated to work but the order will be processed regardless. You will receive an email when your payment is confirmed.%2$s', 'cryptopay' ), '<noscript><p>', '</p><style>.nojs { display:none; }</style></noscript>' );

			// Maybe use custom payment page template
			// Copy the file wp-content/plugins/cryptopay/includes/payment.php to wp-content/themes/yourtheme/cryptopay/payment.php
			$custom_template = apply_filters( 'cw_payment_page_template_path', get_stylesheet_directory() . '/cryptopay/payment.php' );
			if ( file_exists( $custom_template ) ) {
				include_once $custom_template;
			} else {
				include_once CWOO_PLUGIN_DIR . 'includes/payment.php';
			}
		}

	}//end class

	/**
	 * Add CryptoPay to the available payment methods in WooCommerce
	 *
	 * @param  $methods
	 * @return array
	 */
	function woocommerce_cryptowoo_add_gateway( $methods ) {
		$methods[] = 'WC_CryptoWoo';

		return $methods;

	}

	add_filter( 'woocommerce_payment_gateways', 'woocommerce_cryptowoo_add_gateway' );

}

/**
 * Get current WooCommerce store currency.
 * File name: woocommerce/includes/wc-core-functions.php
 **/
function cw_get_woocommerce_currency() {
	if ( isset( $WOOCS ) ) {
		$currency = cw_woocs_get_current_currency();
	} else {
		$currency = cw_get_woocommerce_default_currency();
	}

	return apply_filters( 'woocommerce_currency', $currency );
}


/**
 * Get default WooCommerce store currency.
 * File name: woocommerce/includes/wc-core-functions.php
 **/
function cw_get_woocommerce_default_currency() {
	return get_option( 'woocommerce_currency' );
}

/**
 * Add digital currencies supported by CryptoPay to woocommerce currency list
 * File name: woocommerce/includes/wc-core-functions.php
 *
 * @return array
 */
function cw_add_woocommerce_currencies( $currencies ) {
	return array_merge( $currencies, cw_get_cryptocurrencies() );
}

/**
 * Get full list of currency codes and names for digital currencies supported by CryptoPay.
 *
 * @return string[]
 */
function cw_get_cryptocurrencies() {
	return apply_filters(
		'cw_get_cryptocurrencies',
		array(
			'BTC'      => esc_html__( 'Bitcoin', 'cryptopay' ),
			'BCH'      => esc_html__( 'Bitcoin Cash', 'cryptopay' ),
			'LTC'      => esc_html__( 'Litecoin', 'cryptopay' ),
			'DOGE'     => esc_html__( 'Dogecoin', 'cryptopay' ),
			'BLK'      => esc_html__( 'BlackCoin', 'cryptopay' ),
			'BTCTEST'  => esc_html__( 'Testnet Bitcoin', 'cryptopay' ),
			'DOGETEST' => esc_html__( 'Testnet Dogecoin', 'cryptopay' ),
		)
	);
}

/**
 * Get full list of currency codes and names for digital currencies supported by CryptoPay.
 *
 * Currently only used by Dokan add-on. Important: do not use it, use cw_get_cryptocurrencies() instead!
 *
 * TODO: Change to cw_get_cryptocurrencies() in Dokan addon.
 * TODO: Remove cw_get_woocommerce_currencies() when possible (keep for dokan add-on backwards compatibility for now).
 *
 * @deprecated 0.25.6 This method has been deprecated, use cw_get_cryptocurrencies.
 * @return     array
 */
function cw_get_woocommerce_currencies() {
	return array_unique( apply_filters( 'woocommerce_currencies', cw_get_cryptocurrencies() ) );
}

/**
 * Remove the digital currencies not supported by CryptoPay as Woocommerce store currency (base currency)
 * Note that currently only fiat and Bitcoin is supported as base currencies.
 * So we simply remove the other cryptopay currencies from woocommerce settings.
 */
function cw_remove_unsupported_woocommerce_currencies() {
	remove_filter( 'woocommerce_currencies', 'cw_add_woocommerce_currencies' );
}

/**
 * Customized get_woocommerce_currency_symbol() function
 * File name: woocommerce/includes/wc-core-functions.php
 * Added BTC, LTC and DOGE symbols
 *
 * @param  $currency
 * @return string
 */
function cw_get_currency_symbol( $currency = '' ) {

	if ( ! $currency ) {
		$currency = cw_get_woocommerce_currency();
	}

	switch ( $currency ) {
		case 'AED':
			$currency_symbol = 'د.إ';
			break;
		case 'BDT':
			$currency_symbol = '৳ ';
			break;
		case 'BRL':
			$currency_symbol = 'R$';
			break;
		case 'BGN':
			$currency_symbol = 'лв.';
			break;
		case 'AUD':
		case 'CAD':
		case 'CLP':
		case 'COP':
		case 'MXN':
		case 'NZD':
		case 'HKD':
		case 'SGD':
		case 'USD':
			$currency_symbol = '$';
			break;
		case 'EUR':
			$currency_symbol = '€';
			break;
		case 'CNY':
		case 'RMB':
		case 'JPY':
			$currency_symbol = '¥';
			break;
		case 'RUB':
			$currency_symbol = 'руб.';
			break;
		case 'KRW':
			$currency_symbol = '₩';
			break;
		case 'PYG':
			$currency_symbol = '₲';
			break;
		case 'TRY':
			$currency_symbol = '₺';
			break;
		case 'NOK':
			$currency_symbol = 'kr';
			break;
		case 'ZAR':
			$currency_symbol = 'R';
			break;
		case 'CZK':
			$currency_symbol = 'Kč';
			break;
		case 'MYR':
			$currency_symbol = 'RM';
			break;
		case 'DKK':
			$currency_symbol = 'kr.';
			break;
		case 'HUF':
			$currency_symbol = 'Ft';
			break;
		case 'IDR':
			$currency_symbol = 'Rp';
			break;
		case 'INR':
			$currency_symbol = 'Rs.';
			break;
		case 'NPR':
			$currency_symbol = 'Rs.';
			break;
		case 'ISK':
			$currency_symbol = 'Kr.';
			break;
		case 'ILS':
			$currency_symbol = '₪';
			break;
		case 'PHP':
			$currency_symbol = '₱';
			break;
		case 'PLN':
			$currency_symbol = 'zł';
			break;
		case 'SEK':
			$currency_symbol = 'kr';
			break;
		case 'CHF':
			$currency_symbol = 'CHF';
			break;
		case 'TWD':
			$currency_symbol = 'NT$';
			break;
		case 'THB':
			$currency_symbol = '฿';
			break;
		case 'GBP':
			$currency_symbol = '£';
			break;
		case 'RON':
			$currency_symbol = 'lei';
			break;
		case 'VND':
			$currency_symbol = '₫';
			break;
		case 'NGN':
			$currency_symbol = '₦';
			break;
		case 'HRK':
			$currency_symbol = 'Kn';
			break;
		case 'EGP':
			$currency_symbol = 'EGP';
			break;
		case 'DOP':
			$currency_symbol = 'RD$';
			break;
		case 'KIP':
			$currency_symbol = '₭';
			break;

		// add cryptocurrency symbols
		case 'BTC':
		case 'BTCTEST':
			$currency_symbol = '&#3647;';
			break;
		case 'BCH':
			$currency_symbol = '&#3647;';
			break;
		case 'DOGE':
		case 'DOGETEST':
			$currency_symbol = '&#208;';
			break;
		case 'LTC':
			$currency_symbol = '&#321;';
			break;
		case 'BLK':
			$currency_symbol = '&#1123;';
			break;
		default:
			$currency_symbol = '';
			break;
	}

	return apply_filters( 'cw_get_currency_symbol', $currency_symbol, $currency );
}

/**
 * Return array of enabled currencies with readable names
 *
 * @param  $with_rates_only
 * @param  $include_testnet
 * @return array
 */
function cw_get_enabled_currencies( $with_rates_only = true, $include_testnet = true ) {
	$enabled_currencies = array();

	// Get norates transient
	$norates = get_transient( 'cryptowoo_norates' );

	$currency_nicenames = cw_get_woocommerce_currencies();

	$coin_identifiers = array(
		'BTC'      => 'btc',
		'BTCTEST'  => 'btctest',
		'BCH'      => 'bch',
		'DOGE'     => 'doge',
		'DOGETEST' => 'dogetest',
		'LTC'      => 'ltc',
		'BLK'      => 'blk',
	);
	$coin_identifiers = apply_filters( 'cw_get_enabled_currencies', $coin_identifiers );

	foreach ( $coin_identifiers as $currency => $coin_identifier ) {

		// If we want to exclude currencies without exchange rates
		// we will check if it is listed in the "norates" transient
		$has_rates = $with_rates_only ? ! isset( $norates[ $currency ] ) : true;

		// Maybe exclude testnet currencies or currencies without rates
		if ( ! $has_rates || ( ! $include_testnet && false !== strpos( $currency, 'TEST' ) ) ) {
			continue;
		}

		$is_api = cw_get_option( sprintf( 'cryptowoo_%s_api', $coin_identifier ) );

		 // Get wallet config for this currency
		$wallet_config         = CW_Address::get_wallet_config( $currency, 1, false );
		$is_hd                 = array_key_exists( 'hdwallet', $wallet_config ) ? $wallet_config['hdwallet'] : false;
		$has_addresses_in_list = CW_AddressList::address_list_enabled( $currency );

		// Currency is enabled if we have an API key (or an MPK) and we also have exchange rates (or don't need them)
		if ( $is_hd || $is_api || $has_addresses_in_list ) {
			$enabled_currencies[ $currency ] = $currency_nicenames[ $currency ];
		}
	}

	return apply_filters( 'cw_coins_enabled', $enabled_currencies, $coin_identifiers, cw_get_options() );
}


/**
 * Add the enabled currencies to the "WooCommerce Currency Switcher" plugin by hooking into "woocs_currency_data_manipulation" filter
 *
 * @param $woocs_currencies
 *
 * @return array
 */
function add_currencies_to_woocs( $woocs_currencies ) {
	// Avoid running on the woocs settings page to avoid issue of displaying old value after saving settings.
	if ( is_admin() && isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] && isset( $_GET['tab'] ) && 'woocs' === $_GET['tab'] ) {
		return $woocs_currencies;
	}

	// Add payment currency to woocs order currency when creating order
	if ( isset( $_POST['cw_payment_currency'] ) ) {
		$_POST['woocs_order_currency'] = sanitize_text_field( wp_unslash( $_POST['cw_payment_currency'] ) );
	}

	$currencies = array();

	// Get WooCommerce Currency
	$woocommerce_currency = cw_get_woocommerce_default_currency(); // since switcher 1.1.5 cw_get_woocommerce_currency();

	// Reset switcher to store currency on Dokan withdraw pages
	if ( false !== get_option( 'dokan_pages' ) && false !== strpos( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/withdraw' ) ) {
		$st = new WOOCS_STORAGE();
		$st->set_val( 'woocs_current_currency', $woocommerce_currency );
		return $woocs_currencies;
	}

	/*
	 / Reset to store currency on checkout page
	add_filter('wp_head',function(){
	if(is_checkout()) {
	global $WOOCS;
	$WOOCS->current_currency = get_option('woocommerce_currency');
	$WOOCS->storage->set_val('woocs_current_currency', $WOOCS->current_currency);
	}
	});*/

	// Maybe get currency settings from 10s transient
	$cached = get_transient( 'cryptopay-woocs' );
	if ( false !== $cached ) {
		return $cached;
	}

	// Get currency nicenames
	$wc_currencies = cw_get_woocommerce_currencies();

	$currency_is_fiat = CW_ExchangeRates::tools()->currency_is_fiat( $woocommerce_currency ) ? true : false;

	if ( is_array( cw_get_options() ) ) {

		$enabled_currencies                             = cw_get_enabled_currencies();
		$currency_is_fiat ?: $enabled_currencies['USD'] = 'USD';

		foreach ( $enabled_currencies as $enabled_currency => $nicename ) {
			if ( ! cw_get_option( 'add_currencies_to_woocs' ) ) {
				continue;
			}

			if ( ! strpos( $enabled_currency, 'TEST' ) ) {

				// Get multiplier option
				$multiplier_key = 'multiplier_' . strtolower( $enabled_currency );
				$multiplier     = cw_get_option( $multiplier_key ) ?: 1;

				$rate = CW_ExchangeRates::processing()->get_exchange_rate( $enabled_currency, false, $woocommerce_currency );

				// Create woocs args for currency
				if ( is_numeric( $rate ) ) {
					// Prepare exchange rate, set to 1 if rate not found
					$prep_rate   = $rate > 0 && $multiplier > 0 ? ( 1 / ( $rate / (float) $multiplier ) ) : 1;
					$flag_file   = sprintf( '%sassets/images/flags/%s.png', CWOO_PLUGIN_DIR, strtolower( $enabled_currency ) );
					$flag_exists = $prep_rate !== 1 ? file_exists( $flag_file ) : false;
					$flag_url    = sprintf( '%sassets/images/flags/%s.png', CWOO_PLUGIN_PATH, strtolower( $enabled_currency ) );

					$dec_key      = sprintf( 'decimals_%s', $enabled_currency );
					$default_flag = isset( $woocs_currencies[ $enabled_currency ]['flag'] ) && false === strpos( $woocs_currencies[ $enabled_currency ]['flag'], 'no_flag' ) ? $woocs_currencies[ $enabled_currency ]['flag'] : false;
					$custom_flag  = $flag_exists ? $flag_url : '';
					// Prepare currency data
					/*
					 TODO: PHP docs recommend avoiding "stacking" ternary expressions because PHP behavior non-obvious.
						Source: http://www.php.net/manual/en/language.operators.comparison.php#language.operators.comparison.ternary
						Also see: https://stackoverflow.com/a/5235721 */
					$args['name']        = $prep_rate !== 1 ? $enabled_currency : $woocommerce_currency;
					$args['rate']        = $prep_rate;
					$args['position']    = ! isset( $woocs_currencies[ $enabled_currency ]['position'] ) ? 'left_space' : $woocs_currencies[ $enabled_currency ]['position'];
					$args['is_etalon']   = $woocommerce_currency !== $enabled_currency ? 0 : 1;
					$args['description'] = $prep_rate !== 1 ? isset( $woocs_currencies[ $enabled_currency ]['description'] ) ? $woocs_currencies[ $enabled_currency ]['description'] : $nicename : $wc_currencies[ $woocommerce_currency ];
					$args['hide_cents']  = 0;
					$args['flag']        = $default_flag ?: $custom_flag;
					$args['decimals']    = 8; // Cryptocurrency decimals override to 8 to avoid dynamic decimals issues.
					$args['symbol']      = cw_get_currency_symbol( $enabled_currency );

					$currencies[ $enabled_currency ]             = $args;
					$currencies[ $enabled_currency ]['raw_rate'] = $rate;
				}
			}
		}
	}

	// calculate woocs fiat rates on the fly if base currency is crypto
	if ( false === $currency_is_fiat ) {
		foreach ( $woocs_currencies as $currency_name => & $currency_data ) {
			if ( 'USD' !== $currency_name ) {
				$currency_data['rate'] *= $currencies['USD']['rate'];
			}
		}
	} elseif ( ! $woocs_currencies[ $woocommerce_currency ]['is_etalon'] ) {

		// Make sure the right currency is default in woocs when using fiat as default currency
		// Calculate the exchange rates for fiat currencies on the fly

		foreach ( $woocs_currencies as $currency_name => & $currency_data ) {
			if ( $woocommerce_currency !== $currency_name ) {
				$currency_data['is_etalon'] = 0;
				$currency_data['rate']      = 1 / $woocs_currencies[ $woocommerce_currency ]['rate'];
			} else {
				$currency_data['is_etalon'] = 1;
				$currency_data['rate']      = 1;
			}
		}
	}

	$merged = array_merge( $woocs_currencies, $currencies );
	set_transient( 'cryptopay-woocs', $merged, 10 );
	return $merged;
}

/**
 *
 * Format woocs price for cryptocurrencies.
 * Force override woocs displayed decimals. E.g display 0.014 instead of 0.01400000.
 * Force price to be at least 1 satoshi if crypto is current currency.
 * This solves the rounding in woocs causing 0 as price when decimals lower than actual.
 * This rounding "issue" occurs in line 1674 in woocs_after_33.php.
 * Also ensures that decimals is at most 8 decimals (1 satoshi).
 *
 * @param string|int|float $crypto_amount price.
 *
 * @return float
 */
function format_woocs_crypto_amount( $crypto_amount ) {
	// Bail if not cryptocurrency as we only want to affect cryptopay woocs micropayments here.
	if ( ! CW_ExchangeRates::tools()->currency_is_crypto( cw_woocs_get_current_currency() ) ) {
		return $crypto_amount;
	}

	global $WOOCS;

	// Ensure minimum positive value (e.g. 1 satoshi).
	if ( ! (float) $crypto_amount ) {
		// TODO: cryptocurrency setting. Not all have max 8 decimals.
		$decimals = min( 8, $WOOCS->get_currency_price_num_decimals( $WOOCS->current_currency, $WOOCS->price_num_decimals ) );
		return number_format( floatval( 1 / "1e$decimals" ), $decimals, $WOOCS->decimal_sep, '' );
	}

	// Calculate coin decimals and crypto amount dynamically
	// TODO: Base units setting. Not all have max 8 decimals.
	$base_units    = 1e8;
	$coin_decimals = CW_Formatting::calculate_coin_decimals( cw_woocs_get_current_currency(), $crypto_amount * $base_units );
	$crypto_amount = number_format( $crypto_amount, $coin_decimals, $WOOCS->decimal_sep, '' );

	// Force override displayed decimals. E.g display 0.014 instead of 0.01400000.
	if ( ! function_exists( 'override_formatted_woocs_crypto_amount' ) ) {
		/**
		 *
		 * Remove trailing zeros from formatted woocs crypto amount.
		 */
		function override_formatted_woocs_crypto_amount() {
			add_filter(
				'formatted_woocommerce_price',
				function ( $crypto_amount ) {
					// Strip trailing zeros, source: https://stackoverflow.com/a/12944919, also in CW_Formatting::fbits( $crypto_amount, divide = false );.
					return rtrim( ( strpos( $crypto_amount, '.' ) !== false ? rtrim( $crypto_amount, '0' ) : $crypto_amount ), '.' );
				},
				99999
			);
		}

		override_formatted_woocs_crypto_amount();
	}

	return $crypto_amount;
}

function cw_get_all_currencies() {
	return array_merge( cw_get_active_currencies(), cw_get_enabled_currencies() );
}

function cw_get_fiat_currencies() {
	return array_diff_key( cw_get_active_currencies(), cw_get_enabled_currencies() );
}

function cw_get_active_currencies() {
	$wc_currency = cw_get_woocommerce_currency();

	// The base currencies we want exchange rates for
	$currencies = array();

	// Aelia Currency Switcher enabled currencies
	$aelia_switcher_currencies = apply_filters( 'wc_aelia_cs_enabled_currencies', array( $wc_currency ) );
	foreach ( $aelia_switcher_currencies as $currency ) {
		$currencies[ $currency ] = array( 'rate' => 1 ); // Rate will be overridden with Aelia data before use
	}

	// Apply filter to allow adding base currencies and their exchange rates
	$currencies = apply_filters( 'cw_base_currencies', $currencies );

	// Prepare currencies with given rate or rate = 1
	$all_currencies = array();
	foreach ( $currencies as $currency => $data ) {
		$rate                        = isset( $data['rate'] ) && is_numeric( $data['rate'] ) ? $data['rate'] : 1;
		$all_currencies[ $currency ] = array( 'rate' => $rate );
	}

	// Maybe replace with WooCommerce Currency Switcher currencies
	global $WOOCS;
	if ( isset( $WOOCS ) ) {
		$all_currencies = cw_woocs_get_currencies();
	}

	// Add/override currencies with WooCommerce Multi Currency plugin currencies
	if ( class_exists( 'WOOMULTI_CURRENCY_Data' ) ) {
		$setting        = WOOMULTI_CURRENCY_Data::get_ins();
		$all_currencies = array_merge( $all_currencies, $setting->get_list_currencies() );
	}

	return $all_currencies;
}

function cw_woocs_get_current_currency() {
	global $WOOCS;
	return cw_woocs_get_currencies()[ $WOOCS->current_currency ]['name'];
}

function cw_woocs_get_currencies() {
	global $WOOCS;
	return $WOOCS->get_currencies();
}

/**
 * Calculate price via WooCommerce Multi Currency switcher plugin
 * Plugin URI: https://villatheme.com/extensions/woo-multi-currency/
 *
 * @param float $price The price to convert to the active currency
 *
 * @return float|string|string[]
 */
function cw_wmc_calculate_price( $price ) {
	if ( function_exists( 'wmc_get_price' ) ) {
		$setting     = WOOMULTI_CURRENCY_Data::get_ins();
		$to_currency = $setting->get_current_currency();
		$old_price   = $price;
		$price       = wmc_get_price( $price, $to_currency );
		CW_AdminMain::cryptowoo_log_data(
			0,
			__FUNCTION__,
			array(
				'old_price'   => $old_price,
				'new_price'   => $price,
				'to_currency' => $to_currency,
			),
			'debug'
		);
	}

	return $price;
}

/**
 * Add exchange rates for enabled currencies to "WooCommerce Multilingual (WCML)" plugin
 * see https://wpml.org/wcml-hook/wcml_exchange_rates/
 *
 * @param array $exchange_rates The array includes your site currencies’ rates against the default currency.
 *
 * @return array
 */
function cwwcml_filter_exchange_rates( $exchange_rates ) {

	$options = get_option( 'cryptowoo_payments' );

	// Get WooCommerce Currency
	$woocommerce_currency = cw_get_woocommerce_default_currency();

	// Maybe get currency settings from 10s transient
	$cached = get_transient( 'cryptopay-wcml' );
	if ( false !== $cached ) {
		return $cached;
	}

	$currency_is_fiat = CW_ExchangeRates::tools()->currency_is_fiat( $woocommerce_currency );

	if ( is_array( $options ) ) {

		$enabled_currencies                             = cw_get_enabled_currencies();
		$currency_is_fiat ?: $enabled_currencies['USD'] = 'USD';

		foreach ( $enabled_currencies as $enabled_currency => $nicename ) {

			if ( ! strpos( $enabled_currency, 'TEST' ) ) {

				// Get multiplier option
				$multiplier_key = 'multiplier_' . strtolower( $enabled_currency );
				$multiplier     = isset( $options[ $multiplier_key ] ) ? $options[ $multiplier_key ] : 1;

				$rate = CW_ExchangeRates::processing()->get_exchange_rate( $enabled_currency, false, $woocommerce_currency );

				// Create args for currency
				if ( is_numeric( $rate ) ) {
					// Prepare exchange rate, set to 1 if rate not found
					$prep_rate = $rate > 0 && $multiplier > 0 ? ( 1 / ( $rate / (float) $multiplier ) ) : 1;

					$exchange_rates[ $enabled_currency ] = $prep_rate;
				}
			}
		}
		set_transient( 'cryptopay-wcml', $exchange_rates, 10 );
	}
	return $exchange_rates;
}

/**
 *
 * Get CryptoPay options.
 *
 * @return array|false
 */
function cw_get_options() {
	return get_option( 'cryptowoo_payments' );
}

/**
 *
 * Get a single CryptoPay option.
 *
 * @param string $option_id     Option identifier.
 * @param string $default_value Default value to return if the option is missing.
 *
 * @return mixed|false
 */
function cw_get_option( $option_id, $default_value = false ) {
	$cw_options = cw_get_options();

	return isset( $cw_options[ $option_id ] ) ? $cw_options[ $option_id ] : $default_value;
}

/**
 *
 * Update all CryptoPay options.
 *
 * @param array $updated_options CryptoPay options.
 *
 * @return bool
 */
function cw_update_all_options( $updated_options ) {
	return update_option( 'cryptowoo_payments', $updated_options );
}

/**
 *
 * Update a single CryptoPay option.
 *
 * @param string $option_id    Option identifier.
 * @param mixed  $option_value The new option value.
 *
 * @return bool
 */
function cw_update_option( string $option_id, $option_value ) {
	$cw_options = cw_get_options();

	$cw_options[ $option_id ] = $option_value;

	return cw_update_all_options( $cw_options );
}

/**
 *
 * Update multiple CryptoPay options.
 *
 * @param array $new_options Options to update [option_id => option_value].
 *
 * @return bool
 */
function cw_update_options( array $new_options ) {
	$cw_options = cw_get_options();

	foreach ( $new_options as $option_id => $option_value ) {
		$cw_options[ $option_id ] = $option_value;
	}

	return cw_update_all_options( $cw_options );
}

/**
 * Aelia Currency Switcher for WooCommerce compatibility
 * https://aelia.co/shop/currency-switcher-woocommerce/
 * Appends custom exchange rates to the list returned by the FX provider classes
 *
 * @param array              $rates                Existing exchange rates in the switcher
 * @param string             $base_currency        Base currency
 * @param ExchangeRatesModel $exchange_rates_model Aelia exchange rate class
 *
 * @return array
 */
function cw_add_currencies_to_aelia( $rates, $base_currency, $exchange_rates_model ) {

	$options = get_option( 'cryptowoo_payments' );

	// Get WooCommerce currency
	$woocommerce_currency = cw_get_woocommerce_default_currency();

	// Maybe get currency settings from 10s transient
	$cached = get_transient( 'cryptopay-aelia' );
	if ( false !== $cached ) {
		return $cached;
	}

	$add_rates = array();
	if ( is_array( $options ) ) {

		$enabled_currencies = cw_get_enabled_currencies();

		foreach ( $enabled_currencies as $enabled_currency => $nicename ) {

			if ( ! strpos( $enabled_currency, 'TEST' ) ) {

				// Get multiplier option
				$multiplier_key = 'multiplier_' . strtolower( $enabled_currency );
				$multiplier     = isset( $options[ $multiplier_key ] ) ? $options[ $multiplier_key ] : 1;

				$rate = CW_ExchangeRates::processing()->get_exchange_rate( $enabled_currency, false, $woocommerce_currency );

				// Add rate
				if ( $rate && is_numeric( $rate ) ) {
					// Prepare exchange rate, set to 1 if rate not found
					$prep_rate                      = $rate > 0 && $multiplier > 0 ? ( 1 / ( $rate / (float) $multiplier ) ) : 1;
					$add_rates[ $enabled_currency ] = number_format( $prep_rate, 8, '.', '' );
				}
			}
		}
	}
	if ( 'BTC' === $woocommerce_currency ) {

		// Add USD/BTC rate
		$usd_btc      = CW_ExchangeRates::processing()->get_exchange_rate( 'USD', false, 'BTC' );
		$rates['USD'] = number_format( $usd_btc, 8, '.', '' );

		// Calculate rates for other fiat currencies
		foreach ( $rates as $fiat_currency => $fiat_usd_rate ) {
			if ( 'USD' === $fiat_currency || ! CW_ExchangeRates::tools()->currency_is_fiat( $fiat_currency ) ) {
				continue;
			}
			$rates[ $fiat_currency ] = number_format( $usd_btc * $fiat_usd_rate, 8, '.', '' );
		}
	}
	$merged = array_merge( $rates, $add_rates );
	set_transient( 'cryptopay-aelia', $merged, 10 );

	return $merged;
}

add_filter( 'wc_aelia_exchange_rates', 'cw_add_currencies_to_aelia', 10, 3 );

/**
 * Aelia Currency Switcher: Use 8 as the number of decimals that will be preserved for an exchange rate
 *
 * @param int    $decimals        Number of decimals. Default: 6
 * @param string $target_currency Target currency
 * @param string $base_currency   Base currency
 *
 * @return int
 */
function cw_aelia_override_decimals( $decimals, $target_currency, $base_currency ) {
	if ( ! CW_ExchangeRates::tools()->currency_is_fiat( $target_currency ) ) {
		$decimals = 8;
	}

	return $decimals;
}

add_filter( 'wc_aelia_afc_exchange_rates_decimals', 'cw_aelia_override_decimals', 10, 3 );

/**
 * Override base currency in Aelia Currency Switcher to USD if it is not a fiat currency
 *
 * @param $base_currency
 *
 * @return string
 */
function cw_aelia_override_base_currency( $base_currency ) {

	if ( ! CW_ExchangeRates::tools()->currency_is_fiat( $base_currency ) ) {
		$base_currency = 'USD';
	}
	return $base_currency;
}

add_filter( 'wc_aelia_exchange_rates_base_currency', 'cw_aelia_override_base_currency', 10, 1 );

function cw_aelia_override_enabled_currencies( $enabled_currencies ) {
	$fiat_currencies = array();
	foreach ( $enabled_currencies as $currency ) {
		if ( CW_ExchangeRates::tools()->currency_is_fiat( $currency ) ) {
			$fiat_currencies[] = $currency;
		}
	}
	if ( ! count( $fiat_currencies ) ) {
		$fiat_currencies = array( 'USD' );
	}
	return $fiat_currencies;
}
add_filter( 'wc_aelia_exchange_rates_currencies', 'cw_aelia_override_enabled_currencies', 10, 1 );
