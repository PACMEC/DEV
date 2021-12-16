<?php
/**
 * Booster for WooCommerce - Settings - Add to Cart Button Visibility
 *
 * @version 3.9.0
 * @since   3.3.0
 * @author  Pluggabl LLC.
 * @todo    "Per Tag"
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$products_cats = wcj_get_terms( 'product_cat' );

return array(
	array(
		'title'    => __( 'All Products', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_add_to_cart_button_visibility_global_options',
	),
	array(
		'title'    => __( 'All Products', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_add_to_cart_button_global_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Disable Buttons on Category/Archives Pages', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_disable_archives',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Advanced', 'e-commerce-jetpack' ) . ': ' . __( 'Method', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Method for disabling the buttons. Try changing if buttons are not being disabled (may happen with some themes).', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_disable_archives_method',
		'default'  => 'remove_action',
		'type'     => 'select',
		'options'  => array(
			'remove_action' => __( 'Remove action', 'e-commerce-jetpack' ),
			'add_filter'    => __( 'Add filter', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Content', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Content to replace with on archives (can be empty). You can use HTML and/or shortcodes here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_archives_content',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%',
	),
	array(
		'title'    => __( 'Disable Buttons on Single Product Pages', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_disable_single',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Advanced', 'e-commerce-jetpack' ) . ': ' . __( 'Method', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Method for disabling the buttons. Try changing if buttons are not being disabled (may happen with some themes).', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_disable_single_method',
		'default'  => 'remove_action',
		'type'     => 'select',
		'options'  => array(
			'remove_action' => __( 'Remove action', 'e-commerce-jetpack' ),
			'add_action'    => __( 'Add action', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Content', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Content to replace with on single product pages (can be empty). You can use HTML and/or shortcodes here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_single_content',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_add_to_cart_button_visibility_global_options',
	),
	array(
		'title'    => __( 'Per Product', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_add_to_cart_button_visibility_per_product_options',
	),
	array(
		'title'    => __( 'Per Product', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'desc_tip' => __( 'This will add meta box to each product\'s edit page', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_per_product_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_add_to_cart_button_visibility_per_product_options',
	),
	array(
		'title'    => __( 'Per Category', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_add_to_cart_button_visibility_per_category_options',
	),
	array(
		'title'    => __( 'Per Category', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_add_to_cart_button_per_category_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Disable Buttons on Category/Archives Pages', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_per_category_disable_loop',
		'default'  => '',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => $products_cats,
	),
	array(
		'desc'     => __( 'Content', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Content to replace with on archives (can be empty). You can use HTML and/or shortcodes here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_per_category_content_loop',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%',
	),
	array(
		'title'    => __( 'Disable Buttons on Single Product Pages', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_per_category_disable_single',
		'default'  => '',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => $products_cats,
	),
	array(
		'desc'     => __( 'Content', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Content to replace with on single product pages (can be empty). You can use HTML and/or shortcodes here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_per_category_content_single',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_add_to_cart_button_visibility_per_category_options',
	),
);
