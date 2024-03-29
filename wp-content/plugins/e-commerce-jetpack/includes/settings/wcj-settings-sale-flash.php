<?php
/**
 * Booster for WooCommerce - Settings - Sale Flash
 *
 * @version 4.0.0
 * @since   3.2.4
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Globally', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_sale_flash_global_options',
	),
	array(
		'title'    => __( 'Globally', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_product_images_sale_flash_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Sale Flash', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'You can use HTML and/or shortcodes here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_sale_flash_html',
		'default'  => '<span class="onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>',
		'type'     => 'textarea',
		'css'      => 'width:100%',
	),
	array(
		'title'    => __( 'Hide Everywhere', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_sale_flash_hide_everywhere',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Hide on Archives (Categories) Only', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_sale_flash_hide_on_archives',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Hide on Single Page Only', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_sale_flash_hide_on_single',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_sale_flash_global_options',
	),
	array(
		'title'    => __( 'Per Product', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_sale_flash_per_product_options',
	),
	array(
		'title'    => __( 'Per Product', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'desc_tip' => __( 'This will add meta box to each product\'s edit page.', 'e-commerce-jetpack' ) . '<br>' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_sale_flash_per_product_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_sale_flash_per_product_options',
	),
);

$product_terms['product_cat'] = wcj_get_terms( 'product_cat' );
$product_terms['product_tag'] = wcj_get_terms( 'product_tag' );
foreach ( $product_terms as $id => $_product_terms ) {
	$title = ( 'product_cat' === $id ? __( 'Per Category', 'e-commerce-jetpack' ) : __( 'Per Tag', 'e-commerce-jetpack' ) );
	$settings = array_merge( $settings, array(
		array(
			'title'    => $title,
			'type'     => 'title',
			'id'       => 'wcj_sale_flash_per_' . $id . '_options',
		),
		array(
			'title'    => $title,
			'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
			'desc_tip' => apply_filters( 'booster_message', '', 'desc' ),
			'id'       => 'wcj_sale_flash_per_' . $id . '_enabled',
			'default'  => 'no',
			'type'     => 'checkbox',
			'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		),
		array(
			'desc_tip' => __( 'Terms to Modify', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Save changes to see new option fields.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_sale_flash_per_' . $id . '_terms',
			'default'  => array(),
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'options'  => $_product_terms,
		),
	) );
	foreach ( wcj_get_option( 'wcj_sale_flash_per_' . $id . '_terms', array() ) as $term_id ) {
		$settings = array_merge( $settings, array(
			array(
				'title'    => ( isset( $_product_terms[ $term_id ] ) ? $_product_terms[ $term_id ] : sprintf( __( 'Term #%s', 'e-commerce-jetpack' ), $term_id ) ),
				'desc_tip' => __( 'You can use HTML and/or shortcodes here.', 'e-commerce-jetpack' ),
				'id'       => "wcj_sale_flash_per_{$id}[{$term_id}]",
				'default'  => '<span class="onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
		) );
	}
	$settings = array_merge( $settings, array(
		array(
			'type'     => 'sectionend',
			'id'       => 'wcj_sale_flash_per_' . $id . '_options',
		),
	) );
}

return $settings;
