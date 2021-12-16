<?php
/**
 * Booster for WooCommerce - Settings - Sorting
 *
 * @version 4.3.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'     => __( 'Add Custom Sorting', 'e-commerce-jetpack' ),
		'type'      => 'title',
		'id'        => 'wcj_more_sorting_options',
	),
	array(
		'title'     => __( 'Add More Sorting', 'e-commerce-jetpack' ),
		'desc'      => __( 'Enable Section', 'e-commerce-jetpack' ),
		'id'        => 'wcj_more_sorting_enabled',
		'default'   => 'yes',
		'type'      => 'checkbox',
	),
	array(
		'title'     => __( 'Sort by Name', 'e-commerce-jetpack' ),
		'desc'      => __( 'Default: ', 'e-commerce-jetpack' ) . __( 'Sort by title: A to Z', 'e-commerce-jetpack' ),
		'desc_tip'  => __( 'Text to show on frontend. Leave blank to disable.', 'e-commerce-jetpack' ),
		'id'        => 'wcj_sorting_by_name_asc_text',
		'default'   => __( 'Sort by title: A to Z', 'e-commerce-jetpack' ),
		'type'      => 'text',
		'css'       => 'min-width:300px;',
	),
	array(
		'title'     => '',//__( 'Sort by Name - Desc', 'e-commerce-jetpack' ),
		'desc'      => __( 'Default: ', 'e-commerce-jetpack' ) . __( 'Sort by title: Z to A', 'e-commerce-jetpack' ),
		'desc_tip'  => __( 'Text to show on frontend. Leave blank to disable.', 'e-commerce-jetpack' ),
		'id'        => 'wcj_sorting_by_name_desc_text',
		'default'   => __( 'Sort by title: Z to A', 'e-commerce-jetpack' ),
		'type'      => 'text',
		'css'       => 'min-width:300px;',
	),
	array(
		'title'     => __( 'Sort by SKU', 'e-commerce-jetpack' ),
		'desc'      => __( 'Default: ', 'e-commerce-jetpack' ) . __( 'Sort by SKU: low to high', 'e-commerce-jetpack' ),
		'desc_tip'  => __( 'Text to show on frontend. Leave blank to disable.', 'e-commerce-jetpack' ),
		'id'        => 'wcj_sorting_by_sku_asc_text',
		'default'   => __( 'Sort by SKU: low to high', 'e-commerce-jetpack' ),
		'type'      => 'text',
		'css'       => 'min-width:300px;',
	),
	array(
		'title'     => '',//__( 'Sort by SKU - Desc', 'e-commerce-jetpack' ),
		'desc'      => __( 'Default: ', 'e-commerce-jetpack' ) . __( 'Sort by SKU: high to low', 'e-commerce-jetpack' ),
		'desc_tip'  => __( 'Text to show on frontend. Leave blank to disable.', 'e-commerce-jetpack' ),
		'id'        => 'wcj_sorting_by_sku_desc_text',
		'default'   => __( 'Sort by SKU: high to low', 'e-commerce-jetpack' ),
		'type'      => 'text',
		'css'       => 'min-width:300px;',
	),
	array(
		'title'     => '',
		'desc'      => __( 'Sort SKUs as numbers instead of as texts', 'e-commerce-jetpack' ),
		'id'        => 'wcj_sorting_by_sku_num_enabled',
		'default'   => 'no',
		'type'      => 'checkbox',
		'desc_tip'  => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'     => __( 'Sort by stock quantity', 'e-commerce-jetpack' ),
		'desc'      => __( 'Default: ', 'e-commerce-jetpack' ) . __( 'Sort by stock quantity: low to high', 'e-commerce-jetpack' ),
		'desc_tip'  => __( 'Text to show on frontend. Leave blank to disable.', 'e-commerce-jetpack' ),
		'id'        => 'wcj_sorting_by_stock_quantity_asc_text',
		'default'   => __( 'Sort by stock quantity: low to high', 'e-commerce-jetpack' ),
		'type'      => 'text',
		'css'       => 'min-width:300px;',
	),
	array(
		'title'     => '',//__( 'Sort by stock quantity - Desc', 'e-commerce-jetpack' ),
		'desc'      => __( 'Default: ', 'e-commerce-jetpack' ) . __( 'Sort by stock quantity: high to low', 'e-commerce-jetpack' ),
		'desc_tip'  => __( 'Text to show on frontend. Leave blank to disable.', 'e-commerce-jetpack' ),
		'id'        => 'wcj_sorting_by_stock_quantity_desc_text',
		'default'   => __( 'Sort by stock quantity: high to low', 'e-commerce-jetpack' ),
		'type'      => 'text',
		'css'       => 'min-width:300px;',
	),
	array(
		'type'      => 'sectionend',
		'id'        => 'wcj_more_sorting_options',
	),
	array(
		'title'     => __( 'Rearrange Sorting', 'e-commerce-jetpack' ),
		'type'      => 'title',
		'id'        => 'wcj_sorting_rearrange_options',
	),
	array(
		'title'     => __( 'Rearrange Sorting', 'e-commerce-jetpack' ),
		'desc'      => __( 'Enable Section', 'e-commerce-jetpack' ),
		'id'        => 'wcj_sorting_rearrange_enabled',
		'default'   => 'no',
		'type'      => 'checkbox',
	),
	array(
		'title'     => __( 'Rearrange Sorting', 'e-commerce-jetpack' ),
		'id'        => 'wcj_sorting_rearrange',
		'desc_tip'  => __( 'Default:', 'e-commerce-jetpack' ) . '<br>' . implode( '<br>', $this->get_woocommerce_sortings_order() ),
		'default'   => implode( PHP_EOL, $this->get_woocommerce_sortings_order() ),
		'type'      => 'textarea',
		'css'       => 'min-height:300px;',
	),
	array(
		'type'      => 'sectionend',
		'id'        => 'wcj_sorting_rearrange_options',
	),
	array(
		'title'     => __( 'Default WooCommerce Sorting', 'e-commerce-jetpack' ),
		'type'      => 'title',
		'id'        => 'wcj_sorting_default_sorting_options',
	),
	array(
		'title'     => __( 'Default Sorting Options', 'e-commerce-jetpack' ),
		'desc'      => __( 'Enable Section', 'e-commerce-jetpack' ),
		'id'        => 'wcj_sorting_default_sorting_enabled',
		'default'   => 'no',
		'type'      => 'checkbox',
		'desc_tip'  => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
);
foreach ( $this->get_woocommerce_default_sortings() as $sorting_key => $sorting_desc ) {
	$option_key = str_replace( '-', '_', $sorting_key );
	$settings[] = array(
		'title'     => $sorting_desc,
		'id'        => 'wcj_sorting_default_sorting_' . $option_key,
		'default'   => $sorting_desc,
		'type'      => 'text',
		'css'       => 'min-width:300px;',
	);
	if ( 'menu_order' === $sorting_key ) {
		continue;
	}
	$settings[] = array(
		'desc'      => __( 'Remove', 'e-commerce-jetpack' ) . ' "' . $sorting_desc . '"',
		'id'        => 'wcj_sorting_default_sorting_' . $option_key . '_disable',
		'default'   => 'no',
		'type'      => 'checkbox',
	);
}
$settings = array_merge( $settings, array(
	array(
		'type'      => 'sectionend',
		'id'        => 'wcj_sorting_default_sorting_options',
	),
	array(
		'title'     => __( 'Remove All Sorting', 'e-commerce-jetpack' ),
		'type'      => 'title',
		'id'        => 'wcj_sorting_remove_all_options',
	),
	array(
		'title'     => __( 'Remove All Sorting', 'e-commerce-jetpack' ),
		'desc'      => __( 'Remove all sorting (including WooCommerce default) from shop\'s frontend', 'e-commerce-jetpack' ),
		'desc_tip'  => apply_filters( 'booster_message', '', 'desc' ),
		'id'        => 'wcj_sorting_remove_all_enabled',
		'default'   => 'no',
		'type'      => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'      => 'sectionend',
		'id'        => 'wcj_sorting_remove_all_options',
	),
	array(
		'title'     => __( 'Restore Default WooCommerce Sorting', 'e-commerce-jetpack' ),
		'desc'      => __( "Replaces theme's sorting by default WooCommerce sorting", 'e-commerce-jetpack' ),
		'type'      => 'title',
		'default'   => 'no',
		'id'        => 'wcj_sorting_restore_default_sorting_opt',
	),
	array(
		'title'     => __( 'Restore', 'e-commerce-jetpack' ),
		'desc'      => __( "Restore", 'e-commerce-jetpack' ),
		'type'      => 'checkbox',
		'id'        => 'wcj_sorting_restore_default_sorting',
	),
	array(
		'title'     => __( 'Theme', 'e-commerce-jetpack' ),
		'desc_tip'  => __( "Theme that will have its sorting replaced.", 'e-commerce-jetpack' ),
		'type'      => 'select',
		'options'   => array(
			'avada' => __( 'Avada', 'e-commerce-jetpack' ),
		),
		'default'=>'avada',
		'id'        => 'wcj_sorting_restore_default_sorting_theme',
	),
	array(
		'type'      => 'sectionend',
		'id'        => 'wcj_sorting_restore_default_sorting_opt',
	),
) );
return $settings;
