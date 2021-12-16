<?php

/**
 * Blockcypher API Helper
 */
class CW_Blockcypher {


	/**
	 * @param $currency
	 * @param $options
	 * @param $endpoint
	 *
	 * @return stdClass
	 */
	public static function prepare_blockcypher_api( $currency, $options, $endpoint ) {

		// Determine network.
		$network = strpos( $currency, 'TEST' ) ? 'test3' : 'main';
		$coin    = strtolower( str_replace( 'TEST', '', $currency ) );

		$base_url = sprintf( 'https://api.blockcypher.com/v1/%s/%s', $coin, $network );

		// Blockcypher API token.
		$token = '';
		if ( isset( $options['blockcypher_token'] ) && $options['blockcypher_token'] ) {
			$token = '?token=' . $options['blockcypher_token'];
		}

		// Endpoint.
		switch ( $endpoint ) {
			default:
			case 'full_address':
				$endpoint = '/addrs/%s/full' . $token;
				break;
			case 'confidence':
				$endpoint = '/txs/%s/confidence' . $token;
				break;
			case 'block_height':
				$endpoint = '';
				break;
			case 'tx':
				$endpoint = $token ? '/txs/%s' . $token . '&%s' : '/txs/%s?%s';
				break;
		}

		$api_context = new stdClass();

		$api_context->url = $base_url . $endpoint;

		return $api_context;
	}

	/**
	 * Query Blockcypher multiple full address endpoint
	 *
	 * @param  $batch
	 * @param  $currency
	 * @param  $api_context
	 * @param  $options
	 * @return mixed
	 */
	public static function blockcypher_full_address( $batch, $currency, $api_context ) {

		$error = $fullAddress = false;

		$batch = implode( ';', $batch );

		$url = sprintf( $api_context->url, $batch );

		$fullAddress = self::blockcypher_safe_remote_get( $url, $currency );

		return false !== $error ? $error : $fullAddress;
	}

	/**
	 * Get block height via blockcypher
	 *
	 * @param  $currency
	 * @param  $api_context
	 * @param  $return_error
	 * @return int
	 */
	public static function blockcypher_block_height( $currency, $api_context, $return_error = false ) {

		$error = $blockchain = false;

		$url = $api_context->url;

		$blockchain = self::blockcypher_safe_remote_get( $url, $currency );

		return $error || ! $blockchain || ! isset( $blockchain->height ) ? ! $return_error ? false : $error : (int) $blockchain->height;
	}

	/**
	 * Get transaction confidence via blockcypher
	 *
	 * @param  $transactions
	 * @param  $currency
	 * @param  $api_context
	 * @param  $options
	 * @return array|false
	 */
	public static function blockcypher_tx_confidence( array $transactions, $currency, $api_context, $options ) {
		$last_confidence_check = get_option( 'cryptowoo_last_confidence_check' ) ?: array();

		// Maybe limit Blockcypher API calls based on tx confidence limit rates
		if ( (bool) $options['limit_blockcypher_rate'] ) {
			$bc_status              = CW_AdminMain::get_blockcypher_limit( $options );
			$estimated_min_interval = 3600 / $bc_status['confidence_limit_hour'];
			// Skip if the last update has happened within the estimated minimum interval
			if ( isset( $last_confidence_check['blockcypher'] ) && ( time() - (int) $last_confidence_check['blockcypher'] ) < (int) $estimated_min_interval ) {
				return false;
			}
		}

		$error  = false;
		$result = $errors = array();

		foreach ( $transactions as $transaction ) {
			// Only include unconfirmed transactions
			if ( (int) $transaction->confirmations === 0 || ! isset( $transaction->confirmations ) ) { // TODO Filter transactions that are spending from the payment address
				$data[] = isset( $transaction->hash ) ? $transaction->hash : $transaction->txid;
			}
		}

		if ( isset( $data ) ) {
			$url                    = sprintf( $api_context->url, implode( ';', $data ) );
			$multiple_tx_confidence = self::blockcypher_safe_remote_get( $url, $currency );

			// TODO: Refactor
			// TODO: Look at error handling (relevant for entire project)
			if ( isset( $multiple_tx_confidence ) ) {
				if ( is_array( $multiple_tx_confidence ) ) {
					foreach ( $multiple_tx_confidence as $tx_confidence ) {
						if ( isset( $tx_confidence->error ) ) {
							$errors[] = $tx_confidence->error;
						} else {
							$result[ $tx_confidence->txhash ] = $tx_confidence->confidence;
						}
					}
				} elseif ( $multiple_tx_confidence instanceof stdClass ) {
					if ( isset( $multiple_tx_confidence->error ) ) {
						$errors[] = $multiple_tx_confidence->error;
					} elseif ( isset( $multiple_tx_confidence->txhash ) && isset( $multiple_tx_confidence->confidence ) ) {
						$result[ $multiple_tx_confidence->txhash ] = $multiple_tx_confidence->confidence;
					}
				}
			}

			$options = get_option( 'cryptowoo_payments' );
			if ( CW_AdminMain::logging_is_enabled( 'debug', $options ) ) {
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, array_merge( $result, $errors ), 'debug' );
			}

			$last_confidence_check['blockcypher'] = time();
			update_option( 'cryptowoo_last_confidence_check', $last_confidence_check );
		}

