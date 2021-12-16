<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Litecore Insight Block Explorer API Class
 *
 * @category   CryptoPay
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoPay AS
 */
class CW_Block_Explorer_Litecore extends CW_Block_Explorer_API_Insight {


	/**
	 *
	 * Get the block explorer API URL with format
	 *
	 * @return string
	 */
	protected function get_base_url() : string {
		return 'https://insight.litecore.io/api/';
	}

	/**
	 *
	 * Get the block explorer supported currencies
	 *
	 * @return string[]
	 */
	protected function get_supported_currencies(): array {
		return array( 'LTC' );
	}
}
