<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly
/**
 * CryptoWoo Redux Framework Config
 * ReduxFramework Config File
 * For full documentation, please visit: http://docs.reduxframework.com/
 * For a more extensive sample-config file, you may look at:
 * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
 */

if ( ! class_exists( 'Redux' ) ) {
	return;
}

add_action( 'redux/options/cryptowoo_payments/validate', 'cryptowoo_settings_change', 99 );
add_action( 'redux/options/cryptowoo_payments/settings/change', 'cryptowoo_settings_change', 99 );

/**
 * Redux settings change helper
 */
function cryptowoo_settings_change() {

	if ( class_exists( 'WC_CryptoWoo' ) ) {

		$cryptowoo = new WC_CryptoWoo();
		$cryptowoo->cryptowoo_hash_keys();

		$admin_main = new CW_AdminMain();
		$admin_main->cryptowoo_cron_activation_schedule();
		cryptowoo_gateway_activation( 'redux' );

		// phpcs:ignore
		/*if(class_exists('CW_HDwallet')) {
			// Update block chain data to discard transactions that have been confirmed in previous blocks
			CW_HDWallet::get_block_height('blockcypher', false);
		}*/
	}
}

/**
 * CryptoWoo gateway activation
 *
 * @param string $from From where it was activated (woocommerce or redux).
 */
function cryptowoo_gateway_activation( $from = 'woocommerce' ) {

	$redux       = cw_get_options();
	$woocommerce = get_option( 'woocommerce_cryptowoo_settings' );

	if ( 'redux' === $from ) {
		$woocommerce['enabled']     = (bool) $redux['enabled'] ? 'yes' : 'no';
		$woocommerce['title']       = $redux['title'];
		$woocommerce['description'] = $redux['description'];
		$option_key                 = sprintf( 'woocommerce_%s_settings', CW_PAYMENT_METHOD_ID );
		update_option( $option_key, $woocommerce );
	} else {
		cw_update_option( 'enabled', $redux );
	}
}

if ( ! function_exists( 'redux_validate_enabled' ) ) :

	/**
	 * Redux validate enabled callback.
	 *
	 * @param string $field Field id.
	 * @param string $value New value.
	 * @param string $existing_value Existing value.
	 *
	 * @return mixed
	 */
	function redux_validate_enabled( $field, $value, $existing_value ) {

		// phpcs:ignore $redux = cw_get_options();
		if ( $value ) {
			// Validate.
			$ready_to_use = cryptowoo_is_ready();
			$valid        = isset( $ready_to_use['error'] ) && ! $ready_to_use['error'];
			if ( $valid ) {
				$error = false;
			} else {
				$field['msg'] = $ready_to_use['message'];
				$error        = true;
				$value        = '0'; // $existing_value;
			}
		} else {
			$field['msg'] = __(
				'<b>Error: The settings are incomplete</b> - payment gateway is disabled<br>
                Go to the Wallet Settings and make sure that you have entered either a valid Block.io API key 
                or a valid Master Public Key for at least one livenet currency.<br>
                Then come back here and try to enable the gateway again.',
				'cryptowoo'
			);
			$error        = true;
			$value        = false; // $existing_value;
		}

		$return['value'] = $value;
		if ( $error ) {
			$return['error'] = $field;
			if ( WP_DEBUG ) {
				CW_AdminMain::cryptowoo_log_data(
					0,
					__FUNCTION__,
					date( "Y-m-d H:i:s" ) . __FILE__ . "\n" . 'redux_validate_enabled - key: ' . var_export(
						$value,
						true
					) . ' | result: ' . var_export( $return, true ),
					'alert'
				);
			}
		}

		return $return;
	}

endif;

/**
 * Redux API key validation helper
 */
if ( ! function_exists( 'redux_validate_api_key' ) ) :

	/**
	 * Redux validate api key callback.
	 *
	 * @param string $field Field id.
	 * @param string $value New value.
	 * @param string $existing_value Existing value.
	 *
	 * @return mixed
	 */
	function redux_validate_api_key( $field, $value, $existing_value ) {

		if ( empty( $value ) || $value === $existing_value ) {
			$return['value'] = $value;

			return $return;
		}

		if ( strcmp( $field['id'], 'cryptowoo_btc_api' ) === 0 ) {
			$currency = 'BTC';
		} elseif ( strcmp( $field['id'], 'cryptowoo_btctest_api' ) === 0 ) {
			$currency = 'BTCTEST';
		} elseif ( strcmp( $field['id'], 'cryptowoo_doge_api' ) === 0 ) {
			$currency = 'DOGE';
		} elseif ( strcmp( $field['id'], 'cryptowoo_dogetest_api' ) === 0 ) {
			$currency = 'DOGETEST';
		} elseif ( strcmp( $field['id'], 'cryptowoo_ltc_api' ) === 0 ) {
			$currency = 'LTC';
		}

		$cryptowoo = new WC_CryptoWoo();
		$valid     = $currency ? $cryptowoo->cw_validate_api_keys( $value, $currency ) : false;

		if ( isset( $valid['status'] ) && (bool) $valid['status'] ) {
			$error = false;
			// phpcs:ignore // $field['msg'] = var_export($result, true).__('valid!', 'cryptowoo');
		} else {
			$field['msg'] = isset( $valid['message'] ) ? $valid['message'] : __( 'API key invalid!', 'cryptowoo' );
			$error        = true;
			$value        = ''; // $existing_value;
		}

		$return['value'] = $value;
		if ( $error ) {
			$return['error'] = $field;
			if ( WP_DEBUG ) {
				CW_AdminMain::cryptowoo_log_data(
					0,
					__FUNCTION__,
					date( "Y-m-d H:i:s" ) . __FILE__ . "\n" . 'redux_validate_api_key - key: ' . var_export(
						$value,
						true
					) . ' | result: ' . var_export( $return, true ),
					'alert'
				);
			}
		}
		usleep( 333333 ); // ~3 requests/second

		return $return;
	}

endif;

/**
 * Redux BlockCypher API token validation helper
 */
if ( ! function_exists( 'redux_validate_token' ) ) :

	/**
	 * Redux validate token callback.
	 *
	 * @param string $field Field id.
	 * @param string $value New value.
	 * @param string $existing_value Existing value.
	 *
	 * @return mixed
	 */
	function redux_validate_token( $field, $value, $existing_value ) {

		if ( empty( $value ) || $value === $existing_value ) {
			$return['value'] = $value;

			return $return;
		}
		// Get token data.
		$bc_response = wp_safe_remote_get( "https://api.blockcypher.com/v1/tokens/{$value}" );

		if ( ! is_wp_error( $bc_response ) ) {
			$token_data = json_decode( $bc_response['body'] );
		}

		if ( isset( $token_data ) && isset( $token_data->limits ) ) {
			$error = false;
		} else {
			$field['msg'] = isset( $token_data ) && isset( $token_data->error ) ? $token_data->error : __(
				'BlockCypher Connection Error',
				'cryptowoo'
			);
			$error        = true;
			$value        = ''; // $existing_value;.
		}

		$return['value'] = $value;
		if ( $error ) {
			$return['error'] = $field;
			if ( WP_DEBUG ) {
				CW_AdminMain::cryptowoo_log_data(
					0,
					__FUNCTION__,
					gmdate( 'Y-m-d H:i:s' ) . __FILE__ . "\n" . 'redux_validate_token - blockcypher token: '
					. var_export( // phpcs:disable WordPress.PHP.DevelopmentFunctions
						$value,
						true
					) . ' | result: ' . var_export( $return, true ), // phpcs:disable WordPress.PHP.DevelopmentFunctions
					'alert'
				);
			}
		}
		usleep( 333333 ); // ~3 requests/second

		return $return;
	}

endif;

/**
 * Redux address validation helper
 */
if ( ! function_exists( 'redux_validate_address' ) ) :

	/**
	 * Redux validate address callback.
	 *
	 * @param string $field Field id.
	 * @param string $value New value.
	 * @param string $existing_value Existing value.
	 *
	 * @return mixed
	 */
	function redux_validate_address( $field, $value, $existing_value ) {

		if ( empty( $value ) || $value === $existing_value ) {
			$return['value'] = $value;

			return $return;
		}

		if ( strcmp( $field['id'], 'safe_btc_address' ) === 0 ) {
			$currency = cw_get_option( 'cryptowoo_btctest_api' ) ? 'BTCTEST' : 'BTC';
		} elseif ( strcmp( $field['id'], 'safe_doge_address' ) === 0 ) {
			$currency = cw_get_option( 'cryptowoo_dogetest_api' ) ? 'DOGETEST' : 'DOGE';
		} elseif ( strcmp( $field['id'], 'safe_ltc_address' ) === 0 ) {
			$currency = 'LTC';
		} else {
			$currency = apply_filters( 'cw_redux_validate_address_currency_override', false, $field['id'] );
		}

		$validate      = new CW_Validate();
		$address_valid = $validate->offline_validate_address( $value, $currency );

		if ( ! $address_valid ) {

			$value           = $existing_value;
			$testmode_notice = '';

			if ( 'BTCTEST' === $currency ) {
				$testmode_notice = __(
					'Bitcoin Testnet mode is enabled. Please remove your BTCTEST Block.io API Key or enter a valid Bitcoin Testnet address.',
					'cryptowoo'
				);
			} elseif ( 'DOGETEST' === $currency ) {
				$testmode_notice = __(
					'Dogecoin Testnet mode is enabled. Please remove your DOGETEST Block.io API Key or enter a valid Dogecoin Testnet address.',
					'cryptowoo'
				);
			}
			$field['msg']    = "{$currency} address invalid! <br>{$testmode_notice}";
			$return['error'] = $field;

			if ( WP_DEBUG ) {
				CW_AdminMain::cryptowoo_log_data(
					0,
					__FUNCTION__,
					gmdate(
						'Y-m-d H:i:s'
					) . __FILE__ . "\n" . 'redux_validate_address debug - ' . $field['id'] . ' currency: '
					. var_export(
						$currency,
						true
					) . ' value: ' . var_export( $value, true ) . ' | result: ' . var_export( $return, true ),
					'emergency'
				);
			}
		}
		$return['value'] = $value;

		return $return;
	}

endif;

/**
 * Redux address list validation helper
 */
if ( ! function_exists( 'redux_validate_address_list' ) ) :

	/**
	 * Redux validate address list callback.
	 *
	 * @param string $field Field id.
	 * @param string $value New value.
	 * @param string $existing_value Existing value.
	 *
	 * @return mixed
	 */
	function redux_validate_address_list( $field, $value, $existing_value ) {

		if ( empty( $value ) || $value === $existing_value ) {
			$return['value'] = '';

			return $return;
		}

		$currency  = strtoupper( str_replace( 'address_list_', '', $field['id'] ) );
		$addresses = preg_split( '/\r\n|\r|\n/', $value );
		$validate  = new CW_Validate();
		$added     = false;
		$error     = false;
		$max_limit = apply_filters( 'cw_address_list_max', 20, $currency );

		$limit_reached = $max_limit && $max_limit <= CW_AddressList::get_address_list_count( $currency );
		$addresses     = array_slice( $addresses, 0, $max_limit - 1 );
		if ( $limit_reached ) {
			$field['msg']    = sprintf(
				__( 'Could not add addresses - only %1$d addresses allowed for this currency.%2$s', 'cryptowoo' ),
				$max_limit,
				'<br>'
			);
			$return['error'] = $field;
			$error           = true;

		} else {
			foreach ( $addresses as $i => $address ) {
				$address = trim( $address );
				if ( $address ) {
					$address_valid = $validate->offline_validate_address( $address, $currency );
					if ( ! $address_valid ) {
						$field['msg']   .= sprintf(
							'Invalid %s address "%s"<br>',
							$currency,
							sanitize_text_field( $address )
						);
						$return['error'] = $field;
						$error           = true;
					} else {
						// Address is valid - add it to the list.
						$added = CW_AddressList::add_address_to_list( $address, $currency );
						if ( ! $added ) {
							$field['msg']   .= sprintf(
								__(
									'Could not add address "%1$s" - Make sure to add only unused addresses.%2$s',
									'cryptowoo'
								),
								$address,
								'<br>'
							);
							$return['error'] = $field;
							$error           = true;
						}
					}
					unset( $addresses[ $i ] );
				}
			}
		}
		if ( ! $error && $added ) {
			echo wp_json_encode(
				array(
					'status' => 'success',
					'action' => 'reload',
				)
			);
			die();
		}

		$return['value'] = '';

		return $return;
	}

endif;

/**
 * Redux exchange rate API validation
 */
if ( ! function_exists( 'redux_validate_exchange_api' ) ) :

	/**
	 * Redux validate exchange api callback.
	 *
	 * @param string $field Field id.
	 * @param string $value New value.
	 * @param string $existing_value Existing value.
	 *
	 * @return mixed
	 */
	function redux_validate_exchange_api( $field, $value, $existing_value ) {
		$error = false;

		if ( 0 === strcmp( $field['id'], 'preferred_exchange_btc' ) && 'blockio' === $value && ( ! cw_get_option(
				'cryptowoo_btc_api'
			) ) ) {
			$error    = true;
			$value    = $field['default'];
			$currency = 'Bitcoin';
		} elseif ( 0 === strcmp( $field['id'], 'preferred_exchange_ltc' ) && 'blockio' === $value && ( ! cw_get_option(
				'cryptowoo_ltc_api'
			) ) ) {
			$error    = true;
			$value    = $field['default'];
			$currency = 'Litecoin';
		} elseif ( 0 === strcmp( $field['id'], 'preferred_exchange_doge' ) && 'blockio' === $value && ( ! cw_get_option(
				'cryptowoo_doge_api'
			) ) ) {
			$error    = true;
			$value    = $field['default'];
			$currency = 'Dogecoin';
		}
		$return['value'] = $value;
		if ( $error ) {
			$field['msg']    = sprintf(
				__( 'You have to enter a %s Block.io API key to use the Block.io exchange rate API.', 'cryptowoo' ),
				$currency
			);
			$return['error'] = $field;
			if ( CW_AdminMain::logging_is_enabled( 'alert' ) ) {
				CW_AdminMain::cryptowoo_log_data(
					0,
					__FUNCTION__,
					gmdate( 'Y-m-d H:i:s' ) . __FILE__ . "\n" . 'redux_validate_exchange_api - key: '
					. var_export(
						$value,
						true
					) . ' | result: ' . var_export( $return, true ),
					'alert'
				);
			}
		}

		return $return;
	}

endif;


/**
 * Redux processing API validation
 *
 * @todo Validate Electrum daemon connection
 */
if ( ! function_exists( 'redux_validate_processing_api' ) ) :

	/**
	 * Redux validate processing api callback.
	 *
	 * @param string $field Field id.
	 * @param string $value New value.
	 * @param string $existing_value Existing value.
	 *
	 * @return mixed
	 */
	function redux_validate_processing_api( $field, $value, $existing_value ) {

		if ( 'disabled' === $value ) {
			$return['value'] = $value;

			return $return;
		}

		$error                         = false;
		$cryptowoo_payments_transients = get_option( 'cryptowoo_payments-transients' );
		$changed_values                = isset( $cryptowoo_payments_transients['changed_values'] ) ? $cryptowoo_payments_transients['changed_values'] : array();
		//CW_AdminMain::cryptowoo_log_data(0, 'validate_processing_api', array('changed' => $changed_values), 'redux.log');

		if ( 'custom' === $value ) {
			$desc    = __( 'custom API', 'cryptowoo' );
			$desc_2  = sprintf( __( 'your %s', 'cryptowoo' ), $desc );
			$we_need = 'URL';
		} else {
			$desc    = 'Block.io';
			$desc_2  = 'Block.io';
			$we_need = __( 'API key in the wallet settings', 'cryptowoo' );
		}

		if ( strcmp( $field['id'], 'processing_api_btc' ) === 0 ) {
			if ( 'blockio' === $value && ! cw_get_option( 'cryptowoo_btc_api' ) && ! CW_Validate::check_if_unset( 'cryptowoo_btc_api', $changed_values, false ) ) {
				$error = true;
				$value = 'disabled';//$field['default'];
				// phpcs:ignore
				/*
				elseif($value === 'custom' && ( ! cw_get_option( 'custom_api_btc') ) ) || !redux_check_transient('custom_api_btc'))) {
					$error = true;
					$value = $field['default'];//'custom';
				}
				*/
			}
		} elseif ( strcmp( $field['id'], 'processing_api_doge' ) === 0 ) {
			if ( 'blockio' === $value && ! cw_get_option( 'cryptowoo_doge_api' ) && ! CW_Validate::check_if_unset( 'cryptowoo_doge_api', $changed_values, false ) ) {
				$error = true;
				$value = 'disabled';//$field['default'];
			} /* elseif($value === 'custom' && ( ! cw_get_option( 'custom_api_doge') ) ) && !redux_check_transient('custom_api_doge')) {
                $error = true;
                $value = $field['default'];//'custom';
            } */
		} elseif ( strcmp( $field['id'], 'processing_api_ltc' ) === 0 ) {
			if ( 'blockio' === $value && ! cw_get_option( 'cryptowoo_ltc_api' ) && ! CW_Validate::check_if_unset( 'cryptowoo_ltc_api', $changed_values, false ) ) {
				$error = true;
				$value = 'disabled';//$field['default'];
			} /*elseif($value === 'custom' && ( ! cw_get_option( 'custom_api_ltc') ) ) && !redux_check_transient('custom_api_ltc')) {
                $error = true;
                $value = $field['default'];//'custom';
            }*/
		} elseif ( strcmp( $field['id'], 'processing_api_blk' ) === 0 ) {
			if ( 'cryptoid' === $value ) {
				$desc   = 'cryptoID.info';
				$desc_2 = 'cryptoID.info';
				$error  = true;
				$value  = $field['default'];
			} elseif ( 'custom' === $value && ! cw_get_option( 'custom_api_blk' ) && ! CW_Validate::check_if_unset( 'custom_api_blk', $changed_values, false ) ) {
				$error = true;
				$value = $field['default'];
			}
		}
		$return['value'] = $value;
		if ( $error ) {
			$field['msg']    = sprintf( __( 'You have to enter a %s %s to use %s during payment processing.', 'cryptowoo' ), $desc, $we_need, $desc_2 );
			$return['error'] = $field;
			if ( CW_AdminMain::logging_is_enabled( 'alert' ) ) {
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( "Y-m-d H:i:s" ) . __FILE__ . "\n" . 'redux_validate_exchange_api - key: ' . var_export( $value, true ) . ' | result: ' . var_export( $return, true ), 'alert' );
			}
		}

		return $return;
	}

endif;

/**
 * Redux custom processing API validation
 */
if ( ! function_exists( 'redux_validate_custom_api' ) ) :

	/**
	 * Redux validate custom api callback.
	 *
	 * @param string $field Field id.
	 * @param string $value New value.
	 * @param string $existing_value Existing value.
	 *
	 * @return mixed
	 */
	function redux_validate_custom_api( $field, $value, $existing_value ) {

		if ( empty( $value ) || $value === $existing_value ) {
			$return['value'] = $value;

			return $return;
		}

		$error = true;
		if ( strcmp( $field['id'], 'custom_api_btc' ) === 0 ) {
			$currency = 'BTC';
			$genesis  = '000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f';
		} elseif ( in_array( $field['id'], array( 'custom_api_bch', 'processing_fallback_url_bch' ), true ) ) {
			$currency = 'BCH';
			$genesis  = '000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f';
		} elseif ( strcmp( $field['id'], 'custom_esplora_api_btc' ) === 0 ) {
			$currency = 'BTC';
			$genesis  = '000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f';
		} elseif ( strcmp( $field['id'], 'custom_api_doge' ) === 0 ) {
			$currency = 'DOGE';
			$genesis  = '1a91e3dace36e2be3bf030a65679fe821aa1d6ef92e7c9902eb318182c355691';
		} elseif ( strcmp( $field['id'], 'custom_api_ltc' ) === 0 ) {
			$currency = 'LTC';
			$genesis  = '12a765e31ffd4059bada1e25190f6e98c99d9714d334efa41a195a7e7e04bfe2';
		} elseif ( strcmp( $field['id'], 'custom_api_blk' ) === 0 ) {
			$currency = 'BLK';
			$genesis  = '000001faef25dec4fbcf906e6242621df2c183bf232f263d0ba5b101911e4563';
		} else {
			$currency = 'NoCoin';
			$genesis  = str_repeat( '0', 64 ); // Zero.
		}

		$genesis  = apply_filters( 'validate_custom_api_genesis', $genesis, $field['id'] );
		$currency = apply_filters( 'validate_custom_api_currency', $currency, $field['id'] );

		// Get data.
		$explorer_id    = substr( $field['id'], 0, strpos( $field['id'], '_api' ) );
		$block_explorer = CW_OrderProcessing::block_explorer_tools()->get_block_explorer_instance_by_id( $explorer_id, $currency );
		$api_result     = $block_explorer->override_base_url( $value )->get_block_hash( 0 );

		if ( false !== $api_result ) {
			if ( is_string( $api_result ) ) {
				$block_hash = $api_result;
			}
			if ( isset( $block_hash ) ) {
				$error = strcmp( $block_hash, $genesis ) !== 0;  // False if genesis blocks match.
				$data  = $error ? sprintf( __( 'This is not a %s API. The hash of the genesis block in the API response %s%s%s does not match the %s genesis block %s%s%s', 'cryptowoo' ), $currency, '<pre>', htmlentities( $block_hash ), '</pre>', $currency, '<pre>', $genesis, '</pre>' ) : '';
			} else {
				$data = esc_html( sprintf( 'Invalid response|%s', wp_json_encode( $api_result ) ) );
			}
		} else {
			$data = 'The URL is invalid or the API is down. See email and/or logs for more info';
		}

		if ( (bool) $error ) {
			$return['value'] = '';
			$field['msg']    = sprintf( __( 'API Error. %s%s', 'cryptowoo' ), $data, '<br>' );
			$return['error'] = $field;
			if ( CW_AdminMain::logging_is_enabled( 'emergency' ) ) {
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, date( "Y-m-d H:i:s" ) . __FILE__ . "\n" . 'redux_validate_custom_api - key: ' . var_export( $value, true ) . ' | result: ' . var_export( $return, true ), 'emergency' );
			}

			return $return;
		}

		return [ 'value' => $value ];
	}

endif;


/**
 * Redux custom block explorer API validation
 */
