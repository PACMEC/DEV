<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Process exchange rates
 *
 * Based on class.exchange-rates by author DRDoGE
 * Author: CryptoWoo AS
 *
 * Some functions modified from "Bitcoin Payments for WooCommerce"
 * Author: BitcoinWay
 * URI: http://www.bitcoinway.com/
 *
 * @category   CryptoWoo
 * @package    CryptoWoo
 * @subpackage PriceProcessing
 * @author     Developer: CryptoWoo AS
 */
class CW_ExchangeRate_Processing {

	/**
	 *
	 * Exchange Rate Tools class
	 *
	 * @var CW_ExchangeRate_Tools $tools
	 */
	private $tools;

	/**
	 *
	 * Constructor for CW_ExchangeRate_Processing
	 *
	 * CW_ExchangeRate_Processing constructor.
	 *
	 * @param CW_ExchangeRate_Tools $tools Exchange Rate Tools class instance.
	 */
	public function __construct( $tools = null ) {
		if ( ! $tools ) {
			$tools = CW_ExchangeRates::tools();
		}
		$this->tools = $tools;
	}

	/**
	 *
	 * Is triggered when invoking inaccessible methods.
	 *
	 * @param string $name      the function name.
	 * @param array  $arguments the arguments.
	 *
	 * @return mixed
	 * @link   https://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
	 *
	 * TODO: This is for backward compatibility, remove this when no more usage!
	 */
	public function __call( $name, $arguments ) {
		$foo = CW_ExchangeRates::tools();
		return call_user_func_array( array( $foo, $name ), $arguments );
	}

	/**
	 * Update an altcoin fiat exchange rate.
	 *
	 * @since      0.71
	 * @deprecated 0.22.2 Use update_coin_rates()
	 * @see        update_coin_rates()
	 *
	 * @param string $coin    The coin name.
	 * @param array  $options The CryptoWoo options.
	 * @param bool   $force   If should be force updated.
	 *
	 * @return array|string|null
	 */
	public function update_altcoin_fiat_rates( $coin, $options = false, $force = false ) {
		_deprecated_function( __FUNCTION__, '0.22.2', 'update_coin_rates' );
		return $this->update_coin_rates( $coin, $options, $force );
	}

	/**
	 * Update an altcoin fiat exchange rate.
	 *
	 * @since      0.71
	 * @deprecated 0.22.2 Use update_coin_rates()
	 * @see        update_coin_rates()
	 *
	 * @param string $coin    The coin name.
	 * @param array  $options The CryptoWoo options.
	 * @param bool   $force   If should be force updated.
	 *
	 * @return array|string|null
	 */
	public function update_coin_fiat_rates( $coin, $options = false, $force = false ) {
		_deprecated_function( __FUNCTION__, '0.22.2', 'update_coin_rates' );
		return $this->update_coin_rates( $coin, $options, $force );
	}


	/**
	 * Get the Coin/Fiat exchange_rate.
	 * Backwards compatibility for CryptoWoo addons.
	 *
	 * @package CW_ExchangeRates
	 *
	 * @param string $coin    The currency.
	 * @param bool   $options deprecated TODO: remove when no more usage
	 * @param bool   $force   Force update to prevent use of cached prices.
	 *
	 * @return array|null|string
	 */
	public function update_coin_rates( $coin, $options = false, $force = false ) {
		global $wpdb;

		$timeago = -1;
		$query   = $this->get_exchange_rate_data( $coin );
		if ( $query ) {
			$last_update = $query[0]['last_update'];
			$timeago     = time() - strtotime( $last_update );
		}

		// We must always have updated price for BTC for altcoins as they are dependent of it.
		// That options is enabled means that at least one currency is enabled.
		if ( 'BTC' === $coin && cw_get_option( 'enabled' ) ) {
			- 1 !== $timeago && 60 > $timeago ?: $force = true;
		} elseif ( ! cw_get_option( 'enabled' ) || ! $this->tools->coin_is_enabled( $coin ) ) {
			// Skip if the currency is disabled.
			$status = "skipped: {$coin} is disabled";

			$wpdb->query(
				"UPDATE {$wpdb->prefix}cryptowoo_exchange_rates
											   SET 	status = '{$status}'
											 WHERE coin_type = '{$coin}';"
			);

			return array(
				'status'      => $status,
				'last_update' => "{$timeago}s ago",
				'time'        => date( 'Y-m-d H:i:s' ),
			);
		}

		// only update price if data is older than 1 minute.
		if ( $timeago >= 59 || $force || - 1 === $timeago ) {
			return $this->do_update_coin_rates( $coin );
		}

		$next_update = 60 - $timeago;
		return array(
			'status'      => 'not updated',
			'last_update' => "{$timeago}s ago - next update in {$next_update}s",
			'time'        => date( 'Y-m-d H:i:s' ),
		);
	}

