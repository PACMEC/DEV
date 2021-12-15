<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Luno Exchange Rates Class
 *
 * @category   CryptoWoo
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoWoo AS
 * Author URI: https://cryptowoo.com
 */
class CW_Exchange_Luno extends CW_Exchange_Base {


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
	 * Is the exchange rate search pair uppercase or lowercase in the api url?
	 * Default is lower case.
	 *
	 * @return bool
	 */
	protected function search_pair_is_uppercase() : bool {
		return true;
	}

	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		return 'https://api.mybitx.com/api/1/ticker?pair=%s';
	}

	/**
	 *
	 * Get the exchange price index (last index)
	 *
	 * @return string
	 */
	protected function get_exchange_price_index() : string {
		return 'last_trade';
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_pair_format() : string {
		return '%2$s%1$s';
	}

	/**
	 *
	 * Get the exchange rate pair (base/currency)
	 *
	 * @return string
	 */
	protected function get_search_pair() : string {
		return str_replace( 'BTC', 'XBT', parent::get_search_pair() );
	}
}
