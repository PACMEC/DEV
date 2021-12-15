<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Order processing database handler for CryptoWoo Database
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage Database
 * @author     CryptoWoo AS
 */
class CW_Database_CryptoWoo extends CW_Database_Base {


	/**
	 *
	 * Add expired or pending (timeout value 1 or 0) to data array.
	 *
	 * @param bool $is_timed_out True if order has expired and 0 if order is pending.
	 *
	 * @return $this
	 */
	public function set_is_expired( $is_timed_out = true ) {
		return $this->set_timeout( $is_timed_out );
	}

	/**
	 *
	 * Add expired but payment seen and pending confirm (timeout value 3) to data array.
	 *
	 * @return $this
	 */
	public function set_is_timeout_and_pending_confirm() {
		return $this->set_timeout( 3 );
	}

	/**
	 *
	 * Add expired and needs quote refresh (timeout value 4) to data array.
	 *
	 * @return $this
	 */
	public function set_is_timeout_and_needs_quote_refresh() {
		return $this->set_timeout( 4 );
	}

	/**
	 *
	 * Add timeout to data array.
	 *
	 * @param bool|int $is_timed_out true or 1-4 if timeout, 0 if not timeout yet.
	 *
	 * @return $this
	 */
	public function set_timeout( $is_timed_out = true ) {
		return $this->set_data_item( 'timeout', array( '%d' => (int) $is_timed_out ) );
	}

	/**
	 *
	 * Add paid to data array.
	 *
	 * @param bool $is_paid true or 1-3 if timeout, 0 if not timeout yet.
	 *
	 * @return $this
	 */
	public function set_paid( bool $is_paid = true ) {
		return $this->set_data_item( 'paid', array( '%d' => (int) $is_paid ) );
	}

	/**
	 *
	 * Add paid to data array.
	 *
	 * @param string $gmt_date gmt date string, if unset will be set to current gmt date.
	 *
	 * @return $this
	 */
	public function set_last_update( string $gmt_date = '' ) {
		$gmt_date ?: $gmt_date = gmdate( 'Y-m-d H:i:s' );

		return $this->set_data_item( 'last_update', array( '%s' => $gmt_date ) );
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
		return $this->set_data_item( 'amount', array( '%s' => $amount ) );
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
		return $this->set_data_item( 'crypto_amount', array( '%d' => $crypto_amount ) );
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
		return $this->set_data_item( 'payment_currency', array( '%s' => $payment_currency ) );
	}

	/**
	 *
	 * Add payment currency to data array.
	 *
	 * @param string $address Payment address in blockchain.
	 *
	 * @return $this
	 */
	public function set_address( $address ) {
		return $this->set_data_item( 'address', array( '%s' => $address ) );
	}

	/**
	 *
	 * Add customer reference to data array.
	 *
	 * @param string $customer_reference Customer reference.
	 *
	 * @return $this
	 */
	public function set_customer_reference( $customer_reference ) {
		return $this->set_data_item( 'customer_reference', array( '%s' => $customer_reference ) );
	}

	/**
	 *
	 * Add order id to data array.
	 *
	 * @param int $order_id Woocommerce order id.
	 *
	 * @return $this
	 */
	public function set_order_id( $order_id ) {
		return $this->set_data_item( 'order_id', array( '%d' => $order_id ) );
	}

	/**
	 *
	 * Add timeout value to data array.
	 *
	 * @param int $timeout_value Timeout value (timestamp).
	 *
	 * @return $this
	 */
	public function set_timeout_value( $timeout_value = '' ) {
		if ( ! $timeout_value ) {
			$now            = strtotime( gmdate( 'Y-m-d H:i:s' ) );
			$timeout_option = (int) cw_get_option( 'order_timeout_min' ) * 60 ?: 1800; // Default to 30 minutes.
			$timeout_value  = $now + $timeout_option;// add address timeout.
		}

		return $this->set_data_item( 'timeout_value', array( '%d' => $timeout_value ) );
	}

