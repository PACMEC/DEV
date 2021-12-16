<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * OkCoin.cn Exchange Rates Class
 *
 * @category   CryptoPay
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoPay AS
 * Author URI: https://cryptopay.com
 */
class CW_Exchange_OkCoin_Cn extends CW_Exchange_OkCoin {


	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		return 'https://www.okcoin.cn/api/v1/ticker.do?symbol=%s';
	}
}
