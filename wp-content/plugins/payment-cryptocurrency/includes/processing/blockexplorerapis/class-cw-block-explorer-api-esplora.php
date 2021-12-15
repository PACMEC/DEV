<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Esplora Block Explorer Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
abstract class CW_Block_Explorer_API_Esplora extends CW_Block_Explorer_Base {


	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_txs_endpoint_format() : string {
		return 'address/%2$s/txs';
	}

	/**
	 *
	 * Get the block explorer api block height endpoint format
	 *
	 * @return string
	 */
	protected function get_block_height_endpoint_format() : string {
		return 'blocks/tip/height';
	}

	/**
	 *
	 * Get the block explorer block hash api endpoint format
	 *
	 * @return string
	 */
	protected function get_block_hash_endpoint_format() : string {
		return 'block-height/%2$d';
	}

	/**
	 *
	 * Get the block explorer txs key name
	 *
	 * @return string
	 */
	protected function get_txs_key_name() : string {
		return '';
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
		return '';
	}

	/**
	 *
	 * Get the block explorer txs amount key name
	 *
	 * @return string
	 */
	protected function get_tx_amount_key_name() : string {
		return 'amount';
	}

	/**
	 *
	 * Get the block explorer txs locktime key name
	 *
	 * @return string
	 */
	protected function get_tx_locktime_key_name() : string {
		return 'locktime';
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
	 * Get the block explorer txs timestamp key name
	 *
	 * @return string
	 */
	protected function get_tx_timestamp_key_name() : string {
		return 'block_time';
	}

	/**
	 *
	 * Get the block explorer tx block height key name
	 *
	 * @return string
	 */
	protected function get_tx_block_height_key_name() : string {
		return 'block_height';
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
		return 'vin';
	}

	/**
	 *
	 * Get the block explorer tx outputs key name
	 *
	 * @return string
	 */
	protected function get_tx_outputs_key_name() : string {
		return 'vout';
	}

	/**
	 *
	 * Get the block explorer tx input sequence key name
	 *
	 * @return string
	 */
	protected function get_tx_input_sequence_key_name() : string {
		return 'sequence';
	}

	/**
	 *
	 * Get the block explorer block height key name
	 *
	 * @return string
	 */
	protected function get_block_height_key_name() : string {
		return '';
	}

	/**
	 *
	 * Get the block explorer block hash key name
	 *
	 * @return string
	 */
	protected function get_block_hash_key_name() : string {
		return '';
	}

	/**
	 *
	 * Get the getInfo key name
	 *
	 * @return string
	 */
	protected function get_getinfo_key_name() : string {
		return 'info';
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
	 *
	 * Get the block explorer API network endpoint
	 *
	 * @return string
	 */
	protected function get_network_endpoint() : string {
		if ( 'LBTC' === $this->get_currency_name() ) {
			return 'liquid/';
		} elseif ( 'BTCTEST' === $this->get_currency_name() ) {
			return 'testnet/';
		} else {
			return '';
		}
	}

	/**
	 * Get txs from block explorer api
	 *
	 * @return array|false
	 */
	public function get_txs() {
		$result = parent::get_txs();
		usleep( 333333 ); // Max ~3 requests/second TODO remove when we have proper rate limiting.
		return $result;
	}

	/**
	 *
	 * Get block hash from block explorer api by block index
	 *
	 * @param int $block_index Block index (eg 0 for genesis block). Default is 0.
	 *
	 * @return string|false
	 */
	public function get_block_hash( int $block_index = 0 ) {
		return $this->get_api_data( $this->get_block_hash_url( $block_index ), $this->get_block_hash_key_name(), __FUNCTION__, false );
	}

	/**
	 * Get block height from block explorer api
	 *
	 * @return int|false
	 */
	public function get_block_height() {
		return $this->get_api_data( $this->get_block_height_url(), $this->get_block_height_key_name(), __FUNCTION__, false );
	}


	/**
	 *
	 * Format the data from block explorer result to default data format
	 *
	 * @param array $block_explorer_data Json decoded result from block explorer api call.
	 *
	 * @return array
	 */
	protected function format_result_from_block_explorer( $block_explorer_data ) {
		// If we got false as the response body then the address has received no tx yet.
		if ( false === $block_explorer_data ) {
			return array();
		}

		// If we got unexpected response we will return immediately.
		if ( is_string( $block_explorer_data ) || is_int( $block_explorer_data ) || ! is_array( $block_explorer_data ) ) {
			return $block_explorer_data;
		}

		// Set expected formatting.
		foreach ( $block_explorer_data as $index => & $tx ) {
			foreach ( $this->get_addresses_array() as $address ) {
				$amount = $this->esplora_get_sum_outputs( $this->get_current_address(), $tx->{$this->get_tx_outputs_key_name()} );
				if ( 0 === $amount ) {
					unset( $block_explorer_data[ $index ] );
				} else {
					$tx->{$this->get_tx_address_key_name()}      = $address;
					$tx->{$this->get_tx_amount_key_name()}       = $amount;
					$tx->{$this->get_tx_timestamp_key_name()}    = isset( $tx->status->block_time ) ? (int) $tx->status->block_time : false;
					$tx->{$this->get_tx_block_height_key_name()} = isset( $tx->status->block_height ) ? (int) $tx->status->block_height : false;
				}
			}
		}

		// Reindex the array (index 0, 1, 2 etc).
		$block_explorer_data = array_values( $block_explorer_data );

		return $block_explorer_data;
	}

	/**
	 *
	 * Add up all output amounts in "vout" from Esplora API response objects and convert to integer.
	 *
	 * @param  $order_data
	 * @param  array      $outputs
	 * @return int
	 */
	protected function esplora_get_sum_outputs( $address, $outputs = array() ) {
		$amount_received = 0;

		foreach ( $outputs as $output ) {
			$output_address = is_string( $output->scriptpubkey_address ) ? $output->scriptpubkey_address : '';
			if ( $address === $output_address ) {
				$amount_received += (int) (int) round( (float) $output->value );
			}
		}

		return $amount_received;
	}
}
