<?php

/**
 * Block.io API Helper
 */
class CW_BlockIo {


	/**
	 * Get currencies supported by Block.io
	 *
	 * @return array
	 */
	static function get_supported_currencies() {
		return array( 'BTC', 'LTC', 'DOGE', 'BTCTEST', 'DOGETEST' );
	}

	/**
	 * Get amounts paid to addresses in the current batch via Block.io
	 *
	 * @param  $batch
	 * @param  $currency
	 * @param  $batch_orders
	 * @param  $options
	 * @param  $api_key
	 * @return mixed
	 */
	public static function block_io_batch_tx_update( $batch, $currency, $batch_orders, $options, $api_key ) {

		$received       = new stdClass();
		$received->data = null;
		$tx_data        = $transactions = array();

		$blockio = new BlockIo( $api_key, '' );
		try {
			$received = $blockio->get_transactions(
				array(
					'addresses' => implode( ',', $batch ),
					'type'      => 'received',
				)
			);
		} catch ( Exception $ex ) {
			$status = $ex->getMessage();

			// Action hook for Block.io API error
			do_action( 'cryptowoo_api_error', $status );
			// Update rate limit transient
			$limit_transient              = get_transient( 'cryptowoo_limit_rates' );
			$limit_transient[ $currency ] = isset( $limit_transient[ $currency ]['count'] ) ? array(
				'count' => (int) $limit_transient[ $currency ]['count'] + 1,
				'api'   => 'block_io',
			) : array(
				'count' => 1,
				'api'   => 'block_io',
			);
			// Keep error transient data for 15 minutes. We'll try again after that time.
			set_transient( 'cryptowoo_limit_rates', $limit_transient, 900 );
			if ( CW_AdminMain::logging_is_enabled( 'error', $options ) ) {
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . " Block.io error {$status}", 'error' );
			}
		}

		if ( isset( $received->data->txs ) ) {
			$transactions = $received->data->txs;

			// Create array with recipient address as key
			$api_data = array();
			foreach ( $transactions as $transaction ) {
				if ( isset( $transaction->amounts_received ) && is_array( $transaction->amounts_received ) ) {
					$amounts_received = $transaction->amounts_received;
					foreach ( $amounts_received as $amount_received ) {
						if ( isset( $amount_received->recipient ) ) {
							$api_data[ $amount_received->recipient ][] = $transaction;
						}
					}
				}
			}
			// Analyze Block.io API data
			$tx_data = self::block_io_tx_analysis( $batch_orders, $api_data, $options );
		} else {
			$status = 'Block.io Error in get_transactions';
		}

