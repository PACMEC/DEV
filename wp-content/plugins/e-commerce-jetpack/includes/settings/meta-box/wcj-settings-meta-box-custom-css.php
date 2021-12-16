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
		'title'   => __( 'Add CSS', 'e-commerce-jetpack' ),
		'name'    => 'wcj_product_css_enabled',
		'default' => 'no',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Yes', 'e-commerce-jetpack' ),
			'no'  => __( 'No', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'   => __( 'CSS', 'e-commerce-jetpack' ),
		'name'    => 'wcj_product_css',
		'default' => wcj_get_option( 'wcj_custom_css_per_product_default_value', '' ),
		'type'    => 'textarea',
		'css'     => 'width:100%;min-height:100px;',
	),
);
