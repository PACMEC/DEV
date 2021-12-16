<?php
/**
 * Booster for WooCommerce - Settings - Gateways Currency Converter
 *
 * @version 4.2.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    [dev] maybe make "Advanced: Fix Chosen Payment Method" option enabled by default (or even remove option completely and always perform `$this->fix_chosen_payment_method()`)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Payment Gateways', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you set different currency for each payment gateway.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_payment_gateways_currency_options',
	),
);
$currency_from      = get_woocommerce_currency();
$available_gateways = WC()->payment_gateways->payment_gateways();
foreach ( $available_gateways as $key => $gateway ) {
	$currency_to = wcj_get_option( 'wcj_gateways_currency_' . $key, get_woocommerce_currency() );
	$custom_attributes = array(
		'currency_from'        => $currency_from,
		'currency_to'          => $currency_to,
		'multiply_by_field_id' => 'wcj_gateways_currency_exchange_rate_' . $key,
	);
	if ( $currency_from == $currency_to ) {
		$custom_attributes['disabled'] = 'disabled';
	}
	if ( 'no_changes' == $currency_to ) {
		$custom_attributes['disabled'] = 'disabled';
		$currency_to = $currency_from;
	}
	$settings = array_merge( $settings, array(
		array(
			'title'    => $gateway->get_method_title() . ( $gateway->get_title() != $gateway->get_method_title() ? ' (' . $gateway->get_title() . ')' : '' ),
			'id'       => 'wcj_gateways_currency_' . $key,
			'default'  => 'no_changes',
			'type'     => 'select',
			'options'  => array_merge( array( 'no_changes' => __( 'No changes', 'e-commerce-jetpack' ) ), wcj_get_woocommerce_currencies_and_symbols() ),
			'desc'     => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $key ) . '"' .
				' style="font-style:normal;text-decoration:none;" title="' . __( 'Go to payment gateway\'s settings', 'e-commerce-jetpack' ) . '">&#8505;</a>',
		),
		array(
			'title'                    => '',
			'id'                       => 'wcj_gateways_currency_exchange_rate_' . $key,
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
		'id'       => 'wcj_payment_gateways_currency_options',
	),
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_payment_gateways_currency_general_options',
	),
	array(
		'title'    => __( 'Exchange Rates Updates', 'e-commerce-jetpack' ),
		'id'       => 'wcj_gateways_currency_exchange_rate_update_auto',
		'default'  => 'manual',
		'type'     => 'select',
		'options'  => array(
			'manual' => __( 'Enter Rates Manually', 'e-commerce-jetpack' ),
			'auto'   => __( 'Automatically via Currency Exchange Rates module', 'e-commerce-jetpack' ),
		),
		'desc'     => ( '' == apply_filters( 'booster_message', '', 'desc' ) ) ?
			__( 'Visit', 'e-commerce-jetpack' ) .
				' <a href="' . admin_url( 'admin.php?page=wc-settings&tab=jetpack&wcj-cat=prices_and_currencies&section=currency_exchange_rates' ) . '">' .
					__( 'Currency Exchange Rates module', 'e-commerce-jetpack' ) . '</a>'
			: apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Show Converted Prices', 'e-commerce-jetpack' ),
		'id'       => 'wcj_gateways_currency_page_scope',
		'default'  => 'cart_and_checkout',
		'type'     => 'select',
		'options'  => array(
			'cart_and_checkout' => __( 'On both cart and checkout pages', 'e-commerce-jetpack' ),
			'checkout_only'     => __( 'On checkout page only', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Advanced: Fix "Chosen Payment Method"', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Enable this if you are having compatibility issues with some other plugins or modules.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_gateways_currency_fix_chosen_payment_method',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_payment_gateways_currency_general_options',
	),
) );
return $settings;