		return isset( $status ) ? $status : $tx_data;
	}

	/**
	 * Calculate amounts paid to each address in the current batch from Block.io response
	 *
	 * @todo Check tx locktime and sequence number
	 *
	 * @param  $batch_orders
	 * @param  $api_data
	 * @param  $options
	 * @return mixed
	 */
	public static function block_io_tx_analysis( $batch_orders, $api_data, $options ) {
		$dbupdate      = 0;
		$payment_array = array();

		foreach ( $batch_orders as $order_data ) {

			$double_spend                   = false;
			$total_received_unconfirmed_sat = $total_received_confirmed_sat = 0;

			// Get processing configuration
			$pc_conf = CW_OrderProcessing::get_processing_config( $order_data->payment_currency, $order_data->amount, $options );

			$txs = $txids = array();
			if ( ! array_key_exists( $order_data->address, $api_data ) ) {
				$payment_array[ $order_data->order_id ] = array(
					'status'                     => ! is_string( $api_data ) ? 'Block.io success | no transactions for address' : sprintf( 'Block.io error: %s', $api_data ),
					'force_update'               => 'no',
					'address'                    => $order_data->address,
					'order_id'                   => $order_data->order_id,
					'total_received_confirmed'   => 0,
					'total_received_unconfirmed' => 0,
					'tx_count'                   => 0,
					'lowest_tx_confidence'       => 'none',
				);
				CW_OrderProcessing::bump_last_update( $order_data->address, $order_data->order_id );
				continue; // Go to next order if we did not receive API data for this order
			} else {
				$txs[] = $api_data[ $order_data->address ]; // Use only the transactions for this payment address
			}
			$count = count( $txs );
			// Only calculate tx amounts if there are txs in the API response
			if ( $count > 0 ) {
				foreach ( $txs as $transactions ) {
					foreach ( $transactions as $transaction ) {

						// Skip if address reuse (the transaction was sent to the address and confirmed before the order existed)
						if ( ! ( strtotime( $order_data->created_at ) < ( $transaction->time + 3600 ) ) ) {
							CW_AdminMain::cryptowoo_log_data(
								0,
								__FUNCTION__,
								array(
									sprintf( 'possible address reuse detected - ignoring transaction %s', $transaction->txid ) => array(
										'order_created_at' => $order_data->created_at,
										'order_ts'         => strtotime( $order_data->created_at ),
										'tx_ts'            => $transaction->time,
									),
								),
								'notice'
							);
							continue;
						}

						if ( $pc_conf['min_confidence'] > 0 ) {
							// Log only lowest confidence value
							$transaction->confidence = $pc_conf['min_confidence'] > 0 ? $transaction->confidence : 1;
							$confidence              = isset( $confidence ) && $confidence < (float) $transaction->confidence ? $confidence : (float) $transaction->confidence;
						} else {
							// Raw zeroconf
							$transaction->confidence = $transaction->confirmations = 1;
						}
						$amounts_received = $transaction->amounts_received;

						// Determine age of the transaction
						// $time = (int)$transaction->time;
						// $tx_age = time() - $time;

						foreach ( $amounts_received as $amount_received ) {

							$amount = CW_OrderProcessing::cw_float_to_int( $amount_received->amount );

							// Add tx amount to total amount received
							// if transaction confidence is good enough and it has more than minimum confirmations
							// or comes from green address and tx confidence is good enough
							if ( $order_data->address === $amount_received->recipient && ( (float) $transaction->confidence >= $pc_conf['min_confidence'] && ( (int) $transaction->confirmations >= $pc_conf['min_conf'] || (bool) $transaction->from_green_address ) ) ) {

								// Add tx amount to total amount received
								$total_received_confirmed_sat += $amount;
								/*
								if ((int)$transaction->confirmations < 1 && !(bool)$transaction->from_green_address) {
									// Calculate the amount that is not spendable yet
									$not_spendable += $amount;
								} */
							} else {
								$total_received_unconfirmed_sat += $amount;
							}
							/*
							$txid_amount = isset($amount_received->amount) ? $amount_received->amount : '0';
							if ($double_spend) { // todo revisit later
								$txids[(string)$transaction->txid] = "DOUBLESPEND-{$txid_amount}"; // Add notice about double spend to txid array

								// Action hook for double spend alert
								do_action('cryptowoo_doublespend', $txids);
								$double_spend = true;

							} else {
								$txids[(string)$transaction->txid] = $amount_received->amount;
							} */
							$txids[ (string) $transaction->txid ] = $amount_received->amount;
						} // For each amount_received
					} // For each transaction
				} // For each address
			}
			$total_received_unconfirmed             = ! (bool) $double_spend ? $total_received_unconfirmed_sat : 0; // 100000000 : 0;
			$total_received_confirmed               = ! (bool) $double_spend ? $total_received_confirmed_sat : 0; // 100000000 : 0;
			$txids_serialized                       = serialize( $txids );
			$payment_array[ $order_data->order_id ] = array(
				'status'                     => is_array( $api_data ) ? 'Block.io success' : sprintf( 'Block.io error: %s', $api_data ),
				'address'                    => $order_data->address,
				'order_id'                   => $order_data->order_id,
				'total_received_confirmed'   => $total_received_confirmed,
				'total_received_unconfirmed' => $total_received_unconfirmed,
				'tx_count'                   => $count,
				'lowest_tx_confidence'       => isset( $confidence ) ? (float) $confidence : 'none',
				'txids_serialized'           => $txids_serialized,
			);

			if ( CW_AdminMain::logging_is_enabled( 'debug', $options ) ) {
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . " Block.io|#{$order_data->order_id}|$order_data->address|{$txids_serialized}", 'debug' );
			}
			// $timeago = time() - (int)$order_data->last_update;

			// Force order processing if the order will time out within the next 5.5 minutes
			if ( ! isset( $payment_array[ $order_data->order_id ]['force_update'] ) || $payment_array[ $order_data->order_id ]['force_update'] !== 'yes' ) {
				$time     = time();
				$long_ago = (int) $order_data->last_update - $time > 60 ? true : false;
				$payment_array[ $order_data->order_id ]['force_update'] = $long_ago || (int) $order_data->timeout_value - $time < 330 ? 'yes' : 'no';
			}

			// Calculate order age
			$payment_array[ $order_data->order_id ]['order_age'] = time() - strtotime( $order_data->created_at );

			// Maybe update order data // TODO move to update_tx_details()
			if ( strpos( $payment_array[ $order_data->order_id ]['status'], 'success' ) && ( $payment_array[ $order_data->order_id ]['total_received_confirmed'] != $order_data->received_confirmed || $payment_array[ $order_data->order_id ]['total_received_unconfirmed'] != $order_data->received_unconfirmed ) ) {

				// Force order processing
				$payment_array['force_update'] = 'yes';

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
		}
		$payment_array['dbupdate'] = $dbupdate;
		return $payment_array;
	}
}
