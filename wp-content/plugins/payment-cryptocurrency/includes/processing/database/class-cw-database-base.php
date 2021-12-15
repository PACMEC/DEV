<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Order processing database base class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage Database
 * @author     CryptoWoo AS
 */
class CW_Database_Base extends CW_Singleton_Array {


	/**
	 *
	 * Data array of key => value pairs.
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 *
	 * Return a new instance of this class.
	 *
	 * @param WC_Order|int $order Woocommerce order id or WC_Order object.
	 * @param static       $obj   Set the object (For unit testing).
	 *
	 * @throws InvalidArgumentException Invalid argument if $order is not WC_Order or string or int or string cast able.
	 * @return static|false
	 */
	public static function instance( $order, $obj = null ) {
		$instance = self::get_instance( $order, $obj );

		if ( ! $instance->get_order_object() ) {
			return false;
		}

		return $instance;
	}

	/**
	 *
	 * Data array of key => value pairs.
	 *
	 * @return int
	 */
	protected function get_order_id() {
		return $this->get_id();
	}

	/**
	 *
	 * Data array of key => value pairs.
	 *
	 * @return array
	 */
	protected function get_data() {
		return $this->data;
	}

	/**
	 *
	 * Set an array item value of key => value.
	 *
	 * @param string $key   Array key.
	 * @param mixed  $value Array item value.
	 *
	 * @return static
	 */
	protected function set_data_item( string $key, $value ) {
		$this->data[ $key ] = $value;

		return $this;
	}

	/**
	 *
	 * Reset data array.
	 *
	 * @return static
	 */
	protected function reset_data() {
		$this->data = array();

		return $this;
	}

	/**
	 *
	 * Get the order object from stored order id.
	 *
	 * @return WC_Order
	 */
	protected function get_order_object() {
		return wc_get_order( $this->get_order_id() );
	}

	/**
	 *
	 * Validate the order object from stored order id.
	 *
	 * @return bool
	 */
	protected function validate_order_object() {

		$order = wc_get_order( $this->get_order_id() );
		/**
*
	* Prevent updating of orders that aren't paid with CryptoWoo
		 * maybe the customer switched to another method after submitting his order
		 * and it did not get removed from the processing queue yet
*/
		if ( $order->get_payment_method() !== CW_PAYMENT_METHOD_ID ) {
			// TODO: Throw an exception or other form of error troubleshooting.
			return false;
		}

		if ( ! is_object( $order ) || ! $order instanceof WC_Order ) {
			// TODO: Throw an exception or other form of error troubleshooting.
			return false;
		}

		return true;
	}

	/**
	 *
	 * Validate the order object from stored order id is CryptoWoo order.
	 *
	 * @return bool
	 */
	public function payment_method_is_cryptowoo() {
		/**
*
	* Prevent updating of orders that aren't paid with CryptoWoo
		 * maybe the customer switched to another method after submitting his order
		 * and it did not get removed from the processing queue yet
*/
		if ( $this->get_order_object()->get_payment_method() !== CW_PAYMENT_METHOD_ID ) {
			// TODO: Throw an exception or other form of error troubleshooting.
			return false;
		}

		return true;
	}
}
