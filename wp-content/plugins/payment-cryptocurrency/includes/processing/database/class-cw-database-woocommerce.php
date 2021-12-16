<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Order processing database handler for Woocommerce Database
 *
 * @category   CryptoPay
 * @package    OrderProcessing
 * @subpackage Database
 * @author     CryptoPay AS
 */
class CW_Database_Woocommerce extends CW_Database_Base {


	/**
	 *
	 * Get Woocommerce paid orders with CryptoPay as payment method.
	 *
	 * @param string $currency Payment currency code (eg BTC).
	 * @param int    $limit    Max quantity of orders to get.
	 *
	 * @return WC_Order[]
	 */
	public static function get_paid_orders( $currency, $limit = 10 ) {
		return wc_get_orders(
			array(
				'payment_method'   => CW_PAYMENT_METHOD_ID,
				'limit'            => $limit,
				'payment_currency' => $currency,
				'has_mpk_index'    => true,
				'status'           => wc_get_is_paid_statuses(),
				'orderby'          => 'date',
				'order'            => 'DESC',
			)
		);
	}

	/**
	 *
	 * Get Woocommerce last paid order with CryptoPay as payment method.
	 *
	 * @param string $currency Payment currency code (eg BTC).
	 *
	 * @return WC_Order
	 */
	public static function get_last_paid_order( $currency ) {
		$paid_orders = self::get_paid_orders( $currency, 1 );

		return reset( $paid_orders );
	}

	/**
	 *
	 * Returns if an order has been paid for based on the order status.
	 *
	 * @return bool
	 */
	public function get_is_paid() {
		return $this->get_order_object()->is_paid();
	}

	/**
	 *
	 * Checks if the order status is completed.
	 *
	 * @return bool
	 */
	public function get_is_completed() {
		return $this->get_order_object()->has_status( cw_get_option( 'final_order_status' ) );
	}

	/**
	 *
	 * Checks if the order status is failed.
	 *
	 * @return bool
	 */
	public function get_is_failed() {
		return $this->get_order_object()->has_status( 'failed' );
	}

	/**
	 *
	 * Get crypto amount in satoshi value
	 *
	 * @return int
	 */
	public function get_crypto_amount() {
		return (int) $this->get_order_object()->get_meta( 'crypto_amount' );
	}

	/**
	 *
	 * Get crypto amount difference
	 *
	 * @return int
	 */
	public function get_amount_difference() {
		return (int) $this->get_order_object()->get_meta( 'amount_difference' );
	}

	/**
	 *
	 * Get crypto amount paid in percentage
	 *
	 * @return int
	 */
	public function get_percentage_paid() {
		return (int) $this->get_order_object()->get_meta( 'percentage_paid' );
	}

	/**
	 *
	 * Get payment currency wc order meta
	 *
	 * @return string
	 */
	public function get_payment_currency() {
		return $this->get_order_object()->get_meta( 'payment_currency' );
	}

	/**
	 *
	 * Get payment address wc order meta
	 *
	 * @return string
	 */
	public function get_payment_address() {
		return $this->get_order_object()->get_meta( 'payment_address' );
	}

	/**
	 *
	 * Get tx confirmed wc order meta
	 *
	 * @return string
	 */
	public function get_tx_confirmed() {
		return $this->get_order_object()->get_meta( 'tx_confirmed' );
	}

	/**
	 *
	 * Get is lightning wc order meta
	 *
	 * @return bool
	 */
	public function get_is_lightning() {
		return (bool) $this->get_order_object()->get_meta( 'is_lightning' );
	}

	/**
	 *
	 * Get crypto amount received confirmed wc order meta
	 *
	 * @return int
	 */
	public function get_received_confirmed() {
		return (int) $this->get_order_object()->get_meta( 'received_confirmed' );
	}

	/**
	 *
	 * Get crypto amount received unconfirmed wc order meta
	 *
	 * @return int
	 */
	public function get_received_unconfirmed() {
		return (int) $this->get_order_object()->get_meta( 'received_unconfirmed' );
	}

