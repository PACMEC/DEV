<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}// Exit if accessed directly

class CW_TrezorConnect {


	/**
	 * Check if a coin is supported by Trezor
	 *
	 * @param $currency string The cryptocurrency symbol, e.g. BTC
	 *
	 * @return bool
	 */
	static function coin_is_supported( $currency ) {
		return in_array( strtoupper( $currency ), self::get_supported_coins() );
	}

	/**
	 * Return connect button HTML
	 *
	 * @param $currency string The cryptocurrency symbol, e.g. BTC
	 *
	 * @return string
	 */
	static function get_connect_button( $currency ) {
		$html = '';
		if ( self::coin_is_supported( $currency ) ) {
			$style = 'display: none; padding: 0.2em; border: 1px solid black;border-radius: 2px;';
			$html  = '<br><div class="trezor-connect-log" id="trezor-connect-log-%1$s"%2$s></div><div class="button" id="cwhd-connect-trezor-%1$s" style="color: black; border-color: black;"><i class="cw-coin-trezor-logo"></i></div>';
			$html  = sprintf( $html, strtolower( $currency ), $style );
		}

		return $html;
	}

	/**
	 * Return pay button html
	 *
	 * @param $currency string The cryptocurrency symbol, e.g. BTC
	 *
	 * @return string
	 */
	static function get_pay_button( $currency ) {

		$html = '';
		if ( self::coin_is_supported( $currency ) ) {
			$style = 'display: none; padding: 0.2em; border: 1px solid black;border-radius: 2px;';
			$html  = '<div class="trezor-connect-log" id="trezor-connect-log-%1$s"%2$s></div><div class="button" id="cwhd-connect-trezor-%1$s" style="color: black; border-color: black;"><i class="cw-coin-trezor-logo"></i></div>';
			$html  = sprintf( $html, strtolower( $currency ), $style );
		}

		return $html;
	}

	/**
	 * Echo pay button
	 *
	 * @param $currency
	 */
	static function print_pay_button( $currency ) {
		echo wp_kses_post( self::get_pay_button( $currency ) );
	}

	/**
	 * Trezor supported currencies
	 * according to https://github.com/trezor/connect/blob/develop/src/data/coins.json
	 *
	 * @return string[]
	 */
	static function get_supported_coins() {
		return array(
			'BTC',
			'BTCTEST',
			'ACM',
			'AXE',
			'ZNY',
			'BCH',
			'TBCH',
			'BTG',
			'TBTG',
			'BTCP',
			'XRC',
			'BTX',
			'DASH',
			'tDASH',
			'DGB',
			'DOGE',
			'FTC',
			'FLO',
			'FJC',
			'GIN',
			'GAME',
			'KMD',
			'KOTO',
			'LTC',
			'tLTC',
			'MONA',
			'MUE',
			'NIX',
			'NMC',
			'PIVX',
			'tPIVX',
			'PPC',
			'tPPC',
			'PTC',
			'POLIS',
			'XPM',
			'RVN',
			'RITO',
			'XSN',
			'UNO',
			'XVG',
			'VTC',
			'VIA',
			'ZCR',
			'ZEC',
			'TAZ',
			'XZC',
			'tXZC',
			'ETH',
			'EXP',
			'tROP',
			'tRIN',
			'UBQ',
			'ETSC',
			'RBTC',
			'tRBTC',
			'tKOV',
			'GO',
			'ETC',
			'tETC',
			'ELLA',
			'MIX',
			'ERE',
			'CLO',
			'ATH',
			'EGEM',
			'EOSC',
			'REOSC',
			'ESN',
			'TEO',
			'AKA',
			'ETHO',
			'MUSIC',
			'PIRL',
		);
	}

}
