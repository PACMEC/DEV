<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * CryptoWoo order processing tools
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage Processing
 * @author     CryptoWoo AS
 */
class CW_Order_Processing_Tools extends CW_Singleton {


	/**
	 *
	 * Log order status changed action to CryptoWoo log file.
	 *
	 * @param string $function_name  Name of the function that is logging the message.
	 * @param string $message        Message to log.
	 * @param bool   $db_was_updated True if CryptoWoo DB was updated, default is true.
	 */
	public function log_order_status_changed( $function_name, $message, $db_was_updated = true ) {
		CW_AdminMain::cryptowoo_log_data(
			0,
			$function_name,
			array(
				'message'              => $message,
				'db_had_to_be_updated' => $db_was_updated,
			),
			'debug'
		);
	}

	/**
	 *
	 * Empties the cart and optionally the persistent cart too.
	 *
	 * @param bool $clear_persistent_cart Should the persistant cart be cleared too. Defaults to true.
	 */
	public function empty_wc_cart( $clear_persistent_cart = true ) {
		WC()->cart->empty_cart( $clear_persistent_cart );
	}

	/**
	 *
	 * Redirects customer to the cart.
	 */
	public function redirect_to_cart() {
		$this->redirect_to( wc_get_cart_url() );
	}

	/**
	 *
	 * Redirects customer to the payment url.
	 *
	 * @param string $order_id Woocommerce Order ID.
	 */
	public function redirect_to_checkout_payment( $order_id ) {
		$this->redirect_to( wc_get_order( $order_id )->get_checkout_payment_url() );
	}

	/**
	 *
	 * Redirects customer to an URL.
	 *
	 * @param string $redirect_url URL to redirect the customer to.
	 */
	public function redirect_to( $redirect_url ) {
		// If this is the order-pay page we need to update the redirect URL in the page to trigger redirect.
		// Otherwise redirect using the default Woocommerce redirect function.
		if ( isset( $_POST['action'] ) && 'check_receipt' === $_POST['action'] ) {
			$order_key   = ! empty( $_REQUEST['order_key'] ) ? sanitize_key( $_REQUEST['order_key'] ) : '';
			$order_id    = wc_get_order_id_by_order_key( $order_key );
			$order_data  = CW_Database_CryptoWoo::get_payment_details_by_order_id( $order_id );
			$unconfirmed = $order_data->get_received_unconfirmed();
			$received    = $order_data->get_received_confirmed();
			echo wp_json_encode(
				array(
					'received'    => $received > 0 ? CW_Formatting::fbits( $received ) : '0.00',
					'unconfirmed' => $unconfirmed > 0 ? CW_Formatting::fbits( $unconfirmed ) : '0.00',
					'redirect'    => $redirect_url,
				)
			);
		} else {
			wp_safe_redirect( $redirect_url );
		}

		exit; // This is required to terminate immediately after redirect.
	}

	/**
	 * Check the current user role against allowed user roles
	 *
	 * @return bool
	 */
	public function check_force_complete_permissions() {
		$capabilities = apply_filters( 'cryptowoo_force_complete_user_roles', array( 'shop_manager', 'administrator' ) );
		$has_access   = false;

		// Loop through user roles.
		foreach ( $capabilities as $capability ) {
			if ( current_user_can( $capability ) ) {
				// Stop checking if the user has a role that allows access.
				$has_access = true;
				break;
			}
		}

		return $has_access;
	}

	/**
	 * Convert currency amounts from float to integer to use lowest non-divisible unit for calculations.
	 *
	 * There are 10^8 satoshis in a single bitcoin (100,000,000s = 1BTC), 10^8 base units per litecoin, and 10^8 koinus per dogecoin (100,000,000k = 1DOGE).
	 * Display amounts are calculated afn formatted in CW_Formatting::fbits($amount, $dec_places).
	 *
	 * @param  $value
	 * @return int
	 */
	static function cw_float_to_int( $value ) {
		return (int) round( $value * 1e8 );
	}

