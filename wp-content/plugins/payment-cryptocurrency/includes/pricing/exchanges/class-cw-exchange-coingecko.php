<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * CoinGecko Exchange Rates Class
 *
 * @category   CryptoWoo
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoWoo AS
 * Author URI: https://cryptowoo.com
 */
class CW_Exchange_CoinGecko extends CW_Exchange_Base {


	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		return 'https://api.coingecko.com/api/v3/simple/price?include_last_updated_at=true&%s';
	}

	/**
	 *
	 * Get the exchange price index (last index)
	 *
	 * @return string
	 */
	protected function get_exchange_price_index() : string {
		return strtolower( $this->get_base_currency_name() );
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_pair_format() : string {
		return 'ids=%2$s&vs_currencies=%1$s';
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
		return array();
	}

	/**
	 *
	 * Create and return search currency ID for exchange API (for exchanges that use ID instead of currency code)
	 * This function is used by cryptocurrency addons to add their search currency id. If it has data use it.
	 * Default is false because most exchanges does not use ids but currency code.
	 *
	 * @return string|int|false
	 */
	protected function get_search_currency_id() {
		// TODO: Swap to cw_get_cryptocurrencies when cw_get_woocommerce_currencies() is removed.
		$currency_name = cw_get_woocommerce_currencies() [ $this->get_currency_name() ];

		return strtolower( str_replace( ' ', '-', $currency_name ) );
	}

	/**
	 *
	 * Get the timestamp in the exchange data result
	 * Or generate timestamp if none exist
	 *
	 * @param stdClass $price_data Json decoded result from exchange api call.
	 *
	 * @return string
	 */
	protected function get_timestamp_from_price_data( stdClass $price_data ) : string {
		if ( isset( $price_data->last_updated_at ) ) {
			return $price_data->last_updated_at;
		} else {
			return parent::get_timestamp_from_price_data( $price_data );
		}
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
		$base_currency = $this->get_search_currency_id();
		if ( isset( $price_data->$base_currency ) && $price_data->$base_currency instanceof stdClass ) {
			return $price_data->$base_currency;
		}

		return $price_data;
	}
}
