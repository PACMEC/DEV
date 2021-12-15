<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Block.io Block Explorer API Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
class CW_Block_Explorer_BlockIO extends CW_Block_Explorer_API_BlockIO {


	/**
	 *
	 * Get the block explorer API URL with format
	 *
	 * @return string
	 */
	protected function get_base_url() : string {
		return 'https://block.io/api/v2/';
	}

	/**
	 *
	 * Get the block explorer supported currencies
	 *
	 * @return string[]
	 */
	public function get_supported_currencies(): array {
		return array( 'BTC', 'DOGE', 'LTC', 'BTCTEST', 'DOGETEST', 'LTCTEST' );
	}
}
