<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Address Creation Handler
 *
 * @category   CryptoWoo
 * @package    CryptoWoo
 * @subpackage Address
 */
class CW_Address {


	/**
	 * Get new Block.io default MultiSig address
	 *
	 * @param  $api_key
	 * @param  $label
	 * @return mixed
	 */
	static function create_blockio_multisig_address( $api_key, $label ) {

		try {
			$blockio = new BlockIo( $api_key, '' );
			$data    = $blockio->get_new_address( array( 'label' => $label ) );

			$return['address'] = $data->data->address;
			$return['status']  = $data->status;

			if ( $return['status'] !== 'success' ) {
				$return['api_message'] = $data->data->error_message;
			}
		} catch ( Exception $e ) {
			$return['status'] = array(
				'status'      => 'error',
				'address'     => false,
				'api_message' => $e->getMessage(),
			);
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . ' Status: ' . var_export( $return, true ), 'error' );
		}
		return $return;
	}

	/**
	 * Get new forwarding address from Block.io
	 *
	 * @param  $api_key
	 * @param  $safe_address
	 * @return mixed
	 */
	static function create_blockio_forwarding_address( $api_key, $safe_address ) {

		/*
		 https://pf.block.io/api/v2/create_forwarding_address/?api_key=KEY&to_address=ADDRESS

		{
		"status" : "success",
		"data" : {
		  "network" : "BTCTEST",
		  "forwarding_private_key" : "cTBcw4HwYLVeBXbXgRNyor48gVEApEw4L6KTc4gSmX8v376Xd9o2",
		  "forwarding_address" : "mfuL3hpXzqwvt4qmEnxs15qtNY5EJzD9Ra",
		  "to_address" : "mrDnRgbA88TsmZYAAqQ3mZ9jxu1gM63Sm4"
		}
		}
		*/

		try {
			$blockio = new BlockIo( $api_key, '' );

			$get_fwd_address = $blockio->create_forwarding_address( array( 'to_address' => $safe_address ) );

			$return['status'] = $get_fwd_address->status;

			if ( $return['status'] === 'success' ) {
				$return['status']                 = true;
				$return['forwarding_private_key'] = $get_fwd_address->data->forwarding_private_key;
				$return['forward_to_address']     = $get_fwd_address->data->to_address;
				$return['address']                = $get_fwd_address->data->forwarding_address;
			}
		} catch ( Exception $e ) {

			$return['status']                 = 'fail';
			$return['forwarding_private_key'] = $e->getMessage();
			$return['forward_to_address']     = 'address creation error';
			$return['address']                = false;
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( 'Y-m-d H:i:s' ) . ' Status: ' . var_export( $return, true ) . ' Forwarding address error: ' . $e->getMessage(), 'critical' );
		}
		return $return;
	}

	/**
	 * Archive Block.io addresses with zero balance and update database
	 *
	 * @todo Maybe allow specifying a custom address threshold (currently 25)
	 * @todo Maybe increase log verbosity
	 * @todo Add trigger button to database maintenance page
	 *
	 * @param  bool $options
	 * @param  bool $force
	 * @return array
	 */
	static function archive_addresses( $options = false, $force = false ) {
		$options ?: $options = cw_get_options();

		$result = array();

		// Only continue if address archival threshold is enabled
		$auto_archive_addresses = cw_get_option( 'auto_archive_addresses' );
		if ( ! $auto_archive_addresses ) {
			return true;
		}

		$address_threshold = $force ? 2 : 25;

		// Only continue if there are no open orders
		$unpaid_addresses = count( CW_Database_CryptoWoo::get_unpaid_orders_payment_details() );
		if ( $unpaid_addresses > 0 ) {
			$result['status']  = 'skipped';
			$result['message'] = __( 'Skipped archiving addresses: We have open orders. Try again later.', 'cryptowoo' );

			// Schedule single trigger in 2 hours
			$schedule = time() + 7200;
			wp_schedule_single_event( $schedule, 'cryptowoo_archive_addresses' );

			return $result;
		}

		$api_keys['BTC']      = cw_get_option( 'cryptowoo_btc_api' );
		$api_keys['BTCTEST']  = cw_get_option( 'cryptowoo_btctest_api' );
		$api_keys['LTC']      = cw_get_option( 'cryptowoo_ltc_api' );
		$api_keys['DOGE']     = cw_get_option( 'cryptowoo_doge_api' );
		$api_keys['DOGETEST'] = cw_get_option( 'cryptowoo_dogetest_api' );

		foreach ( $api_keys as $currency => $api_key ) {

			if ( (bool) $api_key ) {

				// Instantiate BlockIo
				$blockio = new BlockIo( $api_key, '' );

				// Get all addresses for $api_key
				$addresses = $blockio->get_my_addresses( array() );
				$addresses = $addresses->data->addresses;

				// Only continue if there are more than x unarchived addresses
				$address_count = count( $addresses );
				if ( $address_count < $address_threshold ) {
					$result['status']               = 'skipped';
					$result[ $currency ]['message'] = sprintf( __( 'Total %1$s address count: %2$d, Current threshold: %3$d -> skipping', 'cryptowoo' ), $currency, $address_count, $address_threshold );
				} else {

					// Prepare API argument
					$address_array = array();
					foreach ( $addresses as $address_obj ) {
						$address_array[] = $address_obj->address;
					}
					$result[ $currency ]['address_count'] = count( $address_array );
					$addresses_string                     = implode( ',', $address_array );

					// Get the balance of each address
					$address_balances = $blockio->get_address_balance( array( 'addresses' => $addresses_string ) );

					// Filter default address and addresses with non-zero balance
					$address_balances = $address_balances->data->balances;
					$to_archive       = array();

					foreach ( $address_balances as $address_balance ) {
						if ( (float) $address_balance->available_balance <= 0 && (float) $address_balance->pending_received_balance <= 0 && strpos( $address_balance->label, 'default' ) !== 0 ) {
							$to_archive[ $address_balance->label ] = $address_balance->address;
						}
					}

					// Count unarchived addresses with zero balance
					$result[ $currency ]['address_count'] = count( $to_archive );

					// Send a warning if we'll reach the free account limit soon
					if ( $address_count > 80 ) {
						$result['status']               = 'alert';
						$result[ $currency ]['message'] = sprintf( __( 'Warning: We found %1$d %2$s addresses in your account at Block.io. The free plan limit is almost reached!', 'cryptowoo' ), $address_count, $currency );
					} else {

						// Archive max 100 addresses in one call
						if ( $result[ $currency ]['address_count'] > 100 ) {
							$to_archive = array_slice( $to_archive, 0, 99 );
						}

						// Prepare API argument
						$addresses_to_archive = implode( ',', $to_archive );

						// Archive addresses
						// /api/v2/archive_addresses/?api_key=API KEY&addresses=ADDRESS1,ADDRESS2
						$result[ $currency ]['archive_result'] = $blockio->archive_addresses( array( 'addresses' => $addresses_to_archive ) );

						$result[ $currency ]['db_query'] = self::wpdb_update_in(
							'cryptowoo_payments_temp', // table
							array( 'is_archived' => '1' ), // data
							array( 'address' => $to_archive ), // where
							array( '%d', '%s' ), // format
							'%s' // where format
						);

						if ( ! isset( $result['status'] ) ) {
							$result['status'] = 'success';
						}
					} // Less than 3 addresses to archive
				} // Below address threshold
			} // We don't have an API key for this currency
		}
		return $result;
	}

	/**
	 * Update multiple rows via $wpdb->query
	 *
	 * http://wordpress.stackexchange.com/questions/156527/wpdb-update-multiple-rows-like-in-in-normal-sql
	 *
	 * @param  $table
	 * @param  $data
	 * @param  $where
	 * @param  null  $format
	 * @param  null  $where_format
	 * @return bool|false|int
	 */
	static function wpdb_update_in( $table, $data, $where, $format = null, $where_format = null ) {
		global $wpdb;

		if ( ! is_string( $table ) ) {
			return false;
		}
		$q       = sprintf( 'UPDATE %s%s SET ', $wpdb->prefix, esc_sql( $table ) );
		$format  = array_values( (array) $format );
		$escaped = array();
		$i       = 0;
		foreach ( (array) $data as $key => $value ) {
			$f         = isset( $format[ $i ] ) && in_array( $format[ $i ], array( '%s', '%d' ), true ) ? $format[ $i ] : '%s';
			$escaped[] = esc_sql( $key ) . ' = ' . $wpdb->prepare( $f, $value );
			$i++;
		}
		$q         .= implode( $escaped, ', ' );
		$where      = (array) $where;
		$where_keys = array_keys( $where );
		$where_val  = (array) array_shift( $where );
		$q         .= ' WHERE ' . esc_sql( array_shift( $where_keys ) ) . ' IN (';
		if ( ! in_array( $where_format, array( '%s', '%d' ), true ) ) {
			$where_format = '%s';
		}
		$escaped = array();
		foreach ( $where_val as $val ) {
			$escaped[] = $wpdb->prepare( $where_format, $val );
		}
		$q .= implode( $escaped, ', ' ) . ')';
		return $wpdb->query( $q );
	}

	/**
	 * Get the method to be used for generating the payment address
	 *
	 * @param  $payment_currency
	 * @param  $amount
	 * @param  bool             $dynamic_decimals
	 * @return mixed
	 */
	public static function get_wallet_config( $payment_currency, $amount, $dynamic_decimals = true ) {
		if ( $dynamic_decimals ) {
			$dec_places = CW_Formatting::calculate_coin_decimals( $payment_currency, $amount );
		} else {
			$dec_places = 8;
		}

		switch ( $payment_currency ) {
			case ( 'BTC' ):
			case ( 'BTCTEST' ):
				$wallet_config             = array(
					'coin_client'   => 'bitcoin',
					'request_coin'  => 'BTC',
					'multiplier'    => (float) cw_get_option( 'multiplier_btc' ) ?: 1,
					'safe_address'  => cw_get_option( 'safe_btc_address' ),
					'decimals'      => $dec_places,
					'fwd_addr_key'  => 'safe_btc_address',
					'threshold_key' => 'forwarding_threshold_btc',
					'mpk_key'       => $payment_currency === 'BTC' ? 'cryptowoo_btc_mpk' : 'cryptowoo_btctest_mpk',
				);
				$wallet_config['hdwallet'] = self::is_hdwallet_enabled( $wallet_config );
				break;
			case 'BCH':
				$wallet_config                       = array(
					'coin_client'   => 'bitcoincash',
					'request_coin'  => 'BCH',
					'multiplier'    => (float) cw_get_options( 'multiplier_bch' ),
					'safe_address'  => false,
					'decimals'      => 8,
					'mpk_key'       => 'cryptowoo_bch_mpk',
					'fwd_addr_key'  => 'safe_bch_address',
					'threshold_key' => 'forwarding_threshold_bch',
				);
				$wallet_config['hdwallet']           = self::is_hdwallet_enabled( $wallet_config );
				$wallet_config['coin_protocols'][]   = 'bitcoincash';
				$wallet_config['forwarding_enabled'] = false;
				break;
			case ( 'LTC' ):
				$wallet_config             = array(
					'coin_client'   => 'litecoin',
					'request_coin'  => 'LTC',
					'multiplier'    => (float) cw_get_option( 'multiplier_ltc' ) ?: 1,
					'safe_address'  => cw_get_option( 'safe_ltc_address' ),
					'decimals'      => $dec_places,
					'mpk_key'       => ! cw_get_option( 'cryptowoo_ltc_mpk_xpub' ) ? 'cryptowoo_ltc_mpk' : 'cryptowoo_ltc_mpk_xpub',
					'fwd_addr_key'  => 'safe_ltc_address',
					'threshold_key' => 'forwarding_threshold_ltc',
				);
				$wallet_config['hdwallet'] = self::is_hdwallet_enabled( $wallet_config );
				break;
			case ( 'DOGE' ):
			case ( 'DOGETEST' ):
				$wallet_config             = array(
					'coin_client'   => 'dogecoin',
					'request_coin'  => 'DOGE',
					'multiplier'    => (float) cw_get_option( 'multiplier_doge' ) ?: 1,
					'safe_address'  => cw_get_option( 'safe_doge_address' ),
					'decimals'      => $dec_places,
					'mpk_key'       => ! cw_get_option( 'cryptowoo_doge_mpk_xpub' ) ? 'cryptowoo_doge_mpk' : 'cryptowoo_doge_mpk_xpub',
					'fwd_addr_key'  => 'safe_doge_address',
					'threshold_key' => 'forwarding_threshold_doge',
				);
				$wallet_config['hdwallet'] = $payment_currency === 'DOGE' ? self::is_hdwallet_enabled( $wallet_config ) : false;
				break;
			case ( 'BLK' ):
				$wallet_config             = array(
					'coin_client'   => 'blackcoin',
					'request_coin'  => 'BLK',
					'multiplier'    => (float) cw_get_option( 'multiplier_blk' ) ?: 1,
					'safe_address'  => false,
					'decimals'      => $dec_places,
					'mpk_key'       => ! cw_get_option( 'cryptowoo_blk_mpk_xpub' ) ? 'cryptowoo_blk_mpk' : 'cryptowoo_blk_mpk_xpub',
					'fwd_addr_key'  => 'safe_blk_address',
					'threshold_key' => 'forwarding_threshold_blk',
				);
				$wallet_config['hdwallet'] = self::is_hdwallet_enabled( $wallet_config );
				break;
			default:
				$wallet_config = array(
					'multiplier'   => 1,
					'safe_address' => false,
					'decimals'     => 8,
					'hdwallet'     => false,
					'fwd_addr_key' => '',
					'request_coin' => $payment_currency,
				);
				break;
		}

		// Use Electrum daemon wallet?
		$wallet_config['electrum'] = false; // Default to false. Enable via wallet_config filter below

		// BIP21 URI allowed protocols
		$wallet_config['coin_protocols'] = array( 'bitcoin', 'lightning', 'litecoin', 'dogecoin', 'blackcoin' );

		$wallet_config = apply_filters( 'wallet_config', $wallet_config, $payment_currency, cw_get_options() );

		if ( ! $wallet_config['hdwallet'] && cw_get_option( 'fwd_addr_key' ) ) {
			// Determine if threshold for using forwarding address is reached
			$threshold_key                       = sprintf( 'forwarding_threshold_%s', strtolower( $wallet_config['request_coin'] ) );
			$wallet_config['forwarding_enabled'] = $wallet_config['safe_address'] && $amount >= (int) cw_get_option( $threshold_key );
		} else {
			$wallet_config['forwarding_enabled'] = false;
		}

		// Use address list?
		$wallet_config['use_address_list'] = CW_AddressList::address_list_enabled( $payment_currency );

		return $wallet_config;
	}

	static function is_hdwallet_enabled( $wallet_config ) {

		$index = get_option( str_replace( array( '_mpk', '_xpub' ), array( '_index', '' ), $wallet_config['mpk_key'] ) );

		$mpk_exists = cw_get_option( $wallet_config['mpk_key'] );

		return is_numeric( $index ) && $mpk_exists;
	}

} // End of class
