<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * DogecoinAverage Exchange Rates Class
 *
 * @category   CryptoWoo
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoWoo AS
 * Author URI: https://cryptowoo.com
 */
class CW_Exchange_DogecoinAverage extends CW_Exchange_Base {


	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		return 'http://dogecoinaverage.com/BTC.json';
	}

	/**
	 *
	 * Get the exchange price index (last index)
	 *
	 * @return string
	 */
	protected function get_exchange_price_index() : string {
		return 'vwap';
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_pair_format() : string {
		return '';
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
		if ( isset( $price_data->date ) ) {
			/**

	   * Note: Bug causes brackets in DateTime:createFromFormat not to work.
			 * Removing closing bracket fixes it: '(e' instead of '(e)'.
			 * See: https://stackoverflow.com/a/38012718.
			 */
			$price_data->timestamp = $this->convert_iso_to_timestamp( $price_data->date, 'jS F Y\, H:i (e' );
		}

		return $price_data;
	}
}
