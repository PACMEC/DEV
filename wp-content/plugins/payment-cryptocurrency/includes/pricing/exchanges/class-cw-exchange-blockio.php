<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * BlockIO Exchange Rates Class
 *
 * @category   CryptoWoo
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoWoo AS
 * Author URI: https://cryptowoo.com
 */
class CW_Exchange_BlockIO extends CW_Exchange_Base {


	/**
	 *
	 * Get the exchange name in nice format.
	 *
	 * @return string
	 */
	public function get_exchange_nicename() : string {
		return 'block.io';
	}

	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		return "https://block.io/api/v2/get_current_price/?api_key={$this->get_api_key()}&price_base={$this->get_base_currency_name()}";
	}

	/**
	 *
	 * Get the exchange price index (last index)
	 *
	 * @return string
	 */
	protected function get_exchange_price_index() : string {
		return 'price';
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_pair_format() : string {
		return '%2$s%1$s';
	}

	/**
	 * Get exchange rates, cross-calculate fiat calculate Altcoin/Fiat values via BTC/Fiat
	 *
	 * @return mixed
	 */
	public function get_coin_price() {
		if ( ! $this->get_api_key() ) {
			return false;
		}

		return parent::get_coin_price();
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
		if ( isset( $price_data->data->prices['0'] ) && $price_data->data->prices['0'] instanceof stdClass ) {
			// TODO: Settings for the merchant to select which rate to get (low / mid / avg / high).
			$lowest_rate_value = null;
			$lowest_rate_index = 0;
			foreach ( $price_data->data->prices as $index => $price_data_object ) {
				if ( ! $lowest_rate_value || $lowest_rate_value > $price_data_object->price ) {
					$lowest_rate_index = $index;
					$lowest_rate_value = $price_data_object->price;
				}
			}

			// The lowest rate in the list is the one we want to use to for the safety of the merchant.
			$formatted_price_data = $price_data->data->prices[ $lowest_rate_index ];

			if ( isset( $formatted_price_data->time ) ) {
				$formatted_price_data->timestamp = $formatted_price_data->time;
			}

			return $formatted_price_data;
		}

		return $price_data;
	}

	/**
	 *
	 * Get Block.io API key from CryptoWoo options.
	 *
	 * @return string|false
	 */
	private function get_api_key() {
		return CW_AdminMain::get_blockio_api_key( $this->get_currency_name(), true );
	}
}
