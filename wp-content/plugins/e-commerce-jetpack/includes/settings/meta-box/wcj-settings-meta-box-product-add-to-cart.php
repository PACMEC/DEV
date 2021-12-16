<?php
/**
 * Booster for WooCommerce - Settings Meta Box - Product Add To Cart
 *
 * @version 3.3.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$options = array();
if ( 'yes' === apply_filters( 'booster_option', 'no', wcj_get_option( 'wcj_add_to_cart_redirect_per_product_enabled', 'no' ) ) ) {
	$options = array_merge( $options, array(
		array(
			'name'       => 'wcj_add_to_cart_redirect_enabled',
			'default'    => 'no',
			'type'       => 'select',
			'options'    => array(
				'yes' => __( 'Yes', 'e-commerce-jetpack' ),
				'no'  => __( 'No', 'e-commerce-jetpack' ),
			),
			'title'      => __( 'Add to Cart Local Redirect', 'e-commerce-jetpack' ),
		),
		array(
			'name'       => 'wcj_add_to_cart_redirect_url',
			'tooltip'    => __( 'Redirect URL. Leave empty to redirect to checkout page (skipping the cart page).', 'e-commerce-jetpack' ),
			'default'    => '',
			'type'       => 'text',
			'title'      => __( 'Add to Cart Local Redirect URL', 'e-commerce-jetpack' ),
			'css'        => 'width:100%;',
		),
	) );
}
if ( 'per_product' === wcj_get_option( 'wcj_add_to_cart_on_visit_enabled', 'no' ) ) {
	$options = array_merge( $options, array(
		array(
			'name'       => 'wcj_add_to_cart_on_visit_enabled',
			'default'    => 'no',
			'type'       => 'select',
			'options'    => array(
				'yes' => __( 'Yes', 'e-commerce-jetpack' ),
				'no'  => __( 'No', 'e-commerce-jetpack' ),
			),
			'title'      => __( 'Add to Cart on Visit', 'e-commerce-jetpack' ),
		),
	) );
}
if ( 'yes' === wcj_get_option( 'wcj_add_to_cart_button_custom_loop_url_per_product_enabled', 'no' ) ) {
	$options = array_merge( $options, array(
		array(
			'name'       => 'wcj_add_to_cart_button_loop_custom_url',
			'default'    => '',
			'type'       => 'text',
			'title'      => __( 'Custom Add to Cart Button URL (Category/Archives)', 'e-commerce-jetpack' ),
		),
	) );
}
if ( 'yes' === wcj_get_option( 'wcj_add_to_cart_button_ajax_per_product_enabled', 'no' ) ) {
	$options = array_merge( $options, array(
		array(
			'name'       => 'wcj_add_to_cart_button_ajax_disable',
			'default'    => 'as_shop_default',
			'type'       => 'select',
			'options'    => array(
				'as_shop_default' => __( 'As shop default (no changes)', 'e-commerce-jetpack' ),
				'yes'             => __( 'Disable', 'e-commerce-jetpack' ),
				'no'              => __( 'Enable', 'e-commerce-jetpack' ),
			),
			'title'      => __( 'Disable Add to Cart Button AJAX', 'e-commerce-jetpack' ),
		),
	) );
}
return $options;
