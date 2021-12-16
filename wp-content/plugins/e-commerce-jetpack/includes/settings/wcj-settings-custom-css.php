<?php
/**
 * Booster for WooCommerce Settings - Custom CSS
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_custom_css_options',
	),
	array(
		'title'    => __( 'Code Position', 'e-commerce-jetpack' ),
		'id'       => 'wcj_custom_css_hook',
		'default'  => 'head',
		'type'     => 'select',
		'options'  => array(
			'head'   => __( 'Header', 'e-commerce-jetpack' ),
			'footer' => __( 'Footer', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Custom CSS - Front end (Customers)', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_custom_css',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;min-height:300px;font-family:monospace;',
		'desc'     => sprintf( __( 'Without the %s tag.', 'e-commerce-jetpack' ), '<code>' . esc_html( '<style></style>' ) . '</code>' )
	),
	array(
		'title'    => __( 'Custom CSS - Back end (Admin)', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_custom_admin_css',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;min-height:300px;font-family:monospace;',
		'desc'     => sprintf( __( 'Without the %s tag.', 'e-commerce-jetpack' ), '<code>' . esc_html( '<style></style>' ) . '</code>' )
	),
	array(
		'title'    => __( 'Custom CSS on per Product Basis', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set product specific CSS to be loaded only on specific product\'s single page.', 'e-commerce-jetpack' ) .
			' ' . __( 'This will add meta box to each product\'s edit page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_custom_css_per_product',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Custom CSS on per Product Basis - Default Field Value', 'e-commerce-jetpack' ),
		'id'       => 'wcj_custom_css_per_product_default_value',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;min-height:100px;font-family:monospace;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_custom_css_options',
	),
);
