<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly
/**
 * WooCommerce template modifications and their processing functions
 *
 * @category   CryptoWoo
 * @package    CryptoWoo
 * @subpackage Address
 */
class CW_Formatting {


	/**
	 *
	 * TODO: Coin setting for base units value (8) ?
	 * Calculate BTC, DOGE, and LTC amounts from their lowest divisible unit and format them according to WooCommerce settings
	 *
	 * @param  $amount
	 * @param  bool|true  $divide
	 * @param  int        $decimals
	 * @param  bool|false $easy_copy
	 * @param  bool       $is_qr
	 * @return string
	 */
	static function fbits( $amount, $divide = true, $decimals = 8, $easy_copy = false, $is_qr = false ) {

		$decimal_separator  = $easy_copy || $is_qr ? '.' : wc_get_price_decimal_separator();
		$thousand_separator = wc_get_price_thousand_separator();
		$float_amount       = $divide && (float) $amount > 0 ? (float) ( $amount / 1e8 ) : (float) $amount;

		if ( $decimal_separator === '.' ) {
			if ( $easy_copy || $is_qr ) {
				$string = sprintf( "%{$decimal_separator}{$decimals}f", $float_amount );
			} else {
				$string = number_format( sprintf( "%{$decimal_separator}{$decimals}f", $float_amount ), $decimals, $decimal_separator, $thousand_separator );
			}
		} else {
			$string_result = $easy_copy ? rtrim( number_format( floatval( $float_amount ), $decimals, $decimal_separator, '' ), '0' ) : rtrim( number_format( floatval( $float_amount ), $decimals, $decimal_separator, $thousand_separator ), '0' );

			if ( strcmp( substr( (string) $string_result, -1 ), $decimal_separator ) === 0 ) {
				// The last character is the decimal seperator - add 2 zeros
				$string = str_replace( $decimal_separator, $decimal_separator . '00', $string_result );
			} else {
				$string = $string_result;
			}
		}

		// Strip trailing zeros, source: https://stackoverflow.com/a/12944919
		$string = rtrim( ( strpos( $string, '.' ) !== false ? rtrim( $string, '0' ) : $string ), '.' );

		return $string;
	}

	/**
	 * Return link to address in block explorer, default to explorer that belongs to processing API
	 *
	 * Blockcypher:     BTC, DOGE, LTC, BTCTEST
	 * SoChain:         BTC, DOGE, LTC, BTCTEST, DOGETEST
	 * Blockr.io:       BTC, LTC, BTCTEST
	 * Smartbit.com.au: BTC, BTCTEST
	 * Blocktrail.com:  BTC, BTCTEST
	 * Blockchair.com:  BTC, ETH, XRP, BCH, LTC, BSV, Dash, Doge, Groestlcoin
	 * Blockstream.info (incl. Onion v2 and v3):  BTC, BTCTEST
	 *
	 * @param  string $currency Currency code (eg BTC).
	 * @param  string $address  Blockchain address.
	 * @param  mixed  $options  Deprecated TODO: remove when no more usage.
	 * @param  bool   $format   If to format the address as URL.
	 * @return string
	 */
	static function link_to_address( $currency, $address, $options = false, $format = false ) {

		$v = new CW_Validate();
		if ( ! $v->offline_validate_address( $address, $currency ) ) {
			return esc_html( $address );
		}

		$lc_currency = strtolower( $currency );
		if ( $currency === 'BLK' ) {
			$url = "https://chainz.cryptoid.info/blk/search.dws?q={$address}";
			return $format ? sprintf( '<a href="%s" title="%s" target="_blank">%s</a>', $url, esc_html__( 'View address in block explorer', 'cryptowoo' ), $address ) : $url;
			// return "https://bitinfocharts.com/blackcoin/address/{$address}";
		}

		$testnet = strpos( $lc_currency, 'test' );

		// Prepare block chain API identifier
		$stripped = $testnet ? str_replace( 'test', '', $lc_currency ) : $lc_currency;

		// Check if we have a block explorer for this currency
		$to_use = CW_OrderProcessing::block_explorer_tools()->get_preferred_block_explorer_link( $stripped );

		// Set to "none" if we don't have an address
		$to_use = strlen( $address ) < 5 ? 'none' : $to_use;
		switch ( $to_use ) {
			case 'none':
				$url = '#';
				break;
			case 'smartbit':
				$network = $testnet ? 'sandbox' : 'www';
				$url     = "https://{$network}.smartbit.com.au/address/{$address}";
				break;
			case 'blockcypher':
				$network = $currency === 'BTCTEST' ? 'btc-testnet' : $lc_currency;
				$url     = "https://live.blockcypher.com/{$network}/address/{$address}";
				break;
			case 'blockr_io':
				$network = $testnet ? 'tbtc.' : '';
				$url     = "https://{$network}blockr.io/address/info/{$address}";
				break;
			case 'blocktrail':
				$network = $currency === 'BTCTEST' ? 'tBTC' : $currency;
				$url     = "https://www.blocktrail.com/{$network}/address/{$address}";
				break;
			case 'esplora_blockstream':
				if ( $currency === 'BTCTEST' ) {
					$url = "https://blockstream.info/testnet/address/{$address}";
				} else {
					$url = "https://blockstream.info/address/{$address}";
				}
				break;
			case 'blockstream_info_onion_v2':
				if ( $currency === 'BTCTEST' ) {
					$url = "http://explorernuoc63nb.onion/testnet/address/{$address}";
				} else {
					$url = "http://explorernuoc63nb.onion/address/{$address}";
				}
				break;
			case 'blockstream_info_onion_v3':
				if ( $currency === 'BTCTEST' ) {
					$url = "http://explorerzydxu5ecjrkwceayqybizmpjjznk5izmitf2modhcusuqlid.onion/testnet/address/{$address}";
				} else {
					$url = "http://explorerzydxu5ecjrkwceayqybizmpjjznk5izmitf2modhcusuqlid.onion/address/{$address}";
				}
				break;
			case 'chain_so':
				$url = "https://sochain.com/address/{$lc_currency}/{$address}";
				break;
			case 'custom':
				$url = preg_replace( '/{{ADDRESS}}/', $address, cw_get_option( "custom_block_explorer_{$stripped}" ) );
				break;
			default:
			case 'blockchair':
				if ( $currency === 'BTCTEST' ) {
					$url = "https://blockstream.info/testnet/address/{$address}";
				} else {
					// Get currency nicenames
					$wc_currencies = cw_get_woocommerce_currencies();
					// Prepare network slug
					$network = isset( $wc_currencies[ $currency ] ) ? str_replace( ' ', '-', strtolower( $wc_currencies[ $currency ] ) ) : $currency;
					$url     = "https://blockchair.com/{$network}/address/{$address}?from=cryptowoo";
				}
				break;
		}
		$url = apply_filters( 'cw_link_to_address', $url, $address, $currency, cw_get_options() );
		// Fail on invalid URL (display address only without url)
		if ( ! wp_http_validate_url( $url ) ) {
			$url    = $address;
			$format = false;
		}
		return $format ? sprintf( '<a href="%s" title="%s" target="_blank">%s</a>', $url, esc_html__( 'View address in block explorer', 'cryptowoo' ), $address ) : $url;
	}

