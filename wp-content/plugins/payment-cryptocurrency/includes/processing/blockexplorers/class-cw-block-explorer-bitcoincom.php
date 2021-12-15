<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Bitcoin.com Insight Block Explorer API Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
class CW_Block_Explorer_BitcoinCom extends CW_Block_Explorer_API_Insight {


	/**
	 *
	 * Get the block explorer name in nice format.
	 *
	 * @return string
	 */
	public function get_nicename() : string {
		return 'Bitcoin.com';
	}

	/**
	 *
	 * Get the block explorer API URL with format
	 *
	 * @return string
	 */
	protected function get_base_url() : string {
		if ( 'TESTBCH' === $this->get_currency_name() ) {
			return 'https://explorer-tbch.api.bitcoin.com/tbch/v1/';
		}

		$lc_currency = strtolower( $this->get_currency_name() );

		return "https://explorer.api.bitcoin.com/{$lc_currency}/v1/";
	}

	/**
	 *
	 * Get the block explorer supported currencies
	 *
	 * @return string[]
	 */
	protected function get_supported_currencies(): array {
		return array( 'BTC', 'BCH', 'TESTBCH' );
	}
}
