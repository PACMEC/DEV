<?php

/**
 * Insight API Helper
 */
class CW_Insight {


	/**
	 * Prepare Insight API for currency
	 *
	 * @param  $currency
	 * @param  $options
	 * @param  bool     $endpoint
	 * @return stdClass
	 */
	public static function prepare_insight_api( $currency, $options = false, $endpoint = false ) {
		if ( ! $options ) {
			$options = get_option( 'cryptowoo_payments' );
		}

		$processing_api_key = 'processing_api_' . strtolower( $currency );
		if ( 'insight' === $options[ $processing_api_key ] ) {
			$field   = Redux::get_field( 'cryptowoo_payments', $processing_api_key );
			$api_url = isset( $field['options'] ) && isset( $field['options']['insight'] ) ? $field['options']['insight'] : '';

			$options[ 'custom_api_' . strtolower( $currency ) ] = 'http://' . $api_url . '/api/';
			! $api_url ?: $options[ $processing_api_key ]       = 'custom';
		}

		$insight      = new stdClass();
		$insight_key  = sprintf( 'custom_api_%s', strtolower( $currency ) );
		$insight->url = CW_Validate::check_if_unset( $insight_key, $options, false );
		if ( false !== $insight->url ) {
			$urls         = $endpoint ? CW_Formatting::format_insight_api_url( $insight->url, $endpoint ) : CW_Formatting::format_insight_api_url( $insight->url, '' );
			$insight->url = $urls['surl'];
		} else {
			$insight = apply_filters( 'cw_prepare_insight_api', $insight, $endpoint, $currency, $options );
		}
		return $insight;
	}

	/**
	 * Return amounts paid to addresses in the current batch via Insight API
	 *
	 * @param  $batch_currency
	 * @param  $batch
	 * @param  $orders
	 * @param  $options
	 * @return mixed
	 */
	public static function insight_batch_tx_update( $batch_currency, $batch, $orders, $options ) {

		$api_data = array();

		// Prepare API
		$api_context = self::prepare_insight_api( $batch_currency, $options, 'addrs/%s/txs' );
		if ( ! $api_context ) {
			return array();
		}

		// Get block height (for checking the locktime) TODO Move somewhere else
		$chain_height = 0; // self::get_block_height($batch_currency); // FIXME A valid url was nopt provided

		// Get data for currency batch
		$full_address_batch = self::insight_full_address( $batch, $batch_currency, $api_context );

		if ( is_object( $full_address_batch ) && isset( $full_address_batch->items ) ) {
			// Batch has items property
			$full_address_batch = $full_address_batch->items;

		} elseif ( is_string( $full_address_batch ) ) {
			// Maybe return error message
			return $full_address_batch;
		}

		// Prepare data for each order
		foreach ( $full_address_batch as $batch_order_data ) {
			$outputs = isset( $batch_order_data->vout ) && is_array( $batch_order_data->vout ) ? $batch_order_data->vout : array();
			foreach ( $outputs as $output ) {
				if ( isset( $output->scriptPubKey->addresses ) && is_array( $output->scriptPubKey->addresses ) ) {
					foreach ( $output->scriptPubKey->addresses as $key => $address ) {
						$api_data[ $address ][] = $batch_order_data;
					}
				}
			}
		}

		// Analyze Insight response
		$tx_data = self::insight_tx_analysis( $orders, $api_data, $options, $chain_height, $api_context );

		return $tx_data;
	}

