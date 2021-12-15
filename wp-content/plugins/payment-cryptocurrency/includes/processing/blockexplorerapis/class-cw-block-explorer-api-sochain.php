<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * SoChain Block Explorer API Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
abstract class CW_Block_Explorer_API_SoChain extends CW_Block_Explorer_Base {


	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_txs_endpoint_format() : string {
		return 'get_tx_received/%1$s/%2$s';
	}

	/**
	 *
	 * Get the block explorer api block height endpoint format
	 *
	 * @return string
	 */
	protected function get_block_height_endpoint_format() : string {
		return 'get_info/%1$s';
	}

	/**
	 *
	 * Get the block explorer block hash api endpoint format
	 *
	 * @return string
	 */
	protected function get_block_hash_endpoint_format() : string {
		return 'get_blockhash/%1$s/%2$d';
	}

	/**
	 *
	 * Get the block explorer txs key name
	 *
	 * @return string
	 */
	protected function get_txs_key_name() : string {
		return 'txs';
	}

	/**
	 *
	 * Get the block explorer block height key name
	 *
	 * @return string
	 */
	protected function get_block_height_key_name() : string {
		return 'blocks';
	}

	/**
	 *
	 * Get the getInfo key name
	 *
	 * @return string
	 */
	protected function get_getinfo_key_name() : string {
		return 'data';
	}

	/**
	 *
	 * Get the block explorer block hash key name
	 *
	 * @return string
	 */
	protected function get_block_hash_key_name() : string {
		return 'blockhash';
	}

	/**
	 *
	 * Get the block explorer txs txid key name
	 *
	 * @return string
	 */
	protected function get_tx_txid_key_name() : string {
		return 'txid';
	}

	/**
	 *
	 * Get the block explorer txs confirms key name
	 *
	 * @return array
	 */
	protected function get_tx_confirms_key_name() : string {
		return 'confirmations';
	}

	/**
	 *
	 * Get the block explorer txs amount key name
	 *
	 * @return string
	 */
	protected function get_tx_amount_key_name() : string {
		return 'value';
	}

	/**
	 *
	 * Get the block explorer txs timestamp key name
	 *
	 * @return string
	 */
	protected function get_tx_timestamp_key_name() : string {
		return 'time';
	}

	/**
	 *
	 * Get the block explorer txs address key name
	 *
	 * @return string
	 */
	protected function get_tx_address_key_name() : string {
		return 'address';
	}

	/**
	 *
	 * Get the block explorer txs locktime key name
	 *
	 * @return string
	 */
	protected function get_tx_locktime_key_name() : string {
		return '';
	}

	/**
	 *
	 * Get the block explorer tx block height key name
	 *
	 * @return string
	 */
	protected function get_tx_block_height_key_name() : string {
		return '';
	}

	/**
	 *
	 * Get the block explorer tx double spend key name
	 *
	 * @return string
	 */
	protected function get_tx_double_spend_key_name() : string {
		return '';
	}

	/**
	 *
	 * Get the block explorer tx inputs key name
	 *
	 * @return string
	 */
	protected function get_tx_inputs_key_name() : string {
		return '';
	}

	/**
	 *
	 * Get the block explorer tx outputs key name
	 *
	 * @return string
	 */
	protected function get_tx_outputs_key_name() : string {
		return '';
	}

	/**
	 *
	 * Get the block explorer tx input sequence key name
	 *
	 * @return string
	 */
	protected function get_tx_input_sequence_key_name() : string {
		return '';
	}

	/**
	 *
	 * Is the block explorer crypto amount in satoshi (1e8)?
	 * Default is true (amount is satoshi ).
	 *
	 * @return bool
	 */
	protected function amount_from_api_is_satoshi(): bool {
		return false;
	}

	/**
	 *
	 * Get the block explorer max txs allowed in api call
	 *
	 * @return int
	 */
	public function get_api_max_allowed_addresses() : int {
		return 1;
	}


	/**
	 * Get txs from block explorer api
	 *
	 * @return array|false
	 */
	public function get_txs() {
		$result = parent::get_txs();
		usleep( 250000 ); // Max ~4 requests/second with SoChain TODO remove when we have proper rate limiting.
		return $result;
	}

	/**
	 *
	 * Get the formatted block explorer search currency cody
	 *
	 * @return string
	 */
	protected function get_search_currency(): string {
		return strtolower( $this->get_currency_name() );
	}

	/**
	 *
	 * Format the data from block explorer result to default data format
	 *
	 * @param stdClass|array $block_explorer_data Json decoded result from block explorer api call.
	 *
	 * @return stdClass|array
	 */
	protected function format_result_from_block_explorer( $block_explorer_data ) {
		if ( isset( $block_explorer_data->{$this->get_getinfo_key_name()} ) ) {
			if ( isset( $block_explorer_data->{$this->get_getinfo_key_name()}->{$this->get_txs_key_name()} ) ) {
				foreach ( $block_explorer_data->{$this->get_getinfo_key_name()}->{$this->get_txs_key_name()} as & $tx ) {
					$tx->address = $block_explorer_data->{$this->get_getinfo_key_name()}->address;
				}
			}
			return $block_explorer_data->{$this->get_getinfo_key_name()};
		}

		return $block_explorer_data;
	}
}
