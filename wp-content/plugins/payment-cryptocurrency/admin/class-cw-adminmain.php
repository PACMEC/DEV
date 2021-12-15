<?php
/**
 * CryptoWoo Admin helper functions class
 *
 * @package CryptoWoo
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Admin helper functions
 *
 * @package    CryptoWoo
 * @subpackage Admin
 */
class CW_AdminMain {


	/**
	 * Get Block.io API key, optionally force corresponding livenet currency API key
	 *
	 * @param string $currency      Currency code (eg BTC).
	 * @param bool   $force_livenet Forces livenet if true.
	 *
	 * @return mixed
	 */
	public static function get_blockio_api_key( $currency, $force_livenet = false ) {
		$stripped = strpos( $currency, 'TEST' ) ? strtolower( str_replace( 'TEST', $force_livenet ? '' : 'test', $currency ) ) : strtolower( $currency );
		return cw_get_option( "cryptowoo_{$stripped}_api", '' );
	}

	/**
	 * On activation, set a time, frequency and name of an action hook to be scheduled.
	 */
	public function cryptowoo_cron_activation_schedule() {
		if ( is_array( cw_get_options() ) ) {
			$starttime = time();

			$interval = cw_get_option( 'soft_cron_interval', 'seconds_30' );

			if ( ! wp_next_scheduled( 'cryptowoo_cron_action' ) ) {
				wp_schedule_event( $starttime + 2, $interval, 'cryptowoo_cron_action' );
			} elseif ( wp_get_schedule( 'cryptowoo_cron_action' ) !== $interval ) {
				// Interval has changed - reset hook.
				wp_clear_scheduled_hook( 'cryptowoo_cron_action' );
				wp_schedule_event( $starttime + 2, $interval, 'cryptowoo_cron_action' );
			}
			if ( ! wp_next_scheduled( 'cryptowoo_integrity_check' ) ) {
				wp_schedule_event( $starttime + 20, 'hourly', 'cryptowoo_integrity_check' );
			}
			if ( ! wp_next_scheduled( 'cryptowoo_archive_addresses' ) && cw_get_option( 'auto_archive_addresses' ) ) {
				// Maybe archive Block.io addresses.
				wp_schedule_event( $starttime + 60, 'daily', 'cryptowoo_archive_addresses' );
			}
		}
		return true;
	}

	/**
	 * Add custom intervals to WP Cron
	 *
	 * @param array $schedules Cron schedules.
	 *
	 * @return mixed
	 */
	public function cron_add_schedules( $schedules ) {

		$custom_schedules = array(
			'seconds_15'  => array(
				'interval' => 15,
				/* translators: %s: number of seconds */
				'display'  => sprintf( __( 'Once every %s seconds', 'cryptowoo' ), 15 ),
			),
			'seconds_30'  => array(
				'interval' => 30,
				/* translators: %s: number of seconds */
				'display'  => sprintf( __( 'Once every %s seconds', 'cryptowoo' ), 30 ),
			),
			'seconds_60'  => array(
				'interval' => 60,
				/* translators: %s: number of minutes */
				'display'  => sprintf( __( 'Once every %s minutes', 'cryptowoo' ), 1 ),
			),
			'seconds_120' => array(
				'interval' => 120,
				/* translators: %s: number of minutes */
				'display'  => sprintf( __( 'Once every %s minutes', 'cryptowoo' ), 2 ),
			),
			'seconds_300' => array(
				'interval' => 300,
				/* translators: %s: number of minutes */
				'display'  => sprintf( __( 'Once every %s minutes' ), 5 ),
			),
		);
		return array_merge( $custom_schedules, $schedules );
	}

	/**
	 * On the scheduled action hook, process open orders and update exchange rates.
	 *
	 * @param bool $return Return data if true.
	 *
	 * @return array|true
	 * @todo   Add log verbosity options
	 */
	public function do_cryptowoo_cron_action( $return = false ) {

		$time_begin = microtime( true );

		/* phpcs:ignore
		// Check cron lock
		$lock = get_option('cryptowoo_cron_running');
		$interval = (int)str_replace('seconds_', '', cw_get_option( 'soft_cron_interval'));
		if(false !== $lock && (($lock + $interval) < $time_begin)) {
		return 'Cron already running';
		} else {
		update_option('cryptowoo_cron_running', $time_begin);
		}
		*/

		// Update transaction details for open orders.
		$tx_update = CW_OrderProcessing::block_explorer()->update_tx_details();
		if ( self::logging_is_enabled( 'debug' ) ) {
			$data['order-processing']['tx-update'] = isset( $tx_update['info'] ) ? $tx_update['info'] : $tx_update;
		} else {
			$data['order-processing']['tx-update'] = isset( $tx_update['info'] ) ? $tx_update['info'] : array( 'status' => 'success' );
		}
		$tx_update_time = microtime( true );

		// Maybe process open orders.
		if ( ! is_string( $data['order-processing']['tx-update'] ) ) { // TODO Revisit force order update.
			$processing_result = CW_Block_Explorer_Processing::process_open_orders();
			$unpaid_addresses  = isset( $processing_result['unpaid_addresses'] ) ? $processing_result['unpaid_addresses'] : 'No unpaid addresses found';
			if ( self::logging_is_enabled( 'debug' ) ) {
				// Full logging.
				$data['order-processing']['order-update'] = $processing_result;
			} else {
				$data['order-processing']['order-update'] = array( esc_html__( 'unpaid_addresses', 'cryptowoo' ) => $unpaid_addresses );
			}
		} else {
			$data['order-processing']['order-update'] = esc_html__( 'Transaction data unchanged - processing skipped', 'cryptowoo' );
		}
		$order_update_time = microtime( true );

		// Update exchange rates.
		$coins = array( 'BTC', 'BCH', 'LTC', 'DOGE', 'BLK' );
		$data  = apply_filters( 'cw_cron_update_exchange_data', array(), cw_get_options() ); // Add-on hook for rate updates.
		foreach ( $coins as $coin ) {
			$coin_data = CW_ExchangeRates::processing()->update_coin_rates( $coin );

			// Maybe log exchange rate updates.
			if ( self::logging_is_enabled( 'debug' ) ) {
				if ( 'not updated' === $coin_data['status'] || strpos( $coin_data['status'], 'disabled' ) ) {
					$data[ $coin ] = strpos( $coin_data['status'], 'disabled' ) ? $coin_data['status'] : $coin_data['last_update'];
				} else {
					$data[ $coin ] = $coin_data;
				}
			}
		}

		// Maybe log exchange rate updates.
		if ( self::logging_is_enabled( 'debug' ) ) {
			$data['durations']['tx-update-duration']    = round( $tx_update_time - $time_begin, 4 ) . 'sec';
			$data['durations']['order-update-duration'] = round( $order_update_time - $tx_update_time, 4 ) . 'sec';
			$data['durations']['rate-updates']          = round( microtime( true ) - $order_update_time, 4 ) . 'sec';

			if ( ! empty( cw_get_option( 'blockcypher_token' ) ) ) {
				$data['rate_limit'] = self::get_blockcypher_limit();
			}
		}
		if ( self::logging_is_enabled( 'debug' ) ) {
			self::cryptowoo_log_data( $time_begin, __FUNCTION__, $data, 'debug' );
		}
		do_action( 'cwrc_catch_request', $data );
		// Remove lock.
		// delete_option('cryptowoo_cron_running');.
		return $return ? $data : true;
	}

	/**
	 * Check if debugging is enabled or not.
	 */
	public static function debug_is_enabled() {
		// We will only log if debug logging is enabled in cryptowoo settings or WP_DEBUG is not enabled.
		return ( cw_get_option( 'enable_debug_log' ) ) || defined( 'WP_DEBUG' ) && WP_DEBUG;
	}

