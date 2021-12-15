<?php
/**
 * Esplora API Helper
 * API Docs: https://github.com/Blockstream/esplora/blob/master/API.md
 */
class CW_Esplora {


	/**
	 * @param $url
	 * @param $currency
	 * @param $endpoint
	 *
	 * @return stdClass
	 */
	public static function prepare_esplora_api( $url, $currency, $endpoint ) {

		// Determine network.

		$network = '';

		if ( 'LBTC' === $currency ) {
			$network = 'liquid/';
		} elseif ( false !== strpos( $currency, 'TEST' ) ) {
			$network = 'testnet/';
		}

		// If we have a placeholder in our URL add testnet or liquid endpoint
		if ( false !== strpos( $url, '%s' ) ) {
			$base_url = sprintf( $url, $network );
		} else {
			$base_url = $url;
		}

		// Endpoint.
		switch ( $endpoint ) {
			default:
			case 'full_address':
				$endpoint = 'address/%s/txs';
				break;
			case 'block_height':
				$endpoint = 'blocks/tip/height';
				break;
			case 'tx':
				$endpoint = 'tx/%s';
				break;
			case 'tx_status':
				// Returns the transaction confirmation status.
				// Available fields: confirmed (boolean), block_height (optional) and block_hash (optional).
				$endpoint = 'tx/%s/status';
				break;
			case 'blocks_since':
				// Returns the 10 newest blocks starting at the tip or at start_height if specified.
				$endpoint = 'blocks/%s';
				break;
			case 'address_mempool_tx':
				// Get unconfirmed transaction history for the specified address.
				$endpoint = 'address/%s/txs/mempool';
				break;
			case 'genesis':
				// Get genesis block
				$endpoint = 'block-height/0';
				break;
		}

		$api_context = new stdClass();

		$api_context->url = $base_url . $endpoint;

		return $api_context;
	}

	/**
	 * Query Esplora full address endpoint
	 *
	 * @param  $batch
	 * @param  $currency
	 * @param  $api_context
	 * @param  $options
	 * @return mixed
	 */
	public static function esplora_full_address( $batch, $currency, $api_context ) {

		$results = array();
		foreach ( $batch as $address ) {

			$url = sprintf( $api_context->url, $address );

			$fullAddress = self::esplora_safe_remote_get( $url, $currency );
			if ( $fullAddress ) {
				$results[ $address ] = $fullAddress;
			}
		}

		return $results;
	}

	/**
	 * Get block height via esplora
	 *
	 * @param  $url
	 * @param  $currency
	 * @param  $api_context
	 * @param  $return_error
	 * @return int
	 */
	public static function esplora_block_height( $url, $currency, $return_error = false ) {

		$error = $blockchain = false;

		$api_context = self::prepare_esplora_api( $url, $currency, 'block_height' );

		$blockchain = self::esplora_safe_remote_get( $api_context->url, $currency );

		return $error || ! $blockchain || ! is_numeric( $blockchain ) ? ! $return_error ? false : $error : (int) $blockchain;
	}

	/**
	 * Execute request to API
	 *
	 * @param $url
	 * @param $currency
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public static function esplora_safe_remote_get( $url, $currency ) {

		$response = wp_remote_get( $url );

		$result = CW_OrderProcessing::check_remote_get_response( $response, $currency, 'esplora', false );

		return $result;
	}


	/**
	 * Get amounts paid to addresses in the current batch via Esplora
	 *
	 * @todo Getting the block height for each currency on each cron execution wastes lots of resources -> refactor and check max. once per smallest avg. block time of the currency we're dealing with
	 *
	 * @param  $tx_update_api
	 * @param  $batch_currency
	 * @param  $batch
	 * @param  $orders
	 * @param  $options
	 * @return mixed
	 */
	public static function esplora_batch_tx_update( $tx_update_api, $batch_currency, $batch, $orders, $options ) {

		// Default to blockstream.info
		$url = 'https://blockstream.info/%sapi/';

		$custom_url = CW_Validate::check_if_unset( 'custom_esplora_api_btc', $options );
		if ( $tx_update_api === 'esplora_custom' ) {
			if ( $custom_url ) {
				// Use custom URL
				$url = $custom_url;
			} else {
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, 'Esplora API Error: Please enter custom URL in settings. Falling back to blockstream.info.', 'error' );
			}
		}