	/**
	 *
	 * Get tx ids wc order meta as array
	 *
	 * @return string[]
	 */
	public function get_tx_ids() {
		return json_decode( $this->get_order_object()->get_meta( 'txids' ), true ) ?: array();
	}

	/**
	 *
	 * Get tx ids wc order meta as string
	 *
	 * @return string
	 */
	public function get_tx_ids_string() {
		return implode( PHP_EOL, array_keys( $this->get_tx_ids() ) );
	}

	/**
	 *
	 * Get refund address wc order meta
	 *
	 * @return string
	 */
	public function get_refund_address() {
		return $this->get_order_object()->get_meta( 'refund_address' );
	}

	/**
	 *
	 * Get mpk key index wc order meta
	 *
	 * @return string
	 */
	public function get_mpk_key_index() {
		return $this->get_order_object()->get_meta( 'mpk_key_index' );
	}

	/**
	 *
	 * Get wc order status
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->get_order_object()->get_status();
	}

	/**
	 *
	 * Get wc order billing email address
	 *
	 * @return string
	 */
	public function get_billing_email() {
		return $this->get_order_object()->get_billing_email();
	}

	/**
	 *
	 * Get wc order checkout payment url
	 *
	 * @return string
	 */
	public function get_checkout_payment_url() {
		return $this->get_order_object()->get_checkout_payment_url();
	}

	/**
	 *
	 * Add amount to data array.
	 *
	 * @param string|float|int $amount Amount in fiat.
	 *
	 * @return $this
	 */
	public function set_amount( $amount ) {
		return $this->set_data_item( 'amount', $amount );
	}

	/**
	 *
	 * Add crypto amount to data array.
	 *
	 * @param int $crypto_amount Amount in fiat.
	 *
	 * @return $this
	 */
	public function set_crypto_amount( $crypto_amount ) {
		return $this->set_data_item( 'crypto_amount', $crypto_amount );
	}

	/**
	 *
	 * Add payment currency to data array.
	 *
	 * @param string $payment_currency Payment currency code (eg. BTC).
	 *
	 * @return $this
	 */
	public function set_payment_currency( $payment_currency ) {
		return $this->set_data_item( 'payment_currency', $payment_currency );
	}

	/**
	 *
	 * Add payment currency to data array.
	 *
	 * @param string $address Payment address in blockchain.
	 *
	 * @return $this
	 */
	public function set_payment_address( $address ) {
		return $this->set_data_item( 'payment_address', $address );
	}

	/**
	 *
	 * Add received confirmed to data array.
	 *
	 * @param int $amount_received Amount received in satoshis.
	 *
	 * @return $this
	 */
	public function set_received_confirmed( $amount_received ) {
		return $this->set_data_item( 'received_confirmed', (int) $amount_received );
	}

	/**
	 *
	 * Add received confirmed to data array.
	 *
	 * @param int $amount_unconfirmed Amount pending in satoshis.
	 *
	 * @return $this
	 */
	public function set_received_unconfirmed( $amount_unconfirmed ) {
		return $this->set_data_item( 'received_unconfirmed', (int) $amount_unconfirmed );
	}

	/**
	 *
	 * Add confirmed to data array.
	 *
	 * @param bool|string $tx_confirmed If blockchain tx is confirmed or not.
	 *
	 * @return $this
	 */
	public function set_tx_confirmed( $tx_confirmed = 'confirmed' ) {
		return $this->set_data_item( 'tx_confirmed', $tx_confirmed );
	}

	/**
	 *
	 * Add tx pending to data array.
	 *
	 * @return $this
	 */
	public function set_tx_pending() {
		return $this->set_tx_confirmed( 'pending' );
	}

