<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * BitPay Insight Block Explorer API Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
class CW_Block_Explorer_Smartbit extends CW_Block_Explorer_API_Smartbit {


	/**
	 *
	 * Get the block explorer API URL with format
	 *
	 * @return string
	 */
	protected function get_base_url() : string {
		if ( 'BTC' === $this->get_currency_name() ) {
			$base_api_url_prefix = 'api';
		} elseif ( 'BTCTEST' === $this->get_currency_name() ) {
			$base_api_url_prefix = 'testnet-api';
		}

		return "https://$base_api_url_prefix.smartbit.com.au/v1/blockchain/";
	}

	/**
	 *
	 * Get the block explorer supported currencies
	 *
	 * @return string[]
	 */
	protected function get_supported_currencies(): array {
		return array( 'BTC', 'BTCTEST' );
	}
}
