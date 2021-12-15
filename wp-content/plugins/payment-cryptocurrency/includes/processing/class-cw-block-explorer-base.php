<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * CryptoWoo Base Block Explorer API Processing Class
 *
 * @category   CryptoWoo
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoWoo AS
 *
 * TODO: API limits
 */
abstract class CW_Block_Explorer_Base {

	/**
	 *
	 * Currency string
	 *
	 * @var string
	 */
	private $currency;

	/**
	 *
	 * Addresses array (index is order id)
	 *
	 * @var string[]
	 */
	private $addresses;

	/**
	 *
	 * Skipped Addresses array
	 *
	 * @var string[]
	 */
	private $skipped_addresses;

	/**
	 *
	 * Custom base URL (Set before the custom base url has been stored in CryptoWoo options or to override the url).
	 *
	 * @var string
	 */
	private $custom_base_url;

	/**
	 * CW_Block_Explorer_Base constructor.
	 *
	 * @param string|false $currency  currency name.
	 * @param array|false  $addresses address(es) to check (with order id as index).
	 *
	 * @throws InvalidArgumentException Throws exception if unsupported currency is supplied.
	 */
	public function __construct( $currency, $addresses = false ) {
		$this->currency  = strtoupper( $currency );
		$this->addresses = $addresses;

		if ( ! in_array( $currency, $this->get_supported_currencies(), true ) ) {
			throw new InvalidArgumentException( "$currency is not supported for block explorer {$this->get_name()}" );
		}

		// Lets make sure we do not include more than the max allowed addresses in a request.
		if ( count( $this->get_addresses_array() ) > $this->get_api_max_allowed_addresses() ) {
			$this->skipped_addresses = array_splice( $this->addresses, $this->get_api_max_allowed_addresses() );
		}
	}

	/**
	 *
	 * Get the block explorer API URL with format
	 *
	 * @return string
	 */
	abstract protected function get_base_url(): string;

	/**
	 *
	 * Get the block explorer supported currencies
	 *
	 * @return string[]
	 */
	abstract protected function get_supported_currencies(): array;

	/**
	 *
	 * Get the block explorer api block height endpoint format
	 *
	 * @return string
	 */
	abstract protected function get_block_height_endpoint_format(): string;

	/**
	 *
	 * Get the block explorer tx api endpoint format
	 *
	 * @return string
	 */
	abstract protected function get_txs_endpoint_format(): string;

	/**
	 *
	 * Get the block explorer block hash api endpoint format
	 *
	 * @return string
	 */
	abstract protected function get_block_hash_endpoint_format(): string;

	/**
	 *
	 * Get the block explorer txs key name
	 *
	 * @return string
	 */
	abstract protected function get_txs_key_name() : string;

	/**
	 *
	 * Get the block explorer txs txid key name
	 *
	 * @return string
	 */
	abstract protected function get_tx_txid_key_name() : string;

	/**
	 *
	 * Get the block explorer txs confirms key name
	 *
	 * @return string
	 */
	abstract protected function get_tx_confirms_key_name() : string;

	/**
	 *
	 * Get the block explorer tx confidence key name.
	 * Default is no confidence key available (empty string).
	 *
	 * @return string
	 */
	protected function get_tx_confidence_key_name() : string {
		return '';
	}

	/**
	 *
	 * Get the block explorer tx instant key name.
	 * Default is no tx instant key available (empty string).
	 *
	 * @return string
	 */
	protected function get_tx_instant_key_name() : string {
		return '';
	}

	/**
	 *
	 * Get the block explorer txs amount key name
	 *
	 * @return string
	 */
	abstract protected function get_tx_amount_key_name() : string;

	/**
	 *
	 * Get the block explorer txs locktime key name
	 *
	 * @return string
	 */
	abstract protected function get_tx_locktime_key_name() : string;

	/**
	 *
	 * Get the block explorer txs timestamp key name
	 *
	 * @return string
	 */
	abstract protected function get_tx_timestamp_key_name() : string;

	/**
	 *
	 * Get the block explorer txs address key name
	 *
	 * @return string
	 */
	abstract protected function get_tx_address_key_name() : string;

	/**
	 *
	 * Get the block explorer tx block height key name
	 *
	 * @return string
	 */
	abstract protected function get_tx_block_height_key_name() : string;

	/**
	 *
	 * Get the block explorer tx double spend key name
	 *
	 * @return string
	 */
	abstract protected function get_tx_double_spend_key_name() : string;

