<?php

/**
 * Chain.so API Helper
 */
class CW_ChainSo {


	/**
	 * Get amounts paid for each order via Chain.so
	 *
	 * @param  $currency
	 * @param  $orders
	 * @param  $options
	 * @return mixed
	 */
	public static function chain_so_single_tx_update( $currency, $orders, $options ) {

		$tx_data = array();
		// Get data for the address
		foreach ( $orders as $order ) {
			$response     = self::get_tx_received( $order->address, $currency );
			$api_data     = isset( $response->data ) ? $response->data : array();
			$transactions = isset( $api_data->txs ) ? $api_data->txs : $response; // Error message on failure
			$tx_data[]    = self::chain_so_tx_analysis( $order, $transactions, $options );
		}
		return $tx_data;
	}

	/**
	 * Calculate amounts paid to the address in the Chain.so response
	 *
	 * @todo Check tx locktime and sequence number
	 *
	 * @param  $order_data
	 * @param  $api_data
	 * @param  $options
	 * @return mixed
	 */
	public static function chain_so_tx_analysis( $order_data, $api_data, $options ) {

		$dbupdate                       = 0;
		$payment_array                  = $txids = array();
		$total_received_unconfirmed_sat = $total_received_confirmed_sat = 0;
		$count                          = count( $api_data );

		// Get processing configuration
		$pc_conf = CW_OrderProcessing::get_processing_config( $order_data->payment_currency, $order_data->amount, $options );

		// Only calculate tx amounts if there are txs in the API response
		if ( is_array( $api_data ) && $count > 0 ) {
			foreach ( $api_data as $transaction ) {

				// Skip if address reuse (the transaction was sent to the address and confirmed before the order existed)
				if ( ( $transaction->time + 3600 ) < strtotime( $order_data->created_at ) ) {
					$data = array(
						sprintf( 'possible address reuse detected - ignoring %s', $transaction->txid ) => array(
							'order_created_at' => $order_data->created_at,
							'order_ts'         => strtotime( $order_data->created_at ),
							'tx_ts'            => $transaction->time,
						),
					);
					CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $data, 'notice' );
					continue;
				}

				// If the transaction is unconfirmed check tx sequence number (to prevent RBF)
				// Not supported by chain.so
				/*
				$is_rbf = (int)$transaction->confirmations < 1 ? CW_OrderProcessing::check_input_sequences($transaction) : false;
				if ($is_rbf) {
					$data = sprintf('replace by fee flag detected - ignoring %s', $transaction->txid);
					CW_AdminMain::cryptowoo_log_data(0, __FUNCTION__, $data, 'cryptowoo-tx-update.log');
					continue;
				} */

				if ( $pc_conf['min_confidence'] > 0 ) {
					// Log only lowest confidence value
					$new_confidence = (int) $transaction->confirmations < 1 && $pc_conf['min_conf'] < 1 ? CW_OrderProcessing::get_tx_confidence( $order_data->payment_currency, $transaction->txid, 'chain_so' ) : $transaction->confirmations;
					$confidence     = isset( $confidence ) && $confidence < (float) $new_confidence ? $confidence : (float) $new_confidence;
				} else {
					// Raw zeroconf
					$confidence = $transaction->confirmations = 1;
				}

				// Convert to integer
				$amount = CW_OrderProcessing::cw_float_to_int( $transaction->value );

				// Determine age of the transaction
				// $time = (int)$transaction->time;
				// $tx_age = time() - $time;

				// Add tx amount to total amount received
				// if transaction confidence is good enough and it has more than minimum confirmations
				if ( (float) $confidence >= $pc_conf['min_confidence'] && (int) $transaction->confirmations >= $pc_conf['min_conf'] ) {

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

				$txids[ (string) $transaction->txid ] = $amount;
			} // For each transaction
		} // If we have transactions

		$txids_serialized                       = serialize( $txids );
		$payment_array[ $order_data->order_id ] = array(
			'status'                     => is_array( $api_data ) ? 'Chain.so success' : sprintf( 'Chain.so error: %s', $api_data ),
			'address'                    => $order_data->address,
			'order_id'                   => $order_data->order_id,
			'total_received_confirmed'   => $total_received_confirmed_sat,
			'total_received_unconfirmed' => $total_received_unconfirmed_sat,
			'tx_count'                   => $count,
			'lowest_tx_confidence'       => isset( $confidence ) ? (float) $confidence : 'none',
			'txids_serialized'           => $txids_serialized,
		);

		if ( CW_AdminMain::logging_is_enabled( 'debug', $options ) ) {
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . " Chain.so|#{$order_data->order_id}|$order_data->address|{$txids_serialized}", 'debug' );
		}
		// $timeago = time() - (int)$order_data->last_update;

		// Force order processing if the order will time out within the next 5.5 minutes
		if ( ! isset( $payment_array['force_update'] ) || $payment_array['force_update'] !== 'yes' ) {
			$payment_array['force_update'] = (int) $order_data->timeout_value - time() < 330 ? 'yes' : 'no';
		}

		// Calculate order age
		$payment_array[ $order_data->order_id ]['order_age'] = time() - strtotime( $order_data->created_at );

		// Maybe update order data // TODO move to update_tx_details()
		if ( strpos( $payment_array[ $order_data->order_id ]['status'], 'success' ) || $payment_array['force_update'] === 'yes' ) {

			// Force order processing
			$payment_array['force_update'] = 'yes'; // TODO Revisit force processing

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
		// }
		$payment_array['dbupdate'] = $dbupdate;
		return $payment_array;
	}

	/**
	 * Get transactions for a given address via Chain.so
	 *
	 * @package OrderProcess
	 * @param   $address
	 * @param   $currency
	 * @return  bool|mixed
	 */
	public static function get_tx_received( $address, $currency ) {
		// GET /api/v2/get_tx_received/{NETWORK}/{ADDRESS}[/{AFTER TXID}]

		// Generate cURL URL
		$url = "https://chain.so/api/v2/get_tx_received/{$currency}/{$address}";

		$response = wp_remote_get( $url );
		$result   = CW_OrderProcessing::check_remote_get_response( $response, $currency, 'chain_so', 'data' );
		usleep( 333333 );

		return $result;
	}
}