	/**
	 * Format Insight API URL, force trailing slash, defaults to genesis block
	 *
	 * @param  $value
	 * @param  $endpoint
	 * @return mixed
	 */
	static function format_insight_api_url( $value, $endpoint = 'block-index/0' ) {
		$url = parse_url( $value );
		if ( isset( $url['path'] ) ) {
			$has_slash         = strpos( substr( $url['path'], -1 ), '/' );
			$url['fixed_path'] = $has_slash === 0 ? $url['path'] : sprintf( '%s/', $url['path'] );
			$url['path']       = $has_slash === 0 ? sprintf( '%s%s', $url['path'], $endpoint ) : sprintf( '%s/%s', $url['path'], $endpoint );
		} else {
			$url['path'] = $url['fixed_path'] = '/';
		}
		$urls['surl']      = isset( $url['port'] ) ? sprintf( '%s://%s:%s%s', $url['scheme'], $url['host'], $url['port'], $url['path'] ) : sprintf( '%s://%s%s', $url['scheme'], $url['host'], $url['path'] );
		$urls['fixed_url'] = isset( $url['port'] ) ? sprintf( '%s://%s:%s%s', $url['scheme'], $url['host'], $url['port'], $url['fixed_path'] ) : sprintf( '%s://%s%s', $url['scheme'], $url['host'], $url['fixed_path'] );
		return $urls;
	}

	/**
	 * Exchange rate table for single product and checkout page
	 *
	 * @param float  $price                 Price in store currency
	 * @param string $selected_currency     The pre-selected currency on the checkout page
	 * @param string $currency_select_style Styling on checkout page
	 *
	 * @return string
	 */
	static function cryptowoo_crypto_rates_list( $price, $selected_currency = '', $currency_select_style = 'dropdown' ) {

		$dbrates = CW_ExchangeRates::processing()->get_all_exchange_rates();
		$rates   = $norates = array();
		if ( empty( $dbrates ) ) {
			return 'No exchange rates found in database!'; // TODO fail gracefully
		}

		$enabled_currencies = cw_get_enabled_currencies( true, true );
		$active_currency    = cw_get_woocommerce_currency();// cw_get_woocommerce_default_currency();

		// Create currency => price and last_update array from enabled currencies
		foreach ( $enabled_currencies as $key => $value ) {
			if ( $key === $active_currency ) {
				$rates[ $key ]       = 1;
				$exchange[ $key ]    = '';
				$last_update[ $key ] = time();
			} else {
				$skey = $key . $active_currency;
				$skey = str_replace( array( '-lightning', 'TEST' ), '', $skey ); // Lightning payments and testnet payments have the same exchange rate as their on-chain currency
				if ( ! isset( $dbrates[ $skey ]['exchange_rate'] ) ) {
					// Collect currencies that are enabled but have no rates in the DB
					$norates[ $key ] = $key;
				} else {
					$rates[ $key ]       = $dbrates[ $skey ]['exchange_rate'];
					$exchange[ $key ]    = isset( $dbrates[ $skey ]['exchange'] ) ? $dbrates[ $skey ]['exchange'] : '';
					$last_update[ $key ] = isset( $dbrates[ $skey ]['exchange'] ) ? $dbrates[ $skey ]['last_update'] : '';
				}
			}
		}

		$message = '';
		if ( count( $norates ) ) {
			// Make currency unavailable on checkout if there are no rates. Keep for 30 seconds.
			set_transient( 'cryptowoo_norates', $norates, 30 ); // TODO tweak transient time
			if ( ! count( $rates ) ) {
				$message = esc_html__( 'Exchange rates not found in database. Please refresh this page.', 'cryptowoo' );
			}
		} else {
			// Clear transient if we have rates
			delete_transient( 'cryptowoo_norates' );
		}
		$currencytable = sprintf( '%s<div class="cw-row crypto-price-table">', $message );
		$i             = 0;

		// Don't display exchange names if one currency has a multiplier enabled
		$using_multiplier = self::is_multiplier_enabled( $enabled_currencies );

		// Maybe override active currency for WooCommerce Multi Currency
		if ( class_exists( 'WOOMULTI_CURRENCY_Data' ) ) {
			$setting         = WOOMULTI_CURRENCY_Data::get_ins();
			$active_currency = $setting->get_current_currency();
		}

		// Maybe override active currency for WooCommerce Multilingual (WCML)
		if ( class_exists( 'WCML_Multi_Currency_Prices' ) ) {
			$active_currency = apply_filters( 'wcml_price_currency', null );
		}

		// Use dropdown or checkout page theme
		$currency_selector_class = 'buttons' === $currency_select_style ? ' cw-coinbtn' : '';

		foreach ( $rates as $coin_type => $rate ) {

			$add_classes = $currency_selector_class;

			if ( CW_ExchangeRates::tools()->currency_is_fiat( $active_currency ) && cw_get_woocommerce_default_currency() !== $active_currency ) {
				// Aelia currency switcher: Get the exchange rate in the active currency if it is a fiat currency
				$rate = apply_filters( 'wc_aelia_cs_convert', $rate, cw_get_woocommerce_default_currency(), $active_currency, 8 );

				// WooCommerce Multilingual (WCML)
				$rate = apply_filters( 'wcml_raw_price_amount', $rate, $active_currency );
			}

			$i++;
			$multiplier_key = sprintf( 'multiplier_%s', strtolower( $coin_type ) );
			$multiplier     = cw_get_option( $multiplier_key ) ?: 1;

			$t_stamp = strtotime( $last_update[ $coin_type ] );

			$crypto_symbol = self::get_coin_icon( $coin_type, 'large' );

			// TODO: Decimals are not 8 for all cryptos!
			$value        = max( 1, ( $price * $multiplier / $rate ) * 1e8 ); // Ensure price is minimum 1 satoshi.
			$display_rate = ( $price / ( $price * $multiplier / $rate ) ) * 1e8;

			$dec_places      = self::calculate_coin_decimals( $coin_type, $value );
			$fiat_dec_places = self::calculate_fiat_decimals( $display_rate );

			// Column count
			$col_count = cw_get_option( 'estimation_col_count' ) ?: 3;

			if ( is_product() ) {

				global $product;
				if ( $product->is_type( 'variable' ) ) {

					// This is a variable product - display from-to prices
					$variations       = $product->get_available_variations();
					$variation_prices = wp_list_pluck( $variations, 'display_price' );

					$value_min = max( 1, ( min( $variation_prices ) * $multiplier / $rate ) * 1e8 ); // Ensure price is minimum 1 satoshi.
					$value_max = max( 1, ( max( $variation_prices ) * $multiplier / $rate ) * 1e8 ); // Ensure price is minimum 1 satoshi.

					$price_min = self::fbits( $value_min, true, $dec_places, false );
					$price_max = self::fbits( $value_max, true, $dec_places, false );

					// small fonts and no date if on single product pages
					$fontsize = '75%'; // TODO create styling settings for currency table
					if ( cw_get_option( 'display_fiat_rate' ) ) {
						$td             = '<div class="cw-col-%1$d" id="price-%2$s"><span style="font-size:%3$s;" class="priceinfo cw-noselect">%4$s %5$s - %6$s</span><br><span style="font-size:%3$s;" class="exchangeinfo cw-noselect">%7$s %8$s/%2$s';
						$currencytable .= sprintf( $td, $col_count + 1, esc_attr( $coin_type ), $fontsize, $crypto_symbol, $price_min, $price_max, self::fbits( $display_rate, true, $fiat_dec_places ), esc_html( $active_currency ) );
					} else {
						$td             = '<div class="cw-col-%1$d" id="price-%2$s"><span style="font-size:%3$s;" class="priceinfo cw-noselect">%4$s %5$s - %6$s';
						$currencytable .= sprintf( $td, $col_count + 1, esc_attr( $coin_type ), $fontsize, $crypto_symbol, $price_min, $price_max );
					}
				} else {

					// small fonts and no date if on single product pages
					$fontsize = '75%'; // TODO create styling settings for currency table
					if ( cw_get_option( 'display_fiat_rate' ) ) {
						$td             = '<div class="cw-col-%1$d" id="price-%2$s"><span style="font-size:%3$s;" class="priceinfo cw-noselect">%4$s %5$s</span><br><span style="font-size:%3$s;" class="exchangeinfo cw-noselect">%6$s %7$s/%2$s';
						$currencytable .= sprintf( $td, $col_count, esc_attr( $coin_type ), $fontsize, $crypto_symbol, self::fbits( $value, true, $dec_places, false ), self::fbits( $display_rate, true, $fiat_dec_places ), esc_html( $active_currency ) );
					} else {
						$td             = '<div class="cw-col-%1$d" id="price-%2$s"><span style="font-size:%3$s;" class="priceinfo cw-noselect">%4$s %5$s';
						$currencytable .= sprintf( $td, $col_count, esc_attr( $coin_type ), $fontsize, $crypto_symbol, self::fbits( $value, true, $dec_places ) );
					}
				}
			} else {
				// Maybe add multiplier discount info
				if ( $multiplier < 1 && cw_get_option( 'show_discounted_rate' ) ) {
					$multiplier_discount_html = sprintf( __( '%1$s(discounted %2$s%3$s)%4$s', 'cryptowoo' ), '<span class="discountinfo cw-noselect">', number_format( ( 1 - $multiplier ) * 100, 0 ), '%', '</span>' );
				} else {
					$multiplier_discount_html = '';
				}

				if ( $selected_currency === $coin_type ) {
					$add_classes .= ' selected_currency';
				}

				$formatted_price = self::fbits( $value, true, $dec_places, false );
				if ( ! cw_get_option( 'display_order_total_estimation' ) ) {
					$formatted_price  = $enabled_currencies[ $coin_type ];
					$coin_type_append = '';
					$col_count        = 6;
				} else {
					$coin_type_append = ' ' . $coin_type;
				}

				if ( cw_get_option( 'display_fiat_rate' ) ) {
					// Show more details if we are not on a product page
					$td             = '<div class="cw-col-%1$d%2$s" id="price-%3$s"><span class="priceinfo cw-noselect">%4$s %5$s</span><br><span class="exchangeinfo cw-noselect">%6$s %7$s/%3$s %8$s';
					$currencytable .= sprintf( $td, $col_count, $add_classes, esc_attr( $coin_type ), $crypto_symbol, $formatted_price, self::fbits( $display_rate, true, $fiat_dec_places ), esc_html( $active_currency ), $multiplier_discount_html );
				} else {
					$col_count        = 6;
					$formatted_price .= $coin_type_append;
					$td               = '<div class="cw-col-%1$d%2$s" id="price-%3$s"><span class="priceinfo cw-noselect">%4$s %5$s</span><span class="exchangeinfo cw-noselect">';
					$currencytable   .= sprintf( $td, $col_count, $add_classes, esc_attr( $coin_type ), $crypto_symbol, $formatted_price );
				}
			}
			$currencytable .= ! $using_multiplier && cw_get_option( 'display_rate_source' ) ? sprintf( '<br>%s </span><span class="cw-rate-ts">%s</span></div>', CW_ExchangeRates::tools()->get_exchange_nicename( $exchange[ $coin_type ] ), date( 'H:i:s', $t_stamp ) ) : '</span></div>';

		}
		$currencytable .= '</div>';

		// Maybe enqueue payment currency select button JavaScript
		if ( 'buttons' === $currency_select_style ) {
			cw_enqueue_script( 'cw_checkout' );
		}
		return $currencytable;
	}