	/**
	 * Get the Coin/Fiat exchange_rate.
	 * Backwards compatibility for CryptoWoo addons.
	 *
	 * @package CW_ExchangeRates
	 *
	 * @param string $coin The currency.
	 *
	 * @return array|null|string
	 */
	private function do_update_coin_rates( string $coin ) {
		$preferred_exchange = $this->tools->get_preferred_exchange( $coin );
		$status             = null;
		$method             = $preferred_exchange;

		do {
			// Calculate cryptocurrency prices.
			$class = $this->tools->get_exchange_instance_by_id( $method, $coin );
			if ( $class ) {
				$prices = $class->get_coin_price();
			} else {
				// Added for backwards compatibility for addons with custom exchange API.
				// TODO: Remove this when no more usage in addons!
				$lc_coin = strtolower( $coin );
				$prices  = apply_filters( "cw_{$lc_coin}_fiat_rates", ! empty( $method ) ? array( $method => array( 'status' => false ) ) : array(), cw_get_options() );
			}

			$method = isset( $prices[ $method ] ) ? $method : 'none';
			$status = isset( $prices[ $method ]['status'] ) ? $prices[ $method ]['status'] : false;

			if ( false !== strpos( $status, 'success' ) ) {
				break;
			} else {
				if ( ! isset( $fallback_exchanges ) ) {
					$fallback_exchanges = $this->tools->get_preferred_exchanges_names( $coin );
					$fallback_exchanges = array_combine( $fallback_exchanges, $fallback_exchanges );
					unset( $fallback_exchanges[ $method ] );
				}

				if ( empty( $fallback_exchanges ) ) {
					break;
				}

				$method = array_shift( $fallback_exchanges );
			}
		} while ( false === strpos( $status, 'success' ) );

		$coin_fiat = isset( $prices[ $method ][ $coin ]['price'] ) ? $prices[ $method ][ $coin ]['price'] : 0;

		if ( false === strpos( $status, 'success' ) ) {
			// Inform admin about failure.
			$this->maybe_warn_admin( $method, false, $prices, gmdate( 'Y-m-d H:i:s' ), $coin, $coin_fiat );
		}

		// Added for compatibility for custom exchange updates in old add-ons.
		// TODO: Remove this when all addons return correct format.
		if ( 0 !== $coin_fiat && 'BTC' !== $coin ) {
			$btc_fiat = $this->get_exchange_rate( 'BTC' );
			$btc_rate = $coin_fiat / $btc_fiat;

			$prices[ $method ][ $coin . 'BTC' ]          = $prices[ $method ][ $coin ];
			$prices[ $method ][ $coin . 'BTC' ]['price'] = $btc_rate;

			unset( $prices[ $method ][ $coin ] );
			$coin_fiat = 0;
		}

		$time = isset( $prices[ $method ][ $coin ]['timestamp'] ) ? gmdate( 'Y-m-d H:i:s', $prices[ $method ][ $coin ]['timestamp'] ) : gmdate( 'Y-m-d H:i:s' );

		// Maybe save rate.
		$maybe_save = $this->maybe_save_rate( $coin, $coin_fiat, $preferred_exchange, $status, $method, $time, $prices );

		if ( is_array( $maybe_save ) ) {
			return $maybe_save;
		} else {
			$preferred_exchange ?: $preferred_exchange = 'No preferred exchange selected and failed to get fallback';
			$prices['errors'][]                        = sprintf( '%s|%s|%s', $preferred_exchange, $coin, wp_json_encode( $prices ) );
		}

		return $prices;
	}

