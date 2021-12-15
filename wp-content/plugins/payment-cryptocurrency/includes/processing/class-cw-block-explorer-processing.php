<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
} // Exit if accessed directly.

/**
 * CryptoWoo Block Explorer Processing
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage Processing
 * @author     CryptoWoo AS
 */
class CW_Block_Explorer_Processing {


	/**
	 *
	 * Exchange Rate Tools class
	 *
	 * @var CW_Block_Explorer_Tools $tools
	 */
	private $tools;

	/**
	 *
	 * Constructor for CW_ExchangeRate_Processing
	 *
	 * CW_ExchangeRate_Processing constructor.
	 *
	 * @param CW_Block_Explorer_Tools $tools Exchange Rate Tools class instance.
	 */
	public function __construct( $tools = null ) {
		if ( ! $tools ) {
			$tools = CW_OrderProcessing::block_explorer_tools();
		}
		$this->tools = $tools;
	}

	/**
	 *
	 * Get the txs from block explorer api by currency and WC CW orders.
	 *
	 * @param string                    $batch_currency The currency code (e.g BTC).
	 * @param CW_Payment_Details_Object $orders         WC CryptoWoo Orders array.
	 * @param array|false               $processing     TX API config array.
	 *
	 * @return array|false|mixed|void|null
	 */
	public function get_txs_from_api( $batch_currency, $orders, $processing = false ) {
		$method = $block_explorer_api = $this->tools->get_preferred_block_explorer( $batch_currency );
		$txs    = array();

		if ( 'disabled' === $block_explorer_api ) {
			$status = 'No processing API selected for ' . $batch_currency;
		} else {
			$status           = null;
			$method           = $block_explorer_api;
			$fallback_enabled = cw_get_option( 'processing_fallback' );

			do {
				try {
					$class = $this->tools->get_block_explorer_instance_by_id( $method, $batch_currency );

					if ( $class instanceof CW_Block_Explorer_Base ) {
						$addr_batch = false;
						foreach ( $orders as $order ) {
							   $addr_batch[ $order->get_order_id() ] = $order->get_address();
						}
						$class = $this->tools->get_block_explorer_instance_by_id( $method, $batch_currency, $addr_batch );
						$txs   = $class->get_txs() ?: array( 'txs' => array() );
						if ( ! empty( $class->get_skipped_addresses() ) ) {
							$txs['skipped_addresses'] = $class->get_skipped_addresses();
						}
						// Get the blockchain block height for checking locktime during tx analysis.
						$txs['chain_height'] = $class->get_block_height();
						// Calculate the number of confirmations if we do not have it already.
						foreach ( $txs['txs'] as & $txs_for_address ) {
							foreach ( $txs_for_address as & $tx ) {
								if ( ! isset( $tx->confirms ) ) {
									// We double check that both chain height and tx block height are positive numbers for extra safety.
									if ( is_numeric( $txs['chain_height'] ) && $txs['chain_height'] > 0 && is_numeric( $tx->height ) && $tx->height > 0 ) {
										// When tx height and chain height is identical it is 1 confirm so we increase by 1.
										$tx->confirms = ( $txs['chain_height'] - $tx->height ) + 1;
									} else {
										// If there is no block height in the tx we assume it has 0 confirmations.
										$tx->confirms = 0;
									}
								}
							}
						}
					} else {
						// Added for backwards compatibility for addons with custom block explorer API.
						// TODO: Remove this when no more usage in addons!
						$orders_batch = array();
						foreach ( $orders as $order_data ) {
							$orders_batch[ $order_data->get_order_id() ] = $order_data->get_current_row();
						}
						$batch_data = apply_filters( 'cw_update_tx_details', array(), $batch_currency, $orders_batch, $processing, cw_get_options() );
						if ( ! isset( $batch_data ) || ! count( $batch_data ) ) {
							$batch_data = array( "{$batch_currency}_API" => sprintf( 'Nothing new from %s', $method ) );
							CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $batch_data, 'debug' );
						}
						$txs = $batch_data;
					}
				} catch ( InvalidArgumentException $exception ) {
					$message = "Invalid arguments were given for $method with currency $batch_currency, attempting fallback to other api";
					CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $message, 'debug' );
					$fallback_enabled = true;
				}

				$status = isset( $txs['status'] ) ? $txs['status'] : "could not get transactions from $method api";

				if ( false !== strpos( $status, 'success' ) ) {
					break;
				} else {

					// Stop trying if the fallback option is disabled.
					if ( ! $fallback_enabled ) {
						break;
					}

					if ( ! isset( $fallback_explorers ) ) {
						$fallback_explorers = $this->tools->get_preferred_block_explorers_names( $batch_currency );
						$fallback_explorers = array_combine( $fallback_explorers, $fallback_explorers );
						unset( $fallback_explorers[ $method ] );
					}

					if ( empty( $fallback_explorers ) ) {
						break;
					}

					$method = array_shift( $fallback_explorers );
				}
			} while ( false === strpos( $status, 'success' ) );
		}

		if ( ! isset( $txs['status'] ) ) {
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $status, 'error' );
			$txs['status'] = $status;
		}
		$txs['api_name'] = $method;

		return $txs;
	}

	/**
	 * Update transaction data for addresses belonging to open orders during cron
	 * Orders with a smaller average block time and the nearest timeout value take priority.
	 * If we run into API limits, maybe use Block.io API as fallback.
	 * If we don't have Block.io API keys for this currency or Block.io is failing, use single-address polling via SoChain.
	 *
	 * @param CW_Payment_Details_Object[][]|false $orders Woocommerce order objects array.
	 *
	 * @return  array
	 * @package OrderProcess
	 */
	public function update_tx_details( $orders = false ) {
		$orders ?: $orders = self::batch_up_orders_per_currency(); // $orders = (array) json_decode( '{"batches":{"BTC":["1FQMb2btmG4Rf1ttipYyqiwkxw9EJjijog"]},"BTC":[{"id":"46","amount":"26.96000000","crypto_amount":"256000","address":"1FQMb2btmG4Rf1ttipYyqiwkxw9EJjijog","payment_currency":"BTC","customer_reference":"319-wc_order_aWwuhR7QxaPzY","order_id":"319","received_confirmed":"256000","received_unconfirmed":"0","created_at":"2019-11-23 09:31:22","last_update":"2020-03-12 19:07:00","timeout_value":"1774503282","timeout":"0","txids":"a:2:{s:64:\"efcd32b5ce54db580e674106b73fbb432c03a659d37ab6f2b55a017047c4e62a\";i:0;s:64:\"d7cedcacaffd6f58453895475ae772d4c2e6a6a564eb0e01e6a0e87ba7b36c32\";i:256000;}","paid":"0","is_archived":"0"}],"count":1}' );

		// TODO: Remove this when no more usage in addons!
		$limit_transient   = get_transient( 'cryptowoo_limit_rates' );
		$last_tx_update_db = get_option( 'cryptowoo_last_tx_update' );
		$last_tx_update    = $last_tx_update_db ? $last_tx_update_db : array();

		if ( isset( $orders['count'] ) && $orders['count'] > 0 ) {
			$batch_data = array();
			if ( isset( $orders['batches'] ) ) {
				// Get txs from api and analyze each order one by one. TODO: Revert when we support multiple addresses per api call.
				foreach ( $orders['batches'] as $batch_currency => $batch ) {
					$payment_details_array = array();
					foreach ( $batch as $order_id => $payment_details ) {
						$payment_details_array[ $order_id ] = $payment_details->get_current_row();
					}
					foreach ( $payment_details_array as $payment_details_row ) {
						$payment_details = new CW_Payment_Details_Object( $payment_details_row );
						$batch_data      = self::get_txs_from_api( $batch_currency, $payment_details );
						// TODO: what to do with this? $last_tx_update[ $processing->tx_update_api ] = time();

						// Analyse tx data from api and update cryptowoo and woocommerce database.
						self::tx_analysis( $payment_details, $batch_data );
					}
				}
			}
			$update_stats = array(
				'api_error_transient' => $limit_transient,
				'payment_data'        => $batch_data,
			);
		} else {
			// We don't have unpaid addresses.
			$update_stats['info'] = esc_html__( 'No unpaid addresses found', 'cryptowoo' );
		}
		update_option( 'cryptowoo_last_tx_update', $last_tx_update );

		return $update_stats;
	}

	/**
	 * Calculate amounts paid to each order matching an address in the api response
	 *
	 * @param CW_Payment_Details_Object $batch_orders Existing payment details object.
	 * @param array                     $api_data     TX update API result array.
	 * @param bool|false                $api_context
	 *
	 * @return mixed
	 */
	public static function tx_analysis( $batch_orders, $api_data, $api_context = false ) {
		$dbupdate      = 0;
		$payment_array = array();

		foreach ( $batch_orders as $order_data ) {

			// Addresses are skipped if the block explorer api supports less addresses than provided.
			if ( isset( $api_data['skipped_addresses'] ) && in_array( $order_data->get_address(), $api_data['skipped_addresses'], true ) ) {
				$data = sprintf( '%s api address limit reached - skipped address %s', $api_data['api_name'], $order_data->get_address() );
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $data, 'info' );
				continue;
			}

			// If address was not skipped and there is no data it means the api detected no txs.
			if ( ! isset( $api_data['txs'][ $order_data->get_address() ] ) ) {
				$payment_array[ $order_data->get_order_id() ] = array(
					'status'                     => isset( $api_data['status'] ) ? $api_data['status'] : "{$api_data['api_name']} success | no transactions for address",
					'force_update'               => 'no',
					'address'                    => $order_data->get_address(),
					'order_id'                   => $order_data->get_order_id(),
					'total_received_confirmed'   => 0,
					'total_received_unconfirmed' => 0,
					'tx_count'                   => 0,
				);
				CW_Database_CryptoWoo::bump_last_update( $order_data->get_order_id() );
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, "No transactions for address {$order_data->get_address()} detected.", 'info' );
				continue; // Go to next order if we did not receive data for this address.
			}

			// Get processing configuration.
			$pc_conf = self::get_processing_config( $order_data->get_payment_currency(), $order_data->get_fiat_amount() );

			$status = $api_data['status'];

			// Prepare transaction data for the payment address of this order.
			$transactions = $api_data['txs'][ $order_data->get_address() ];

			$txids                          = array();
			$total_received_unconfirmed_sat = $total_received_confirmed_sat = 0;
			$double_spend                   = false;
			$count                          = count( $transactions );

			// Only calculate tx amounts if there are txs in the API response.
			foreach ( $transactions as $transaction ) {
				// Skip if address reuse (more than one tx or already confirmed tx was sent to the address before the order existed).
				$tx_ts    = $transaction->time;
				$is_fresh = ! $tx_ts || $order_data->get_created_at() < ( $tx_ts + 3600 );
				if ( ! $is_fresh ) {
					$data = array(
						sprintf( 'possible address reuse detected - ignoring transaction %s', $transaction->id ) => array(
							'order_created_at' => $order_data->get_created_at(),
							'order_ts'         => $order_data->get_last_update(),
							'tx_ts'            => $tx_ts,
						),
					);
					CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $data, 'notice' );
					continue;
				}

				// Instant transactions (secure 0-conf, eg Dash instantSend).
				if ( isset( $pc_conf['instant_send'] ) && $pc_conf['instant_send'] && isset( $transaction->instant ) && true === $transaction->instant ) {
					$transaction->confirms += $pc_conf['instant_send_depth'];
					$msg                    = sprintf( __( 'InstantSend detected: %1$d + %2$d %3$s', 'cryptowoo' ), $transaction->confirms, $pc_conf['instant_send_depth'], $transaction->hash );
					CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $msg, 'debug' );
				}

				// Confirms = less than 1 or Block height = -1 if unconfirmed.
				$is_unconfirmed = isset( $transaction->confirms ) && $transaction->confirms < 1 || isset( $transaction->height ) && -1 === (int) $transaction->height;

				// Maybe check lock time.
				if ( $is_unconfirmed && ! empty( $transaction->locktime ) && ! empty( $api_data['chain_height'] ) ) {
					$locktime_ok = self::check_tx_lock_time( $transaction, $api_data['chain_height'] );
				} else {
					$locktime_ok = true;
				}

				if ( $locktime_ok ) {

					if ( $pc_conf['min_confidence'] > 0 ) {
						// Get tx confidence from the batch if the merchant accepts zeroconf, else use 0/1 value from address/full endpoint.
						$confidence = isset( $transaction->confidence ) && is_numeric( $transaction->confidence ) ? (float) $transaction->confidence : (float) $transaction->confirms;
					} else {
						// Raw zeroconf.
						$confidence = $transaction->confirms = 1;
					}

					// Add all outputs of the tx that go to the payment address.
					$amount_received = $transaction->amount;

					// If a BTC transaction is unconfirmed check tx sequence number to prevent RBF.
					if ( 'BTC' === $order_data->get_payment_currency() ) {
						$sequences = $transaction->sequences ?? array();
						$is_rbf    = $is_unconfirmed && self::check_input_sequences( $sequences );
					} else {
						$is_rbf = false;
					}

					// Add tx amount to total amount received if transaction confidence is good or it has more than the required minimum confirmations.
					if ( ( ( $is_unconfirmed && ! $is_rbf && $confidence >= (float) $pc_conf['min_confidence'] ) || ! $is_unconfirmed ) && (int) $transaction->confirms >= $pc_conf['min_conf'] ) {

						// Add tx amount to total amount received.
						$total_received_confirmed_sat += $amount_received;
					} else {
						if ( $is_rbf ) {
							if ( CW_AdminMain::logging_is_enabled( 'info' ) ) {
								CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, sprintf( 'replace-by-fee flag detected - no zeroconf for %s', $transaction->id ), 'info' );
							}
							$transaction->id .= '-RBF';
						}
						$total_received_unconfirmed_sat += $amount_received;
					}

					if ( ! $is_rbf && isset( $transaction->doublespend ) && $transaction->doublespend ) {
						$txids[ $transaction->id ] = "DOUBLESPEND-{$confidence}|{$amount_received}"; // Add notice about double spend to txid array
						$double_spend              = true;
						// Action hook for double spend alert.
						do_action( 'cryptowoo_doublespend', $txids );
					} else {
						$txids[ $transaction->id ] = $amount_received;
					}
				} else {
					$msg = sprintf( 'Invalid locktime detected - ignoring transaction %s', $transaction->id );
					CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $msg, 'info' );
				} // Check block height
			} // Foreach transaction
			$total_received_unconfirmed = ! (bool) $double_spend ? $total_received_unconfirmed_sat : 0; // 100000000 : 0;
			$total_received_confirmed   = ! (bool) $double_spend ? $total_received_confirmed_sat : 0; // 100000000 : 0;
			$txids_json_encoded         = wp_json_encode( $txids );

			// Prepare tx update result for order.
			$payment_array[ $order_data->get_order_id() ] = array(
				'status'                     => is_array( $api_data ) ? "{$api_data['api_name']}: {$status}" : sprintf( '%s error: %s %s', $api_data['api_name'], $status, var_export( $api_data, true ) ),
				'address'                    => $order_data->get_address(),
				'order_id'                   => $order_data->get_order_id(),
				'total_received_confirmed'   => $total_received_confirmed,
				'total_received_unconfirmed' => $total_received_unconfirmed,
				'tx_confidence'              => isset( $confidence ) ? (float) $confidence : 'none',
				'tx_count'                   => $count,
				'txids_json_encoded'         => $txids_json_encoded,
			);
			if ( CW_AdminMain::logging_is_enabled( 'debug' ) ) {
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . " {$api_data['api_name']}|#{$order_data->get_order_id()}|{$order_data->get_address()}|{$txids_json_encoded}", 'debug' );
			}
			// $timeago = time() - (int)$order_data->get_last_update();

			// Force order processing if the order will time out within the next 5.5 minutes.
			if ( ! isset( $payment_array[ $order_data->get_order_id() ]['force_update'] ) || $payment_array[ $order_data->get_order_id() ]['force_update'] !== 'yes' ) {
				$time     = time();
				$long_ago = $time - (int) $order_data->get_last_update() > 60 ? true : false;
				$payment_array[ $order_data->get_order_id() ]['force_update'] = $long_ago || ( (int) $order_data->get_timeout_timestamp() - $time ) < 330 ? 'yes' : 'no';
			}

			// Maybe update order data // TODO move to update_tx_details() ?
			if ( false !== strpos( $payment_array[ $order_data->get_order_id() ]['status'], 'success' ) || $payment_array[ $order_data->get_order_id() ]['force_update'] === 'yes' ) {

				// Force order processing since we have new tx data.
				$payment_array[ $order_data->get_order_id() ]['force_update'] = 'yes'; // TODO Revisit force order update.

				// Update payments table.
				$cwdb      = CW_Database_CryptoWoo::instance( $order_data->get_order_id() );
				$dbupdate += $cwdb->set_address( $order_data->get_address() )
					->set_received_confirmed( $payment_array[ $order_data->get_order_id() ]['total_received_confirmed'] )
					->set_received_unconfirmed( $payment_array[ $order_data->get_order_id() ]['total_received_unconfirmed'] )
					->set_tx_ids( $txids )
					->update();

				// Update order meta.
				CW_Order_Processing::instance( $order_data->get_order_id() )->update_order_meta_by_cryptowoo_table();
			} else {
				$payment_array[ $order_data->get_order_id() ] = array_merge(
					$payment_array[ $order_data->get_order_id() ],
					array(
						'timeout_in' => $order_data->get_timeout_timestamp() - time(),
						'timeout'    => $order_data->get_timeout(),
					)
				);
			}
			unset( $txids_json_encoded );
		}
		$payment_array['dbupdate'] = $dbupdate;

		return $payment_array;
	}

	/**
	 *
	 * Get minimum number of confirmations for $currency while honoring max order amount threshold for zeroconf acceptance
	 *
	 * @param string    $currency     Payment currency shortcode (eg. BTC).
	 * @param float|int $order_amount Order amount due in fiat.
	 *
	 * @return  array
	 * @package OrderProcess
	 * @todo    More fine grained confirmation requirements for different order amounts
	 */
	public static function get_processing_config( $currency, $order_amount ) {
		$lc_currency = strtolower( str_replace( 'TEST', '', $currency ) );

		$min_conf_key       = "cryptowoo_{$lc_currency}_min_conf"; // Confirmations.
		$max_amount_key     = "cryptowoo_max_unconfirmed_{$lc_currency}"; // Order amount threshold.
		$min_confidence_key = "min_confidence_{$lc_currency}"; // Transaction confidence (if applicable for currency).
		$raw_zeroconf_key   = "{$lc_currency}_raw_zeroconf"; // Raw zeroconf (if applicable for currency).

		$pc_conf['min_conf'] = (int) cw_get_option( $min_conf_key ?: 1 );

		// Force minimum one confirmation at BLK // TODO Refactor.
		if ( 'BLK' === $currency && $pc_conf['min_conf'] < 1 ) {
			$pc_conf['min_conf'] = 1;
		}

		if ( cw_get_option( $raw_zeroconf_key ) ) {
			$pc_conf['min_confidence'] = 0;
		} elseif ( cw_get_option( $min_confidence_key ) ) {
			$pc_conf['min_confidence'] = (float) cw_get_option( $min_confidence_key ) / 100 ?: 0.9895;
		}

		$pc_conf = apply_filters( 'cw_get_processing_config', $pc_conf, $currency, cw_get_options() );

		// Require at least one confirmation if the order amount is above the threshold TODO Add more options.
		$max_amount = cw_get_option( $max_amount_key ) ?: 100;
		if ( $pc_conf['min_conf'] < 1 && $max_amount > 0 && (float) $order_amount >= $max_amount ) {
			$pc_conf['min_conf'] ++;
		}

		// CW_AdminMain::cryptowoo_log_data(0, __FUNCTION__,  cw_get_option( $min_conf_key), 'cryptowoo-cron.log');
		return $pc_conf;
	}

	/**
	 *
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
	 * @param  stdClass $transaction  Transaction object from api result.
	 * @param  int      $chain_height BlockChain height.
	 * @return bool
	 */
	public static function check_tx_lock_time( $transaction, $chain_height ) {
		return (int) $transaction->locktime <= (int) $chain_height + 1; // Height +1 for the next block from now
	}

	/**
	 * Prevent acceptance of Replace-by-Fee and timelocked transactions https://github.com/bitcoin/bitcoin/pull/6871
	 *
	 * A nSequence below (MAX-1) represents opt-in RBF. https://github.com/bitcoin/bips/blob/master/bip-0125.mediawiki
	 * A nSequence below MAX represents a transaction with a locktime.
	 *
	 * Thus, as long as the locktime isn't preventing next block acceptance, the sequence number of a transaction input has to be >= MAX-1 (0xFFFFFFFE = 4294967294) to be taken into account for zeroconf.
	 *
	 * Bitcoin Core 0.12.0 uses nLocktime to help prevent fee sniping https://github.com/bitcoin/bitcoin/pull/6216/files
	 *
	 *    The reason you see a lot of (MAX-1) nSequences on the network below (MAX) on at least one input is required for the nLocktime field to be consensus enforced.
	 *
	 *      This is not a serious issue today with high block subsidies to transaction fee ratios, but a good measure to implement now so that it becomes standard policy as the subsidy drops, average fees per bytes increases, and block sizes grow)
	 *    and because more transactions are spending OP_CLTV-protected outputs which require the spending transaction set nLocktime.
	 *    Opt-in RBF's signalling criteria was specifically designed to allow those other users of locktime to be able to continue using it without signalling RBF,
	 *    which is why opt-in RBF is below (MAX-1) rather than less than or equal to (MAX-1). https://www.reddit.com/r/Bitcoin/comments/47upgx/nsequence_and_optin_replacebyfee_difference/d0fsno0
	 *
	 * Read more about fee sniping at self::check_tx_lock_time($transaction, $chain_height)
	 *
	 * @param  $input_sequences
	 * @return bool
	 */
	static function check_input_sequences( $input_sequences ) {
		$input_is_rbf = array();

		if ( ! $input_sequences ) {
			// No inputs present - in dubio pro reo.
			return false;
		}

		// Check sequence values for all inputs.
		foreach ( $input_sequences as $sequence ) {
			$input_is_rbf[] = (int) $sequence < 4294967294; // 0xFFFFFFFE === UINT_MAX - 1 === 4294967295 - 1 ;
		}

		return in_array( true, $input_is_rbf );
	}

	/**
	 *
	 * Update transaction data for addresses belonging to open orders during cron
	 *
	 * @param CW_Payment_Details_Object|false $unpaid_orders_arg Optionally send existing payment details object.
	 *
	 * @return array
	 */
	static function batch_up_orders_per_currency( $unpaid_orders_arg = false ) {

		// Get unpaid order's payment details. Prioritize and sort them by average block time.
		if ( $unpaid_orders_arg ) {
			$unpaid_orders = $unpaid_orders_arg;
			$run_filter    = false;
		} else {
			$unpaid_orders = CW_Database_CryptoWoo::get_unpaid_orders_payment_details( false, 0, true );
			$run_filter    = true;
		}

		if ( count( $unpaid_orders ) <= 0 ) {

			// If we currently don't have any open unpaid orders,
			// get the addresses that are marked as paid but still have unconfirmed amounts in the DB and update their status.
			$unpaid_orders = CW_Database_CryptoWoo::get_unconfirmed_addresses();
		}

		if ( $run_filter ) {
			// Filter less time sensitive orders and update them less frequently
			$unpaid_orders = CW_OrderSorting::filter_long_unpaid_addresses( $unpaid_orders );
		}

		// Convert into array of currency batches of orders.
		$batch_orders = array(
			'count'   => 0,
			'batches' => array(),
		);
		foreach ( $unpaid_orders as $order_details ) {
			$batch_orders['batches'][ $order_details->get_payment_currency() ][] = $order_details->get_current_row();
			$batch_orders['count']++;
		}

		// Convert the order batch array back into payment details objects.
		foreach ( $batch_orders['batches'] as &$currency_batch ) {
			$currency_batch = new CW_Payment_Details_Object( $currency_batch );
		}

		return $batch_orders;
	}

	/**
	 * Process and update order data for open orders during cron
	 *
	 * @return  string
	 * @package OrderProcess
	 */
	public static function process_open_orders() {
		// Get unpaid order's payment details.
		$unpaid_orders = CW_Database_CryptoWoo::get_unpaid_orders_payment_details();

		return self::process_unpaid_orders( $unpaid_orders );
	}

	/**
	 *
	 * Check that order object is valid and payment method is CryptoWoo
	 *
	 * @param CW_Database_Woocommerce   $cw_db_woocommerce CryptoWoo Database Woocommerce object.
	 * @param CW_Payment_Details_Object $info              CryptoWoo Payment Details Object.
	 */
	private static function validate_order_and_payment_method( $cw_db_woocommerce, $info ) {
		$status = __( 'Undefined Error', 'cryptowoo' );

		if ( ! $cw_db_woocommerce ) {
			$status = sprintf( esc_html__( 'Error: Order #%1$s with address %2$s not found', 'cryptowoo' ), (int) $info->get_order_id(), $info->get_address() );
			return false;
		} elseif ( ! $cw_db_woocommerce->payment_method_is_cryptowoo() ) {
			$status = sprintf( esc_html__( 'Payment method has been changed to %s - removing address from queue', 'cryptowoo' ), $payment_method );
			// Add order note about the changed payment method.
			if ( 4 !== $info->get_timeout() ) {
				$cw_db_woocommerce->add_order_note( $status );
			}
			return false;
		}

		return true;
	}

	/**
	 * Process and update order data unpaid addresses
	 *
	 * @param CW_Payment_Details_Object $payment_details Array of Payment Details object for unpaid addresses.
	 *
	 * @return  array|string|true
	 * @package OrderProcess
	 */
	public static function process_unpaid_orders( $payment_details ) {
		if ( ! count( $payment_details ) ) {
			return esc_html__( 'No unpaid addresses found', 'cryptowoo' );
		}

		$double_spend_detected = false;

		foreach ( $payment_details as $info ) {
			// Amounts.
			$amount_due         = $info->get_crypto_amount_due();
			$amount_unconfirmed = $info->get_received_unconfirmed();
			$amount_received    = $info->get_received_confirmed();

			// Calculate percentage_paid of amount_due.
			$percentage_paid = self::calculate_percentage_paid( $info );

			$cw_db_woocommerce = CW_Database_Woocommerce::instance( $info->get_order_id() );
			$cw_db_cryptowoo   = CW_Database_CryptoWoo::instance( $info->get_order_id() );

			// Skip if invalid order or CryptoWoo is not the payment method of this order.
			if ( ! self::validate_order_and_payment_method( $cw_db_woocommerce, $info ) ) {
				$cw_db_cryptowoo->set_paid( false )->set_is_timeout_and_needs_quote_refresh()->update();
				continue;
			}

			// Payment complete? Also honour upper limit of underpayment_notice_range to ignore slight underpayments.
			$paid = ( (bool) $info->get_is_paid() || self::calculate_amount_difference( $info ) >= 0 ) && $amount_due > 0 && $amount_received > 0 ? true : ! $amount_unconfirmed && $percentage_paid >= (float) cw_get_option( 'underpayment_notice_range' )[2] && $amount_due > 0;

			if ( ! $paid ) {
				self::process_unpaid_order( $info );
			} // Order is unpaid

			// Complete payment.
			if ( 1 === (int) $paid && $amount_due > 0 && ! $double_spend_detected ) {
				// Maybe mark as overpayment and add order note.
				$check_for_overpayments = cw_get_option( 'overpayment_handling_enabled' );
				if ( $check_for_overpayments && $percentage_paid > 100 ) {
					self::order_overpayment( $info );
				}
				CW_Order_Processing::instance( $info->get_order_id() )->complete_order();
			}
		} // Loop through unpaid addresses.

		return true;
	}

	/**
	 *
	 * Process an unpaid order
	 *
	 * @param CW_Payment_Details_Object $info CryptoWoo Payment Details Object.
	 */
	private static function process_unpaid_order( $info ) {
		// Order expiration time in seconds.
		$order_timeout = (int) cw_get_option( 'order_timeout_min' ) * 60;

		// Time in seconds until the order expires.
		$times_out_in = $info->get_timeout_timestamp() - time();

		// Has the address already received a payment?
		$address_in_use = self::calculate_total_received( $info ) > 0 ? true : false;

		// Calculate percentage_paid of amount_due.
		$percentage_paid = self::calculate_percentage_paid( $info );

		// Underpayment notice range.
		$in_underpayment_range = (float) $percentage_paid <= (float) cw_get_option( 'underpayment_notice_range' )[2] && (float) $percentage_paid >= (float) cw_get_option( 'underpayment_notice_range' )[1];

		// Timeout reached?
		if ( $times_out_in <= 0 && ! (bool) $address_in_use ) {
			// Order has been abandoned -> cancel and remove from queue.
			CW_Database_CryptoWoo::update_order_status_cancelled_timeout( $info->get_order_id() );
		} elseif ( $address_in_use ) {

			// Get underpayment trigger event.
			if ( is_numeric( cw_get_option( 'underpayment_notice_trigger' ) ) ) {
				// Expiration time based notice trigger.
				$mispayment_trigger = $times_out_in <= (int) cw_get_option( 'underpayment_notice_trigger' ) + 16;
			} else {
				// Order is still unpaid because the amount is too low but either the transaction is confirmed or time is running out.
				$mispayment_trigger = ( ! $info->get_received_unconfirmed() && $times_out_in >= 40 ) || $times_out_in <= 40;
			}

			if ( ( (float) $percentage_paid >= (float) cw_get_option( 'underpayment_notice_range' )[2] || $in_underpayment_range ) && $mispayment_trigger ) {
				self::order_underpayment( $info->get_order_id(), $in_underpayment_range, $percentage_paid, $info->get_timeout(), $times_out_in );
			}

			if ( cw_get_option( 'kill_unconfirmed_after' ) && 3 === (int) $info->get_timeout() && $times_out_in <= - $order_timeout && $percentage_paid < (float) cw_get_option( 'underpayment_notice_range' )[2] ) {
				// Incoming transactions don't cover the order amount (but are higher than cw_get_option( 'underpayment_notice_range' )[1])
				// and we're already waiting for double the specified timeout cw_get_option( 'order_timeout_min' )
				// and the congestion handling is not unlimited -> cancel order and request manual intervention.
				CW_Database_CryptoWoo::update_order_status_cancelled_timeout( $info->get_order_id() );
			} elseif ( cw_get_option( 'kill_unconfirmed_after' ) && $percentage_paid < (float) cw_get_option( 'underpayment_notice_range' )[1] && (int) $info->get_timeout() < 3 && $times_out_in <= 0 ) {
				// Customer paid less than cw_get_option( 'underpayment_notice_range' )[1] of the order amount for longer than cw_get_option( 'order_timeout_min' ).
				CW_Database_CryptoWoo::update_order_status_cancelled_timeout( $info->get_order_id() );
			}
		}

		// Update info object to ensure it is fresh and make cw_db_woocommerce instance.
		$info              = CW_Database_CryptoWoo::get_payment_details_by_order_id( $info->get_order_id() );
		$cw_db_woocommerce = CW_Database_Woocommerce::instance( $info->get_order_id() );

		// Change order status if order has timed out and notify customer and admin.
		if ( 1 === $info->get_timeout() ) {

			if ( $percentage_paid >= (float) cw_get_option( 'underpayment_notice_range' )[1] ) {

				// Change the status to on-hold and add a note for the admin and order meta.
				$cw_db_woocommerce->add_admin_note( sprintf( esc_html__( 'CryptoWoo payment failed - Percentage of order paid: %1$s%2$s. Manual interaction needed.', 'cryptowoo' ), $percentage_paid, '%' ) )
					->update_status_on_hold( esc_html__( 'Payment timeout - ', 'cryptowoo' ) )
					->set_tx_confirmed( 'failed - timeout' )
					->update();

				// Action hook for refund necessary.
				do_action( 'cryptowoo_refund_required', $info );
			} else {

				// Maybe cancel order (default)
				// or set to "quote-refresh"
				// or use option value as custom order status.
				$timeout_order_status = cw_get_option( 'timeout_action' ) ?: 'cancelled';
				$order_status_note    = esc_html__( 'Payment timeout - ', 'cryptowoo' );

				if ( 'quote-refresh' === $timeout_order_status ) {
					$payment_url = $cw_db_woocommerce->get_checkout_payment_url();
					// Set timeout to 4 so we can detect the required refresh in check_basic_order_validity().
					CW_Database_CryptoWoo::instance( $info->get_order_id() )->set_is_timeout_and_needs_quote_refresh()->update();

					// Change the status to quote refresh and add a note for the admin and order meta.
					$note             = sprintf( esc_html__( 'The price quote for your order #%1$d has expired. Please visit your account orders section or use the link below to get a new quote for this order. %2$s', 'cryptowoo' ), $info->get_order_id(), $payment_url );
					$is_customer_note = cw_get_option( 'send_quote_refresh_customer_email' );
					$cw_db_woocommerce->add_order_note( $note, $is_customer_note )->update_status_quote_refresh( $order_status_note );
				} else {
					if ( $address_in_use ) {
						// Ask customer to contact merchant because if insufficient payment.
						$txnreference = sprintf( esc_html__( 'This order has expired. Please contact us as insufficient payment has been sent. Reference: %d', 'cryptowoo' ), $info->get_order_id() );
					} else {
						// Default order note.
						$txnreference = sprintf( esc_html__( 'This order has expired. Please try again or contact us if you have already sent a payment. Reference: %d', 'cryptowoo' ), $info->get_order_id() );
					}
					// Add an order note to the customer or admin for the timeout.
					$note             = apply_filters( 'cryptowoo_timeout_txnreference', $txnreference, wc_get_order( $info->get_order_id() ) );
					$is_customer_note = cw_get_option( 'send_cancelled_order_customer_email' );
					$cw_db_woocommerce->add_order_note( $note, $is_customer_note );

					// Set the correct order status after timeout according to Timeout Action settings.
					if ( 'on-hold' === $timeout_order_status ) {
						$cw_db_woocommerce->update_status_on_hold( $order_status_note );
					} elseif ( 'failed' === $timeout_order_status ) {
						$cw_db_woocommerce->update_status_failed( $order_status_note );
					} else {
						$cw_db_woocommerce->update_status_cancelled( $order_status_note );
					}
				}

				if ( CW_AdminMain::logging_is_enabled( 'debug' ) ) {
					CW_AdminMain::cryptowoo_log_data(
						0,
						__FUNCTION__,
						array(
							'order_id'             => $info->get_order_id(),
							'timeout_order_status' => $timeout_order_status,
						),
						'debug'
					);
				}

				// Update order meta.
				CW_Database_Woocommerce::instance( $info->get_order_id() )->set_tx_confirmed( $timeout_order_status . ' - timeout' )->update();
			}
		} else {
			// Order is still unpaid but has not timed out yet - check for double spend or other issues.
			$double_spend_detected = strpos( $info->get_tx_ids_json_encoded(), 'DOUBLESPEND' ) ? true : false;

			if ( $double_spend_detected || $info->get_crypto_amount_due() <= 0 || empty( $info->get_address() ) ) {

				if ( $double_spend_detected ) {
					// Add a note to the admin.
					$cw_db_woocommerce->add_admin_note( sprintf( esc_html__( 'CryptoWoo detected a replace-by-fee or doublespend attempt. Old txid: %s', 'cryptowoo' ), $info->get_tx_ids_json_encoded() ) );
				} else {
					// Change the status to cancelled and add a note to the customer.
					$customer_note = esc_html__( 'We detected an error and had to cancel this order. Please try again or contact us if you already sent a payment. Sorry for the inconvenience.', 'cryptowoo' );
					$cw_db_woocommerce->add_customer_note( $customer_note )->update_status_cancelled( esc_html__( 'Payment error - ', 'cryptowoo' ) );
				}

				// Update order meta.
				$cw_db_woocommerce->set_tx_confirmed( 'failed - error' )->update();
			}

			// Maybe add order note about incoming payment.
			if ( $info->get_received_unconfirmed() > 0 || $info->get_received_confirmed() > 0 ) {

				$tx_confirmed = get_post_meta( $info->get_order_id(), 'tx_confirmed', true );

				if ( empty( $tx_confirmed ) || '0' === $tx_confirmed ) {

					$full_amount_pending = self::calculate_total_received( $info );

					// Payment received notice.
					$cw_db_woocommerce->add_order_note( sprintf( esc_html__( 'Incoming Payment: %1$s%2$s%3$s%4$s', 'cryptowoo' ), CW_Formatting::fbits( $full_amount_pending ), $info->get_payment_currency(), PHP_EOL, $info->get_tx_ids_json_encoded() ) );

					// Update order meta.
					$cw_db_woocommerce
						->set_tx_pending()
						->set_received_unconfirmed( CW_Formatting::fbits( $info->get_received_unconfirmed() ) )
						->set_tx_ids( $info->get_tx_ids() )
						->update();
				}
			}
		} // Unpaid & not timed out
	}

	/**
	 *
	 * Calculate total received (confirmed + unconfirmed)
	 *
	 * @param CW_Payment_Details_Object $payment_details CryptoWoo Payment Details Object.
	 *
	 * @return int
	 */
	private static function calculate_total_received( $payment_details ) {
		return $payment_details->get_received_confirmed() + $payment_details->get_received_unconfirmed();

	}

	/**
	 *
	 * Calculate percentage paid of amount_due.
	 *
	 * @param CW_Payment_Details_Object $payment_details CryptoWoo Payment Details Object.
	 *
	 * @return float|int
	 */
	private static function calculate_percentage_paid( $payment_details ) {
		return ( self::calculate_total_received( $payment_details ) / $payment_details->get_crypto_amount_due() ) * 100;

	}

	/**
	 *
	 * Calculate amount difference between paid and due.
	 *
	 * @param CW_Payment_Details_Object $payment_details CryptoWoo Payment Details Object.
	 *
	 * @return float|int
	 */
	private static function calculate_amount_difference( $payment_details ) {
		return self::calculate_total_received( $payment_details ) - $payment_details->get_crypto_amount_due();
	}

	/**
	 *
	 * Mark as overpayment and add order note.
	 *
	 * @param CW_Payment_Details_Object $info CryptoWoo Payment Details Object.
	 */
	private static function order_overpayment( $info ) {
		// Amounts.
		$amount_due      = $info->get_crypto_amount_due();
		$amount_received = $info->get_received_confirmed();

		// Calculate percentage_paid of amount_due.
		$percentage_paid = self::calculate_percentage_paid( $info );

		// Is the received amount larger than the amount due?
		$amount_diff = self::calculate_amount_difference( $info );

		// Maybe mark as overpayment and add order note.
		CW_Database_Woocommerce::instance( $info->get_order_id() )
		->set_amount_difference( $amount_diff )
		->set_percentage_paid( $percentage_paid )
		->update();

		// Is the payment larger than the overpayment buffer?
		if ( ( (float) $percentage_paid - (float) cw_get_option( 'overpayment_buffer' ) ) > 100 && $amount_due < $amount_received ) {
			// Check if we have a refund address for this order.
			$refund_address = CW_Database_Woocommerce::instance( $info->get_order_id() )->get_refund_address();

			// Add order note and notify customer.
			CW_Database_Woocommerce::instance( $info->get_order_id() )->add_customer_note( CW_Formatting::prepare_overpayment_message( $info, $refund_address ) );

			// Send email to admin.
			self::send_overpayment_email_to_admin( $info, $percentage_paid, $amount_diff, $refund_address );
		}

		// Action hook for refund necessary.
		do_action( 'cryptowoo_refund_required', $info );

	}


	/**
	 *
	 * Mark as underpayment and add order note.
	 *
	 * @param int       $order_id              Woocommerce Order id.
	 * @param bool      $in_underpayment_range If is in underpayment range (for what underpayment amount or less we will.
	 * @param float|int $percentage_paid       How many percentage of crypto amount due has been received.
	 * @param int       $timeout               Timeout value.
	 * @param int       $times_out_in          How long left until order will expire.
	 */
	private static function order_underpayment( $order_id, $in_underpayment_range, $percentage_paid, $timeout, $times_out_in ) {
		/**
*
	* ~5 minutes before order expiration or insufficient amount confirmed.
		 a) underpayment -> notify customer and keep in update queue.
		 b) unusually long network confirmation duration -> redirect customer but keep in queue.
*/

		// Only notify customer if the incoming amounts are in the underpayment notice range.
		if ( (float) $percentage_paid < 100 && $in_underpayment_range && (int) $timeout != 3 ) {

			/**

	   * We have only a partial payment.
			 either 5 minutes before cw_get_option( 'order_timeout_min' ) is reached.
			 or the insufficient amount has received the required number of confirmations.
			 "Partial" meaning maximum 100 - (float)cw_get_option( 'underpayment_notice_range' )[1] is missing when considering all confirmed and unconfirmed txs
*/

			// Prepare order note.
			$note = sprintf( esc_html__( 'Your payment is incomplete. This order has not been paid in full. Please send the missing amount within the next %d minutes.', 'cryptowoo' ), round( ( ( $order_timeout + $times_out_in ) / 60 ), 2 ) );

			// Add a note for the customer.
			CW_Database_Woocommerce::instance( $order_id )->add_customer_note( $note );

			// Set timeout to 3.
			CW_Database_Cryptowoo::instance( $order_id )->set_is_timeout_and_pending_confirm()->update();
		} elseif ( (float) $percentage_paid >= (float) cw_get_option( 'underpayment_notice_range' )[2] ) {

			// The full amount (= more than (float)cw_get_option( 'underpayment_notice_range' )[2]) is seen on the network but has not yet received the required confirmations.
			if ( $times_out_in <= 30 ) {
				// Redirect customer to "Order Received" page but keep order in queue.
				CW_Database_Cryptowoo::instance( $order_id )->set_is_timeout_and_pending_confirm()->update();
				$kill_after = (int) cw_get_option( 'kill_unconfirmed_after' ) * 60 * 60;

				if ( (bool) $kill_after && absint( $times_out_in ) >= absint( $times_out_in + $kill_after ) ) {
					// Transaction did not reach the required confirmations for more than kill_unconfirmed_after - manual interaction needed.
					CW_Database_Cryptowoo::instance( $order_id )->set_is_expired()->update();
				}
			}
		}
	}

	/**
	 *
	 * Send overpayment notice email to admin.
	 *
	 * @param CW_Payment_Details_Object $info            CryptoWoo Payment Details Object.
	 * @param float|int                 $percentage_paid Percentage paid.
	 * @param int                       $amount_diff     Crypto amount difference (paid - due).
	 * @param string                    $refund_address  Refund blockchain address.
	 */
	private static function send_overpayment_email_to_admin( $info, $percentage_paid, $amount_diff, $refund_address ) {

		$to         = get_option( 'admin_email' );
		$blogname   = get_bloginfo( 'name', 'raw' );
		$subject    = sprintf( '%s %s%s', $blogname, esc_html__( ' - Overpayment for order #', 'cryptowoo' ), $info->get_order_id() );
		$order_url  = admin_url( "post.php?post={$info->get_order_id()}&action=edit" );
		$view_order = sprintf( '<p><a href="%s">%s #%s</a></p>', $order_url, esc_html__( 'View Order', 'cryptowoo' ), $info->get_order_id() );

		$message = CW_Formatting::cw_get_template_html( 'email-header', $subject );

		$message .= sprintf( esc_html__( 'The customer paid %1$d%2$s (%3$s %4$s) too much.', 'cryptowoo' ), round( $percentage_paid - 100, 3 ), '%', CW_Formatting::fbits( $amount_diff ), $info->get_payment_currency() );
		$message .= sprintf( '<p>%s: %s</p>%s', esc_html__( 'Customer e-Mail address', 'cryptowoo' ), CW_Database_Woocommerce::instance( $info->get_order_id() )->get_billing_email(), $view_order );

		// Maybe add refund address.
		if ( $refund_address ) {
			$message      .= sprintf( '<p>%s %s</p>', esc_html__( 'Refund address: ', 'cryptowoo' ), CW_Formatting::link_to_address( $info->get_payment_currency(), $refund_address, false, true ) );
			$label         = rawurlencode( sprintf( '%s %s', esc_html__( 'Refund Overpayment for Order', 'cryptowoo' ), $info->get_order_id() ) );
			$wallet_config = CW_Address::get_wallet_config( $info->get_payment_currency(), $info->get_crypto_amount_due() );

			$qr_data = sprintf( '%s:%s?amount=%s&label=%s', $wallet_config['coin_client'], $refund_address, CW_Formatting::fbits( $amount_diff, true, 8, true, true ), $label );

			// Maybe create QR Code TODO always create QR code when library added support for PHP7.
			if ( defined( 'CWOO_SHOW_REFUND_QR' ) ) {
				$message .= esc_html__( 'Scan or click the QR code to refund the excess amount.', 'cryptowoo' );
				$qr       = QRCode::getMinimumQRCode( $qr_data, QR_ERROR_CORRECT_LEVEL_L );
				$im       = $qr->createImage( 2, 4 );
				ob_start();
				// Generate the byte stream.
				imagejpeg( $im, null, 100 );
				// Retrieve the byte stream.
				$rawImageBytes = ob_get_clean();
				$qr_code       = "<img src='data:image/jpeg;base64," . base64_encode( $rawImageBytes ) . "' />";
			} else {
				$qr_code = esc_html__( 'Click here to open your wallet and refund the excess amount.', 'cryptowoo' );
			}
			$message .= sprintf( '<br><br><a href="%s">%s</a>', esc_url( $qr_data, $wallet_config['coin_protocols'], false ), $qr_code );
		} else {
			$message .= sprintf( '<p>%s</p>', esc_html__( 'No refund address available.', 'cryptowoo' ) );
		}
		$message .= CW_Formatting::cw_get_template_html( 'email-footer' );

		$headers = array(
			"From: CryptoWoo Plugin <{$to}>",
			'Content-Type: text/html; charset=UTF-8',
		);
		wp_mail( $to, $subject, $message, $headers );
	}

	/**
	 * Send processing API error to admin email
	 *
	 * @param string $error Error message.
	 */
	public static function processing_api_error_action( $error ) {
		$last_sent = get_transient( 'cryptowoo_last_sent_processing_error_email' );

		// Max 1 email every 15 minutes.
		if ( ! $last_sent || ( time() - $last_sent ) > 900 ) {
			// Send email to admin.
			$to       = get_option( 'admin_email' );
			$blogname = get_bloginfo( 'name', 'raw' );
			$subject  = sprintf( '%s %s', $blogname, esc_html__( ' - Payment processing API error', 'cryptowoo' ) );

			$message  = CW_Formatting::cw_get_template_html( 'email-header', $subject );
			$message .= sprintf( esc_html__( 'CryptoWoo has detected an issue during payment processing%1$s %2$s', 'cryptowoo' ), '<br>', $error );
			$message .= CW_Formatting::cw_get_template_html( 'email-footer' );

			$headers = array(
				"From: CryptoWoo Plugin <{$to}>",
				'Content-Type: text/html; charset=UTF-8',
			);
			wp_mail( $to, $subject, $message, $headers );
			set_transient( 'cryptowoo_last_sent_processing_error_email', time(), 900 );
		}
	}

	/**
	 * Force requeue the address with a new timeout
	 *
	 * @param WC_Order $order Woocommerce order object.
	 */
	public static function requeue_order( $order ) {
		CW_Database_CryptoWoo::requeue_order( $order );
	}
}