if ( ! function_exists( 'redux_validate_custom_blockexplorer' ) ) :

	/**
	 * Redux validate custom block explorer callback.
	 *
	 * @param string $field Field id.
	 * @param string $value New value.
	 * @param string $existing_value Existing value.
	 *
	 * @return mixed
	 */
	function redux_validate_custom_blockexplorer( $field, $value, $existing_value ) {

		if ( empty( $value ) || $value === $existing_value ) {
			$return['value'] = $value;

			return $return;
		}

		$error = false;

		if ( ! wp_http_validate_url( $value ) ) {
			$error = __( 'Invalid URL', 'cryptowoo' );
		}
		if ( false === strpos( $value, '{{ADDRESS}}' ) ) {
			$error = __( '{{ADDRESS}} placeholder not found', 'cryptowoo' );
		}

		$return['value'] = $value;

		if ( (bool) $error ) {
			$return['value'] = $existing_value;
			$field['msg']    = sprintf( __( 'Error: %s', 'cryptowoo' ), $error );
			$return['error'] = $field;
		}

		return $return;
	}

endif;

/**
 * Validate SOCKS5 connection by trying to reach https://blockchainbdgpzk.onion/ticker
 *
 * @param $field
 * @param $value
 * @param $existing_value
 *
 * @return mixed
 */
function redux_validate_socks5_proxy_url( $field, $value, $existing_value ) {

	$return['value'] = $value;

	if ( empty( $value ) || $value === $existing_value ) {

		return $return;
	}

	$error = false;

	$source_url = 'https://blockchainbdgpzk.onion/ticker';
	$result     = CW_ExchangeRates::processing()->request( $source_url, true, false, 10, $value );
	if ( ! json_decode( $result ) ) {
		$error = sprintf( __( 'Error connecting to %s via %s', 'cryptowoo' ), $source_url, esc_html( $value ) );
	}

	if ( $error ) {
		$return['value'] = $existing_value;
		$field['msg']    = $error;
		$return['error'] = $field;
	}

	return $return;
}

/**
 * Check if the plugin is ready to use
 *
 * @return array
 * @todo fix
 */
function cryptowoo_is_ready() {

	$result['error'] = true;

	// Check PHP requirements
	$requirements = WC_CryptoWoo::check_php_requirements();
	if ( is_string( $requirements ) ) {
		$result['error']   = true;
		$result['message'] = __( '<b>Error: ' . $requirements . '</b>', 'cryptowoo' );

		return $result;
	}

	$enabled['BTC_blockio']  = (bool) cw_get_option( 'cryptowoo_btc_api' ); // TODO refactor this.
	$enabled['DOGE_blockio'] = (bool) cw_get_option( 'cryptowoo_doge_api' );
	$enabled['LTC_blockio']  = (bool) cw_get_option( 'cryptowoo_ltc_api' );

	$enabled['BTC_mpk']  = (bool) cw_get_option( 'cryptowoo_btc_mpk' );
	$enabled['BCH_mpk']  = (bool) cw_get_option( 'cryptowoo_bch_mpk' );
	$enabled['DOGE_mpk'] = (bool) cw_get_option( 'cryptowoo_doge_mpk' );
	$enabled['LTC_mpk']  = (bool) cw_get_option( 'cryptowoo_ltc_mpk' );
	$enabled['BLK_mpk']  = (bool) cw_get_option( 'cryptowoo_blk_mpk' );

	// Maybe we have just enabled a new currency -> look in options transient.
	$cryptowoo_payments_transients = get_option( 'cryptowoo_payments-transients' );
	$changed_values                = $cryptowoo_payments_transients['changed_values'];

	// Check address list.
	$enabled['BTC_address_list']  = CW_AddressList::address_list_enabled( 'BTC' );
	$enabled['BCH_address_list']  = CW_AddressList::address_list_enabled( 'BCH' );
	$enabled['LTC_address_list']  = CW_AddressList::address_list_enabled( 'LTC' );
	$enabled['DOGE_address_list'] = CW_AddressList::address_list_enabled( 'DOGE' );

	if ( is_array( $changed_values ) ) {
		$enabled['BTC_blockio_transient']  = (bool) CW_Validate::check_if_unset( 'cryptowoo_btc_api', $changed_values );
		$enabled['DOGE_blockio_transient'] = (bool) CW_Validate::check_if_unset( 'cryptowoo_doge_api', $changed_values );
		$enabled['LTC_blockio_transient']  = (bool) CW_Validate::check_if_unset( 'cryptowoo_ltc_api', $changed_values );

		$enabled['BTC_mpk_transient']  = (bool) CW_Validate::check_if_unset( 'cryptowoo_btc_mpk', $changed_values );
		$enabled['BCH_mpk_transient']  = (bool) CW_Validate::check_if_unset( 'cryptowoo_bch_mpk', $changed_values );
		$enabled['DOGE_mpk_transient'] = (bool) CW_Validate::check_if_unset( 'cryptowoo_doge_mpk', $changed_values );
		$enabled['LTC_mpk_transient']  = (bool) CW_Validate::check_if_unset( 'cryptowoo_ltc_mpk', $changed_values );
		$enabled['BLK_mpk_transient']  = (bool) CW_Validate::check_if_unset( 'cryptowoo_blk_mpk', $changed_values );
	}
	$enabled = apply_filters( 'cryptowoo_is_ready', $enabled, cw_get_options(), $changed_values );
	// at least one livenet currency enabled.
	if ( in_array( true, $enabled, true ) ) {
		// cron working?
		// TODO check if cronjob is working.
		$result['error'] = false;

	} else {

		$result['error']   = true;
		$result['message'] = __( '<b>Error: No currency enabled</b><br>Make sure that you have configured the Wallet Settings for at least one livenet currency, then come back here and enable the gateway.', 'cryptowoo' );
	}

	return $result;
}

/**
 * Begin Redux Framework Config
 *
 * @package CryptoWoo
 * @subpackage Admin
 */
// This is your option name where all the Redux data is stored.
$opt_name = "cryptowoo_payments"; //cryptowoo_payments.

$woocommerce_currency = get_option( 'woocommerce_currency' );

switch ( $woocommerce_currency ) {
	case 'BTC':
	case 'BCH':
	case 'LTC':
	case 'DOGE':
	case 'BLK':
		$cryptostore = true;
		break;
	default:
		$cryptostore = false;
		break;
}
$cryptostore            = apply_filters( 'is_cryptostore', $cryptostore, $woocommerce_currency );
$admin_url              = get_admin_url();
$db_actions_url         = $admin_url . 'admin.php?page=cryptowoo_database_maintenance';
$wc_currency_format_url = $admin_url . 'admin.php?page=wc-settings';
$payments_overview_url  = $admin_url . 'admin.php?page=cryptowoo';
$woocommerce_logs_url   = $admin_url . 'admin.php?page=wc-status&tab=logs';
$admin_email            = get_option( 'admin_email' );

$template_tag_string = sprintf( __( 'Use in posts, pages, and widgets: <pre>[cw_currency_switch]</pre> Use in PHP files: <pre> %s </pre>', 'cryptowoo' ), htmlspecialchars( "<?php do_shortcode('[cw_currency_switch]'); ?>", ENT_QUOTES ) );

/**
 * Display WC Store currency notice if we have only one available exchange
 *
 * @param $field
 *
 * @return mixed
 */
function cw_limited_api_warning( $field ) {
	if ( count( construct_preferred_exchange_array() ) <= 2 ) {
		$woocommerce_settings = admin_url( 'admin.php?page=wc-settings' );
		$limited_api_warning  = sprintf( __( '%s Limited Exchange API Choice%s
                 Your WooCommerce store currency %s limits the available Bitcoin exchange rate APIs.%s
                 For a wider range of available rate APIs change your %sWooCommerce Currency Settings%s to %sUS Dollar%s', 'cryptowoo' ), '<p><i class="el el-warning-sign cw-message"></i>', '<br>', cw_get_woocommerce_currency(), '<br>', sprintf( '<a class="button" href="%s" title="WooCommerce Settings">', $woocommerce_settings ), '</a>', '<strong>', '</strong>.</p>' );
	} else {
		$limited_api_warning = '';
	}
	$field['desc'] .= $limited_api_warning;

	return $field;
}

add_filter( "redux/options/{$opt_name}/field/rates_info", 'cw_limited_api_warning' );

/**
 * Check BlockCypher API limits button
 *
 * @param $field
 *
 * @return mixed
 */
function add_blockcypher_limit_link( $field ) {
	$options = cw_get_options();
	$token   = cw_get_option( 'blockcypher_token' ) ?: '';
	if ( ! empty( $token ) || in_array( 'blockcypher', $options ) ) {
		// Display data if we have an API token or BlockCypher is a preferred processing API
		$token_data    = CW_AdminMain::get_blockcypher_limit( false );
		$manual_check  = sprintf( __( ' %s%s%sCheck manually%s' ), '<a href="https://api.blockcypher.com/v1/tokens/', $token, '" title="Check BlockCypher rate limit" target="_blank">', '</a>' );
		$field['desc'] = sprintf( ' <strong>BlockCypher Limits:</strong> <pre>%s</pre> %s %s', var_export( $token_data, true ), $field['desc'], $manual_check );

	}

	return $field;
}

add_filter( "redux/options/{$opt_name}/field/blockcypher_token", 'add_blockcypher_limit_link' );

/**
 * Unset field if not needed
 *
 * @param $field
 *
 * @return mixed
 */
function redux_remove_field( $field ) {
	if ( cw_hd_active() ) {
		return $field;
	}
}

add_filter( "redux/options/{$opt_name}/field/cryptowoo_blk_min_conf", 'redux_remove_field' );
add_filter( "redux/options/{$opt_name}/field/cryptowoo_max_unconfirmed_blk", 'redux_remove_field' );
add_filter( "redux/options/{$opt_name}/field/processing_api_blk", 'redux_remove_field' );
add_filter( "redux/options/{$opt_name}/field/cryptoid_api_key", 'redux_remove_field' );
add_filter( "redux/options/{$opt_name}/field/custom_api_blk", 'redux_remove_field' );
add_filter( "redux/options/{$opt_name}/field/preferred_exchange_blk", 'redux_remove_field' );
add_filter( "redux/options/{$opt_name}/field/multiplier_blk", 'redux_remove_field' );


function get_discount_info() {

	$discount_currencies = Redux::get_section( 'cryptowoo_payments', 'rates-multiplier' );

	if ( is_array( $discount_currencies ) ) {

		$discount_info = array();
		foreach ( $discount_currencies as $discount_currency => $multiplier ) {
			$discount_percent                    = $multiplier * 100;
			$discount_info[ $discount_currency ] = sprintf( __( 'Pay with %s and receive a %s%s discount on all products.<br>', 'cryptowoo' ), $discount_currency, $discount_percent, '%' );
		}
	} else {
		$discount_info = __( 'Pay with digital currencies to receive a discount on all orders!', 'cryptowoo' );
	}

	return null;//$discount_info;.
}

/**
 * Remove exchange rate providers that do not support the current WooCommerce store currency
 */
function construct_preferred_exchange_array() {
	$preferred_exchanges = array();

	$woocommerce_currency = cw_get_woocommerce_default_currency();

	$preferred_exchanges_names = array(
		'coincap'         => 'CoinCap.io',
		'coingecko'       => 'CoinGecko',
		'blockio'         => 'Block.io (Enter API keys in "Wallet Settings")',
		'bitcoinaverage'  => 'BitcoinAverage',
		'bitpay'          => 'BitPay',
		'bitstamp'        => 'Bitstamp',
		'coinbase'        => 'Coinbase Pro',
		'bitfinex'        => 'Bitfinex',
		'okcoin'          => 'OKCoin.com',
		'blockchain_info' => 'Blockchain.info',
		'bitcoincharts'   => 'Bitcoincharts.com',
		'coindesk'        => 'CoinDesk BPI',
		'luno'            => 'Luno.com',
		'okcoincn'        => 'OKCoin.cn',
		'kraken'          => 'Kraken',
		'livecoin'        => 'Livecoin',
	);

	$preferred_exchanges_allowed_currencies_pairs = array(
		'coingecko'       => array(
			'USD',
			'AED',
			'ARS',
			'AUD',
			'BDT',
			'BHD',
			'BMD',
			'BRL',
			'CAD',
			'CHF',
			'CLP',
			'CNY',
			'CZK',
			'DKK',
			'EUR',
			'GBP',
			'HKD',
			'HUF',
			'IDR',
			'ILS',
			'INR',
			'JPY',
			'KRW',
			'KWD',
			'LKR',
			'MMK',
			'MXN',
			'MYR',
			'NOK',
			'NZD',
			'PHP',
			'PKR',
			'PLN',
			'RUB',
			'SAR',
			'SEK',
			'SGD',
			'THB',
			'TRY',
			'TWD',
			'UAH',
			'VEF',
			'VND',
			'ZAR',
			'XDR',
			'XAG',
			'XAU',
		),
		'blockio'         => array( 'USD' ),
		'coincap'         => array( 'USD' ),
		'bitcoinaverage'  => true,
		'bitpay'          => array( 'USD' ),
		'bitstamp'        => array( 'USD' ),
		'coinbase'        => array( 'USD' ),
		'bitfinex'        => array( 'USD' ),
		'okcoin'          => array( 'USD' ),
		'blockchain_info' => array(
			'USD',
			'JPY',
			'CNY',
			'SGD',
			'HKD',
			'CAD',
			'NZD',
			'AUD',
			'CLP',
			'GBP',
			'DKK',
			'SEK',
			'ISK',
			'CHF',
			'BRL',
			'EUR',
			'RUB',
			'PLN',
			'THB',
			'KRW',
			'TWD',
		),
		'bitcoincharts'   => array(
			'IDR',
			'USD',
			'SGD',
			'EUR',
			'XRP',
			'PLN',
			'HKD',
			'BRL',
			'AUD',
			'CHF',
			'GBP',
			'DKK',
			'GAU',
			'JPY',
			'RUB',
			'ILS',
			'KRW',
			'RON',
			'LTC',
			'CNY',
			'CAD',
			'NZD',
			'SEK',
			'CLP',
			'ARS',
			'NOK',
			'HUF',
			'THB',
			'UAH',
			'ZAR',
			'INR',
			'SLL',
			'CZK',
			'MXN',
			'NMC',
		),
		'coindesk'        => array(
			'AED',
			'AFN',
			'ALL',
			'AMD',
			'ANG',
			'AOA',
			'ARS',
			'AUD',
			'AWG',
			'AZN',
			'BAM',
			'BBD',
			'BDT',
			'BGN',
			'BHD',
			'BIF',
			'BMD',
			'BND',
			'BOB',
			'BRL',
			'BSD',
			'BTC',
			'BTN',
			'BWP',
			'BYR',
			'BZD',
			'CAD',
			'CDF',
			'CHF',
			'CLF',
			'CLP',
			'CNY',
			'COP',
			'CRC',
			'CUP',
			'CVE',
			'CZK',
			'DJF',
			'DKK',
			'DOP',
			'DZD',
			'EEK',
			'EGP',
			'ERN',
			'ETB',
			'EUR',
			'FJD',
			'FKP',
			'GBP',
			'GEL',
			'GHS',
			'GIP',
			'GMD',
			'GNF',
			'GTQ',
			'GYD',
			'HKD',
			'HNL',
			'HRK',
			'HTG',
			'HUF',
			'IDR',
			'ILS',
			'INR',
			'IQD',
			'IRR',
			'ISK',
			'JEP',
			'JMD',
			'JOD',
			'JPY',
			'KES',
			'KGS',
			'KHR',
			'KMF',
			'KPW',
			'KRW',
			'KWD',
			'KYD',
			'KZT',
			'LAK',
			'LBP',
			'LKR',
			'LRD',
			'LSL',
			'LTL',
			'LVL',
			'LYD',
			'MAD',
			'MDL',
			'MGA',
			'MKD',
			'MMK',
			'MNT',
			'MOP',
			'MRO',
			'MTL',
			'MUR',
			'MVR',
			'MWK',
			'MXN',
			'MYR',
			'MZN',
			'NAD',
			'NGN',
			'NIO',
			'NOK',
			'NPR',
			'NZD',
			'OMR',
			'PAB',
			'PEN',
			'PGK',
			'PHP',
			'PKR',
			'PLN',
			'PYG',
			'QAR',
			'RON',
			'RSD',
			'RUB',
			'RWF',
			'SAR',
			'SBD',
			'SCR',
			'SDG',
			'SEK',
			'SGD',
			'SHP',
			'SLL',
			'SOS',
			'SRD',
			'STD',
			'SVC',
			'SYP',
			'SZL',
			'THB',
			'TJS',
			'TMT',
			'TND',
			'TOP',
			'TRY',
			'TTD',
			'TWD',
			'TZS',
			'UAH',
			'UGX',
			'USD',
			'UYU',
			'UZS',
			'VEF',
			'VND',
			'VUV',
			'WST',
			'XAF',
			'XAG',
			'XAU',
			'XBT',
			'XCD',
			'XDR',
			'XOF',
			'XPF',
			'YER',
			'ZAR',
			'ZMK',
			'ZMW',
			'ZWL',
		),
		'luno'            => array( 'EUR', 'ZAR', 'NGN', 'MYR', 'IDR' ),
		'okcoincn'        => array( 'CNY' ),
		'kraken'          => array( 'EUR', 'GBP', 'USD', 'CAD' ),
		'livecoin'        => array( 'EUR', 'USD' ),
	);

	foreach ( $preferred_exchanges_allowed_currencies_pairs as $exchange_name => $preferred_exchanges_allowed_currencies ) {
		construct_maybe_add_exchange( $preferred_exchanges, $woocommerce_currency, $exchange_name, $preferred_exchanges_names[ $exchange_name ], $preferred_exchanges_allowed_currencies );
	}

	return $preferred_exchanges;
}

/**
 * Filter exchange rate providers that support the current WooCommerce store currency.
 *
 * @param array      $preferred_exchanges The preferred exchanges array.
 * @param string     $woocommerce_currency The woocommerce store currency.
 * @param array|true $currencies The currencies that are supported for this exchange.
 * @param string     $exchange_name The exchange name.
 * @param string     $exchange_nice_name The exchange nice name.
 *
 * @return mixed
 */
function construct_maybe_add_exchange( &$preferred_exchanges, $woocommerce_currency, $exchange_name, $exchange_nice_name, $currencies ) {
	if ( true === $currencies || in_array( $woocommerce_currency, $currencies, true ) || 'BTC' === $woocommerce_currency && in_array( 'USD', $currencies, true ) ) {
		$preferred_exchanges[ $exchange_name ] = $exchange_nice_name;
	}

	return $preferred_exchanges;
}

/**
 * Prepare overpayment message option explanation
 *
 * @return string
 */
function redux_overpayment_message_expl() {
	$scaffold     = '%s<p><strong>%s</strong></p><ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>';
	$explanation  = __( 'Customize the message that is sent to the customer upon sending too many coins.', 'cryptowoo' );
	$list_heading = __( 'Available placeholders:', 'cryptowoo' );
	$placeholders = array(
		'{{PERCENTAGE_PAID}}',
		'{{AMOUNT_DIFF}}',
		'{{PAYMENT_CURRENCY}}',
		'{{REFUND_ADDRESS}} (if entered on checkout page)',
	);

	return sprintf( $scaffold, $explanation, $list_heading, $placeholders[0], $placeholders[1], $placeholders[2], $placeholders[3] );
}


/**
 * ---> SET ARGUMENTS
 * All the possible arguments for Redux.
 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
 * */

//add_filter('redux/cryptowoo_payments/panel/templates_path', 'return_redux_panel_template_dir');
function return_redux_panel_template_dir() {
	// @todo add custom redux panel template
	return CWOO_PLUGIN_DIR . '/admin/redux-panel/';

}

//$theme = wp_get_theme(); // For use with some settings. Not necessary.

//$website_button = '<span style="right: 10%; padding-left: 5px;"><a href="http://www.cryptowoo.com" class="button" target="_blank">Visit Website</a></span>';
$db_admin_page        = admin_url( 'admin.php?page=cryptowoo_database_maintenance' );
$database_maintenance = '<span style="right: 10%; padding-left: 5px;"><a class="button-primary" href="' . $db_admin_page . '" title="' . __( 'Database Maintenance', 'cryptowoo' ) . '">' . __( 'Database Maintenance', 'cryptowoo' ) . '</a></span>';
$updateSuccess        = null;

$args = array(
	'opt_name'              => 'cryptowoo_payments',
	'use_cdn'               => false,
	'display_name'          => '<img src="' . CWOO_PLUGIN_PATH . 'assets/images/cryptowoo-redux.png" />',
	'display_version'       => CWOO_VERSION,
	'page_slug'             => 'cryptowoo',
	'page_title'            => __( 'CryptoWoo Options', 'cryptowoo' ),
	'update_notice'         => false,
	'intro_text'            => $database_maintenance,
	//'footer_text' => '<p>This text is displayed below the options panel. It isn\’t required, but more info is always better! The footer_text field accepts all HTML.</p>',
	'admin_bar'             => false,
	'menu_type'             => 'menu',
	'menu_title'            => __( 'CryptoWoo', 'cryptowoo' ),
	'menu_icon'             => CWOO_PLUGIN_PATH . 'assets/images/CryptoWooDarkBG-square-28x28.png',
	'page_priority'         => 59,
	'allow_sub_menu'        => false,
	'page_parent'           => 'cryptowoo',
	'page_parent_post_type' => 'your_post_type',
	'customizer'            => false,
	'default_show'          => true,
	'class'                 => 'cryptowoo',
	'default_mark'          => '',
	'hints'                 => array(
		'icon'          => 'fa fa-info',
		'icon_position' => 'right',
		'icon_size'     => 'normal',
		'tip_style'     => array(
			'color'   => 'light',
			'rounded' => '1',
			'style'   => 'bootstrap',
		),
		'tip_position'  => array(
			'my' => 'top left',
			'at' => 'bottom right',
		),
		'tip_effect'    => array(
			'show' => array(
				'effect'   => 'slide',
				'duration' => '500',
				'event'    => 'mouseover',
			),
			'hide' => array(
				'effect'   => 'slide',
				'duration' => '500',
				'event'    => 'mouseleave unfocus',
			),
		),
	),
	'output'                => true,
	'output_tag'            => true,
	'settings_api'          => true,
	'cdn_check_time'        => '1440',
	'compiler'              => true,
	'page_permissions'      => 'manage_woocommerce',
	'save_defaults'         => true,
	'show_import_export'    => true,
	'transient_time'        => '3600',
	'database'              => 'options',
	'network_sites'         => false,
	'network_admin'         => false,
	'dev_mode'              => false,
);

// Maybe use multisite admin.
if ( defined( 'CWOO_MULTISITE' ) && MULTISITE && is_multisite() ) {
	$args['database']      = 'network';
	$args['network_sites'] = true;
	$args['network_admin'] = true;
}

// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
$args['share_icons'][] = array(
	'url'   => 'https://github.com/CryptoWoo/',
	'title' => 'Visit us on GitHub',
	'icon'  => 'fab fa-github'
	//'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
);
$args['share_icons'][] = array(
	'url'   => 'https://www.facebook.com/CryptoWoo',
	'title' => 'Like us on Facebook',
	'icon'  => 'fab fa-facebook',
);
$args['share_icons'][] = array(
	'url'   => 'https://twitter.com/CryptoWoo',
	'title' => 'Follow us on Twitter',
	'icon'  => 'fab fa-twitter',
);
$args['share_icons'][] = array(
	'url'   => 'https://www.cryptowoo.com/',
	'title' => 'Visit Plugin Website',
	'icon'  => 'fa fa-globe',
);
$args['share_icons'][] = array(
	'url'   => 'http://cryptowoo.zendesk.com/',
	'title' => 'Visit Help Desk',
	'icon'  => 'fa fa-question-circle',
);

Redux::set_args( $opt_name, $args );

/*
 * ---> END ARGUMENTS
 */

/*
 * ---> START HELP TABS
 */

// phpcs:ignore
/*
$tabs = array(
	array(
		'id'      => 'redux-help-tab-1',
		'title'   => __( 'Theme Information 1', 'admin_folder' ),
		'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'admin_folder' )
	),
	array(
		'id'      => 'redux-help-tab-2',
		'title'   => __( 'Theme Information 2', 'admin_folder' ),
		'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'admin_folder' )
	)
);
//Redux::set_help_tab( $opt_name, $tabs );

// Set the help sidebar.
$content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'admin_folder' );
Redux::set_help_sidebar( $opt_name, $content );
*/


/*
 * <--- END HELP TABS
 */
/**
 * Check if php-gd is enabled
 *
 * @return string
 */
function cw_check_php_gd() {
	if ( ! extension_loaded( 'gd' ) ) {
		return sprintf( '<div class="cryptowoo-warning">%s</div>', esc_html__( 'You need the  PHP extension "php-gd" to use this feature. Please check your PHP configuration and make sure the extension is enabled.', 'cryptowoo' ) );
	}
}

/*
 *
 * ---> START SECTIONS
 *
 */
// -> START General Options
Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'General', 'cryptowoo' ),
		'id'         => 'general',
		'subsection' => false,
		'desc'       => __( 'General Options', 'cryptowoo' ),
		'icon'       => 'fa fa-power-off',
	)
);

