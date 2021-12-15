<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * BlockCypher Block Explorer API Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 */
abstract class CW_Block_Explorer_API_BlockCypher extends CW_Block_Explorer_Base {


	/**
	 *
	 * Get the api key key string
	 *
	 * @return string
	 */
	protected function get_api_key_key(): string {
		return 'blockcypher_token';
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_txs_endpoint_format() : string {
		return '%1$s/' . $this->get_chain_name() . '/addrs/%2$s/full' . ( $this->has_api_key() ? '?token=%3$s' : '' );
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_tx_endpoint_format() : string {
		return '%1$s/' . $this->get_chain_name() . '/txs/%2$s/?' . ( $this->has_api_key() ? 'token=%3$s&' : '' );
	}

	/**
	 *
	 * Get the block explorer api block height endpoint format
	 *
	 * @return string
	 */
	protected function get_block_height_endpoint_format() : string {
		return '%1$s/' . $this->get_chain_name() . '' . ( $this->has_api_key() ? '?token=%3$s' : '' );
	}

	/**
	 *
	 * Get the block explorer block hash api endpoint format
	 *
	 * @return string
	 */
	protected function get_block_hash_endpoint_format() : string {
		return '%1$s/' . $this->get_chain_name() . '/blocks/%2$s?limit=1' . ( $this->has_api_key() ? '&token=%3$s' : '' );
	}

	/**
	 *
	 * Get the formatting of confidence factor api endpoint
	 *
	 * @return string
	 */
	protected function get_confidence_endpoint_format() : string {
		return '%1$s/' . $this->get_chain_name() . '/txs/%2$s/confidence' . ( $this->has_api_key() ? '?token=%3$s' : '' );
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
	 * Get the confidence key name
	 *
	 * @return string
	 */
	protected function get_tx_confidence_key_name() : string {
		if ( CW_Order_Processing_Tools::instance()->zero_conf_is_enabled( $this->get_current_order_id() ) ) {
			return 'confidence';
		}

		return '';
	}

	/**
	 *
	 * Get the block explorer txs txid key name
	 *
	 * @return string
	 */
	protected function get_tx_txid_key_name() : string {
		return 'hash';
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
	 * Get the block explorer txs locktime key name
	 *
	 * @return string
	 */
	protected function get_tx_locktime_key_name() : string {
		return 'lock_time';
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
		return 'received';
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
	 * Get the block explorer max txs allowed in api call
	 *
	 * @return int
	 */
	public function get_api_max_allowed_addresses() : int {
		// TODO: Set to 3 for free and more for paid accounts with ; separating them but do not before its tested!
		return 1;
	}

	/**
	 *
	 * Return the net name on blockcypher ('main' for main net and 'test3' for test net)
	 *
	 * @return string
	 */
	protected function get_chain_name() {
		if ( CW_Order_Processing_Tools::instance()->currency_is_test_coin( $this->get_currency_name() ) ) {
			return 'test3';
		} else {
			return 'main';
		}
	}

	/**
	 * Get txs from block explorer api through full address endpoint
	 *
	 * @return array|false
	 */
	public function get_txs() {
		// if $timeago > $rate_limit only update balance if data is older than x seconds TODO revisit tx update rate limiting.
		$result = parent::get_txs();
		usleep( 333333 ); // Max ~3 requests/second TODO remove when we have proper rate limiting.
		return $result;
	}

	/**
	 * Get tx from block explorer api through txs hash endpoint
	 *
	 * @param string $tx_hash Blockchain transaction id.
	 * @param array  $params  Parameters. Options: instart, outstart and limit.
	 *
	 * @return stdClass|false
	 */
	public function get_tx( $tx_hash, $params ) {
		// if $timeago > $rate_limit only update balance if data is older than x seconds TODO revisit tx update rate limiting.
		$url = $this->format_api_url( $this->get_tx_endpoint_format(), $this->get_search_currency(), $tx_hash ) . http_build_query( $params );

		return json_decode( CW_ExchangeRates::processing()->request( $url, $this->is_json(), $this->is_user_agent(), $this->get_timeout(), $this->get_proxy() ) );
	}

	/**
	 *
	 * Get the formatted confidence URL
	 *
	 * @param string $tx_id Transaction ID.
	 *
	 * @return string
	 */
	public function get_confidence_url( string $tx_id ) : string {
		return $this->format_api_url( $this->get_confidence_endpoint_format(), false, $tx_id );
	}

	/**
	 *
	 * Get the required indexes (key names) from the block explorer result txs data.
	 * Remove the confidence key from the required txs data result.
	 *
	 * @return array
	 */
	protected function get_required_txs_keys() : array {
		$required_txs_keys = parent::get_required_txs_keys();
		unset( $required_txs_keys['confidence'] );

		return $required_txs_keys;
	}

	/**
	 *
	 * Get confidence rating from block explorer api
	 *
	 * @param string $tx_id Transaction ID.
	 *
	 * @return float|false
	 */
	public function get_confidence( string $tx_id ) {
		return $this->get_api_data( $this->get_confidence_url( $tx_id ), $this->get_tx_confidence_key_name(), __FUNCTION__ );
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
		// Request more transaction outputs until we have the required outputs for the payment address.
		foreach ( $this->get_addresses_array() as $address ) {
			$txs_data = $this->merge_related_outputs( $txs_data, $address );
		}

		// Remove duplicate transactions. TODO: Remove this when bug on blockcypher side is resolved.
		$txs_data = $this->remove_duplicate_transactions( $txs_data );

		// Format confirmed and address to expected values.
		foreach ( $txs_data as & $tx ) {
			$tx->{$this->get_tx_timestamp_key_name()} = $this->convert_iso_to_timestamp( $tx->{$this->get_tx_timestamp_key_name()}, 'Y-m-d\TH:i:s.uP' );
			$tx->{$this->get_tx_amount_key_name()}    = $this->get_sum_outputs( $tx->address, $tx->outputs );
			$tx->{$this->get_tx_locktime_key_name()}  = isset( $tx->{$this->get_tx_locktime_key_name()} ) ? $tx->{$this->get_tx_locktime_key_name()} : 0; // lock_time is not always included from api.
		}

		// Get confidence if at least one tx is unconfirmed and an api key exists.
		if ( $this->has_api_key() && CW_Order_Processing_Tools::instance()->zero_conf_is_enabled( $this->get_current_order_id() ) ) {
			foreach ( $txs_data as & $tx ) {
				if ( ! $tx->{$this->get_tx_confirms_key_name()} ) {
					$tx->{$this->get_tx_confidence_key_name()} = $this->get_confidence( $tx->{$this->get_tx_txid_key_name()} );
					break;
				}
			}
		}

		return $txs_data;
	}

	/**
	 *
	 * Validate and return api data
	 *
	 * @param string|WP_Error $request  Return data from block explorer API.
	 * @param string          $data_key Key name of the data we want from the result.
	 * @param bool            $is_json  If the data result is supposed to be json or not (if not its string).
	 *
	 * @return array
	 */
	protected function validate_api_result( $request, $data_key, bool $is_json ) : array {
		$result = parent::validate_api_result( $request, $data_key, $is_json );

		// If tx has already been confirmed when calling confidence endpoint it will fail, then return confidence 1.
		if ( $this->get_tx_confidence_key_name() === $data_key ) {
			if ( isset( $result['block_explorer_data']->error ) && false !== strpos( $result['block_explorer_data']->error, 'already been confirmed' ) ) {
				$result['status']                          = 'success';
				$result['block_explorer_data']->confidence = 1;
				unset( $result['block_explorer_data']->error );
			}
		}

		return $result;
	}

	/**
	 *
	 * Remove duplicate txs from Blockcypher API result.
	 *
	 * @param array $transactions Blockchain transactions.
	 *
	 * @return array
	 */
	protected function remove_duplicate_transactions( $transactions ) {
		$count = count( $transactions );

		// Remove duplicate txs from blockcypher api (both same tx but one is unconfirmed and the other is confirmed).
		// TODO: Remove when BlockCypher has Fixed the duplicate txs issue.
		// TODO: This should be done in BlockCypher API.
		if ( 1 < $count ) {
			$filtered_transactions = array();
			foreach ( $transactions as &$transaction ) {

				if ( ! array_key_exists( $transaction->{$this->get_tx_txid_key_name()}, $filtered_transactions ) ) {
					// We did not see this hash yet - let's add it to our list.
					$filtered_transactions[ $transaction->{$this->get_tx_txid_key_name()} ] = $transaction;
				} else {
					// We already have this transaction in our filtered list.
					// - let's check if the one we are currently looking at is already confirmed.
					// if so, replace the previous version of the transaction in the filtered list.
					if ( (bool) ( ( isset( $transaction->{$this->get_tx_confirms_key_name()} ) && 0 < (int) $transaction->{$this->get_tx_confirms_key_name()} ) || - 1 !== (int) $transaction->{$this->get_tx_block_height_key_name()} ) ) {
						$filtered_transactions[ $transaction->{$this->get_tx_txid_key_name()} ] = $transaction;
					}
				}
			}

			return $filtered_transactions;
		}

		return $transactions;
	}

	/**
	 * Add up all outputs for a payment address
	 *
	 * @param string $payment_address Blockchain payment address.
	 * @param array  $outputs         Blockchain transaction outputs.
	 *
	 * @return int
	 */
	protected function get_sum_outputs( $payment_address, $outputs = array() ) {
		$amount_received = 0;

		foreach ( $outputs as $output ) {
			$output_addresses = isset( $output->addresses ) && is_array( $output->addresses ) ? $output->addresses : array();
			if ( in_array( $payment_address, $output_addresses, true ) ) {
				$amount_received += (int) $output->value;
			}
		}

		return (int) $amount_received;
	}

	/**
	 * Blockcypher only returns 20 outputs per request.
	 * Request more transaction outputs until we have the required outputs for the payment address.
	 *
	 * @param stdClass[] $transactions    Array of transactions.
	 * @param string     $payment_address Blockchain address.
	 *
	 * @return mixed
	 */
	protected function merge_related_outputs( $transactions, $payment_address ) {
		$next_outputs_start = 0;

		foreach ( $transactions as $key => &$transaction ) {
			$outputs       = self::get_outputs_addresses_for_transaction( $transaction );
			$outputs_count = count( $outputs );

			while ( ! in_array( $payment_address, $outputs, true ) && 20 === $outputs_count ) {
				$next_outputs_start += $outputs_count;
				$transaction         = $this->get_tx(
					$transaction->{$this->get_tx_txid_key_name()},
					array(
						'outstart' => $next_outputs_start,
						'limit'    => $outputs_count,
					)
				);
				$outputs             = self::get_outputs_addresses_for_transaction( $transaction );
				$outputs_count       = count( $outputs );
			}

			// Remove transactions that go out of the wallet because we only want to analyze incoming txs.
			if ( ! in_array( $payment_address, $outputs, true ) ) {
				unset( $transactions[ $key ] );
			} else {
				// Add the address to the tx result.
				$transaction->{$this->get_tx_address_key_name()} = $payment_address;
			}
		}

		// Reindex the array (index 0, 1, 2 etc).
		$transactions = array_values( $transactions );

		return $transactions;
	}

	/**
	 * Get outputs from the transaction object
	 *
	 * @param stdClass $transaction Blockchain transaction id.
	 *
	 * @return array
	 */
	protected function get_outputs_addresses_for_transaction( $transaction ) {
		$outputs_addresses = array();
		$outputs           = $transaction->{$this->get_tx_outputs_key_name()};

		foreach ( $outputs as $output ) {
			$output_addresses  = isset( $output->addresses ) && is_array( $output->addresses ) ? $output->addresses : array();
			$outputs_addresses = array_merge( $outputs_addresses, $output_addresses );
		}

		return $outputs_addresses;
	}
}