	/**
	 * Check if the multiplier is enabled for one of the currencies
	 *
	 * @param $currencies
	 *
	 * @return bool
	 */
	static function is_multiplier_enabled( $currencies ) {
		foreach ( $currencies as $currency ) {
			$multiplier_option_key = sprintf( 'multiplier_%s', strtolower( $currency ) );
			$multiplier            = cw_get_option( $multiplier_option_key );

			if ( false !== $multiplier && $multiplier != 1 ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Count the number of decimals $crypto_amount will have after formatting
	 *
	 * @param int $crypto_amount The cryptocurrency amount as "satoshi" integer
	 *
	 * @return int The number of decimals
	 */
	static function count_coin_decimals( $crypto_amount ) {
		$trimmed    = rtrim( (string) $crypto_amount, '0' );
		$zero_count = strlen( (string) $crypto_amount ) - strlen( $trimmed );
		$decimals   = 8 - $zero_count;

		return max( 0, $decimals );
	}

	/**
	 * Calculate minimum required decimals for coin values
	 *
	 * @param string $coin_type Coin type short name (e.g. BTC).
	 * @param float  $value     Crypto amount (satoshi precision).
	 *
	 * @return int
	 */
	static function calculate_coin_decimals( $coin_type, $value ) {
		$minimum_positive_decimals = cw_get_option( "decimals_$coin_type" ) ?: 8;

		return self::calculate_value_decimals( $value, $minimum_positive_decimals );
	}

	/**
	 * Calculate minimum required decimals for fiat values
	 *
	 * @param float $value
	 * @param int   $decimals
	 *
	 * @return int
	 */
	static function calculate_fiat_decimals( $value, $decimals = 2 ) {
		return self::calculate_value_decimals( $value, (int) $decimals );
	}

	/**
	 * Calculate minimum required decimals
	 *
	 * @param float $value                           Value to calculate decimals for.
	 * @param int   $minimum_positive_decimal_digits How many decimal digits must be positive.
	 *
	 * @return int
	 */
	static function calculate_value_decimals( $value, $minimum_positive_decimal_digits ) {
		$maximum_decimals  = 14; // Do not use a higher number than 14 here because float precision in php is 14!
		$decimal_separator = wc_get_price_decimal_separator();

		// TODO: Use the real max decimals for the coin type, not all are 8 decimals max!
		$max_crypto_decimals = 8;

		// Make sure we set at least 1 positive decimal digit in case the option value is missing
		$minimum_positive_decimal_digits = $minimum_positive_decimal_digits ? (int) $minimum_positive_decimal_digits : 1;

		// Format real value.
		$value_f                          = $value / 1e8;
		1 > $value_f ?: $maximum_decimals = $minimum_positive_decimal_digits; // Limit max decimals if the value is positive (more than 0 before decimal seperator).
		$value_real                       = number_format( $value_f, $maximum_decimals, $decimal_separator, '' );

		// Remove insignificant zeros and decimal seperator.
		false === strpos( $value_real, $decimal_separator ) ?: $value_real = rtrim( $value_real, "0$decimal_separator" );

		// If there is no decimal point, there are no decimals.
		$dec_pointer_pos = strpos( $value_real, $decimal_separator );
		if ( false === $dec_pointer_pos ) {
			return 0;
		}

		// Count how many decimals we have now.
		$value_real_decimals = strlen( substr( strrchr( $value_real, $decimal_separator ), 1 ) );

		// Find how many leading 0 in decimals before a positive digit.
		$value_real_leading_zeros = strspn( $value_real, '0', $dec_pointer_pos + 1 );

		// Calculate how many decimals to use.
		$calculated_decimals = $value_real_leading_zeros + $minimum_positive_decimal_digits;

		// Decimals are minimum positive digits not including insignificant zeros and not more than max crypto decimals.
		return min( $value_real_decimals, $calculated_decimals, $max_crypto_decimals );
	}

	/**
	 * Add cryptocurrency price list to single product pages
	 * Triggered in display settings: add_action('woocommerce_single_product_summary', 'CW_Formatting::display_crypto_price', 15);
	 */
	static function display_crypto_price() {
		global $product;
		if ( $price = $product->get_price() ) {
			echo wp_kses_post( self::cryptowoo_crypto_rates_list( $price ) );
		}
	}

	/**
	 * Display currency settings hook
	 */
	static function display_currency_settings() {
		// Cryptocurrency price table on single product pages
		if ( cw_get_option( 'cryptowoo_currency_table_on_single_products' ) ) {
			// Add cryptocurrency price list to short description on single product pages
			add_action( 'woocommerce_single_product_summary', 'CW_Formatting::display_crypto_price', 15 );
		}
	}

	/**
	 * Add the field for the payment currency to the checkout page
	 *
	 * @param string $description The payment method description.
	 **/
	static function cryptowoo_payment_currency_checkout_field( $description ) {
		// Check if the gateway is enabled
		if ( ! cw_get_option( 'enabled' ) ) {
			return;
		}

		// Maybe set default currency if woocs currency select is a cryptocurrency
		if ( cw_get_option( 'woocs_auto_default_payment_currency' ) ) {
			$cw_current_currency = cw_get_woocommerce_currency();
			if ( CW_ExchangeRates::tools()->currency_is_crypto( $cw_current_currency ) ) {
				cw_update_option( 'default_payment_currency', $cw_current_currency );
			}
		}

		printf( '<div id="%s_checkout_field">', esc_attr( CW_PAYMENT_METHOD_ID ) );

		// Get enabled currencies that have exchange rates
		$currencies = cw_get_enabled_currencies();

		// Maybe use preselected payment currency
		$preselected              = array();
		$default_payment_currency = '';
		if ( count( $currencies ) > 1 ) {
			if ( ( cw_get_option( 'default_payment_currency' ) === 'disabled' || $currencies === array() || ! isset( $currencies[ cw_get_option( 'default_payment_currency' ) ] ) ) ) {
				$preselected['please_choose'] = esc_html__( 'Please select a currency', 'cryptowoo' );
			} else {
				$default_payment_currency                 = cw_get_option( 'default_payment_currency' );
				$preselected[ $default_payment_currency ] = $currencies[ $default_payment_currency ];
			}
			$hide = '';
		} else {
			$hide = ' cw-hidden';
		}

		$currencies = array_merge( $preselected, $currencies );

		// Display pricing
		$show_prices = cw_get_option( 'display_order_total_estimation' );

		// Maybe use buttons instead of dropdown field
		$currency_select_style = cw_get_option( 'currency_select_style' ) ?: 'dropdown';
		if ( 'buttons' === $currency_select_style ) {
			$hide = ' cw-hidden';
		}
		woocommerce_form_field(
			'cw_payment_currency',
			array(
				'type'     => 'select',
				'class'    => array( sprintf( 'payment-currency-select form-row-full%s', $hide ) ),
				'label'    => esc_html__( 'Payment Currency', 'cryptowoo' ),
				'required' => true,
				'options'  => $currencies,
			)
		);

		if ( isset( $description ) ) {
			printf( '<div id="cw_gateway_description" class="payment-method-description">%s</div>', do_shortcode( $description ) );
		}

		$total_amount = WC()->cart->total;

		// Maybe display cryptocurrency prices
		if ( $total_amount > 0 ) {
			if ( $show_prices || 'buttons' === $currency_select_style ) {
				   // Do not highlight selected currency when using the dropdown
				$default_payment_currency = 'buttons' === $currency_select_style ? $default_payment_currency : '';
				echo wp_kses_post( sprintf( '<div id="estd_order_total_checkout"><p>%s</p></div>', self::cryptowoo_crypto_rates_list( $total_amount, $default_payment_currency, $currency_select_style ) ) );
			}
		}

		// Maybe add refund address field
		if ( 'disabled' !== cw_get_option( 'collect_refund_address' ) ) {
			$required = cw_get_option( 'collect_refund_address' ) === 'required' ? true : false;
			woocommerce_form_field(
				'refund_address',
				array(
					'type'        => 'text',
					'class'       => array( 'refund-address form-row-full' ),
					'label'       => esc_html__( 'Refund Address', 'cryptowoo' ),
					'required'    => $required,
					'placeholder' => '',
					'clear'       => true,
				)
			);
		}
		if ( cw_get_option( 'display_checkout_branding' ) ) {
			$cryptowoo_logo = self::get_coin_icon( 'cryptowoo', 'small' );
			echo wp_kses_post( sprintf( '<p class="form-row form-row-full"><div class="cw-branding cryptowoo-smalltext"><a href="%s" class="about-cryptowoo" target="_blank" title="%s">%s</a></div></p>', esc_url( 'https://www.cryptowoo.com/' ), esc_html__( 'powered by CryptoWoo', 'cryptowoo' ), $cryptowoo_logo ) );
		}
		echo '</div>';
	}

	/**
	 * Process the checkout
	 */
	static function payment_currency_checkout_validation() {
		// Check if set, if its not set add an error.
		if ( isset( $_POST['cw_payment_currency'] ) && $_POST['payment_method'] === CW_PAYMENT_METHOD_ID ) {
			if ( $_POST['cw_payment_currency'] === 'please_choose' ) {
				wc_add_notice( esc_html__( 'Please select your payment currency.', 'cryptowoo' ), 'error' );
			}
			// Is an address required or filled out?
			if ( isset( $_POST['refund_address'] ) ) {
				$validate = new CW_Validate();
				if ( empty( $_POST['refund_address'] ) && cw_get_option( 'collect_refund_address' ) === 'required' ) {
					wc_add_notice( esc_html__( 'Please enter a refund address.', 'cryptowoo' ), 'error' );
				} elseif ( ! empty( $_POST['refund_address'] ) && ! $validate->offline_validate_address( sanitize_text_field( wp_unslash( $_POST['refund_address'] ) ), sanitize_text_field( wp_unslash( $_POST['cw_payment_currency'] ) ) ) ) {
					wc_add_notice( esc_html__( 'Your refund address seems to be invalid. Make sure you copy and paste the whole address.', 'cryptowoo' ), 'error' );
				}
			}
		}
	}


	/**
	 * Force store currency as order currency when processing order with cryptocurrency selected
	 */
	static function payment_currency_checkout_maybe_force_store_currency() {
		if ( ! isset( $_POST['payment_method'] ) || CW_PAYMENT_METHOD_ID !== $_POST['payment_method'] ) {
			return;
		}

		if ( ! cw_get_option( 'woocs_force_checkout_in_store_currency' ) ) {
			return;
		}

		global $WOOCS;
		if ( ! isset( $WOOCS ) || CW_ExchangeRates::tools()->currency_is_fiat( $WOOCS->current_currency ) ) {
			return;
		}

		// Store currency to session so we can reset to it after creating order
		$_SESSION['cw_current_currency'] = $WOOCS->current_currency;

		$WOOCS->current_currency = cw_get_woocommerce_default_currency();
		$WOOCS->storage->set_val( 'woocs_current_currency', $WOOCS->current_currency );
	}


	/**
	 * Reset store currency after processing order
	 */
	static function payment_currency_checkout_maybe_reset_store_currency() {
		global $WOOCS;
		if ( isset( $_SESSION['cw_current_currency'] ) && isset( $WOOCS ) ) {
			$WOOCS->current_currency = sanitize_text_field( $_SESSION['cw_current_currency'] );
			$WOOCS->storage->set_val( 'woocs_current_currency', $WOOCS->current_currency );
		}
	}

	/**
	 * Update the order meta payment_currency and refund_address
	 *
	 * @param int $order_id
	 */
	static function cryptowoo_payment_currency_update_order_meta( $order_id ) {
		// check if $_POST has our custom fields and if payment with digital currencies has been selected.
		if ( isset( $_POST['cw_payment_currency'] ) ) {
			$cw_woocommerce_database = CW_Database_Woocommerce::instance( $order_id );
			if ( $_POST['cw_payment_currency'] === 'please_choose' || $_POST['payment_method'] !== CW_PAYMENT_METHOD_ID ) {
				$cw_woocommerce_database->set_payment_currency( cw_get_woocommerce_currency() );
			} else {
				$cw_woocommerce_database->set_payment_currency( sanitize_text_field( wp_unslash( $_POST['cw_payment_currency'] ) ) );
			}
			if ( isset( $_POST['refund_address'] ) ) {
				$cw_woocommerce_database->set_refund_address( sanitize_text_field( wp_unslash( $_POST['refund_address'] ) ) );
			}
			// Update WooCommerce order details.
			$cw_woocommerce_database->update();
		}
	}

	/**
	 * Display cryptocurrency payment info on order received page
	 * Doesn't differentiate between unconfirmed and confirmed transactions
	 *
	 * @param  $id
	 * @return bool
	 */
	static function display_crypto_payment_info( $id ) {

		// WooCommerce order
		$order = wc_get_order( $id );
		if ( ! $order || ! ( CW_PAYMENT_METHOD_ID === $order->get_payment_method() ) ) {
			return;
		}
		$data = CW_Database_CryptoWoo::get_payment_details_by_order_id( $id );

		$amount_due = 0;
		if ( $data ) {

			// Use data in temp table
			$amount_due       = $data->get_crypto_amount_due();
			$payment_currency = $data->get_payment_currency();
			$payment_address  = $data->get_address();
			$timeout          = (int) $data->get_timeout();

			$paid               = (bool) $data->get_is_paid();
			$received_confirmed = $data->get_received_confirmed();
			$amount_unconfirmed = $data->get_received_unconfirmed();

			// Calculate missing amount
			$amount_missing = ( $received_confirmed + $amount_unconfirmed ) > 0 ? $amount_due - $received_confirmed - $amount_unconfirmed : $amount_due;

		} else {

			// Entry does not exist in temp table, get from WooCommerce order.
			$cwdb_woocommerce = CW_Database_Woocommerce::instance( $order );
			$amount_due       = $cwdb_woocommerce->get_crypto_amount();
			$payment_currency = $cwdb_woocommerce->get_payment_currency();
			$payment_address  = $cwdb_woocommerce->get_payment_address();
			$tx_confirmed     = $cwdb_woocommerce->get_tx_confirmed();
			$timeout          = strpos( $tx_confirmed, 'timeout' ) ? 1 : 0;

			$received_confirmed = $cwdb_woocommerce->get_received_confirmed();
			$amount_unconfirmed = $cwdb_woocommerce->get_received_unconfirmed();

			// Calculate missing amount and check payment status
			$amount_missing = ( $received_confirmed + $amount_unconfirmed ) > 0 ? ( $amount_due - $received_confirmed - $amount_unconfirmed ) : $amount_due;

		}

		$percentage_paid = $amount_due > 0 ? ( ( $received_confirmed + $amount_unconfirmed ) / $amount_due ) * 100 : 0;
		$paid            = $percentage_paid >= (float) cw_get_option( 'underpayment_notice_range' )[2];

		if ( ! $order->is_paid() ) {
			if ( $timeout === 4 ) {
				$txnreference = esc_html__( 'The price quote for your order has expired. Please click the PAY button below to get a new quote.', 'cryptowoo' );
				wc_print_notice( $txnreference, 'error' );
			} elseif ( $timeout === 1 ) {

				if ( $percentage_paid > 0 ) {
					// Ask customer to contact merchant because if insufficient payment
					CW_Order_Processing::instance( $order->get_id() )->add_customer_notice_insufficient_payment();
				} else {
					// Default order note
					CW_Order_Processing::instance( $order->get_id() )->add_customer_notice_timeout();
				}
				CW_Order_Processing_Tools::instance()->redirect_to_cart();
			}
		}
		if ( $amount_due >= 0 && (bool) $payment_address ) {

			// Get nice currency names
			$wc_currencies = cw_get_woocommerce_currencies();

			echo wp_kses_post( sprintf( esc_html__( '%1$s%2$s Payment Details%3$s', 'cryptowoo' ), '<div class="thankyou-cryptowoo"><h3>', $wc_currencies[ $payment_currency ], '</h3>' ) );
			echo wp_kses_post( sprintf( esc_html__( 'Your order total: %1$s %2$s%3$s', 'cryptowoo' ), self::fbits( $amount_due ), $payment_currency, '<br>' ) );
			echo wp_kses_post( sprintf( esc_html__( 'Amount received: %1$s %2$s%3$s', 'cryptowoo' ), self::fbits( $received_confirmed ), $payment_currency, '<br>' ) );

			// Maybe display unconfirmed amount
			if ( $amount_unconfirmed > 0 ) {
				echo wp_kses_post( sprintf( esc_html__( 'Amount unconfirmed: %1$s %2$s%3$s', 'cryptowoo' ), self::fbits( $amount_unconfirmed ), $payment_currency, '<br>' ) );
			}

			// Link to payment address on block chain
			$url = self::link_to_address( $payment_currency, $payment_address );

			if ( $url ) {
				echo wp_kses_post( sprintf( '%s: <a title="%s" target="_blank" href="%s">%s</a><br>', esc_html__( 'Payment address', 'cryptowoo' ), sprintf( esc_html__( 'Payment address on the %s blockchain', 'cryptowoo' ), $wc_currencies[ $payment_currency ] ), $url, $payment_address ) );
			} else {
				echo wp_kses_post( sprintf( '%s: %s<br>', esc_html__( 'Payment address', 'cryptowoo' ), $payment_address ) );
			}

			do_action( "cw_display_extra_details_order_received_$payment_currency", $order );

			// WooCommerce order status
			$on_hold = $order->has_status( 'on-hold' );

			// Amount missing
			if ( $on_hold || ( $amount_missing > ( $amount_due * ( (float) cw_get_option( 'underpayment_notice_range' )[2] / 100 ) ) && $amount_due > 0 && ! $paid ) ) {
				echo wp_kses_post( sprintf( esc_html__( 'Amount missing: %1$s %2$s%3$s', 'cryptowoo' ), self::fbits( $amount_missing ), $payment_currency, '<br>' ) );

				if ( $timeout !== 1 && ! $on_hold && ( $received_confirmed > 0 || $amount_unconfirmed > 0 ) ) {

					// Request customer to send the missing amount
					wc_print_notice( esc_html__( 'Please send the missing amount to the address above. You will receive an email when your payment is fully confirmed.', 'cryptowoo' ), 'notice' );

				} elseif ( $on_hold ) {

					$hold_notice = $on_hold ? esc_html__( ' and has been put on hold. Please contact us.', 'cryptowoo' ) : esc_html__( '. Please try again or contact us if you need assistance.', 'cryptowoo' );
					// Inform customer about timeout and maybe that his order is on hold.
					wc_print_notice( sprintf( esc_html__( 'Your order is expired%s', 'cryptowoo' ), $hold_notice ), 'error' );

				}
			} elseif ( $paid && $amount_due >= 0 && $received_confirmed > 0 && $amount_unconfirmed <= 0 ) {
				// Maybe use custom thank you page text
				$text = cw_get_option( 'thankyou_page_text' ) ? do_shortcode( cw_get_option( 'thankyou_page_text' ) ) : esc_html__( 'Your payment has been received. Thank you for shopping with us!', 'cryptowoo' );

				wc_print_notice( $text, 'success' );

			} elseif ( $amount_unconfirmed > 0 && $timeout !== 1 && $order->has_status( 'pending' ) ) {
				wc_print_notice( esc_html__( 'You will receive an email when your payment is fully confirmed.', 'cryptowoo' ), 'notice' );
			}
			echo '</div>';
		}
	}

	/**
	 * Add cryptocurrency payment info to WooCommerce emails
	 *
	 * @param  $order
	 * @return string
	 */
	static function display_order_email_info( $order, $sent_to_admin, $plain_text, $email ) {

		if ( CW_PAYMENT_METHOD_ID === $order->get_payment_method() ) {

			$data = CW_Database_CryptoWoo::get_payment_details_by_order_id( $order->get_id() );

			if ( $data ) {
				$amount_due         = $data->get_crypto_amount_due();
				$paid               = (int) $data->get_is_paid();
				$timeout            = (int) $data->get_timeout();
				$amount_unconfirmed = $data->get_received_unconfirmed();

				// Prevent showing double amount in case of RBF payments
				// Note: This will be wrong if the customer does indeed send the exact same amount in two transactions
				if ( $data->get_received_confirmed() !== $data->get_received_unconfirmed() ) {
					$full_amount = $data->get_received_confirmed() + $data->get_received_unconfirmed();
				} else {
					$full_amount = $data->get_received_confirmed();
				}
				$payment_currency = $data->get_payment_currency();
				$payment_address  = $data->get_address();
				$amount_missing   = $data->get_crypto_amount_due() - $data->get_received_confirmed() - $data->get_received_unconfirmed();

				// Get currency nicenames
				$wc_currencies = cw_get_woocommerce_currencies();

				$data  = sprintf( esc_html__( '%1$s%2$s Payment Details%3$s', 'cryptowoo' ), '<div class="thankyou-cryptowoo"><h3>', $wc_currencies[ $payment_currency ], '</h3>' );
				$data .= sprintf( esc_html__( 'Your order total: %1$s %2$s%3$s', 'cryptowoo' ), self::fbits( $amount_due ), $payment_currency, '<br>' );
				$data .= sprintf( esc_html__( 'Amount received: %1$s %2$s%3$s', 'cryptowoo' ), self::fbits( $full_amount ), $payment_currency, '<br>' );

				// Maybe display unconfirmed amount
				if ( $amount_unconfirmed > 0 ) {
					$data .= sprintf( esc_html__( 'Unconfirmed: %1$s %2$s %3$s', 'cryptowoo' ), self::fbits( $amount_unconfirmed, true ), $payment_currency, '<br>' );
				}

				// Link to payment address on block chain
				$url   = self::link_to_address( $payment_currency, $payment_address );
				$data .= sprintf( '%s: <a title="%s" target="_blank" href="%s">%s</a><br>', esc_html__( 'Payment address', 'cryptowoo' ), sprintf( esc_html__( 'Payment address on the %s blockchain', 'cryptowoo' ), $wc_currencies[ $payment_currency ] ), $url, $payment_address );

				echo wp_kses_post( $data );
				do_action( "cw_display_extra_details_order_received_$payment_currency", $order );
				$data = '';

				// Amount missing
				if ( $amount_missing > ( $amount_due * ( (float) cw_get_option( 'underpayment_notice_range' )[2] / 100 ) ) && $amount_due > 0 && $paid !== 1 ) {
					$data .= sprintf( esc_html__( '%1$sAmount missing: %2$s %3$s%4$s', 'cryptowoo' ), '<span style="font-weight: bold; color: red;">', self::fbits( $amount_missing ), $payment_currency, '</span><br>' );
				}

				// Status message
				if ( $paid === 1 && $timeout !== 1 ) {
					$data .= sprintf( esc_html__( '%1$sYour payment of %2$s %3$s has been received.%4$s', 'cryptowoo' ), '<p>', self::fbits( $full_amount ), $payment_currency, '</p>' );
				} elseif ( $paid !== 1 && $timeout === 1 ) {
					$data .= esc_html__( 'Your order is expired. Please try again or contact us if you need assistance.', 'cryptowoo' );
				} elseif ( $paid !== 1 && $timeout === 3 ) {
					// Maybe tell customer to contact the shop owner
					// $order = wc_get_order($order->get_id());
					if ( $order->has_status( 'on-hold' ) ) {
						$data .= sprintf( '<span style="font-weight: bold; color: red;">%s</span>', esc_html__( 'Your order has been put on hold. Please get in touch with us.', 'cryptowoo' ) );
					} else {
						$data .= sprintf( '<p>%s</p>', esc_html__( 'You will receive another email when your payment is fully confirmed.', 'cryptowoo' ) );
					}
				}

				$data .= '</div>';
				echo wp_kses_post( $data );
			}
		}
	}

	/**
	 * Prepare overpayment message for customer
	 *
	 * @param  $order_data     CW_Payment_Details_Object
	 * @param  $refund_address
	 * @return string
	 */
	static function prepare_overpayment_message( $order_data, $refund_address ) {
		$overpay_message = cw_get_option( 'overpayment_message' ) ?: esc_html__( 'You paid {{AMOUNT_DIFF}} {{PAYMENT_CURRENCY}} too much. Please get in touch with us.', 'cryptowoo' );
		$cnote           = preg_replace_callback(
			'/\{\{([a-zA-Z-0-9\ \_]+?)\}\}/',
			function ( $match ) use ( $order_data, $refund_address ) {
				$cwdb_woocommerce = CW_Database_Woocommerce::instance( $order_data->get_order_id() );
				switch ( strtolower( $match[1] ) ) {
					// faucet information:
					case 'refund_address':
						return (bool) $refund_address ? CW_Formatting::link_to_address( $order_data->get_payment_currency(), $refund_address, false, true ) : 'n/a';
					case 'percentage_paid':
						$value = $cwdb_woocommerce->get_percentage_paid() - 100;
						return $value > 1 ? round( $value, 0 ) : round( $value, 3 );
					case 'amount_diff':
						return CW_Formatting::fbits( $cwdb_woocommerce->get_amount_difference() );
					case 'payment_currency':
						return $cwdb_woocommerce->get_payment_currency();
					default:
						return $match[1];
				}
			},
			$overpay_message
		);

		return $cnote;
	}

	/**
	 * Like wc_get_template_html, returns the HTML
	 *
	 * @see    wc_get_template
	 * @since  2.5.0
	 * @param  string $template_name
	 * @return string
	 *
	 * @deprecated 0.25.14 No longer used by internal code and not recommended.
	 */
	static function cw_get_template_html( $template_name, $email_heading = '', $args = array(), $template_path = '', $default_path = '' ) {
		$file = sprintf( '%stemplates/%s.php', CWOO_PLUGIN_DIR, $template_name );
		if ( file_exists( $file ) ) {
			$template = self::cw_ob( $file );
			return str_replace( '{{EMAIL_HEADING}}', $email_heading, $template );
		} else {
			return '';
		}
	}

	/**
	 * Output buffer
	 *
	 * @param  $file
	 * @return string
	 *
	 * @deprecated 0.25.14 No longer used by internal code and not recommended.
	 */
	private static function cw_ob( $file ) {
		ob_start();
		include $file;
		return ob_get_clean();
	}

	/**
	 * Get coin icon
	 *
	 * @param string $currency
	 * @param string $size
	 *
	 * @return string
	 */
	static function get_coin_icon( $currency, $size = 'normal' ) {

		// Maybe use black or white icons instead of colorized ones
		$icon_color = '';
		if ( ! in_array( $currency, array( 'trezor-lock', 'trezor-logo' ) ) ) {
			$icon_color_option = cw_get_option( 'cryptocurrency_icon_color' );
			$icon_color        = ! $icon_color_option || $icon_color_option === 'color' ? '' : '-' . $icon_color_option;
		}
		$icon_class_name = sprintf( 'cw-coin-%s%s', strtolower( $currency ), $icon_color );

		// Some icons are split in different paths
		$span_paths = '<span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span>';
		if ( 'cryptowoo' === $currency ) {
			$span_paths .= '<span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span><span class="path28"></span><span class="path29"></span><span class="path30"></span><span class="path31"></span><span class="path32"></span><span class="path33"></span><span class="path34"></span>';
		}

		switch ( $size ) {
			default:
			case 'normal':
				$style = '';
				break;
			case 'small':
				$style = ' style="font-size: 80%;"';
				break;
			case 'large':
				$style = ' style="font-size: 120%;"';
				break;
		}

		$icon_html = sprintf( '<span class="%s" %s >%s</span> ', $icon_class_name, $style, $span_paths );
		return apply_filters( 'cw_coin_icon_html', $icon_html, $currency, $size );
	}

	/**
	 * Create GIF image from payment address
	 *
	 * @param $payment_address
	 *
	 * @return string
	 */
	static function generate_sec_image( $payment_address ) {

		// Make sure the php-gd extension is enabled
		if ( ! extension_loaded( 'gd' ) || ! function_exists( 'ImageTTFText' ) ) {
			return '';
		}
		$text = sprintf( esc_html__( 'Please compare the destination address with the one below%1$sbefore you send the payment:%1$s', 'cryptowoo' ), PHP_EOL );
		$im   = imagecreate( 800, 120 );
		ImageColorAllocate( $im, 0, 0, 0 ); // Black background // TODO maybe use pre-defined image that makes OCR more difficult
		$white = ImageColorAllocate( $im, 255, 255, 255 );
		ImageTTFText( $im, 15, 0, 10, 25, $white, CWOO_PLUGIN_DIR . 'assets/fonts/dejavu/DejaVuSansMono.ttf', $text );
		$red = ImageColorAllocate( $im, 255, 0, 0 );
		ImageTTFText( $im, 15, 0, 200, 90, $red, CWOO_PLUGIN_DIR . 'assets/fonts/dejavu/DejaVuSansMono.ttf', $payment_address );
		ob_start();
		ImageGif( $im );
		$image_data = ob_get_clean();
		ImageDestroy( $im );

		return sprintf( '<h3><i class="fa fa-lock" aria-hidden="true"></i> %s</h3> <img src="data:image/gif;base64,%s" />', __( 'Security Check', 'cryptowoo' ), base64_encode( $image_data ) );

	}

	/**
	 * Add a custom action "force check order status" to order actions select box on edit order page
	 * Only added for unpaid orders
	 *
	 * @param  array $actions order actions array to display
	 * @return array - updated actions
	 */
	static function cw_add_order_meta_box_action_force_check_order( $actions ) {
		return self::cw_add_order_meta_box_action( $actions, 'force_update_payment_status_action', __( 'Force update payment status', 'cryptowoo' ) );
	}

	/**
	 * Add a custom action "force accept payment" to order actions select box on edit order page
	 * Only added for orders without payment status completed
	 *
	 * @param  array $actions order actions array to display
	 * @return array - updated actions
	 */
	static function cw_add_order_meta_box_action_force_accept_payment( $actions ) {
		if ( CW_Order_Processing_Tools::instance()->check_force_complete_permissions() ) {
			return self::cw_add_order_meta_box_action( $actions, 'force_accept_payment_action', __( 'Force accept payment', 'cryptowoo' ) );
		}
	}

	/**
	 * Source: https://www.skyverge.com/blog/add-woocommerce-custom-order-actions/
	 * Customized by CryptoWoo to our needs
	 *
	 * Add a custom action to order actions select box on edit order page
	 * Only added for unpaid orders
	 *
	 * @param  array $actions order actions array to display
	 * @return array - updated actions
	 */
	static function cw_add_order_meta_box_action( $actions, $action_name, $action_text ) {
		/**
*
		 *
	* @var WC_Order $theorder
*/
		global $theorder;

		// bail if the order has been paid for or if CryptoWoo is not the payment method.
		if ( $theorder->is_paid() || CW_PAYMENT_METHOD_ID !== $theorder->get_payment_method() ) {
			return $actions;
		}

		// add "force update payment status" custom action
		$actions[ $action_name ] = $action_text;
		return $actions;
	}

	/**
	 * Add a custom action to the Woocommerce Order Overview
	 *
	 * Only added for unpaid CryptoWoo orders
	 *
	 * @param $actions
	 * @param WC_Order $order
	 *
	 * @return mixed
	 */
	static function add_force_check_order_action( $actions, $order ) {
		if ( ! $order->is_paid() && CW_PAYMENT_METHOD_ID === $order->get_payment_method() ) {
			$actions['force_update'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=cw_force_update_payment_status&order_id=' . $order->get_id() ), 'cw-force-update-payment-status' ),
				'name'   => __( 'Force Update', 'cryptowoo' ),
				'title'  => __( 'Force update payment status', 'cryptowoo' ),
				'action' => 'refresh',
			);
		}
		return $actions;
	}

	/**
	 * Add a custom action to the Woocommerce Order Overview
	 * And remove the 'complete' button as this button replace it
	 *
	 * Only added for orders without payment status complete
	 *
	 * @param $actions
	 * @param WC_Order $order
	 *
	 * @return mixed
	 */
	static function add_force_accept_payment_action( $actions, $order ) {
		if ( ! $order->is_paid() && CW_PAYMENT_METHOD_ID === $order->get_payment_method() && CW_Order_Processing_Tools::instance()->check_force_complete_permissions() ) {
			add_thickbox();
			add_action( "admin_post_submit_cryptowoo_force_complete_form_order_{$order->get_id()}", array( self::class, 'submit_cryptowoo_force_accept_payment_form' ) );
			$actions[ "force_accept_payment_{$order->get_id()}" ] = array(
				'url'    => "#TB_inline&width=300&height=450&inlineId=cryptowoo-force-complete-form-order-{$order->get_id()}",
				'name'   => __( 'Force accept', 'cryptowoo' ),
				'title'  => __( 'Force accept payment', 'cryptowoo' ),
				'action' => 'processing thickbox',
			);

			// Dirty fix for form element html not getting posted by php echo for the first order in list. TODO: Find a solution and remove this dirty fix
			echo "<form name='form_cryptowoo_force_accept_payment_dummy' method='post' style='display:none;' action='" . esc_url( admin_url( 'admin-post.php' ) ) . "?action=submit_cryptowoo_force_accept_payment_form'></form>";

			// The css style display is needed to display the TB_window only on button click.
			add_filter( 'safe_style_css', function( $styles ) {
				$styles['display'] = 'display';
				return $styles;
			} );
			$allowed_html = array_merge(
				wp_kses_allowed_html( 'post' ),
				array(
					'input'  => array(
						'type'  => true,
						'name'  => true,
						'value' => true,
						'id'    => true,
						'class' => true,
					),
					'select' => array(
						'class' => true,
						'id'    => true,
						'name'  => true,
					),
					'option' => array(
						'value'    => true,
						'selected' => true,
					),
					'form'   => array(
						'id'     => true,
						'name'   => true,
						'method' => true,
						'action' => true,
					),
				)
			);
			echo wp_kses( self::generate_cryptowoo_force_accept_payment_form( $order ), $allowed_html );

			// Remove standard WooCommerce "processing" and "completed" action.
			unset( $actions['processing'] );
			unset( $actions['complete'] );
		}

		return $actions;
	}

	/**
	 *
	 * Add a cryptowoo force accept payment form ThickBox
	 *
	 * @param WC_Order $the_order Woocommerce order object.
	 *
	 * @return string
	 */
	static function generate_cryptowoo_force_accept_payment_form( $the_order ) {
		$order_id = $the_order->get_id();

		$cwdb_woocommerce = CW_Database_Woocommerce::instance( $order_id );
		$crypto_amount    = $cwdb_woocommerce->get_crypto_amount();
		$payment_currency = $cwdb_woocommerce->get_payment_currency();
		$payment_address  = $cwdb_woocommerce->get_payment_address();
		$txids            = $cwdb_woocommerce->get_tx_ids_string();

		$form_currencies = '';
		foreach ( cw_get_enabled_currencies() as $currency_short => $currency_nice ) {
			$form_currencies .= "<option value='$currency_short' ";
			if ( $currency_short == $payment_currency ) {
				$form_currencies .= "selected='selected' ";
			}
			$form_currencies .= ">$currency_nice</option>";
		}

		$crypto_amount = self::fbits( $crypto_amount, true, self::calculate_coin_decimals( $payment_currency, $crypto_amount ), true );
		$nonce_html    = wp_nonce_field( 'cw-force-update-payment-status', '_wpnonce', true, false );
		return // TODO: Let the user select the amount of each tx.
		"<div id='cryptowoo-force-complete-form-order-$order_id' style='display:none;'>
                <form id='form_cryptowoo_force_accept_payment_$order_id' name='form_cryptowoo_force_accept_payment_$order_id'  method='post' action='" . esc_url( admin_url( 'admin-post.php' ) ) . "?action=submit_cryptowoo_force_accept_payment_form'>
					<div class='form_description'>
						<h2>CryptoWoo Force accept payment #$order_id</h2>
						<p>Enter the payment details and click submit to update the order.</p>
					</div>
					<ul >
						<li id='li_1' >
							<label class='description' for='received_confirmed'>Received Confirmed </label>
							<input id='received_confirmed' name='received_confirmed' class='element text medium' type='number' size='35' step='0.00000001' value='$crypto_amount'/>
						</li>
						<li id='li_2' >
							<label class='description' for='received_unconfirmed'>Received Unconfirmed </label>
							<input id='received_unconfirmed' name='received_unconfirmed' class='element text medium' type='number' size='35' step='0.00000001' value='0'/>
						</li>
						<li id='li_5' >
							<label class='description' for='payment_currency'>Payment Currency </label>
							<select class='element select large' id='payment_currency' name='payment_currency'>
								$form_currencies
							</select>
						</li>
						<li id='li_3' >
							<label class='description' for='txids'>Transaction ID's (one per line)</label><br>
							<textarea id='txids' name='txids' cols='33' class='element textarea medium'>$txids</textarea>
						</li>
						<li id='li_4' >
							<label class='description' for='payment_address'>Payment Address </label>
							<input id='payment_address' name='payment_address' class='element text large' type='text' size='35' value='$payment_address'/>
						</li>
						<li class='buttons'>
							<input type='hidden' name='order_id' value='{$the_order->get_id()}' />
							$nonce_html
							<br>
							<input id='saveForm' class='button-primary' type='submit' name='submit' value='Submit' />
						</li>
					</ul>
				</form>
			</div>";
	}

	/**
	 * Force accept payment when a force accept payment request has been submitted (submit button clicked)
	 */
	static function submit_cryptowoo_force_accept_payment_form() {

		$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( $_REQUEST['_wpnonce'] ) : false;

		if ( CW_Order_Processing_Tools::instance()->check_force_complete_permissions() && $nonce && wp_verify_nonce( $nonce, 'cw-force-update-payment-status' ) ) {
			$order                = wc_get_order( sanitize_key( $_REQUEST['order_id'] ) );
			$payment_address      = sanitize_text_field( wp_unslash( $_REQUEST['payment_address'] ) );
			$payment_currency     = sanitize_text_field( wp_unslash( $_REQUEST['payment_currency'] ) );
			$received_confirmed   = number_format( wp_unslash( $_REQUEST['received_confirmed'] ) * 1e8, 0, '', '' );
			$received_unconfirmed = number_format( wp_unslash( $_REQUEST['received_unconfirmed'] ) * 1e8, 0, '', '' );
			$tx_ids               = empty( $_REQUEST['txids'] ) ? array() : explode( PHP_EOL, sanitize_textarea_field( wp_unslash( $_REQUEST['txids'] ) ) );

			CW_Order_Processing::force_accept_payment( $order, $received_confirmed, $received_unconfirmed, $tx_ids, $payment_address, $payment_currency );
		}
		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=shop_order' ) );
		die(); // request handlers should die() when they complete
	}

	/**
	 * Format the payment page instructions
	 *
	 * @param $payment_address
	 * @param $crypto_amount
	 * @param $payment_currency
	 * @param $wallet_config
	 * @param $custom_text
	 *
	 * @return string
	 */
	static function format_payment_page_instructions( $payment_address, $crypto_amount, $payment_currency, $wallet_config, $custom_text = '' ) {
		$text              = $custom_text ?: cw_get_option( 'payment_page_text' );
		$search            = array( '{{PAYMENT_ADDRESS}}', '{{CRYPTO_AMOUNT}}', '{{PAYMENT_CURRENCY}}' );
		$replace           = array( $payment_address, self::fbits( $crypto_amount, true, $wallet_config['decimals'], true ), esc_html( $payment_currency ) );
		$payment_page_text = do_shortcode( str_replace( $search, $replace, $text ) );
		return sprintf( '<span style="clear: both;">%s</span>', $payment_page_text );

	}


} // End Class CW_Formatting

/*
	Functions for enabling cryptos as base currency.
	USE AT YOUR OWN RISK

	Using crypto as base currency can create problems with calculating exchange rates
	depending on shop and product configuration.

*/
/*
add_filter( 'woocommerce_currencies', 'cw_add_btc' );

function cw_add_btc( $currencies ) {
	$currencies['BTC'] = __( 'Bitcoin', 'woocommerce' );
	return $currencies;
}

add_filter('woocommerce_currency_symbol', 'cw_add_btc_symbol', 10, 2);

function cw_add_btc_symbol( $currency_symbol, $currency ) {
	return $currency === 'BTC' ? '฿' : $currency_symbol;
}

add_filter( 'woocommerce_currencies', 'cw_add_ltc' );

function cw_add_ltc( $currencies ) {
	$currencies['LTC'] = __( 'Litecoin', 'woocommerce' );
	return $currencies;
}

add_filter('woocommerce_currency_symbol', 'cw_add_ltc_symbol', 10, 2);

function cw_add_ltc_symbol( $currency_symbol, $currency ) {
	return $currency === 'LTC' ? 'Ł' : $currency_symbol;
}

add_filter( 'woocommerce_currencies', 'cw_add_doge' );

function cw_add_doge( $currencies ) {
	$currencies['DOGE'] = __( 'Dogecoin', 'woocommerce' );
	return $currencies;
}

add_filter('woocommerce_currency_symbol', 'cw_add_doge_symbol', 10, 2);

function cw_add_doge_symbol( $currency_symbol, $currency ) {

	return $currency === 'DOGE' ? 'Ð' : $currency_symbol;
}
*/