$enabled_currencies = cw_get_enabled_currencies( false );
$disabled           = array( 'disabled' => __( 'No default currency', 'cryptowoo' ) );

Redux::set_section( $opt_name, array(
	'title'      => __( 'Checkout Flow Configuration', 'cryptowoo' ),
	'id'         => 'general-checkout-flow',
	'icon'       => 'fa fa-wrench',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'                => 'enabled',
			'type'              => 'switch',
			'ajax_save'         => false, // Force page reload on enabling or disabling the payment method gateway.
			'title'             => __( 'Enable/Disable Payment Gateway', 'cryptowoo' ),
			'desc'              => __( 'Enable to display the payment method on the checkout page.', 'cryptowoo' ),
			//'desc'     => __( 'This is the description field, again good for additional info.', 'cryptowoo' ),
			'default'           => false,// true = on | false = off
			'validate_callback' => 'redux_validate_enabled',
		),
		// Order Status.
		array(
			'type'   => 'section',
			'title'  => __( 'Order Status', 'cryptowoo' ),
			'id'     => 'general-order-status-start',
			'indent' => true,
		),
		array(
			'id'       => 'final_order_status',
			'type'     => 'select',
			'title'    => __( 'Final Order Status', 'cryptowoo' ),
			'subtitle' => __( 'Select the WooCommerce order status after the payment has been received.', 'cryptowoo' ),
			'options'  => array(
				'processing' => __( 'Processing', 'cryptowoo' ),
				'completed'  => __( 'Completed', 'cryptowoo' ),
				'on-hold'    => __( 'On Hold', 'cryptowoo' ),
				'disable'    => __( 'Use WooCommerce default logic', 'cryptowoo' ),
			),
			'default'  => 'disable',
			'select2'  => array( 'allowClear' => false ),
			'desc'     => __( 'Set this to "Completed" to give your customer instant access to software downloads or other digital products.', 'cryptowoo' ),
		),
		array(
			'id'       => 'timeout_action',
			'type'     => 'select',
			'title'    => __( 'Timeout Action', 'cryptowoo' ),
			'subtitle' => __( 'Select the behavior of CryptoWoo when the Order Expiration Time runs out.', 'cryptowoo' ),
			'options'  => array(
				'cancelled'     => __( 'Cancelled', 'cryptowoo' ),
				'quote-refresh' => __( 'Quote Refresh: Refresh the cryptocurrency order total and let the customer try again.', 'cryptowoo' ),
			),
			'default'  => 'cancelled',
			'select2'  => array( 'allowClear' => false ),
			'desc'     => __( 'CryptoWoo will set all orders to this status after they time out.', 'cryptowoo' ),
		),
		array(
			'id'        => 'send_quote_refresh_customer_email',
			'type'      => 'switch',
			'ajax_save' => true,
			'title'     => __( 'Send "Quote Expired" Email', 'cryptowoo' ),
			'subtitle'  => __( 'Enable this to notify the customer via email that the cryptocurrency quote has expired.', 'cryptowoo' ),
			'desc'      => __( 'The email contains a link to the payment page so the customer can pick up the order again.', 'cryptowoo' ),
			'default'   => false, // true = on | false = off.
			'required'  => array(
				array( 'timeout_action', '=', 'quote-refresh' ),
			),
		),
		array(
			'id'        => 'send_cancelled_order_customer_email',
			'type'      => 'switch',
			'ajax_save' => true,
			'title'     => __( 'Send "Order Cancelled" Email', 'cryptowoo' ),
			'subtitle'  => __( 'Enable this to notify the customer via email that the order has expired.', 'cryptowoo' ),
			'default'   => false, // true = on | false = off.
			'required'  => array(
				array( 'timeout_action', '=', 'cancelled' ),
			),
		),
		array(
			'id'     => 'general-order-status-end',
			'type'   => 'section',
			'indent' => false,
		),
	),
) );

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Checkout Page', 'cryptowoo' ),
		'id'         => 'general-checkout-page',
		'icon'       => 'fas fa-file-invoice-dollar',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'title',
				'type'     => 'text',
				'title'    => __( 'Gateway Title', 'cryptowoo' ),
				'subtitle' => __(
					'This is the title of the payment gateway the customer will see on the checkout page.',
					'cryptowoo'
				),
				//'desc'     => __( 'Field Description', 'cryptowoo' ),
				'default'  => 'Digital Currencies',
				'validate' => 'no_html',
			),
			array(
				'id'       => 'description',
				'type'     => 'editor',
				'title'    => __( 'Gateway Description', 'cryptowoo' ),
				'subtitle' => __(
					'This is the description the customer can see on the checkout page. Use this to display explanations or instructions before the customer places the order.',
					'cryptowoo'
				),
				'default'  => __( 'Pay with Bitcoin, Litecoin or Dogecoin?', 'cryptowoo' ),
				'args'     => array(
					'teeny'         => true,
					'textarea_rows' => 5,
				),
			),
			array(
				'id'       => 'default_payment_currency',
				'type'     => 'select',
				'title'    => __( 'Default Payment Currency', 'cryptowoo' ),
				'subtitle' => __( 'Pre-select a payment currency.', 'cryptowoo' ),
				'options'  => $enabled_currencies ? array_merge( $disabled, $enabled_currencies ) : $disabled,
				'default'  => 'disabled',
				'select2'  => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'currency_select_style',
				'type'     => 'select',
				'title'    => __( 'Currency Select Field Style', 'cryptowoo' ),
				'subtitle' => __(
					'Change the style of the payment currency select field on the checkout page.',
					'cryptowoo'
				),
				'options'  => array(
					'dropdown' => __( 'Dropdown', 'cryptowoo' ),
					'buttons'  => __( 'Buttons', 'cryptowoo' ),
				),
				'default'  => 'buttons',
				'select2'  => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'estimation_col_count',
				'type'     => 'spinner',
				'title'    => __( 'Currency estimation column width', 'cryptowoo' ),
				'subtitle' => __( 'Width for each cryptocurrency column in the order total estimation.', 'cryptowoo' ),
				'default'  => 3,
				'min'      => 1,
				'step'     => 1,
				'max'      => 12,
			),
			array(
				'id'       => 'display_order_total_estimation',
				'type'     => 'switch',
				'title'    => __( 'Show/Hide Order Total Estimation', 'cryptowoo' ),
				'subtitle' => __(
					'Disable to hide the cryptocurrency order total estimation from the checkout page.',
					'cryptowoo'
				),
				'desc'     => __( 'Show/Hide', 'cryptowoo' ),
				'default'  => true,
			),
			array(
				'id'       => 'display_fiat_rate',
				'type'     => 'switch',
				'title'    => __( 'Show/Hide Fiat Exchange Rate', 'cryptowoo' ),
				'subtitle' => __( 'Disable to hide the underlying exchange rate from the checkout page.', 'cryptowoo' ),
				'desc'     => __( 'Show/Hide', 'cryptowoo' ),
				'default'  => true,
			),
			array(
				'id'       => 'display_rate_source',
				'type'     => 'switch',
				'title'    => __( 'Show/Hide Exchange Rate Provider', 'cryptowoo' ),
				'subtitle' => __(
					'Disable to hide the current exchange rate provider from the checkout page.',
					'cryptowoo'
				),
				'desc'     => __( 'Show/Hide', 'cryptowoo' ),
				'default'  => false,
			),
			array(
				'id'       => 'collect_refund_address',
				'type'     => 'select',
				'title'    => __( 'Refund Addresses', 'cryptowoo' ),
				'subtitle' => __( 'Collect refund addresses and save them to the order meta.', 'cryptowoo' ),
				'options'  => array(
					'disabled' => __( 'Disabled', 'cryptowoo' ),
					'optional' => __( 'Refund address is optional', 'cryptowoo' ),
					'required' => __( 'Refund address is required', 'cryptowoo' ),
				),
				'default'  => 'optional',
				'select2'  => array( 'allowClear' => false ),
			),
		),
	)
);
Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Payment Page', 'cryptowoo' ),
		'id'         => 'general-payment-page',
		'icon'       => 'fas fa-qrcode',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'payment_page_text',
				'type'     => 'editor',
				'title'    => __( 'Payment Page Instructions', 'cryptowoo' ),
				'subtitle' => __( 'Custom instructions to be displayed on the payment page.', 'cryptowoo' ),
				'desc'     => __(
					'Available placeholders: {{PAYMENT_ADDRESS}}, {{CRYPTO_AMOUNT}}, {{PAYMENT_CURRENCY}}',
					'cryptowoo'
				),
				'default'  => '',
				'args'     => array(
					'teeny'         => true,
					'textarea_rows' => 3,
				),
			),
			array(
				'id'       => 'show_countdown',
				'type'     => 'switch',
				'title'    => __( 'Show/Hide Countdown', 'cryptowoo' ),
				'subtitle' => __( 'Hide the countdown and the progress bar from the payment page.', 'cryptowoo' ),
				'desc'     => sprintf(
					__(
						'If you set this to "Off", the customer will not see how much time is left to send the payment before the order expires. Hiding the countdown is generally not recommended but it may be useful if you have configured a very long "Order Expiration Time" and do not want to rush your customer.%sNote: The "Order Expiration Time" setting will be honoured regardless if the countdown is visible or not.%s',
						'cryptowoo'
					),
					'<br><strong>',
					'</strong>'
				),
				'default'  => true,
			),
			array(
				'id'       => 'cw_redirect_on_unconfirmed',
				'type'     => 'switch',
				'title'    => __( 'Redirect on unconfirmed transaction', 'cryptowoo' ),
				'subtitle' => __(
					'Redirect the user to the "Thank You" page as soon as an unconfirmed transaction is detected.',
					'cryptowoo'
				),
				'desc'     => __( 'The redirect will not affect the status of the WooCommerce order.', 'cryptowoo' ),
				'default'  => false,
			),
			array(
				'id'       => 'payment_page_width',
				'type'     => 'select',
				'title'    => __( 'Payment Page Width', 'cryptowoo' ),
				'subtitle' => '',
				'options'  => array(
					'4' => __( 'Narrow', 'cryptowoo' ),
					'6' => __( 'Medium', 'cryptowoo' ),
					'8' => __( 'Wide', 'cryptowoo' ),
				),
				'default'  => '8',
				'select2'  => array( 'allowClear' => false ),
				'desc'     => __(
					'Change this setting if the elements on the payment page are too close together or too far apart.',
					'cryptowoo'
				),
			),
			array(
				'id'       => 'sec_image',
				'type'     => 'spinner',
				'title'    => sprintf(
					__( 'Security Image Order Threshold (%s)', 'cryptowoo' ),
					$woocommerce_currency
				),
				'subtitle' => sprintf(
					__(
						'Display the payment address in an additional image if the order amount in %s is above this value and the customer highlights the payment address.',
						'cryptowoo'
					),
					$woocommerce_currency
				),
				'desc'     => sprintf( '%s%s', __( 'Set to "0" to disable', 'cryptowoo' ), cw_check_php_gd() ),
				'default'  => 100,
				'min'      => 0,
				'step'     => 10,
				'max'      => 9999999,
			),
			array(
				'id'       => 'cw_display_pay_later_button',
				'type'     => 'switch',
				'title'    => __( '"I have sent the payment" Button', 'cryptowoo' ),
				'subtitle' => __( 'Show/Hide', 'cryptowoo' ),
				'desc'     => __(
					'If the customer clicks the "I have sent the payment" button on the payment page, he will be redirected to the receipt page
                           where a notice about the current status of the payment is displayed. This button does not influence whether an order will be confirmed.
                           The processing will continue in the background.',
					'cryptowoo'
				),
				'default'  => false,
			),
			array(
				'id'       => 'cw_display_pay_with_trezor_button',
				'type'     => 'switch',
				'title'    => __( '"Pay with Trezor" Button', 'cryptowoo' ),
				'subtitle' => __( 'Show/Hide', 'cryptowoo' ),
				'default'  => false,
			),
		),
	)
);

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( '"Thank You" Page', 'cryptowoo' ),
		'id'         => 'general-thankyou-page',
		'icon'       => 'fas fa-receipt',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'thankyou_page_text',
				'type'     => 'editor',
				'title'    => __( 'Custom "Thank You" Text', 'cryptowoo' ),
				'subtitle' => __(
					'Custom "payment completed" text on "Thank You" and "View Order" (logged in users only) pages.',
					'cryptowoo'
				),
				'default'  => __( 'Your payment has been received. Thank you for shopping with us.', 'cryptowoo' ),
				'args'     => array(
					'teeny'         => true,
					'textarea_rows' => 3,
				),
			),
		),
	)
);

if ( ! function_exists( 'change_timeout_status_list' ) ) {
	/**
	 * Add custom WooCommerce order statuses to the Timeout Action field options
	 *
	 * @param array $args Arguments.
	 *
	 * @return mixed
	 */
	function change_timeout_status_list( $args ) {
		$statuses = $args['options'];
		if ( ! function_exists( 'wc_get_order_statuses' ) ) {
			return $args;
		}
		$wc_statuses = wc_get_order_statuses();

		// Since the order is not paid when the timeout expires we need to remove some statuses to prevent configuration errors.
		$unwanted_statuses = array_merge( wc_get_is_paid_statuses(), [ 'pending', 'refunded' ] );

		foreach ( $wc_statuses as $key => $nicename ) {
			$status_key = str_replace( 'wc-', '', $key );
			if ( ! in_array( $status_key, $unwanted_statuses ) && ! array_key_exists( $status_key, $statuses ) ) {
				$statuses[ $status_key ] = $nicename;
			}
		}

		$args['options'] = $statuses;

		return $args;
	}
}
add_filter( "redux/options/{$opt_name}/field/timeout_action", 'change_timeout_status_list' );

Redux::set_section( $opt_name, array(
	'title'      => __( 'WordPress Multisite', 'cryptowoo' ),
	'desc'       => __( 'Configure the plugin behavior on <a href="https://codex.wordpress.org/Create_A_Network" target="_blank" title="WordPress Multisite">WordPress Multisite</a> installations.', 'cryptowoo' ),
	'id'         => 'general-multisite',
	'icon'       => 'fab fa-wordpress-simple',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'        => 'network_wide_admin',
			'type'      => 'switch',
			'ajax_save' => false, // Force page reload.
			'title'     => __( 'Network Wide Admin (Multisite only!)', 'cryptowoo' ),
			'subtitle'  => '',
			'desc'      => __( '<strong>Enable:</strong> Use only one instance of CryptoWoo settings for your whole multisite network.<br>
                              <strong>Disable:</strong> The plugin settings for each blog have to be configured seperately.', 'cryptowoo' ),
			'default'   => false,
		),
		array(
			'id'       => 'cryptowoo_multisite_info',
			'type'     => 'info',
			'style'    => 'critical',
			'notice'   => false,
			'required' => array( 'network_wide_admin', 'equals', true ),
			'icon'     => 'fa fa-warning',
			'title'    => __( 'Multisite Setup Info', 'cryptowoo' ),
			/* translators: %1$s: Link to multi-site cron setup article on CryptoWoo website */
			'desc'     => sprintf( __( 'To enable network wide CryptoWoo settings, follow the instructions at %1$s', 'cryptowoo' ), 'https://www.cryptowoo.com/how-to-setup-cron-jobs-for-multisite-wordpress/' ),
		),

	),
) );

// -> START Wallet Settings.
Redux::set_section( $opt_name, array(
	'title' => __( 'Wallet Settings', 'cryptowoo' ),
	'id'    => 'wallets',
	'desc'  => __( 'Wallet Settings', 'cryptowoo' ),
	'icon'  => 'fas fa-wallet',
) );

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Address List', 'cryptowoo' ),
		'id'         => 'wallets-address_list',
		'desc'       => __(
			'Supply a list of addresses to use. Each WooCommerce order will get a unique address assigned. If you enter an invalid address it will be removed.',
			'cryptowoo'
		),
		'subsection' => true,
		'ajax_save'  => false,
		'icon'       => 'fa fa-list',
		'fields'     => array(
			array(
				'id'    => 'info_hd_addon_automation',
				'type'  => 'info',
				'style' => 'info',
				'icon'  => 'el-icon-info-sign',
				'desc'  => sprintf(
					__(
						'%1$sTired of manually adding addresses? Get our %2$sHD Wallet Add-on%3$s to automate the derivation of new addresses!',
						'cryptowoo'
					),
					"",
					'<a href="https://www.cryptowoo.com/shop/cryptowoo-hd-wallet-addon/" target="_blank">',
					'</a>'
				),
			),
			array(
				'id'       => 'send_address_list_alert',
				'type'     => 'switch',
				'title'    => __( 'Email Alert', 'cryptowoo' ),
				'desc'     => sprintf(
					__( 'Send an email alert to %s when less than 5 addresses are left for a currency.', 'cryptowoo' ),
					$admin_email
				),
				'subtitle' => __( 'Enable/Disable email alert', 'cryptowoo' ),
				'default'  => false,
			),
			array(
				'id'    => 'info_address_list',
				'type'  => 'info',
				'style' => 'info',
				'icon'  => 'el-icon-info-sign',
				//'required' => array(array('')),
				'desc'  => __(
					"One address per line. Only add addresses for one currency at a time. Do not forget to click 'Save Changes' after you added the addresses.",
					'cryptowoo'
				),
			),
			array(
				'id'                => 'address_list_btc',
				'type'              => 'textarea',
				'ajax_save'         => true,
				'title'             => sprintf( __( '%s Addresses', 'cryptowoo' ), 'Bitcoin' ),
				'validate_callback' => 'redux_validate_address_list',
				'subtitle'          => sprintf(
					__( 'Current unused %1$s addresses: %2$s%3$s%4$s', 'cryptowoo' ),
					'Bitcoin',
					CW_AddressList::get_address_list_details( 'BTC' ),
					'<br>',
					CW_AddressList::get_delete_list_button( 'BTC' )
				),
				'desc'              => '',
				'hint'              => array(
					'title'   => 'Please Note:',
					'content' => __(
						"One address per line. Only add addresses for one currency at a time. Do not forget to click 'Save Changes' after you added the addresses.",
						'cryptowoo'
					),
				),
			),
			array(
				'id'                => 'address_list_bch',
				'type'              => 'textarea',
				'ajax_save'         => true,
				'title'             => sprintf( __( '%s Addresses', 'cryptowoo' ), 'Bitcoin Cash' ),
				'validate_callback' => 'redux_validate_address_list',
				'subtitle'          => sprintf(
					__( 'Current unused %1$s addresses: %2$s%3$s%4$s', 'cryptowoo' ),
					'Bitcoin Cash',
					CW_AddressList::get_address_list_details( 'BCH' ),
					'<br>',
					CW_AddressList::get_delete_list_button( 'BCH' )
				),
				'desc'              => '',
				'hint'              => array(
					'title'   => 'Please Note:',
					'content' => __(
						"One address per line. Only add addresses for one currency at a time. Do not forget to click 'Save Changes' after you added the addresses.",
						'cryptowoo'
					),
				),
			),
			array(
				'id'                => 'address_list_ltc',
				'type'              => 'textarea',
				'ajax_save'         => false,
				'title'             => sprintf( __( '%s Addresses', 'cryptowoo' ), 'Litecoin' ),
				'validate_callback' => 'redux_validate_address_list',
				'subtitle'          => sprintf(
					__( 'Current unused %1$s addresses: %2$s%3$s%4$s', 'cryptowoo' ),
					'Litecoin',
					CW_AddressList::get_address_list_details( 'LTC' ),
					'<br>',
					CW_AddressList::get_delete_list_button( 'LTC' )
				),
				'desc'              => '',
				'hint'              => array(
					'title'   => 'Please Note:',
					'content' => __(
						"One address per line. Only add addresses for one currency at a time. Do not forget to click 'Save Changes' after you added the addresses.",
						'cryptowoo'
					),
				),
			),
			array(
				'id'                => 'address_list_doge',
				'type'              => 'textarea',
				'ajax_save'         => false,
				'title'             => sprintf( __( '%s Addresses', 'cryptowoo' ), 'Dogecoin' ),
				'validate_callback' => 'redux_validate_address_list',
				'subtitle'          => sprintf(
					__( 'Current unused %1$s addresses: %2$s%3$s%4$s', 'cryptowoo' ),
					'Dogecoin',
					CW_AddressList::get_address_list_details( 'DOGE' ),
					'<br>',
					CW_AddressList::get_delete_list_button( 'DOGE' )
				),
				'desc'              => '',
				'hint'              => array(
					'title'   => 'Please Note:',
					'content' => __(
						"One address per line. Only add addresses for one currency at a time. Do not forget to click 'Save Changes' after you added the addresses.",
						'cryptowoo'
					),
				),
			),
			array(
				'id'                => 'address_list_dogetest',
				'type'              => 'textarea',
				'ajax_save'         => false,
				'title'             => sprintf( __( '%s Addresses', 'cryptowoo' ), 'Dogecoin Testnet' ),
				'validate_callback' => 'redux_validate_address_list',
				'subtitle'          => sprintf(
					__( 'Current unused %1$s addresses: %2$s%3$s%4$s', 'cryptowoo' ),
					'Dogecoin Testnet',
					CW_AddressList::get_address_list_details( 'DOGETEST' ),
					'<br>',
					CW_AddressList::get_delete_list_button( 'DOGETEST' )
				),
				'desc'              => '',
				'hint'              => array(
					'title'   => 'Please Note:',
					'content' => __(
						"One address per line. Only add addresses for one currency at a time. Do not forget to click 'Save Changes' after you added the addresses.",
						'cryptowoo'
					),
				),
			),
		),
	)
);