	/**
	 * Request amounts paid to addresses in the current batch via Insight API
	 *
	 * @param  $batch
	 * @param  $currency
	 * @param  $api_context
	 * @return array|bool|mixed|object|string|WP_Error
	 */
	public static function insight_full_address( $batch, $currency, $api_context ) {
		$error = $fullAddress = false;

		// Rate limit transient
		$limit_transient = get_transient( 'cryptowoo_limit_rates' );

		// Get data
		$url = sprintf( $api_context->url, implode( ',', $batch ) );

		$fullAddress = wp_remote_get( $url );

		if ( is_wp_error( $fullAddress ) ) {

			$error = $fullAddress->get_error_message() . $url;

			// Action hook for Insight API error
			do_action( 'cryptowoo_api_error', 'Insight ' . $currency . ' API error: ' . $error );

			// Update rate limit transient
			if ( isset( $limit_transient[ $currency ]['count'] ) ) {
				$limit_transient[ $currency ] = array(
					'count' => (int) $limit_transient[ $currency ]['count'] + 1,
					'api'   => 'insight',
				);
			} else {
				$limit_transient[ $currency ] = array(
					'count' => 1,
					'api'   => 'insight',
				);
			}
			// Keep error data until the next full hour (rate limits refresh every full hour). We'll try again after that time.
			set_transient( 'cryptowoo_limit_rates', $limit_transient, CW_AdminMain::seconds_to_next_hour() );
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . " Insight full address error {$error}", 'error' );
		} else {
			$fullAddress = json_decode( $fullAddress['body'] );
		}
		// Delete rate limit transient if the last call was successful
		if ( false !== $limit_transient && false === $error ) {
			delete_transient( 'cryptowoo_limit_rates' );
		}
		return false !== $error ? $error : $fullAddress;
	}

	/**
	 * Calculate amounts paid to each order matching an address in the Insight API response
	 *
	 * @param  $batch_orders
	 * @param  $api_data
	 * @param  $options
	 * @param  $chain_height
	 * @param  bool|false   $api_context
	 * @return mixed
	 */
	public static function insight_tx_analysis( $batch_orders, $api_data, $options, $chain_height, $api_context = false ) {

		$dbupdate      = 0;
		$payment_array = array();

		foreach ( $batch_orders as $order_data ) {

			if ( ! isset( $api_data[ $order_data->address ] ) ) {
				$payment_array[ $order_data->order_id ] = array(
					'status'                     => ! is_string( $api_data ) ? 'Insight success | no transactions for address' : sprintf( 'Insight error: %s', $api_data ),
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

			if ( ! $api_context ) {
				$api_context = self::prepare_insight_api( $order_data->payment_currency, $options ); // TODO maybe remove
			}

			// Get processing configuration
			$pc_conf = CW_OrderProcessing::get_processing_config( $order_data->payment_currency, $order_data->amount, $options );

			$status = is_array( $api_data ) && isset( $full_address['message'] ) ? $full_address['message'] : 'success';

			// Prepare transaction data for the payment address of this order
			$transactions = isset( $api_data[ $order_data->address ] ) ? $api_data[ $order_data->address ] : array();

			$txids                          = array();
			$total_received_unconfirmed_sat = $total_received_confirmed_sat = 0;
			$double_spend                   = false;

			// Only calculate tx amounts if there are txs in the API response
			foreach ( $transactions as $transaction ) {

				// Number of confirmations
				$num_conf = ! isset( $transaction->confirmations ) || (int) $transaction->confirmations < 1 ? 0 : (int) $transaction->confirmations;

				if ( isset( $pc_conf['instant_send'] ) && $pc_conf['instant_send'] && isset( $transaction->txlock ) && $transaction->txlock === true ) {
					$num_conf += $pc_conf['instant_send_depth'];
					$msg       = sprintf( __( 'InstantSend detected: %1$d + %2$d %3$s', 'cryptopay' ), (int) $num_conf, (int) $pc_conf['instant_send_depth'], $transaction->hash );
					CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $msg, 'debug' );
				}

				// Check lock time
				$locktime_ok = $chain_height && $num_conf < 1 ? self::insight_check_tx_lock_time( $transaction, $chain_height ) : true;
				// TODO adapt when tx-punisher is merged, otherwise zeroconf transactions will never be considered here
				// Only consider the transaction output if the lock time is ok and it is either unconfirmed or the order has been created before the transaction
				if ( $locktime_ok && ( $num_conf < 1 || ( isset( $transaction->time ) && strtotime( $order_data->created_at ) < ( $transaction->time + 3600 ) ) ) ) { // block height if unconfirmed

					if ( $pc_conf['min_confidence'] > 0 ) {
						// Maybe get tx confidence via chain.so. use the number of confirmations if the transaction is already confirmed
						$confidence = $num_conf < 1 && $pc_conf['min_conf'] < 1 && (bool) $options['custom_api_confidence'] ? CW_OrderProcessing::get_tx_confidence( $order_data->payment_currency, $transaction->txid, 'chain_so' ) : $num_conf;
					} else {
						// Raw zeroconf
						$confidence = $num_conf = 1;
					}

					// Add all outputs of the tx that go to the payment address
					$amount_received = self::insight_get_sum_outputs( $order_data, $transaction->vout );

					// If the transaction is unconfirmed check tx sequence number (to prevent RBF)
					$is_rbf = $num_conf ? CW_OrderProcessing::check_input_sequences( $transaction ) : false;

					// Add tx amount to total amount received
					// if transaction confidence is good and the sequence number is final
					// or it has more than the required minimum confirmations
					if ( ( ! $is_rbf || $num_conf > 0 ) && $confidence >= $pc_conf['min_confidence'] && $num_conf >= $pc_conf['min_conf'] ) {

						// Add tx amount to total amount received
						$total_received_confirmed_sat += $amount_received;

					} else {
						if ( $is_rbf ) {
							CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, sprintf( 'replace-by-fee flag detected - no zeroconf for %s', $transaction->txid ), 'info' );
						}
						$total_received_unconfirmed_sat += $amount_received;
					}

					if ( ! $is_rbf && isset( $transaction->doubleSpentTxID ) && (bool) $transaction->doubleSpentTxID ) {
						$txids[ $transaction->hash ] = "DOUBLESPEND-{$confidence}|{$amount_received}|{$transaction->doubleSpentTxID}"; // Add notice about double spend to txid array
						$double_spend                = true;
						// Action hook for double spend alert
						do_action( 'cryptowoo_doublespend', $txids );
					}
					// Record transaction
					$txids[ $transaction->txid ] = $amount_received;
				} // Check block height
			} // Foreach transaction
			$total_received_unconfirmed = ! (bool) $double_spend ? $total_received_unconfirmed_sat : 0; // 100000000 : 0;
			$total_received_confirmed   = ! (bool) $double_spend ? $total_received_confirmed_sat : 0; // 100000000 : 0;
			$txids_serialized           = serialize( $txids );

			// Prepare tx update result for order
			$payment_array[ $order_data->order_id ] = array(
				'status'                     => is_array( $api_data ) ? "Insight: {$status}" : sprintf( 'Insight error: %s %s', $status, var_export( $api_data, true ) ),
				'address'                    => $order_data->address,
				'order_id'                   => $order_data->order_id,
				'total_received_confirmed'   => $total_received_confirmed,
				'total_received_unconfirmed' => $total_received_unconfirmed,
				'tx_confidence'              => isset( $confidence ) ? (float) $confidence : 'none',
				'txids_serialized'           => $txids_serialized,
			);
			if ( CW_AdminMain::logging_is_enabled( 'debug', $options ) ) {
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . " Insight|#{$order_data->order_id}|{$order_data->address}|{$txids_serialized}", 'debug' );
			}
			// $timeago = time() - (int)$order_data->last_update;

			// Force order processing if the order will time out within the next 5.5 minutes
			if ( ! isset( $payment_array[ $order_data->order_id ]['force_update'] ) || $payment_array[ $order_data->order_id ]['force_update'] !== 'yes' ) {
				$time     = time();
				$long_ago = $time - (int) $order_data->last_update > 60 ? true : false;
				$payment_array[ $order_data->order_id ]['force_update'] = $long_ago || (int) $order_data->timeout_value - $time < 330 ? 'yes' : 'no';
			}

			// Calculate order age
			$payment_array[ $order_data->order_id ]['order_age'] = time() - strtotime( $order_data->created_at );

			// Maybe update order data // TODO move to update_tx_details()
			if ( true || strpos( $payment_array[ $order_data->order_id ]['status'], 'success' ) && ( $payment_array[ $order_data->order_id ]['total_received_confirmed'] != $order_data->received_confirmed || $payment_array[ $order_data->order_id ]['total_received_unconfirmed'] != $order_data->received_unconfirmed ) ) {

				// Force order processing since we have new tx data
				$payment_array[ $order_data->order_id ]['force_update'] = 'yes';

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
	 * Add up all output amounts in "vout" from Insight API response objects and convert to integer.
	 *
	 * @param  $order_data
	 * @param  array      $outputs
	 * @return int
	 */
	public static function insight_get_sum_outputs( $order_data, $outputs = array() ) {
		$amount_received = 0;

		foreach ( $outputs as $output ) {
			$output_addresses = isset( $output->scriptPubKey->addresses ) && is_array( $output->scriptPubKey->addresses ) ? $output->scriptPubKey->addresses : array();
			if ( in_array( $order_data->address, $output_addresses ) ) {
				$amount_received += (int) CW_OrderProcessing::cw_float_to_int( (float) $output->value ); // To Satoshi
			}
		}
		return $amount_received;
	}

	/**
	 * Prevent acceptance of timelocked transactions by checking against the current block height
	 *
	 *  // Discourage fee sniping. https://github.com/bitcoin/bitcoin/blob/master/src/wallet/wallet.cpp#L1975
	 *
	 *    For a large miner the value of the transactions in the best block and
	 *    the mempool can exceed the cost of deliberately attempting to mine two
	 *    blocks to orphan the current best block. By setting nLockTime such that
	 *    only the next block can include the transaction, we discourage this
	 *    practice as the height restricted and limited blocksize gives miners
	 *    considering fee sniping fewer options for pulling off this attack.
	 *
	 *    A simple way to think about this is from the wallet's point of view we
	 *    always want the blockchain to move forward. By setting nLockTime this
	 *    way we're basically making the statement that we only want this
	 *    transaction to appear in the next block; we don't want to potentially
	 *    encourage reorgs by allowing transactions to appear at lower heights
	 *    than the next block in forks of the best chain.
	 *
	 *    Of course, the subsidy is high enough, and transaction volume low
	 *    enough, that fee sniping isn't a problem yet, but by implementing a fix
	 *    now we ensure code won't be written that makes assumptions about
	 *    nLockTime that preclude a fix later.
	 *
	 *    txNew.nLockTime = chainActive.Height();
	 *
	 *    Secondly occasionally randomly pick a nLockTime even further back, so
	 *    that transactions that are delayed after signing for whatever reason,
	 *    e.g. high-latency mix networks and some CoinJoin implementations, have
	 *    better privacy.
	 *
	 *    if (GetRandInt(10) == 0)
	 *          txNew.nLockTime = std::max(0, (int)txNew.nLockTime - GetRandInt(100));
	 *
	 * @param  $transaction
	 * @param  $chain_height
	 * @return bool
	 */
	static function insight_check_tx_lock_time( $transaction, $chain_height ) {
		return (int) $transaction->locktime <= ( (int) $chain_height + 1 ); // Height +1 for the next block from now
	}

	/**
	 * Get the current block height
	 *
	 * @param  $currency
	 * @return int
	 */
	static function get_block_height( $currency ) {

		$bh_transient = sprintf( 'block-height-%s', $currency );
		if ( false !== ( $block_height = get_transient( $bh_transient ) ) ) {
			return (int) $block_height;
		}
		$api_context = self::prepare_insight_api( $currency, false, 'status?q=getInfo' );

		// Get data
		$getinfo = wp_remote_get( sprintf( $api_context->url ) );

		$error = '';

		if ( is_wp_error( $getinfo ) ) {

			$error = $getinfo->get_error_message();

			// Action hook for Insight API error
			do_action( 'cryptowoo_api_error', 'Insight API error: ' . $error );

			// Update rate limit transient
			$limit_transient[ $currency ] = isset( $limit_transient[ $currency ]['count'] ) ? array(
				'count' => (int) $limit_transient[ $currency ]['count'] + 1,
				'api'   => 'insight',
			) : array(
				'count' => 1,
				'api'   => 'insight',
			);
			// Keep error data until the next full hour (rate limits refresh every full hour). We'll try again after that time.
			set_transient( 'cryptowoo_limit_rates', $limit_transient, CW_AdminMain::seconds_to_next_hour() );

		} else {
			$getinfo = json_decode( $getinfo['body'] );
		}

		if ( isset( $getinfo->info ) && isset( $getinfo->info->blocks ) ) {
			$block_height = $getinfo->info->blocks;
			set_transient( $bh_transient, $block_height, 180 ); // Cache for 3 minutes
		} else {
			$block_height = 0;
		}

		if ( (bool) $error ) {
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . " Insight get_block_height {$error}", 'error' );
		}
		return (int) $block_height;
	}
}