	/**
	 *
	 * Maybe save the new exchange rate
	 *
	 * @param string     $coin               coin name.
	 * @param string     $coin_fiat          fiat name.
	 * @param string     $preferred_exchange preferred exchange id.
	 * @param string     $status             status of the exchange rate.
	 * @param string     $method             the method that was used to get exchange rate.
	 * @param int|string $time               the timestamp of the exchange rate.
	 * @param array      $prices             array of prices (exchange rate data result).
	 *
	 * @return array|bool
	 */
	public function maybe_save_rate( $coin, $coin_fiat, $preferred_exchange, $status, $method, $time, $prices ) {

		if ( is_numeric( $coin_fiat ) && $coin_fiat > 0 ) {

			$wc_currency = isset( $prices['fiat_currency'] ) ? $prices['fiat_currency'] : cw_get_woocommerce_default_currency();
			if ( $wc_currency === $coin ) {
				$result = true;
			} else {
				$result = $this->save_rate( $coin . $wc_currency, $coin_fiat, $preferred_exchange, $status, $method, $time );
			}

			$update_success = $result ? true : false;

			return array(
				'exchange' => $preferred_exchange,
				'price'    => $coin_fiat,
				'status'   => $status,
				'method'   => $method,
				'time'     => $time,
			);
		} elseif ( is_array( $prices ) && is_array( current( $prices ) ) ) {

			foreach ( $prices as $exchange => &$price_data ) {
				if ( isset( $price_data['status'] ) ) {
					unset( $price_data['status'] );
				}

				foreach ( $price_data as $coin_fiat_pair => $currency_price_data ) {
					if ( 0 !== strpos( $coin_fiat_pair, $coin ) ) {
						continue;
					}

					if ( $coin_fiat_pair === $coin . $coin ) {
						$currency_price_data['fiat_currency'] = $coin;
					} else {
						$currency_price_data['fiat_currency'] = ( explode( $coin, $coin_fiat_pair ) )[1];
					}

					$fiat_prices[ $coin_fiat_pair ] = $this->maybe_save_rate( $coin, $currency_price_data['price'], $preferred_exchange, $status, $method, $time, $currency_price_data );
				}
			}
		}

		if ( ! empty( $fiat_prices ) ) {
			return array(
				'exchange' => $preferred_exchange,
				'prices'   => $fiat_prices,
				'status'   => $status,
				'method'   => $method,
				'time'     => $time,
			);
		}

		return false;
	}

	/**
	 *
	 * Helper function for remote requests
	 *
	 * @param string $url        the url to call.
	 * @param bool   $json       the json string to add to request.
	 * @param bool   $user_agent the user agent to use.
	 * @param int    $timeout    the timeout for request.
	 * @param string $proxy      the proxy f.ex 'localhost:9050'.
	 *
	 * @return bool|mixed
	 */
	public function request( $url, $json = true, $user_agent = false, $timeout = 10, $proxy = '' ) {
		$args = array();

		if ( ! empty( $timeout ) ) {
			$args['timeout'] = $timeout;
		}

		if ( ! empty( $user_agent ) ) {
			$args['user-agent'] = $user_agent;
		}

		if ( ! empty( $proxy ) ) {
			define( 'WP_PROXY_HOST', $proxy );
		}

		// Execute the request.
		$result = wp_safe_remote_get( $url, $args );
		if ( $result instanceof WP_Error ) {
			$error = $result->get_error_code();
		} elseif ( ! is_array( $result ) || ! isset( $result['body'] ) || ! isset( $result['response'] ) ) {
			$error = isset( $result['response']['code'] ) ? $result['response']['code'] : -1;
		} else {
			$error = false;
		}

		$header = isset( $result['headers'] ) ? $result['headers'] : null;

		if ( $error || 200 !== $result['response']['code'] ) {
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, gmdate( 'Y-m-d H:i:s' ) . 'curl header: ' . var_export( $header, true ) . ' response: ' . var_export( $result, true ) . var_export( $error, true ), 'error' ); // phpcs:disable WordPress.PHP.DevelopmentFunctions
			return $result;
		}

