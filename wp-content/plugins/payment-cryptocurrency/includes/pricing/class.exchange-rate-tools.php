<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 *
 * Exchange Rate tools
 *
 * @category   CryptoWoo
 * @package    CryptoWoo
 * @subpackage ExchangeRates
 * @author     Developer: CryptoWoo AS
 */
class CW_ExchangeRate_Tools {


	/**
	 *
	 * Get the preferred exchange for a coin.
	 *
	 * @param string $currency The coin.
	 *
	 * @return mixed
	 */
	public function get_preferred_exchange( $currency ) {
		$lc_currency            = strtolower( $currency );
		$preferred_exchange_key = sprintf( 'preferred_exchange_%s', $lc_currency );
		return apply_filters( 'cw_override_preferred_exchange_' . $lc_currency, cw_get_option( $preferred_exchange_key ) );
	}

	/**
	 *
	 * Get the array of coin enabled status
	 *
	 * @param string $currency The coin.
	 *
	 * @return mixed
	 */
	private function get_coin_enabled_status( $currency ) {
		$lc_coin = strtolower( $currency );

		$enabled = cw_get_enabled_currencies( false, false );

		return apply_filters( "cw_coins_enabled_$lc_coin", $enabled, $lc_coin, cw_get_options() ); // TODO Consolidate with cw_coins_enabled in cw_get_enabled_currencies()
	}

	/**
	 *
	 * Returns true or false if the coin is enabled or not.
	 *
	 * @param string $currency The coin.
	 *
	 * @return bool
	 */
	public function coin_is_enabled( string $currency ) : bool {
		return array_key_exists( $currency, $this->get_coin_enabled_status( $currency ) );
	}

	/**
	 *
	 * Get an exchange class instance by the exchange id (index)
	 *
	 * @param string      $exchange_id The exchange id (index).
	 * @param string|null $coin_type   The type of coin (f.ex BTC).
	 *
	 * @return CW_Exchange_Base|null
	 */
	public function get_exchange_instance_by_id( string $exchange_id, string $coin_type ) {
		$class_name = $this->get_class_name_from_exchange_name( $exchange_id );

		return $this->get_exchange_instance_by_name( $class_name, $coin_type );
	}

	/**
	 *
	 * Get an exchange class instance by the exchange class name
	 *
	 * @param string      $exchange_class_name The exchange class name.
	 * @param string|null $coin_type           The type of coin (f.ex BTC).
	 *
	 * @return CW_Exchange_Base|null
	 */
	public function get_exchange_instance_by_name( string $exchange_class_name, string $coin_type ) {
		// This lets CryptoWoo Add-ons override exchange class for a specific cryptocurrency.
		$exchange_class_name = apply_filters( 'cw_exchange_class_name', $exchange_class_name, $coin_type );
		if ( class_exists( $exchange_class_name, false ) ) {
			return new $exchange_class_name( $coin_type );
		}

		return class_exists( $exchange_class_name ) ? new $exchange_class_name( $coin_type ) : null;
	}

	/**
	 *
	 * Get API URL for the exchange
	 *
	 * @param string $currency Currency name.
	 * @param string $method   Exchange id.
	 *
	 * @return string
	 */
	public function get_rate_api_url( $currency, $method ) {
		// Return none if no api was used.
		if ( 'none' === $method ) {
			return 'none';
		}

		$exchange_instance = $this->get_exchange_instance_by_id( $method, $currency );
		if ( ! $exchange_instance instanceof CW_Exchange_Base ) {
			return __( 'Could not get exchange api url ', 'cryptowoo' );
		}

		return $exchange_instance->get_exchange_url();
	}