	/**
	 *
	 * Add tx ids to data array.
	 *
	 * @param string[] $txids Transaction ids.
	 *
	 * @return $this
	 */
	public function set_tx_ids( $txids ) {
		return $this->set_data_item( 'txids', wp_json_encode( $txids ) );
	}

	/**
	 *
	 * Add amount difference to data array.
	 *
	 * @param string $amount_difference Amount difference in crypto.
	 *
	 * @return $this
	 */
	public function set_amount_difference( $amount_difference ) {
		return $this->set_data_item( 'amount_difference', $amount_difference );
	}

	/**
	 *
	 * Add percentage paid to data array.
	 *
	 * @param string $percentage_paid Percentage paid.
	 *
	 * @return $this
	 */
	public function set_percentage_paid( $percentage_paid ) {
		return $this->set_data_item( 'percentage_paid', $percentage_paid );
	}

	/**
	 *
	 * Add master public key index to data array.
	 *
	 * @param string $mpk_key_index Master public key index.
	 *
	 * @return $this
	 */
	public function set_mpk_key_index( $mpk_key_index ) {
		return $this->set_data_item( 'mpk_key_index', $mpk_key_index );
	}

	/**
	 *
	 * Add master public key position to data array.
	 *
	 * @param string $mpk_key_position Master public key position.
	 *
	 * @return $this
	 */
	public function set_mpk_key_position( $mpk_key_position ) {
		return $this->set_data_item( 'mpk_key_index', $mpk_key_position );
	}

	/**
	 *
	 * Add refund address to data array.
	 *
	 * @param string $refund_address Refund address.
	 *
	 * @return $this
	 */
	public function set_refund_address( $refund_address ) {
		return $this->set_data_item( 'refund_address', $refund_address );
	}

	/**
	 *
	 * Insert CryptoPay Woocommerce Order Meta.
	 * This is identical as update, just for nice code readability.
	 *
	 * @return int|false The Order ID if successful, 0 if unsuccessful.
	 */
	public function insert() {
		return $this->update();
	}

	/**
	 *
	 * Update CryptoPay Woocommerce Order Meta.
	 *
	 * @return false|int The Order ID if successful, false if unsuccessful.
	 */
	public function update() {
		return $this->insert_or_update_order_meta();
	}

	/**
	 *
	 * Delete CryptoPay Woocommerce Order Meta.
	 *
	 * @return false|int
	 */
	public function delete() {
		return $this->delete_order_meta();
	}

	/**
	 *
	 * Add an order note for customer.
	 *
	 * @param string $note Note to add to the order.
	 *
	 * @return self
	 */
	public function add_customer_note( $note ) {
		$this->get_order_object()->add_order_note( $note, true );

		return $this;
	}

	/**
	 *
	 * Add an order note for admin.
	 *
	 * @param string $note Note to add to the order.
	 *
	 * @return self
	 */
	public function add_admin_note( $note ) {
		$this->add_order_note( $note, false );

		return $this;
	}

	/**
	 *
	 * Add an order note for system or customer.
	 *
	 * @param string $note             Note to add to the order.
	 * @param bool   $is_customer_note True if customer should get this note or false if system only.
	 *
	 * @return self
	 */
	public function add_order_note( $note, $is_customer_note = false ) {
		$this->get_order_object()->add_order_note( $note, $is_customer_note );

		return $this;
	}

	/**
	 *
	 * Update wc order status
	 *
	 * @param string $new_status Status to change the order to. No internal wc- prefix is required.
	 * @param string $note       Optional note to add.
	 * @param bool   $manual     Is this a manual order status change?.
	 *
	 * @return $this
	 */
	private function update_status( $new_status, $note = '', $manual = false ) {
		$this->get_order_object()->update_status( $new_status, $note, $manual );

		return $this;
	}

	/**
	 *
	 * Update wc order status to on hold
	 *
	 * @param string $note   Optional note to add.
	 * @param bool   $manual Is this a manual order status change?.
	 *
	 * @return $this
	 */
	public function update_status_on_hold( $note = '', $manual = false ) {
		return $this->update_status( 'on-hold', $note, $manual );
	}

