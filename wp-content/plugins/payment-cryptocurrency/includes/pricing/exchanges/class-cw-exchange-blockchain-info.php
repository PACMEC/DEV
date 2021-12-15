<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Blockchain_Info Exchange Rates Class
 *
 * @category   CryptoWoo
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoWoo AS
 * Author URI: https://cryptowoo.com
 */
class CW_Exchange_Blockchain_Info extends CW_Exchange_Base {


	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		if ( ! empty( cw_get_option( 'bc_info_tor' ) ) ) {
			return 'https://blockchainbdgpzk.onion/ticker';
		}

		return 'https://blockchain.info/ticker';
	}

	/**
	 *
	 * Get the exchange API proxy
	 *
	 * @return string
	 */
	protected function get_proxy() : string {
		$bc_info_tor = cw_get_option( 'bc_info_tor' );

		return $bc_info_tor ?: parent::get_proxy();
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
		$base_currency = $this->get_base_currency_name();
		if ( isset( $price_data->$base_currency ) && $price_data->$base_currency instanceof stdClass ) {
			return $price_data->$base_currency;
		}

		return $price_data;
	}
}
