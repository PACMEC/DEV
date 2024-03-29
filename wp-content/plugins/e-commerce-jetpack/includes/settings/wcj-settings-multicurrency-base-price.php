<?php
/**
 * Booster for WooCommerce - Settings - Multicurrency Product Base Price
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    (maybe) `if ( isset( $all_currencies[ $currency_from ] ) ) { unset( $all_currencies[ $currency_from ] ); }`
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$currency_from  = get_woocommerce_currency();
$all_currencies = wcj_get_woocommerce_currencies_and_symbols();
$settings = array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_multicurrency_base_price_options',
	),
	array(
		'title'    => __( 'Exchange Rates Updates', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_base_price_exchange_rate_update',
		'default'  => 'manual',
		'type'     => 'select',
		'options'  => array(
			'manual' => __( 'Enter Rates Manually', 'e-commerce-jetpack' ),
			'auto'   => __( 'Automatically via Currency Exchange Rates module', 'e-commerce-jetpack' ),
		),
		'desc'     => ( '' == apply_filters( 'booster_message', '', 'desc' ) ) ?
			__( 'Visit', 'e-commerce-jetpack' ) . ' <a href="' . admin_url( 'admin.php?page=wc-settings&tab=jetpack&wcj-cat=prices_and_currencies&section=currency_exchange_rates' ) . '">' . __( 'Currency Exchange Rates module', 'e-commerce-jetpack' ) . '</a>'
			:
			apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Round Prices', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_base_price_round_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'rounding precision', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Number of decimals.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_base_price_round_precision',
		'default'  => wcj_get_option( 'woocommerce_price_num_decimals' ),
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'title'    => __( 'Convert Product Prices in Admin Products List', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_base_price_do_convert_in_back_end',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_multicurrency_base_price_options',
	),
	array(
		'title'    => __( 'Advanced', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_multicurrency_base_price_advanced',
	),
	array(
		'title'    => __( 'Save Calculated Products Prices', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This may help if you are experiencing compatibility issues with other plugins.  If you are facing your price will not be displayed properly then enable this option', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_base_price_save_prices',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Price Filters Priority', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Priority for all module\'s price filters. If you face pricing issues while using another plugin or booster module, You can change the Priority, Greater value for high priority & Lower value for low priority. Set to zero to use default priority.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_base_price_advanced_price_hooks_priority',
		'default'  => 0,
		'type'     => 'number',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_multicurrency_base_price_advanced',
	),
	array(
		'title'    => __( 'Compatibility', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_multicurrency_base_price_currencies_options',
	),
	array(
		'title'    => __( 'WooCommerce Price Filter Compatibility', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable','e-commerce-jetpack' ),
		'desc_tip' => __( 'Adds compatibility with WooCommerce Price Filter Widget','e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_base_price_advanced_price_filter_comp',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'             => __( 'WooCommerce Price Sorting', 'e-commerce-jetpack' ),
		'desc_tip'          => __( 'Adds compatibility with WooCommerce Price Sorting', 'e-commerce-jetpack' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'id'                => 'wcj_multicurrency_base_price_comp_wc_price_sorting',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_multicurrency_base_price_compatibility',
	),
	array(
		'title'    => __( 'Currencies Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_multicurrency_base_price_currencies_options',
	),
	array(
		'title'    => __( 'Total Currencies', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_base_price_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ? apply_filters( 'booster_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min'  => '1', )
		),
	),
);
$total_number = apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_multicurrency_base_price_total_number', 1 ) );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$currency_to = wcj_get_option( 'wcj_multicurrency_base_price_currency_' . $i, $currency_from );
	$custom_attributes = array(
		'currency_from'        => $currency_from,
		'currency_to'          => $currency_to,
		'multiply_by_field_id' => 'wcj_multicurrency_base_price_exchange_rate_' . $i,
	);
	if ( $currency_from == $currency_to ) {
		$custom_attributes['disabled'] = 'disabled';
	}
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Currency', 'e-commerce-jetpack' ) . ' #' . $i,
			'id'       => 'wcj_multicurrency_base_price_currency_' . $i,
			'default'  => $currency_from,
			'type'     => 'select',
			'options'  => $all_currencies,
			'css'      => 'width:250px;',
		),
		array(
			'title'                    => '',
			'id'                       => 'wcj_multicurrency_base_price_exchange_rate_' . $i,
			'default'                  => 1,
			'type'                     => 'exchange_rate',
			'custom_attributes_button' => $custom_attributes,
			'value'                    => $currency_from . '/' . $currency_to,
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_multicurrency_base_price_currencies_options',
	),
) );
return $settings;