	/**
	 *
	 * Add created at data array.
	 *
	 * @param string $gmt_date gmt date string, if unset will be set to current gmt date.
	 *
	 * @return $this
	 */
	public function set_created_at( string $gmt_date = '' ) {
		$gmt_date ?: $gmt_date = gmdate( 'Y-m-d H:i:s' );

		return $this->set_data_item( 'created_at', array( '%s' => $gmt_date ) );
	}

	/**
	 *
	 * Add is archived to data array.
	 *
	 * @param bool $is_archived true or 1if is archived, 0 if not archived.
	 *
	 * @return $this
	 */
	public function set_archived( $is_archived = true ) {
		return $this->set_data_item( 'is_archived', array( '%d' => (int) $is_archived ) );
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
		return $this->set_data_item( 'received_confirmed', array( '%d' => (int) $amount_received ) );
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
		return $this->set_data_item( 'received_unconfirmed', array( '%d' => (int) $amount_unconfirmed ) );
	}

	/**
	 *
	 * Add tx ids to data array.
	 *
	 * @param string[] $txids Transaction ids (array).
	 *
	 * @return $this
	 */
	public function set_tx_ids( $txids ) {
		return $this->set_data_item( 'txids', array( '%s' => wp_json_encode( $txids ) ) );
	}

	/**
	 *
	 * Insert CryptoWoo Payments Table row.
	 *
	 * @return int|false The number of rows inserted, or false on error.
	 */
	public function insert() {
		$data_values = self::get_values_from_data( $this->get_data() );
		$data_format = self::get_wildcards_from_data( $this->get_data() );

		$data_values['order_id'] = $this->get_order_id(); // int.
		$data_format[]           = '%d'; // order_id.

		return self::insert_payments_table_row( $data_values, $data_format );
	}

	/**
	 *
	 * Update CryptoWoo Payments Table row.
	 *
	 * @return false|int
	 */
	public function update() {
		// Make sure that the last_update timestamp is always updated during update.
		$this->set_last_update();

		$data_values = self::get_values_from_data( $this->get_data() );
		$data_format = self::get_wildcards_from_data( $this->get_data() );

		return self::update_payments_table_row( $data_values, $data_format, array( 'order_id' => $this->get_order_id() ) );
	}

	/**
	 *
	 * Delete CryptoWoo Payments Table row.
	 *
	 * @return false|int
	 */
	public function delete() {
		$delete_order_payment_request = $this->delete_payments_table_row( array( 'order_id' => $this->get_order_id() ) );

		if ( empty( $delete_order_payment_request ) ) {
			$message = 'Payment request has been deleted.';
		} else {

			$message = $delete_order_payment_request;
		}

		return $message;
	}

	/**
	 *
	 * Get paid status of address in table payments
	 *
	 * @param string $address Cryptocurrency blockchain payment address.
	 *
	 * @return  bool
	 * @package OrderProcess
	 */
	public static function paid( $address ) {
		$sql   = self::prepare_payments_table( 'paid', "address = '%s'", $address );
		$query = self::wpdb()->get_var( $sql );

		if ( 1 === $query ) {
			return true;
		} else {
			return $query;
		}
	}

	/**
	 *
	 * Return payment details of an order
	 *
	 * @param string    $condition          Where condition (with or without placeholders).
	 * @param mixed     $args               Values to enter into placeholders.
	 * @param false|int $limit              Limits the number of results.
	 * @param string    $order_by           Which column to order by.
	 * @param string    $order              Which order to order by ('desc' or 'asc') default is 'desc'.
	 * @param bool      $order_by_blocktime Whether to order by block time after the db query is returned.
	 *
	 * @return CW_Payment_Details_Object
	 */
	public static function get_payment_details( $condition, $args, $limit = 0, $order_by = 'id', $order = 'desc', $order_by_blocktime = false ) {
		$sql   = self::prepare_payments_table( '*', "$condition", $args, "$order_by", "$order", "$limit" );
		$query = self::wpdb()->get_results( $sql );

		if ( $order_by_blocktime ) {
			$query = CW_OrderSorting::prioritize_unpaid_addresses( $query, $limit );
		}

		return new CW_Payment_Details_Object( $query );
	}