	/**
	 *
	 * Update wc order status to quote refresh
	 *
	 * @param string $note   Optional note to add.
	 * @param bool   $manual Is this a manual order status change?.
	 *
	 * @return $this
	 */
	public function update_status_quote_refresh( $note = '', $manual = false ) {
		return $this->update_status( 'quote-refresh', $note, $manual );
	}

	/**
	 *
	 * Update wc order status to cancelled
	 *
	 * @param string $note   Optional note to add.
	 * @param bool   $manual Is this a manual order status change?.
	 *
	 * @return $this
	 */
	public function update_status_cancelled( $note = '', $manual = false ) {
		return $this->update_status( 'cancelled', $note, $manual );
	}

	/**
	 *
	 * Update wc order status to failed
	 *
	 * @param string $note   Optional note to add.
	 * @param bool   $manual Is this a manual order status change?.
	 *
	 * @return $this
	 */
	public function update_status_failed( $note = '', $manual = false ) {
		return $this->update_status( 'failed', $note, $manual );
	}

	/**
	 *
	 * Update wc order status to completed
	 *
	 * @param string $note   Optional note to add.
	 * @param bool   $manual Is this a manual order status change?.
	 *
	 * @return $this
	 */
	public function update_status_completed( $note = '', $manual = false ) {
		return $this->update_status( cw_get_option( 'final_order_status' ), $note, $manual );
	}

	/**
	 * When a payment is complete this function is called.
	 *
	 * Most of the time this should mark an order as 'processing' so that admin can process/post the items.
	 * If the cart contains only downloadable items then the order is 'completed' since the admin needs to take no action.
	 * Stock levels are reduced at this point.
	 * Sales are also recorded for products.
	 * Finally, record the date of payment.
	 *
	 * @param  string $transaction_id Optional transaction id to store in post meta.
	 * @return bool success
	 */
	public function register_payment_complete( $transaction_id = '' ) {
		return $this->get_order_object()->payment_complete( $transaction_id );
	}

	/**
	 *
	 * Update order status info in database
	 *
	 * @param string $address  Cryptocurrency blockchain payment address.
	 * @param bool   $paid     true or 1 if paid, false or 0 if unpaid.
	 * @param int    $timeout  true or 1-3 if timeout, 0 if not timeout yet.
	 * @param int    $order_id Woocommerce order id.
	 *
	 * @return  mixed
	 * @package OrderProcess
	 */
	public static function update_order_info( $address, $paid, $timeout, $order_id ) {

	}

	/**
	 *
	 * Insert or update WooCommerce order meta
	 *
	 * @return false|int The Order ID if successful or false if unsuccessful.
	 */
	private function insert_or_update_order_meta() {
		$order = $this->validate_order_meta_and_get_order_object();
		if ( ! $order ) {
			return false;
		}

		foreach ( $this->get_data() as $meta_key => $meta_value ) {
			$order->update_meta_data( $meta_key, $meta_value );
		}

		// Reset meta_data array in case an instance of this class is reused to avoid duplicate update_meta_data calls.
		$this->reset_data();

		return $order->save() ?: false;
	}

	/**
	 *
	 * Delete WooCommerce order meta
	 *
	 * @return false|int The Order ID if successful or false if unsuccessful.
	 */
	private function delete_order_meta() {
		$order = $this->validate_order_meta_and_get_order_object();
		if ( ! $order ) {
			return false;
		}

		foreach ( $this->get_data() as $meta_key => $meta_value ) {
			$order->delete_meta_data( $meta_key );
		}

		return $order->save() ?: false;
	}


	/**
	 *
	 * Validate order meta before insert, update or delete.
	 *
	 * @return WC_Order|false
	 */
	private function validate_order_meta_and_get_order_object() {
		if ( ! count( $this->get_data() ) ) {
			return false;
		}

		return $this->get_order_object();
	}
}
