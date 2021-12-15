<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Custom ERC-20 Exchange Rates Class
 *
 * @category   CryptoWoo
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoWoo AS
 * Author URI: https://cryptowoo.com
 */
class CW_Exchange_ERC20Custom extends CW_Exchange_Base {


	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		return apply_filters( 'cw_erc20rates_url_format', 'https://example.com/api/rates/%susdt.json' );
	}

	/**
	 *
	 * Get the exchange price index (last index)
	 *
	 * @return string
	 */
	protected function get_exchange_price_index() : string {
		return apply_filters( 'cw_erc20rates_price_index', 'price_usd' );
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_pair_format() : string {
		return apply_filters( 'cw_erc20rates_pair_format', '%2$s/%1$s' );
	}

	/**
	 *
	 * Get timeout for exchange API call
	 *
	 * @return int
	 */
	protected function get_timeout() : int {
		return apply_filters( 'cw_erc20rates_timeout', 60 );
	}

	/**
	 *
	 * Custom ERC20 return token/USD price
	 *
	 * @return string
	 */
	protected function get_base_currency_name() : string {
		return 'USD';
	}
}