/**
 * Is the HD Wallet Add-on activated?
 *
 * @return bool
 */
function cw_hd_active() {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	return is_plugin_active( 'cryptowoo-hd-wallet-addon/cryptowoo-hd-wallet-addon.php' ) && file_exists( WP_PLUGIN_DIR . '/cryptowoo-hd-wallet-addon/hdwallet-option.php' );
}

// Maybe include HD Wallet Add-on Settings.
if ( cw_hd_active() ) {
	include_once WP_PLUGIN_DIR . '/cryptowoo-hd-wallet-addon/hdwallet-option.php';
} else {

	Redux::set_section(
		$opt_name,
		array(
			'title'      => __( 'HD Wallet', 'cryptowoo' ),
			'id'         => 'wallets-hdwallet',
			'desc'       => __(
				'<p>Hierarchical deterministic ("<a href="https://bitcoin.org/en/glossary/hd-protocol" target="_blank" title="Bitcoin.org Glossary: HD Protocol">HD</a>") wallets enable you to receive payments directly to addresses under your control without relying on any third party service.
                <br>You need to create an "Extended Public Key" with a <a href="https://github.com/bitcoin/bips/blob/master/bip-0032.mediawiki" target="_blank">BIP32</a> or <a href="https://github.com/bitcoin/bips/blob/master/bip-0044.mediawiki" target="_blank">BIP44</a> compatible client to use this feature. Check out the <a href="http://www.cryptowoo.com/hd-wallet-tutorials?utm_source=config" target="_blank" title="HD Wallet Tutorials">HD wallet tutorial section</a> on our website to learn how to make the most out of CryptoWoo.</p>
                <table>
					<tr><th>Compatible clients</th></tr>
					<tr>
						<td>Bitcoin</td>
						<td><a href="https://electrum.org/" title="Electrum" target="_blank">Electrum</a> | <a title="Mycelium Wallet" href="https://mycelium.com/mycelium-wallet.html" target="_blank">Mycelium Wallet</a> | <a href="https://trezor.io/" target="_blank">TREZOR</a> | <a href="https://www.ledger.com/" target="_blank">Ledger Wallet</a> | <a href="https://github.com/dcpos/bip39" target="_blank">BIP39 Tool</a></td>
					</tr>
					<tr>
						<td>Bitcoin Cash</td>
						<td><a href="https://electroncash.org/" title="Electron Cash" target="_blank">Electron Cash</a> | <a href="https://trezor.io/" target="_blank">TREZOR</a> | <a href="https://www.ledger.com/" target="_blank">Ledger</a> | <a href="https://github.com/dcpos/bip39" target="_blank">BIP39 Tool</a></td>
					</tr>
					<tr>
						<td>Litecoin</td>
						<td><a href="https://electrum-ltc.org/" title="Electrum for Litecoin" target="_blank">Electrum-LTC</a> | <a href="https://trezor.io/" target="_blank">TREZOR</a> | <a href="https://www.ledger.com/" target="_blank">Ledger</a> | <a href="https://github.com/dcpos/bip39" target="_blank">BIP39 Tool</a></td>
					</tr>
					<tr>
						<td>Dogecoin</td>
						<td><a href="https://github.com/dcpos/bip39" target="_blank">BIP39 Tool</a></td>
					</tr>
					<tr>
						<td>BlackCoin</td>
						<td><a href="http://blackcoin.co/" title="BlackCoin Website" target="_blank">More</a></td>
					</tr>
				</table>',
				'cryptowoo'
			),
			'subsection' => true,
			'icon'       => 'fas fa-shield-alt',
			'fields'     => array(
				array(
					'id'    => 'info_hdwallet_addon',
					'type'  => 'info',
					'style' => 'info',
					'icon'  => 'fa fa-info',
					/* translators: %1$s: Link to HD Wallet Add-on on CryptoWoo website, %2$s: html a tag end */
					'desc'  => sprintf( __( 'You need the CryptoWoo HD Wallet Add-on to use this feature. %1$sGet it now!%2$s', 'cryptowoo' ), '<a href="https://www.cryptowoo.com/shop/cryptowoo-hd-wallet-addon/?ref=config-page" target="_blank">', '</a>' ),
				),
			),
		)
	);
}


Redux::set_section(
	$opt_name,
	array(
		'title'      => __(
			'Electrum Daemon',
			'cryptowoo'
		),
		'id'         => 'electrum',
		'desc'       => __(
			'<p>Use the Electrum daemon JSON-RPC interface to look up transactions or to create payment requests in your Electrum wallet.
					  You have to configure your client before using this feature.
					  Refer to the <a href="http://docs.electrum.org/en/latest/merchant.html#requirements" target="_blank">Electrum Documentation</a> for more information.</p>
					  <p><span class="cryptowoo-warning"><strong>Please note:</strong> This is an experimental feature. Proceed with caution!</span></p>',
			'cryptowoo'
		),
		'subsection' => true,
		'icon'       => 'fa fa-atom',
		'fields'     => array(
			array(
				'id'    => 'info_electrum_addon',
				'type'  => 'info',
				'style' => 'info',
				'icon'  => 'fa fa-info',
				/* translators: %1$s: Link to Electrum Daemon Add-on on CryptoWoo website, %2$s: html a tag end */
				'desc'  => sprintf(
					__(
						'You need the CryptoWoo Electrum Daemon Add-on to use this feature. %1$sGet it now!%2$s',
						'cryptowoo'
					),
					'<a href="https://www.cryptowoo.com/shop/cryptowoo-electrum-daemon-addon/?ref=config-page" target="_blank">',
					'</a>'
				),
			),
		),
	)
);


Redux::set_section(
	$opt_name,
	array(
		'title'      => __(
			'Block.io',
			'cryptowoo'
		),
		'id'         => 'wallets-blockio',
		'desc'       => __(
			'Get your API keys in your <a href="http://block.io/" target="_blank">Block.io</a> Administration Console<br>
									 <strong>Create a dedicated Block.io account to handle payments for your store. <br>DO NOT use the same account for other purposes.</strong>',
			'cryptowoo'
		),
		'subsection' => true,
		'icon'       => 'fa fa-cube',
		'fields'     => array(
			// Block.io API Keys.
			array(
				'id'       => 'info_blockio_btc',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el-icon-info-sign',
				'required' => array( 'cryptowoo_btc_mpk', 'not', '' ),
				'desc'     => sprintf(
					__(
						'HD wallet detected. If you want to use the Block.io online wallet, you need to remove the %s Extended Public Key. Using Block.io only as processing API will work fine.',
						'cryptowoo'
					),
					'Bitcoin'
				),
			),
			array(
				'id'                => 'cryptowoo_btc_api',
				'type'              => 'text',
				'desc'              => __(
					'Remove to disable the currency',
					'cryptowoo'
				),
				'title'             => 'BTC API Key',
				'subtitle'          => sprintf(
					__(
						'<a href="http://block.io/" target="_blank">Block.io</a> %s API Key',
						'cryptowoo'
					),
					'Bitcoin'
				),
				'validate_callback' => 'redux_validate_api_key',
				// phpcs:ignore 'required' => array('cryptowoo_btc_mpk','equals',''),
			),
			array(
				'id'       => 'info_blockio_doge',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el-icon-info-sign',
				'required' => array( 'cryptowoo_doge_mpk', 'not', '' ),
				'desc'     => sprintf(
					__(
						'HD wallet detected. If you want to use the Block.io online wallet, you need to remove the %s Extended Public Key. Using Block.io only as processing API will work fine.',
						'cryptowoo'
					),
					'Dogecoin'
				),
			),
			array(
				'id'                => 'cryptowoo_doge_api',
				'type'              => 'text',
				'desc'              => __(
					'Remove to disable the currency',
					'cryptowoo'
				),
				'title'             => 'DOGE API Key',
				'subtitle'          => sprintf(
					__(
						'<a href="http://block.io/" target="_blank">Block.io</a> %s API Key',
						'cryptowoo'
					),
					'Dogecoin'
				),
				'validate_callback' => 'redux_validate_api_key',
				// phpcs:ignore 'required' => array('cryptowoo_doge_mpk','equals',''),
			),
			array(
				'id'       => 'info_blockio_ltc',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el-icon-info-sign',
				'required' => array( 'cryptowoo_ltc_mpk', 'not', '' ),
				'desc'     => sprintf(
					__(
						'HD wallet detected. If you want to use the Block.io online wallet, you need to remove the %s Extended Public Key. Using Block.io only as processing API will work fine.',
						'cryptowoo'
					),
					'Litecoin'
				),
			),
			array(
				'id'                => 'cryptowoo_ltc_api',
				'type'              => 'text',
				'desc'              => __(
					'Remove to disable the currency',
					'cryptowoo'
				),
				'title'             => 'LTC API Key',
				'subtitle'          => sprintf(
					__(
						'<a href="http://block.io/" target="_blank">Block.io</a> %s API Key',
						'cryptowoo'
					),
					'Litecoin'
				),
				'validate_callback' => 'redux_validate_api_key',
				// phpcs:ignore 'required' => array('cryptowoo_ltc_mpk','equals',''),
			),
			// Address archival.
			array(
				'id'       => 'auto_archive_addresses',
				'type'     => 'switch',
				'title'    => __(
					'Archive Block.io Addresses',
					'cryptowoo'
				),
				'desc'     => sprintf(
					__(
						'CryptoWoo will archive up to 100 addresses with a zero balance once every 24 hours.
											<br>Archiving of Block.io wallet addresses helps you control account bloat and enhances the operational security by allowing you to move coins to new addresses without clogging your API call responses.',
						'cryptowoo'
					),
					$admin_email
				),
				'subtitle' => __(
					'Enable/Disable automatic archiving of empty addresses in your Block.io account.',
					'cryptowoo'
				),
				'default'  => false,
			),

			// Testnet Currencies.
			array(
				'id'     => 'testnet-section-start',
				'type'   => 'section',
				'title'  => __(
					'TESTNET Currencies',
					'cryptowoo'
				),
				'icon'   => 'fa fa-flask',
				// phpcs:ignore 'required' => array('wallets-blockio-enable','equals','1'),
				'desc'   => __(
					'Accept BTC/DOGE testnet coins in your store. (testing purposes only!)',
					'cryptowoo'
				),
				'indent' => true,
			),
			array(
				'id'       => 'testmode_enabled',
				'type'     => 'switch',
				'title'    => __(
					'Show Testnet Currency options',
					'cryptowoo'
				),
				'subtitle' => __(
					'Show/Hide Testnet Currencies',
					'cryptowoo'
				),
				'desc'     => __(
					'Remove the API key to disable the currency.',
					'cryptowoo'
				),
				'default'  => false,// phpcs:ignore true = on | false = off
				// phpcs:ignore 'required' => array('wallets-blockio-enable','equals','1').
			),
			array(
				'id'       => 'info_btctest',
				'type'     => 'info',
				'style'    => 'warn',
				'icon'     => 'fa fa-warning',
				'required' => array(
					array( 'cryptowoo_btc_api', 'equals', '' ),
					array( 'testmode_enabled', 'equals', true ),
				),
				'desc'     => sprintf(
					__(
						'Please enter your %1$s Block.io API key above before you enable the %1$s Testnet.',
						'cryptowoo'
					),
					'Bitcoin'
				),
			),
			array(
				'id'                => 'cryptowoo_btctest_api',
				'type'              => 'text',
				'desc'              => sprintf(
					__(
						'This is obtained through the <a href="http://block.io/" target="_blank">Block.io</a> Administration Console - %s API-Key',
						'cryptowoo'
					),
					'BTCTEST'
				),
				'title'             => 'BTCTEST API Key',
				'validate_callback' => 'redux_validate_api_key',
				'required'          => array(
					// phpcs:ignore array('cryptowoo_btctest_mpk','equals',''),
					array( 'cryptowoo_btc_api', 'not', '' ),
					array( 'testmode_enabled', 'equals', true ),
				),
			),
			array(
				'id'       => 'info_dogetest',
				'type'     => 'info',
				'style'    => 'warn',
				'icon'     => 'fa fa-warning',
				'required' => array(
					array( 'cryptowoo_doge_api', 'equals', '' ),
					array( 'testmode_enabled', 'equals', true ),
				),
				'desc'     => sprintf(
					__(
						'Please enter your %1$s Block.io API key above before you enable the %1$s Testnet.',
						'cryptowoo'
					),
					'Dogecoin'
				),
			),
			array(
				'id'                => 'cryptowoo_dogetest_api',
				'type'              => 'text',
				'desc'              => sprintf(
					__(
						'This is obtained through the <a href="http://block.io/" target="_blank">Block.io</a> Administration Console - %s API-Key',
						'cryptowoo'
					),
					'DOGETEST'
				),
				'title'             => 'DOGETEST API Key',
				'validate_callback' => 'redux_validate_api_key',
				'required'          => array(
					// phpcs:ignore array('cryptowoo_dogetest_mpk','equals',''),
					array( 'cryptowoo_doge_api', 'not', '' ),
					array( 'testmode_enabled', 'equals', true ),
				),
			),
			array(
				'id'     => 'testnet-section-end',
				'type'   => 'section',
				'indent' => false,
			),
		),
	)
);


/**
 * Get the coins available for use with the shifty button
 *
 * @param array $enabled_currencies Enabled cryptocurrencies.
 *
 * @return array
 */
function cw_get_shifty_coins( $enabled_currencies ) {

	$select = array( 'disable' => __( 'Disable "Shifty" button', 'cryptowoo' ) );
	foreach ( $enabled_currencies as $enabled_currency => $nice_name ) {
		$select[ $enabled_currency ] = sprintf(
			/* translators: %1$s: Cryptocurrency name, %2$s: Cryptocurrency code (e.g. BTC) */
			__( 'With %1$s as destination currency (receive %2$s from Shapeshift)', 'cryptowoo' ),
			$nice_name,
			$enabled_currency
		);
	}

	return apply_filters( 'cw_get_shifty_coins', $select );
}

 // phpcs:ignore
/*
Redux::set_section(
	$opt_name,
	array(
		'title'      => __(
			'Shapeshift',
			'cryptowoo'
		),
		'id'         => 'wallets-shapeshift',
		'desc'       => '',
		'subsection' => true,
		'icon'       => 'fa fa-recycle',
		'fields'     => array(
			array(
				'id'     => 'shapeshift_disabled_info',
				'type'   => 'info',
				'notice' => false,
				'icon'   => 'fa fa-question-circle',
				'title'  => __(
					'Shifty Button Unavailable',
					'cryptowoo'
				),
				'desc'   => __(
					'Unfortunately Shapeshift discontinued the "Shifty" button. We are working on an integration with the Shapeshift API.',
					'cryptowoo'
				),
			)
			/*
			array(
				'id'      => 'cryptowoo_shapeshift_info',
				'type'    => 'info',
				'notice'    => false,
				'icon'  => 'fa fa-info-circle',
				'title'   => __('Destination Currency', 'cryptowoo'),
				'desc'    => __('The destination currency needs to be enabled in CryptoWoo to show up in the select field below.', 'cryptowoo'),
			),
			array(
				'id'       => 'shapeshift_button',
				'type'     => 'select',
				'title'    => __('Shapeshift "Shifty" Button Destination Currency', 'cryptowoo'),
				'subtitle' => __('The currency you want to receive from Shapeshift after they converted the altcoin of the customer.','cryptowoo'),
				'options'  => cw_get_shifty_coins($enabled_currencies),
				'default'  => 'disable',
				'select2'  => array( 'allowClear' => false ),
				'desc' => sprintf(__('%sPlease Note:%s Using Shapeshift to accept payments requires you to trust that Shapeshift will forward the payments to your address.', 'cryptowoo'), '<strong>', '</strong>')
			),
			array(
				'id'       => 'support_cryptowoo_ss',
				'type'     => 'switch',
				'title'    => __('Support CryptoWoo', 'cryptowoo'),
				'subtitle' => __('Enable this to support CryptoWoo when a customer uses the Shapeshift integration by adding our affiliate ID to the Shifty button on the payment page.', 'cryptowoo'),
				'default'  => false,
			),
			array(
				'id'       => 'shapeshift_affiliate_id',
				'type'     => 'text',
				//'desc'       => __('', 'cryptowoo'),
				'title'    => 'Shapeshift Affiliate ID',
				'subtitle' => __('Want to use your own Shapeshift affiliate ID?', 'cryptowoo'),
				'required' => array('support_cryptowoo_ss','=',false),
			),

		))
);
*/

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Other', 'cryptowoo' ),
		'id'         => 'wallets-other',
		'desc'       => 'This section is for the configuration of add-ons that do not use HD wallet functionality to derive the payment addresses.',
		'subsection' => true,
		'icon'       => 'fas fa-money-bill',
	)
);