		// Return the decoded json or response body.
		if ( ! $json ) {
			return json_decode( $result['body'] );
		} else {
			return $result['body'];
		}
	}

	/**
	 *
	 * Calculate coin price by base currency rate and fiat or coin currency rate
	 *
	 * @param string $currency             Destination currency code.
	 * @param string $base_currency        Base currency code.
	 * @param string $woo_default_currency Woocommerce Default currency code.
	 *
	 * @return float
	 */
	public function calculate_coin_pair_rate( $currency, $base_currency, $woo_default_currency ) {
		if ( ! $woo_default_currency ) {
			$woo_default_currency = cw_get_woocommerce_default_currency();
		}
		$base_currency_is_crypto = $this->tools->currency_is_crypto( $base_currency );
		$currency_is_crypto      = $this->tools->currency_is_crypto( $currency );

		if ( 'BTC' === $currency && ! $base_currency_is_crypto ) {
			$inter_rate = $base_rate = $this->get_exchange_rate( 'BTC', false, $woo_default_currency );
			$fiat_rates = cw_get_fiat_currencies();
			$coin_rate  = $inter_rate && isset( $fiat_rates[ $base_currency ] ) ? $inter_rate * $fiat_rates[ $base_currency ]['rate'] : 0;
		} elseif ( $currency_is_crypto && ! $base_currency_is_crypto ) {
			$destination_rate = $this->get_exchange_rate( $currency, false, 'BTC' );
			$base_rate        = $this->get_exchange_rate( 'BTC', false, $base_currency );
			$coin_rate        = $base_rate * $destination_rate;
		} elseif ( $currency_is_crypto && $base_currency_is_crypto ) {
			$destination_rate = $this->get_exchange_rate( $currency, false, 'BTC' );
			$base_rate        = $this->get_exchange_rate( $base_currency, false, 'BTC' );
			$coin_rate        = $destination_rate / $base_rate;
		} else {
			$coin_rate = 0;
		}

		return $this->format_price( $coin_rate );
	}

	/**
	 *
	 * Format the price to default settings.
	 *
	 * @param string|float|int $price The price.
	 *
	 * @return float
	 */
	public function format_price( $price ) {
		return number_format( floatval( $price ), 8, '.', '' );
	}

	/**
	 * Return the fiat exchange rate for $currency cross-calculate Altcoin/Fiat values via BTC/USD
	 *
	 * @param string|false $currency
	 * @param bool         $force
	 * @param string|false $fiat_currency
	 *
	 * @return array
	 */
	private function get_exchange_rate_data( $currency = false, $force = false, $fiat_currency = false ) : array {
		global $wpdb;
		if ( ! $fiat_currency && $currency ) {
			$fiat_currency = cw_get_woocommerce_default_currency();
		}

		if ( $force ) {
			$this->do_update_coin_rates( $currency );
		}

		$query = "SELECT * FROM {$wpdb->prefix}cryptowoo_exchange_rates";
		if ( $currency ) {
			$query = $wpdb->prepare( "$query WHERE coin_type = %s", $currency . $fiat_currency );
		}

		return $wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * Return the fiat exchange rate for a currency from database
	 *
	 * @param string       $currency
	 * @param bool         $force
	 * @param string|false $base_currency
	 *
	 * @return float
	 */
	function get_exchange_rate_from_db( string $currency, $force = false, $base_currency = false ) : float {
		// Same currency-base pair is always 1.
		if ( $currency === $base_currency ) {
			return 1;
		}

		$currency_is_fiat      = $this->tools->currency_is_fiat( $currency );
		$base_currency_is_fiat = $this->tools->currency_is_fiat( $base_currency );

		// fiat-fiat rates are not stored in database.
		if ( $currency_is_fiat && $base_currency_is_fiat ) {
			return 0;
		}

		// Crypto is before fiat in crypto-fiat pair.
		// BTC is after altcoin in altcoin-btc pair
		if ( ( $currency_is_fiat && ! $base_currency_is_fiat ) || ( 'BTC' === $currency && ! $base_currency_is_fiat ) ) {
			$exchange_rate = $this->get_exchange_rate_from_db( $base_currency, $force, $currency );

			return 0 != $exchange_rate ? 1 / $exchange_rate : 0;
		}

		$exchange_rate_data = $this->get_exchange_rate_data( $currency, $force, $base_currency );
		$rates              = $exchange_rate_data ? $exchange_rate_data[0] : array();

		if ( isset( $rates['exchange_rate'] ) && is_numeric( $rates['exchange_rate'] ) ) {
			wp_cache_set( $currency . $base_currency, $rates['exchange_rate'], 'cryptowoo-rates', 60 );
			return (float) $rates['exchange_rate'];
		}

		return (float) 0;
	}

	/**
	 * Return the fiat exchange rate for $currency cross-calculate Altcoin/Base values via BTC/BASE
	 *
	 * @param string       $currency
	 * @param bool         $force
	 * @param string|false $base_currency
	 *
	 * @return float
	 */
	function get_exchange_rate( string $currency, $force = false, $base_currency = false ) : float {
		$woo_default_currency = cw_get_woocommerce_default_currency();
		if ( false === $base_currency ) {
			$base_currency = cw_get_woocommerce_default_currency();
		}

		! $force ?: $this->update_coin_rates( $currency, false, $force );

		$rate_cache = $this->get_coin_price_cache( $currency, $base_currency );
		if ( $rate_cache ) {
			return $rate_cache;
		}

		if ( 'BTC' !== $base_currency && ( 'BTC' !== $currency || $woo_default_currency !== $base_currency ) ) {
			$coin_rate = $this->calculate_coin_pair_rate( $currency, $base_currency, $woo_default_currency );
			wp_cache_set( $currency . $base_currency, $coin_rate, 'cryptowoo-rates', 60 );

			return $coin_rate;
		}

		$exchange_rate = $this->get_exchange_rate_from_db( $currency, $force, $base_currency );

		// Make sure that if exchange rate was 0 and we do not force update, that we do force an update.
		// Rates would be 0 if admin ajax run before the cron jobs and no exchange rates exists in database.
		if ( ! $exchange_rate && ! $force ) {
			return $this->get_exchange_rate( $currency, true, $base_currency );
		}

		return $exchange_rate;
	}

	/**
	 *
	 * Get cache for coin price from WP object cache.
	 *
	 * @param string       $currency      The currency name.
	 * @param string|false $fiat_currency The fiat currency name.
	 *
	 * @return false|string
	 */
	public function get_coin_price_cache( $currency, $fiat_currency = false ) {
		if ( empty( $fiat_currency ) ) {
			$fiat_currency = cw_get_woocommerce_currency();
		}
		$coin_pair = $currency . $fiat_currency;

		return wp_cache_get( $coin_pair, 'cryptowoo-rates' );
	}

	/**
	 * Return the fiat exchange rate for all currencies in the database
	 *
	 * @param  $force
	 * @return string
	 */
	function get_all_exchange_rates( $force = false ) {

		if ( $force ) {
			$admin_main = new CW_AdminMain();
			$admin_main->update_exchange_data();
		}

		global $wpdb;

		$cached = wp_cache_get( 'all_rates', 'cryptowoo-rates' ); // get rates from WP object cache

		if ( ! $cached ) {

			$results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'cryptowoo_exchange_rates', ARRAY_A );

			$enabled_currencies = cw_get_enabled_currencies( false, false );

			if ( empty( $results ) || count( $results ) < count( $enabled_currencies ) ) {
				if ( ! $force ) {
					return $this->get_all_exchange_rates( true );
				}
			}

			$rates = array();
			foreach ( $results as $result ) {
				if ( array_key_exists( $result['coin_type'], $rates ) ) {
					// - we want the lowest exchange rate for now
					if ( $result['exchange_rate'] >= $rates[ $result['coin_type'] ]['exchange_rate'] ) {
						continue;
					}
				}
				$rates[ $result['coin_type'] ] = $result;
			}
			unset( $results );

			// WooCommerce store currency
			$default_currency = cw_get_woocommerce_default_currency();

			// Get seconds passed since last update of BTC/$default_currency pair
			$last_update_btc      = array_key_exists( 'BTC' . $default_currency, $rates ) ? $rates[ 'BTC' . $default_currency ]['last_update'] : '01.01.1970';
			$last_update_time_ago = time() - strtotime( $last_update_btc );

			// Force rate update if the last update was more than 12 minutes ago
			if ( $last_update_time_ago > 720 && ! $force ) {
				$message = sprintf( 'Force refreshing exchange rates. Last update was %s minutes ago. Is your cron job working correctly?', number_format( $last_update_time_ago / 60, 2 ) );
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $message, 'warning' );
				return $this->get_all_exchange_rates( true );
			}

			// Add base currency same currency rate, eg BTCBTC
			$skey                            = $default_currency . $default_currency;
			$rates[ $skey ]['coin_type']     = $skey;
			$rates[ $skey ]['exchange_rate'] = 1;

			// Merge with fiat rates from addons (f.ex woocs)
			foreach ( cw_get_fiat_currencies() as $currency_name => $fiat_currency_data ) {
				$rates[ 'BTC' . $currency_name ]                  = $rates[ 'BTC' . $default_currency ];
				$rates[ 'BTC' . $currency_name ]['coin_type']     = 'BTC' . $currency_name;
				$rates[ 'BTC' . $currency_name ]['exchange_rate'] = $rates[ 'BTC' . $default_currency ]['exchange_rate'] * $fiat_currency_data['rate'];
			}
			// Calculate other rates on the fly.

			$active_currency = cw_get_woocommerce_currency();// cw_get_woocommerce_default_currency();

			// Create currency => price and last_update array from enabled currencies without testnet coins
			foreach ( $enabled_currencies as $key => $value ) {
				if ( ! in_array( $key, array( 'BTCTEST', 'BTCTEST-lightning', 'DOGETEST' ) ) ) {
					$active_currency_is_crypto = true;
					if ( ! CW_ExchangeRates::tools()->currency_is_crypto( $active_currency ) ) {
						$active_currency_is_crypto = false;
					}
					$skey = $key . $active_currency;

					if ( ! isset( $rates[ $skey ] ) ) {
						$bkey = $active_currency_is_crypto ? $active_currency . 'BTC' : 'BTC' . $active_currency;
						$dkey = 'BTC' !== $key ? $key . 'BTC' : $active_currency . 'BTC';

						$base_rate        = 'BTC' !== $active_currency ? $rates[ $bkey ]['exchange_rate'] : 1;
						$destination_rate = 'BTC' !== $key ? $rates[ $dkey ]['exchange_rate'] : 1;

						$calculated_rate = $active_currency_is_crypto ? $destination_rate / $base_rate : $destination_rate * $base_rate;

						$rates[ $skey ]                  = 'BTC' !== $active_currency ? $rates[ $dkey ] : $rates[ 'BTC' . $default_currency ];
						$rates[ $skey ]['coin_type']     = $skey;
						$rates[ $skey ]['exchange_rate'] = $calculated_rate;
					}
				}
			}

			// Save rate to WP object cache
			wp_cache_set( 'all_rates', $rates, 'cryptowoo-rates', 60 );
			$cached = $rates;
		}
		return $cached;
	}

	/**
	 * Save exchange rate to DB
	 *
	 * @param  $coin
	 * @param  $coin_fiat
	 * @param  $preferred_exchange
	 * @param  $status
	 * @param  $method
	 * @param  $time
	 * @return false|int
	 */
	function save_rate( $coin, $coin_fiat, $preferred_exchange, $status, $method, $time ) {
		global $wpdb;
		$last_update = date( 'Y-m-d H:i:s' );

		$result = $wpdb->query(
			$wpdb->prepare(
				"REPLACE INTO {$wpdb->prefix}cryptowoo_exchange_rates (coin_type, exchange_rate, exchange, status, method, last_update, api_timestamp) VALUES (%s, %.8f, %s, %s, %s, %s, %s)",
				$coin, // string
				$coin_fiat, // float
				$preferred_exchange, // string
				$status, // string
				$method, // string
				$last_update, // string
				$time
			)// string
			// s,f,s,s,s,s
		);

		// Save rate to WP object cache
		wp_cache_set( $coin, $coin_fiat, 'cryptowoo-rates', 60 ); // wp_cache_set( $currency, $rate, 'cryptowoo-rates', $expire );

		return $result;
	}

	/**
	 * Save market exchange rate to DB
	 *
	 * @param  $markets
	 * @param  $preferred_exchange
	 * @param  $status
	 * @param  $method
	 * @param  $time
	 * @return false|int
	 */
	function save_multi_rate( $markets, $preferred_exchange, $status, $method, $time ) {
		global $wpdb;
		$last_update = date( 'Y-m-d H:i:s' );

		$result = 0;
		foreach ( $markets as $market => $coin_fiat ) {
			$sql = $wpdb->prepare(
				"REPLACE INTO {$wpdb->prefix}cryptowoo_exchange_rates (coin_type, exchange_rate, exchange, status, method, last_update, api_timestamp)
													VALUES (%s, %f, %s, %s, %s, %s, %s);",
				$market, // string
				$coin_fiat, // float
				$preferred_exchange, // string
				$status, // string
				$method, // string
				$last_update, // string
				$time
			);// string
			// s,f,s,s,s,s
			// Save rate to WP object cache
			wp_cache_set( $market, $coin_fiat, 'cryptowoo-rates', 60 ); // wp_cache_set( $currency, $rate, 'cryptowoo-rates', $expire );
			$result += $wpdb->query( $sql );

		}

		return $result;
	}

	/**
	 * Maybe display backend notice and send e-Mail
	 *
	 * @param string     $used_method    The method that was used.
	 * @param bool       $update_success If update was successful or not.
	 * @param array      $prices         The array of prices (result data).
	 * @param int|string $time           The timestamp of result.
	 * @param string     $coin           The coin name.
	 * @param string     $coin_fiat      The fiat name.
	 *
	 * @return bool;
	 */
	public function maybe_warn_admin( $used_method, $update_success, $prices, $time, $coin, $coin_fiat ) {

		// Do not proceed if merchant disabled exchange rate warnings in CryptoWoo settings.
		if ( cw_get_option( 'cryptowoo_exchange_rate_warning' ) ) {
			return false;
		}

		$rate_error_transient = get_transient( 'cryptowoo_rate_errors' );

		$exchange_key       = sprintf( 'preferred_exchange_%s', strtolower( str_replace( 'TEST', '', $coin ) ) );
		$preferred_exchange = cw_get_option( $exchange_key ) ?: $exchange_key;

		$api_time                                   = strtotime( $time );
		$prices[ $used_method ]['api_time']         = $time;
		$prices[ $used_method ]['time_lag_seconds'] = time() - $api_time;
		$prices[ $used_method ]['time_lag_minutes'] = round( $prices[ $used_method ]['time_lag_seconds'] / 60, 2 );

		if ( ! $update_success || $prices[ $used_method ]['time_lag_seconds'] > 1800 ) {

			// Start counting if this is the first email in 60 minutes or use time from transient
			$rate_errors['counter_start'] = isset( $rate_error_transient['error_count'] ) && (int) $rate_error_transient['error_count'] >= 1 ? $rate_error_transient['counter_start'] : time();

			// Increase error counter
			$rate_errors['error_count'] = isset( $rate_error_transient['error_count'] ) ? (int) $rate_error_transient['error_count'] + 1 : 1;

			// Sent count
			$rate_errors['sent_count'] = isset( $rate_error_transient['sent_count'] ) ? (int) $rate_error_transient['sent_count'] : 0;

			// Human readable time
			$rate_errors['counter_start_date'] = date( 'Y-m-d H:i:s', $rate_errors['counter_start'] );

			// Calculate time since we started counting
			$last_error = time() - $rate_errors['counter_start'];

			// Reset counter after 60 minutes
			if ( (int) $last_error >= 3600 ) {
				$rate_errors['error_count_previous_period'] = $rate_errors['error_count'];
				$rate_errors['error_count']                 = 0;
			}

			// Add error stats to email data
			$prices['error_stats'] = $rate_errors;

			$status = var_export( $prices, true ); // print_r($prices, true);
			// $update_success = print_r($update_success, true);
			$update_success = $update_success ? 'success' : 'error';

			// Maybe prepare rate error data
			if ( cw_get_option( 'rate_error_charts' ) ) {
				$rate_error_log = array(
					'time'               => $api_time,
					'preferred_exchange' => $preferred_exchange,
					'used_exchange'      => $used_method,
					'coin'               => $coin,
					'coin_fiat'          => $coin_fiat,
					'status'             => $update_success,
					'time_lag_seconds'   => time() - $api_time,
					'time_lag_minutes'   => round( $prices[ $used_method ]['time_lag_seconds'] / 60, 2 ),
					'counter_start'      => $rate_errors['counter_start'],
					'error_count'        => $rate_errors['error_count'],
				);

				CW_AdminMain::cryptowoo_log_rate_errors_for_chart( $rate_error_log );
			}

			if ( (int) $rate_errors['error_count'] <= 1 ) {

				$to       = get_option( 'admin_email' );
				$blogname = get_bloginfo( 'name', 'raw' );
				$subject  = sprintf( __( '%s: CryptoWoo exchange rate update errors', 'cryptowoo' ), $blogname );

				// $error_count = isset( $prices['error_stats']['error_count'] ) ? $prices['error_stats']['error_count'] : '%undefined%';
				$date = isset( $prices['error_stats']['counter_start_date'] ) ? $prices['error_stats']['counter_start_date'] : date( 'd. M Y H:i:s' );

				$db_actions_page = sprintf( '<a href="%1$s">%1$s</a><br>', admin_url( 'admin.php?page=cryptowoo_database_maintenance' ) );
				$text            = __(
					'Hello Admin,<br>CryptoWoo has detected %1$s error(s) since %2$s while updating the exchange rates via %3$s:<br>
                Please log in at %4$s, check your settings, reset the error counter via the button on the database maintenance page, and try to update the rates manually.<br>
                Generally, these errors may occur from time to time, but if the exchange rate update fails constantly, please select a different preferred exchange API.<br>%5$s',
					'cryptowoo'
				);
				$message         = sprintf( $text, $rate_errors['error_count'], $date, $preferred_exchange, $blogname, $db_actions_page );

				$text_2   = _(
					'If you continue to receive this e-Mail after selecting another exchange rate API, please submit a ticket at http://cryptowoo.zendesk.com <br>
                Preferred method: %s <br>Used method: %s<br>Update Data: <br>%s<br>
                <br>Update status: %s<br>',
					'cryptowoo'
				);
				$message .= sprintf( $text_2, $preferred_exchange, $used_method, $status, $update_success );

				$headers = array(
					"From: CryptoWoo Plugin < {
	$to} > ",
					'Content-Type: text/html; charset=UTF-8',
				);

				if ( function_exists( 'wp_mail' ) ) {
					wp_mail( $to, $subject, $message, $headers );
				}
				$rate_errors['sent_count'] = isset( $rate_errors['sent_count'] ) ? $rate_errors['sent_count']++ : 1;
			}

			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, sprintf( '%s %s rate update: %s <br>status: %s', date( 'Y-m-d H:i:s' ), $used_method, $update_success, $status ), 'warning' );
			// Keep transient for one week after the last error
			set_transient( 'cryptowoo_rate_errors', $rate_errors, ( DAY_IN_SECONDS * 7 ) );
		}
	}

	/**
	 * Compare the exchange rate from the API response with previous rates
	 *
	 * @param  $price
	 * @param  $currency
	 * @param  $exchange
	 * @return bool
	 * @todo   review float difference calculation via epsilon
	 */
	function validate_exchange_rate( $price, $currency, $exchange ) {

		return is_numeric( $price ) && $price > 0;
		/*
		global $wpdb;

		$result = array();
		$result['is_valid'] = true;

		$query = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cryptowoo_exchange_rates
									   WHERE `coin_type` = '$currency';");

		if (!empty($query)) {
			$exchange_rate = $result['exchange_rate'] = $query[0]->exchange_rate;

			// Calculate maximum rate difference of 15%
			$epsilon = (float)$exchange_rate * 0.15;

			// Invalid if difference is larger than 15% -> use fallback
			if(abs((float)$exchange_rate-(float)$price) > $epsilon) {
				 $result['is_valid'] = false;
			}
		}
		if(!$result['is_valid']) {
			file_put_contents(CW_LOG_DIR . 'cryptowoo-rate-error.log', date("Y-m-d H:i:s") . " ==Rate validation error==\n". print_r($result, true) . "\r\n", FILE_APPEND);
		}
		return $result['is_valid'];
		*/
	}

	/**
	 * Display exchange rates from DB
	 *
	 * @package CW_ExchangeRates
	 */
	public function get_exchange_rates() {
		global $wpdb;
		$query = $this->get_exchange_rate_data();

		$rates = __( '<div id="message" class="error fade"><p class="flash_message">No exchange rates found in database. Please update manually.</p></div>', 'cryptowoo' );

		// $currencies = array();
		if ( ! empty( $query ) ) {

			$woocommerce_currency = cw_get_woocommerce_currency();
			$schedule             = wp_get_schedule( 'cryptowoo_cron_action' );

			$schedule_seconds = str_replace( 'seconds_', '', $schedule );

			$rates = '<br><div id="dvData"><table class="cw-table" style="border: 1px solid black;">
					<thead>
					<tr><th colspan="9" style="text-align:center;">Current Rates in Database</th></tr>
						<tr><th>Rate</th>
							<th>Currency Pair</th>
							<th>Exchange</th>
							<th>Method</th>
							<th>Status</th>
							<th>Last Run</th>
							<th>API Time</th>
							<th>Data Lag</th>
							<th>API URL</th>
						</tr></thead>' . "\n";

			foreach ( $query as $currency ) {
				// Get API URL
				$base_currency    = strpos( $currency['coin_type'], 'BTC' ) === 0 ? $woocommerce_currency : 'BTC';
				$coin_name        = str_replace( $base_currency, '', $currency['coin_type'] );
				$api_url          = $this->tools->get_rate_api_url( $coin_name, $currency['method'] );
				$last_update      = strtotime( $currency['last_update'] );
				$last_run_sec_ago = time() - $last_update;
				$data_lag         = $last_update - strtotime( $currency['api_timestamp'] );

				// take cron schedule into account when evaluating last run status
				$execution_status = (int) $last_run_sec_ago - (int) $schedule_seconds;

				if ( (int) $execution_status <= 65 ) {
					// all good: last run timestamp within 15 seconds on schedule
					$last_run_color = 'color:green;';
				} elseif ( (int) $execution_status > 65 && (int) $execution_status < 135 ) {
					// last rate update more than 60 seconds after current schedule: warn
					$last_run_color = 'background-color:yellow; font-weight: bold;';
				} else {
					// error: ĺast update is more than 60 seconds after schedule
					$last_run_color = ! strpos( $currency['status'], 'disabled' ) ? 'color:#B94A48; font-weight: bold;' : '';
				}

				// API response status colour
				$td_color_status = $currency['status'] == 'success' || strpos( $currency['status'], 'disabled' ) ? 'color:green;' : 'background-color:yellow; font-weight: bold;';

				// API Lag colour
				if ( $data_lag >= 300 && $data_lag < 666 ) {
					$td_color_time = 'background-color:yellow; font-weight: bold;';
				} elseif ( $data_lag < 300 ) {
					$td_color_time = 'color:green;';
				} else {
					$td_color_time = false === strpos( $currency['status'], 'disabled' ) ? 'color:#B94A48; font-weight: bold;' : '';
				}

				$rates .= '<tr>';
				$rates .= '<td class="cw-bold">' . CW_Formatting::fbits( $currency['exchange_rate'], false ) . '</td>';

				$print_currency = $currency['coin_type'];

				$rates .= '<td class="bits" style="text-align: left;">' . $print_currency . '</td>';
				$rates .= '<td class="bits" style="text-align: center;">' . $currency['exchange'] . '</td>';
				$rates .= '<td class="bits" style="text-align: center;">' . $currency['method'] . '</td>';
				$rates .= '<td class="cw-bold" style="text-align: center;' . $td_color_status . '">' . $currency['status'] . '</td>';
				$rates .= '<td class="bits" style="border: 1px solid black; ' . $last_run_color . '">' . $last_run_sec_ago . ' sec ago (' . $currency['last_update'] . ')</td>';
				$rates .= '<td class="bits" style="border: 1px solid black;">' . $currency['api_timestamp'] . '</td>';
				$rates .= '<td class="bits" style="' . $td_color_time . '">' . (int) $data_lag . ' sec</td>';
				$rates .= '<td class="bits" style="text-align: left;">';
				$rates .= $api_url ? '<a href="' . $api_url . '" target="_blank" title="Open API URL">' . $api_url . '</a>' : 'N/A';
				$rates .= '</td>';
				$rates .= '</tr>';
			}
			$rates .= '</table></div>';
		} else {
			$rates = '<div id="message" class="error fade"><p class="flash_message">No exchange rates found in database. Please update manually.</p></div>';
		}

		return $rates;
	}

}//end class
