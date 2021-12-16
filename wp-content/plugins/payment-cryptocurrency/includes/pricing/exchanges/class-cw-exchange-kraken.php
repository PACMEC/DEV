<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Kraken Exchange Rates Class
 *
 * @category   CryptoPay
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoPay AS
 * Author URI: https://cryptopay.com
 */
class CW_Exchange_Kraken extends CW_Exchange_Base {


	/**
	 *
	 * Is the search currency XBT instead of BTC in search pair?
	 * Default is false (the search pair is BTC).
	 *
	 * @return bool
	 */
	protected function search_pair_btc_is_xbt() : bool {
		return true;
	}

	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		return 'https://api.kraken.com/0/public/Ticker?pair=%s';
	}

	/**
	 *
	 * Get the exchange price index (last index)
	 *
	 * @return string
	 */
	protected function get_exchange_price_index() : string {
		return '0';
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_pair_format() : string {
		return 'X%2$sZ%1$s';
	}

	/**
	 *
	 * Is the exchange rate search pair uppercase or lowercase in the api url?
	 *
	 * @return bool
	 */
	protected function search_pair_is_uppercase() : bool {
		return true;
	}

	/**
	 *
	 * Format the price data from exchange result to default data format
	 *
	 * @param stdClass $price_data Json decoded result from exchange api call.
	 *
	 * @return stdClass
	 */
	protected function format_price_data_from_exchange( stdClass $price_data ) : stdClass {
		$search_pair = $this->get_search_pair();
		if ( isset( $price_data->result->$search_pair->c ) && $price_data->result->$search_pair instanceof stdClass ) {
			$data = $price_data->result->$search_pair->c;
			if ( is_array( $data ) && 2 === count( $data ) && isset( $data[0] ) ) {
				$data = (object) $data;

				return $data;
			}
		}

		return $price_data;
	}
}