	/**
	 * Get an array of log severity levels to log
	 *
	 * @param string $level One of the following:
	 *                      'emergency': System is unusable.
	 *                      'alert': Action must be taken immediately.
	 *                      'critical': Critical conditions.
	 *                      'error': Error conditions.
	 *                      'warning': Warning conditions.
	 *                      'notice': Normal but significant condition.
	 *                      'info': Informational messages.
	 *                      'debug': Debug-level messages.
	 */
	public static function get_log_severity_array( $level = 'debug' ) {
		if ( empty( $level ) ) {
			$level = 'error'; // Default log level is error.
		}

		$severity_levels = array(
			'emergency' => __( 'Emergency: System is unusable.', 'cryptowoo' ),
			'alert'     => __( 'Alert: Action must be taken immediately.', 'cryptowoo' ),
			'critical'  => __( 'Critical: Critical conditions.', 'cryptowoo' ),
			'error'     => __( 'Error: Error conditions.', 'cryptowoo' ),
			'warning'   => __( 'Warning: Warning conditions.', 'cryptowoo' ),
			'notice'    => __( 'Notice: Normal but significant condition.', 'cryptowoo' ),
			'info'      => __( 'Info: Informational messages.', 'cryptowoo' ),
			'debug'     => __( 'Debug: Debug-level messages.', 'cryptowoo' ),
		);

		$level_position_in_array = 1 + array_search( $level, array_keys( $severity_levels ), true );

		return array_slice( $severity_levels, 0, $level_position_in_array, true );
	}

	/**
	 * Get an array of log severity levels to log
	 *
	 * @param string $level One of the following:
	 *                      'emergency': System is unusable.
	 *                      'alert': Action must be taken immediately.
	 *                      'critical': Critical conditions.
	 *                      'error': Error conditions.
	 *                      'warning': Warning conditions.
	 *                      'notice': Normal but significant condition.
	 *                      'info': Informational messages.
	 *                      'debug': Debug-level messages.
	 */
	public static function logging_is_enabled( $level = 'error' ) {
		// We will only log if debug logging is enabled in cryptowoo settings or WP_DEBUG is not enabled.
		if ( false === self::debug_is_enabled() ) {
			return false;
		}

		// Backwards compatibility with old settings values - array to string.
		if ( is_array( cw_get_option( 'logging' ) ) ) {
			if ( in_array( '1', cw_get_option( 'logging' ), true ) ) {
				// Logging was enabled - set to debug.
				cw_update_option( 'logging', 'debug' );
			} else {
				// Logging was disabled - set to default.
				cw_update_option( 'logging', 'error' );
			}
		}
		// We will only log if the severity is equal or worse than the minimum severity in CryptoWoo settings.
		$log_severity_array = self::get_log_severity_array( cw_get_option( 'logging', 'error' ) );
		return array_key_exists( $level, $log_severity_array );
	}

	/**
	 * Log data using WC_Logger
	 *
	 * @param int    $time_begin The time of the event to be logged.
	 * @param string $function   Function name where the log occurred.
	 * @param mixed  $data       Data to include in the log.
	 * @param string $log_level  Log level determines which type of events that are logged according to user settings.
	 *
	 * @since 0.22.3 All log messages must now include a level.
	 * They express the severity of the log message from debug to emergency, lowest to highest.
	 * They are described in the IEFT RFC5424 spec:
	 *    Emergency: system is unusable
	 *    Alert: action must be taken immediately
	 *    Critical: critical conditions
	 *    Error: error conditions
	 *    Warning: warning conditions
	 *    Notice: normal but significant condition
	 *    Informational: informational messages
	 *    Debug: debug-level messages
	 */
	public static function cryptowoo_log_data( $time_begin, $function, $data, $log_level = 'info' ) {

		// This function must run after WooCommerce is loaded to access wc_logger.
		if ( ! class_exists( WooCommerce::class ) ) {
			add_action(
				'woocommerce_init',
				function () use ( $time_begin, $function, $data, $log_level ) {
					self::cryptowoo_log_data( $time_begin, $function, $data, $log_level );
				}
			);
			return;
		}

		$handle = 'cryptowoo';

		// TODO Remove after all calls of $filename have been replaced with $log_level.
		$log_level = preg_replace( '/cryptowoo-|.log/', '', $log_level );

		// Only log entries above selected level.
		if ( self::logging_is_enabled( $log_level ) ) {

			if ( $time_begin > 0 && is_array( $data ) ) {
				$data['durations']['full-runtime'] = round( microtime( true ) - $time_begin, 4 ) . 'sec';
			}

			$msg = sprintf( "- %s\r\n%s", $function, var_export( $data, true ) ); // phpcs:disable WordPress.PHP.DevelopmentFunctions

			$logger = wc_get_logger();

			$context = array( 'source' => $handle );

			$logger->log( $log_level, $msg, $context );
		}

	}

	/**
	 * Log detailed exchange rate error data
	 *
	 * @param array $rate_error_details Exchange rate error details.
	 */
	public static function cryptowoo_log_rate_errors_for_chart( $rate_error_details ) {
		$rate_error_transient_key = 'cryptowoo_detailed_rate_errors';
		$transient                = get_transient( $rate_error_transient_key );
		if ( ! $transient ) {
			$transient = array( $rate_error_details );
		} else {
			$transient[] = $rate_error_details;
		}

		// Purge data older than a week.
		$count = count( $transient );
		for ( $i = 0; $i < $count; $i++ ) {
			if ( $transient[ $i ]['time'] < ( time() - WEEK_IN_SECONDS ) ) {
				array_splice( $transient, $i, 1 );
			}
			$count = count( $transient );
		}
		set_transient( $rate_error_transient_key, $transient, 2 * WEEK_IN_SECONDS );
	}

	/**
	 * Get BlockCypher API limit
	 *
	 * @param bool $force Force rechecking api limits at blockcypher if true.
	 *
	 * @return array|mixed
	 * @todo use neutral token to get current free tier limits
	 */
	public static function get_blockcypher_limit( $force = false ) {
		$blockcypher_token = cw_get_option( 'blockcypher_token' );

		if ( empty( $blockcypher_token ) ) {
			return array(
				'limit_hour'            => 200,
				'confidence_limit_hour' => 15,
				'limit_sec'             => 3,
			);
		}
		$keep_until = self::seconds_to_next_hour();
		$bc_status  = get_transient( 'blockcypher_token_limit' );
		if ( $force || false === $bc_status ) {
			$result = wp_safe_remote_get( "https://api.blockcypher.com/v1/tokens/{$blockcypher_token}" );
			if ( ! is_wp_error( $result ) ) {

				$result = json_decode( $result['body'] );
				$limits = isset( $result->limits ) ? (array) $result->limits : array(
					'api/hour'   => 200,
					'api/second' => 3,
				);

				$bc_status = array(
					'limit_hour'            => $limits['api/hour'],
					'confidence_limit_hour' => $limits['confidence/hour'],
					'limit_sec'             => $limits['api/second'],
				);

				if ( isset( $result->hits ) ) {
					$hits                   = (array) $result->hits;
					$bc_status['hits_hour'] = $hits['api/hour'];
					$bc_status['hits_sec']  = isset( $hits['api/second'] ) ? $hits['api/second'] : 'not_set';
				}
				$bc_status['time_to_reset'] = $keep_until;

				// Set transient, keep for 2 minutes.
				set_transient( 'blockcypher_token_limit', $bc_status, 600 );
			}
		}
		$bc_status['time_to_reset'] = $keep_until;
		return $bc_status;
	}


	/**
	 * Calculate seconds left to the next full hour
	 * Since we're dividing by 3600, it will give seconds since the last full hour increment (1:00, 2:00, 3:00, etc...).
	 * If it's 1:30:01, the modulo operation will return 1 and $prev will be 1:00. Adding 3600 seconds will make $next equal to 2:00.
	 *
	 * @return int
	 */
	public static function seconds_to_next_hour() {
		$now  = time();
		$prev = $now - ( $now % 3600 );
		$next = $prev + 3600;
		return absint( $now - $next );
	}

