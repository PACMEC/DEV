<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Dash.org Insight Block Explorer API Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
class CW_Block_Explorer_DashOrg extends CW_Block_Explorer_API_Insight {


	/**
	 *
	 * Get the block explorer name in nice format.
	 *
	 * @return string
	 */
	public function get_nicename() : string {
		return 'Dash.org';
	}

	/**
	 *
	 * Get the block explorer API URL with format
	 *
	 * @return string
	 */
	protected function get_base_url() : string {
		return 'https://explorer.dash.org/insight-api/';
	}

	/**
	 *
	 * Get the block explorer supported currencies
	 *
	 * @return string[]
	 */
	protected function get_supported_currencies(): array {
		return array( 'DASH' );
	}

	/**
	 *
	 * Get the block explorer tx instant key name.
	 * Default is no tx instant key available (empty string).
	 *
	 * @return string
	 */
	protected function get_tx_instant_key_name() : string {
		return 'txlock';
	}

}
