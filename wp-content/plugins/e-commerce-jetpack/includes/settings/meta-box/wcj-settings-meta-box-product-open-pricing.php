<?php
/**
 * Booster for WooCommerce - Settings Meta Box - Custom CSS
 *
 * @version 2.8.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Enabled', 'e-commerce-jetpack' ),
		'name'     => 'wcj_product_open_price_enabled',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'yes' => __( 'Yes', 'e-commerce-jetpack' ),
			'no'  => __( 'No', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Default Price', 'e-commerce-jetpack' ) . ' (' . get_woocommerce_currency_symbol() . ')',
		'name'     => 'wcj_product_open_price_default_price',
		'default'  => '',
		'type'     => 'price',
		'custom_attributes' => 'min="0"',
	),
	array(
		'title'    => __( 'Min Price', 'e-commerce-jetpack' ) . ' (' . get_woocommerce_currency_symbol() . ')',
		'name'     => 'wcj_product_open_price_min_price',
		'default'  => 1,
		'type'     => 'price',
		'custom_attributes' => 'min="0"',
	),
	array(
		'title'    => __( 'Max Price', 'e-commerce-jetpack' ) . ' (' . get_woocommerce_currency_symbol() . ')',
		'name'     => 'wcj_product_open_price_max_price',
		'default'  => '',
		'type'     => 'price',
		'custom_attributes' => 'min="0"',
	),
);