	/**
	 * On the scheduled action hook, archive Block.io addresses
	 */
	public function cryptowoo_archive_addresses() {
		$time_begin = microtime( true );
		$options    = cw_get_options();
		$data       = CW_Address::archive_addresses( $options );

		// Get status.
		if ( isset( $data['status'] ) && isset( $data['message'] ) && 'alert' === $data['status'] ) {
			// Send email to admin.
			self::send_blockio_address_archive_warning( $data );
		}

		if ( self::logging_is_enabled( 'debug' ) ) {
			self::cryptowoo_log_data( $time_begin, 'do_archive_addresses', $data, 'debug' );
		}
	}

	/**
	 * Send "Block.io Account threshold reached" e-Mail
	 *
	 * @param array $data Array with a value in key 'message'.
	 */
	public static function send_blockio_address_archive_warning( $data ) {

		$to       = get_option( 'admin_email' );
		$blogname = get_bloginfo( 'name', 'raw' );
		$subject  = sprintf( '%s: %s', $blogname, __( 'Block.io address archival warning', 'cryptowoo' ) );
		/* translators: %1$s: new line, %2$s: website name, %3$s%: new line, %4$s: warning message. */
		$message = sprintf( esc_html__( 'Hello Admin,%1$sCryptoWoo at %2$s has found an issue while archiving your addresses at Block.io:%3$s%4$s', 'cryptowoo' ), '<br>', $blogname, '<br>', $data['message'] );

		$headers = array( "From: CryptoWoo Plugin <{$to}>", 'Content-Type: text/html; charset=UTF-8' );

		wp_mail( $to, $subject, $message, $headers );

	}

	/**
	 * Order table sorting functions
	 *
	 * @param array $columns Order table columns.
	 *
	 * @return array
	 */
	public function cryptowoo_order_data( $columns ) {
		$new_columns = ( is_array( $columns ) ) ? $columns : array();

		// Actions key is order_actions before and wc_actions after wc version 3.3.
		$actions_key = array_key_exists( 'wc_actions', $columns ) ? 'wc_actions' : 'order_actions';
		// Actions should be last in the columns list so remove it for now.
		unset( $new_columns[ $actions_key ] );

		// edit this for you column(s)
		// all of your columns will be added before the actions column.
		$new_columns['crypto_amount']      = 'Amount due';
		$new_columns['received_confirmed'] = 'Amount received';
		$new_columns['payment_currency']   = 'Payment Currency';
		$new_columns['payment_address']    = 'Payment Address';

		// stop editing.

		// Actions should be last in columns list so add it back now.
		$new_columns[ $actions_key ] = $columns[ $actions_key ];

		return $new_columns;
	}

	/**
	 * Print order table column values
	 *
	 * @param array $column Order table column.
	 */
	public function cryptowoo_columns_values_function( $column ) {
		global $post;
		$data = get_post_meta( $post->ID );

		// start editing, I was saving my fields for the orders as custom post meta
		// if you did the same, follow this code.
		if ( 'crypto_amount' === $column ) {
			echo ( isset( $data['crypto_amount'][0] ) ? esc_attr( CW_Formatting::fbits( $data['crypto_amount'][0] ) ) : '0' );
		}
		if ( 'received_confirmed' === $column ) {
			echo ( isset( $data['received_confirmed'][0] ) ? esc_attr( CW_Formatting::fbits( $data['received_confirmed'][0] ) ) : '0' );
		}
		if ( 'payment_currency' === $column ) {
			echo ( isset( $data['payment_currency'][0] ) ? esc_attr( $data['payment_currency'][0] ) : '' );
		}
		if ( 'payment_address' === $column ) {
			echo ( isset( $data['payment_address'][0] ) && isset( $data['payment_currency'][0] ) && ! empty( $data['payment_currency'][0] ) ? wp_kses_post( CW_Formatting::link_to_address( $data['payment_currency'][0], $data['payment_address'][0], false, true ) ) : '' );
		}

		// stop editing.
	}

	/**
	 * Sort CryptoWoo columns on order edit page
	 *
	 * @param array $columns Columns array.
	 *
	 * @return array|object
	 */
	public function cryptowoo_columns_sort_function( $columns ) {
		$custom = array(
			'crypto_amount'      => 'crypto_amount',
			'received_confirmed' => 'received_confirmed',
			'payment_address'    => 'payment_address',
			'payment_currency'   => 'payment_currency',
		);

		return wp_parse_args( $custom, $columns );
	}