	/**
	 *
	 * Get the block explorer tx inputs key name
	 *
	 * @return string
	 */
	abstract protected function get_tx_inputs_key_name() : string;

	/**
	 *
	 * Get the block explorer tx outputs key name
	 *
	 * @return string
	 */
	abstract protected function get_tx_outputs_key_name() : string;

	/**
	 *
	 * Get the block explorer tx input sequence key name
	 *
	 * @return string
	 */
	abstract protected function get_tx_input_sequence_key_name() : string;

	/**
	 *
	 * Get the block explorer block height key name
	 *
	 * @return string
	 */
	abstract protected function get_block_height_key_name() : string;

	/**
	 *
	 * Get the block explorer block hash key name
	 *
	 * @return string
	 */
	abstract protected function get_block_hash_key_name() : string;

	/**
	 *
	 * Get the block explorer max txs allowed in api call
	 *
	 * @return int
	 */
	abstract public function get_api_max_allowed_addresses() : int;

	/**
	 *
	 * Get the block explorer name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return strtolower( str_replace( array( 'CW_Block_Explorer_', '_' . $this->get_currency_name() ), '', get_class( $this ) ) );
	}

	/**
	 *
	 * Get the block explorer name in nice format.
	 *
	 * @return string
	 */
	public function get_nicename(): string {
		return str_replace( array( 'CW_Block_Explorer_', $this->get_currency_name(), '_' ), array( '', '', ' ' ), get_class( $this ) );
	}

