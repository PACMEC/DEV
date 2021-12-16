<?php
/**
 * Booster for WooCommerce - Settings - Checkout Custom Info
 *
 * @version 3.3.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    [dev] clean up
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Checkout Custom Info Blocks', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_custom_info_blocks_options',
	),
	array(
		'title'    => __( 'Total Blocks', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_custom_info_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_custom_info_blocks_options',
	),
);
$total_number = apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_checkout_custom_info_total_number', 1 ) );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Info Block', 'e-commerce-jetpack' ) . ' #' . $i,
			'type'     => 'title',
			'id'       => 'wcj_checkout_custom_info_options_' . $i,
		),
		array(
			'title'    => __( 'Content', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_custom_info_content_' . $i,
			'default'  => '[wcj_cart_items_total_weight before="Total weight: " after=" kg"]',
			'type'     => 'textarea',
			'css'      => 'width:100%;height:200px;',
		),
		array(
			'title'    => __( 'Position', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_custom_info_hook_' . $i,
			'default'  => 'woocommerce_checkout_after_order_review',
			'type'     => 'select',
			'options'  => array(

				'woocommerce_before_checkout_form'              => __( 'Before checkout form', 'e-commerce-jetpack' ),
				'woocommerce_checkout_before_customer_details'  => __( 'Before customer details', 'e-commerce-jetpack' ),
				'woocommerce_checkout_billing'                  => __( 'Billing', 'e-commerce-jetpack' ),
				'woocommerce_checkout_shipping'                 => __( 'Shipping', 'e-commerce-jetpack' ),
				'woocommerce_checkout_after_customer_details'   => __( 'After customer details', 'e-commerce-jetpack' ),
				'woocommerce_checkout_before_order_review'      => __( 'Before order review', 'e-commerce-jetpack' ),
				'woocommerce_checkout_order_review'             => __( 'Order review', 'e-commerce-jetpack' ),
				'woocommerce_checkout_after_order_review'       => __( 'After order review', 'e-commerce-jetpack' ),
				'woocommerce_after_checkout_form'               => __( 'After checkout form', 'e-commerce-jetpack' ),
				/*
				'woocommerce_before_checkout_shipping_form'     => __( 'woocommerce_before_checkout_shipping_form', 'e-commerce-jetpack' ),
				'woocommerce_after_checkout_shipping_form'      => __( 'woocommerce_after_checkout_shipping_form', 'e-commerce-jetpack' ),
				'woocommerce_before_order_notes'                => __( 'woocommerce_before_order_notes', 'e-commerce-jetpack' ),
				'woocommerce_after_order_notes'                 => __( 'woocommerce_after_order_notes', 'e-commerce-jetpack' ),

				'woocommerce_before_checkout_billing_form'      => __( 'woocommerce_before_checkout_billing_form', 'e-commerce-jetpack' ),
				'woocommerce_after_checkout_billing_form'       => __( 'woocommerce_after_checkout_billing_form', 'e-commerce-jetpack' ),
				'woocommerce_before_checkout_registration_form' => __( 'woocommerce_before_checkout_registration_form', 'e-commerce-jetpack' ),
				'woocommerce_after_checkout_registration_form'  => __( 'woocommerce_after_checkout_registration_form', 'e-commerce-jetpack' ),

				'woocommerce_review_order_before_cart_contents' => __( 'woocommerce_review_order_before_cart_contents', 'e-commerce-jetpack' ),
				'woocommerce_review_order_after_cart_contents'  => __( 'woocommerce_review_order_after_cart_contents', 'e-commerce-jetpack' ),
				'woocommerce_review_order_before_shipping'      => __( 'woocommerce_review_order_before_shipping', 'e-commerce-jetpack' ),
				'woocommerce_review_order_after_shipping'       => __( 'woocommerce_review_order_after_shipping', 'e-commerce-jetpack' ),
				'woocommerce_review_order_before_order_total'   => __( 'woocommerce_review_order_before_order_total', 'e-commerce-jetpack' ),
				'woocommerce_review_order_after_order_total'    => __( 'woocommerce_review_order_after_order_total', 'e-commerce-jetpack' ),
				*/
				'woocommerce_thankyou'                          => __( 'Order Received (Thank You) page', 'e-commerce-jetpack' ),
			),
		),
		array(
			'title'    => __( 'Position Order (i.e. Priority)', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_custom_info_priority_' . $i,
			'default'  => 10,
			'type'     => 'number',
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'wcj_checkout_custom_info_options_' . $i,
		),
	) );
}
return $settings;
