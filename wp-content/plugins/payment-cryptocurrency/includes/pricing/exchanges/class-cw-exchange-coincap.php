<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * CoinCap Exchange Rates Class
 *
 * @category   CryptoWoo
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoWoo AS
 * Author URI: https://cryptowoo.com
 */
class CW_Exchange_CoinCap extends CW_Exchange_Base {


	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		return 'https://api.coincap.io/v2/assets/%s';
	}

	/**
	 *
	 * Get the exchange price index (last index)
	 *
	 * @return string
	 */
	protected function get_exchange_price_index() : string {
		return 'priceUsd';
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_pair_format() : string {
		return '%2$s';
	}

	/**
*
 * Create and return search currency ID for exchange API (for exchanges that use ID instead of currency code)
	 * Default is empty array because most exchanges does not use ids but currency code.
	 * Note that this function should be set to final in exchange classes if used.
	 *
	 * @return array
	 */
	final protected function get_search_currency_ids() {
		return array( 'BTC' => 'bitcoin' );
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
		if ( isset( $price_data->data ) && $price_data->data instanceof stdClass ) {
			if ( isset( $price_data->timestamp ) ) {
				$price_data->data->timestamp = $price_data->timestamp;
			}

			return $price_data->data;
		}

		return $price_data;
	}
}
