<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Vertcoin.org Insight Block Explorer API Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
class CW_Block_Explorer_VertcoinOrg extends CW_Block_Explorer_API_Insight {


	/**
	 *
	 * Get the block explorer name in nice format.
	 *
	 * @return string
	 */
	public function get_nicename() : string {
		return 'Vertcoin.org';
	}

	/**
	 *
	 * Get the block explorer API URL with format
	 *
	 * @return string
	 */
	protected function get_base_url() : string {
		return 'https://insight.vertcoin.org/insight-vtc-api/';
	}

	/**
	 *
	 * Get the block explorer supported currencies
	 *
	 * @return string[]
	 */
	protected function get_supported_currencies(): array {
		return array( 'VTC' );
	}

	/**
	 *
	 * Get the required indexes (key names) from the block explorer result txs data.
	 *
	 * @return array
	 */
	protected function get_required_txs_keys() : array {
		$required_keys = parent::get_required_txs_keys();

		// Remove the block height requirement because it does not exist before tx is confirmed.
		unset( $required_keys['height'] );

		return $required_keys;
	}


}
