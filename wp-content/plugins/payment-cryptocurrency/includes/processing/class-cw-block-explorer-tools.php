<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * CryptoWoo Block Explorer tools
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage Processing
 * @author     CryptoWoo AS
 */
class CW_Block_Explorer_Tools {


	/**
	 *
	 * Get the preferred block explorer for a coin.
	 *
	 * @param string $currency The coin.
	 *
	 * @return mixed
	 */
	public function get_preferred_block_explorer( $currency ) {
		$preferred_block_explorer_key = sprintf( 'processing_api_%s', strtolower( str_replace( 'TEST', '', $currency ) ) );

		return cw_get_option( $preferred_block_explorer_key );
	}

	/**
	 *
	 * Get the preferred block explorer for a coin.
	 *
	 * @param string $currency The coin.
	 *
	 * @return mixed
	 */
	public function get_preferred_block_explorer_link( $currency ) {
		$preferred_block_explorer_key = sprintf( 'preferred_block_explorer_%s', strtolower( $currency ) );
		$preferred_block_explorer     = cw_get_option( $preferred_block_explorer_key );

		if ( 'autoselect' !== $preferred_block_explorer ) {
			return $preferred_block_explorer;
		}

		$processing_api = $this->get_preferred_block_explorer( $currency );

		// Block.io processing API uses SoChain for links.
		$force_chain = array( 'blockio', 'sochain' );

		// DOGETEST mandates SoChain.
		$to_use = ! in_array( $processing_api, $force_chain, true ) && 'DOGETEST' !== $currency ? $processing_api : 'sochain';

		// Get the current processing API when auto select is selected.
		return $to_use;
	}

	/**
	 * Get all block explorer options for a specific currency (Redux)
	 *
	 * @param string $currency The currency to get block explorer options for.
	 *
	 * @return array
	 */
	public function get_preferred_block_explorers_options( string $currency ) {
		$preferred_api_field_id = 'processing_api_' . strtolower( str_replace( 'TEST', '', $currency ) );
		$preferred_api_options  = Redux::get_field( 'cryptowoo_payments', $preferred_api_field_id )['options'];

		// Remove disabled and custom index as it is not a pre defined block explorer api.
		unset( $preferred_api_options['disabled'] );
		unset( $preferred_api_options['custom'] );
		unset( $preferred_api_options['custom_esplora'] );

		return $preferred_api_options ?: array();
	}

	/**
	 * Get all block explorer names for a specific currency (Redux)
	 *
	 * @param string $currency The currency to get block explorer options for.
	 *
	 * @return array
	 */
	public function get_preferred_block_explorers_names( string $currency ) {
		return array_keys( $this->get_preferred_block_explorers_options( $currency ) );
	}

	/**
	 *
	 * Get an block explorer class instance by the block explorer id (index).
	 *
	 * @param string      $block_explorer_id The block explorer id (index).
	 * @param string|null $coin_type         The type of coin (f.ex BTC).
	 * @param array|false $addresses         address(es) to check.
	 *
	 * @return CW_Block_Explorer_Base|null
	 * @throws InvalidArgumentException Throws exception if unsupported currency is supplied.
	 */
	public function get_block_explorer_instance_by_id( string $block_explorer_id, string $coin_type, $addresses = false ) {
		$class_name = $this->get_class_name_from_block_explorer_name( $block_explorer_id );

		return $this->get_block_explorer_instance_by_name( $class_name, $coin_type, $addresses );
	}

	/**
	 *
	 * Get an block explorer class instance by the block explorer class name.
	 *
	 * @param string      $block_explorer_class_name The block explorer class name.
	 * @param string|null $coin_type                 The type of coin (f.ex BTC).
	 * @param array|false $addresses                 address(es) to check.
	 *
	 * @return CW_Block_Explorer_Base|null
	 * @throws InvalidArgumentException Throws exception if unsupported currency is supplied.
	 */
	public function get_block_explorer_instance_by_name( string $block_explorer_class_name, string $coin_type, $addresses = false ) {
		// This lets CryptoWoo Add-ons override block explorer class for a specific cryptocurrency.
		$block_explorer_class_name = apply_filters( 'cw_block_explorer_class_name', $block_explorer_class_name, $coin_type );
		if ( class_exists( $block_explorer_class_name, false ) ) {
			return new $block_explorer_class_name( $coin_type, $addresses );
		}

		return class_exists( $block_explorer_class_name ) ? new $block_explorer_class_name( $coin_type, $addresses ) : null;
	}

	/**
	 * Get the class name from _block explorerid
	 *
	 * @param string $block_explorer_id the block explorer id.
	 *
	 * @return string
	 */
	public function get_class_name_from_block_explorer_name( $block_explorer_id ) {
		$block_explorer_name = str_replace( '-fallback', '', $block_explorer_id );

		return "CW_Block_Explorer_$block_explorer_name";
	}
}
