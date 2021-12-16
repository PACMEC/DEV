<?php
/**
 * Booster for WooCommerce - Settings - Currency Exchange Rates
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    add "rounding" and "fixed offset" options for each pair separately (and option to enable/disable these per pair extra settings)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$desc = '';
if ( $this->is_enabled() ) {
	if ( '' != wcj_get_option( 'wcj_currency_exchange_rate_cron_time', '' ) ) {
		$scheduled_time_diff = wcj_get_option( 'wcj_currency_exchange_rate_cron_time', '' ) - time();
		if ( $scheduled_time_diff > 60 ) {
			$desc = '<br><em>' . sprintf( __( '%s till next update.', 'e-commerce-jetpack' ), human_time_diff( 0, $scheduled_time_diff ) ) . '</em>';
		} elseif ( $scheduled_time_diff > 0 ) {
			$desc = '<br><em>' . sprintf( __( '%s seconds till next update.', 'e-commerce-jetpack' ), $scheduled_time_diff ) . '</em>';
		}
	}
}
$settings = array(
	array(
		'title'    => __( 'General Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_currency_exchange_rates_options',
	),
	array(
		'title'    => __( 'Exchange Rates Updates', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_exchange_rates_auto',
		'default'  => 'daily',
		'desc_tip' => __( 'How frequently do you want to update currency rates. ', 'e-commerce-jetpack' ),
		'type'     => 'select',
		'options'  => array(
			'minutely'   => __( 'Update Every Minute', 'e-commerce-jetpack' ),
			'hourly'     => __( 'Update Hourly', 'e-commerce-jetpack' ),
			'twicedaily' => __( 'Update Twice Daily', 'e-commerce-jetpack' ),
			'daily'      => __( 'Update Daily', 'e-commerce-jetpack' ),
			'weekly'     => __( 'Update Weekly', 'e-commerce-jetpack' ),
		),
		'desc'     => ( $this->is_enabled() ?
			$desc . ' ' . '<a href="' . add_query_arg( 'wcj_currency_exchange_rates_update_now', '1' ) . '">' . __( 'Update all rates now', 'e-commerce-jetpack' ) . '</a>' : '' ),
	),
	array(
		'title'    => __( 'Exchange Rates Server', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_exchange_rates_server',
		'default'  => 'ecb',
		'desc_tip' => __( 'If rates are not updated then re-enable the cron system open your wp-config.php file located in the base root of your WordPress directory and look for a PHP Constant named define("ALTERNATE_WP_CRON", true);and set it’s value to true..', 'e-commerce-jetpack' ),
		'type'     => 'select',
		'options'  => wcj_get_currency_exchange_rate_servers(),
	),
	array(
		'title'    => __( 'Exchange Rates Rounding', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_exchange_rates_rounding_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Number of decimals', 'woocommerce' ) . ' (' . __( 'i.e. rounding precision', 'e-commerce-jetpack' ) . ')',
		'desc_tip' => __( 'Rounding precision sets number of decimal digits to round to.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_exchange_rates_rounding_precision',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => '0' ),
	),
	array(
		'title'    => __( 'Exchange Rates Offset - Percent', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If both percent and fixed offsets are set - percent offset is applied first and fixed offset after that.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_exchange_rates_offset_percent',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'step' => '0.001' ),
	),
	array(
		'title'    => __( 'Exchange Rates Offset - Fixed', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If both percent and fixed offsets are set - percent offset is applied first and fixed offset after that.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_exchange_rates_offset_fixed',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'step' => '0.000001' ),
	),
	array(
		'title'    => __( 'Calculate with Inversion', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If your currency pair have very small exchange rate, you may want to invert currencies before calculating the rate.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_exchange_rates_calculate_by_invert',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Always Use cURL', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If for some reason currency exchange rates are not updating, try enabling this option.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_exchange_rates_always_curl',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Force Point as Decimal Separator', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Force "." as decimal separator for exchange rates.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_exchange_rates_point_decimal_separator',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	/*
	array(
		'title'    => __( 'Logging', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_exchange_logging_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	*/
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_currency_exchange_rates_options',
	),
	array(
		'title'    => __( 'API Keys', 'e-commerce-jetpack' ),
		'desc'     => __( 'API keys provided by the Exchange Rates Servers', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_currency_exchange_api_key',
	),
	array(
		'title'    => __( 'Free Currency Converter', 'e-commerce-jetpack' ),
		'desc'     => sprintf(__( 'More information at %s', 'e-commerce-jetpack' ),'<a target="_blank" href="https://free.currencyconverterapi.com/free-api-key">https://free.currencyconverterapi.com/free-api-key</a>'),
		'type'     => 'text',
		'id'       => 'wcj_currency_exchange_api_key_fccapi',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_currency_exchange_api_key',
	),
	array(
		'title'    => __( 'Custom Currencies Options', 'e-commerce-jetpack' ),
		'desc'     => sprintf(
			__( 'You can add more currencies in this section. E.g. this can be used to display exchange rates with %s shortcodes.', 'e-commerce-jetpack' ),
			'<code>[wcj_currency_exchange_rate]</code>, <code>[wcj_currency_exchange_rates_table]</code>'
		),
		'type'     => 'title',
		'id'       => 'wcj_currency_exchange_custom_currencies_options',
	),
);
// Additional (custom) currencies
$all_currencies = wcj_get_woocommerce_currencies_and_symbols();
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Total Custom Currencies', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_exchange_custom_currencies_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
) );
$total_number = apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_currency_exchange_custom_currencies_total_number', 1 ) );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Custom Currency', 'e-commerce-jetpack' ) . ' #' . $i,
			'id'       => 'wcj_currency_exchange_custom_currencies_' . $i,
			'default'  => 'disabled',
			'type'     => 'select',
			'options'  => array_merge( array( 'disabled' => __( 'Disabled', 'e-commerce-jetpack' ) ), $all_currencies ),
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_currency_exchange_custom_currencies_options',
	),
) );
// Exchange rates
$exchange_rate_settings = $this->get_all_currencies_exchange_rates_settings( true );
if ( ! empty( $exchange_rate_settings ) ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Exchange Rates', 'e-commerce-jetpack' ),
			'type'     => 'title',
			'desc'     => __( 'All currencies from all <strong>enabled</strong> modules (with "Exchange Rates Updates" set to "Automatically via Currency Exchange Rates module") will be automatically added to the list.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_currency_exchange_rates_rates',
		),
	) );
	$settings = array_merge( $settings, $exchange_rate_settings );
	$settings = array_merge( $settings, array(
		array(
			'type'     => 'sectionend',
			'id'       => 'wcj_currency_exchange_rates_rates',
		),
	) );
}
return $settings;
