<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Smartbit Block Explorer API Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
abstract class CW_Block_Explorer_API_Smartbit extends CW_Block_Explorer_Base {


	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_txs_endpoint_format() : string {
		return 'address/%2$s';
	}

	/**
	 *
	 * Get the block explorer api block height endpoint format
	 *
	 * @return string
	 */
	protected function get_block_height_endpoint_format() : string {
		return 'block';
	}

	/**
	 *
	 * Get the block explorer block hash api endpoint format
	 *
	 * @return string
	 */
	protected function get_block_hash_endpoint_format() : string {
		return 'block/%2$d';
	}

	/**
	 *
	 * Get the block explorer txs key name
	 *
	 * @return string
	 */
	protected function get_txs_key_name() : string {
		return 'transactions';
	}

	/**
	 *
	 * Get the block explorer block height key name
	 *
	 * @return string
	 */
	protected function get_block_height_key_name() : string {
		return 'height';
	}

	/**
	 *
	 * Get the block explorer block hash key name
	 *
	 * @return string
	 */
	protected function get_block_hash_key_name() : string {
		return 'hash';
	}


	/**
	 *
	 * Get the address key name from api result
	 *
	 * @return string
	 */
	protected function get_getaddress_key_name() : string {
		return 'address';
	}

	/**
	 *
	 * Get the block key name from api result
	 *
	 * @return string
	 */
	protected function get_getblock_key_name() : string {
		return 'block';
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
		return 'first_seen';
	}

	/**
	 *
	 * Get the block explorer tx block height key name
	 *
	 * @return string
	 */
	protected function get_tx_block_height_key_name() : string {
		return 'block';
	}

	/**
	 *
	 * Get the block explorer tx double spend key name
	 *
	 * @return string
	 */
	protected function get_tx_double_spend_key_name() : string {
		return 'double_spend';
	}

	/**
	 *
	 * Get the block explorer tx inputs key name
	 *
	 * @return string
	 */
	protected function get_tx_inputs_key_name() : string {
		return 'inputs';
	}

	/**
	 *
	 * Get the block explorer tx outputs key name
	 *
	 * @return string
	 */
	protected function get_tx_outputs_key_name() : string {
		return 'outputs';
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
		usleep( 250000 ); // Max ~4 requests/second with Smartbit.com.au TODO remove when we have proper rate limiting.
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
		if ( isset( $block_explorer_data->{$this->get_getblock_key_name()} ) ) {
			return $block_explorer_data->{$this->get_getblock_key_name()};
		} elseif ( isset( $block_explorer_data->{$this->get_getaddress_key_name()} ) ) {
			// If this address never received a tx the transactions object is missing from response so we add it in.
			if ( ! isset( $block_explorer_data->{$this->get_getaddress_key_name()}->{$this->get_txs_key_name()} ) ) {
				$block_explorer_data->{$this->get_getaddress_key_name()}->{$this->get_txs_key_name()} = array();
			}
			$block_explorer_data = $block_explorer_data->{$this->get_getaddress_key_name()};
		}

		// Calculate smartbit api sum amount from tx outputs.
		if ( isset( $block_explorer_data->{$this->get_txs_key_name()} ) ) {
			foreach ( $block_explorer_data->{$this->get_txs_key_name()} as $index => $tx ) {
				foreach ( $this->get_addresses_array() as $address ) {
					$amount = $this->smartbit_get_sum_outputs( $address, $tx->outputs );
					if ( 0 === $amount ) {
						unset( $block_explorer_data->{$this->get_txs_key_name()}[ $index ] );
					} else {
						$block_explorer_data->{$this->get_txs_key_name()}[ $index ]->amount  = $amount;
						$block_explorer_data->{$this->get_txs_key_name()}[ $index ]->address = $address;
					}
				}
			}
			// Reindex the array (index 1, 2, 3 etc).
			$block_explorer_data->{$this->get_txs_key_name()} = array_values( $block_explorer_data->{$this->get_txs_key_name()} );
		}

		return $block_explorer_data;
	}

	/**
	 *
	 * Add up all output amounts in "vout" from Smartbit API response objects and convert to integer.
	 *
	 * @param  $order_data
	 * @param  array      $outputs
	 * @return int
	 */
	protected function smartbit_get_sum_outputs( $address, $outputs = array() ) {
		$amount_received = 0;

		foreach ( $outputs as $output ) {
			$output_addresses = isset( $output->addresses ) && is_array( $output->addresses ) ? $output->addresses : array();
			if ( in_array( $address, $output_addresses ) ) {
				$amount_received += $output->value_int;
			}
		}

		return $amount_received;
	}
}
