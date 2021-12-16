<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * CryptoPay order processing
 *
 * @category   CryptoPay
 * @package    OrderProcessing
 * @subpackage Processing
 * @author     CryptoPay AS
 */
class CW_Order_Processing extends CW_Singleton_Array {


	/**
	 *
	 * CW_Order_Processing_Database constructor.
	 *
	 * @param int|string $order_id Woocommerce order id.
	 */
	protected function __construct( int $order_id ) {
		parent::__construct( (int) $order_id );
	}

	/**
	 * Set WooCommerce order status, add order note, and update order meta
	 */
	public function complete_order() {
		$cw_db_woocommerce = CW_Database_Woocommerce::instance( $this->get_id() );

		// Complete CryptoPay order.
		CW_Database_CryptoWoo::update_order_status_completed( $this->get_id() );

		// Complete WooCommerce order.
		$this->update_order_meta_by_cryptowoo_table();
		$cw_db_woocommerce->register_payment_complete();

		// Maybe set final order status.
		if ( 'disable' !== cw_get_option( 'final_order_status' ) && ! $cw_db_woocommerce->get_is_completed() ) {
			$cw_db_woocommerce->update_status_completed( esc_html__( 'Payment Complete - ', 'cryptopay' ) );
		}
	}

	/**
	 *
	 * Decline checkout order for timeout. Adds customer error notice.
	 */
	public function decline_order_timeout() {
		$cw_db      = CW_Database_CryptoWoo::get_payment_details_by_order_id( $this->get_id() );
		$log_string = sprintf( "%s Order has expired: %s %s Status: %s \n", date( 'Y-m-d H:i:s' ), $cw_db->get_payment_currency(), $cw_db->get_address(), 'timeout' );
		CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $log_string, 'debug' );

