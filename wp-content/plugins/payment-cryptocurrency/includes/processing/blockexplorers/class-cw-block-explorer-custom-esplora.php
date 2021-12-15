<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Custom Esplora Block Explorer API Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
class CW_Block_Explorer_Custom_Esplora extends CW_Block_Explorer_API_Esplora {


	/**
	 *
	 * Get the block explorer API URL with format
	 *
	 * @return string
	 */
	protected function get_base_url() : string {
		$lc_currency = strtolower( str_replace( 'TEST', '', $this->get_currency_name() ) );
		$base_url    = cw_get_option( "custom_esplora_api_$lc_currency" );

		return sprintf( $base_url, $this->get_network_endpoint() );
	}

	/**
	 *
	 * Override base URL (custom URL).
	 * Set before the custom base URL has been stored in CryptoWoo options or to override the url.
	 *
	 * @param string $custom_api_url The custom URL to use for order processing.
	 *
	 * @return $this
	 */
	public function override_base_url( $custom_api_url ) {
		parent::override_base_url( sprintf( $custom_api_url, $this->get_network_endpoint() ) );

		return $this;
	}

	/**
	 *
	 * Get the block explorer supported currencies
	 *
	 * @return string[]
	 */
	protected function get_supported_currencies(): array {
		return array( $this->get_currency_name() );
	}
}
