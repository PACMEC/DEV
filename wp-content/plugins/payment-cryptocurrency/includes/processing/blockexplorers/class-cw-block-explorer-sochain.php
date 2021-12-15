<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * BlockCypher Block Explorer API Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
class CW_Block_Explorer_SoChain extends CW_Block_Explorer_API_SoChain {


	/**
	 *
	 * Get the block explorer API URL with format
	 *
	 * @return string
	 */
	protected function get_base_url() : string {
		return 'https://sochain.com/api/v2/';
	}

	/**
	 *
	 * Get the block explorer supported currencies
	 *
	 * @return string[]
	 */
	public function get_supported_currencies(): array {
		return array( 'BTC', 'DASH', 'ZEC', 'DOGE', 'LTC', 'BTCTEST', 'DASHTEST', 'ZECTEST', 'DOGETEST', 'LTCTEST' );
	}
}