	/**
	 *
	 * Return payment details of an order via order ID
	 *
	 * @param string $order_id Order id to search for.
	 *
	 * @return CW_Payment_Details_Object
	 */
	public static function get_payment_details_by_order_id( $order_id ) {
		return self::get_payment_details( 'order_id = %d', "$order_id" );
	}

	/**
	 *
	 * Return unpaid CryptoWoo orders.
	 * Return all orders in table cryptowoo_payments_temp that
	 *        - have not been paid yet
	 *        - are older than min_order_age seconds
	 *        - have not timed out (being 0 or 3)
	 *        - have an order amount > 0
	 * Orders that weren't updated lately take priority
	 *
	 * @param string|false $currency           Currency code (eg BTC).
	 * @param int          $limit              Limit number of unpaid orders to get.
	 * @param bool         $order_by_blocktime Order by cryptocurrencies block time.
	 *
	 * @return  CW_Payment_Details_Object
	 * @package OrderProcess
	 */
	public static function get_unpaid_orders_payment_details( $currency = false, $limit = 10, $order_by_blocktime = true ) {
		$args       = $currency ? $currency : array();
		$condition  = "paid = '0' AND timeout != '1' AND timeout != '4' AND crypto_amount > 0";
		$condition .= $currency ? ' AND payment_currency = %s' : '';

		return self::get_payment_details( $condition, $args, $limit, 'last_update', 'asc', $order_by_blocktime );
	}

	/**
	 *
	 * Get paid CryptoWoo orders.
	 * Get orders from table cryptowoo_payments_temp for a specific currency that are marked as paid and contain only confirmed coins
	 * Return orders for all currencies if $currency_name is not specified
	 *
	 * @param string|false $currency Currency code (eg. BTC).
	 *
	 * @return  CW_Payment_Details_Object
	 * @package OrderProcess
	 */
	public static function get_paid_orders( $currency = false ) {
		$args       = $currency ? $currency : array();
		$condition  = "received_confirmed > 0 AND received_unconfirmed = '0' AND paid = '1' AND crypto_amount > 0 AND is_archived = '0'";
		$condition .= $currency ? ' AND payment_currency = %s' : '';

		return self::get_payment_details( $condition, $args, 0 );
	}

	/**
	 *
	 * Get CryptoWoo orders pending blockchain confirms.
	 * Return orders from table cryptowoo_payments_temp that have been marked as paid but still contain unconfirmed transactions
	 * Orders that weren't updated lately take priority
	 *
	 * @param string|false $currency Currency code (eg. BTC), false for no filter.
	 *
	 * @return  CW_Payment_Details_Object
	 * @package OrderProcess
	 */
	public static function get_unconfirmed_addresses( $currency = false, $limit = 10, $order_by_blocktime = true ) {
		$args       = $currency ? $currency : array();
		$condition  = "received_unconfirmed > 0 AND crypto_amount > 0 AND paid > 0 AND timeout != '1' AND timeout < '4'";
		$condition .= $currency ? ' AND payment_currency = %s' : '';

		return self::get_payment_details( $condition, $args, $limit, 'date(last_update)', 'asc' );
	}

	/**
	 *
	 * Update last_update timestamp for an order.
	 *
	 * @param int $order_id Woocommerce Order ID.
	 *
	 * @return mixed
	 */
	public static function bump_last_update( $order_id ) {
		return self::instance( $order_id )->update();
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
		return self::instance( $order_id )
			->set_address( $address )
			->set_paid( $paid )
			->set_is_expired( $timeout )
			->update();
	}

	/**
	 *
	 * Delete a payment request from DB
	 *
	 * @param int $order_id Woocommerce order id.
	 *
	 * @return string
	 */
	public static function delete_order_payment_request( $order_id ) {
		return self::instance( $order_id )
		->delete();
	}