	/**
	 * Flatten array
	 *
	 * @param  array       $array
	 * @param  $nokeys bool
	 * @return array
	 */
	static function flatten_array( array $array, $nokeys = false ) {
		$return = array();
		if ( $nokeys ) {
			array_walk_recursive(
				$array,
				function ( $a ) use ( &$return ) {
					$return[] = $a;
				}
			);
		} else {
			array_walk_recursive(
				$array,
				function ( $a, $b ) use ( &$return ) {
					$return[ $b ] = $a;
				}
			);
		}
		return $return;
	}

	/**
	 *
	 * Checks if gap limit has been reached for a currency
	 * Add admin warning notice if gap limit was reached
	 * Removes admin warning notice if gap limit is restored
	 *
	 * @param string $currency  Currency code (eg BTC).
	 * @param int    $gap_limit Gap limit (number of addresses before wallet does not continue checking for payments).
	 */
	public function check_gap_limit_for_currency( $currency, $gap_limit = 20 ) {
		$index_last_paid = self::get_last_paid_mpk_index_int_for_currency( $currency );
		// We cannot check gap limit if last paid index could not be found.
		if ( false === $index_last_paid ) {
			return;
		}

		$index_current        = get_option( "cryptowoo_{$currency}_index" );
		$gap_limit_currencies = get_option( 'cryptowoo_gap_limit_notice_currencies' );

		// Dismiss notice when gap limit has not been reached.
		if ( $gap_limit >= $index_current - $index_last_paid ) {
			$key = array_search( $currency, $gap_limit_currencies, true );
			if ( false !== $key ) {
				unset( $gap_limit_currencies[ $key ] );
				update_option( 'cryptowoo_gap_limit_notice_currencies', $gap_limit_currencies );
			}

			if ( empty( $gap_limit_currencies ) ) {
				update_option( 'cryptowoo_gap_limit_notice', 'dismissed' );
			}

			return;
		}

		// Gap limit is reached, add the currency to the gap limit currencies array.
		$new_gap_limit_currencies[] = $currency;
		if ( ! empty( $gap_limit_currencies ) ) {
			$new_gap_limit_currencies = array_unique( array_merge( $gap_limit_currencies, $new_gap_limit_currencies ) );
		}

		// Enable display of gap limit warning notice and add currency to notice array.
		update_option( 'cryptowoo_gap_limit_notice', 'display' );
		update_option( 'cryptowoo_gap_limit_notice_currencies', $new_gap_limit_currencies );
	}

	/**
	 * Gets the last mpk index that received payment for a currency
	 *
	 * @param string $currency Payment currency.
	 *
	 * @return bool|mixed
	 */
	public function get_last_paid_mpk_index_int_for_currency( $currency ) {
		$last_mpk_index_string = $this->get_last_paid_mpk_index_string_for_currency( $currency );

		return (int) filter_var( $last_mpk_index_string, FILTER_SANITIZE_NUMBER_INT );
	}

	/**
	 * Gets the last mpk index that received payment for a currency
	 *
	 * @param string $currency Payment currency.
	 *
	 * @return bool|mixed
	 */
	public function get_last_paid_mpk_index_string_for_currency( $currency ) {
		$order = CW_Database_Woocommerce::get_last_paid_order( $currency );

		// Find the first paid order with a mpk key index and we got the last used index.
		// When no orders have mpk key index, we cannot check gap limit. In that case return false.
		return $order ? CW_Database_Woocommerce::instance( $order->get_id() )->get_mpk_key_index() : false;
	}

	/**
	 *
	 * Check whether we accept zero conf or not for the address and fiat amount of the order.
	 *
	 * @param int $order_id Order id to check.
	 *
	 * @return bool
	 */
	public function zero_conf_is_enabled( $order_id ) {
		$payment_details = CW_Database_CryptoWoo::get_payment_details_by_order_id( $order_id );
		$currency        = $payment_details->get_payment_currency();
		$fiat_amount     = $payment_details->get_fiat_amount();

		return 0 === CW_Block_Explorer_Processing::get_processing_config( $currency, $fiat_amount )['min_conf'];
	}

	/**
	 *
	 * Checks if the currency is a test currency (for test net).
	 *
	 * @param string $currency The coin.
	 *
	 * @return bool
	 */
	public function currency_is_test_coin( $currency ) {
		return 'TEST' === substr( $currency, -4 );
	}
}
