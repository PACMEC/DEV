<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Insight Block Explorer API Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
abstract class CW_Block_Explorer_API_Insight extends CW_Block_Explorer_Base {


	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_txs_endpoint_format() : string {
		return 'addrs/%2$s/txs';
	}

	/**
	 *
	 * Get the block explorer api block height endpoint format
	 *
	 * @return string
	 */
	protected function get_block_height_endpoint_format() : string {
		return 'status?q=getInfo';
	}

	/**
	 *
	 * Get the block explorer block hash api endpoint format
	 *
	 * @return string
	 */
	protected function get_block_hash_endpoint_format() : string {
		return 'block-index/%2$d';
	}

	/**
	 *
	 * Get the block explorer txs key name
	 *
	 * @return string
	 */
	protected function get_txs_key_name() : string {
		return 'items';
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
	 * Get the block explorer block hash key name
	 *
	 * @return string
	 */
	protected function get_block_hash_key_name() : string {
		return 'blockHash';
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
	 * Get the block explorer txs timestamp key name
	 *
	 * @return string
	 */
	protected function get_tx_timestamp_key_name() : string {
		return 'time';
	}

	/**
	 *
	 * Get the block explorer tx block height key name
	 *
	 * @return string
	 */
	protected function get_tx_block_height_key_name() : string {
		return 'blockheight';
	}

	/**
	 *
	 * Get the block explorer tx double spend key name
	 *
	 * @return string
	 */
	protected function get_tx_double_spend_key_name() : string {
		return 'doubleSpentTxID';
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
	 * Get the block explorer txs address key name
	 *
	 * @return string
	 */
	protected function get_tx_address_key_name() : string {
		return 'address';
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
		return 5;
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
	 * Format the data from block explorer result to default data format
	 *
	 * @param stdClass|array $block_explorer_data Json decoded result from block explorer api call.
	 *
	 * @return stdClass|array
	 */
	protected function format_result_from_block_explorer( $block_explorer_data ) {
		if ( isset( $block_explorer_data->{$this->get_getinfo_key_name()} ) ) {
			return $block_explorer_data->{$this->get_getinfo_key_name()};
		}

		return $block_explorer_data;
	}

	/**
	 *
	 * Format the data from block explorer txs result to default data format
	 *
	 * @param stdClass|array $txs_data Json decoded txs result from block explorer api call.
	 *
	 * @return stdClass|array
	 */
	protected function format_txs_result_from_block_explorer( $txs_data ) {
		foreach ( $txs_data as $index => & $tx ) {
			foreach ( $this->get_addresses_array() as $address ) {
				// Calculate the received amount and unset if address is not in output (amount is 0).
				$amount = $this->insight_get_sum_outputs( $address, $tx->vout );
				if ( 0 === $amount ) {
					unset( $txs_data[ $index ] );
					continue;
				}

				// Check if double spend is detected.
				$double_spend = false;
				foreach ( $tx->{$this->get_tx_inputs_key_name()} as $input ) {
					if ( $input->{$this->get_tx_double_spend_key_name()} ) {
						$double_spend = $input->{$this->get_tx_double_spend_key_name()};
						break;
					}
				}

				// Format the data to expected format.
				$tx->{$this->get_tx_amount_key_name()}       = $amount;
				$tx->{$this->get_tx_address_key_name()}      = $address;
				$tx->{$this->get_tx_double_spend_key_name()} = $double_spend;
			}
		}

		// Reindex the array (index 0, 1, 2 etc).
		return array_values( $txs_data );
	}

	/**
	 *
	 * Add up all output amounts in "vout" from Insight API response objects and convert to integer.
	 *
	 * @param  $order_data
	 * @param  array      $outputs
	 * @return int
	 */
	protected function insight_get_sum_outputs( $address, $outputs = array() ) {
		$amount_received = 0;

		foreach ( $outputs as $output ) {
			$output_addresses = isset( $output->scriptPubKey->addresses ) && is_array( $output->scriptPubKey->addresses ) ? $output->scriptPubKey->addresses : array();
			if ( in_array( $address, $output_addresses ) ) {
				$amount_received += $output->value;
			}
		}

		return $amount_received;
	}
}