	/**
	 *
	 * Exchange API nice name
	 *
	 * @param string $exchange_id the exchange id (index).
	 *
	 * @return string
	 */
	public function get_exchange_nicename( $exchange_id ) {
		if ( empty( $exchange_id ) ) {
			return '';
		}

		$exchange_instance = $this->get_exchange_instance_by_id( $exchange_id, 'btc' );
		if ( ! $exchange_instance instanceof CW_Exchange_Base ) {
			return ucfirst( $exchange_id );
		}

		return $exchange_instance->get_exchange_nicename();
	}

	/**
	 * Get all exchanges
	 *
	 * @param string|false $currency For a specific currency or all?.
	 *
	 * @return array
	 */
	public function get_exchanges( $currency = false ) {
		$exchanges_class_names = array();
		if ( $currency ) {
			$exchanges_class_names = $this->get_preferred_exchanges_class_names( $currency );
		} else {
			foreach ( get_declared_classes() as $class ) {
				if ( is_subclass_of( $class, CW_Exchange_Base::class ) ) {
					$exchanges_class_names = $class;
				}
			}
		}

		$exchanges = array();
		foreach ( $exchanges_class_names as $class ) {
			/**

	   * Instantiate exchange class @var CW_Exchange_Base $exchange_class Exchange class instance
*/
			$exchange_class = $this->get_exchange_instance_by_name( $class, $currency );

			$exchanges[ $exchange_class->get_exchange_name() ] = array(
				'nicename' => $exchange_class->get_exchange_nicename(),
				'api_url'  => $exchange_class->get_exchange_url(),
			);
		}

		return $exchanges;
	}

	/**
	 * Get all exchange class names for specific currency preferred exchanges options (Redux)
	 *
	 * @param string $currency The currency to get preferred exchanges for.
	 *
	 * @return array
	 */
	public function get_preferred_exchanges_class_names( string $currency ) {
		$preferred_exchanges = $this->get_preferred_exchanges_names( $currency );

		$declared_classes = array();
		foreach ( $preferred_exchanges as $exchange ) {
			$class_name = $this->get_class_name_from_exchange_name( $exchange );
			if ( $class_name && is_subclass_of( $class_name, CW_Exchange_Base::class ) ) {
				$declared_classes[] = $class_name;
			}
		}

		return $declared_classes;
	}

	/**
	 * Get all exchange options for a specific currency (Redux)
	 *
	 * @param string $currency The currency to get exchange options for.
	 *
	 * @return array
	 */
	public function get_preferred_exchanges_options( string $currency ) {
		$preferred_exchange_field = Redux::get_field( 'cryptowoo_payments', 'preferred_exchange_' . strtolower( $currency ) );

		return $preferred_exchange_field['options'] ?? array();
	}

	/**
	 * Get all exchange names for a specific currency (Redux)
	 *
	 * @param string $currency The currency to get exchange options for.
	 *
	 * @return array
	 */
	public function get_preferred_exchanges_names( string $currency ) {
		return array_keys( $this->get_preferred_exchanges_options( $currency ) );
	}

	/**
	 * Get the class name from exchange id
	 *
	 * @param string $exchange_id the exchange id.
	 *
	 * @return string
	 */
	public function get_class_name_from_exchange_name( $exchange_id ) {
		$exchange_name = str_replace( '-fallback', '', $exchange_id );

		return "CW_Exchange_$exchange_name";
	}

	/**
	 *
	 * Returns if the currency is a fiat currency or not.
	 *
	 * @param string $currency The currency short name (eg USD).
	 *
	 * @return bool
	 */
	public function currency_is_fiat( $currency ) {
		return ! $this->currency_is_crypto( $currency );
	}

	/**
	 *
	 * Returns if the currency is a cryptocurrency or not
	 *
	 * @param string $currency The currency short name (eg BTC).
	 *
	 * @return bool
	 */
	public function currency_is_crypto( $currency ) {
		$cw_currencies        = cw_get_enabled_currencies();
		$cw_currencies['BTC'] = 'Bitcoin'; // Make sure Bitcoin is always in list even if not enabled.

		return isset( $cw_currencies[ $currency ] );
	}
}
