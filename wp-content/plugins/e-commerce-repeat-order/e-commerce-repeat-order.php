<?php

/**
 * Plugin Name: Repeat Order for Woocommerce
 * Plugin URI: https://poly-res.com/plugins/repeat-order-for-woocommerce/
 * Description: Add an "order again" button in Recent Orders list
 * Version: 1.2.0
 * Author: polyres
 * Author URI: https://poly-res.com/
 * Text Domain: repeat-order-for-woocommerce
 * Domain Path: /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/PolyRes/repeat-order-for-woocommerce
 * GitHub Branch:     master
 * Requires WP:       4.8
 * Requires PHP:      5.3
 * Tested up to: 5.8.1
 * WC requires at least: 3.4.0
 * WC tested up to: 5.7.0
 *
 * @link      https://poly-res.com
 * @author    Frank Neumann-Staude
 * @license   GPL-2.0+
 *
 * CodeRisk Maintainer: http://coderisk.com/wp/plugin/repeat-order-for-woocommerce/RIPS-sWgOoMX8XN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'PRRO_VERSION', '1.2.0' );

class RepeatOrderForWoocommerce {

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 * @access  public
	 * @action repeat_order_for_woocommerce_init
	 */
	public function __construct() {
		$plugin = plugin_basename( __FILE__ );
		add_filter( 'woocommerce_settings_tabs_array',          array( $this, 'add_settings_tab' ), 50 );
		add_action( 'woocommerce_settings_tabs_repeat_order',   array( $this, 'settings_tab' ) );
		add_action( 'woocommerce_update_options_repeat_order',  array( $this, 'update_settings' ) );
		add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'add_order_again_action' ), 10, 2 );
		add_action( 'woocommerce_ordered_again',                array( $this, 'ordered_again' ) );
		add_action( 'woocommerce_thankyou',                     array( $this, 'create_order_note') );
		add_action( 'woocommerce_cart_is_empty',                array( $this, 'reset_session_flag') );
		add_filter( "plugin_action_links_$plugin",              array( $this, 'plugin_add_settings_link' ) );
		add_action( 'init',                                     array( $this, 'reactivate_action'), 9999 );
		add_action( 'init',                                     array( $this, 'load_plugin_textdomain') );
		add_action( 'current_screen',                           array( $this, 'current_screen') );
		add_filter( 'woocommerce_admin_order_actions',          array( $this, 'add_custom_order_status_actions_button' ), 100, 2 );
		// if WP < 5.0
		add_action( 'init',                                     array( $this, 'allow_data_order_id' ) );

		register_activation_hook( __FILE__,                 array( 'RepeatOrderForWoocommerce', 'install' ) );
		register_uninstall_hook( __FILE__,                  array( 'RepeatOrderForWoocommerce', 'uninstall' ) );

		do_action( 'repeat_order_for_woocommerce_init' );
	}

	/**
	 * Load the translation
	 *
	 * @since    1.0.0
	 * @access  public
	 * @filter plugin_locale
	 */
	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'repeat-order-for-woocommerce' );

		load_textdomain( 'repeat-order-for-woocommerce', trailingslashit( WP_LANG_DIR ) . 'repeat-order-for-woocommerce/repeat-order-for-woocommerce-' . $locale . '.mo' );
		load_plugin_textdomain( 'repeat-order-for-woocommerce', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Reactivate the reorder link in order details
	 *
	 * @since    1.0.0
	 * @access  public
	 */
	public function reactivate_action() {
		if ( get_option( 'prro_reactivate_action' ) == 'yes' ) {
			add_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button', 9999 );
		}
	}

	/**
	 * Add a link to plugin settings to the plugin list
	 *
	 * @since    1.0.0
	 * @access  public
	 */
	public function plugin_add_settings_link( $links ) {
		$settings_link = '<a href="'. admin_url( 'admin.php?page=wc-settings&tab=repeat_order' ) .'">' . __( 'Settings' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Save old order id to woocommerce session
	 *
	 * @since    1.0.0
	 * @access  public
	 */
	public function ordered_again( $order_id ) {
		WC()->session->set( 'reorder_from_orderid', $order_id );
		$notice = get_option( 'prro_cart_notice' );
		if ( $notice != '' ) {
			wc_add_notice( $notice, 'notice' );
		}
	}

	/**
	 * Create a order note with link to the old order
	 *
	 * @since    1.0.0
	 * @access  public
	 * @filters repeat_order_for_woocommerce_order_note
	 */
	public function create_order_note( $order_id ) {
		$reorder_id = WC()->session->get( 'reorder_from_orderid');
		if ($reorder_id != '' ) {
            add_post_meta( $order_id, '_reorder_from_id', $reorder_id, true );
		}
		if ( get_option( 'prro_create_order_note' ) != 'yes' ) {
			return;
		}
		if ($reorder_id != '' ) {
			$order = wc_get_order( $order_id );
			$url = get_edit_post_link( $reorder_id );
			$note = sprintf( wp_kses( __( 'This is an reorder of order #<a href="%1s">%2s</a> <a href="#" class="order-preview" data-order-id="%3s" title="Vorschau"></a>. As a rule, customers can access items that have already been saved and linked to the selected delivery address when placing a "new order". Please note, however, that customers may have changed the number and quantity of items during the ordering process.', 'repeat-order-for-woocommerce' ), array(  'a' => array( 'href' => array(), 'class' => array(), 'data-order-id' => array() ) ) ), esc_url( $url ), $reorder_id,  $reorder_id );
			$order->add_order_note( apply_filters( 'repeat_order_for_woocommerce_order_note', $note, $reorder_id, $order_id ) );
		}
		WC()->session->set( 'reorder_from_orderid' , '' );
	}

	/**
	 * Add a reorder link to the order list in user account
	 *
	 * @since    1.0.0
	 * @access  public
	 * @filter  repeat_order_for_woocommerce_order_note
	 */
	public function add_order_again_action( $actions, $order ) {
		if ( get_option( 'prro_show_repeat_order_on_order_list' ) != 'yes' ) {
			return $actions;
		}
		if ( ! $order || ! $order->has_status( apply_filters( 'woocommerce_valid_order_statuses_for_order_again', array( 'completed' ) ) ) || ! is_user_logged_in() ) {
			return $actions;
		}

		$actions['order-again'] = array(
			'url'  => wp_nonce_url( add_query_arg( 'order_again', $order->get_id() ) , 'woocommerce-order_again' ),
			'name' => __( 'Order again', 'woocommerce' )
		);

		return $actions;
	}

	/**
	 * Add a new settings tab to woocommerce/settings
	 *
	 * @since    1.0.0
	 * @access  public
	 */
	public function add_settings_tab( $settings_tabs ) {
		$settings_tabs['repeat_order'] = _x( 'Repeat Order', 'WooCommerce Settngs Tab', 'repeat-order-for-woocommerce' );
		return $settings_tabs;
	}

	/**
	 * @ince    1.0.0
	 * @access  public
	 */
	public  function settings_tab() {
		woocommerce_admin_fields( self::get_settings() );
	}

	/**
	 * @since    1.0.0
	 * @access  public
	 */
	function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}


	/**
	 * Define the settings for this plugin
	 *
	 * @since    1.0.0
	 * @access  public
	 * @filters repeat_order_for_woocommerce_settings
	 */
	public function get_settings() {
		$settings = array(
			'section_title' => array(
				'name'     => __( 'Repeat Order', 'repeat-order-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => '',
				'id'       => 'prro_section_title'
			),
			'hide_in_loop' => array(
				'name' => __( 'Show link on order list', 'repeat-order-for-woocommerce' ),
				'type' => 'checkbox',
				'desc' => __( 'If checked, it show the repeat order link on the order list', 'repeat-order-for-woocommerce' ),
				'id'   => 'prro_show_repeat_order_on_order_list'
			),
			'hide_in_cart' => array(
				'name' => __( 'Create order note', 'repeat-order-for-woocommerce' ),
				'type' => 'checkbox',
				'desc' => __( 'If checked, it create an order note with a link to the original order.', 'repeat-order-for-woocommerce' ),
				'id'   => 'prro_create_order_note'
			),
			'reactivate_order_again' => array(
				'name' => __( 'Reactivate order again in order detail', 'repeat-order-for-woocommerce' ),
				'type' => 'checkbox',
				'desc' => __( 'If you are using a plugin or theme who deactivate the order again link/button or you have the plugin WooCommerce Germanized (with version 2.0.4 or older) activated then check this option to reactivate the action.', 'repeat-order-for-woocommerce' ),
				'id'   => 'prro_reactivate_action'
			),
			'cart_notice' => array(
				'name' => __( 'Your own notice in cart, after an reorder', 'repeat-order-for-woocommerce' ),
				'type' => 'text',
				'desc' => __( 'Display an own notice in the cart, after the reorder link is clicked.', 'repeat-order-for-woocommerce' ),
				'id'   => 'prro_cart_notice'
			),
		);

		$settings['section_end'] = array(
			'type' => 'sectionend',
			'id' => 'prro_section_end'
		);

		return apply_filters( 'repeat_order_for_woocommerce_settings', $settings );
	}

	/**
	 * Check cart, if empty reset the reorder flag in woocommerce session
	 *
	 * @since    1.0.0
	 * @access  public
	 */
	public function reset_session_flag() {
		WC()->session->set( 'reorder_from_orderid' , '' );
	}

	/**
	 * Setup Database on activating the plugin
	 *
	 * @since    1.0.0
	 * @access  public
	 */
	static public function install() {
		if ( false === get_option( 'prro_show_repeat_order_on_order_list' ) ) {
			add_option( 'prro_show_repeat_order_on_order_list', 'yes' );
		}
		if ( false === get_option( 'prro_create_order_note' ) ) {
			add_option( 'prro_create_order_note', 'yes' );
		}
		if ( false === get_option( 'prro_reactivate_action' ) ) {
			add_option( 'prro_reactivate_action', 'no' );
		}
		if ( false === get_option( 'prro_cart_notice' ) ) {
			add_option( 'prro_cart_notice', '' );
		}
	}

	/**
	 * Cleanup Database on deleting the plugin
	 *
	 * @since    1.0.0
	 * @access  public
	 */
	static public function uninstall() {
		delete_option( 'prro_show_repeat_order_on_order_list' );
		delete_option( 'prro_create_order_note' );
		delete_option( 'prro_reactivate_action' );
		delete_option( 'prro_cart_notice' );
	}

	/**
	 *
	 * @since    1.1.0
	 * @access  public
	 */
	public function allow_data_order_id() {
		global $allowedposttags, $allowedtags;
		$newattribute = "data-order-id";

		$allowedposttags["a"][$newattribute] = true;
		$allowedtags["a"][$newattribute] = true;
    }

	/**
	 *
	 * @since    1.1.0
	 * @access  public
	 */
	public function current_screen() {
		$cs = get_current_screen();
		if ( $cs->post_type == 'shop_order' ) {
			add_action( 'admin_footer', array( $this, 'order_preview_template' ) );
		    add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}
	}

	/**
	 *
	 * @since    1.1.0
	 * @access  public
	 */
	public function admin_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'wc-orders', WC()->plugin_url() . '/assets/js/admin/wc-orders' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-blockui' ), PRRO_VERSION );
		wp_localize_script(
			'wc-orders',
			'wc_orders_params',
			array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'preview_nonce' => wp_create_nonce( 'woocommerce-preview-order' ),
			)
		);
		wp_enqueue_script( 'wc-orders' );
		wp_register_style( 'prro_admin_menu_styles', plugins_url('style.css', __FILE__), array(), PRRO_VERSION );
		wp_enqueue_style( 'prro_admin_menu_styles' );
    }

	/**
	 * Order Preview Template
	 *
	 * copyied from WooCommerve  class-wc-admin-list-table-orders
	 *
	 * @since    1.1.0
	 * @access  public
	 */
	static public function order_preview_template() {
		?>
		<script type="text/template" id="tmpl-wc-modal-view-order">
			<div class="wc-backbone-modal wc-order-preview">
				<div class="wc-backbone-modal-content">
					<section class="wc-backbone-modal-main" role="main">
						<header class="wc-backbone-modal-header">
							<mark class="order-status status-{{ data.status }}"><span>{{ data.status_name }}</span></mark>
							<?php /* translators: %s: order ID */ ?>
							<h1><?php echo esc_html( sprintf( __( 'Order #%s', 'woocommerce' ), '{{ data.order_number }}' ) ); ?></h1>
							<button class="modal-close modal-close-link dashicons dashicons-no-alt">
								<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce' ); ?></span>
							</button>
						</header>
						<article>
							<?php do_action( 'woocommerce_admin_order_preview_start' ); ?>

							<div class="wc-order-preview-addresses">
								<div class="wc-order-preview-address">
									<h2><?php esc_html_e( 'Billing details', 'woocommerce' ); ?></h2>
									{{{ data.formatted_billing_address }}}

									<# if ( data.data.billing.email ) { #>
									<strong><?php esc_html_e( 'Email', 'woocommerce' ); ?></strong>
									<a href="mailto:{{ data.data.billing.email }}">{{ data.data.billing.email }}</a>
									<# } #>

									<# if ( data.data.billing.phone ) { #>
									<strong><?php esc_html_e( 'Phone', 'woocommerce' ); ?></strong>
									<a href="tel:{{ data.data.billing.phone }}">{{ data.data.billing.phone }}</a>
									<# } #>

									<# if ( data.payment_via ) { #>
									<strong><?php esc_html_e( 'Payment via', 'woocommerce' ); ?></strong>
									{{{ data.payment_via }}}
									<# } #>
								</div>
								<# if ( data.needs_shipping ) { #>
								<div class="wc-order-preview-address">
									<h2><?php esc_html_e( 'Shipping details', 'woocommerce' ); ?></h2>
									<# if ( data.ship_to_billing ) { #>
									{{{ data.formatted_billing_address }}}
									<# } else { #>
									<a href="{{ data.shipping_address_map_url }}" target="_blank">{{{ data.formatted_shipping_address }}}</a>
									<# } #>

									<# if ( data.shipping_via ) { #>
									<strong><?php esc_html_e( 'Shipping method', 'woocommerce' ); ?></strong>
									{{ data.shipping_via }}
									<# } #>
								</div>
								<# } #>

								<# if ( data.data.customer_note ) { #>
								<div class="wc-order-preview-note">
									<strong><?php esc_html_e( 'Note', 'woocommerce' ); ?></strong>
									{{ data.data.customer_note }}
								</div>
								<# } #>
							</div>

							{{{ data.item_html }}}

							<?php do_action( 'woocommerce_admin_order_preview_end' ); ?>
						</article>
						<footer>
							<div class="inner">
								{{{ data.actions_html }}}

								<a class="button button-primary button-large" aria-label="<?php esc_attr_e( 'Edit this order', 'woocommerce' ); ?>" href="<?php echo esc_url( admin_url( 'post.php?action=edit' ) ); ?>&post={{ data.data.id }}"><?php esc_html_e( 'Edit', 'woocommerce' ); ?></a>
							</div>
						</footer>
					</section>
				</div>
			</div>
			<div class="wc-backbone-modal-backdrop modal-close"></div>
		</script>
		<?php
	}

	public function add_custom_order_status_actions_button( $actions, $order ) {
	    $order_id = $order->get_id();
	    $reorder_from  = get_post_meta( $order_id, '_reorder_from_id', true );

	    if ( $reorder_from > 0 ) {
            $actions['pr_repeat'] = array(
                'url'       => wp_nonce_url( admin_url( 'post.php?post=' . $reorder_from . '&action=edit' ), '' ),
                'name'      => __( 'Show original order', 'repeat-order-for-woocommerce' ),
                'action'    => "view-original-order",
            );
	    }
		return $actions;
	}
}

$polyresRepeatOrderForWoocommerce = new RepeatOrderForWoocommerce();
