<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * BitcoinAverage Exchange Rates Class
 *
 * @category   CryptoWoo
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoWoo AS
 * Author URI: https://cryptowoo.com
 */
class CW_Exchange_BitcoinAverage extends CW_Exchange_Base {


	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		return 'https://apiv2.bitcoinaverage.com/indices/global/ticker/short?%s';
	}

	/**
	 *
	 * Get the exchange price index (last index)
	 *
	 * @return string
	 */
	protected function get_exchange_price_index() : string {
		return 'last';
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_pair_format() : string {
		return 'crypto=%2$s&fiats=%1$s';
	}

	/**
	 *
	 * Get timeout for exchange API call
	 *
	 * @return int
	 */
	protected function get_timeout() : int {
		return 30;
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
		$pair = $this->get_currency_name() . $this->get_base_currency_name();
		if ( isset( $price_data->$pair ) && $price_data->$pair instanceof stdClass ) {
			return $price_data->$pair;
		}

		return $price_data;
	}
}
