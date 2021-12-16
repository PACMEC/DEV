<?php
/**
 * Booster for WooCommerce - Settings Meta Box - Product Images
 *
 * @version 4.1.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'name'    => 'wcj_product_images_meta_custom_on_single',
		'default' => '',
		'type'    => 'textarea',
		'title'   => __( 'Replace image with custom HTML on single product page', 'e-commerce-jetpack' ),
		'tooltip' => __( 'You can use shortcodes here.', 'e-commerce-jetpack' ),
		'css'     => 'width:100%;height:75px;',
	),
	array(
		'name'    => 'wcj_product_images_meta_custom_on_archives',
		'default' => '',
		'type'    => 'textarea',
		'title'   => __( 'Replace image with custom HTML on archives', 'e-commerce-jetpack' ),
		'tooltip' => __( 'You can use shortcodes here.', 'e-commerce-jetpack' ),
		'css'     => 'width:100%;height:75px;',
	),
	array(
		'name'    => 'wcj_product_images_hide_image_on_single',
		'default' => 'no',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Yes', 'e-commerce-jetpack' ),
			'no'  => __( 'No', 'e-commerce-jetpack' ),
		),
		'title'   => __( 'Hide Image on Single', 'e-commerce-jetpack' ),
	),
	array(
		'name'    => 'wcj_product_images_hide_thumb_on_single',
		'default' => 'no',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Yes', 'e-commerce-jetpack' ),
			'no'  => __( 'No', 'e-commerce-jetpack' ),
		),
		'title'   => __( 'Hide Thumbnails on Single', 'e-commerce-jetpack' ),
	),
	array(
		'name'    => 'wcj_product_images_hide_image_on_archives',
		'default' => 'no',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Yes', 'e-commerce-jetpack' ),
			'no'  => __( 'No', 'e-commerce-jetpack' ),
		),
		'title'   => __( 'Hide Image on Archives', 'e-commerce-jetpack' ),
	),
);