	/**
	 *
	 * Save payment details to DB
	 *
	 * @param string $payment_address    Cryptocurrency blockchain payment address.
	 * @param int    $amount             Order total in fiat value.
	 * @param string $customer_reference Customer reference.
	 * @param string $payment_currency   Payment currency code (eg. BTC).
	 * @param float  $crypto_amount      Order total in cryptocurrency satoshi value.
	 * @param int    $order_id           Woocommerce order id.
	 *
	 * @return int|false The number of rows inserted, or false on error.
	 */
	public static function insert_payment_details( $payment_address, $amount, $customer_reference, $payment_currency, $crypto_amount, $order_id ) {
		return self::instance( $order_id )
			->set_amount( $amount )
			->set_crypto_amount( $crypto_amount )
			->set_payment_currency( $payment_currency )
			->set_address( $payment_address )
			->set_customer_reference( $customer_reference )
			->set_archived( false )
			->set_timeout_value()
			->set_created_at()
			->set_last_update( '1970-01-01 00:00:01' )
			->insert();
	}

	/**
	 *
	 * Update CryptoWoo orders payment details
	 *
	 * @param string $payment_address    Cryptocurrency blockchain payment address.
	 * @param int    $amount             Order total in fiat value.
	 * @param string $customer_reference Customer reference.
	 * @param string $payment_currency   Payment currency code (eg. BTC).
	 * @param float  $crypto_amount      Order total in cryptocurrency satoshi value.
	 * @param int    $order_id           Woocommerce order id.
	 *
	 * @return int|false
	 */
	public static function reset_payment_details( $payment_address, $amount, $customer_reference, $payment_currency, $crypto_amount, $order_id ) {
		return self::instance( $order_id )
			->set_amount( $amount )
			->set_crypto_amount( $crypto_amount )
			->set_payment_currency( $payment_currency )
			->set_address( $payment_address )
			->set_customer_reference( $customer_reference )
			->set_received_confirmed( 0 )
			->set_received_unconfirmed( 0 )
			->set_is_expired( false )
			->set_archived( false )
			->set_timeout_value()
			->update();
	}

	/**
	 *
	 * Update the payment request for the order_id.
	 *
	 * @param string $order_id Woocommerce order id.
	 *
	 * @return false|int
	 */
	public static function update_order_status_completed( $order_id ) {
		return self::instance( $order_id )
			->set_paid()
			->update();
	}

	/**
	 * Force requeue the address with a new timeout
	 *
	 * @param \WC_Order $order Woocommerce order object.
	 */
	public static function requeue_order( $order ) {
		self::instance( $order->get_id() )
		->set_is_expired( false )
		->set_archived( false )
		->set_timeout_value()
		->update();
	}

	/**
	 *
	 * Fires when a WooCommerce order is set to "cancelled"
	 * Sets the timeout value to 1.
	 *
	 * @param int $order_id Woocommerce order id.
	 *
	 * @return bool
	 */
	public static function update_order_status_cancelled_timeout( $order_id ) {
		return self::instance( $order_id )
			->set_is_expired()
			->update();
	}

	/**
	 *
	 * Prepare wpdb cryptowoo payments table sql.
	 *
	 * @param string       $column    Column name in sql select condition.
	 * @param string       $condition Where condition (eg "post_id = %s").
	 * @param string|array $args      variable(s) to substitute into the query's placeholders.
	 * @param bool         $order_by  Order by column name.
	 * @param string       $order     Order type (asc or desc), only used if $order_by is set.
	 * @param int          $limit     Limit results in the return value, if 0 or false no limit is used.
	 *
	 * @return string|void
	 */
	public static function prepare_payments_table( $column, $condition, $args = array(), $order_by = false, $order = 'asc', $limit = 0 ) {
		return self::prepare( $column, self::payments_table_name(), $condition, $args, $order_by, $order, $limit );
	}

