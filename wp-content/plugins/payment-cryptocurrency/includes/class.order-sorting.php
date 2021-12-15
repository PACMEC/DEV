<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly
/**
 * Sorting of order objects
 */
class CW_OrderSorting {


	static function cw_order_by_id( $a, $b ) {
		return strcmp( $a->id, $b->id );
	}

	static function cw_order_by_date( $a, $b ) {
		return strcmp( strtotime( $a->order_date ), strtotime( $b->order_date ) );
	}

	static function cw_order_by_last_update( $a, $b ) {
		return strcmp( strtotime( $a->last_update ), strtotime( $b->last_update ) );
	}

	static function cw_order_by_timeout( $a, $b ) {
		return $a->timeout_value < $b->timeout_value;
	}

	static function cw_order_by_received_confirmed( $a, $b ) {
		return strcmp( $a->received_confirmed, $b->received_confirmed );
	}

	static function cw_order_by_received_unconfirmed( $a, $b ) {
		return strcmp( $a->received_unconfirmed, $b->received_unconfirmed );
	}

	static function cw_order_by_amount_due( $a, $b ) {
		return strcmp( $a->amount_due, $b->amount_due );
	}

	static function cw_order_by_crypto_amount( $a, $b ) {
		return strcmp( $a->crypto_amount, $b->crypto_amount );
	}

	static function cw_order_by_email( $a, $b ) {
		return strcmp( $a->_billing_email, $b->_billing_email );
	}

	static function cw_sort_orders( $cryptowoo_orders, $orderby, $sort ) {

		switch ( $orderby ) {
			default:
			case 'id':
				$function = 'cw_order_by_id';
				break;
			case 'created_at':
				$function = 'cw_order_by_date';
				break;
			case 'last_update':
				$function = 'cw_order_by_last_update';
				break;
			case 'received_confirmed':
				$function = 'cw_order_by_received_confirmed';
				break;
			case 'received_unconfirmed':
				$function = 'cw_order_by_received_unconfirmed';
				break;
			case 'amount_due':
				$function = 'cw_order_by_amount_due';
				break;
			case 'crypto_amount':
				$function = 'cw_order_by_crypto_amount';
				break;
			case 'email':
				$function = 'cw_order_by_email';
				break;
		}

		uasort( $cryptowoo_orders, "CW_OrderSorting::{$function}" );

		// Maybe reverse order
		if ( strcasecmp( $sort, 'ASC' ) ) {
			$cryptowoo_orders = array_reverse( $cryptowoo_orders );
		}
		return $cryptowoo_orders;
	}

	/**
	 * Sort unpaid addresses by average blocktime (DOGE > BLK > LTC > BTC)
	 * filter long unpaid orders that do not need to be updated
	 * and prepare address batches
	 *
	 * array(
	 *  'batches' =>
	 *        array(
	 *            'BTC' => array( 0 => 'ADDRESS1', 1 => 'ADDRESS2', 3 => 'ADDRESS3'),
	 *            'LTC' => array( 0 => ...
	 *
	 *        ),
	 *    'DOGE' => $unpaid_addresses_doge,
	 *  'LTC' => $unpaid_addresses_ltc,
	 *  'BTC' => $unpaid_addresses_btc
	 *  'BLK' => $unpaid_addresses_blk
	 *    );
	 *
	 * @param  $unpaid_addresses_raw
	 * @return mixed
	 */
	public static function sort_unpaid_addresses( $unpaid_addresses_raw ) {
		$address_batch = array();
		$top_n         = array( array(), array(), array(), array() );
		// batches,  [0] DOGE, [1] BLK, [2] LTC, [3] BTC

		// Order the items according to their currencies' average blocktime
		foreach ( $unpaid_addresses_raw as $address ) {
			$payment_currency = $address->get_payment_currency();
			if ( strcmp( $payment_currency, 'BTC' ) === 0 ) {
				$top_n[3]['BTC'][]      = $address;
				$address_batch['BTC'][] = $address->get_address();
			} elseif ( strcmp( $address->payment_currency, 'BCH' ) === 0 ) {
				$top_n[3]['BCH'][]      = $address;
				$address_batch['BCH'][] = $address->get_address();
			} elseif ( strcmp( $payment_currency, 'DOGE' ) === 0 ) {
				$top_n[0]['DOGE'][]      = $address;
				$address_batch['DOGE'][] = $address->get_address();
			} elseif ( strcmp( $payment_currency, 'LTC' ) === 0 ) {
				$top_n[2]['LTC'][]      = $address;
				$address_batch['LTC'][] = $address->get_address();
			} elseif ( strcmp( $payment_currency, 'BLK' ) === 0 ) {
				$top_n[1][ $payment_currency ][]      = $address;
				$address_batch[ $payment_currency ][] = $address->get_address();
			} elseif ( strcmp( $payment_currency, 'BTCTEST' ) === 0 ) {
				$top_n[2]['BTCTEST'][]      = $address;
				$address_batch['BTCTEST'][] = $address->get_address();
			} elseif ( strcmp( $payment_currency, 'DOGETEST' ) === 0 ) {
				$top_n[0]['DOGETEST'][]      = $address;
				$address_batch['DOGETEST'][] = $address->get_address();
			} else {
				$top_n         = apply_filters( 'cw_sort_unpaid_addresses', $top_n, $address );
				$address_batch = apply_filters( 'cw_filter_batch', $address_batch, $address );
			}
		}

		$unpaid_addresses = array_merge( array( 'batches' => $address_batch ), $top_n[0], $top_n[1], $top_n[2], $top_n[3] );

		return $unpaid_addresses;
	}

