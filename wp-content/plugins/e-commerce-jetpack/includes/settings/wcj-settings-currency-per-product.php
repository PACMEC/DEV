<?php
/**
 * Booster for WooCommerce - Settings - Currency per Product
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$currency_from  = get_woocommerce_currency();
$all_currencies = wcj_get_woocommerce_currencies_and_symbols();
/*
foreach ( $all_currencies as $currency_key => $currency_name ) {
	if ( $currency_from == $currency_key ) {
		unset( $all_currencies[ $currency_key ] );
	}
}
*/
$settings = array(
	array(
		'title'    => __( 'Cart and Checkout Behaviour Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_currency_per_product_cart_options',
	),
	array(
		'title'    => __( 'Cart and Checkout Behaviour', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_per_product_cart_checkout',
		'default'  => 'convert_shop_default',
		'type'     => 'select',
		'options'  => array(
			'convert_shop_default'  => __( 'Convert to shop default currency', 'e-commerce-jetpack' ),
			'leave_one_product'     => __( 'Leave product currency (allow only one product to be added to cart)', 'e-commerce-jetpack' ),
			'leave_same_currency'   => __( 'Leave product currency (allow only same currency products to be added to cart)', 'e-commerce-jetpack' ),
			'convert_last_product'  => __( 'Convert to currency of last product in cart', 'e-commerce-jetpack' ),
			'convert_first_product' => __( 'Convert to currency of first product in cart', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Message', 'e-commerce-jetpack' ) . ': ' . __( 'Leave product currency (allow only one product to be added to cart)', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_per_product_cart_checkout_leave_one_product',
		'default'  => __( 'Only one product can be added to the cart. Clear the cart or finish the order, before adding another product to the cart.', 'e-commerce-jetpack' ),
		'type'     => 'textarea',
		'css'      => 'min-width:300px;width:66%',
	),
	array(
		'title'    => __( 'Message', 'e-commerce-jetpack' ) . ': ' . __( 'Leave product currency (allow only same currency products to be added to cart)', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_per_product_cart_checkout_leave_same_currency',
		'default'  => __( 'Only products with same currency can be added to the cart. Clear the cart or finish the order, before adding products with another currency to the cart.', 'e-commerce-jetpack' ),
		'type'     => 'textarea',
		'css'      => 'min-width:300px;width:66%',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_currency_per_product_cart_options',
	),
	array(
		'title'    => __( 'Per Product Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_currency_per_product_per_product_options',
	),
	array(
		'title'    => __( 'Currency per Product', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will add meta box to each product\'s edit page', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_per_product_per_product',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_currency_per_product_per_product_options',
	),
	array(
		'title'    => __( 'Additional Options', 'e-commerce-jetpack' ),
		'desc'     => __( 'Save module\'s settings after changing this options to see new settings fields.', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_currency_per_product_additional_options',
	),
	array(
		'title'    => __( 'Currency per Product Authors', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_per_product_by_users_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Currency per Product Authors User Roles', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_per_product_by_user_roles_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Currency per Product Categories', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_per_product_by_product_cats_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Currency per Product Tags', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_per_product_by_product_tags_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_currency_per_product_additional_options',
	),
	array(
		'title'    => __( 'Exchange Rates Updates Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_currency_per_product_exchange_rate_update_options',
	),
	array(
		'title'    => __( 'Exchange Rates Updates', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_per_product_exchange_rate_update',
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
		'type'     => 'sectionend',
		'id'       => 'wcj_currency_per_product_exchange_rate_update_options',
	),
	array(
		'title'    => __( 'Currencies Options', 'e-commerce-jetpack' ),
		'desc'     => __( 'Exchange rates for currencies won\'t be used for products if "Cart and Checkout Behaviour" is set to one of "Leave product currency ..." options. However it may be used for shipping price conversion.', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_currency_per_product_currencies_options',
	),
	array(
		'title'    => __( 'Total Currencies', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_per_product_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ? apply_filters( 'booster_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min'  => '1', )
		),
	),
);
if ( 'yes' === wcj_get_option( 'wcj_currency_per_product_by_users_enabled', 'no' ) ) {
	$users_as_options = wcj_get_users_as_options();
}
if ( 'yes' === wcj_get_option( 'wcj_currency_per_product_by_user_roles_enabled', 'no' ) ) {
	$user_roles_as_options = wcj_get_user_roles_options();
}
if ( 'yes' === wcj_get_option( 'wcj_currency_per_product_by_product_cats_enabled', 'no' ) ) {
	$product_cats_as_options = wcj_get_terms( 'product_cat' );
}
if ( 'yes' === wcj_get_option( 'wcj_currency_per_product_by_product_tags_enabled', 'no' ) ) {
	$product_tags_as_options = wcj_get_terms( 'product_tag' );
}
$total_number = apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_currency_per_product_total_number', 1 ) );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$currency_to = wcj_get_option( 'wcj_currency_per_product_currency_' . $i, $currency_from );
	$custom_attributes = array(
		'currency_from'        => $currency_from,
		'currency_to'          => $currency_to,
		'multiply_by_field_id' => 'wcj_currency_per_product_exchange_rate_' . $i,
	);
	if ( $currency_from == $currency_to ) {
		$custom_attributes['disabled'] = 'disabled';
	}
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Currency', 'e-commerce-jetpack' ) . ' #' . $i,
			'id'       => 'wcj_currency_per_product_currency_' . $i,
			'default'  => $currency_from,
			'type'     => 'select',
			'options'  => $all_currencies,
			'css'      => 'width:250px;',
		),
		array(
			'title'                    => '',
			'id'                       => 'wcj_currency_per_product_exchange_rate_' . $i,
			'default'                  => 1,
			'type'                     => 'exchange_rate',
			'custom_attributes_button' => $custom_attributes,
			'value'                    => $currency_from . '/' . $currency_to,
		),
	) );
	if ( 'yes' === wcj_get_option( 'wcj_currency_per_product_by_users_enabled', 'no' ) ) {
		$settings = array_merge( $settings, array(
			array(
				'desc'     => __( 'Product Authors', 'e-commerce-jetpack' ),
				'id'       => 'wcj_currency_per_product_users_' . $i,
				'default'  => '',
				'type'     => 'multiselect',
				'options'  =>  $users_as_options,
				'class'    => 'chosen_select',
			),
		) );
	}
	if ( 'yes' === wcj_get_option( 'wcj_currency_per_product_by_user_roles_enabled', 'no' ) ) {
		$settings = array_merge( $settings, array(
			array(
				'desc'     => __( 'Product Authors User Roles', 'e-commerce-jetpack' ),
				'id'       => 'wcj_currency_per_product_user_roles_' . $i,
				'default'  => '',
				'type'     => 'multiselect',
				'options'  =>  $user_roles_as_options,
				'class'    => 'chosen_select',
			),
		) );
	}
	if ( 'yes' === wcj_get_option( 'wcj_currency_per_product_by_product_cats_enabled', 'no' ) ) {
		$settings = array_merge( $settings, array(
			array(
				'desc'     => __( 'Product Categories', 'e-commerce-jetpack' ),
				'id'       => 'wcj_currency_per_product_product_cats_' . $i,
				'default'  => '',
				'type'     => 'multiselect',
				'options'  =>  $product_cats_as_options,
				'class'    => 'chosen_select',
			),
		) );
	}
	if ( 'yes' === wcj_get_option( 'wcj_currency_per_product_by_product_tags_enabled', 'no' ) ) {
		$settings = array_merge( $settings, array(
			array(
				'desc'     => __( 'Product Tags', 'e-commerce-jetpack' ),
				'id'       => 'wcj_currency_per_product_product_tags_' . $i,
				'default'  => '',
				'type'     => 'multiselect',
				'options'  =>  $product_tags_as_options,
				'class'    => 'chosen_select',
			),
		) );
	}
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_currency_per_product_currencies_options',
	),
	array(
		'title'    => __( 'Advanced Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_currency_per_product_advanced_options',
	),
	array(
		'title'    => __( 'Advanced: Save Calculated Products Prices', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This may help if you are experiencing compatibility issues with other plugins. If you are facing your price will not be displayed properly then enable this option.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_currency_per_product_save_prices',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_currency_per_product_advanced_options',
	),
) );
return $settings;