// -> START Payment processing.
Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Payment Processing', 'cryptowoo' ),
		'id'         => 'processing',
		'subsection' => false,
		'desc'       => '',
		'icon'       => 'fas fa-shopping-cart',
	)
);

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Cron Scheduling', 'cryptowoo' ),
		'id'         => 'processing-cron',
		'desc'       => __(
			'We are using the default WordPress scheduled events manager ("WP cron") to update the exchange rates and process open orders. ',
			'cryptowoo'
		),
		'subsection' => true,
		'icon'       => 'fas fa-clock',
		'fields'     => array(
			array(
				'id'       => 'soft_cron_interval',
				'type'     => 'select',
				'title'    => __( 'WP-Cron interval', 'cryptowoo' ),
				'subtitle' => sprintf(
					__(
						'Choose an interval for the payment processing and the exchange rates.
		 To save resources, exchange rates will be updated maximum once per minute.
		 %sDon\'t use intervals below one minute together with zeroconf transactions if you expect to have multiple open orders simultaneously as you could run into API request limits.%s',
						'cryptowoo'
					),
					'<strong>',
					'</strong>'
				),
				'options'  => array(
					'seconds_15'  => sprintf(
						__( 'Once every %s seconds', 'cryptowoo' ),
						15
					),
					'seconds_30'  => sprintf(
						__( 'Once every %s seconds', 'cryptowoo' ),
						30
					),
					'seconds_60'  => sprintf(
						_n(
							'Once every minute',
							'Once every %s minutes',
							1,
							'cryptowoo'
						),
						1
					),
					'seconds_120' => sprintf(
						_n(
							'Once every minute',
							'Once every %s minutes',
							2,
							'cryptowoo'
						),
						2
					),
					'seconds_300' => sprintf(
						_n(
							'Once every minute',
							'Once every %s minutes',
							5,
							'cryptowoo'
						),
						5
					),
				),
				'default'  => 'seconds_60',
				'select2'  => array( 'allowClear' => false ),
				'desc'     => __(
					'<strong>Please Note:</strong> Follow the cron setup instructions below to make sure everything is running smoothly.',
					'cryptowoo'
				),
			),
			array(
				'id'       => 'cryptowoo_cronjob_single_info',
				'type'     => 'info',
				'notice'   => false,
				'icon'     => 'fa fa-question-circle',
				'title'    => __(
					'Single Cronjob Setup Info',
					'cryptowoo'
				),
				'desc'     => CW_AdminMain::get_cronjob_info(),
				'required' => array(
					array( 'soft_cron_interval', '!=', 'seconds_30' ),
					array( 'soft_cron_interval', '!=', 'seconds_15' ),
				),
			),
			array(
				'id'       => 'cryptowoo_cronjob_multi_info',
				'type'     => 'info',
				'notice'   => false,
				'icon'     => 'fa fa-question-circle',
				'title'    => __(
					'Multiple Cronjob Setup Info',
					'cryptowoo'
				),
				'desc'     => CW_AdminMain::get_cronjob_info( false ),
				'required' => array(
					array( 'soft_cron_interval', '!=', 'seconds_60' ),
					array( 'soft_cron_interval', '!=', 'seconds_120' ),
					array( 'soft_cron_interval', '!=', 'seconds_300' ),
				),
			),
		),
	)
);

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Confirmations', 'cryptowoo' ),
		'id'         => 'processing-confirmations',
		'desc'       => __(
			'<p>Generally, it is recommended to accept transactions only after they received at least one confirmation in the blockchain.</p><p>Accepting unconfirmed transactions as payment (="0 Confirmations") allows for very fast payment completion but makes high value transactions susceptible to <a target="_blank" title="Read more about double spending in the Bitcoin wiki." href="https://en.bitcoin.it/wiki/Double-spending">double spending</a> attacks.
	<br>Use the order amount threshold and the transaction confidence value settings below to adjust the plugin behavior to the level of fraud risk you are willing to take.
	<br>This risk is lower if you are selling only physical items and manually verify the payment in your favorite block explorer before shipping the order.
	<br>Please note that processing zeroconf transactions requires an additional API call for each open order so you may run into API request limits if you are processing multiple open orders simultaneously.</p>',
			'cryptowoo'
		),
		'subsection' => true,
		'icon'       => 'fa fa-check',
		'fields'     => array(
			array(
				'id'      => 'cryptowoo_btc_min_conf',
				'type'    => 'spinner',
				'title'   => sprintf(
					__( '%s Minimum Confirmations', 'cryptowoo' ),
					'BTC'
				),
				'desc'    => sprintf(
					__(
						'Minimum number of confirmations for <strong>%s</strong> transactions - %s Confirmation Threshold',
						'cryptowoo'
					),
					'Bitcoin',
					'BTC'
				),
				'default' => 1,
				'min'     => 0,
				'step'    => 1,
				'max'     => 100,
			),
			array(
				'id'      => 'cryptowoo_bch_min_conf',
				'type'    => 'spinner',
				'title'   => sprintf(
					__( '%s Minimum Confirmations', 'cryptowoo' ),
					'BCH'
				),
				'desc'    => sprintf(
					__(
						'Minimum number of confirmations for <strong>%s</strong> transactions - %s Confirmation Threshold',
						'cryptowoo'
					),
					'Bitcoin Cash',
					'Bitcoin Cash'
				),
				'default' => 1,
				'min'     => 0,
				'step'    => 1,
				'max'     => 100,
			),
			array(
				'id'       => 'bch_raw_zeroconf',
				'type'     => 'switch',
				'title'    => __(
					'Bitcoin Cash "Raw" Zeroconf',
					'cryptowoo'
				),
				'subtitle' => __(
					'Accept unconfirmed Bitcoin Cash transactions as soon as they are seen on the network.',
					'cryptowoo'
				),
				'desc'     => sprintf(
					__(
						'%sThis practice is generally not recommended. Only enable this if you know what you are doing!%s',
						'cryptowoo'
					),
					'<strong>',
					'</strong>'
				),
				'default'  => false,
				'required' => array(
					//array('processing_api_bch', '=', 'custom'),
					array( 'cryptowoo_bch_min_conf', '=', 0 ),
				),
			),
			array(
				'id'       => 'btc_zeroconf_blockcypher_tkn',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'title'    => __( 'BlockCypher token required', 'cryptowoo' ),
				'required' => array(
					array( 'cryptowoo_btc_min_conf', '<', 1 ),
					array( 'processing_api_btc', '=', 'blockcypher' ),
					array( 'blockcypher_token', '=', '' ),
				),
				'desc'     => __(
					'Please enter your BlockCypher token in the "Blockchain Access" tab to use the BlockCypher confidence metric or set the "Transaction Confidence" option to zero.',
					'cryptowoo'
				),
			),
			array(
				'id'       => 'btc_smartbit_minconf_warning',
				'type'     => 'info',
				'title'    => __( 'No transaction confidence metrics available via smartbit API', 'cryptowoo' ),
				'style'    => 'critical',
				'desc'     => __(
					'Using SoChain transaction confidence metrics instead.',
					'cryptowoo'
				),
				'required' => array(
					array( 'processing_api_btc', '=', 'smartbit' ),
					array( 'cryptowoo_btc_min_conf', '<', 1 ),
				),
			),
			array(
				'id'       => 'btc_custom_minconf_warning',
				'type'     => 'info',
				'title'    => __(
					'No confidence metrics available',
					'cryptowoo'
				),
				'style'    => 'critical',
				'desc'     => __(
					'You may want to enable the third party confidence metrics at the bottom of this page to reduce your risk of double-spend attacks against your custom processing API.',
					'cryptowoo'
				),
				'required' => array(
					array( 'processing_api_btc', '=', 'custom' ),
					array( 'cryptowoo_btc_min_conf', '<', 1 ),
					array( 'custom_api_confidence', '=', false ),
				),
			),
			array(
				'id'      => 'cryptowoo_doge_min_conf',
				'type'    => 'spinner',
				'title'   => sprintf(
					__( '%s Minimum Confirmations', 'cryptowoo' ),
					'DOGE'
				),
				'desc'    => sprintf(
					__(
						'Minimum number of confirmations for <strong>%s</strong> transactions - %s Confirmation Threshold',
						'cryptowoo'
					),
					'Dogecoin',
					'DOGE'
				),
				'default' => 1,
				'min'     => 0,
				'step'    => 1,
				'max'     => 100,
			),
			array(
				'id'       => 'doge_zeroconf_blockcypher_tkn',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'title'    => __( 'BlockCypher token required', 'cryptowoo' ),
				'required' => array(
					array( 'cryptowoo_doge_min_conf', '<', 1 ),
					array( 'processing_api_doge', '=', 'blockcypher' ),
					array( 'blockcypher_token', '=', '' ),
				),
				'desc'     => __(
					'Please enter your BlockCypher token in the "Blockchain Access" tab to use the BlockCypher confidence metric or set the "Transaction Confidence" option to zero.',
					'cryptowoo'
				),
			),
			array(
				'id'       => 'doge_custom_minconf_warning',
				'type'     => 'info',
				'title'    => __( 'No confidence metrics available', 'cryptowoo' ),
				'style'    => 'critical',
				'desc'     => __(
					'You may want to enable the third party confidence metrics at the bottom of this page to reduce your risk of double-spend attacks against your custom processing API.',
					'cryptowoo'
				),
				'required' => array(
					array( 'processing_api_doge', '=', 'custom' ),
					array( 'cryptowoo_doge_min_conf', '<', 1 ),
					array( 'custom_api_confidence', '=', false ),
				),
			),
			array(
				'id'      => 'cryptowoo_ltc_min_conf',
				'type'    => 'spinner',
				'title'   => sprintf(
					__( '%s Minimum Confirmations', 'cryptowoo' ),
					'LTC'
				),
				'desc'    => sprintf(
					__(
						'Minimum number of confirmations for <strong>%s</strong> transactions - %s Confirmation Threshold',
						'cryptowoo'
					),
					'Litecoin',
					'LTC'
				),
				'default' => 1,
				'min'     => 0,
				'step'    => 1,
				'max'     => 100,
			),
			array(
				'id'       => 'ltc_zeroconf_blockcypher_tkn',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'title'    => __(
					'BlockCypher token required',
					'cryptowoo'
				),
				'required' => array(
					array( 'cryptowoo_ltc_min_conf', '<', 1 ),
					array( 'processing_api_ltc', '=', 'blockcypher' ),
					array( 'blockcypher_token', '=', '' ),
				),
				'desc'     => __(
					'Please enter your BlockCypher token in the "Blockchain Access" tab to use the BlockCypher confidence metric or set the "Transaction Confidence" option to zero.',
					'cryptowoo'
				),
			),
			array(
				'id'       => 'ltc_custom_minconf_warning',
				'type'     => 'info',
				'title'    => __( 'No confidence metrics available', 'cryptowoo' ),
				'style'    => 'critical',
				'desc'     => __(
					'You may want to enable the third party confidence metrics at the bottom of this page to reduce your risk of double-spend attacks against your custom processing API.',
					'cryptowoo'
				),
				'required' => array(
					array( 'processing_api_ltc', '=', 'custom' ),
					array( 'cryptowoo_ltc_min_conf', '<', 1 ),
					array( 'custom_api_confidence', '=', false ),
				),
			),
			array(
				'id'      => 'cryptowoo_blk_min_conf',
				'type'    => 'spinner',
				'title'   => sprintf(
					__( '%s Minimum Confirmations', 'cryptowoo' ),
					'BLK'
				),
				'desc'    => sprintf(
					__(
						'Minimum number of confirmations for <strong>%s</strong> transactions - %s Confirmation Threshold',
						'cryptowoo'
					),
					'BlackCoin',
					'BLK'
				),
				'default' => 1,
				'min'     => 1,
				'step'    => 1,
				'max'     => 100,
			),
		),
	)
);

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Zeroconf Threshold', 'cryptowoo' ),
		'id'         => 'processing-zeroconf',
		'desc'       => sprintf(
			__(
				'The maximum order amount in %s for which you want to accept unconfirmed transactions (= Transactions that have been broadcasted to the network but are not yet included in the block chain.)
			If the order amount is higher than the threshold, the plugin will require at least one confirmation.
			When set to "0" the plugin will accept unconfirmed transactions regardless of the order amount (not recommended).',
				'cryptowoo'
			),
			$woocommerce_currency
		),
		'subsection' => true,
		'icon'       => 'fa fa-signal',
		'fields'     => array(
			array(
				'id'       => 'cryptowoo_max_unconfirmed_btc',
				'type'     => 'slider',
				'title'    => sprintf(
					__( '%s zeroconf threshold (%s)', 'cryptowoo' ),
					'Bitcoin',
					$woocommerce_currency
				),
				'desc'     => '',
				'required' => array( 'cryptowoo_btc_min_conf', '<', 1 ),
				'default'  => 100,
				'min'      => 0,
				'step'     => 10,
				'max'      => 500,
			),
			array(
				'id'       => 'cryptowoo_btc_zconf_notice',
				'type'     => 'info',
				'style'    => 'info',
				'notice'   => false,
				'required' => array( 'cryptowoo_btc_min_conf', '>', 0 ),
				'icon'     => 'fa fa-info-circle',
				'title'    => sprintf(
					__( '%s Zeroconf Threshold Disabled', 'cryptowoo' ),
					'Bitcoin'
				),
				'desc'     => sprintf(
					__( 'This option is disabled because you do not accept unconfirmed %s payments.', 'cryptowoo' ),
					'Bitcoin'
				),
			),
			array(
				'id'       => 'cryptowoo_max_unconfirmed_bch',
				'type'     => 'slider',
				'title'    => sprintf(
					__( '%s zeroconf threshold (%s)', 'cryptowoo' ),
					'Bitcoin Cash',
					$woocommerce_currency
				),
				'desc'     => '',
				'required' => array( 'cryptowoo_bch_min_conf', '<', 1 ),
				'default'  => 100,
				'min'      => 0,
				'step'     => 10,
				'max'      => 500,
			),
			array(
				'id'       => 'cryptowoo_bch_zconf_notice',
				'type'     => 'info',
				'style'    => 'info',
				'notice'   => false,
				'required' => array( 'cryptowoo_bch_min_conf', '>', 0 ),
				'icon'     => 'fa fa-info-circle',
				'title'    => sprintf(
					__( '%s Zeroconf Threshold Disabled', 'cryptowoo' ),
					'Bitcoin Cash'
				),
				'desc'     => sprintf(
					__( 'This option is disabled because you do not accept unconfirmed %s payments.', 'cryptowoo' ),
					'Bitcoin Cash'
				),
			),
			array(
				'id'       => 'cryptowoo_max_unconfirmed_doge',
				'type'     => 'slider',
				'title'    => sprintf(
					__( '%s zeroconf threshold (%s)', 'cryptowoo' ),
					'Dogecoin',
					$woocommerce_currency
				),
				'desc'     => '',
				'required' => array( 'cryptowoo_doge_min_conf', '<', 1 ),
				'default'  => 100,
				'min'      => 0,
				'step'     => 10,
				'max'      => 500,
			),
			array(
				'id'       => 'cryptowoo_doge_zconf_notice',
				'type'     => 'info',
				'style'    => 'info',
				'notice'   => false,
				'required' => array( 'cryptowoo_doge_min_conf', '>', 0 ),
				'icon'     => 'fa fa-info-circle',
				'title'    => sprintf(
					__( '%s Zeroconf Threshold Disabled', 'cryptowoo' ),
					'Dogecoin'
				),
				'desc'     => sprintf(
					__( 'This option is disabled because you do not accept unconfirmed %s payments.', 'cryptowoo' ),
					'Dogecoin'
				),
			),
			array(
				'id'       => 'cryptowoo_max_unconfirmed_ltc',
				'type'     => 'slider',
				'title'    => sprintf(
					__( '%s zeroconf threshold (%s)', 'cryptowoo' ),
					'Litecoin',
					$woocommerce_currency
				),
				'desc'     => '',
				'required' => array( 'cryptowoo_ltc_min_conf', '<', 1 ),
				'default'  => 100,
				'min'      => 0,
				'step'     => 10,
				'max'      => 500,
			),
			array(
				'id'       => 'cryptowoo_ltc_zconf_notice',
				'type'     => 'info',
				'style'    => 'info',
				'notice'   => false,
				'required' => array( 'cryptowoo_ltc_min_conf', '>', 0 ),
				'icon'     => 'fa fa-info-circle',
				'title'    => sprintf(
					__( '%s Zeroconf Threshold Disabled', 'cryptowoo' ),
					'Litecoin'
				),
				'desc'     => sprintf(
					__( 'This option is disabled because you do not accept unconfirmed %s payments.', 'cryptowoo' ),
					'Litecoin'
				),
			),
			// phpcs:ignore
			/* // Disabled.
			array(
				'id'       => 'cryptowoo_max_unconfirmed_blk',
				'type'     => 'slider',
				'title'    => sprintf( __( 'BlackCoin zeroconf threshold (%s)', 'cryptowoo' ), $woocommerce_currency ),
				'desc'     => '',
				'required' => array( 'cryptowoo_blk_min_conf', '<', 1 ),
				'default'  => 100,
				'min'      => 0,
				'step'     => 10,
				'max'      => 500,
			),
			array(
				'id'       => 'cryptowoo_blk_zconf_notice',
				'type'     => 'info',
				'style'    => 'info',
				'notice'   => false,
				'required' => array( 'cryptowoo_blk_min_conf', '>', 0 ),
				'icon'     => 'fa fa-info-circle',
				'title'    => sprintf( __( '%s Zeroconf Threshold Disabled', 'cryptowoo' ), 'BlackCoin' ),
				'desc'     => sprintf(
					__( 'This option is disabled because you do not accept unconfirmed %s payments.', 'cryptowoo' ),
					'BlackCoin'
				),
			),
			*/
		),
	)
);

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Transaction Confidence', 'cryptowoo' ),
		'id'         => 'processing-confidence',
		'desc'       => __(
			'To mitigate the risk of unconfirmed transactions, CryptoWoo only accepts transactions with a "Transaction Confidence" of or above the value set below. This rating is calculated by the API provider.
                            Transaction Confidence is basically a rating for the network\'s belief in the probability that a specific transaction will be included in the next block that is mined. More details can be found in the documentation of the respective providers BlockCypher or Block.io.
                            If a double spend is detected for an unconfirmed transaction, its confidence rating falls to 0 and the order status changes to "failed".',
			'cryptowoo'
		),
		'subsection' => true,
		'icon'       => 'fa fa-tachometer-alt',
		'fields'     => array(
			array(
				'title' => __( '"Raw" Zeroconf', 'cryptowoo' ),
				'id'    => 'info_normal',
				'type'  => 'info',
				'desc'  => sprintf(
					__(
						'To accept unconfirmed transaction without any further security checks as soon as they are seen on the network, set the confidence slider to "0". %sBeware that this opens you up to double-spend attacks and the chance that a transaction never receives any confirmation.%s',
						'cryptowoo'
					),
					'<strong>',
					'</strong>'
				),
			),
			array(
				'id'       => 'min_confidence_btc_notice',
				'type'     => 'info',
				'style'    => 'info',
				'notice'   => false,
				'required' => array( 'cryptowoo_btc_min_conf', '>', 0 ),
				'icon'     => 'fa fa-info-circle',
				'title'    => sprintf(
					__( '%s Transaction Confidence Disabled', 'cryptowoo' ),
					'Bitcoin'
				),
				'desc'     => sprintf(
					__( 'This option is disabled because you do not accept unconfirmed %s payments.', 'cryptowoo' ),
					'Bitcoin'
				),
			),
			array(
				'id'       => 'btc_confidence_warning',
				'type'     => 'info',
				'title'    => __( 'Be careful!', 'cryptowoo' ),
				'style'    => 'warning',
				'desc'     => __(
					'Accepting transactions with a low confidence value increases your exposure to double-spend attacks. Only proceed if you don\'t automatically deliver your products and know what you\'re doing.',
					'cryptowoo'
				),
				'required' => array( 'min_confidence_btc', '<', 95 ),
			),
			array(
				'id'         => 'min_confidence_btc',
				'type'       => 'slider',
				'title'      => sprintf(
					__( '%s transaction confidence (%s)', 'cryptowoo' ),
					'Bitcoin',
					'%'
				),
				//'desc'    => '',
				'required'   => array( 'cryptowoo_btc_min_conf', '<', 1 ),
				'default'    => 98.95,
				'min'        => 0,
				'step'       => .01,
				'max'        => 99.99,
				'resolution' => 0.01,
				//'required' => array('processing_api_btc', 'not', 'custom')
			),
			array(
				'id'       => 'min_confidence_doge_notice',
				'type'     => 'info',
				'style'    => 'info',
				'notice'   => false,
				'required' => array( 'cryptowoo_doge_min_conf', '>', 0 ),
				'icon'     => 'fa fa-info-circle',
				'title'    => sprintf(
					__( '%s Transaction Confidence Disabled', 'cryptowoo' ),
					'Dogecoin'
				),
				'desc'     => sprintf(
					__( 'This option is disabled because you do not accept unconfirmed %s payments.', 'cryptowoo' ),
					'Dogecoin'
				),
			),
			array(
				'id'         => 'min_confidence_doge',
				'type'       => 'slider',
				'title'      => sprintf(
					__( '%s transaction confidence (%s)', 'cryptowoo' ),
					'Dogecoin',
					'%'
				),
				//'desc'    => '',
				'required'   => array( 'cryptowoo_doge_min_conf', '<', 1 ),
				'default'    => 98.95,
				'min'        => 0,
				'step'       => .01,
				'max'        => 99.99,
				'resolution' => 0.01,
			),
			array(
				'id'       => 'doge_confidence_warning',
				'type'     => 'info',
				'title'    => __( 'Are you sure you want to do this?', 'cryptowoo' ),
				'style'    => 'warning',
				'desc'     => __(
					'Accepting transactions with a low confidence value increases your exposure to double-spend attacks. Only proceed if you don\'t automatically deliver your products and know what you\'re doing.',
					'cryptowoo'
				),
				'required' => array( 'min_confidence_doge', '<', 95 ),
			),
			array(
				'id'       => 'min_confidence_ltc_notice',
				'type'     => 'info',
				'style'    => 'info',
				'notice'   => false,
				'required' => array( 'cryptowoo_ltc_min_conf', '>', 0 ),
				'icon'     => 'fa fa-info-circle',
				'title'    => sprintf(
					__( '%s Transaction Confidence Disabled', 'cryptowoo' ),
					'Litecoin'
				),
				'desc'     => sprintf(
					__( 'This option is disabled because you do not accept unconfirmed %s payments.', 'cryptowoo' ),
					'Litecoin'
				),
			),
			array(
				'id'         => 'min_confidence_ltc',
				'type'       => 'slider',
				'title'      => sprintf(
					__( '%s transaction confidence (%s)', 'cryptowoo' ),
					'Litecoin',
					'%'
				),
				// 'desc'    => '',
				'required'   => array( 'cryptowoo_ltc_min_conf', '<', 1 ),
				'default'    => 98.95,
				'min'        => 0,
				'step'       => .01,
				'max'        => 99.99,
				'resolution' => 0.01,
			),
			array(
				'id'       => 'ltc_confidence_warning',
				'type'     => 'info',
				'title'    => __( 'Are you sure you want to do this?', 'cryptowoo' ),
				'style'    => 'warning',
				'desc'     => __(
					'Accepting transactions with a low confidence value increases your exposure to double-spend attacks. Only proceed if you don\'t automatically deliver your products and know what you\'re doing.',
					'cryptowoo'
				),
				'required' => array( 'min_confidence_ltc', '<', 95 ),
			),
			array(
				'id'       => 'custom_api_confidence',
				'type'     => 'switch',
				'title'    => __( 'Third Party Confidence Metrics', 'cryptowoo' ),
				'subtitle' => __(
					'Enable this to use the SoChain confidence metrics when accepting zeroconf transactions with your custom Bitcoin, Litecoin, or Dogecoin API.',
					'cryptowoo'
				),
				'default'  => false,
			),
		),
	)
);

