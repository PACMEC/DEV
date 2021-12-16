<?php
/**
 * Booster for WooCommerce - Settings Meta Box - Add to Cart Button Visibility
 *
 * @version 3.3.0
 * @since   3.3.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'name'       => 'wcj_add_to_cart_button_disable',
		'default'    => 'no',
		'type'       => 'select',
		'options'    => array(
			'yes' => __( 'Hide', 'e-commerce-jetpack' ),
			'no'  => __( 'Show', 'e-commerce-jetpack' ),
		),
		'title'      => __( 'Single Product Page', 'e-commerce-jetpack' ),
	),
	array(
		'name'       => 'wcj_add_to_cart_button_disable_content',
		'default'    => '',
		'type'       => 'textarea',
		'title'      => '',
		'css'        => 'width:100%;',
		'tooltip'    => __( 'Content to replace add to cart button on single product page.', 'e-commerce-jetpack' ) . ' ' .
			__( 'You can use HTML and/or shortcodes here.', 'e-commerce-jetpack' ),
	),
	array(
		'name'       => 'wcj_add_to_cart_button_loop_disable',
		'default'    => 'no',
		'type'       => 'select',
		'options'    => array(
			'yes' => __( 'Hide', 'e-commerce-jetpack' ),
			'no'  => __( 'Show', 'e-commerce-jetpack' ),
		),
		'title'      => __( 'Category/Archives', 'e-commerce-jetpack' ),
	),
	array(
		'name'       => 'wcj_add_to_cart_button_loop_disable_content',
		'default'    => '',
		'type'       => 'textarea',
		'title'      => '',
		'css'        => 'width:100%;',
		'tooltip'    => __( 'Content to replace add to cart button on category/archives.', 'e-commerce-jetpack' ) . ' ' .
			__( 'You can use HTML and/or shortcodes here.', 'e-commerce-jetpack' ),
	),
);