	/**
	 *
	 * Prepare wpdb sql.
	 *
	 * @param string       $column    Column name in sql select condition.
	 * @param string       $table     Table name in sql where condition.
	 * @param string       $condition Where condition (eg "post_id = %s").
	 * @param string|array $args      variable(s) to substitute into the query's placeholders.
	 * @param bool         $order_by  Order by column name.
	 * @param string       $order     Order type (asc or desc), only used if $order_by is set.
	 * @param int          $limit     Limit results in the return value, if 0 or false no limit is used.
	 *
	 * @return string|void
	 */
	public static function prepare( $column, $table, $condition, $args = array(), $order_by = false, $order = 'asc', $limit = 0 ) {
		$prefix = self::prefix_with_table_name( $table );
		$sql    = "SELECT {$column} FROM {$prefix} WHERE {$condition}";

		if ( $order_by ) {
			$sql .= " ORDER BY $order_by $order ";
		}

		// Make sure args is an array.
		$args = is_array( $args ) ? $args : array( $args );

		if ( $limit ) {
			$args[] = (int) $limit;
			$sql   .= ' LIMIT %d';
		}

		// If there are no args then we must not do prepare.
		if ( empty( $args ) ) {
			return $sql;
		}

		return self::wpdb()->prepare( $sql, $args );
	}

	/**
	 *
	 * Update a row in the table
	 *
	 * @param array        $data_values  Data to update (in column => value pairs).
	 * @param array|string $data_format  Optional. An array of formats to be mapped to each of the values in $data.
	 * @param array        $where_values A named array of WHERE clauses (in column => value pairs).
	 * @param array|string $where_format Optional. An array of formats to be mapped to each of the values in $where.
	 *
	 * @return int|false The number of rows updated, or false on error.
	 */
	private static function update_payments_table_row( $data_values, $data_format, $where_values, $where_format = '%d' ) {
		$table_name = self::prefix_with_table_name( self::payments_table_name() );

		return self::wpdb()->update( $table_name, $data_values, $where_values, $data_format, $where_format );
	}

	/**
	 *
	 * Insert a row in the table
	 *
	 * @param array        $data_values Data to update (in column => value pairs).
	 * @param array|string $data_format Optional. An array of formats to be mapped to each of the values in $data.
	 *
	 * @return int|false The number of rows inserted, or false on error.
	 */
	private static function insert_payments_table_row( $data_values, $data_format ) {
		$table_name = self::prefix_with_table_name( self::payments_table_name() );

		return self::wpdb()->insert( $table_name, $data_values, $data_format );
	}

	/**
	 *
	 * Delete a row in the table
	 *
	 * @param array|       $where_values A named array of WHERE clauses (in column => value pairs).
	 * @param array|string $where_format Optional. An array of formats to be mapped to each of the values in $where.
	 *
	 * @return int|false The number of rows updated, or false on error.
	 */
	private static function delete_payments_table_row( $where_values, $where_format = '%d' ) {
		$table_name = self::prefix_with_table_name( self::payments_table_name() );

		return self::wpdb()->delete( $table_name, $where_values, $where_format );
	}

	/**
	 *
	 * Generate wildcards array from data array
	 *
	 * @param array $data Array of values.
	 *
	 * @return string[]
	 */
	private static function get_wildcards_from_data( array $data ) {
		$data_format = array();

		foreach ( $data as $item ) {
			foreach ( $item as $wildcard => $value ) {
				$data_format[] = $wildcard;
			}
		}

		return $data_format;
	}

	/**
	 *
	 * Generate values array from data array
	 *
	 * @param array $data Array of values.
	 *
	 * @return string[]
	 */
	private static function get_values_from_data( array $data ) {
		$data_format = array();

		foreach ( $data as $data_key => $item ) {
			foreach ( $item as $wildcard => $value ) {
				$data_format[ $data_key ] = $value;
			}
		}

		return $data_format;
	}

	/**
	 *
	 * Get CryptoWoo payments table name
	 *
	 * @return string
	 */
	public static function payments_table_name() {
		return 'cryptowoo_payments_temp';
	}

	/**
	 *
	 * Get table name
	 *
	 * @param string $table_name The name of the table except table prefix.
	 *
	 * @return string
	 */
	public static function prefix_with_table_name( $table_name ) {
		$wpdb_prefix = self::wpdb_prefix();

		return "{$wpdb_prefix}$table_name";
	}

	/**
	 *
	 * Return the wpdb class
	 *
	 * @return wpdb
	 */
	private static function wpdb() {
		global $wpdb;

		return $wpdb;
	}

	/**
	 *
	 * Return the wpdb prefix name
	 *
	 * @return string
	 */
	private static function wpdb_prefix() {
		return self::wpdb()->prefix;
	}
}
