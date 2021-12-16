<?php
/**
 * Booster for WooCommerce - Settings - Upsells
 *
 * @version 3.6.0
 * @since   3.5.3
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_upsells_options',
	),
	array(
		'title'    => __( 'Upsells Total', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set to zero for WooCommerce default.', 'e-commerce-jetpack' ) . ' ' . __( 'Set to -1 for unlimited.', 'e-commerce-jetpack' ),
		'type'     => 'number',
		'id'       => 'wcj_upsells_total',
		'default'  => 0,
		'custom_attributes' => array( 'min' => -1 ),
	),
	array(
		'title'    => __( 'Upsells Columns', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set to zero for WooCommerce default.', 'e-commerce-jetpack' ),
		'type'     => 'number',
		'id'       => 'wcj_upsells_columns',
		'default'  => 0,
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'title'    => __( 'Upsells Order By', 'e-commerce-jetpack' ),
		'type'     => 'select',
		'id'       => 'wcj_upsells_orderby',
		'default'  => 'no_changes',
		'options'  => array(
			'no_changes'  => __( 'No changes (default behaviour)', 'e-commerce-jetpack' ),
			'rand'        => __( 'Random', 'e-commerce-jetpack' ),
			'title'       => __( 'Title', 'e-commerce-jetpack' ),
			'id'          => __( 'ID', 'e-commerce-jetpack' ),
			'date'        => __( 'Date', 'e-commerce-jetpack' ),
			'modified'    => __( 'Modified', 'e-commerce-jetpack' ),
			'menu_order'  => __( 'Menu order', 'e-commerce-jetpack' ),
			'price'       => __( 'Price', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Upsells Position', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Upsells position on single product page.', 'e-commerce-jetpack' ),
		'type'     => 'select',
		'id'       => 'wcj_upsells_position',
		'default'  => 'no_changes',
		'options'  => array(
			'no_changes'                                => __( 'No changes (default)', 'e-commerce-jetpack' ),
			'woocommerce_before_single_product'         => __( 'Before single product', 'e-commerce-jetpack' ),
			'woocommerce_before_single_product_summary' => __( 'Before single product summary', 'e-commerce-jetpack' ),
			'woocommerce_single_product_summary'        => __( 'Inside single product summary', 'e-commerce-jetpack' ),
			'woocommerce_after_single_product_summary'  => __( 'After single product summary', 'e-commerce-jetpack' ),
			'woocommerce_after_single_product'          => __( 'After single product', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Position priority', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Ignored if "Upsells Position" option above is set to "No changes (default)".', 'e-commerce-jetpack' ),
		'type'     => 'number',
		'id'       => 'wcj_upsells_position_priority',
		'default'  => 15,
	),
	array(
		'title'    => __( 'Hide Upsells', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'type'     => 'checkbox',
		'id'       => 'wcj_upsells_hide',
		'default'  => 'no',
	),
	array(
		'title'    => __( 'Global Upsells', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Enable this section if you want to add same upsells to all products.', 'e-commerce-jetpack' ) . ' ' .
			apply_filters( 'booster_message', '', 'desc' ),
		'type'     => 'checkbox',
		'id'       => 'wcj_upsells_global_enabled',
		'default'  => 'no',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'desc'     => __( 'Global upsells', 'e-commerce-jetpack' ),
		'type'     => 'multiselect',
		'id'       => 'wcj_upsells_global_ids',
		'default'  => '',
		'class'    => 'chosen_select',
		'options'  => wcj_get_products(),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_upsells_options',
	),
);
return $settings;
