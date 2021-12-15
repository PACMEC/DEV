<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Payment Details object for CryptoWoo Database rows
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage Database
 * @author     CryptoWoo AS
 */
class CW_Payment_Details_Object implements Countable, Iterator {

	/**
	 *
	 * CryptoWoo payments details table rows object
	 *
	 * @var stdClass|stdClass[]
	 */
	private $payment_details = array();

	/**
	 *
	 * CW_Payment_Details_Object constructor.
	 *
	 * @param stdClass[]|stdClass $payment_details CryptoWoo payments details table rows object.
	 */
	public function __construct( $payment_details ) {
		if ( ! is_array( $payment_details ) ) {
			$payment_details = array( $payment_details );
		}

		foreach ( $payment_details as $payment_detail ) {
			$this->payment_details[ $payment_detail->order_id ] = $payment_detail;
		}
	}

	/**
	 *
	 * Get the woocommerce order id
	 *
	 * @return int
	 */
	public function get_order_id() {
		return (int) $this->get_current_row()->order_id;
	}

	/**
	 *
	 * Get the payment address
	 *
	 * @return string
	 */
	public function get_address() {
		return $this->get_current_row()->address;
	}

	/**
	 *
	 * Get crypto amount received confirmed
	 *
	 * @return int
	 */
	public function get_received_confirmed() {
		return (int) $this->get_current_row()->received_confirmed;
	}

	/**
	 *
	 * Get crypto amount received unconfirmed
	 *
	 * @return int
	 */
	public function get_received_unconfirmed() {
		return (int) $this->get_current_row()->received_unconfirmed;
	}

	/**
	 *
	 * Get fiat amount
	 *
	 * @return float
	 */
	public function get_fiat_amount() {
		return floatval( $this->get_current_row()->amount );
	}

	/**
	 *
	 * Get crypto amount
	 *
	 * @return int
	 */
	public function get_crypto_amount_due() {
		return (int) $this->get_current_row()->crypto_amount;
	}

	/**
	 *
	 * Get blockchain payment currency
	 *
	 * @return string
	 */
	public function get_payment_currency() {
		return $this->get_current_row()->payment_currency;
	}

	/**
	 *
	 * Get created at timestamp
	 *
	 * @return int
	 */
	public function get_created_at() {
		return strtotime( $this->get_current_row()->created_at );
	}

	/**
	 *
	 * Get last updated timestamp
	 *
	 * @return int
	 */
	public function get_last_update() {
		return strtotime( $this->get_current_row()->last_update );
	}

	/**
	 *
	 * Get timeout
	 *
	 * @return int
	 */
	public function get_timeout() {
		return (int) $this->get_current_row()->timeout;
	}

	/**
	 *
	 * Get timeout timestamp
	 *
	 * @return int
	 */
	public function get_timeout_timestamp() {
		return (int) $this->get_current_row()->timeout_value;
	}

	/**
	 *
	 * Get blockchain transaction ids
	 *
	 * @return string[]
	 */
	public function get_tx_ids() {
		return json_decode( $this->get_current_row()->txids, true ) ?: array();
	}

	/**
	 *
	 * Get blockchain transaction ids
	 *
	 * @return string
	 */
	public function get_tx_ids_json_encoded() {
		return $this->get_current_row()->txids;
	}

	/**
	 *
	 * Get paid status, true if paid and false if unpaid
	 *
	 * @return bool
	 */
	public function get_is_paid() {
		return (bool) $this->get_current_row()->paid;
	}

	/**
	 *
	 * Get the current CryptoWoo payment details object row.
	 *
	 * @return stdClass
	 */
	public function get_current_row() {
		return $this->payment_details[ $this->key() ];
	}

	/**
	 *
	 * Rewind the payments array to the first index
	 */
	public function rewind() {
		reset( $this->payment_details );
	}


	/**
	 *
	 * Get the current CryptoWoo Payment Details table row object.
	 *
	 * @return $this
	 */
	public function current() {
		return $this;
	}

	/**
	 *
	 * Get the current CryptoWoo Payment Details table row key.
	 *
	 * @return string|int
	 */
	public function key() {
		return key( $this->payment_details );
	}

	/**
	 *
	 * Iterate to the next CryptoWoo Payment Details row.
	 *
	 * @return $this|void
	 */
	public function next() {
		next( $this->payment_details );

		return $this;
	}

	/**
	 *
	 * Check if the CryptoWoo Payment Details table object row is valid.
	 *
	 * @return bool
	 */
	public function valid() {
		$key = key( $this->payment_details );

		return ( null !== $key && false !== $key );
	}

	/**
	 *
	 * Count elements of an object (overrides php count function).
	 *
	 * @link   https://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 */
	public function count() {
		return count( $this->payment_details );
	}

	/**
	 *
	 * Check if the payment details array is empty.
	 *
	 * @return bool
	 */
	public function is_empty() {
		return empty( $this->payment_details );
	}
}