	/**
	 * Return max. $number of unpaid addresses sorted by average blocktime (DOGE > BLK > LTC > BTC)
	 *
	 * @param array $unpaid_addresses_raw Array of unpaid addresses.
	 * @param int   $max_length           Max number of unpaid addresses to return.
	 *
	 * @return mixed
	 */
	public static function prioritize_unpaid_addresses( $unpaid_addresses_raw, $max_length = 10 ) {
		if ( empty( $unpaid_addresses_raw ) ) {
			return $unpaid_addresses_raw;
		}

		// We will order priority by currencies block time in milliseconds. Let addons add other currencies.
		$currencies_blocktime_ms = apply_filters(
			'cw_currency_blocktime_milliseconds',
			array(
				'BTC'      => 600000,
				'BTCTEST'  => 600000,
				'BCH'      => 600000,
				'LTC'      => 150000,
				'LTCTEST'  => 150000,
				'BLK'      => 64000,
				'DOGE'     => 60000,
				'DOGETEST' => 60000,
			)
		);
		asort( $currencies_blocktime_ms );

		// Add any missing cryptocurrencies (for backwards compatibility). TODO: Remove when no more old addon versions.
		foreach ( $unpaid_addresses_raw as $address ) {
			if ( ! isset( $currencies_blocktime_ms[ $address->payment_currency ] ) ) {
				$currencies_blocktime_ms[ $address->payment_currency ] = 0;
			}
		}

		// This gives us an array with index 0 and up and currency string as value.
		$currencies_sorted = array_keys( $currencies_blocktime_ms );

		// Order the items according to their currencies' average blocktime.
		foreach ( $unpaid_addresses_raw as $address ) {
			$payment_currency = $address->payment_currency;
			isset( ${"currency_pos_$payment_currency"} ) ?: ${"currency_pos_$payment_currency"} = array_search( $payment_currency, $currencies_sorted, true );

			$top_n[ ${"currency_pos_$payment_currency"} ][] = $address;
		}

		// Sort the individual orders per currency by last_update to update the oldest data first.
		foreach ( $top_n as $n => $currency_orders ) {
			$top_n_sorted[ $n ] = self::cw_sort_orders( $currency_orders, 'last_update', 'ASC' );
		}

		// No longer needed to sort by order so we will merge togethere the arrays of orders data.
		$result = array_merge( ...$top_n_sorted );

		// Return up to max length (max number of results) as specified by block explorer api.
		$unpaid_addresses = array_slice( $result, 0, $max_length ?: null );

		return $unpaid_addresses;
	}

