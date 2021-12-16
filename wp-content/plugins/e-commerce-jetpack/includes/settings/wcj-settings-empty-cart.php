<?php
/**
 * Booster for WooCommerce Settings - Empty Cart Button
 *
 * @version 3.7.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'desc'     => __( 'You can also use <strong>[wcj_empty_cart_button]</strong> shortcode to place the button anywhere on your site.', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_empty_cart_customization_options',
	),
	array(
		'title'    => __( 'Empty Cart Button Text', 'e-commerce-jetpack' ),
		'id'       => 'wcj_empty_cart_text',
		'default'  => 'Empty Cart',
		'type'     => 'text',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Wrapping DIV style', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Style for the button\'s div. Default is "float: right;"', 'e-commerce-jetpack' ),
		'id'       => 'wcj_empty_cart_div_style',
		'default'  => 'float: right;',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Button HTML Class', 'e-commerce-jetpack' ),
		'id'       => 'wcj_empty_cart_button_class',
		'default'  => 'button',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Button position on the Cart page', 'e-commerce-jetpack' ),
		'id'       => 'wcj_empty_cart_position',
		'default'  => 'woocommerce_after_cart',
		'type'     => 'select',
		'options'  => array(
			'disable'                                    => __( 'Do not add', 'e-commerce-jetpack' ),
			'woocommerce_before_cart'                    => __( 'Before cart', 'e-commerce-jetpack' ),
			'woocommerce_before_cart_totals'             => __( 'Cart totals: Before cart totals', 'e-commerce-jetpack' ),
			'woocommerce_cart_totals_before_shipping'    => __( 'Cart totals: Before shipping', 'e-commerce-jetpack' ),
			'woocommerce_cart_totals_after_shipping'     => __( 'Cart totals: After shipping', 'e-commerce-jetpack' ),
			'woocommerce_cart_totals_before_order_total' => __( 'Cart totals: Before order total', 'e-commerce-jetpack' ),
			'woocommerce_cart_totals_after_order_total'  => __( 'Cart totals: After order total', 'e-commerce-jetpack' ),
			'woocommerce_proceed_to_checkout'            => __( 'Cart totals: After proceed to checkout button', 'e-commerce-jetpack' ),
			'woocommerce_after_cart_totals'              => __( 'Cart totals: After cart totals', 'e-commerce-jetpack' ),
			'woocommerce_cart_collaterals'               => __( 'After cart collaterals', 'e-commerce-jetpack' ),
			'woocommerce_after_cart'                     => __( 'After cart', 'e-commerce-jetpack' ),
		),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Button position on the Checkout page', 'e-commerce-jetpack' ),
		'id'       => 'wcj_empty_cart_checkout_position',
		'default'  => 'disable',
		'type'     => 'select',
		'options'  => array(
			'disable'                          => __( 'Do not add', 'e-commerce-jetpack' ),
			'woocommerce_before_checkout_form' => __( 'Before checkout form', 'e-commerce-jetpack' ),
			'woocommerce_after_checkout_form'  => __( 'After checkout form', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Confirmation', 'e-commerce-jetpack' ),
		'id'       => 'wcj_empty_cart_confirmation',
		'default'  => 'no_confirmation',
		'type'     => 'select',
		'options'  => array(
			'no_confirmation'         => __( 'No confirmation', 'e-commerce-jetpack' ),
			'confirm_with_pop_up_box' => __( 'Confirm by pop up box', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Confirmation Text (if enabled)', 'e-commerce-jetpack' ),
		'id'       => 'wcj_empty_cart_confirmation_text',
		'default'  => __( 'Are you sure?', 'e-commerce-jetpack' ),
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_empty_cart_customization_options',
	),
);