		return $error ? false : $result; // TODO better handling of response {"error": "Transaction hash not found or transaction hasalready been confirmed: 93f44720befff58abe4e73cd782402bb5e8af227e2b581ae282a1ec938b3ae7c."}
	}

	/**
	 * Execute request to API
	 *
	 * @param $url
	 * @param $currency
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public static function blockcypher_safe_remote_get( $url, $currency ) {

		$response = wp_safe_remote_get( $url );
		// TODO check rate limit remaining
		// $rate_limit_remaining = isset($fullAddress['headers']['x-ratelimit-remaining']) ? $fullAddress['headers']['x-ratelimit-remaining'] : 0;

		$result = CW_OrderProcessing::check_remote_get_response( $response, $currency, 'blockcypher', false );

		return $result;
	}


	/**
	 * Get amounts paid to addresses in the current batch via Blockcypher
	 *
	 * @todo Getting the block height for each currency on each cron execution wastes lots of resources -> refactor and check max. once per smallest avg. block time of the currency we're dealing with
	 *
	 * @param  $batch_currency
	 * @param  $batch
	 * @param  $orders
	 * @param  $options
	 * @return mixed
	 */
	public static function blockcypher_batch_tx_update( $batch_currency, $batch, $orders, $options ) {

		$api_data = array();

		// Prepare API
		$api_context = self::prepare_blockcypher_api( $batch_currency, $options, 'full_address' );

		// Maybe get block height (for checking the locktime) TODO Move somewhere else
		/*
		if ($min_conf === 0) {
			$chain_height = CW_OrderProcessing::blockcypher_block_height($batch_currency, $api_context);
		}
		*/

		// Get data for currency batch
		$full_address_batch = self::blockcypher_full_address( $batch, $batch_currency, $api_context, $options );

		// Maybe return error message
		if ( is_string( $full_address_batch ) ) {
			return array( 'message' => $full_address_batch );
		}

		if ( isset( $full_address_batch->address ) ) {
			$api_data[ $full_address_batch->address ] = $full_address_batch;
		} else {

			// Prepare data for each order
			foreach ( $full_address_batch as $batch_order_data ) {
				if ( isset( $batch_order_data->address ) ) {
					$api_data[ $batch_order_data->address ] = $batch_order_data;
				}
			}
		}

		// Analyze Blockcypher response
		$tx_data = self::blockcypher_tx_analysis( $orders, $api_data, $options, $api_context ); // $chain_height);

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

		return self::blockcypher_safe_remote_get( $url, $currency );
	}

	/**
	 * Blockcypher only returns 20 outputs per request.
	 * Request more transaction outputs until we have the required outputs for the payment address.
	 *
	 * @param $transactions
	 * @param $order_data
	 * @param $api_context
	 *
	 * @return mixed
	 */
	public static function merge_related_outputs( $transactions, $order_data, $api_context ) {

		$next_outputs_start = 0;

		foreach ( $transactions as $key => &$transaction ) {
			$outputs       = self::get_outputs_addresses_for_transaction( $transaction );
			$outputs_count = sizeof( $outputs );

			while ( ! in_array( $order_data->address, $outputs ) && $outputs_count == 20 ) {
				$next_outputs_start += $outputs_count;
				$transaction         = self::get_transaction(
					$order_data->payment_currency,
					$transaction->hash,
					array(
						'outstart' => $next_outputs_start,
						'limit'    => $outputs_count,
					),
					$api_context
				);
				$outputs             = self::get_outputs_addresses_for_transaction( $transaction );
				$outputs_count       = sizeof( $outputs );
			}

			if ( ! in_array( $order_data->address, $outputs ) ) {
				unset( $transactions[ $key ] );
			}
		}

		return $transactions;
	}


	/**
	 * Calculate amounts paid to each order matching an address in the Blockcypher response
	 *
	 * @todo Pass $chain_height to check locktime
	 *
	 * @param  $batch_orders
	 * @param  $api_data
	 * @param  $options
	 * @param  bool|false   $api_context
	 * @return mixed
	 */
	public static function blockcypher_tx_analysis( $batch_orders, $api_data, $options, $api_context = false ) {

		$dbupdate      = 0;
		$payment_array = array();
		/*
		if(WP_DEBUG) {
			file_put_contents(CW_LOG_DIR . 'cryptopay-tx-update.log', date('Y-m-d H:i:s') . ' blockcypher tx analysis ' . var_export($api_data, true) . "\r\n", FILE_APPEND);
		} */

		foreach ( $batch_orders as $order_data ) {

			if ( ! isset( $api_data[ $order_data->address ] ) || ( isset( $api_data[ $order_data->address ]->final_n_tx ) && $api_data[ $order_data->address ]->final_n_tx < 1 ) ) {
				$payment_array[ $order_data->order_id ] = array(
					'status'                     => ! is_string( $api_data ) ? 'Blockcypher success | no transactions for address' : sprintf( 'BlockCypher error: %s', $api_data ),
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
			$txs_to_address = $api_data[ $order_data->address ];
			$transactions   = isset( $txs_to_address->txs ) ? $txs_to_address->txs : array();

			$txids                          = array();
			$total_received_unconfirmed_sat = $total_received_confirmed_sat = 0;
			$double_spend                   = false;

			// Make sure we have all outputs to the payment address in this transaction
			$api_context  = self::prepare_blockcypher_api( $order_data->payment_currency, $options, 'tx' );
			$transactions = self::merge_related_outputs( $transactions, $order_data, $api_context );

			// Get confidence for tx batch if the merchant accepts zeroconf, else use 0/1 value from address/full endpoint
			$api_context      = self::prepare_blockcypher_api( $order_data->payment_currency, $options, 'confidence' );
			$confidence       = self::blockcypher_tx_confidence( $transactions, $order_data->payment_currency, $api_context, $options );
			$batch_confidence = $pc_conf['min_confidence'] > 0 && $pc_conf['min_conf'] < 1 ? $confidence : array();

			$count = count( $transactions );

			// Remove duplicate txs from blockcypher api (both same tx but one is unconfirmed and the other is confirmed).
			// TODO: Remove when BlockCypher has Fixed the duplicate txs issue.
			if ( 1 < $count ) {
				$filtered_transactions = array();
				foreach ( $transactions as &$transaction ) {

					if ( ! array_key_exists( $transaction->hash, $filtered_transactions ) ) {
						   // We did not see this hash yet - let's add it to our list.
						   $filtered_transactions[ $transaction->hash ] = $transaction;
					} else {
							  // We already have this transaction in our filtered list.
							  // - let's check if the one we are currently looking at is already confirmed.
							  // if so, replace the previous version of the transaction in the filtered list.
						if ( (bool) ( ( isset( $transaction->confirmations ) && 0 < (int) $transaction->confirmations ) || -1 !== (int) $transaction->block_height ) ) {
							$filtered_transactions[ $transaction->hash ] = $transaction;
						}
					}
				}

				// Overwrite transactions with filtered transactions.
				$transactions = $filtered_transactions;

				// Update transaction count.
				$count = count( $transactions );
			}

			// Only calculate tx amounts if there are txs in the API response
			foreach ( $transactions as $transaction ) {

				// $payment_array[$order_data->order_id]['result'] = sprintf('%s|%s', strtotime($order_data->created_at), strtotime($transaction->received));
				// Only consider the transaction output if the lock time is ok and it is either unconfirmed or the order has been created before the transaction

				// Skip if address reuse (more than one tx or already confirmed tx was sent to the address before the order existed)
				$tx_ts    = strtotime( $transaction->received );
				$is_fresh = (bool) ( ! isset( $transaction->received ) || ! $tx_ts || strtotime( $order_data->created_at ) < ( $tx_ts + 3600 ) );
				if ( ! $is_fresh ) {
					$data = array(
						sprintf( 'possible address reuse detected - ignoring transaction %s', $transaction->hash ) => array(
							'order_created_at' => $order_data->created_at,
							'order_ts'         => strtotime( $order_data->created_at ),
							'tx_ts'            => $tx_ts,
						),
					);
					CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $data, 'notice' );
					continue;
				}

				// Block height = -1 if unconfirmed
				$is_unconfirmed = (bool) ( ( isset( $transaction->confirmations ) && (int) $transaction->confirmations < 1 ) || (int) $transaction->block_height == -1 );

				// Maybe check lock time
				$locktime_ok = $is_unconfirmed && isset( $chain_height ) ? CW_OrderProcessing::check_tx_lock_time( $transaction, $chain_height ) : true;

				if ( $locktime_ok ) {

					if ( $pc_conf['min_confidence'] > 0 ) {
						// Get tx confidence from the batch if the merchant accepts zeroconf, else use 0/1 value from address/full endpoint
						$confidence = isset( $batch_confidence[ $transaction->hash ] ) && is_numeric( $batch_confidence[ $transaction->hash ] ) ? (float) $batch_confidence[ $transaction->hash ] : (float) $transaction->confirmations;
					} else {
						// Raw zeroconf
						$confidence = $transaction->confirmations = 1;
					}
					// Determine age of the transaction
					// $time = strtotime($transaction->received);
					// $tx_age = time() - $time;

					// Add all outputs of the tx that go to the payment address
					$amount_received = self::get_sum_outputs( $order_data, $transaction->outputs );

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
								CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, sprintf( 'replace-by-fee flag detected - no zeroconf for %s', $transaction->hash ), 'info' );
							}
							$transaction->hash .= '-RBF';
						}
						$total_received_unconfirmed_sat += $amount_received;
					}

					if ( ! $is_rbf && (bool) $transaction->double_spend ) {
						$txids[ $transaction->hash ] = "DOUBLESPEND-{$confidence}|{$amount_received}"; // Add notice about double spend to txid array
						$double_spend                = true;
						// Action hook for double spend alert
						do_action( 'cryptowoo_doublespend', $txids );
					} else {
						$txids[ $transaction->hash ] = $amount_received;
					}
				} // Check block height
			} // Foreach transaction
			$total_received_unconfirmed = ! (bool) $double_spend ? $total_received_unconfirmed_sat : 0; // 100000000 : 0;
			$total_received_confirmed   = ! (bool) $double_spend ? $total_received_confirmed_sat : 0; // 100000000 : 0;
			$txids_serialized           = serialize( $txids );

			// Prepare tx update result for order
			$payment_array[ $order_data->order_id ] = array(
				'status'                     => is_array( $api_data ) ? "Blockcypher: {$status}" : sprintf( 'Blockcypher error: %s %s', $status, var_export( $api_data, true ) ),
				'address'                    => $order_data->address,
				'order_id'                   => $order_data->order_id,
				'total_received_confirmed'   => $total_received_confirmed,
				'total_received_unconfirmed' => $total_received_unconfirmed,
				'tx_confidence'              => isset( $confidence ) ? (float) $confidence : 'none',
				'tx_count'                   => $count,
				'txids_serialized'           => $txids_serialized,
			);
			if ( CW_AdminMain::logging_is_enabled( 'debug', $options ) ) {
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . " Blockcypher|#{$order_data->order_id}|{$order_data->address}|{$txids_serialized}", 'debug' );
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
			$output_addresses = isset( $output->addresses ) && is_array( $output->addresses ) ? $output->addresses : array();
			if ( in_array( $order_data->address, $output_addresses ) ) {
				$amount_received += (int) $output->value;
			}
		}
		return (int) $amount_received;
	}
}
