<?php
/**
 * Booster for WooCommerce - Settings - Products per Page
 *
 * @version 3.8.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_products_per_page_options',
	),
	array(
		'title'    => __( 'Select Options', 'e-commerce-jetpack' ),
		'desc'     => __( '<code>Name|Number</code>; one per line; <code>-1</code> for all products;', 'e-commerce-jetpack' ),
		'id'       => 'wcj_products_per_page_select_options',
		'default'  => implode( PHP_EOL, array( '10|10', '25|25', '50|50', '100|100', 'All|-1' ) ),
		'type'     => 'textarea',
		'css'      => 'height:200px;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
		'desc_tip' => apply_filters( 'booster_message', '', 'desc_no_link' ),
	),
	array(
		'title'    => __( 'Default', 'e-commerce-jetpack' ),
		'id'       => 'wcj_products_per_page_default',
		'default'  => 10,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => -1 ),
	),
	array(
		'title'    => __( 'Position', 'e-commerce-jetpack' ),
		'id'       => 'wcj_products_per_page_position',
		'default'  => array( 'woocommerce_before_shop_loop' ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => array(
			'woocommerce_before_shop_loop' => __( 'Before shop loop', 'e-commerce-jetpack' ),
			'woocommerce_after_shop_loop'  => __( 'After shop loop', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Position Priority', 'e-commerce-jetpack' ),
		'id'       => 'wcj_products_per_page_position_priority',
		'default'  => 40,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'title'    => __( 'Template - Before Form', 'e-commerce-jetpack' ),
		'id'       => 'wcj_products_per_page_text_before',
		'default'  => '<div class="clearfix"></div><div>',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%',
	),
	array(
		'title'    => __( 'Template - Form', 'e-commerce-jetpack' ),
		'id'       => 'wcj_products_per_page_text',
		'default'  => __( 'Products <strong>%from% - %to%</strong> from <strong>%total%</strong>. Products on page %select_form%', 'e-commerce-jetpack' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%',
	),
	array(
		'title'    => __( 'Template - After Form', 'e-commerce-jetpack' ),
		'id'       => 'wcj_products_per_page_text_after',
		'default'  => '</div>',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%',
	),
	array(
		'title'    => __( 'Form Method', 'e-commerce-jetpack' ),
		'id'       => 'wcj_products_per_page_form_method',
		'default'  => 'post',
		'type'     => 'select',
		'options'  => array(
			'post'  => __( 'POST', 'e-commerce-jetpack' ),
			'get' => __( 'GET', 'e-commerce-jetpack' ),
		)
	),
	array(
		'title'    => __( 'Saving Method', 'e-commerce-jetpack' ),
		'id'       => 'wcj_products_per_page_saving_method',
		'default'  => 'cookie',
		'type'     => 'select',
		'options'  => array(
			'cookie'  => __( 'Cookie', 'e-commerce-jetpack' ),
			'session' => __( 'Session', 'e-commerce-jetpack' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_products_per_page_options',
	),
);
