<?php
/**
 * Booster for WooCommerce - Module - Currencies
 *
 * @version 3.9.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Currencies' ) ) :

class WCJ_Currencies extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 3.2.4
	 * @todo    [dev] (maybe) update description
	 * @todo    [dev] (maybe) "add additional currencies" checkbox
	 * @todo    [dev] (maybe) save settings as array
	 * @todo    [dev] (maybe) fix missing country flags
	 */
	function __construct() {

		$this->id         = 'currency';
		$this->short_desc = __( 'Currencies', 'e-commerce-jetpack' );
		$this->desc       = __( 'Add all world currencies and cryptocurrencies to your store; change currency symbol (Plus); add custom currencies (1 allowed in free version).', 'e-commerce-jetpack' );
		$this->desc_pro   = __( 'Add all world currencies and cryptocurrencies to your store; change currency symbol; add custom currencies.', 'e-commerce-jetpack' );
		$this->link_slug  = 'woocommerce-all-currencies';
		parent::__construct();

		if ( $this->is_enabled() ) {
			add_filter( 'woocommerce_currencies',       array( $this, 'add_all_currencies'),              PHP_INT_MAX );
			add_filter( 'woocommerce_currency_symbol',  array( $this, 'change_currency_symbol'),          PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_general_settings', array( $this, 'add_edit_currency_symbol_field' ), PHP_INT_MAX );
		}
	}

	/**
	 * get_custom_currencies.
	 *
	 * @version 3.9.0
	 * @since   3.9.0
	 */
	function get_custom_currencies() {
		$custom_currencies = array();
		for ( $i = 1; $i <= apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_currency_custom_currency_total_number', 1 ) ); $i++ ) {
			$custom_currency_code = wcj_get_option( 'wcj_currency_custom_currency_code_'   . $i, '' );
			$custom_currency_name = wcj_get_option( 'wcj_currency_custom_currency_name_'   . $i, '' );
			if ( '' != $custom_currency_code && '' != $custom_currency_name ) {
				$custom_currencies[ $custom_currency_code ] = $custom_currency_name;
			}
		}
		return $custom_currencies;
	}

	/**
	 * get_additional_currencies.
	 *
	 * @version 3.9.0
	 * @since   3.9.0
	 * @todo    [dev] (maybe) add more cryptocurrencies
	 */
	function get_additional_currencies() {
		return array(
			// Crypto
			'AUR' => __( 'Auroracoin', 'e-commerce-jetpack' ),
			'BCC' => __( 'BitConnect', 'e-commerce-jetpack' ),
			'BCH' => __( 'Bitcoin Cash', 'e-commerce-jetpack' ),
			'KOI' => __( 'Coinye', 'e-commerce-jetpack' ),
			'XDN' => __( 'DigitalNote', 'e-commerce-jetpack' ),
			'EMC' => __( 'Emercoin', 'e-commerce-jetpack' ),
			'ETC' => __( 'Ethereum Classic', 'e-commerce-jetpack' ),
			'ETH' => __( 'Ethereum', 'e-commerce-jetpack' ),
			'FMC' => __( 'Freemasoncoin', 'e-commerce-jetpack' ),
			'GRC' => __( 'Gridcoin', 'e-commerce-jetpack' ),
			'IOT' => __( 'IOTA', 'e-commerce-jetpack' ),
			'LTC' => __( 'Litecoin', 'e-commerce-jetpack' ),
			'MZC' => __( 'MazaCoin', 'e-commerce-jetpack' ),
			'XMR' => __( 'Monero', 'e-commerce-jetpack' ),
			'NMC' => __( 'Namecoin', 'e-commerce-jetpack' ),
			'XEM' => __( 'NEM', 'e-commerce-jetpack' ),
			'NXT' => __( 'Nxt', 'e-commerce-jetpack' ),
			'MSC' => __( 'Omni', 'e-commerce-jetpack' ),
			'PPC' => __( 'Peercoin', 'e-commerce-jetpack' ),
			'POT' => __( 'PotCoin', 'e-commerce-jetpack' ),
			'XPM' => __( 'Primecoin', 'e-commerce-jetpack' ),
			'XRP' => __( 'Ripple', 'e-commerce-jetpack' ),
			'SIL' => __( 'SixEleven', 'e-commerce-jetpack' ),
			'AMP' => __( 'Synereo AMP', 'e-commerce-jetpack' ),
			'TIT' => __( 'Titcoin', 'e-commerce-jetpack' ),
			'UBQ' => __( 'Ubiq', 'e-commerce-jetpack' ),
			'VTC' => __( 'Vertcoin', 'e-commerce-jetpack' ),
			'ZEC' => __( 'Zcash', 'e-commerce-jetpack' ),
			// Other
			'XDR' => __( 'Special Drawing Rights', 'e-commerce-jetpack' ),
			// Virtual
			'MYC' => __( 'myCred', 'e-commerce-jetpack' ),
		);
	}

	/**
	 * get_additional_currency_symbol.
	 *
	 * @version 3.9.0
	 * @since   3.9.0
	 */
	function get_additional_currency_symbol( $currency_code ) {
		return $currency_code;
	}

	/**
	 * add_all_currencies.
	 *
	 * @version 3.9.0
	 */
	function add_all_currencies( $currencies ) {
		return array_merge( $currencies, $this->get_additional_currencies(), $this->get_custom_currencies() );
	}

	/**
	 * get_saved_currency_symbol.
	 *
	 * @version 3.9.0
	 * @since   3.9.0
	 */
	function get_saved_currency_symbol( $currency, $default_symbol ) {
		if ( false === ( $saved_currency_symbol = wcj_get_option( 'wcj_currency_' . $currency, false ) ) ) {
			return ( in_array( $currency, array_keys( $this->get_additional_currencies() ) ) ? $this->get_additional_currency_symbol( $currency ) : $default_symbol );
		} else {
			return $saved_currency_symbol;
		}
	}

	/**
	 * change_currency_symbol.
	 *
	 * @version 3.9.0
	 */
	function change_currency_symbol( $currency_symbol, $currency ) {
		// Maybe return saved value
		if ( isset( $this->saved_symbol[ $currency ] ) ) {
			return $this->saved_symbol[ $currency ];
		}
		// Maybe hide symbol
		if ( 'yes' === wcj_get_option( 'wcj_currency_hide_symbol', 'no' ) ) {
			return '';
		}
		// Custom currencies
		for ( $i = 1; $i <= apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_currency_custom_currency_total_number', 1 ) ); $i++ ) {
			$custom_currency_code = wcj_get_option( 'wcj_currency_custom_currency_code_' . $i, '' );
			$custom_currency_name = wcj_get_option( 'wcj_currency_custom_currency_name_' . $i, '' );
			if ( '' != $custom_currency_code && '' != $custom_currency_name && $currency === $custom_currency_code ) {
				$this->saved_symbol[ $currency ] = do_shortcode( wcj_get_option( 'wcj_currency_custom_currency_symbol_' . $i, '' ) );
				return $this->saved_symbol[ $currency ];
			}
		}
		// List
		$this->saved_symbol[ $currency ] = apply_filters( 'booster_option', $currency_symbol, do_shortcode( $this->get_saved_currency_symbol( $currency, $currency_symbol ) ) );
		return $this->saved_symbol[ $currency ];
	}

	/**
	 * add_edit_currency_symbol_field.
	 *
	 * @version 3.9.0
	 * @todo    [dev] (maybe) remove this
	 */
	function add_edit_currency_symbol_field( $settings ) {
		$updated_settings = array();
		foreach ( $settings as $section ) {
			if ( isset( $section['id'] ) && 'woocommerce_currency_pos' == $section['id'] ) {
				$updated_settings[] = array(
					'name'     => __( 'Booster: Currency Symbol', 'e-commerce-jetpack' ),
					'desc_tip' => __( 'This sets the currency symbol.', 'e-commerce-jetpack' ),
					'id'       => 'wcj_currency_' . get_woocommerce_currency(),
					'type'     => 'text',
					'default'  => get_woocommerce_currency_symbol(),
					'desc'     => apply_filters( 'booster_message', '', 'desc' ),
					'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
				);
			}
			$updated_settings[] = $section;
		}
		return $updated_settings;
	}

}

endif;

return new WCJ_Currencies();