		// Decline order and redirect customer.
		$this->add_customer_notice_timeout();
		CW_Order_Processing_Tools::instance()->redirect_to_cart()();
	}

	/**
	 *
	 * Decline checkout order for unexpected error. Adds customer notice and clears wc cart.
	 */
	public function decline_order_unexpected_error() {
		CW_Order_Processing_Tools::instance()->empty_wc_cart();

		$cw_db_woocommerce = CW_Database_Woocommerce::instance( $this->get_id() );

		if ( ! $cw_db_woocommerce->get_is_failed() ) {
			$cw_db_woocommerce->update_status_failed( 'CryptoPay Error', true );
		}

		// Decline order and redirect customer.
		$this->add_customer_notice_unexpected_error();
		CW_Order_Processing_Tools::instance()->redirect_to_cart()();
	}

	/**
	 *
	 * Decline checkout order for address error. Adds customer notice and logs error to admin.
	 */
	public function decline_order_address_error() {
		$cw_db      = CW_Database_CryptoWoo::get_payment_details_by_order_id( $this->get_id() );
		$log_string = sprintf( "%s  Address creation error: %s %s Status: %s \n", date( 'Y-m-d H:i:s' ), $cw_db->get_payment_currency(), $cw_db->get_address(), 'address is invalid' );
		CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $log_string, 'emergency' );

		// Decline order and redirect customer.
		$this->add_customer_notice_address_error();
		CW_Order_Processing_Tools::instance()->redirect_to_checkout_payment( $this->get_id() );
	}

	/**
	 *
	 * Decline checkout order for address error. Adds customer notice and logs error to admin.
	 */
	public function decline_order_amount_error() {
		$cw_db      = CW_Database_CryptoWoo::get_payment_details_by_order_id( $this->get_id() );
		$log_string = sprintf( "%s  Crypto amount calculation error: %s %s Status: %s \n", date( 'Y-m-d H:i:s' ), $cw_db->get_payment_currency(), $cw_db->get_address(), 'failed crypto amount' );
		CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $log_string, 'emergency' );

		// Decline order and redirect customer.
		$this->add_customer_notice_amount_error();
		CW_Order_Processing_Tools::instance()->redirect_to_checkout_payment( $this->get_id() );
	}

	/**
	 *
	 * Decline checkout order for quote refresh. Adds customer notice and logs debug to admin.
	 */
	public function decline_order_quote_refresh() {
		$cw_db      = CW_Database_CryptoWoo::get_payment_details_by_order_id( $this->get_id() );
		$log_string = sprintf( "%s Price quote for order expired: %s %s Status: %s \n", date( 'Y-m-d H:i:s' ), $cw_db->get_payment_currency(), $cw_db->get_address(), 'quote refresh' );
		CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $log_string, 'debug' );

		// Decline order and redirect customer.
		$this->add_customer_notice_quote_refresh();
		CW_Order_Processing_Tools::instance()->redirect_to_checkout_payment( $this->get_id() );
	}

	/**
	 *
	 * Maybe redirect to payment received when order is not fully completed.
	 */
	public function maybe_redirect_incomplete_order_to_payment_received() {
		$cw_order_data           = CW_Database_CryptoWoo::get_payment_details_by_order_id( $this->get_id() );
		$paid                    = $cw_order_data->get_is_paid();
		$received_unconfirmed    = $cw_order_data->get_received_unconfirmed();
		$redirect_on_unconfirmed = cw_get_option( 'cw_redirect_on_unconfirmed' );

		// Redirect if redirect on unconfirmed is enabled or if order is registered paid or has no unconfirmed fund.
		if ( ! $received_unconfirmed || $redirect_on_unconfirmed || $paid > 0 ) {
			$redirect_url = wc_get_order( $this->get_id() )->get_checkout_order_received_url();
			CW_Order_Processing_Tools::instance()->redirect_to( $redirect_url );
		}
	}

	/**
	 *
	 * Add order status notice to frontend for unexpected error.
	 */
	public function add_customer_notice_unexpected_error() {
		$this->add_customer_error_notice( 'An unexpected error occurred and we had to cancel your order. Please contact us if you need assistance.' );
	}

	/**
	 *
	 * Add order status notice to frontend for timeout.
	 */
	public function add_customer_notice_timeout() {
		$this->add_customer_error_notice( apply_filters( 'cryptowoo_timeout_txnreference', 'This order has expired. Please try again or contact us if you have already sent a payment.', wc_get_order( $this->get_id() ) ) );
	}

	/**
	 *
	 * Add order status notice to frontend for timeout insufficient payment.
	 */
	public function add_customer_notice_insufficient_payment() {
		$this->add_customer_error_notice( 'This order has expired. Please contact us as insufficient payment has been sent.' );
	}

	/**
	 *
	 * Add order status notice to frontend for address error.
	 */
	public function add_customer_notice_address_error() {
		$this->add_customer_error_notice( 'An error occurred while creating the payment address. Please try again.' );
	}

	/**
	 *
	 * Add order status notice to frontend for amount error.
	 */
	public function add_customer_notice_amount_error() {
		$this->add_customer_error_notice( 'An error occurred while calculating the digital currency amount. Please try again.' );
	}

	/**
	 *
	 * Add order status notice to frontend for amount error.
	 */
	public function add_customer_notice_quote_refresh() {
		$this->add_customer_error_notice( 'The price quote for your order has expired. Please click the "Pay for order"  button below to get a new quote.' );
	}

	/**
	 *
	 * Add order status notice to frontend for error.
	 *
	 * @param string $message Error notice to show the customer.
	 */
	public function add_customer_error_notice( $message ) {
		$this->add_customer_notice( $message, 'error' );
	}

	/**
	 *
	 * Add order status notice to frontend for info.
	 *
	 * @param string $message      Notice message to show the customer.
	 * @param string $message_type Notice type to show the customer (eg. 'error').
	 */
	public function add_customer_notice( $message, $message_type ) {
		// translators: First %s is replaced with an error message, second %s is replaced with woocommerce order id.
		wc_add_notice( esc_html( sprintf( __( 'ATTENTION: %s', 'cryptopay' ), sprintf( __( "$message Reference: %s" ), $this->get_id(), 'cryptopay' ) ) ), $message_type );
	}

	/**
	 *
	 * Check basic order validity
	 *
	 * @param CW_Payment_Details_Object $payment_details CryptoPay payment details object from DB.
	 */
	public function check_order_validity( $payment_details ) {
		if ( $payment_details->get_crypto_amount_due() <= 0 ) {
			$this->decline_order_amount_error();
		}
		if ( ! (bool) CW_Validate::check_if_unset( $payment_details->get_address(), false ) ) {
			$this->decline_order_address_error();
		}
		if ( 1 === (int) $payment_details->get_timeout() ) {
			$this->decline_order_timeout();
		}
		if ( 3 === (int) $payment_details->get_timeout() ) {
			$this->maybe_redirect_incomplete_order_to_payment_received();
		}
		if ( 4 === (int) $payment_details->get_timeout() ) {
			$this->decline_order_quote_refresh();
		}
		if ( empty( $payment_details->get_payment_currency() ) ) {
			$this->decline_order_unexpected_error();
		}
	}

	/**
	 *
	 * Updates the Woocommerce order meta fields with the values from CryptoPay Payments Table.
	 *
	 * @return false|int The Order ID if successful, false if unsuccessful.
	 */
	public function update_order_meta_by_cryptowoo_table() {
		$payment_details   = CW_Database_CryptoWoo::get_payment_details_by_order_id( $this->get_id() );
		$cw_db_woocommerce = CW_Database_Woocommerce::instance( $this->get_id() );

		// Add/Update payment metadata to woocommerce order.
		$cw_db_woocommerce
			->set_payment_address( $payment_details->get_address() )
			->set_received_confirmed( $payment_details->get_received_confirmed() )
			->set_received_unconfirmed( $payment_details->get_received_unconfirmed() )
			->set_crypto_amount( $payment_details->get_crypto_amount_due() )
			->set_payment_currency( $payment_details->get_payment_currency() )
			->set_tx_ids( $payment_details->get_tx_ids() );

		if ( $payment_details->get_received_confirmed() >= $payment_details->get_crypto_amount_due() && ! $payment_details->get_received_unconfirmed() && $payment_details->get_is_paid() ) {
			// Set tx confirmed meta and payment confirmed note.
			// translators: %1$s is replaced with payment currency and %2$s is replaced with crypto amount confirmed.
			$order_note = esc_html__( '%1$s Payment Complete - Amount Confirmed: %2$s', 'cryptopay' );
			$cw_db_woocommerce
				->set_tx_confirmed()
				->add_order_note( sprintf( $order_note, $payment_details->get_payment_currency(), CW_Formatting::fbits( $payment_details->get_received_confirmed() ) ), 3 === $payment_details->get_timeout() );

			// Action hook for payment confirmed.
			do_action( 'cryptowoo_confirmed', $payment_details );
		}

		// Save our updated order meta data.
		return $cw_db_woocommerce->update();
	}

	/**
	 *
	 * Fires when a WooCommerce order is set to "completed"
	 * Removes the payment address for the order from the queue
	 * Removes any gap limit warning notices for this currency
	 *
	 * @param int|string $order_id Woocommerce Order ID.
	 */
	public static function order_status_completed_action( $order_id ) {
		$is_crypto_order = CW_Database_CryptoWoo::get_payment_details_by_order_id( $order_id );
		if ( ! $is_crypto_order ) {
			return;
		}

		// Update the payment request for the order_id.
		$db_was_updated = CW_Database_CryptoWoo::update_order_status_completed( $order_id );

		// translators: %1$s is replaced with a date and %2$d is replaced by woocommerce order id.
		$admin_note = sprintf( esc_html__( '%1$s: Order #%2$d has been completed - removing address from queue.', 'cryptopay' ), date( 'Y-m-d H:i:s' ), $order_id );
		CW_Order_Processing_Tools::instance()->log_order_status_changed( __FUNCTION__, $admin_note, $db_was_updated );

		// Add admin note to order.
		CW_Database_Woocommerce::get_instance( $order_id )->add_order_note( $admin_note );

		// Remove any gap limit warning notices for this currency.
		if ( CW_Database_Woocommerce::instance( $order_id )->get_mpk_key_index() ) {
			CW_Order_Processing_Tools::instance()->check_gap_limit_for_currency( $is_crypto_order->get_payment_currency() );
		}
	}

	/**
	 *
	 * Fires when a WooCommerce order is set to "cancelled"
	 * Removes the payment address for the order from the queue
	 * Checks if gap limit is reached and adds admin notice
	 *
	 * @param int $order_id Woocommerce order id.
	 */
	public static function order_status_cancelled_action( $order_id ) {
		$is_crypto_order = CW_Database_CryptoWoo::get_payment_details_by_order_id( $order_id );
		if ( ! $is_crypto_order ) {
			return;
		}

		// Update the payment request for the order_id.
		$updated = CW_Database_CryptoWoo::update_order_status_cancelled_timeout( $order_id );

		// translators: %1$s is replaced with a date and %2$d is replaced by woocommerce order id.
		$note = sprintf( esc_html__( '%1$s: Order #%2$d has been cancelled - removing address from queue.', 'cryptopay' ), date( 'Y-m-d H:i:s' ), $order_id );
		CW_Order_Processing_Tools::instance()->log_order_status_changed( __FUNCTION__, $note, $updated );

		// Add note to order.
		$order = wc_get_order( $order_id );
		$order->add_order_note( $note, false );

		// Check if gap limit reached and update notice.
		if ( CW_Database_Woocommerce::instance( $order_id )->get_mpk_key_index() ) {
			CW_Order_Processing_Tools::instance()->check_gap_limit_for_currency( $is_crypto_order->get_payment_currency() );
		}
	}


	/**
	 *
	 * Force update payment status when the order action is called
	 * Updates the order status even if it is timed out
	 *
	 * @param WC_Order $order Woocommerce order object.
	 */
	public static function force_update_payment_status( $order ) {

		// Add an order note for information that payment status force-updating is in progress.
		$message = __( 'Force update of payment status has been started', 'cryptopay' );
		CW_Database_Woocommerce::instance( $order->get_id() )->add_order_note( $message );

		// Get payment details for the order.
		$unpaid_addresses = CW_Database_CryptoWoo::get_payment_details_by_order_id( $order->get_id() );
		$order_batch      = CW_Block_Explorer_Processing::batch_up_orders_per_currency( $unpaid_addresses );

		// Update payment status from blockchain.
		CW_OrderProcessing::block_explorer()->update_tx_details( $order_batch );

		// Update payment status in cryptopay and add order note.
		self::cw_force_process_unpaid_addresses( $order );
	}

	/**
	 * Force process unpaid addresses for an order in cryptopay
	 * Add order notes with the payment status after update
	 *
	 * @param WC_Order $order Woocommerce order object.
	 */
	public static function cw_force_process_unpaid_addresses( $order ) {
		// Get payment details for the order.
		$unpaid_orders = CW_Database_CryptoWoo::get_payment_details_by_order_id( $order->get_id() );

		// Process the payment status to force order update.
		CW_Block_Explorer_Processing::process_unpaid_orders( $unpaid_orders );

		// Get payment info.
		$cwdb_wc     = CW_Database_Woocommerce::instance( $order->get_id() );
		$format      = '<br>%s: %s<br>%s: %s<br>%s';
		$confirmed   = CW_Formatting::fbits( $cwdb_wc->get_received_confirmed(), true );
		$unconfirmed = CW_Formatting::fbits( $cwdb_wc->get_received_unconfirmed(), true );
		$txids       = ! empty( $cwdb_wc->get_tx_ids() ) ? $cwdb_wc->get_tx_ids() : '';
		$amounts     = sprintf( $format, __( 'Confirmed', 'cryptopay' ), $confirmed, __( 'Unconfirmed', 'cryptopay' ), $unconfirmed, print_r( $txids, true ) );

		// Add an order note for information that payment status was force updated.
		// translators: %s is replaced with crypto amount unconfirmed and confirmed and txids.
		$message = sprintf( __( 'Payment status for order was force updated.%s', 'cryptopay' ), $amounts );
		$order->add_order_note( $message );
	}

	/**
	 *
	 * Force accept payment for an order
	 * Updates the order status even if it is timed out
	 * Updates the order to whatever parameters is sent in
	 *
	 * @param WC_Order $order Woocommerce order object.
	 */
	public static function force_accept_payment_action( $order ) {
		self::force_accept_payment( $order, true );
	}

	/**
	 *
	 * Force accept payment for an order
	 * Updates the order status even if it is timed out
	 * Updates the order to whatever parameters is sent in
	 *
	 * @param WC_Order     $order                Woocommerce order object.
	 * @param int|true     $received_confirmed   Received confirmed in crypto satoshi value.
	 * @param int          $received_unconfirmed Pending confirmation in crypto satoshi value.
	 * @param array        $tx_ids               Array of blockchain tx ids and corresponding satoshi value.
	 * @param false|string $payment_address      Blockchain payment address.
	 * @param false|string $payment_currency     Payment currency code (eg BTC).
	 */
	public static function force_accept_payment( $order, $received_confirmed = 0, $received_unconfirmed = 0, $tx_ids = array(), $payment_address = false, $payment_currency = false ) {
		$cwdb_woocommerce = CW_Database_Woocommerce::instance( $order->get_id() );

		// Add an order note for information that payment status force-updating is in progress.
		$message = sprintf( __( 'Force accept payment has been started', 'cryptopay' ) );
		$cwdb_woocommerce->add_order_note( $message );

		// If total received confirmed is set to true, order is marked as paid in full.
		if ( true === $received_confirmed ) {
			$received_confirmed = $cwdb_woocommerce->get_crypto_amount();
		}

		// Convert tx ids array to correct format.
		foreach ( $tx_ids as $key => $value ) {
			// If value is a string and key numeric it means the value is the tx id.
			if ( is_numeric( $key ) && is_string( $value ) ) {
				if ( 1 === count( $tx_ids ) ) {
					// If we only have one txid the amount must be unconfirmed + confirmed.
					$tx_ids[ $value ] = $received_unconfirmed + $received_confirmed;
				} else {
					// Otherwise we do not know the amount so we set -1 to indicate issue setting amount.
					$tx_ids[ $value ] = - 1;
				}
				unset( $tx_ids[ $key ] );
			}
		}

		// Update payments table.
		CW_Database_CryptoWoo::instance( $order->get_id() )
		->set_address( $payment_address ?: $cwdb_woocommerce->get_payment_address() )
		->set_payment_currency( $payment_currency ?: $cwdb_woocommerce->get_payment_currency() )
		->set_received_confirmed( $received_confirmed + $received_unconfirmed )
		->set_received_unconfirmed( 0 )
		->set_tx_ids( $tx_ids )
		->set_last_update()
		->update();

		/* Update order meta. TODO: I think its unnecessary, if so remove. */
		$cwdb_woocommerce
			->set_payment_currency( $payment_currency )
			->set_payment_address( $payment_address )
			->set_crypto_amount( $received_confirmed + $received_unconfirmed )
			->set_received_confirmed( $received_confirmed )
			->set_received_unconfirmed( $received_unconfirmed )
			->set_tx_ids( $tx_ids )
			->update();

		// Update payment status in cryptopay and add order note.
		self::cw_force_process_unpaid_addresses( $order );

		// If the order for some reason is still not completed, we force accept it.
		if ( ! $cwdb_woocommerce->get_is_paid() ) {
			self::instance( $order->get_id() )->complete_order();
		}
	}

	/**
	 * Handle WooCommerce Force Update Payment order action on order overview
	 */
	public static function force_update_payment_status_handler() {
		$order_id = isset( $_REQUEST['order_id'] ) ? sanitize_key( $_REQUEST['order_id'] ) : false;
		$nonce    = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( $_REQUEST['_wpnonce'] ) : false;

		// Check order ID and nonce.
		if ( is_numeric( $order_id ) && $nonce && wp_verify_nonce( $nonce, 'cw-force-update-payment-status' ) ) {
			$order = wc_get_order( $order_id );
			if ( is_object( $order ) ) {
				// Force update payment status.
				self::force_update_payment_status( $order );
			}
		}

		// Redirect back to previous page.
		wp_safe_redirect( wp_get_referer() );
		wp_die();
	}

	/**
	 * Handle WooCommerce force accept payment action on order overview
	 */
	public static function cw_force_accept_payment_handler() {
		$order_id = isset( $_REQUEST['order_id'] ) ? sanitize_key( $_REQUEST['order_id'] ) : false;
		$nonce    = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( $_REQUEST['_wpnonce'] ) : false;

		// Check user role, order ID and nonce.
		if ( CW_Order_Processing_Tools::instance()->check_force_complete_permissions() && is_numeric( $order_id ) && $nonce && wp_verify_nonce( $nonce, 'cw-force-complete-order' ) ) {
			$order = wc_get_order( $order_id );
			if ( is_object( $order ) ) {
				// Force accept the payment.
				self::force_accept_payment( $order, true );
			}
		}

		// Redirect back to previous page.
		wp_safe_redirect( wp_get_referer() );
		wp_die();
	}
}
