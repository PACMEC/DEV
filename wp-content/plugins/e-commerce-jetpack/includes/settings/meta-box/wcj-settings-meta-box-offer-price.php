<?php
/**
 * Booster for WooCommerce - Settings Meta Box - Offer Price
 *
 * @version 2.9.0
 * @since   2.9.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Enable Offer Price', 'e-commerce-jetpack' ),
		'name'     => 'wcj_offer_price_enabled',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'yes' => __( 'Yes', 'e-commerce-jetpack' ),
			'no'  => __( 'No', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Price Step', 'e-commerce-jetpack' ),
		'tooltip'  => __( 'Number of decimals', 'woocommerce' ) . '. ' . __( 'Leave blank to use global value.', 'e-commerce-jetpack' ),
		'name'     => 'wcj_offer_price_price_step',
		'default'  => '',
		'type'     => 'number',
		'placeholder' => wcj_get_option( 'wcj_offer_price_price_step', wcj_get_option( 'woocommerce_price_num_decimals' ) ),
		'custom_attributes' => 'min="0"',
	),
	array(
		'title'    => __( 'Minimal Price', 'e-commerce-jetpack' ),
		'tooltip'  => __( 'Leave blank to use global value.', 'e-commerce-jetpack' ),
		'name'     => 'wcj_offer_price_min_price',
		'default'  => '',
		'type'     => 'number',
		'placeholder' => wcj_get_option( 'wcj_offer_price_min_price', 0 ),
		'custom_attributes' => 'min="0"',
	),
	array(
		'title'    => __( 'Maximal Price', 'e-commerce-jetpack' ),
		'tooltip'  => __( 'Set zero to disable.', 'e-commerce-jetpack' ) . ' ' . __( 'Leave blank to use global value.', 'e-commerce-jetpack' ),
		'name'     => 'wcj_offer_price_max_price',
		'default'  => '',
		'type'     => 'number',
		'placeholder' => wcj_get_option( 'wcj_offer_price_max_price', 0 ),
		'custom_attributes' => 'min="0"',
	),
	array(
		'title'    => __( 'Default Price', 'e-commerce-jetpack' ),
		'tooltip'  => __( 'Set zero to disable.', 'e-commerce-jetpack' ) . ' ' . __( 'Leave blank to use global value.', 'e-commerce-jetpack' ),
		'name'     => 'wcj_offer_price_default_price',
		'default'  => '',
		'type'     => 'number',
		'placeholder' => wcj_get_option( 'wcj_offer_price_default_price', 0 ),
		'custom_attributes' => 'min="0"',
	),
);