$insight_hint = array(
	'title'   => 'Please Note:',
	'content' => __( 'Make sure the root URL of the API has a trailing slash ( / ).', 'cryptowoo' ),
);

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Blockchain Access', 'cryptowoo' ),
		'id'         => 'processing-api',
		'desc'       => sprintf(
			__(
				'To find out if we have received a payment for an order, we have to query transaction data from the currency\'s block chain.
                          If your store exceeds the request limits of the selected API, a fallback will be used and the polling interval will automatically slow down for a while before it goes back to the selected interval.
                          Please note that you have to trust the API provider to deliver honest data. To further improve the privacy and security we recommend to use your own %1$sEsplora%3$s or %2$sInsight API%3$s instance for transaction verification.',
				'cryptowoo'
			),
			'<a href="https://github.com/Blockstream/esplora" title="Esplora API" target="_blank">',
			'<a href="https://github.com/bitpay/insight-api/" title="Insight API" target="_blank">',
			'</a>'
		),
		'icon'       => 'fa fa fa-cubes',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'                => 'processing_api_btc',
				'type'              => 'select',
				'title'             => sprintf(
					__( '%s Processing API', 'cryptowoo' ),
					'Bitcoin'
				),
				'subtitle'          => sprintf(
					__( 'Choose the API provider you want to use to look up %s payments.', 'cryptowoo' ),
					'Bitcoin'
				),
				'options'           => array(
					'esplora_blockstream' => 'Esplora (blockstream.info)',
					// 'bitpay'              => 'BitPay (no testnet)', TODO: Add when bitpay api works.
					'sochain'             => 'SoChain (One address per interval per currency)',
					'smartbit'            => 'Smartbit.com.au (tx confidence via SoChain)',
					'bitcoincom'          => 'Bitcoin.com (explorer.api.bitcoin.com)',
					'blockcypher'         => 'BlockCypher.com',
					'blockio'             => 'Block.io (Enter API keys in "Wallet Settings")',
					'custom_esplora'      => 'Custom Esplora (Enter URL below)',
					'custom'              => __( 'Custom Insight (no testnet)', 'cryptowoo' ),
					'disabled'            => __( 'Disabled', 'cryptowoo' ),
				),
				'desc'              => __(
					'If you use CryptoWoo with a Block.io account, we recommend you also select Block.io here.',
					'cryptowoo'
				),
				'default'           => 'disabled',
				'ajax_save'         => false, // Force page load when this changes
				'validate_callback' => 'redux_validate_processing_api',
				'select2'           => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'processing_api_btc_info',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'required' => array(
					array( 'processing_api_btc', 'equals', 'custom' ),
					array( 'custom_api_btc', 'equals', '' ),
				),
				'desc'     => sprintf(
					__( 'Please enter a valid URL in the field below to use a custom %s processing API', 'cryptowoo' ),
					'Bitcoin'
				),
			),
			array(
				'id'                => 'custom_api_btc',
				'type'              => 'text',
				'title'             => sprintf(
					__( '%s Insight API URL', 'cryptowoo' ),
					'Bitcoin'
				),
				'subtitle'          => sprintf(
					__( 'Connect to any %sInsight API%s instance.', 'cryptowoo' ),
					'<a href="https://github.com/bitpay/insight-api/" title="Insight API" target="_blank">',
					'</a>'
				),
				'desc'              => sprintf(
					__(
						'The root URL of the API instance:%sLink to address:%shttps://insight.bitpay.com/api/addr/12c6DSiU4Rq3P4ZxziKxzrL5LmMBrzjrJX%sRoot URL: %shttps://insight.bitpay.com/api/%s',
						'cryptowoo'
					),
					'<p>',
					'<code>',
					'</code><br>',
					'<code>',
					'</code></p>'
				),
				'placeholder'       => 'https://insight.bitpay.com/api/',
				'required'          => array( 'processing_api_btc', 'equals', 'custom' ),
				'validate_callback' => 'redux_validate_custom_api',
				'ajax_save'         => false,
				'msg'               => __( 'Invalid Bitcoin API URL', 'cryptowoo' ),
				'default'           => '',
				'text_hint'         => $insight_hint,
			),
			array(
				'id'                => 'custom_esplora_api_btc',
				'type'              => 'text',
				'title'             => sprintf(
					__( '%s Esplora API URL', 'cryptowoo' ),
					'Bitcoin'
				),
				'subtitle'          => sprintf(
					__(
						'Enter the The root URL of the API instance to connect to any %sEsplora API%s instance.',
						'cryptowoo'
					),
					'<a href="https://github.com/Blockstream/esplora" title="Insight API" target="_blank">',
					'</a>'
				),
				'desc'              => sprintf(
					__(
						'%sLink to address:%shttps://blockstream.info/api/address/12c6DSiU4Rq3P4ZxziKxzrL5LmMBrzjrJX%sRoot URL: %shttps://blockstream.info/api/%sUse "%s" as placeholder for testnet and liquid endpoint support (=/api/, /testnet/api/, /liquid/api/)',
						'cryptowoo'
					),
					'<p>',
					'<code>',
					'</code><br>',
					'<code>',
					'</code></p>',
					'%s'
				),
				'placeholder'       => 'https://blockstream.info/%sapi/',
				'required'          => array( 'processing_api_btc', 'equals', 'custom_esplora' ),
				'validate_callback' => 'redux_validate_custom_api',
				'ajax_save'         => false,
				'msg'               => __( 'Invalid Bitcoin API URL', 'cryptowoo' ),
				'default'           => '',
				'text_hint'         => $insight_hint,
			),
			array(
				'id'                => 'processing_api_bch',
				'type'              => 'select',
				'title'             => sprintf(
					__( '%s Processing API', 'cryptowoo' ),
					'Bitcoin Cash'
				),
				'subtitle'          => sprintf(
					__( 'Choose the API provider you want to use to look up %s payments.', 'cryptowoo' ),
					'Bitcoin Cash'
				),
				'options'           => array(
					'bitcoincom' => 'Bitcoin.com (explorer.api.bitcoin.com)',
					'custom'     => __( 'Custom (no testnet)', 'cryptowoo' ),
					'disabled'   => __( 'Disabled', 'cryptowoo' ),
				),
				'desc'              => '',
				'default'           => 'disabled',
				'ajax_save'         => false, // Force page load when this changes
				'validate_callback' => 'redux_validate_processing_api',
				'select2'           => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'processing_api_bch_info',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'required' => array(
					array( 'processing_api_bch', 'equals', 'custom' ),
					array( 'custom_api_bch', 'equals', '' ),
				),
				'desc'     => sprintf(
					__( 'Please enter a valid URL in the field below to use a custom %s processing API', 'cryptowoo' ),
					'Bitcoin Cash'
				),
			),
			array(
				'id'                => 'custom_api_bch',
				'type'              => 'text',
				'title'             => sprintf(
					__( '%s Insight API URL', 'cryptowoo' ),
					'Bitcoin Cash'
				),
				'subtitle'          => sprintf(
					__( 'Connect to any %sInsight API%s instance.', 'cryptowoo' ),
					'<a href="https://github.com/bitpay/insight-api/" title="Insight API" target="_blank">',
					'</a>'
				),
				'desc'              => sprintf(
					__(
						'The root URL of the API instance:%sLink to address:%shttps://explorer.api.bitcoin.com/bch/v1/txs?address=%sRoot URL: %shttps://explorer.api.bitcoin.com/bch/v1/%s',
						'cryptowoo-bch-addon'
					),
					'<p>',
					'<code>',
					'</code><br>',
					'<code>',
					'</code></p>'
				),
				'placeholder'       => 'https://explorer.api.bitcoin.com/bch/v1/',
				'required'          => array( 'processing_api_bch', 'equals', 'custom' ),
				'validate_callback' => 'redux_validate_custom_api',
				'ajax_save'         => false,
				'msg'               => __( 'Invalid BCH Insight API URL', 'cryptowoo' ),
				'default'           => '',
				'text_hint'         => array(
					'title'   => 'Please Note:',
					'content' => __( 'Make sure the root URL of the API has a trailing slash ( / ).', 'cryptowoo' ),
				),
			),

			array(
				'id'                => 'processing_api_doge',
				'type'              => 'select',
				'title'             => sprintf(
					__( '%s Processing API', 'cryptowoo' ),
					'Dogecoin'
				),
				'subtitle'          => sprintf(
					__( 'Choose the API provider you want to use to look up %s payments.', 'cryptowoo' ),
					'Dogecoin'
				),
				'options'           => array(
					'sochain'     => 'SoChain (One address per interval per currency)',
					'blockcypher' => __( 'BlockCypher.com (no DOGE testnet)', 'cryptowoo' ),
					'blockio'     => __( 'Block.io (Enter API keys in "Wallet Settings")', 'cryptowoo' ),
					'custom'      => __( 'Custom (no testnet)', 'cryptowoo' ),
					'disabled'    => __( 'Disabled', 'cryptowoo' ),
				),
				'desc'              => __(
					'If you use CryptoWoo with a Block.io account, we recommend you also select Block.io here.',
					'cryptowoo'
				),
				'default'           => 'disabled',
				'ajax_save'         => false, // Force page load when this changes
				'validate_callback' => 'redux_validate_processing_api',
				'select2'           => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'processing_api_doge_info',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'required' => array(
					array( 'processing_api_doge', 'equals', 'custom' ),
					array( 'custom_api_doge', 'equals', '' ),
				),
				'desc'     => sprintf(
					__( 'Please enter a valid URL in the field below to use a custom %s processing API', 'cryptowoo' ),
					'Dogecoin'
				),
			),
			array(
				'id'                => 'custom_api_doge',
				'type'              => 'text',
				'title'             => sprintf(
					__( '%s Insight API URL', 'cryptowoo' ),
					'Dogecoin'
				),
				'subtitle'          => sprintf(
					__( 'Connect to any %sInsight API%s instance.', 'cryptowoo' ),
					'<a href="https://github.com/bitpay/insight-api/" title="Insight API" target="_blank">',
					'</a>'
				),
				'desc'              => sprintf(
					__(
						'The root URL of the API instance:%sLink to address:%shttps://insight.bitpay.com/api/addr/12c6DSiU4Rq3P4ZxziKxzrL5LmMBrzjrJX%sRoot URL: %shttps://insight.bitpay.com/api/%s',
						'cryptowoo'
					),
					'<p>',
					'<code>',
					'</code><br>',
					'<code>',
					'</code></p>'
				),
				'placeholder'       => 'https://insight.bitpay.com/api/',
				'required'          => array( 'processing_api_doge', 'equals', 'custom' ),
				'validate_callback' => 'redux_validate_custom_api',
				'msg'               => __( 'Invalid Dogecoin API URL', 'cryptowoo' ),
				'default'           => '',
				'text_hint'         => $insight_hint,
			),
			array(
				'id'                => 'processing_api_ltc',
				'type'              => 'select',
				'title'             => sprintf(
					__( '%s Processing API', 'cryptowoo' ),
					'Litecoin'
				),
				'subtitle'          => sprintf(
					__( 'Choose the API provider you want to use to look up %s payments.', 'cryptowoo' ),
					'Litecoin'
				),
				'desc'              => __(
					'If you use CryptoWoo with a Block.io account, we recommend you also select Block.io here.',
					'cryptowoo'
				),
				'options'           => array(
					'litecore'    => __( 'Litecore (insight.litecore.io)', 'cryptowoo' ),
					'sochain'     => 'SoChain (One address per interval per currency)',
					'blockcypher' => __( 'BlockCypher.com', 'cryptowoo' ),
					'blockio'     => __( 'Block.io (Enter API keys in "Wallet Settings")', 'cryptowoo' ),
					'custom'      => __( 'Custom (no testnet)', 'cryptowoo' ),
					'disabled'    => __( 'Disabled', 'cryptowoo' ),
				),
				'default'           => 'disabled',
				'ajax_save'         => false, // Force page load when this changes
				'validate_callback' => 'redux_validate_processing_api',
				'select2'           => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'processing_api_ltc_info',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'required' => array(
					array( 'processing_api_ltc', 'equals', 'custom' ),
					array( 'custom_api_ltc', 'equals', '' ),
				),
				'desc'     => sprintf(
					__( 'Please enter a valid URL in the field below to use a custom %s processing API', 'cryptowoo' ),
					'Litecoin'
				),
			),
			array(
				'id'                => 'custom_api_ltc',
				'type'              => 'text',
				'ajax_save'         => false, // Force page load when this changes
				'title'             => sprintf(
					__( '%s Insight API URL', 'cryptowoo' ),
					'Litecoin'
				),
				'subtitle'          => sprintf(
					__( 'Connect to any %sInsight API%s instance.', 'cryptowoo' ),
					'<a href="https://github.com/bitpay/insight-api/" title="Insight API" target="_blank">',
					'</a>'
				),
				'desc'              => sprintf(
					__(
						'The root URL of the API instance:%sLink to address:%shttps://insight.bitpay.com/api/addr/12c6DSiU4Rq3P4ZxziKxzrL5LmMBrzjrJX%sRoot URL: %shttps://insight.bitpay.com/api/%s',
						'cryptowoo'
					),
					'<p>',
					'<code>',
					'</code><br>',
					'<code>',
					'</code></p>'
				),
				'placeholder'       => 'https://insight.bitpay.com/api/',
				'required'          => array( 'processing_api_ltc', 'equals', 'custom' ),
				'validate_callback' => 'redux_validate_custom_api',
				'msg'               => __( 'Invalid Litecoin API URL', 'cryptowoo' ),
				'default'           => '',
				'text_hint'         => $insight_hint,
			),
			array(
				'id'                => 'processing_api_blk',
				'type'              => 'select',
				'ajax_save'         => false,
				// Force page load when this changes
				'title'             => sprintf(
					__( '%s Processing API', 'cryptowoo' ),
					'BlackCoin'
				),
				'subtitle'          => sprintf(
					__( 'Choose the API provider you want to use to look up %s payments.', 'cryptowoo' ),
					'BlackCoin'
				),
				//'desc' => sprintf(__('Currently the %s is the only supported BlackCoin processing API. For installation and configuration instructions please refer to the README in the Blacksight repository on GitHub.', 'cryptowoo'),'<a href="https://github.com/janko33bd/insight-api/tree/blacksight-api" target="_blank" title="Blacksight API">Blacksight API</a>'),
				'options'           => array(
					'cryptoid' => __( 'cryptoID.info', 'cryptowoo' ),
					'custom'   => 'Blacksight',
					'disabled' => __( 'Disabled', 'cryptowoo' ),
				),
				'default'           => 'disabled',
				//'required' => array('hd_enabled', 'equals', true), //array('cryptowoo_blk_mpk', 'not', ''),
				'validate_callback' => 'redux_validate_processing_api',
				'select2'           => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'processing_api_blk_info',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'title'    => __( 'cryptoID API Key required', 'cryptowoo' ),
				'required' => array(
					array( 'processing_api_blk', 'equals', 'cryptoid' ),
				),
				'desc'     => sprintf(
					__(
						'Please enter your cryptoID API key below. Otherwise payment data will be delayed by up to 6 hours.%shttps://chainz.cryptoid.info/api.dws%s',
						'cryptowoo'
					),
					'<br><a href="https://chainz.cryptoid.info/api.dws" title="cryptoID API Docs" target="_blank">',
					'</a></br>'
				),
			),
			array(
				'id'                => 'custom_api_blk',
				'type'              => 'text',
				'ajax_save'         => false, // Force page load when this changes
				'title'             => sprintf(
					__( '%s Insight API URL', 'cryptowoo' ),
					'Blacksight'
				),
				'subtitle'          => sprintf(
					__(
						'Connect to any %sBlacksight API%s instance. For installation and configuration instructions please refer to the README in the Blacksight repository on GitHub.',
						'cryptowoo'
					),
					'<a href="https://github.com/janko33bd/insight-api/tree/blacksight-api" title="Blacksight API" target="_blank">',
					'</a>'
				),
				'desc'              => sprintf(
					__(
						'The root URL of the API instance:%sLink to address:%shttps://insight.bitpay.com/api/addr/12c6DSiU4Rq3P4ZxziKxzrL5LmMBrzjrJX%sRoot URL: %shttps://insight.bitpay.com/api/%s',
						'cryptowoo'
					),
					'<p>',
					'<code>',
					'</code><br>',
					'<code>',
					'</code></p>'
				),
				'placeholder'       => 'https://insight.bitpay.com/api/',
				'required'          => array( 'processing_api_blk', 'equals', 'custom' ),
				'validate_callback' => 'redux_validate_custom_api',
				'msg'               => __( 'Invalid BlackCoin API URL', 'cryptowoo' ),
				'default'           => '',
				'text_hint'         => $insight_hint,
			),
			array(
				'id'                => 'blockcypher_token',
				'type'              => 'text',
				'ajax_save'         => false, // Force page load when this changes
				'desc'              => sprintf(
					__( '%sMore info%s', 'cryptowoo' ),
					'<a href="http://dev.blockcypher.com/#rate-limits-and-tokens" title="BlockCypher Docs: Rate limits and tokens" target="_blank">',
					'</a>'
				),
				'title'             => __( 'BlockCypher Token (optional)', 'cryptowoo' ),
				'subtitle'          => sprintf(
					__( 'Use the API token from your %sBlockCypher%s account.', 'cryptowoo' ),
					'<strong><a href="https://accounts.blockcypher.com/" title="BlockCypher account dashboard" target="_blank">',
					'</a></strong>'
				),
				'validate_callback' => 'redux_validate_token',
			),
			array(
				'id'        => 'cryptoid_api_key',
				'type'      => 'text',
				'ajax_save' => false, // Force page load when this changes
				'desc'      => sprintf(
					__( '%sMore info%s', 'cryptowoo' ),
					'<a href="https://chainz.cryptoid.info/api.dws" title="cryptoID API Docs" target="_blank">',
					'</a>'
				),
				'title'     => __( 'cryptoID API Key (required)', 'cryptowoo' ),
				'subtitle'  => sprintf(
					__(
						'Use the API token from your %sCryptoID%s account.',
						'cryptowoo'
					),
					'<strong><a href="https://chainz.cryptoid.info/api.key.dws" title="Request cryptoID API Key" target="_blank">',
					'</a></strong>'
				),
				//'validate_callback' => 'redux_validate_token',
			),
		),
	) );

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'API Resource Control', 'cryptowoo' ),
		'id'         => 'processing-api-resources',
		'desc'       => '',
		'icon'       => 'fa fa fa-database',
		'subsection' => true,
		'fields'     => array(

			// phpcs:ignore
			/* // Disabled
			array(
				'id'            => 'min_order_age',
				'type'          => 'slider',
				'title'         => __( 'Order Queue Offset (Seconds)', 'cryptowoo' ),
				// TODO fix timezone issues and re-enable
				'subtitle'      => __(
					'The <strong>age of an order in seconds</strong> after which the corresponding payment address is added to the polling queue.',
					'cryptowoo'
				),
				'desc'          => __(
					'Most users take 30 seconds and more to send the payment.<br>
									This option let\'s you save on API resources by only checking payment addresses where the customer already had time to read the payment page and send the transaction.',
					'cryptowoo'
				),
				'default'       => 30,
				'min'           => 1,
				'step'          => 1,
				'max'           => 180,
				'resolution'    => 1,
				'display_value' => 'text',
			),
			*/
			array(
				'id'       => 'processing_fallback',
				'type'     => 'switch',
				'title'    => __( 'Processing API Fallback', 'cryptowoo' ),
				'subtitle' => __(
					'Use a fallback API to process the payments in case the one selected above fails. Retry the originally selected API upon beginning of the next hour.',
					'cryptowoo'
				),
				'desc'     => sprintf(
					__(
						'%sPlease Note:%s If you disable this and your selected API fails repeatedly, orders may time out even though they received a payment.',
						'cryptowoo'
					),
					'<br><strong>',
					'</strong>'
				),
				'default'  => true,
			),
			array(
				'type'   => 'section',
				'title'  => __( 'Low Frequency Update Interval', 'cryptowoo' ),
				'id'     => 'processing-api-resource-control-interval',
				'desc'   => __(
					'Change the update interval for long unpaid orders to reduce the number of API requests.
	            Example: Update orders that are unpaid for longer than three days only once every three hours.',
					'cryptowoo'
				),
				'indent' => true,
			),
			array(
				'id'       => 'long_unpaid_threshold_hr',
				'type'     => 'spinner',
				'title'    => __( 'Order Age Threshold (Hours)', 'cryptowoo' ),
				'subtitle' => sprintf(
					__(
						'The %sage in hours%s after which the plugin will look up the transactions for an order in the Low Frequency Update Interval set below.',
						'cryptowoo'
					),
					'<strong>',
					'</strong>',
					'<br>'
				),
				'desc'     => __(
					'Use this option to keep the background processing queue free to process more time-sensitive orders. If an order is unpaid for longer than this value in hours, the plugin will  will look up transactions for it less frequently to save resources.',
					'cryptowoo'
				),
				'default'  => 3 * 24,
				'min'      => 1,
				'step'     => 1,
				'max'      => 14 * 24,
			),
			array(
				'id'       => 'long_unpaid_update_interval_hr',
				'type'     => 'spinner',
				'title'    => __( 'Low Frequency Update Interval (Hours)', 'cryptowoo' ),
				'subtitle' => sprintf(
					__(
						'Select the %supdate interval in hours%s for orders that are open for longer than the Order Age Threshold above.',
						'cryptowoo'
					),
					'<strong>',
					'</strong>'
				),
				'desc'     => sprintf(
					__(
						'The higher you set this value, the more resources you will save, but it will also take longer to detect the payments.%sExample: Setting this to 2 will update less time-sensitive orders only once every 2 hours.',
						'cryptowoo'
					),
					'<br>'
				),
				'default'  => 1,
				'min'      => 1,
				'step'     => 1,
				'max'      => 24,
			),
			array(
				'id'     => 'processing-api-resource-control-interval-end',
				'type'   => 'section',
				'indent' => false,
			),
			array(
				'id'       => 'limit_blockcypher_rate',
				'type'     => 'switch',
				'title'    => __( 'Prevent BlockCypher Rate Limiting', 'cryptowoo' ),
				'subtitle' => __(
					'Slows down the cron interval depending on the number of concurrent orders and the time until the limit counter resets. This setting tries to prevent hitting BlockCypher API request limits when several orders are in the queue at the same time but may increase the order completion time of these orders.',
					'cryptowoo'
				),
				'desc'     => sprintf(
					__(
						'If you are frequently hitting the limit you may want to upgrade to a paid BlockCypher account or enable the "Processing API Fallback" to use a different processing API until the BlockCypher request limits have been reset. Check the %scryptowoo-tx-update.log%s to find out if your store is hitting the limit.',
						'cryptowoo'
					),
					'<code>',
					'</code>'
				),
				'default'  => false,
			),
		),
	)
);
Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Advanced Options', 'cryptowoo' ),
		'id'         => 'processing-advanced',
		'subsection' => true,
		'desc'       => '',
		'icon'       => 'fa fa-tasks',
		'fields'     => array(
			array(
				'id'       => 'info_long_expiration_time',
				'type'     => 'info',
				'style'    => 'warning',
				'icon'     => 'el-icon-info-sign',
				'title'    => __( 'Long Order Expiration Time', 'cryptowoo' ),
				'required' => array( 'order_timeout_min', '>', 1400 ),
				'desc'     => __(
					'You can fine-tune the interval of less time-sensitive orders under Payment Processing > API Resource Control > Low Frequency Update Interval.
		        This will prevent long unpaid or abandoned orders from clogging up your background processing queue.',
					'cryptowoo'
				),
			),
			array(
				'id'       => 'order_timeout_min',
				'type'     => 'spinner',
				'title'    => __( 'Order Expiration Time (Minutes)', 'cryptowoo' ),
				'subtitle' => sprintf(
					__(
						'The %sduration in minutes%s after which the order expires and no further payment will be accepted.%s
                                        %sNote%s: If the full amount is seen on the network (confirmed & unconfirmed) but the payment is not receiving the required confirmations within this limit,
                                        the "Network Congestion Handling" option below can be used to put orders on hold that take too long to confirm.',
						'cryptowoo'
					),
					'<strong>',
					'</strong>',
					'<br>',
					'<strong>',
					'</strong>'
				),
				'desc'     => sprintf(
					__(
						'%sYour exchange rate volatility risk increases the more time you give your customers to pay for their order.%s
                                        If you set this value low, your customers will have less time to pay before the order status is set to cancelled.%s',
						'cryptowoo'
					),
					'<strong>',
					'<br>',
					'</strong><br>'
				),
				'default'  => 30,
				'min'      => 1,
				'step'     => 1,
				'max'      => 20160,
			),
			array(
				'id'            => 'kill_unconfirmed_after',
				'type'          => 'slider',
				'title'         => __( 'Network Congestion Handling (Hours)', 'cryptowoo' ),
				'subtitle'      => __(
					'The <strong>duration in hours</strong> after which the order is set to "on hold" even though the full amount is seen on the network but the transaction does not receive the required confirmations.<br>
                                        <strong>Note: This applies only to orders where the full amount is seen on the network but does not get confirmed.
                                        All orders with no or insufficient payments (=less than the lower boundary of the "Underpayment Notice Range") get cancelled after the "Order Expiration Time".</strong>',
					'cryptowoo'
				),
				'desc'          => __(
					'If you leave this at "0", all orders that have the full amount incoming will stay open until the transaction confirms.
                                        This may be helpful when transactions take longer than usual before they are included in a block because the transaction fees are too low.<br>
                                        <strong>The order status will be set to "cancelled" if the transaction is dropped from the memory pool of the selected processing API.</strong>',
					'cryptowoo'
				),
				'default'       => 0,
				'min'           => 0,
				'step'          => 1,
				'max'           => 72,
				'resolution'    => 1,
				'display_value' => 'text',
			),
			array(
				'type'   => 'section',
				'title'  => __( 'Underpayments', 'cryptowoo' ),
				'id'     => 'processing-advanced-underpayments-start',
				//'required' => array('wallets-blockio-enable','equals','1'),
				'desc'   => __( 'Handling of underpayments', 'cryptowoo' ),
				'indent' => true,
			),
			array(
				'id'            => 'underpayment_notice_range',
				'type'          => 'slider',
				'title'         => __( 'Underpayment Notice Range (%)', 'cryptowoo' ),
				'subtitle'      => sprintf(
					__(
						'The range expressed as a percentage of the order amount in which the customer receives a notification and is given another "Order Expiration Time" to pay for the order.
                                       %sIf the customer fails to send the missing amount within the extended time, the order will be removed from the background polling queue and the order status will change to "On Hold".%s',
						'cryptowoo'
					),
					'<strong>',
					'</strong>'
				),
				'desc'          => __(
					'A customer that sends more than the lower boundary but less than the upper boundary will be notified and the timeout will be extended.<br>
                                        Examples:<br>- If you set the lower boundary to "90", a customer that sends more than 90% of the order amount will be notified and given more time to send the missing amount.<br>
                                       - If you set the upper boundary to "98", an order will be completed even if the amount received is 2% lower than the amount due.<br>
                                       - Set both values to "100" to disable underpayment handling completely. All orders that receive less than 100% of the amount will be set to "cancelled" after the "Order Expiration Time".',
					'cryptowoo'
				),
				"default"       => array(
					1 => 95,
					2 => 100,
				),
				"min"           => 5,
				"step"          => .1,
				"max"           => 100,
				'resolution'    => .1,
				'display_value' => 'float',
				'handles'       => 2,
			),
			array(
				'id'        => 'underpayment_notice_trigger',
				'type'      => 'select',
				'title'     => __( 'Underpayment Notice Trigger', 'cryptowoo' ),
				'subtitle'  => __( 'Which event shall trigger the underpayment e-mail to the customer?', 'cryptowoo' ),
				'desc'      => __(
					'If you have configured an "Order Expiration Time" of more than one hour, you may want to trigger the underpayment notice when the transaction that contains the insufficient amount receives the required number of confirmations instead of waiting until shortly before the "Order Expiration Time" is reached.',
					'cryptowoo'
				),
				'options'   => array(
					120                  => sprintf(
						_n(
							'One minute before order expiration',
							'%d minutes before order expiration',
							2,
							'cryptowoo'
						),
						2
					),
					300                  => sprintf(
						_n(
							'One minute before order expiration',
							'%d minutes before order expiration',
							5,
							'cryptowoo'
						),
						5
					),
					// 600    => __('10 minutes before order expiration', 'cryptowoo'), // TODO more options for time-based underpayment notice trigger
					'confirmed_first_tx' => __( 'Insufficient amount confirmed in blockchain', 'cryptowoo' ),
				),
				'default'   => 300,
				'ajax_save' => false, // Force page load when this changes
				'select2'   => array( 'allowClear' => false ),
			),
			// Overpayment message
			array(
				'type'   => 'section',
				'title'  => __( 'Overpayments', 'cryptowoo' ),
				'id'     => 'processing-advanced-overpayments-start',
				//'required' => array('wallets-blockio-enable','equals','1'),
				'desc'   => __( 'Handling of overpayments', 'cryptowoo' ),
				'indent' => true,
			),
			array(
				'id'      => 'overpayment_handling_enabled',
				'type'    => 'switch',
				'title'   => __( 'Enable/Disable Overpayment Handling', 'cryptowoo' ),
				//'subtitle' => __('', 'cryptowoo'),
				'desc'    => __( 'Set this to "Off" to ignore all overpayments.', 'cryptowoo' ),
				'default' => true,
			),
			array(
				'id'            => 'overpayment_buffer',
				'type'          => 'slider',
				'title'         => __( 'Overpayment Buffer (%)', 'cryptowoo' ),
				'subtitle'      => __(
					'The overpayment buffer is the percentage up to which the received amount may exceed the order amount before it is considered as an overpayment.',
					'cryptowoo'
				),
				'desc'          => __(
					'This is useful to ignore slight overpayments where the customer rounds his transaction amount up.<br>
                                       If you set this to "0", all orders that receive more than the exact amount will trigger an overpayment.',
					'cryptowoo'
				),
				'default'       => 1,
				'min'           => 0,
				'step'          => .1,
				'max'           => 100,
				'resolution'    => .1,
				'display_value' => 'text',
			),
			array(
				'id'       => 'overpayment_message',
				'type'     => 'editor',
				'title'    => __( 'Overpayment Message Text', 'cryptowoo' ),
				'subtitle' => redux_overpayment_message_expl(),
				'default'  => __(
					'You paid {{AMOUNT_DIFF}} {{PAYMENT_CURRENCY}} too much. Please get in touch with us.',
					'cryptowoo'
				),
				'desc'     => sprintf(
					'<p><strong>%s:</strong></p>%s<br>%s',
					__( 'Example', 'cryptowoo' ),
					__(
						'You paid {{PERCENTAGE_PAID}}% ({{AMOUNT_DIFF}} {{PAYMENT_CURRENCY}}) too much. You will receive a refund to {{REFUND_ADDRESS}} within 48 hours.',
						'cryptowoo'
					),
					__(
						'You paid 2% (0.03 BTC) too much. You will receive a refund to 1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa within 48 hours.',
						'cryptowoo'
					)
				),
				'args'     => array(
					'teeny'         => true,
					'textarea_rows' => 3,
				),
			),
			array(
				'id'     => 'processing-advanced-overpayments-end',
				'type'   => 'section',
				'indent' => false,
			),
		),
	) );