	/**
	 * Low Frequency Update Interval filter
	 * Update addresses less frequently if they are unpaid for a longer time
	 *
	 * @param CW_Payment_Details_Object $unpaid_addresses CryptoWoo payment details object for the unpaid addresses.
	 *
	 * @return CW_Payment_Details_Object
	 */
	public static function filter_long_unpaid_addresses( $unpaid_addresses ) {
		$filtered_addresses = array();

		$long_unpaid_threshold_hours       = cw_get_option( 'long_unpaid_threshold_hr' ) ?: 3;
		$long_unpaid_threshold             = 60 * 60 * $long_unpaid_threshold_hours;
		$long_unpaid_update_interval_hours = cw_get_option( 'long_unpaid_update_interval_hr' ) ?: 3;
		$long_unpaid_update_interval       = 60 * 60 * $long_unpaid_update_interval_hours; // Only update every x hours
		$oldest_last_update                = 0;
		$most_outdated_order               = array();
		// Order the items according to their currencies' average blocktime
		// Maybe exclude orders that run on Low Frequency Update Interval
		foreach ( $unpaid_addresses as $address ) {
			// Orders that are open longer than x hours will only be checked
			// if they have not been updated for at least y hours
			$order_age   = time() - $address->get_created_at();
			$last_update = time() - $address->get_last_update();

			// Check if the order is still time-sensitive
			$order_still_young = $order_age <= $long_unpaid_threshold;

			// Check if the extended time has passed
			$time_to_update = $last_update >= $long_unpaid_update_interval;

			// Make sure we include the order if it would expire in the next interval
			$times_out_in   = (int) $address->get_timeout_timestamp() - time();
			$times_out_soon = $times_out_in <= $long_unpaid_update_interval;

			// Keep the order that has not been updated the longest time in case the filter returns zero
			if ( $last_update >= $oldest_last_update ) {
				$most_outdated_order = $address->get_current_row();
				$oldest_last_update  = $last_update;
			}

			if ( $order_still_young || $time_to_update || $times_out_soon ) {
				$filtered_addresses[] = $address->get_current_row();
			} else {
				$data = sprintf( 'Skipping %1$s - order age: %2$s hours - last update %3$s min ago - expires in %4$s min', substr( $address->get_order_id(), 0, 5 ), $order_age / 60 / 60, $last_update / 60, $times_out_in / 60 );
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $data );
			}
		}
		// If we filtered all addresses
		// use the order that wasn't updated the longest time
		if ( $most_outdated_order && ! count( $filtered_addresses ) ) {
			$filtered_addresses = array( $most_outdated_order );
			$data               = sprintf( 'Low Frequency Update Interval filter returned zero - Using longest unpaid order %s before we end up doing nothing.', $most_outdated_order->order_id );
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $data );
		}

		return new CW_Payment_Details_Object( $filtered_addresses );
	}

	public static function order_mockup( $order_id, $age_sec, $last_update_sec, $time_left_sec ) {

		$currencies                = array( 'BTC', 'LTC', 'DOGE' );
		$currency                  = $currencies[0]; // $currencies[rand(0, count($currencies)-1 )];
		$address                   = new stdClass();
		$address->order_id         = $order_id;
		$address->created_at       = gmdate( 'Y-m-d H:i:s', time() - $age_sec );
		$address->last_update      = gmdate( 'Y-m-d H:i:s', time() - $last_update_sec );
		$address->payment_currency = $currency;
		$address->timeout_value    = time() + $time_left_sec;
		$address->address          = $currency . uniqid();
		return $address;
	}


	public static function create_test_orders() {
		$age_sec              = 60 * 60 * 73; // order age seconds, if younger than 72 it will pass the filter
		$last_update_sec      = 60 * 60 * 1; // hours since last update, if lower than 1 it will not pass the filter
		$time_left_sec        = 60 * 60 * 2; // seconds left to expiry, if lower than 1 it will pass the filter
		$unpaid_addresses_raw = array(
			self::order_mockup( 1, $age_sec, $last_update_sec - 1, $time_left_sec ), // filtered
			self::order_mockup( 2, 60 * 60 * 20, 1, $time_left_sec ),            // NOT filtered - Too young to consider - order age 20 hours
			self::order_mockup( 3, $age_sec, 30, $time_left_sec ),                   // filtered - last update <1hr ago
			self::order_mockup( 4, 60, 60, $time_left_sec ),                 // NOT filtered - Too young and too fresh
			self::order_mockup( 5, $age_sec, 1, 2 ),                     // NOT filtered - expires soon
			self::order_mockup( 6, $age_sec, $last_update_sec, 1 ),                    // NOT filtered - expires soon

		);
		echo 'Expecting the filter to keep 2 4 5 6 and skip 1 and 3' . PHP_EOL;
		return $unpaid_addresses_raw;
	}

	// Run test with:
	// CW_OrderSorting::test_long_unpaid_filter()
	public static function test_long_unpaid_filter() {
		$test_orders = self::create_test_orders();
		// var_export($test_orders);
		$queued_orders = self::filter_long_unpaid_addresses( $test_orders );

		return array(
			'unfiltered' => $test_orders,
			'filtered'   => $queued_orders,
		);

	}
}