	/**
	 *
	 * Get the block explorer name.
	 *
	 * @return string
	 */
	public function get_api_name(): string {
		return strtolower( str_replace( array( 'CW_Block_Explorer_API_' ), '', get_parent_class( $this ) ) );
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
	 * Get the addresses array
	 *
	 * @return string[]
	 */
	protected function get_addresses_array(): array {
		return $this->addresses ?: array();
	}

	/**
	 *
	 * Get the address or addresses string for use in blockchain url parameter or url
	 *
	 * @return string
	 */
	protected function get_addresses_string(): string {
		// There is no point to continue if only one address is supported,then just return first address.
		if ( 1 === $this->get_api_max_allowed_addresses() ) {
			return $this->get_current_address();
		}

		return implode( ',', $this->get_addresses_array() );
	}

	/**
	 *
	 * Get the skipped addresses string
	 * When there is a limit of
	 *
	 * @return string[]
	 */
	public function get_skipped_addresses(): array {
		return $this->skipped_addresses ?: array();
	}

	/**
	 *
	 * Get the current address in address array
	 *
	 * @return string
	 */
	protected function get_current_address(): string {
		return current( $this->get_addresses_array() );
	}

	/**
	 *
	 * Get the current order id in address array (the index is the order id)
	 *
	 * @return int
	 */
	protected function get_current_order_id(): int {
		return key( $this->get_addresses_array() );
	}

	/**
	 *
	 * If api has an api key in CryptoWoo settings.
	 *
	 * @return bool
	 */
	public function has_api_key(): bool {
		return ! empty( $this->get_api_key() );
	}

	/**
	 *
	 * Get the api key key string
	 *
	 * @return string
	 */
	protected function get_api_key_key(): string {
		return "cryptowoo_{$this->get_api_name()}_api";
	}

	/**
	 *
	 * Get the api key string
	 *
	 * @return string
	 */
	protected function get_api_key(): string {
		return cw_get_option( $this->get_api_key_key() );
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
	 * Is the block explorer currency uppercase or lowercase in the api url?
	 * Default is lower case.
	 *
	 * @return bool
	 */
	protected function search_currency_is_uppercase(): bool {
		return false;
	}

	/**
	 *
	 * Is the block explorer crypto amount in satoshi (1e8)?
	 * Default is true (amount is satoshi ).
	 *
	 * @return bool
	 */
	protected function amount_from_api_is_satoshi(): bool {
		return true;
	}

	/**
	 *
	 * Get the formatted txs URL
	 *
	 * @return string
	 */
	public function get_txs_url() : string {
		return $this->format_api_url( $this->get_txs_endpoint_format() );
	}

	/**
	 *
	 * Get the formatted block height URL
	 *
	 * @return string
	 */
	public function get_block_height_url() : string {
		return $this->format_api_url( $this->get_block_height_endpoint_format() );
	}

	/**
	 *
	 * Get the formatted block height URL
	 *
	 * @param int $block_index Block index (eg 0 for genesis block).
	 *
	 * @return string
	 */
	public function get_block_hash_url( int $block_index = 0 ) : string {
		return $this->format_api_url( $this->get_block_hash_endpoint_format(), false, $block_index );
	}

	/**
	 *
	 * Get the formatted block height URL
	 *
	 * @param string $endpoint_format Endpoint URL format.
	 * @param mixed  $param_1         First parameter in URL format, default is currency_name.
	 * @param mixed  $param_2         Second parameter in URL format, default is address.
	 * @param mixed  $param_3         Third parameter in URL format, default is api key.
	 *
	 * @return string
	 */
	protected function format_api_url( string $endpoint_format, $param_1 = false, $param_2 = false, $param_3 = false ) : string {
		false !== $param_1 ?: $param_1 = $this->get_search_currency();
		false !== $param_2 ?: $param_2 = $this->get_addresses_string();
		false !== $param_3 ?: $param_3 = $this->get_api_key();

		$base_url = empty( $this->custom_base_url ) ? $this->get_base_url() : $this->custom_base_url;

		return $endpoint_format ? sprintf( $base_url . $endpoint_format, $param_1, $param_2, $param_3 ) : '';
	}

	/**
	 *
	 * Get the formatted block explorer search currency cody
	 *
	 * @return string
	 */
	protected function get_search_currency(): string {
		$search_pair = str_replace( 'TEST', '', $this->get_currency_name() );
		if ( $this->search_currency_is_uppercase() ) {
			return strtoupper( $search_pair );
		} else {
			return strtolower( $search_pair );
		}
	}

	/**
	 *
	 * Format the data from block explorer result to default data format
	 *
	 * @param stdClass|array $block_explorer_data Json decoded result from block explorer api call.
	 *
	 * @return stdClass|array
	 */
	protected function format_result_from_block_explorer( $block_explorer_data ) {
		return $block_explorer_data;
	}

	/**
	 *
	 * Format the data from block explorer txs result to default data format
	 *
	 * @param stdClass|array $txs_data Json decoded txs result from block explorer api call.
	 *
	 * @return stdClass|array
	 */
	protected function format_txs_result_from_block_explorer( $txs_data ) {
		return $txs_data;
	}

	/**
	 *
	 * Filter the txs from block explorer result to keep only necessary data and to simplify order processing.
	 *
	 * @param stdClass[] $block_explorer_txs_data Json decoded txs result from block explorer api call.
	 *
	 * @return stdClass[]
	 */
	protected function filter_txs_from_block_explorer( array $block_explorer_txs_data ) : array {
		$filtered_txs_data = array();

		foreach ( $block_explorer_txs_data as $index => $tx ) {
			$filtered_txs_data[] = (object) array_filter(
				array(
					'id'          => isset( $tx->{$this->get_tx_txid_key_name()} ) ? $tx->{$this->get_tx_txid_key_name()} : null,
					'amount'      => isset( $tx->{$this->get_tx_amount_key_name()} ) ? $tx->{$this->get_tx_amount_key_name()} : null,
					'confirms'    => isset( $tx->{$this->get_tx_confirms_key_name()} ) ? $tx->{$this->get_tx_confirms_key_name()} : null,
					'confidence'  => isset( $tx->{$this->get_tx_confidence_key_name()} ) ? $tx->{$this->get_tx_confidence_key_name()} : null,
					'locktime'    => isset( $tx->{$this->get_tx_locktime_key_name()} ) ? $tx->{$this->get_tx_locktime_key_name()} : null,
					'time'        => isset( $tx->{$this->get_tx_timestamp_key_name()} ) ? $tx->{$this->get_tx_timestamp_key_name()} : null,
					'address'     => isset( $tx->{$this->get_tx_address_key_name()} ) ? $tx->{$this->get_tx_address_key_name()} : null,
					'height'      => isset( $tx->{$this->get_tx_block_height_key_name()} ) ? $tx->{$this->get_tx_block_height_key_name()} : null,
					'sequences'   => isset( $tx->{$this->get_tx_input_sequence_key_name()} ) ? $tx->{$this->get_tx_input_sequence_key_name()} : null,
					'doublespend' => isset( $tx->{$this->get_tx_double_spend_key_name()} ) ? $tx->{$this->get_tx_double_spend_key_name()} : null,
					'instant'     => isset( $tx->{$this->get_tx_instant_key_name()} ) ? $tx->{$this->get_tx_instant_key_name()} : null,
				),
				function ( $v ) {
					return ! is_null( $v );
				}
			);
		}

		return $filtered_txs_data;
	}

	/**
	 *
	 * Get the required indexes (key names) from the block explorer result txs data.
	 *
	 * @return array
	 */
	protected function get_required_txs_keys() : array {
		return array_filter(
			array(
				'id'          => $this->get_tx_txid_key_name(),
				'confirms'    => $this->get_tx_confirms_key_name(),
				'confidence'  => $this->get_tx_confidence_key_name(),
				'amount'      => $this->get_tx_amount_key_name(),
				'locktime'    => $this->get_tx_locktime_key_name(),
				'time'        => $this->get_tx_timestamp_key_name(),
				'address'     => $this->get_tx_address_key_name(),
				'height'      => $this->get_tx_block_height_key_name(),
				'doublespend' => $this->get_tx_double_spend_key_name(),
				'inputs'      => $this->get_tx_inputs_key_name(),
				'outputs'     => $this->get_tx_outputs_key_name(),
			)
		);
	}


	/**
	 *
	 * Get the timestamp in the block explorer data result
	 * Or generate timestamp if none exist
	 *
	 * @param stdClass $block_explorer_data Json decoded result from block explorer api call.
	 *
	 * @return string
	 */
	protected function get_timestamp_from_data( stdClass $block_explorer_data ) : string {
		if ( ! isset( $block_explorer_data->timestamp ) || ! $block_explorer_data->timestamp ) {
			return time();
		}
		return $block_explorer_data->timestamp;
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
	 * Get block hash from block explorer api by block index
	 *
	 * @param int $block_index Block index (eg 0 for genesis block). Default is 0.
	 *
	 * @return string|false
	 */
	public function get_block_hash( int $block_index = 0 ) {
		return $this->get_api_data( $this->get_block_hash_url( $block_index ), $this->get_block_hash_key_name(), __FUNCTION__ );
	}

	/**
	 * Get block height from block explorer api
	 *
	 * @return int|false
	 */
	public function get_block_height() {
		return $this->get_api_data( $this->get_block_height_url(), $this->get_block_height_key_name(), __FUNCTION__ );
	}

	/**
	 * Get txs from block explorer api
	 *
	 * @return array|false
	 */
	public function get_txs() {
		$api_result = $this->get_api_data( $this->get_txs_url(), $this->get_txs_key_name(), __FUNCTION__ );

		if ( false === $api_result || ! $this->validate_api_data_has_keys( $api_result, $this->get_required_txs_keys(), __FUNCTION__ ) ) {
			return false;
		}

		// Add sequences from inputs to an array if this API supports it.
		if ( $this->get_tx_input_sequence_key_name() ) {
			foreach ( $api_result as & $tx ) {
				foreach ( $tx->{$this->get_tx_inputs_key_name()} as $input ) {
					$tx->{$this->get_tx_input_sequence_key_name()}[] = $input->{$this->get_tx_input_sequence_key_name()};
				}
			}
		}

		$filtered_txs_data = $this->filter_txs_from_block_explorer( $api_result );

		// If this block explorer returns non-satoshi value, convert it to satoshi by multiplying by 1e8.
		if ( ! $this->amount_from_api_is_satoshi() ) {
			foreach ( $filtered_txs_data as & $tx ) {
				$tx->amount = (int) round( ( (float) $tx->amount * 1e8 ) );
			}
		}

		// For multi address support add the addresses to array indexes.
		$filtered_addresses_txs_data = array();
		foreach ( $filtered_txs_data as & $tx ) {
			$address = $tx->address;
			unset( $tx->address );
			$filtered_addresses_txs_data[ $address ][] = $tx;
		}

		return $this->format_data_result_array( 'txs', $filtered_addresses_txs_data );
	}

	/**
	 *
	 * Validate api data has required keys and return api data
	 *
	 * @param array    $block_explorer_data Json decoded and formatted block explorer api data result.
	 * @param string[] $required_keys       The required keys in the block explorer api data result.
	 * @param string   $function_name       The function name that called this validation function.
	 *
	 * @return bool
	 */
	protected function validate_api_data_has_keys( $block_explorer_data, array $required_keys, string $function_name ) : bool {
		// Check that all required data in the result is there.
		foreach ( $block_explorer_data as $data ) {
			foreach ( $required_keys as $array_key => $required_key ) {
				if ( ! property_exists( $data, $required_key ) && ! property_exists( $data->$array_key, $required_key ) ) {
					$log_data = sprintf( "$function_name() error: required key $required_key is missing. Data resultset: %s", print_r( $block_explorer_data, true ) );
					CW_AdminMain::cryptowoo_log_data( 0, $function_name, $log_data, 'error' );
					return false;
				}
			}
		}

		return true;
	}

	/**
	 *
	 * Format data result nicely for array dataset.
	 *
	 * @param string $key_name Key name in resultset.
	 * @param mixed  $data     Data in resultset.
	 *
	 * @return array
	 */
	protected function format_data_result_array( $key_name, $data ) : array {
		return array(
			'api_name' => $this->get_api_name(),
			'name'     => $this->get_name(),
			$key_name  => $data,
			'status'   => 'success',
		);
	}

	/**
	 * Do block explorer api request, validate and return result
	 *
	 * @param string $url           Request URL.
	 * @param string $data_key      Key name of the data we want from the result.
	 * @param string $function_name Function name (calling function, for error logging).
	 * @param bool   $is_json       If the data result is supposed to be json or not (if not its string).
	 *
	 * @return array|false
	 */
	protected function get_api_data( string $url, string $data_key, string $function_name, bool $is_json = true ) {
		if ( empty( $url ) ) {
			return false;
		}

		$request = CW_ExchangeRates::processing()->request( $url, $this->is_json(), $this->is_user_agent(), $this->get_timeout(), $this->get_proxy() );

		$block_explorer_data = static::validate_api_result( $request, $data_key, $is_json );

		if ( 'success' !== $block_explorer_data['status'] ) {
			$email_body = "{$this->get_api_name()} api error: {$block_explorer_data['status']} | URL: $url";
			if ( isset( $block_explorer_data['block_explorer_data'] ) ) {
				$email_body .= ' | Response: ' . wp_json_encode( $block_explorer_data['block_explorer_data'] );
			}
			do_action( 'cryptowoo_api_error', $email_body );
			$block_explorer_data = array( $this->get_name() => $block_explorer_data );
			$log_data            = rtrim( sprintf( "get_api_data() error: %s\n", print_r( $block_explorer_data, true ) ) );
			CW_AdminMain::cryptowoo_log_data( 0, $function_name, $log_data, 'error' );

			return false;
		}

		return ! empty( $data_key ) ? $block_explorer_data['block_explorer_data']->$data_key : $block_explorer_data['block_explorer_data'];
	}

	/**
	 *
	 * Validate and return api data
	 *
	 * @param string|WP_Error $request  Return data from block explorer API.
	 * @param string          $data_key Key name of the data we want from the result.
	 * @param bool            $is_json  If the data result is supposed to be json or not (if not its string).
	 *
	 * @return array
	 */
	protected function validate_api_result( $request, $data_key, bool $is_json ) : array {
		if ( $request instanceof WP_Error ) {
			$error_code = $request->get_error_code();
			$error_msg  = $request->get_error_message();
			return array( 'status' => "$error_code|$error_msg|Block Explorer result not found" );
		}

		if ( $is_json ) {
			$block_explorer_data = json_decode( $request );
		} else {
			$block_explorer_data = $request;
		}

		// If json failed to decode we will return immediately error. Otherwise continue validating.
		if ( $is_json && ( null === $block_explorer_data ) ) {
			return array(
				'status'              => 'Could not decode json|Block Explorer result not found',
				'block_explorer_data' => $request,
			);
		}

		$block_explorer_data = $this->format_result_from_block_explorer( $block_explorer_data );
		if ( isset( $block_explorer_data->{$this->get_txs_key_name()} ) ) {
			$block_explorer_data->{$this->get_txs_key_name()} = $this->format_txs_result_from_block_explorer( $block_explorer_data->{$this->get_txs_key_name()} );
		}

		if ( ( ! is_array( $block_explorer_data ) && ! $block_explorer_data ) || ( ! empty( $data_key ) && ! isset( $block_explorer_data->{$data_key} ) ) ) {
			return array(
				'status'              => 'Block Explorer result not found',
				'block_explorer_data' => $block_explorer_data,
			);
		}

		return array(
			'status'              => 'success',
			'block_explorer_data' => $block_explorer_data,
		);
	}

	/**
	 *
	 * Override base URL (custom URL).
	 * Set before the custom base URL has been stored in CryptoWoo options or to override the url.
	 *
	 * @param string $custom_api_url The custom URL to use for order processing.
	 *
	 * @return $this
	 */
	public function override_base_url( $custom_api_url ) {
		$this->custom_base_url = $custom_api_url;

		return $this;
	}
}