// -> START Exchange Rate Settings
Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Pricing', 'cryptowoo' ),
		'id'    => 'rates',
		'desc'  => '',
		'icon'  => 'fas fa-dollar-sign',
	) );
$woocommerce_currency = cw_get_woocommerce_currency();
$blk_bittrex          = cw_hd_active(
) ? ', <a href="https://bittrex.com/api/v1.1/public/getticker/?market=BTC-LTC" target="_blank">here</a>, and <a href="https://bittrex.com/api/v1.1/public/getticker/?market=BTC-BLK" target="_blank">here</a>' : ' and <a href="https://bittrex.com/api/v1.1/public/getticker/?market=BTC-LTC" target="_blank">here</a>.';
Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Preferred Exchanges', 'cryptowoo' ),
		'id'         => 'rates-exchange',
		'icon'       => 'fa fa-chart-line',
		'desc'       => sprintf(
			__(
				'If there is a problem with the preferred exchange\'s API, CryptoWoo uses the lowest exchange rate provided by SoChain
                                        (the rate that results in the highest amount of digital currency for the given price).
                                        Additionally, if an error or problem has been detected, the plugin notifies the administrator by sending an e-mail to %s%s%s',
				'cryptowoo'
			),
			'<strong>',
			$admin_email,
			'</strong>'
		),
		'subsection' => true,
		'fields'     => array(
			array(
				'id'    => 'rates_info',
				'type'  => 'info',
				//'style'    => 'warning',
				'title' => __(
					'Exchange Rate Providers',
					'cryptowoo'
				),
				'desc'  => sprintf(
					'<a href="https://coingecko.com/api/documentations/v3" target="_blank"><strong>CoinGecko</strong></a>: Realtime prices from <a href="https://api.coingecko.com/api/v3/simple/price?include_last_updated_at=true&ids=bitcoin&vs_currencies=%s" target="_blank">here</a><br>
							<a href="https://docs.coincap.io/" target="_blank"><strong>CoinCap</strong></a>: Realtime prices from <a href="https://api.coincap.io/v2/assets/bitcoin" target="_blank">here</a><br>
                            <a href="https://bitcoinaverage.com/api/" target="_blank"><strong>Bitcoinaverage</strong></a>: Last price from <a href="https://apiv2.bitcoinaverage.com/indices/global/ticker/short?crypto=BTC&fiats=%s" target="_blank">here</a>. <a href="https://bitcoinaverage.com/en/methodology" title="Index Calculation Info" target="_blank">Index Calculation Info</a> NOTE: Results will be cached locally for ~10 minutes<br>
                            <a href="https://block.io/api/" target="_blank"><strong>Block.io (API keys required)</strong></a>: Lowest exchange rate from supported exchanges (= best rate for merchant)<br>
                            <a href="https://bitpay.com/api/" target="_blank"><strong>BitPay</strong></a>: Realtime prices from <a href="https://bitpay.com/api/rates" target="_blank">here</a><br>
                            <a href="https://www.bitstamp.net/api/" target="_blank"><strong>Bitstamp</strong></a>: Realtime prices from <a href="https://www.bitstamp.net/api/v2/ticker/btcusd/" target="_blank">here</a> or <a href="https://www.bitstamp.net/api/v2/ticker/btceur/" target="_blank">here</a><br>
                            <a href="https://docs.pro.coinbase.com/" target="_blank"><strong>Coinbase Pro</strong></a>: Realtime prices from <a href="https://api.pro.coinbase.com/products/BTC-USD/ticker" target="_blank">here</a>, <a href="https://api.pro.coinbase.com/products/BTC-EUR/ticker" target="_blank">here</a>, or <a href="https://api.pro.coinbase.com/products/BTC-CAD/ticker" target="_blank">here</a><br>
                            <a href="http://dogecoinaverage.com/" target="_blank"><strong>Dogecoinaverage</strong></a>: Volume weighted price from <a href="http://dogecoinaverage.com/BTC.json" target="_blank">here</a><br>
                            <a href="https://shapeshift.io/" target="_blank"><strong>ShapeShift</strong></a>: Realtime prices from <a href="https://shapeshift.io/rate/doge_btc" target="_blank">here</a>, <a href="https://shapeshift.io/rate/ltc_btc" target="_blank">here</a>, and <a href="https://shapeshift.io/rate/blk_btc" target="_blank">here</a><br>
                            <a href="https://poloniex.com/" target="_blank"><strong>Poloniex</strong></a>: Realtime prices from <a href="https://poloniex.com/public?command=returnTicker" target="_blank">here</a><br>
                            <a href="https://bittrex.com/Home/Api" target="_blank"><strong>Bittrex</strong></a>: Realtime prices from <a href="https://bittrex.com/api/v1.1/public/getticker/?market=BTC-DOGE" target="_blank">here</a>%s<br>
                            <a href="https://blockchain.info/api/exchange_rates_api" target="_blank"><strong>Blockchain.info</strong></a>: Realtime prices from <a href="https://blockchain.info/ticker" target="_blank">here</a><br>
                            <a href="http://bitcoincharts.com/about/markets-api/" target="_blank"><strong>Bitcoincharts.com</strong></a>: 24h weighted average prices from <a href="http://api.bitcoincharts.com/v1/weighted_prices.json" target="_blank">here</a><br>
                            <a href="https://sochain.com/api/" target="_blank"><strong>SoChain</strong></a> (fallback only) : Lowest exchange rate from supported exchanges (= best rate for merchant) <a href="https://sochain.com/api/v2/get_price/BTC/USD" target="_blank">here</a>, <a href="https://sochain.com/api/v2/get_price/BTC/DOGE" target="_blank">here</a>, and <a href="https://sochain.com/api/v2/get_price/BTC/LTC" target="_blank">here</a><br>
                            <a href="http://coindesk.com/api/" target="_blank"><strong>Coindesk.com</strong></a>: Bitcoin Price Index (BPI) real-time data from <a href="https://api.coindesk.com/v1/bpi/currentprice/%1$s.json" target="_blank">here</a><br>
                            <a href="https://luno.com/en/api/" target="_blank"><strong>Luno.com</strong></a>: Realtime prices from <a href="https://api.mybitx.com/api/1/ticker?pair=XBT%1$s" target="_blank">here</a><br>
                            <a href="https://www.okcoin.com/rest_api.html" target="_blank"><strong>OKCoin.com</strong></a>: Realtime prices from <a href="https://www.okcoin.com/api/v1/ticker.do?symbol=btc_usd" target="_blank">here</a><br>
                            <a href="https://www.okcoin.cn/rest_api.html" target="_blank"><strong>OKCoin.cn</strong></a>: Realtime prices from <a href="https://www.okcoin.cn/api/v1/ticker.do?symbol=btc_cny" target="_blank">here</a><br>
                            <a href="https://www.kraken.com/help/api" target="_blank"><strong>Kraken</strong></a>: Realtime prices from <a href="https://api.kraken.com/0/public/Ticker?pair=XBTUSD" target="_blank">here</a>, <a href="https://api.kraken.com/0/public/Ticker?pair=XBTGBP" target="_blank">here</a> and <a href="https://api.kraken.com/0/public/Ticker?pair=XBTEUR" target="_blank">here</a><br>
                            <br><a class="button" href="%s&update_exchange_data=1&submit=1" title="CryptoWoo Database Actions">Update exchange rates manually</a>',
					$woocommerce_currency,
					$woocommerce_currency,
					$blk_bittrex,
					$db_actions_url
				),
			),
			//$limited_api_warning,.
			array(
				'id'                => 'preferred_exchange_btc',
				'type'              => 'select',
				'title'             => sprintf(
					'Bitcoin Exchange (BTC/%s)',
					cw_get_woocommerce_currency()
				),
				'subtitle'          => sprintf(
					__(
						'Choose the exchange you prefer to use to calculate the <strong>Bitcoin to %s exchange rate</strong>.',
						'cryptowoo'
					),
					cw_get_woocommerce_currency()
				),
				'options'           => construct_preferred_exchange_array(),
				'default'           => 'coingecko',
				'ajax_save'         => false, // Force page load when this changes
				'validate_callback' => 'redux_validate_exchange_api',
				'select2'           => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'info_preferred_exchange_btc',
				'type'     => 'info',
				'style'    => 'warning',
				'icon'     => 'el-icon-info-sign',
				'required' => array( 'preferred_exchange_btc', 'equals', 'bitcoinaverage' ),
				'desc'     => __(
					'Bitcoinaverage exchange rates will be cached for ~10 minutes to stay below the request limits for free accounts',
					'cryptowoo'
				),
			),
			array(
				'id'       => 'info_preferred_exchange_btc2',
				'type'     => 'info',
				'style'    => 'warning',
				'icon'     => 'el-icon-info-sign',
				'required' => array( 'preferred_exchange_btc', 'equals', 'bitcoincharts' ),
				'desc'     => __(
					'Bitcoincharts.com exchange rates will be cached for ~15 minutes to stay below the request limits for free accounts',
					'cryptowoo'
				),
			),
			array(
				'id'                => 'bc_info_tor',
				'type'              => 'text',
				'desc'              => __( 'Leave empty to connect to the clearnet URL', 'cryptowoo' ),
				'title'             => 'SOCKS5 Proxy',
				'subtitle'          => __(
					'Connect to blockchain.info\'s hidden service (blockchainbdgpzk.onion) via SOCKS5 proxy',
					'cryptowoo'
				),
				'placeholder'       => 'localhost:9050',
				'validate_callback' => 'redux_validate_socks5_proxy_url',
				'required'          => array( 'preferred_exchange_btc', 'equals', 'blockchain_info' ),
			),
			array(
				'id'                => 'preferred_exchange_bch',
				'type'              => 'select',
				'title'             => 'Bitcoin Cash Exchange (BCH/BTC)',
				'subtitle'          => sprintf(
					__(
						'Choose the exchange you prefer to use to calculate the %sBitcoin Cash to Bitcoin exchange rate%s',
						'cryptowoo'
					),
					'<strong>',
					'</strong>.'
				),
				'desc'              => sprintf(
					__( 'Cross-calculated via BTC/%s', 'cryptowoo' ),
					$woocommerce_currency
				),
				'options'           => array(
					'coingecko'  => 'CoinGecko',
					'binance'    => 'Binance',
					'coinbase'   => 'Coinbase',
					'bittrex'    => 'Bittrex',
					'poloniex'   => 'Poloniex',
					// 'bitfinex'   => 'Bitfinex', TODO: Add bitfinex, only the pair BCHN:USD is supported.
					'bitstamp'   => 'Bitstamp',
					'bitpay'     => 'BitPay',
					'shapeshift' => 'ShapeShift',
					'livecoin'   => 'Livecoin',
					'okcoin'     => 'OKCoin.com',
				),
				'default'           => 'coingecko',
				'ajax_save'         => false, // Force page load when this changes.
				'validate_callback' => 'redux_validate_exchange_api',
				'select2'           => array( 'allowClear' => false ),
			),
			array(
				'id'                => 'preferred_exchange_doge',
				'type'              => 'select',
				'title'             => 'Dogecoin Exchange (DOGE/BTC)',
				'subtitle'          => __(
					'Choose the exchange you prefer to use to calculate the <strong>Dogecoin to Bitcoin exchange rate</strong>.',
					'cryptowoo'
				),
				'desc'              => sprintf(
					__( 'Cross-calculated via BTC/%s', 'cryptowoo' ),
					$woocommerce_currency
				),
				'options'           => array(
					'coingecko'       => 'CoinGecko',
					'blockio'         => 'Block.io (Enter API keys in "Wallet Settings")',
					'dogecoinaverage' => 'DogecoinAverage.com',
					'shapeshift'      => 'ShapeShift',
					'poloniex'        => 'Poloniex',
					'bittrex'         => 'Bittrex',
				),
				'default'           => 'coingecko',
				'ajax_save'         => false, // Force page load when this changes.
				'validate_callback' => 'redux_validate_exchange_api',
				'select2'           => array( 'allowClear' => false ),
			),
			array(
				'id'                => 'preferred_exchange_ltc',
				'type'              => 'select',
				'title'             => 'Litecoin Exchange (LTC/BTC)',
				'subtitle'          => 'Choose the exchange you prefer to use to calculate the <strong>Litecoin to Bitcoin exchange rate</strong>.',
				'desc'              => sprintf(
					__( 'Cross-calculated via BTC/%s', 'cryptowoo' ),
					$woocommerce_currency
				),
				'options'           => array(
					'coingecko'  => 'CoinGecko',
					'blockio'    => 'Block.io (Enter API keys in "Wallet Settings")',
					//'btc_e'             => 'BTC-e',
					'bitfinex'   => 'Bitfinex',
					'shapeshift' => 'ShapeShift',
					'poloniex'   => 'Poloniex',
					'bittrex'    => 'Bittrex',
					'binance'    => 'Binance',
				),
				'default'           => 'coingecko',
				'ajax_save'         => false, // Force page load when this changes.
				'validate_callback' => 'redux_validate_exchange_api',
				'select2'           => array( 'allowClear' => false ),
			),
			array(
				'id'                => 'preferred_exchange_blk',
				'type'              => 'select',
				'title'             => 'BlackCoin Exchange (BLK/BTC)',
				'subtitle'          => 'Choose the exchange you prefer to use to calculate the <strong>BlackCoin to Bitcoin exchange rate</strong>.',
				'desc'              => sprintf(
					__( 'Cross-calculated via BTC/%s', 'cryptowoo' ),
					$woocommerce_currency
				),
				'options'           => array(
					'coingecko'  => 'CoinGecko',
					'shapeshift' => 'ShapeShift',
					'poloniex'   => 'Poloniex',
					'bittrex'    => 'Bittrex',
				),
				'default'           => 'coingecko',
				'ajax_save'         => false, // Force page load when this changes.
				'validate_callback' => 'redux_validate_exchange_api',
				'select2'           => array( 'allowClear' => false ),
			),
		),
	) );


Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Decimals', 'cryptowoo' ),
		'id'         => 'pricing-decimals',
		'desc'       => __(
			'Select the number of decimals for amounts in each of the currencies. This option also overrides the decimals option of the WooCommerce Currency Switcher plugin.',
			'cryptowoo'
		),
		'icon'       => 'fas fa-calculator ',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'decimals_BTC',
				'type'     => 'select',
				'title'    => 'Bitcoin amount decimals',
				'subtitle' => '',
				'desc'     => '',
				'options'  => array(
					2 => '2',
					4 => '4',
					6 => '6',
					8 => '8',
				),
				'default'  => 4,
				'select2'  => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'decimals_BCH',
				'type'     => 'select',
				'title'    => sprintf(
					__( '%s amount decimals', 'cryptowoo' ),
					'Bitcoin Cash'
				),
				'subtitle' => '',
				'desc'     => __(
					'This option overrides the decimals option of the WooCommerce Currency Switcher plugin.',
					'cryptowoo'
				),
				'options'  => array(
					2 => '2',
					4 => '4',
					6 => '6',
					8 => '8',
				),
				'default'  => 4,
				'select2'  => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'decimals_LTC',
				'type'     => 'select',
				'title'    => 'Litecoin amount decimals',
				'subtitle' => '',
				'desc'     => '',
				'options'  => array(
					2 => '2',
					4 => '4',
					6 => '6',
					8 => '8',
				),
				'default'  => 4,
				'select2'  => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'decimals_DOGE',
				'type'     => 'select',
				'title'    => 'Dogecoin amount decimals',
				'subtitle' => '',
				'desc'     => '',
				'options'  => array(
					0 => '0',
					2 => '2',
					4 => '4',
					6 => '6',
					8 => '8',
				),
				'default'  => 2,
				'select2'  => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'decimals_BLK',
				'type'     => 'select',
				'title'    => 'BlackCoin amount decimals',
				'subtitle' => '',
				'desc'     => '',
				'options'  => array(
					0 => '0',
					2 => '2',
					4 => '4',
					6 => '6',
					8 => '8',
				),
				'default'  => 4,
				'select2'  => array( 'allowClear' => false ),
			),
		),
	) );

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Rate Multiplier (Discount & Surcharge)', 'cryptowoo' ),
		'id'         => 'rates-multiplier',
		'icon'       => 'fa fa-balance-scale',
		'desc'       => __(
			'Use the rate multiplier to give a discount to customers purchasing with digital currencies by setting the multiplier to value smaller than 1.00 or to compensate merchant\'s loss to fees when converting to local currency (by setting a value higher than 1.00).<br>
                            Example: 1.05 - will add extra 5% to the total price of the order in the selected digital currency.',
			'cryptowoo'
		),
		'fields'     => array(
			array(
				'id'       => 'show_discounted_rate',
				'type'     => 'switch',
				'title'    => __( 'Show/Hide Discounted Rate', 'cryptowoo' ),
				'subtitle' => __( 'Display the discount percentage on the checkout page.', 'cryptowoo' ),
				'desc'     => __( 'Show/Hide Does not display surcharges.', 'cryptowoo' ),
				'default'  => true,
			),
			array(
				'id'            => 'multiplier_btc',
				'type'          => 'slider',
				'title'         => sprintf(
					__( '%s exchange rate multiplier', 'cryptowoo' ),
					'Bitcoin'
				),
				'subtitle'      => sprintf(
					__( 'Extra multiplier to apply when calculating %s prices.', 'cryptowoo' ),
					'Bitcoin'
				),
				//'desc'          => __( 'Description', 'cryptowoo' ),
				'default'       => 1,
				'min'           => .001,
				'step'          => .001,
				'max'           => 2,
				'resolution'    => 0.001,
				'validate'      => 'comma_numeric',
				'display_value' => 'text',
			),
			array(
				'id'            => 'multiplier_bch',
				'type'          => 'slider',
				'title'         => sprintf(
					__( '%s exchange rate multiplier', 'cryptowoo' ),
					'Bitcoin Cash'
				),
				'subtitle'      => sprintf(
					__( 'Extra multiplier to apply when calculating %s prices.', 'cryptowoo' ),
					'Bitcoin Cash'
				),
				'desc'          => '',
				'default'       => 1,
				'min'           => .001,
				'step'          => .001,
				'max'           => 2,
				'resolution'    => 0.001,
				'validate'      => 'comma_numeric',
				'display_value' => 'text',
			),
			array(
				'id'            => 'multiplier_doge',
				'type'          => 'slider',
				'title'         => sprintf(
					__( '%s exchange rate multiplier', 'cryptowoo' ),
					'Dogecoin'
				),
				'subtitle'      => sprintf(
					__( 'Extra multiplier to apply when calculating %s prices.', 'cryptowoo' ),
					'Dogecoin'
				),
				//'desc'          => __( 'Description', 'cryptowoo' ),
				'default'       => 1,
				'min'           => .001,
				'step'          => .001,
				'max'           => 2,
				'resolution'    => 0.001,
				'validate'      => 'comma_numeric',
				'display_value' => 'text',
			),
			array(
				'id'            => 'multiplier_ltc',
				'type'          => 'slider',
				'title'         => sprintf(
					__( '%s exchange rate multiplier', 'cryptowoo' ),
					'Litecoin'
				),
				'subtitle'      => sprintf(
					__( 'Extra multiplier to apply when calculating %s prices.', 'cryptowoo' ),
					'Litecoin'
				),
				//'desc'          => __( 'Description', 'cryptowoo' ),
				'default'       => 1,
				'min'           => .001,
				'step'          => .001,
				'max'           => 2,
				'resolution'    => 0.001,
				'validate'      => 'comma_numeric',
				'display_value' => 'text',
			),
			array(
				'id'            => 'multiplier_blk',
				'type'          => 'slider',
				'title'         => sprintf(
					__( '%s exchange rate multiplier', 'cryptowoo' ),
					'Blackcoin'
				),
				'subtitle'      => sprintf(
					__( 'Extra multiplier to apply when calculating %s prices.', 'cryptowoo' ),
					'BlackCoin'
				),
				'desc'          => '',
				'default'       => 1,
				'min'           => .001,
				'step'          => .001,
				'max'           => 2,
				'resolution'    => 0.001,
				'validate'      => 'comma_numeric',
				'display_value' => 'text',
			),
			array(
				'id'       => 'discount_notice',
				'type'     => null !== ( get_discount_info() ) ? 'editor' : 'info',
				// @todo uncomment when custom discount notice is supported
				'title'    => __( 'Discount Notice', 'cryptowoo' ),
				'subtitle' => __(
					'Use this field to notify your customers about an eventual discount when paying with digital currencies.',
					'cryptowoo'
				),
				'default'  => null !== ( get_discount_info() ) ? print_r(
					get_discount_info(),
					true
				) : __(
					'Coming soon: Use this field to notify your customers about an eventual discount when paying with digital currencies.',
					'cryptowoo'
				),
				'args'     => array(
					'teeny'         => true,
					'textarea_rows' => 10,
				),
			),

		),
		'subsection' => true,
	)
);

Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'WooCommerce Currency Switcher Plugin', 'cryptowoo' ),
		'id'         => 'woocs-currency-switcher',
		'desc'       => '',
		'icon'       => 'fas fa-exchange-alt ',
		'subsection' => true,
		'fields'     => array(

			array(
				'id'     => 'currency_switcher_info',
				'type'   => 'info',
				'notice' => false,
				//'style' => 'warning',
				'icon'   => 'fa fa-info',
				'title'  => __( 'Price Rewriting with "WooCommerce Currency Switcher" plugin', 'cryptowoo' ),
				'desc'   => __(
					'<p>Just install and enable the plugin and enable the option below to add the rates to the switcher plugin dropdown. Requires <a href="https://wordpress.org/plugins/woocommerce-currency-switcher/" title="WooCommerce Currency Switcher" target="_blank">WooCommerce Currency Switcher</a></p>',
					'cryptowoo'
				),
			),
			array(
				'id'       => 'add_currencies_to_woocs',
				'type'     => 'switch',
				'title'    => __( 'Add CryptoWoo Rates to WooCommerce Currency Switcher', 'cryptowoo' ),
				'subtitle' => __(
					'CryptoWoo uses the rates of the "Preferred Exchange" provider to calculate the order amount in cryptocurrency. Enable this option to add the CryptoWoo rates to the WooCommerce Currency Switcher plugin.',
					'cryptowoo'
				),
				'desc'     => sprintf(
					__(
						'%sPlease Note:%s If you disable this you have to add the currencies to the WooCommerce Currency Switcher manually and rely on it to update the exchange rate it is using.',
						'cryptowoo'
					),
					'<br><strong>',
					'</strong>'
				),
				'default'  => false,
			),
			array(
				'id'       => 'woocs_force_checkout_in_store_currency',
				'type'     => 'switch',
				'title'    => __( 'Force checkout in fiat currency', 'cryptowoo' ),
				'subtitle' => __(
					'When the customer places the order, the currency that is selected in WooCommerce Currency Switcher is used as the WooCommerce order currency. Enable this to force the order currency to be in the default WooCommerce store currency, if the selected currency is a cryptocurrency.',
					'cryptowoo'
				),
				'desc'     => __(
					'Enable to force the checkout to be in the WooCommerce store currency if the selected currency in the switcher is a cryptocurrency.',
					'cryptowoo'
				),
				'default'  => true,// true = on | false = off
				'required' => array( 'add_currencies_to_woocs', '=', true ),
			),
			array(
				'id'       => 'woocs_auto_default_payment_currency',
				'type'     => 'switch',
				'title'    => __( 'Default payment currency is selected currency', 'cryptowoo' ),
				'subtitle' => __(
					'Normally the customer has to select the payment currency in the checkout. The customer may have selected a cryptocurrency in the currency switcher. In that case the pre-selected payment currency will be the cryptocurrency that is selected in the currency switcher.',
					'cryptowoo'
				),
				'desc'     => __(
					'Enable to pre-select the CryptoWoo payment currency if the selected currency in the switcher is a cryptocurrency.',
					'cryptowoo'
				),
				'default'  => true,// true = on | false = off
				'required' => array( 'add_currencies_to_woocs', '=', true ),
			),
			array(
				'id'       => 'switcher_bg_color',
				'type'     => 'color_rgba',
				'title'    => 'Currency Switcher Flag Background Color',
				'subtitle' => 'Set the background color of the currency flag that is displayed in the WooCommerce Currency Switcher',
				'desc'     => 'You can ignore this option if you don\'t use the currency switcher plugin.',

				// See Notes below about these lines.
				'output'   => array( 'background-color' => 'img.dd-image-right' ),
				//'compiler'  => array('color' => '.site-header, .site-footer', 'background-color' => '.nav-bar'),
				'default'  => array(
					'color' => '#fdfdfd',
					'alpha' => 1,
				),

				'required' => array( 'add_currencies_to_woocs', '=', true ),

				// These options display a fully functional color palette.  Omit this argument
				// for the minimal color picker, and change as desired.
				'options'  => array(
					'show_input'             => true,
					'show_initial'           => true,
					'show_alpha'             => true,
					'show_palette'           => true,
					'show_palette_only'      => false,
					'show_selection_palette' => true,
					'max_palette_size'       => 10,
					'allow_empty'            => true,
					'clickout_fires_change'  => false,
					'choose_text'            => 'Choose',
					'cancel_text'            => 'Cancel',
					'show_buttons'           => true,
					'use_extended_classes'   => true,
					'palette'                => null,  // show default
					'input_text'             => 'Select Color',
				),
			),
		),
	)
);

