<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Get the exchange rates from supported exchanges:
 *
 * - Bitcoinaverage.com
 * - BTC-e.com
 * - Bitfinex.com
 * - BitPay.com
 * - Block.io (authenticated only)
 * - SoChain
 * - Dogecoinaverage.com
 * - Poloniex.com
 * - Shapeshift.com
 * - Bittrex.com
 * - Bitcoincharts
 * - Coinbase (pro.coinbase.com)
 * - Bitstamp.com
 * - Blockchain.info
 * - Coindesk BPI
 * - Luno.com
 * - OKCoin.com
 * - OKCoin.cn
 * - Kraken
 * - Livecoin.net
 *
 * Some functions modified from "Bitcoin Payments for WooCommerce"
 * Author: BitcoinWay
 * URI: http://www.bitcoinway.com/
 *
 * @category   CryptoPay
 * @package    CryptoPay
 * @subpackage ExchangeRates
 * @author     DRDoGE
 * @todo       refactor: Remove backward compatibility
 */
class CW_ExchangeRates {

	/**
	 *
	 * Exchange rate processing instance
	 *
	 * @var CW_ExchangeRate_Processing The exchange rates processing object.
	 */
	private static $processing;
	/**
	 *
	 * Exchange rate tools instance
	 *
	 * @var CW_ExchangeRate_Tools The exchange rates tools object.
	 */
	private static $tools;

	/**
	 *
	 * Is triggered when invoking inaccessible methods in a static context.
	 *
	 * @param string $name      the function name.
	 * @param array  $arguments the arguments.
	 *
	 * @return mixed
	 * @link   https://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
	 *
	 * TODO: This is for backward compatibility, remove this when no more usage!
	 */
	public static function __callStatic( $name, $arguments ) {
		$foo = self::processing();
		return call_user_func_array( array( $foo, $name ), $arguments );
	}

	/**
	 *
	 * Get the exchange rate processing object
	 *
	 * @param null|CW_ExchangeRate_Processing $obj Set the object (For unit testing).
	 *
	 * @return CW_ExchangeRate_Processing
	 */
	public static function processing( $obj = null ) {
		if ( $obj ) {
			self::$processing = $obj;
		} elseif ( ! self::$processing ) {
			self::$processing = new CW_ExchangeRate_Processing();
		}

		return self::$processing;
	}

	/**
	 *
	 * Get the exchange rate tools object
	 *
	 * @param null|CW_ExchangeRate_Tools $obj Set the object (For unit testing).
	 *
	 * @return CW_ExchangeRate_Tools
	 */
	public static function tools( $obj = null ) {
		if ( $obj ) {
			self::$tools = $obj;
		} elseif ( ! self::$tools ) {
			self::$tools = new CW_ExchangeRate_Tools();
		}

		return self::$tools;
	}

} //End class CW_ExchangeRates
