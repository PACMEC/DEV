<?php
/**
 * Booster for WooCommerce - Settings - Shipping Options
 *
 * @version 5.2.0
 * @since   2.9.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Hide if Free Shipping is Available', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you hide other shipping options when free shipping is available on shop frontend.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_hide_if_free_available_options',
	),
	array(
		'title'    => __( 'Hide when free is available', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable section', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_hide_if_free_available_all',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'id'       => 'wcj_shipping_hide_if_free_available_type',
		'desc_tip' => sprintf( __( 'Available options: hide all; hide all except "Local Pickup"; hide "Flat Rate" only.', 'e-commerce-jetpack' ) ),
		'default'  => 'hide_all',
		'type'     => 'select',
		'options'  => array(
			'hide_all'            => __( 'Hide all', 'e-commerce-jetpack' ),
			'except_local_pickup' => __( 'Hide all except "Local Pickup"', 'e-commerce-jetpack' ),
			'flat_rate_only'      => __( 'Hide "Flat Rate" only', 'e-commerce-jetpack' ),
		),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Advanced: Filter Priority', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set to zero to use the default priority.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_options_hide_free_shipping_filter_priority',
		'default'  => 0,
		'type'     => 'number',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_shipping_hide_if_free_available_options',
	),
);
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Free Shipping by Product', 'e-commerce-jetpack' ),
		'desc'     => __( 'In this section you can select products which grant free shipping when added to cart.', 'e-commerce-jetpack' ) . '<br>' .
			sprintf( __( 'Similar results can be achieved with %s module.', 'e-commerce-jetpack' ),
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=jetpack&wcj-cat=shipping_and_orders&section=shipping_by_products' ) . '">' .
					__( 'Shipping Methods by Products', 'e-commerce-jetpack' ) . '</a>' ),
		'type'     => 'title',
		'id'       => 'wcj_shipping_free_shipping_by_product_options',
	),
	array(
		'title'    => __( 'Free Shipping by Product', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_free_shipping_by_product_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Products', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_free_shipping_by_product_products',
		'default'  => '',
		'type'     => 'multiselect',
		'options'  => wcj_get_products(),
		'class'    => 'chosen_select',
	),
	array(
		'title'    => __( 'Type', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Select either <strong>all products</strong> or <strong>at least one product</strong> in cart must grant free shipping.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_free_shipping_by_product_type',
		'default'  => 'all',
		'type'     => 'select',
		'options'  => array(
			'all'          => __( 'All products in cart must grant free shipping', 'e-commerce-jetpack' ),
			'at_least_one' => __( 'At least one product in cart must grant free shipping', 'e-commerce-jetpack' ),
		),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_shipping_free_shipping_by_product_options',
	),
) );
$shipping_methods_opt = array_map( function ( $item ) {
	return $item->method_title;
}, WC()->shipping->get_shipping_methods() );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Show Only the Most Expensive Shipping', 'e-commerce-jetpack' ),
		'desc'     => __( 'In this section you can show only the most expensive shipping, ignoring other ones as you wish, like free shipping or local pickup.', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_shipping_most_expensive',
	),
	array(
		'title'    => __( 'Show Only the Most Expensive Shipping', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_most_expensive_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Ignored Shipping Methods', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_most_expensive_ignored_methods',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc_tip' => __( 'Use it if you\'d like to show the most expensive shipping method ignoring some other one.', 'e-commerce-jetpack' ),
		'default'  => array( 'free_shipping' ),
		'type'     => 'multiselect',
		'options'  => $shipping_methods_opt,
		'class'    => 'chosen_select',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_shipping_most_expensive',
	),
) );
return $settings;