// -> START Display Settings
Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Display Settings', 'cryptowoo' ),
		'id'         => 'rewriting',
		'desc'       => '',
		'subsection' => false,
		'icon'       => 'fa fa-paint-brush',
		'fields'     => array(
			array(
				'id'       => 'display_checkout_branding',
				'type'     => 'switch',
				'title'    => __( 'Show/Hide Powered By CryptoWoo', 'cryptowoo' ),
				'subtitle' => __(
					'Adds Powered By CryptoWoo to the checkout page and payment page.', 'cryptowoo' ),
				'desc'     => __( 'Show/Hide', 'cryptowoo' ),
				'default'  => false,
			),
			array(
				'id'       => 'cryptocurrency_icon_color',
				'type'     => 'select',
				'title'    => __( 'Cryptocurrency Icon Color', 'cryptowoo' ),
				'subtitle' => __( 'Select a different color for the cryptocurrency icons.', 'cryptowoo' ),
				'desc'     => '',
				'options'  => array(
					'color' => __( 'Colored', 'cryptowoo' ),
					'black' => __( 'Black', 'cryptowoo' ),
					'white' => __( 'White', 'cryptowoo' ),
				),
				'default'  => 'color',
				'select2'  => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'cryptowoo_currency_table_on_single_products',
				'type'     => 'switch',
				'title'    => __( 'Show/Hide Price Table', 'cryptowoo' ),
				'subtitle' => __(
					'Add a digital currency price estimate above the product short description on single product pages.',
					'cryptowoo'
				),
				'desc'     => __( 'Show/Hide', 'cryptowoo' ),
				'default'  => false,
			),
			array(
				'id'       => 'preferred_block_explorer_btc',
				'type'     => 'select',
				'title'    => sprintf(
					__( '%s Block Explorer', 'cryptowoo' ),
					'Bitcoin'
				),
				'subtitle' => sprintf(
					__( 'Choose the block explorer you want to use for links to the %s blockchain.', 'cryptowoo' ),
					'Bitcoin'
				),
				'desc'     => '',
				'options'  => array(
					'autoselect'                => __( 'Autoselect by processing API', 'cryptowoo' ),
					'blockcypher'               => 'blockcypher.com',
					'blocktrail'                => 'blocktrail.com',
					'blockr_io'                 => 'blockr.io',
					'sochain'                   => 'SoChain',
					'smartbit'                  => 'smartbit.com.au',
					'blockstream_info'          => 'blockstream.info',
					'blockstream_info_onion_v2' => 'blockstream.info Onion v2 (explorernuoc63nb.onion)',
					'blockstream_info_onion_v3' => 'blockstream.info Onion v3 (explorerzydxu5ecjrkwceayqybizmpjjznk5izmitf2modhcusuqlid.onion)',
					'blockchair'                => 'blockchair.com',
					'custom'                    => __( 'Custom (Please enter URL below)', 'cryptowoo' ),
				),
				'default'  => 'autoselect',
				'select2'  => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'preferred_block_explorer_btc_info',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'required' => array(
					array( 'preferred_block_explorer_btc', '=', 'custom' ),
					array( 'custom_block_explorer_btc', '=', '' ),
				),
				'desc'     => sprintf(
					__( 'Please enter a valid URL in the field below to use a custom %s block explorer', 'cryptowoo' ),
					'bitcoin'
				),
			),
			array(
				'id'                => 'custom_block_explorer_btc',
				'type'              => 'text',
				'title'             => sprintf(
					__( 'Custom %s Block Explorer URL', 'cryptowoo' ),
					'Bitcoin'
				),
				'subtitle'          => __( 'Link to a block explorer of your choice.', 'cryptowoo' ),
				'desc'              => sprintf(
					__(
						'The URL to the page that displays the information for a single address.%sPlease add %s{{ADDRESS}}%s as placeholder for the cryptocurrency address in the URL.%s',
						'cryptowoo'
					),
					'<br><strong>',
					'<code>',
					'</code>',
					'</strong>'
				),
				'placeholder'       => 'http://live.blockcypher.com/btc/address/{{ADDRESS}}/',
				'required'          => array( 'preferred_block_explorer_btc', '=', 'custom' ),
				'validate_callback' => 'redux_validate_custom_blockexplorer',
				'ajax_save'         => false,
				'msg'               => __( 'Invalid custom block explorer URL', 'cryptowoo' ),
				'default'           => '',
			),
			array(
				'id'       => 'preferred_block_explorer_bch',
				'type'     => 'select',
				'title'    => sprintf(
					__( '%s Block Explorer', 'cryptowoo' ),
					'Bitcoin Cash'
				),
				'subtitle' => sprintf(
					__( 'Choose the block explorer you want to use for links to the %s blockchain.', 'cryptowoo' ),
					'Bitcoin Cash'
				),
				'desc'     => '',
				'options'  => array(
					'autoselect' => __( 'Autoselect by processing API', 'cryptowoo' ),
					'blockchair' => 'blockchair.com',
					'custom'     => __( 'Custom (enter URL below)' ),
				),
				'default'  => 'autoselect',
				'select2'  => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'preferred_block_explorer_bch_info',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'required' => array(
					array( 'preferred_block_explorer_bch', '=', 'custom' ),
					array( 'custom_block_explorer_bch', '=', '' ),
				),
				'desc'     => sprintf(
					__( 'Please enter a valid URL in the field below to use a custom %s block explorer', 'cryptowoo' ),
					'Bitcoin Cash'
				),
			),
			array(
				'id'                => 'custom_block_explorer_bch',
				'type'              => 'text',
				'title'             => sprintf(
					__( 'Custom %s Block Explorer URL', 'cryptowoo' ),
					'Bitcoin Cash'
				),
				'subtitle'          => __( 'Link to a block explorer of your choice.', 'cryptowoo' ),
				'desc'              => sprintf(
					__(
						'The URL to the page that displays the information for a single address.%sPlease add %s{{ADDRESS}}%s as placeholder for the cryptocurrency address in the URL.%s',
						'cryptowoo'
					),
					'<br><strong>',
					'<code>',
					'</code>',
					'</strong>'
				),
				'placeholder'       => 'https://explorer.api.bitcoin.com/bch/v1/txs?address={$address}',
				'required'          => array( 'preferred_block_explorer_bch', '=', 'custom' ),
				'validate_callback' => 'redux_validate_custom_blockexplorer',
				'ajax_save'         => false,
				'msg'               => __( 'Invalid custom block explorer URL', 'cryptowoo' ),
				'default'           => '',
			),
			array(
				'id'       => 'preferred_block_explorer_ltc',
				'type'     => 'select',
				'title'    => sprintf(
					__( '%s Block Explorer', 'cryptowoo' ),
					'Litecoin'
				),
				'subtitle' => sprintf(
					__( 'Choose the block explorer you want to use for links to the %s blockchain.', 'cryptowoo' ),
					'Litecoin'
				),
				'desc'     => '',
				'options'  => array(
					'autoselect'  => __(
						'Autoselect by processing API',
						'cryptowoo'
					),
					'blockcypher' => 'blockcypher.com',
					'blockr_io'   => 'blockr.io',
					'sochain'     => 'SoChain',
					'blockchair'  => 'blockchair.com',
					'custom'      => __( 'Custom (Please enter URL below)', 'cryptowoo' ),
				),
				'default'  => 'autoselect',
				'select2'  => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'preferred_block_explorer_ltc_info',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'required' => array(
					array( 'preferred_block_explorer_ltc', '=', 'custom' ),
					array( 'custom_block_explorer_ltc', '=', '' ),
				),
				'desc'     => sprintf(
					__( 'Please enter a valid URL in the field below to use a custom %s block explorer', 'cryptowoo' ),
					'litecoin'
				),
			),
			array(
				'id'                => 'custom_block_explorer_ltc',
				'type'              => 'text',
				'title'             => sprintf(
					__( 'Custom %s Block Explorer URL', 'cryptowoo' ),
					'Litecoin'
				),
				'subtitle'          => __( 'Link to a block explorer of your choice.', 'cryptowoo' ),
				'desc'              => sprintf(
					__(
						'The URL to the page that displays the information for a single address.%sPlease add %s{{ADDRESS}}%s as placeholder for the cryptocurrency address in the URL.%s',
						'cryptowoo'
					),
					'<br><strong>',
					'<code>',
					'</code>',
					'</strong>'
				),
				'placeholder'       => 'http://live.blockcypher.com/ltc/address/{{ADDRESS}}/',
				'required'          => array( 'preferred_block_explorer_ltc', '=', 'custom' ),
				'validate_callback' => 'redux_validate_custom_blockexplorer',
				'ajax_save'         => false,
				'msg'               => __( 'Invalid custom block explorer URL', 'cryptowoo' ),
				'default'           => '',
			),
			array(
				'id'       => 'preferred_block_explorer_doge',
				'type'     => 'select',
				'title'    => sprintf(
					__( '%s Block Explorer', 'cryptowoo' ),
					'Dogecoin'
				),
				'subtitle' => sprintf(
					__( 'Choose the block explorer you want to use for links to the %s blockchain.', 'cryptowoo' ),
					'Dogecoin'
				),
				'desc'     => '',
				'options'  => array(
					'autoselect'  => __( 'Autoselect by processing API', 'cryptowoo' ),
					'blockcypher' => 'blockcypher.com',
					'blockr_io'   => 'blockr.io',
					'sochain'     => 'SoChain',
					'blockchair'  => 'blockchair.com',
					'custom'      => __( 'Custom (Please enter URL below)', 'cryptowoo' ),
				),
				'default'  => 'autoselect',
				'select2'  => array( 'allowClear' => false ),
			),
			array(
				'id'       => 'preferred_block_explorer_doge_info',
				'type'     => 'info',
				'style'    => 'critical',
				'icon'     => 'el el-warning-sign',
				'required' => array(
					array( 'preferred_block_explorer_doge', '=', 'custom' ),
					array( 'custom_block_explorer_doge', '=', '' ),
				),
				'desc'     => sprintf(
					__( 'Please enter a valid URL in the field below to use a custom %s block explorer', 'cryptowoo' ),
					'dogecoin'
				),
			),
			array(
				'id'                => 'custom_block_explorer_doge',
				'type'              => 'text',
				'title'             => sprintf(
					__( 'Custom %s Block Explorer URL', 'cryptowoo' ),
					'Dogecoin'
				),
				'subtitle'          => __( 'Link to a block explorer of your choice.', 'cryptowoo' ),
				'desc'              => sprintf(
					__(
						'The URL to the page that displays the information for a single address.%sPlease add %s{{ADDRESS}}%s as placeholder for the cryptocurrency address in the URL.%s',
						'cryptowoo'
					),
					'<br><strong>',
					'<code>',
					'</code>',
					'</strong>'
				),
				'placeholder'       => 'http://live.blockcypher.com/ltc/address/{{ADDRESS}}/',
				'required'          => array( 'preferred_block_explorer_doge', '=', 'custom' ),
				'validate_callback' => 'redux_validate_custom_blockexplorer',
				'ajax_save'         => false,
				'msg'               => __( 'Invalid custom block explorer URL', 'cryptowoo' ),
				'default'           => '',
			),
		),
	) );


Redux::set_section(
	$opt_name,
	array(
		'title'      => __( 'Debugging', 'cryptowoo' ),
		'desc'       => '',
		'id'         => 'debug',
		'subsection' => false,
		'icon'       => 'el el-warning-sign',
		'fields'     => array(
			array(
				'id'       => 'debug_log_disabled_info',
				'type'     => 'info',
				'style'    => 'info',
				'icon'     => '',
				'required' => array(
					array( 'enable_debug_log', '=', false ),
				),
				'desc'     => __( 'Debug logging is disabled', 'cryptowoo' ),
			),
			array(
				'id'       => 'debug_log_enabled_info',
				'type'     => 'info',
				'style'    => 'warn',
				'icon'     => 'el el-warning-sign',
				'required' => array(
					array( 'enable_debug_log', '=', true ),
				),
				'desc'     => __( 'Debug logging is enabled. Make sure to disable it after you are done.', 'cryptowoo' ),
			),
			array(
				'id'       => 'enable_debug_log',
				'type'     => 'switch',
				'title'    => __( 'Enable Debug Logging', 'cryptowoo' ),
				'subtitle' => sprintf(
					__(
						'Add event log files to the WooCommerce "System Status" log file directory. %s%s%s',
						'cryptowoo'
					),
					'<pre>',
					CW_LOG_DIR,
					'</pre>'
				),
				'desc'     => sprintf(
					__(
						'<a href="%s" target="_blank" title="Open WooCommerce log viewer in new tab">Click here to view the logs</a>',
						'cryptowoo'
					),
					$woocommerce_logs_url
				),
				'default'  => false,
			),
			array(
				'id'       => 'logging',
				'type'     => 'select',
				'title'    => __(
					'Log Verbosity', 'cryptowoo' ),
				'subtitle' => __( 'Select which message severity and up to log.', 'cryptowoo' ),
				'desc'     => 'All information of the selected severity and worse is logged',

				//Must provide key => value pairs for select options
				'options'  => array(
					'emergency' => __( 'Emergency: System is unusable.', 'cryptowoo' ),
					'alert'     => __( 'Alert: Action must be taken immediately.', 'cryptowoo' ),
					'critical'  => __( 'Critical: Critical conditions.', 'cryptowoo' ),
					'error'     => __( 'Error: Error conditions.', 'cryptowoo' ),
					'warning'   => __( 'Warning: Warning conditions.', 'cryptowoo' ),
					'notice'    => __( 'Notice: Normal but significant condition.', 'cryptowoo' ),
					'info'      => __( 'Info: Informational messages.', 'cryptowoo' ),
					'debug'     => __( 'Debug: Debug-level messages.', 'cryptowoo' ),
				),

				// Set default status.
				'default'  => 'error',
			),
			array(
				'id'       => 'display_rate_error_notice',
				'type'     => 'switch',
				'title'    => __( 'Rate Error Counter', 'cryptowoo' ),
				'subtitle' => __(
					'Display a admin notice with the exchange rate error count since the last counter reset.',
					'cryptowoo'
				),
				'desc'     => __(
					'The error counter is independent from the detailed error data visualization below.',
					'cryptowoo'
				),
				'default'  => false,
			),
			array(
				'id'       => 'cryptowoo_exchange_rate_warning',
				'type'     => 'switch',
				'title'    => __( 'Disable exchange rate error warning', 'cryptowoo' ),
				'subtitle' => __(
					'Exchange rate error notifications are not sent when failing to get exchange rates.',
					'cryptowoo'
				),
				'desc'     => __(
					'When enabling this setting be aware that no exchange rate error notifications will be sent.',
					'cryptowoo'
				),
				'default'  => false,
			),
			array(
				'id'       => 'rate_error_charts',
				'type'     => 'switch',
				'title'    => __( 'Visualize Exchange Rate Errors', 'cryptowoo' ),
				'desc'     => sprintf(
					__(
						'%sPlease note:%s This feature uses the Google Charts libraries.%shttps://developers.google.com/chart/%s',
						'cryptowoo'
					),
					'<strong>',
					'</strong>',
					'<pre>',
					'</pre>'
				),
				'subtitle' => sprintf(
					__(
						'Collect additional details about the exchange rate errors and display them on the %s%s%sdatabase actions%s page.',
						'cryptowoo'
					),
					'<a href="',
					$db_admin_page,
					'">',
					'</a>'
				),
				'default'  => false,
			),
			array(
				'id'       => 'keep_tables',
				'type'     => 'switch',
				'title'    => __( 'Keep Tables', 'cryptowoo' ),
				'desc'     => __(
					'Removing the tables will not have an influence on the payment details for previous orders.',
					'cryptowoo'
				),
				'subtitle' => __( 'Keep CryptoWoo <strong>tables</strong> when uninstalling the plugin.', 'cryptowoo' ),
				'default'  => false,
			),
			array(
				'id'       => 'keep_options',
				'type'     => 'switch',
				'title'    => __( 'Keep Settings', 'cryptowoo' ),
				'subtitle' => __( 'Keep CryptoWoo <strong>settings</strong> when uninstalling the plugin.', 'cryptowoo' ),
				//'subtitle'     => __( 'This is the description field, again good for additional info.', 'cryptowoo' ),
				'default'  => false,
			),
		),
	) );

/*
 * <--- END SECTIONS
 */