		// Prepare API
		$api_context = self::prepare_esplora_api( $url, $batch_currency, 'full_address' );

		// Maybe get block height (for checking the locktime)
		$height_transient_key = sprintf( 'block_height_%s', strtolower( $batch_currency ) );
		$chain_height         = get_transient( $height_transient_key );

		// Refresh chain height transient if outdated
		if ( ! $chain_height ) {
			$chain_height = self::esplora_block_height( $url, $batch_currency, $return_error = false );
			if ( $chain_height ) {
				set_transient( $height_transient_key, $chain_height, 600 );
			}
		}

		// Get data for currency batch
		$full_address_batch = self::esplora_full_address( $batch, $batch_currency, $api_context );

		// Maybe return error message
		if ( is_string( $full_address_batch ) ) {
			return array( 'message' => $full_address_batch );
		}

		// Analyze Esplora response
		$tx_data = self::esplora_tx_analysis( $orders, $full_address_batch, $options, $chain_height, $api_context );

		return $tx_data;
	}

	/**
	 * Get outputs from the transaction object
	 *
	 * @return array
	 */
	public static function get_outputs_addresses_for_transaction( $transaction ) {
		$outputs_addresses = array();
		$outputs           = $transaction->outputs;

		foreach ( $outputs as $output ) {
			$output_addresses  = isset( $output->addresses ) && is_array( $output->addresses ) ? $output->addresses : array();
			$outputs_addresses = array_merge( $outputs_addresses, $output_addresses );
		}

		return $outputs_addresses;
	}

	/**
	 * Obtain the TX resource for the given identifier.
	 *
	 * @param string   $hash
	 * @param array    $params     Parameters. Options: instart, outstart and limit
	 * @param stdClass $apiContext is the APIContext for this call. It is used to pass dynamic configuration and credentials.
	 *
	 * @return mixed
	 */
	public static function get_transaction( $currency, $hash, $params = array(), $apiContext ) {
		$url = sprintf( $apiContext->url, $hash, http_build_query( $params ) );

		return self::esplora_safe_remote_get( $url, $currency );
	}


	/**
	 * Calculate amounts paid to each order matching an address in the Esplora response
	 *
	 * @todo Pass $chain_height to check locktime
	 *
	 * @param  $batch_orders
	 * @param  $api_data
	 * @param  $options
	 * @param  $chain_height
	 * @param  bool|false   $api_context
	 * @return mixed
	 */
	public static function esplora_tx_analysis( $batch_orders, $api_data, $options, $chain_height, $api_context = false ) {

		$dbupdate      = 0;
		$payment_array = array();

		foreach ( $batch_orders as $order_data ) {

			if ( ! isset( $api_data[ $order_data->address ] ) ) {
				$payment_array[ $order_data->order_id ] = array(
					'status'                     => ! is_string( $api_data ) ? 'Esplora success | no transactions for address' : sprintf( 'Esplora error: %s', $api_data ),
					'force_update'               => 'no',
					'address'                    => $order_data->address,
					'order_id'                   => $order_data->order_id,
					'total_received_confirmed'   => 0,
					'total_received_unconfirmed' => 0,
					'tx_count'                   => 0,
				);
				CW_OrderProcessing::bump_last_update( $order_data->address, $order_data->order_id );
				continue; // Go to next order if we did not receive data for this address
			}

			// Get processing configuration
			$pc_conf = CW_OrderProcessing::get_processing_config( $order_data->payment_currency, $order_data->amount, $options );

			$status = is_array( $api_data ) && isset( $api_data['message'] ) ? $api_data['message'] : 'success';

			// Prepare transaction data for the payment address of this order
			$transactions = is_array( $api_data[ $order_data->address ] ) ? $api_data[ $order_data->address ] : array();

			$txids                          = array();
			$total_received_unconfirmed_sat = $total_received_confirmed_sat = 0;
			$double_spend                   = false;

			$batch_confidence = array();

			$count = count( $transactions );

			// Only calculate tx amounts if there are txs in the API response
			foreach ( $transactions as $transaction ) {

				// $payment_array[$order_data->order_id]['result'] = sprintf('%s|%s', strtotime($order_data->created_at), strtotime($transaction->received));
				// Only consider the transaction output if the lock time is ok and it is either unconfirmed or the order has been created before the transaction

				// Skip if address reuse (more than one tx or already confirmed tx was sent to the address before the order existed)
				$tx_ts    = isset( $transaction->status->block_time ) ? $transaction->status->block_time : 0;
				$is_fresh = (bool) ( ! $tx_ts || strtotime( $order_data->created_at ) < ( $tx_ts + 3600 ) );
				if ( ! $is_fresh ) {
					$data = array(
						sprintf( 'possible address reuse detected - ignoring transaction %s', $transaction->txid ) => array(
							'order_created_at' => $order_data->created_at,
							'order_ts'         => strtotime( $order_data->created_at ),
							'tx_ts'            => $tx_ts,
						),
					);
					CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $data, 'notice' );
					continue;
				}

				// Block height = -1 if unconfirmed
				$is_unconfirmed = isset( $transaction->status->confirmed ) ? ! (bool) $transaction->status->confirmed : true;

				$locktime_ok = true;
				if ( isset( $chain_height ) ) {
					// Maybe check lock time
					if ( $is_unconfirmed ) {
						$locktime_ok                = CW_OrderProcessing::check_tx_lock_time( $transaction, $chain_height );
						$transaction->confirmations = 0;
					} else {
						$tx_height                  = isset( $transaction->status->block_height ) ? : 0;
						$transaction->confirmations = (int) $chain_height - (int) $tx_height;
					}
				}

				if ( $locktime_ok ) {

					if ( $pc_conf['min_confidence'] > 0 ) {
						// Get tx confidence from the batch if the merchant accepts zeroconf, else use 0/1 value from address/full endpoint
						$confidence = isset( $batch_confidence[ $transaction->txid ] ) && is_numeric( $batch_confidence[ $transaction->txid ] ) ? (float) $batch_confidence[ $transaction->txid ] : (float) $transaction->confirmations;
					} else {
						// Raw zeroconf
						$confidence = $transaction->confirmations = 1;
					}
					// Determine age of the transaction
					// $time = strtotime($transaction->received);
					// $tx_age = time() - $time;

					// Add all outputs of the tx that go to the payment address
					$amount_received = self::get_sum_outputs( $order_data, $transaction->vout );

					// If the transaction is unconfirmed check tx sequence number (to prevent RBF)
					$is_rbf = $is_unconfirmed ? CW_OrderProcessing::check_input_sequences( $transaction ) : false;

					// Add tx amount to total amount received
					// if transaction confidence is good or it has more than the required minimum confirmations
					if ( ( ( $is_unconfirmed && ! $is_rbf && $confidence >= (float) $pc_conf['min_confidence'] ) || ! $is_unconfirmed ) && (int) $transaction->confirmations >= $pc_conf['min_conf'] ) {

						// Add tx amount to total amount received
						$total_received_confirmed_sat += $amount_received;
						/*
						if ((int)$transaction->confirmations < 1) {
							// Calculate the amount that is not spendable yet
							$not_spendable_sat += $amount_received;
						} */
					} else {
						if ( $is_rbf ) {
							if ( CW_AdminMain::logging_is_enabled( 'info', $options ) ) {
								CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, sprintf( 'replace-by-fee flag detected - no zeroconf for %s', $transaction->txid ), 'info' );
							}
							$transaction->txid .= '-RBF';
						}
						$total_received_unconfirmed_sat += $amount_received;
					}

					if ( ! $is_rbf && (bool) $transaction->double_spend ) {
						$txids[ $transaction->txid ] = "DOUBLESPEND-{$confidence}|{$amount_received}"; // Add notice about double spend to txid array
						$double_spend                = true;
						// Action hook for double spend alert
						do_action( 'cryptowoo_doublespend', $txids );
					} else {
						$txids[ $transaction->txid ] = $amount_received;
					}
				} // Check block height
			} // Foreach transaction
			$total_received_unconfirmed = ! (bool) $double_spend ? $total_received_unconfirmed_sat : 0; // 100000000 : 0;
			$total_received_confirmed   = ! (bool) $double_spend ? $total_received_confirmed_sat : 0; // 100000000 : 0;
			$txids_serialized           = serialize( $txids );

			// Prepare tx update result for order
			$payment_array[ $order_data->order_id ] = array(
				'status'                     => is_array( $api_data ) ? "Esplora: {$status}" : sprintf( 'Esplora error: %s %s', $status, var_export( $api_data, true ) ),
				'address'                    => $order_data->address,
				'order_id'                   => $order_data->order_id,
				'total_received_confirmed'   => $total_received_confirmed,
				'total_received_unconfirmed' => $total_received_unconfirmed,
				'tx_confidence'              => isset( $confidence ) ? (float) $confidence : 'none',
				'tx_count'                   => $count,
				'txids_serialized'           => $txids_serialized,
			);
			if ( CW_AdminMain::logging_is_enabled( 'debug', $options ) ) {
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . " Esplora|#{$order_data->order_id}|{$order_data->address}|{$txids_serialized}", 'debug' );
			}
			// $timeago = time() - (int)$order_data->last_update;

			// Force order processing if the order will time out within the next 5.5 minutes
			if ( ! isset( $payment_array[ $order_data->order_id ]['force_update'] ) || $payment_array[ $order_data->order_id ]['force_update'] !== 'yes' ) {
				$time     = time();
				$long_ago = $time - (int) $order_data->last_update > 60 ? true : false;
				$payment_array[ $order_data->order_id ]['force_update'] = $long_ago || ( (int) $order_data->timeout_value - $time ) < 330 ? 'yes' : 'no';
			}

			// Calculate order age
			$payment_array[ $order_data->order_id ]['order_age'] = time() - strtotime( $order_data->created_at );

			// Maybe update order data // TODO move to update_tx_details()
			if ( strpos( $payment_array[ $order_data->order_id ]['status'], 'success' ) || $payment_array[ $order_data->order_id ]['force_update'] === 'yes' ) {

				// Force order processing since we have new tx data
				$payment_array[ $order_data->order_id ]['force_update'] = 'yes'; // TODO Revisit force order update

				// Update payments table TODO batch up and update in one query
				$dbupdate += CW_OrderProcessing::update_address_info( $order_data->address, $payment_array[ $order_data->order_id ]['total_received_confirmed'], $payment_array[ $order_data->order_id ]['total_received_unconfirmed'], $txids_serialized, $order_data->order_id );

				// Update order meta
				$order_meta = array(
					'received_confirmed'   => $payment_array[ $order_data->order_id ]['total_received_confirmed'],
					'received_unconfirmed' => $payment_array[ $order_data->order_id ]['total_received_unconfirmed'],
					'txids'                => $txids_serialized,
					'has_txids'            => ! empty( $txids ),
				);

				CW_OrderProcessing::cwwc_update_order_meta( $order_data->order_id, $order_meta );

			} else {
				$payment_array[ $order_data->order_id ] = array_merge(
					$payment_array[ $order_data->order_id ],
					array(
						'timeout_in' => $order_data->timeout_value - time(),
						'timeout'    => $order_data->timeout,
					)
				);
			}
			unset( $txids_serialized );
		}
		$payment_array['dbupdate'] = $dbupdate;
		return $payment_array;
	}

	/**
	 * Add up all outputs for a payment address
	 *
	 * @param  $order_data
	 * @param  array      $outputs
	 * @return int
	 */
	static function get_sum_outputs( $order_data, $outputs = array() ) {
		$amount_received = 0;

		foreach ( $outputs as $output ) {
			$output_address = isset( $output->scriptpubkey_address ) ? $output->scriptpubkey_address : '';
			if ( $order_data->address === $output_address ) {
				$amount_received += (int) $output->value;
			}
		}
		return (int) $amount_received;
	}
}
