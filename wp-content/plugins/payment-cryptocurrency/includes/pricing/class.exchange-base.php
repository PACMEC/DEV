<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

if ( ! class_exists( CW_Exchange_Base::class ) ) {
	/**
	 * Base Exchange class to get exchange rates
	 *
	 * @category   CryptoWoo
	 * @package    Exchange
	 * @subpackage ExchangeBase
	 * Author: CryptoWoo AS
	 * Author URI: https://cryptowoo.com
	 */
	abstract class CW_Exchange_Base {

		/**
		 *
		 * Currency string
		 *
		 * @var string
		 */
		private $currency;

		/**
		 *
		 * Base currency override string
		 *
		 * @var string
		 */
		private $base_currency_override;

		/**
		 * CW_Exchange_Base constructor.
		 *
		 * @param string $currency currency name.
		 */
		public function __construct( string $currency ) {
			$this->currency = strtoupper( $currency );
		}

		/**
		 *
		 * Get the exchange API URL with format
		 *
		 * @return string
		 */
		abstract protected function get_exchange_url_format(): string;

		/**
		 *
		 * Get the exchange price index (last index)
		 *
		 * @return string
		 */
		abstract protected function get_exchange_price_index(): string;

		/**
		 *
		 * Get the formatting of currency pair for exchange API
		 *
		 * @return string
		 */
		abstract protected function get_pair_format(): string;


		/**
		 *
		 * Get the exchange name.
		 *
		 * @return string
		 */
		public function get_exchange_name(): string {
			return strtolower( str_replace( array( 'CW_Exchange_', '_' . $this->get_currency_name() ), '', get_class( $this ) ) );
		}

		/**
		 *
		 * Get the exchange name in nice format.
		 *
		 * @return string
		 */
		public function get_exchange_nicename(): string {
			return str_replace( array( 'CW_Exchange_', $this->get_currency_name(), '_' ), array( '', '', ' ' ), get_class( $this ) );
		}

		/**
		 *
		 * Get the currency name
		 *
		 * @return string
		 */
		protected function get_currency_name(): string {
			return $this->currency;
		}

		/**
		 *
		 * Get the base Currency name
		 *
		 * @return string
		 */
		protected function get_base_currency_name(): string {

			if ( isset( $this->base_currency_override ) ) {
				$base_currency_name = $this->base_currency_override;
			} else {
				$base_currency_name = 'BTC';
				if ( 'BTC' === $this->get_currency_name() ) {
					$default_currency   = cw_get_woocommerce_default_currency();
					$base_currency_name = 'BTC' !== $default_currency ? $default_currency : 'USD';
				}
			}
			return $base_currency_name;
		}

		/**
		 *
		 * Override the base Currency name
		 *
		 * @param string $base_currency_name Base currency name, eg. EUR
		 *
		 * @return string
		 */
		public function set_base_currency_override( $base_currency_name ): string {
			$this->base_currency_override = $base_currency_name;
			return $base_currency_name;
		}

		/**
		 *
		 * Get timeout for exchange API call
		 *
		 * @return int
		 */
		protected function get_timeout(): int {
			return 10;
		}

		/**
		 *
		 * If exchange API call returns json or not
		 *
		 * @return bool
		 */
		protected function is_json(): bool {
			return true;
		}

		/**
		 *
		 * If exchange api should be called as user agent
		 *
		 * @return bool
		 */
		protected function is_user_agent(): bool {
			return false;
		}

		/**
		 *
		 * Exchange api proxy (default no proxy)
		 *
		 * @return string
		 */
		protected function get_proxy() : string {
			return '';
		}

		/**
		 *
		 * Get the stale index in exchange result.
		 * Default is '' (no stale index in result)
		 *
		 * @return string
		 */
		protected function get_exchange_stale_index() : string {
			return '';
		}

		/**
		 *
		 * Create and return search currency ID for exchange API (for exchanges that use ID instead of currency code)
		 * Default is empty array because most exchanges does not use ids but currency code.
		 * Note that this function should be set to final in exchange classes if used.
		 *
		 * @return array
		 */
		protected function get_search_currency_ids() {
			return array();
		}

		/**
		 *
		 * Create and return search currency ID for exchange API (for exchanges that use ID instead of currency code)
		 * This function is used by cryptocurrency addons to add their search currency id. If it has data use it.
		 * Default is false because most exchanges does not use ids but currency code.
		 *
		 * @return string|int|false
		 */
		protected function get_search_currency_id() {
			$search_currency_ids = $this->get_search_currency_ids();

			if ( isset( $search_currency_ids[ $this->get_currency_name() ] ) ) {
				return $search_currency_ids[ $this->get_currency_name() ];
			}

			return false;
		}

		/**
		 *
		 * Get the exchange rate pair (base/currency)
		 *
		 * @return string
		 */
		protected function get_search_pair(): string {
			$search_pair = (bool) $this->get_currency_name() ? sprintf( $this->get_pair_format(), $this->get_base_currency_name(), $this->get_search_currency() ) : 'ALL';
			if ( $this->search_pair_is_uppercase() ) {
				return strtoupper( $search_pair );
			} else {
				return strtolower( $search_pair );
			}
		}

		/**
		 *
		 * Is the exchange rate search pair uppercase or lowercase in the api url?
		 * Default is lower case.
		 *
		 * @return bool
		 */
		protected function search_pair_is_uppercase(): bool {
			return false;
		}

		/**
		 *
		 * Is the search currency XBT instead of BTC in search pair?
		 * Default is false (the search pair is BTC).
		 *
		 * @return bool
		 */
		protected function search_pair_btc_is_xbt(): bool {
			return false;
		}

		/**
		 *
		 * Get the formatted exchange API URL
		 *
		 * @return string
		 */
		public function get_exchange_url() : string {
			return sprintf( $this->get_exchange_url_format(), $this->get_search_pair() );
		}

		/**
		 *
		 * Format the price data from exchange result to default data format
		 *
		 * @param stdClass $price_data Json decoded result from exchange api call.
		 *
		 * @return stdClass
		 */
		protected function format_price_data_from_exchange( stdClass $price_data ) : stdClass {
			return $price_data;
		}

		/**
		 *
		 * Get the timestamp in the exchange data result
		 * Or generate timestamp if none exist
		 *
		 * @param stdClass $price_data Json decoded result from exchange api call.
		 *
		 * @return string
		 */
		protected function get_timestamp_from_price_data( stdClass $price_data ) : string {
			if ( ! isset( $price_data->timestamp ) || ! $price_data->timestamp ) {
				return time();
			}
			return $price_data->timestamp;
		}

		/**
		 *
		 * Convert iso string to timestamp
		 *
		 * @param string $iso_string Time as iso string.
		 * @param string $format     Time string format to use.
		 *
		 * @return int|false
		 */
		protected function convert_iso_to_timestamp( $iso_string, $format ) : int {
			$date = DateTime::createFromFormat( $format, $iso_string );
			if ( false === $date ) {
				return false;
			}
			return $date->getTimestamp();
		}

		/**
		 *
		 * Make sure price is formatted correctly
		 *
		 * @param string|float|int $price The price.
		 *
		 * @return float
		 */
		protected function format_price( $price ) {
			return CW_ExchangeRates::processing()->format_price( $price );
		}


		/**
		 * Get exchange rates, cross-calculate fiat calculate Altcoin/Fiat values via BTC/Fiat
		 *
		 * @return mixed
		 */
		public function get_coin_price() {
			$request = CW_ExchangeRates::processing()->request( $this->get_exchange_url(), $this->is_json(), $this->is_user_agent(), $this->get_timeout(), $this->get_proxy() );

			$price_data = self::get_coin_price_validate( $request );
			$price_pair = $this->get_currency_name() . $this->get_base_currency_name();

			if ( 'success' !== $price_data['status'] ) {
				$log_data = array(
					'price_pair' => $price_pair,
					'exchange'   => $this->get_exchange_name(),
					'url'        => $this->get_exchange_url(),
				);
				CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $log_data, 'error' );
				return $price_data;
			}

			$price_data = $price_data['price_data'];

			$prices[ $this->get_exchange_name() ][ $price_pair ]['price']     = $this->format_price( $price_data->{$this->get_exchange_price_index()} );
			$prices[ $this->get_exchange_name() ][ $price_pair ]['timestamp'] = $this->get_timestamp_from_price_data( $price_data );
			$prices[ $this->get_exchange_name() ]['status']                   = 'success';

			return $prices;
		}

		/**
		 *
		 * Validate and return coin price data
		 *
		 * @param string|WP_Error $request Return data from exchange API.
		 *
		 * @return array
		 */
		private function get_coin_price_validate( $request ) : array {
			if ( $request instanceof WP_Error ) {
				$error_code = $request->get_error_code();
				$error_msg  = $request->get_error_message();
				return array( 'status' => "$error_code|$error_msg|Exchange rate not found" );
			}

			$price_data = json_decode( $request );
			if ( null === $price_data || ! $price_data instanceof stdClass ) {
				return array(
					'status'     => 'Could not decode json|Exchange rate not found',
					'price_data' => $request,
				);
			}

			$price_data = $this->format_price_data_from_exchange( $price_data );
			if ( ! $price_data || ! isset( $price_data->{$this->get_exchange_price_index()} ) ) {
				return array(
					'status'     => 'Exchange rate not found',
					'price_data' => $price_data,
				);
			}

			$stale_index = $this->get_exchange_stale_index();
			if ( $stale_index && $price_data->$stale_index ) {
				return array(
					'status'     => 'Exchange rate is stale',
					'price_data' => $price_data,
				);
			}

			return array(
				'status'     => 'success',
				'price_data' => $price_data,
			);
		}

		/**
		 *
		 * Create and return search currency for exchange API
		 *
		 * @return string
		 */
		private function get_search_currency(): string {
			// If the exchange is using search currency ids instead of currency code, use the id.
			if ( $this->get_search_currency_id() ) {
				return $this->get_search_currency_id();
			}

			$currency_name = $this->get_currency_name();

			if ( $this->search_pair_btc_is_xbt() && 'BTC' === strtoupper( $currency_name ) ) {
				return 'XBT';
			}

			return $currency_name;
		}
	}
}