	/**
	 * If WooCommerce is not active deactivate cronjobs and disable CryptoWoo payments.
	 */
	public function cw_is_woocommerce_active() {
		if ( is_admin() ) {

			include_once ABSPATH . 'wp-admin/includes/plugin.php';

			// Add "Extension Disabled" notice.
			add_action( 'admin_notices', array( $this, 'cryptowoo_extension_notice' ) );
			if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

				// deactivate cronjobs.
				cryptowoo_plugin_deactivate();

				// Disable gateway.

				cw_update_options(
					array(
						'cryptowoo_price_rewrite' => 'disable',
						'cryptowoo_currency_switch_position' => 'disable',
						'enable_soft_cron'        => '0',
					)
				);
				/* // phpcs:ignore
				Inactive/Not installed WooCommerce is handled by TGMPA

				if(!file_exists(WP_PLUGIN_DIR.'woocommerce/woocommerce.php')) {
				// Add WooCommerce not installed notice
				add_action('admin_notices', array($this, ' cryptowoo_wc_notinstalled_notice'));
				} else {
				// Add "WooCommerce Disabled" notice
				add_action('admin_notices', array($this, 'cryptowoo_wc_inactive_notice'));
				}
				*/
				return;

			}
			if ( isset( $_SERVER['REQUEST_URI'] ) && false === strpos( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'cryptowoo' ) && ! cw_get_option( 'enabled' ) ) {

				// Add "Gateway Disabled" notice.
				add_action( 'admin_notices', array( $this, 'cryptowoo_cw_inactive_notice' ) );
			}

			// Maybe display missing integrity check file notice.
			if ( ! cw_get_option( 'cw_filename' ) ) {
				$filename = $this->get_integrity_check_filename();
				cw_update_option( 'cw_filename', sanitize_file_name( $filename ) );
			} else {
				$filename = cw_get_option( 'cw_filename' );
			}
			$path = trailingslashit( wp_upload_dir()['basedir'] ) . sanitize_file_name( $filename );
			if ( ! file_exists( $path ) && false === strpos( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'cryptowoo' ) ) {
				add_action( 'admin_notices', array( $this, 'cryptowoo_integrity_notice' ) );
			}

			// Maybe display cronjob setup info notice on options page.
			if ( strpos( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'cryptowoo' ) && ! get_option( 'cryptowoo_cronjob_notice' ) ) {
				add_action( 'admin_notices', array( $this, 'create_cronjob_notice' ) );
			}

			// Maybe display exchange rate error notice.
			if ( cw_get_option( 'display_rate_error_notice' ) ) {
				$rate_errors = get_transient( 'cryptowoo_rate_errors' );
				$error_count = (int) CW_Validate::check_if_unset( 'error_count', $rate_errors, '0' );
				if ( $error_count >= 1 ) {
					add_action( 'admin_notices', array( $this, 'cryptowoo_rate_notice' ) );
				}
			}

			// Maybe display license expiration notice.
			$license_expiration = get_option( 'cryptowoo_license_expiration' );
			if ( $license_expiration && false === get_option( 'cryptowoo_license_notice', false ) ) {
				$now                  = time();
				$days_before          = 4 * WEEK_IN_SECONDS;
				$expiration_timestamp = $license_expiration['access_expires'];
				if ( ( $expiration_timestamp - $now ) < $days_before ) {
					add_action( 'admin_notices', array( $this, 'cryptowoo_license_expiration_notice' ) );
				}
			}

			// Maybe display option reset notice on options page.
			if ( CWOO_VERSION === '0.12.1' && false === get_option( 'cryptowoo_option_reset_notice', false ) ) {
				add_action( 'admin_notices', array( $this, 'cryptowoo_option_reset_notice' ) );
			}

			/* // phpcs:ignore
			if('dismissed' !== get_option('cryptowoo_save_options_notice')) {
				add_action('admin_notices', array($this, 'cryptowoo_please_save_options_notice'));
			}*/

			if ( 'display' === get_option( 'cryptowoo_gap_limit_notice' ) ) {
				add_action( 'admin_notices', array( $this, 'cryptowoo_gap_limit_notice' ) );
			}

			// Version compatibility notices.
			CW_Versions::check_addon_version_compatibility();

		}
	}

	/**
	 * Render cronjob setup info depending on the set interval and if we are in a WP multisite network
	 *
	 * @param  bool $single Show single cron-task interval.
	 *
	 * @return string
	 */
	public static function get_cronjob_info( $single = true ) {
		/* translators: %1$s: html paragraph tag, %2$s: new line */
		$cronjob_info  = sprintf( __( '%1$sWe recommend you use an external cron via cPanel/crontab/Task Scheduler to trigger WP Cron to make sure that the orders are being processed and the rates are updated.%2$s', 'cryptowoo' ), '<p>', '<br>' );
		$cronjob_info .= '<div style="padding: 1em; background-color: #ccc;">';
		$url           = get_bloginfo( 'wpurl' );
		if ( ! defined( 'CWOO_MULTISITE' ) || ( defined( 'CWOO_MULTISITE' ) && ! CWOO_MULTISITE ) ) {
			$cronjob_info .= sprintf( '%s* * * * * cd %s; php -q wp-cron.php > /dev/null 2>&1 # Trigger WP Cron once per minute%s', '<pre>', ABSPATH, '</pre>' );
			if ( ! $single ) {
				$intervals = array( '15', '30', '45' );
				/* translators: %1$s: html pre tag, %2$s: end html pre tag */
				$cronjob_info .= sprintf( __( '%1$s# Additionally for intervals < 1 minute:%2$s', 'cryptowoo' ), '<pre>', '</pre>' );
				foreach ( $intervals as $interval ) {
					$cronjob_info .= sprintf( '<pre>* * * * * sleep %s; cd %s; php -q wp-cron.php > /dev/null 2>&1 # Sleep for %ssec before running </pre>', $interval, ABSPATH, $interval );
				}
			}
			$cronjob_info .= sprintf( '%s<pre>* * * * * wget -O - "%s/wp-cron.php?doing_wp_cron" > /dev/null 2>&1 # Use wget to trigger WP Cron once per minute</pre>', __( '# Alternative command: ' ), $url );
		} else {
			$cronjob_info .= sprintf( '%s* * * * * wget -q -O - "https://%s/wp-cron-multisite.php" > /dev/null 2>&1 # Trigger WP Cron once per minute%s', '<pre>', DOMAIN_CURRENT_SITE, '</pre>' );
			if ( ! $single ) {
				$intervals = array( '15', '30', '45' );
				/* translators: %1$s: html pre tag, %2$s: end html pre tag */
				$cronjob_info .= sprintf( __( '%1$s# Additionally for intervals < 1 minute:%2$s', 'cryptowoo' ), '<pre>', '</pre>' );
				foreach ( $intervals as $interval ) {
					$cronjob_info .= sprintf( '<pre>* * * * * sleep %s; wget -q -O - "https://%s/wp-cron-multisite.php" > /dev/null 2>&1 # Sleep for %ssec before running </pre>', $interval, DOMAIN_CURRENT_SITE, $interval );
				}
			}
			/* translators: %1$s: html code tag, %2$s: end html code tag, %3$s: current website */
			$cronjob_info .= sprintf( __( '# Make sure you create the file %1$swp-multisite-cron.php%2$s in %3$s as described at %4$s', 'cryptowoo' ), '<code>', '</code>', DOMAIN_CURRENT_SITE, 'https://www.cryptowoo.com/how-to-setup-cron-jobs-for-multisite-wordpress/' );
		}
		$cronjob_info .= $single ? '</div></p>' : __( '</div></p><p>Explanation:<br>The example above consists of <strong>4 crontasks that are each executed every minute</strong>.<br>The 2nd, 3rd, and 4th cronjob will sleep for 15, 30, and 45 seconds before they trigger WP cron.<br>This results in a <strong>15 second interval</strong> between each WP cron execution <strong>instead of the smallest possible interval of one minute</strong> when using only one crontask.</p>', 'cryptowoo' );
		/* translators: %1$s: CryptoWoo Help Desk url, %2$s: html a tag */
		$cronjob_info .= sprintf( __( '<p>The first crontask is enough if you set the cron interval to 60 seconds or higher. Refer to %1$sthe tutorial%2$s on our Help Desk for further information.</p><p><i class="fa fa-exclamation-triangle"></i> After setting up the crontask(s) add <code>define(\'DISABLE_WP_CRON\', true);</code> to your <code>/wp-config.php</code>.</p>', 'cryptowoo' ), '<a href="https://www.cryptowoo.com/cron-issues/?utm_source=plugin&utm_medium=link&utm_campaign=settings-tooltip" title="Visit CryptoWoo Help Desk" target="_blank">', '</a>' );
		return wp_kses_post( $cronjob_info );
	}

	/**
	 * Cronjob setup info admin notice
	 */
	public function create_cronjob_notice() {
		CW_Admin_Notice::generate( CW_Admin_Notice::NOTICE_INFO )
			->add_message( self::get_cronjob_info() )
			->make_dismissible( 'cronjob' )
			->print_notice();
	}

	/**
	 * WooCommerce inactive notice
	 */
	public function cryptowoo_wc_inactive_notice() {
		$update_actions = array(
			'activate_plugin_wc' => '<a class="button" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . rawurlencode( 'woocommerce/woocommerce.php' ), 'activate-plugin_woocommerce/woocommerce.php' ) . '" title="' . esc_attr__( 'Activate WooCommerce Plugin', 'cryptowoo' ) . '" target="_parent">' . __( 'Activate WooCommerce', 'cryptowoo' ) . '</a>',
			'cw_settings'        => '<a class="button" href="' . self_admin_url( 'admin.php?page=cryptowoo' ) . '" title="' . esc_attr__( 'Go to CryptoWoo Checkout Settings' ) . '" target="_parent">' . __( 'Enable CryptoWoo Payment Gateway', 'cryptowoo' ) . '</a>',
			/*'plugins_page' => '<a href="' . self_admin_url('plugins.php') . '" title="' . esc_attr__('Go to plugins page') . '" target="_parent">' . __('Return to Plugins page') . '</a>'*/
		);
		?>
		<div class="error">
			<p><?php echo wp_kses_post( __( '<b>CryptoWoo payment gateway has been disabled!</b> It seems like WooCommerce has been deactivated or is not installed.<br>Go to the Plugins menu and make sure that the WooCommerce plugin is installed and activated. Then visit the CryptoWoo checkout settings to re-enable the CryptoWoo payment gateway.', 'cryptowoo' ) ); ?></p>
		<?php
		foreach ( $update_actions as $key => $value ) {
			echo wp_kses_post( $value . ' ' );
		}
		?>
		</div>
		<?php
	}

	/**
	 * CryptoWoo payment gateway disabled notice
	 */
	public function cryptowoo_cw_inactive_notice() {
		CW_Admin_Notice::generate( CW_Admin_Notice::NOTICE_ERROR )
			->add_message( esc_html__( 'CryptoWoo payment gateway has been disabled!', 'cryptowoo' ) )
			->add_message( esc_html__( 'Go to the CryptoWoo checkout settings to make sure the settings are correct and re-enable the CryptoWoo payment gateway.', 'cryptowoo' ) )
			->add_button_menu_link( esc_html__( 'Go to CryptoWoo Settings', 'cryptowoo' ), esc_html__( 'Go to CryptoWoo Settings', 'cryptowoo' ), 'cryptowoo' )
			->make_dismissible( 'cw_inactive' )
			->print_notice();
	}

	/**
	 * CryptoWoo payment gateway disabled notice
	 */
	public function cryptowoo_integrity_notice() {
		CW_Admin_Notice::generate( CW_Admin_Notice::NOTICE_ERROR )
			->add_message( __( '<b>CryptoWoo can\'t find the integrity check file!</b><br>Go to the CryptoWoo checkout settings and click the "Save Changes" button to re-create it.', 'cryptowoo' ) )
			->add_button_menu_link( esc_html__( 'Go to CryptoWoo Settings', 'cryptowoo' ), esc_html__( 'Go to CryptoWoo Settings', 'cryptowoo' ), 'cryptowoo' )
			->print_notice();
	}

	/**
	 * CryptoWoo exchange rate error notice
	 */
	public function cryptowoo_rate_notice() {
		$rate_errors = get_transient( 'cryptowoo_rate_errors' );

		$error_count   = (int) CW_Validate::check_if_unset( 'error_count', $rate_errors, 0 );
		$counter_start = (int) CW_Validate::check_if_unset( 'counter_start', $rate_errors, 0 );

		$counting_since_date = gmdate( 'l jS \of F Y h:i:s', $counter_start );

		$buttons  = '<a class="button" href="' . self_admin_url( 'admin.php?page=cryptowoo_database_maintenance' ) . '" title="' . esc_attr__( 'Go to Database Maintenance Page' ) . '" target="_parent">1. ' . __( 'Go to Database Maintenance Page' ) . '</a>';
		$buttons .= ' <a class="button" href="' . self_admin_url( 'admin.php?page=cryptowoo' ) . '" title="' . esc_attr__( 'Go to CryptoWoo Options' ) . '" target="_parent">2. ' . __( 'Go to CryptoWoo Options' ) . '</a>';
		?>
		<div class="error redux-messageredux-notice notice is-dismissible redux-notice">
			<?php /* translators: %1$s: error count, %2$s: gmdate since counting started, %3$s: buttons */ ?>
			<p><?php echo wp_kses_post( sprintf( __( '<b>CryptoWoo has detected %1$s exchange rate update errors since %2$s.</b><br>Go to the CryptoWoo Database Maintenance page, reset the error counter and try to update the rates manually.<br>Generally, these errors may occur from time to time and the fallback solution will catch them, but if the exchange rate update constantly fails, please select a different Preferred Exchange API on the options page.<p>%3$s</p>', 'cryptowoo' ), $error_count, $counting_since_date, $buttons ) ); ?></p>
		</div>
		<?php
	}

	/**
	 * WooCommerce not installed notice
	 */
	public function cryptowoo_wc_notinstalled_notice() {
		$update_actions = array(
			'activate_plugin_wc' => '<a class="button" href="' . wp_nonce_url( rawurlencode( 'update.php?action=install-plugin&plugin=woocommerce' ) ) . '" title="' . esc_attr__( 'Activate WooCommerce Plugin' ) . '" target="_parent">' . __( 'Install WooCommerce' ) . '</a>',
			'cw_settings'        => '<a class="button" href="' . self_admin_url( 'admin.php?page=cryptowoo' ) . '" title="' . esc_attr__( 'Go to CryptoWoo Checkout Settings' ) . '" target="_parent">' . __( 'Enable CryptoWoo Payment Gateway' ) . '</a>',
			/*'plugins_page' => '<a href="' . self_admin_url('plugins.php') . '" title="' . esc_attr__('Go to plugins page') . '" target="_parent">' . __('Return to Plugins page') . '</a>'*/
		);
		?>
		<div class="error">
			<p><?php echo wp_kses_post( __( '<b>CryptoWoo payment gateway has been disabled!</b> It seems like WooCommerce is not installed.<br>Click here to install and activate the WooCommerce plugin. Then visit the CryptoWoo checkout settings to re-enable the CryptoWoo payment gateway.', 'cryptowoo' ) ); ?></p>
		<?php
		foreach ( $update_actions as $key => $value ) {
			echo wp_kses_post( $value . ' ' );
		}
		?>
		</div>
		<?php
	}

	/**
	 * License expiration notice
	 */
	public function cryptowoo_license_expiration_notice() {
		$expiration_info = get_option( 'cryptowoo_license_expiration' );
		/* translators: %1$s: expiration gmdate, %2$s: discount code */
		$message = wp_kses_post( sprintf( __( '<h4>Your CryptoWoo License Key will expire on <strong>%1$s</strong></h4> Renew your license now for a discount!<br>Your discount code: <strong>%2$s</strong>', 'cryptowoo' ), gmdate( 'l jS \of F Y', $expiration_info['access_expires'] ), $expiration_info['order_key'] ) );

		CW_Admin_Notice::generate( CW_Admin_Notice::NOTICE_ERROR )
			->add_message( $message )
			->add_button_with_full_path_url( esc_html__( 'Renew CryptoWoo License Key', 'cryptowoo' ), esc_html__( 'Click to go to CryptoWoo website to renew the license key', 'cryptowoo' ), 'https://cryptowoo.com/' )
			->make_dismissible( 'license_expired' )
			->print_notice();
	}

	/**
	 * Display gap limit reached warning notice
	 */
	public function cryptowoo_gap_limit_notice() {
		$gap_limit_notice_coins = get_option( 'cryptowoo_gap_limit_notice_currencies' );
		$currencies             = implode( ', ', $gap_limit_notice_coins );
		/* translators: %1$s: html paragraph being tag, %2$s: currencies comma separated, %3$s: new line, %4$s: html paragraph end tag */
		$format  = esc_html__( '%1$sGap limit may have been reached in your wallet for cryptocurrencies: %2$s%3$sSend a small payment to your latest wallet address to see more transactions.%4$s', 'cryptowoo' );
		$message = wp_kses_post( sprintf( $format, '<p>', $currencies, '<br>', '</p>' ) );

		CW_Admin_Notice::generate( CW_Admin_Notice::NOTICE_INFO )
			->add_message( $message )
			->make_dismissible( 'gap_limit' )
			->print_notice();
	}

	/**
	 * "Please save options" notice
	 */
	public function cryptowoo_please_save_options_notice() {
		$message = printf( '<h4>%s</h4>', esc_html__( 'Thanks for updating CryptoWoo! Please verify that your settings are correct and click the "Save settings" button.', 'cryptowoo' ) );

		CW_Admin_Notice::generate( CW_Admin_Notice::NOTICE_INFO )
			->add_message( $message )
			->make_dismissible( 'save_options' )
			->print_notice();
	}

	/**
	 * PHP extensions missing notice
	 */
	public function cryptowoo_extension_notice() {

		$version = phpversion();
		$message = '';

		/* translators: %s: name of missing extension */
		$not_installed = __( '<b>%s extension</b> seems not to be installed.<br>', 'cryptowoo' );

		if ( version_compare( PHP_VERSION, '5.6.0', '<' ) ) {
			/* translators: %s: php version */
			$message .= sprintf( __( 'Your current <b>PHP Version is %s</b>.<br>', 'cryptowoo' ), $version );
		}
		if ( ! extension_loaded( 'gmp' ) ) {
			$message .= sprintf( $not_installed, 'GMP' );
		}

		if ( ! extension_loaded( 'curl' ) ) {
			$message .= sprintf( $not_installed, 'cURL' );
		}

		if ( ! extension_loaded( 'bcmath' ) ) {
			$message .= sprintf( $not_installed, 'bcmath' );
		}

		if ( '' !== $message ) {
			?>
			<div class="error">
				<?php /* translators: %s: an already translated message about missing php extension or invalid php version */ ?>
				<p><?php echo wp_kses_post( sprintf( __( '%sYou need to have at least PHP version 5.6 with <a href="https://php.net/manual/en/gmp.installation.php" target="_blank">GMP</a>, and <a href="https://php.net/manual/en/curl.installation.php" target="_blank">cURL</a> extensions enabled to use CryptoWoo. <a href="https://www.cryptowoo.com/enable-required-php-extensions/?ref=cw_ext_error" target="_blank">More Info</a>', 'cryptowoo' ), $message ) ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Cronjob setup info admin notice
	 */
	public function cryptowoo_option_reset_notice() {
		/* translators: %1$s: html tag for url begin, %2$s: admin url to cryptowoo settings page, %3$s: html tag for url end, html tag for paragraph end */
		$cw_settings = sprintf( esc_html__( '%1$s%2$s%3$sCryptoWoo settings page%4$s', 'cryptowoo' ), '<a href="', admin_url( 'admin.php?page=cryptowoo' ), '">', '</a>' );
		/* translators: %1$s: html tag for strong text begin, %2$s: html tag for strong text end and new line, %3$s: html tag for code begin, %4$s: html tag for url end, %5$s: html tag for new line, %6$s: already translated settings page url */
		$message = sprintf( esc_html__( '%1$sImportant notice:%2$s During this CryptoWoo update we had to set the %3$s"Payment Processing" > "Order Expiration Time"%4$s back to the default value.%5$sIf you customized this setting before the update, please go to the %6$s and set it again.', 'cryptowoo' ), '<strong>', '</strong><br>', '<code>', '</code>', '<br>', $cw_settings );

		CW_Admin_Notice::generate( CW_Admin_Notice::NOTICE_INFO )
			->add_message( $message )
			->make_dismissible( 'reset' )
			->print_notice();
	}

	/**
	 * Misconfiguration notice if no processing API is selected
	 */
	public function cryptowoo_misconfig_notice() {
		if ( cw_get_option( 'enabled' ) ) {
			$coins = array( 'BTC', 'BCH', 'LTC', 'DOGE', 'BLK' );
			foreach ( $coins as $coin ) {
				$enabled[ $coin ] = $this->currency_is_misconfigured( 'BTC' );
			}
			$enabled   = apply_filters( 'cw_misconfig_notice', $enabled, cw_get_options() );
			$message   = '';
			$nicenames = cw_get_cryptocurrencies();
			foreach ( $enabled as $currency => $status ) {
				if ( $status ) {
					/* translators: %1$s: html tag for paragraph and strong begin, %2$s: currency name, %3$s: html tag for paragraph and strong end */
					$message .= sprintf( esc_html__( '%1$s%2$s payment processing configuration error%3$s', 'cryptowoo' ), '<p><strong>', $nicenames[ $currency ], '</strong></p>' );
				}
			}
			if ( ! empty( $message ) ) {
				$message .= sprintf( '<p>%s</p>', esc_html__( 'Go to "Payment Processing" > "Blockchain Access" and select a "Processing API" provider. Otherwise CryptoWoo can not look up transactions in the block chain.', 'cryptowoo' ) );
				/* translators: %1$s: html tag for paragraph and strong begin, %2$s: currency name, %3$s: html tag for paragraph and strong end */
				echo wp_kses_post( sprintf( '<div class="error redux-messageredux-notice notice is-dismissible redux-notice">%s</div>', $message ) );
			}
		}
	}

	/**
	 * Check if a currency is missing a processing API even though a wallet is set up
	 *
	 * @param string $currency Currency name..
	 *
	 * @return bool
	 */
	public function currency_is_misconfigured( $currency ) {
		$is_misconfigured = false;
		if ( $this->currency_has_wallet_settings( $currency ) ) {
			$is_misconfigured = ! $this->currency_has_processing_api( $currency );
		}
		return $is_misconfigured;
	}

	/**
	 * Check if a currency has a processing API selected
	 *
	 * @param string $currency Currency name..
	 *
	 * @return bool
	 */
	public function currency_has_processing_api( $currency ) {
		$selected = cw_get_option( 'processing_api_' . strtolower( $currency ) );
		return $selected && 'disabled' !== $selected;
	}

	/**
	 * Check if a currency has wallet_settings selected
	 *
	 * @param string $currency Currency name.
	 *
	 * @return bool
	 */
	public function currency_has_wallet_settings( $currency ) {
		$enabled_currencies = cw_get_enabled_currencies();
		return array_key_exists( $currency, $enabled_currencies );
	}


	/** Do CryptoWoo integrity check
	 *
	 * @return bool
	 */
	public function do_cryptowoo_integrity_check() {

		$validate       = new CW_Validate();
		$api_keys_valid = $validate->cryptowoo_api_check();

		$result = ! isset( $api_keys_valid['valid'] ) || $api_keys_valid['valid'];
		if ( ! $result ) {

			// Disable gateway.
			cw_update_options(
				array(
					'enabled'                            => false,
					'btc_enabled'                        => '0',
					'doge_enabled'                       => '0',
					'ltc_enabled'                        => '0',
					'cryptowoo_price_rewrite'            => 'disable',
					'cryptowoo_currency_switch_position' => 'disabled',
					'cryptowoo_currency_table_on_single_products' => false,
				)
			);

			// Disable cron.
			wp_clear_scheduled_hook( 'cryptowoo_cron_action' );

			$print_result = var_export( $api_keys_valid, true );
			$to           = get_option( 'admin_email' );
			$blogname     = get_bloginfo( 'name', 'raw' );
			$subject      = sprintf( '%s: %s', $blogname, __( 'Unauthorized changes detected', 'cryptowoo' ) );

			/* translators: %1$s: new line, %2$s: gmdate with format l jS \of F Y h:i:s, %3$s: store name */
			$message = sprintf( __( 'Hello Admin,%1$s on %2$s, CryptoWoo has detected an unauthorized change of your settings at %3$s.%1$s', 'cryptowoo' ), PHP_EOL, gmdate( 'l jS \of F Y h:i:s' ), $blogname );

			$message .= sprintf(
				/* translators: %1$s: new line, %2$s: validation result */
				__( 'The CryptoWoo payment gateway has been disabled. From now on, open WooCommerce orders using CryptoWoo will not be completed and the payment method will not be available for new orders.%1$s%1$sValidation result:%2$s%1$s', 'cryptowoo' ),
				PHP_EOL,
				$print_result
			);
			$message .= __( 'NOTE: This may be a false-positive if you recently migrated or reinstalled your site. In this case just save the CryptoWoo settings page to update the option checksum.', 'cryptowoo' );
			$message .= __( 'If you need help, please submit a ticket at https://www.cryptowoo.com/contact', 'cryptowoo' );

				$headers = array( "From: CryptoWoo Plugin <{$to}>" );

			wp_mail( $to, $subject, $message, $headers );

			// Write to log.
			self::cryptowoo_log_data( 0, __FUNCTION__, "\r\n==========-cryptowoo_integrity_check-==========\r\nBEGIN " . gmdate( 'Y-m-d H:i:s' ) . "\r\n" . $print_result . "\r\nEND\r\n====================", 'alert' );
		}
		return $result;
	}

	/**
	 * Retrieves the best guess of the client's actual IP address.
	 * Takes into account numerous HTTP proxy headers due to variations
	 * in how different ISPs handle IP addresses in headers between hops.
	 * Stolen from https://gist.github.com/cballou/2201933#file-get-ip-address-optimized-php
	 */
	public function get_ip() {
		$ip_keys = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' );
		foreach ( $ip_keys as $key ) {
			if ( array_key_exists( $key, $_SERVER ) === true ) {
				foreach ( explode( ',', sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ) ) as $ip ) {
					// trim for safety measures.
					$ip = trim( $ip );
					// attempt to validate IP.
					if ( $this->validate_ip( $ip ) ) {
						return $ip;
					}
				}
			}
		}
		return CW_Validate::check_if_unset( 'REMOTE_ADDR', $_SERVER );

	}

	/**
	 * Prepare the file name of the integrity check file
	 * Concatenate NONCE_SALT with the current blog id to make sure we have separate files for each blog in the network
	 *
	 * @return string
	 */
	public function get_integrity_check_filename() {
		$blog = get_current_blog_id();
		return sha1( NONCE_SALT . $blog );
	}

	/**
	 * Ensures an ip address is both a valid IP and does not fall within a private network range.
	 * Taken from https://gist.github.com/cballou/2201933#file-get-ip-address-optimized-php
	 *
	 * @param string $ip IP address.
	 *
	 * @return bool
	 */
	public function validate_ip( $ip ) {
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
			return false;
		}
		return true;
	}

	/**
	 * Display CryptoWoo Database Maintenance page
	 */
	public function database_maintenance() {

		// Only get balances if WooCommerce is active.
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			// BEGIN Database maintenance.

			// include database maintenance processing & form.

			include 'database-maintenance.php';
		} else {
			add_action( 'admin_notices', array( $this, 'cryptowoo_wc_inactive_notice' ) );
		}
		// END Database maintenance.
	}

	/**
	 * Delete data from CryptoWoo payment table
	 */
	public function delete_payment_data() {
		global $wpdb;

		$payments = $wpdb->get_results( 'DELETE FROM `' . $wpdb->prefix . 'cryptowoo_payments_temp` WHERE 1', ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery

		$message = (bool) $payments ? esc_html__( 'Payment data deleted', 'cryptowoo' ) : esc_html__( 'Error deleting payment data', 'cryptowoo' );

		return $message;

	}

	/**
	 * Create CryptoWoo table
	 *
	 * @param string $blog_id Blog ID.
	 */
	public function create_plugin_table( $blog_id ) {

		// Create payments table.
		$sql = $this->get_table_definition_sql( 'cryptowoo_payments_temp' );

		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		// Create exchange rate table.
		$sql = $this->get_table_definition_sql( 'cryptowoo_exchange_rates' );

		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

	}

	/**
	 * Recreate table for exchange rates
	 *
	 * @param bool $drop Drop table if true.
	 *
	 * @return array|bool
	 */
	public function recreate_table_exchange_rates( $drop = false ) {
		return $this->recreate_table( 'cryptowoo_exchange_rates', $drop );
	}

	/**
	 * Recreate table for exchange rates
	 *
	 * @param bool $drop  Drop table if true.
	 *
	 * @return array|bool
	 */
	public function recreate_table_payments( $drop = false ) {
		return $this->recreate_table( 'cryptowoo_payments_temp', $drop );
	}

	/**
	 * Get table definition
	 *
	 * @param string $table Table name.
	 * @param bool   $drop  Drop table if true.
	 *
	 * @return array|bool
	 */
	private function recreate_table( $table = 'cryptowoo_exchange_rates', $drop = false ) {
		global $wpdb;

		if ( $drop ) {
			if ( 'cryptowoo_exchange_rates' === $table ) {
				$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cryptowoo_exchange_rates" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
			} elseif ( 'cryptowoo_payments_temp' === $table ) {
				$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cryptowoo_payments_temp" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
			}
		}

		$sql = $this->get_table_definition_sql( $table );

		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$dbdelta = dbDelta( $sql );

		return empty( $dbdelta ) ? false : $dbdelta;
	}

	/** Get table definition
	 *
	 * @param string $table_name Table name.
	 *
	 * @return string
	 */
	public function get_table_definition_sql( $table_name ) {
		global $wpdb;
		$charset_collate = '';
		$sql             = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}

		if ( 'cryptowoo_payments_temp' === $table_name ) {
			// Create payments table.
			$sql = 'CREATE TABLE ' . $wpdb->prefix . "cryptowoo_payments_temp (
				id int(11) NOT NULL AUTO_INCREMENT,
				amount decimal(17,8) NOT NULL,
				crypto_amount bigint(20) NOT NULL,
				address varchar(106) NOT NULL DEFAULT '',
				payment_currency varchar(20) NOT NULL DEFAULT '',
				customer_reference varchar(50) NOT NULL DEFAULT '',
				order_id int(11) NOT NULL,
				received_confirmed bigint(20) NOT NULL DEFAULT '0',
				received_unconfirmed bigint(20) NOT NULL DEFAULT '0',
				created_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				last_update timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				timeout_value int(11) NOT NULL,
				timeout tinyint(1) NOT NULL DEFAULT '0',
				txids text NOT NULL DEFAULT '',
				paid tinyint(1) NOT NULL DEFAULT '0',
				is_archived tinyint(1) NOT NULL DEFAULT '0',
				PRIMARY KEY  (id),
				KEY address (address),
				KEY order_id (order_id),
				KEY ticks (timeout,paid)
			) $charset_collate;";
		} else {
			// Create exchange rate table.
			$sql = 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'cryptowoo_exchange_rates;
				CREATE TABLE ' . $wpdb->prefix . "cryptowoo_exchange_rates (
					coin_type varchar(8) NOT NULL,
					exchange_rate decimal(17,8) NOT NULL,
					exchange varchar(20) NOT NULL,
					status varchar(40) NOT NULL,
					method varchar(40) NOT NULL,
					last_update timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
					api_timestamp timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY  (coin_type),
					KEY last_update (last_update)
				) $charset_collate;";
		}

		return $sql;

	}

	/**
	 * Force update exchange rates for all enabled currencies
	 *
	 * @return array
	 */
	public function update_exchange_data() {
		// Update exchange rates for natively supported currencies.
		$native_currencies  = array(
			'BTC'      => 'btc',
			'BTCTEST'  => 'btctest',
			'BCH'      => 'bch',
			'DOGE'     => 'doge',
			'DOGETEST' => 'dogetest',
			'LTC'      => 'ltc',
			'BLK'      => 'blk',
		);
		$enabled_currencies = cw_get_enabled_currencies( false, false );
		$results            = array();
		foreach ( $native_currencies as $currency => $coin_identifier ) {
			if ( 'BTC' === $currency || array_key_exists( $currency, $enabled_currencies ) ) {
				$results[ $coin_identifier ] = CW_ExchangeRates::processing()->update_coin_rates( $currency, false, true );
			}
		}

		return apply_filters( 'cw_force_update_exchange_rates', $results );
	}

	/**
	 * Update exchange rates callback
	 */
	public function cw_exchange_rates_callback() {
		$admin_main = new CW_AdminMain();
		echo '<div class="update-nag">';
		echo esc_html( 'Request time: ' . gmdate( 'Y-m-d H:i:s' ) . '<pre>' );
		$rate_updates = $admin_main->update_exchange_data();
		print_r( $rate_updates ); // phpcs:disable WordPress.PHP.DevelopmentFunctions
		echo '</pre>';
		echo '<a class="button" href="javascript: window.location.reload(true)">Reload Page</a></div>';
		wp_die(); // this is required to terminate immediately and return a proper response.
	}

	/**
	 * Reset exchange rate error counter callback
	 */
	public function cw_reset_error_counter_callback() {
		// Reset transient.
		delete_transient( 'cryptowoo_rate_errors' );
		echo '<div id="message" class="success fade" style="color: green;"><i class="fa fa-check"></i>' . esc_html__( 'Rate error counter has been reset.', 'cryptowoo' ) . '</div>';
		wp_die(); // this is required to terminate immediately and return a proper response.
	}

	/**
	 * Reset exchange rate table callback
	 */
	public function cw_reset_exchange_rate_table_callback() {
		$admin_main = new CW_AdminMain();
		echo '<div class="update-nag">';
		echo wp_kses_post( 'Request time: ' . gmdate( 'Y-m-d H:i:s' ) . '<pre>' );
		$truncate_success = $admin_main->recreate_table_exchange_rates( true );
		if ( $truncate_success ) {
			$message = esc_html__( 'Exchange rate table truncated successfully!', 'cryptowoo' );
		} else {
			$message = esc_html__( 'There was a problem preparing the plugin table. Please try again.', 'cryptowoo' );
		}
		echo esc_html( $message );
		echo '</pre><a class="button" href="javascript: window.location.reload(true)">Reload Page</a></div>';
		wp_die(); // this is required to terminate immediately and return a proper response.
	}

	/**
	 * Recreate payments table callback
	 */
	public function cw_reset_payments_table_callback() {
		$admin_main = new CW_AdminMain();
		echo '<div class="update-nag">';
		echo esc_html( 'Request time: ' . gmdate( 'Y-m-d H:i:s' ) );
		$delete_success = $admin_main->recreate_table_payments( true );
		if ( $delete_success ) {
			$message = esc_html__( 'Payments table data has been reset!', 'cryptowoo' );
		} else {
			$message = esc_html__( 'There was a problem resetting the payments table. Please try again.', 'cryptowoo' );
		}
		echo esc_html( $message );
		echo '</div>';
		wp_die(); // this is required to terminate immediately and return a proper response.
	}

	/**
	 * Update tx details callback
	 */
	public function cw_update_tx_details_callback() {
		$data = CW_OrderProcessing::block_explorer()->update_tx_details();
		self::cryptowoo_log_data( time(), 'manual tx update', $data, 'debug' );
		echo wp_json_encode( $data );
		wp_die(); // this is required to terminate immediately and return a proper response.
	}

	/**
	 * Admin process open orders callback
	 */
	public function cw_process_open_orders_callback() {
		$data = CW_Block_Explorer_Processing::process_open_orders();
		echo wp_json_encode( $data );
		wp_die();
	}

	/**
	 * Admin delete address list callback
	 */
	public function cw_delete_address_list_callback() {
		check_ajax_referer( 'cw_delete_address_list', 'nonce' );
		if ( current_user_can( 'manage_options' ) && isset( $_POST['currency'] ) ) {
			$currency       = strtoupper( sanitize_text_field( wp_unslash( $_POST['currency'] ) ) );
			$delete_success = CW_AddressList::delete_list( $currency );
			if ( $delete_success ) {
				$result = sprintf( '%s address list deleted', $currency );
			} else {
				$result = sprintf( 'Error deleting %s address list', $currency );
			}
			echo wp_json_encode( $result );
		}
		wp_die();
	}

	/**
	 * Frontend force process open orders callback
	 */
	public function cw_front_process_open_orders_callback() {
		if ( isset( $_REQUEST['wp_nonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['wp_nonce'] ), 'cw_poll_callback' ) ) {
			$data = CW_Block_Explorer_Processing::process_open_orders();
			self::cryptowoo_log_data( time(), 'cw_front_process_open_orders_callback', $data, 'debug' );
		}
		wp_die();
	}

	/**
	 * Maybe redirect the user to the options page and trigger notice that instructs him to click the  "Save settings" button
	 */
	public function cw_maybe_redirect_to_options_page() {
		$version = get_option( 'cryptowoo_version' );
		if ( $version && version_compare( $version, '0.16.1', '<' ) ) {
			add_option( 'cryptowoo_save_options_notice', 'display' );
			add_option( 'cryptowoo_plugin_do_activation_redirect', true );
			update_option( 'cryptowoo_version', CWOO_VERSION );
		}
	}

	/**
	 * Check previously installed plugin version and maybe run database updates.
	 * Hooked into admin_init to catch updates when the files are replaced manually instead of using the WP update routine.
	 */
	public function cw_handle_updates() {

		$version = get_option( 'cryptowoo_version' );

		// If we don't have a previous version number or the version is older than the current one.
		if ( ! $version || ( $version && version_compare( $version, CWOO_VERSION, '<' ) ) ) {

			// Update block explorer processing api and preferred explorer for links after update to 0.27.0.
			// Schedule change serialized txids to json encoded in order meta to next cron job after update to 0.27.0.
			if ( $version && version_compare( $version, '0.27.0', '<' ) ) {
				$new_values_map = array(
					'chain_so'       => 'sochain',
					'esplora_custom' => 'custom_esplora',
					'insight'        => 'litecore',
					'cashexplorer'   => 'bitcoincom',
				);
				$cw_options     = cw_get_options();
				foreach ( $cw_options as $cw_option_id => $cw_option_value ) {
					if ( false === strpos( $cw_option_id, 'processing_api_' ) && false === strpos( $cw_option_id, 'preferred_block_explorer_' ) ) {
						continue;
					}
					if ( in_array( $cw_option_value, array_keys( $new_values_map ), true ) ) {
						$new_options[ $cw_option_id ] = $new_values_map[ $cw_option_value ];
					}
				}

				if ( ! empty( $new_options ) ) {
					cw_update_options( $new_options );
				}

				// Schedule change serialized txids to json encoded in order meta to next cron job.
				wp_schedule_single_event( time(), 'cryptowoo_cron_update_order_meta_txids' );
			}

			if ( $version && version_compare( $version, '0.23.0', '<' ) ) {
				if ( $this->recreate_table_payments() ) {
					$result = 'Updated table cryptowoo_payments_temp to ' . CWOO_VERSION;

				} else {
					$result = 'Error updating table cryptowoo_payments_temp to ' . CWOO_VERSION;
				}
				self::cryptowoo_log_data( 0, __FUNCTION__, $result, 'debug' );
			}

			// Maybe force refresh exchange rates after update to 0.25.23.
			if ( $version && version_compare( $version, '0.25.23', '<' ) ) {

				// Drop table and recreate.
				if ( $this->recreate_table_exchange_rates( true ) ) {
					$result = 'Updated table cryptowoo_exchange_rates to ' . CWOO_VERSION;

					// Update exchange rates.
					CW_ExchangeRates::processing()->get_all_exchange_rates( true );

				} else {
					$result = 'Error updating table cryptowoo_exchange_rates to ' . CWOO_VERSION;
				}
				self::cryptowoo_log_data( 0, __FUNCTION__, $result, 'debug' );
			}

			// Maybe force refresh payments_temp table after update to 0.24.0 (remove redundant columns).
			if ( $version && version_compare( $version, '0.24.0', '<' ) ) {
				global $wpdb;
				$sql = sprintf( 'ALTER TABLE %s DROP COLUMN email, DROP COLUMN invoice_number', $wpdb->prefix . 'cryptowoo_payments_temp' );
				$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared

				// Change all "blockio" option values to "block_io" (should have been done for 0.23.6 but we missed the chance).
				$updated_options = array();
				foreach ( cw_get_options() as $opt_key => $opt_value ) {
					if ( 'blockio' === $opt_value ) {
						$updated_options[ $opt_key ] = 'block_io';
					} else {
						$updated_options[ $opt_key ] = $opt_value;
					}
				}
				cw_update_options( $updated_options );
			}
			// Update to 0.24.5 (change logging settings).
			if ( $version && version_compare( $version, '0.24.5', '<' ) ) {

				// Change log verbosity settings from per-function to severity level.
				$logging = cw_get_option( 'logging' );

				if ( is_array( $logging ) ) {
					if ( in_array( '1', $logging, true ) ) {
						// If logging was enabled before, set severity to "debug".
						cw_update_options(
							array(
								'enable_debug_log' => '1',
								'logging'          => 'debug',
							)
						);

					} else {
						// Else set severity to "error" but disable logging.
						cw_update_options(
							array(
								'enable_debug_log' => '0',
								'logging'          => 'error',
							)
						);
					}
				}

				// Upgrade to new filenames for integrity check file.
				$old_filename = CW_Validate::check_if_unset( 'cw_filename' );
				$cryptowoo    = new WC_CryptoWoo();
				$cryptowoo->cryptowoo_hash_keys();

				// Delete the old file if it exists.
				$file_path = trailingslashit( wp_upload_dir()['basedir'] ) . sanitize_file_name( $old_filename );
				if ( $old_filename && file_exists( $file_path ) ) {
					unlink( $file_path );
				}
			}

			// Update version number in database.
			update_option( 'cryptowoo_version', CWOO_VERSION );
		}
	}

	/**
	 * Update serialized txid order meta to json encoded
	 */
	public function do_cryptowoo_cron_update_order_meta_txids() {
		$args   = array(
			'payment_method' => CW_PAYMENT_METHOD_ID,
		);
		$orders = wc_get_orders( $args );
		foreach ( $orders as $order ) {
			$txids_serialized = $order->get_meta( 'txids' );
			if ( is_serialized( $txids_serialized ) ) {
				$txids = unserialize( $txids_serialized ); // phpcs:ignore
				if ( $txids ) {
					$order->update_meta_data( 'txids', wp_json_encode( $txids ) );
					$order->save();
				}
			}
		}
	}
}

