<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * CoinDesk Exchange Rates Class
 *
 * @category   CryptoWoo
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoWoo AS
 * Author URI: https://cryptowoo.com
 */
class CW_Exchange_CoinDesk extends CW_Exchange_Base {


	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		return 'http://api.coindesk.com/v1/bpi/currentprice/%s.json';
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_pair_format() : string {
		return '%1$s';
	}

	/**
	 *
	 * Get the exchange price index (last index)
	 *
	 * @return string
	 */
	protected function get_exchange_price_index() : string {
		return 'rate_float';
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
		$currency = $this->get_base_currency_name();
		if ( isset( $price_data->bpi->$currency ) && $price_data->bpi->$currency instanceof stdClass ) {
			$formatted_price_data = $price_data->bpi->$currency;

			if ( isset( $price_data->time->updatedISO ) ) {
				$formatted_price_data->timestamp = $this->convert_iso_to_timestamp( $price_data->time->updatedISO, 'Y-m-d\TH:i:s+P' );
			}

			return $formatted_price_data;
		}

		return $price_data;
	}
}
