<?php
/**
 * Booster for WooCommerce - Settings Meta Box - Product Addons
 *
 * @version 4.7.1
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$options = array(
	array(
		'name'       => 'wcj_product_addons_per_product_settings_enabled',
		'default'    => 'no',
		'type'       => 'select',
		'options'    => array(
			'yes' => __( 'Yes', 'e-commerce-jetpack' ),
			'no'  => __( 'No', 'e-commerce-jetpack' ),
		),
		'title'      => __( 'Enabled', 'e-commerce-jetpack' ),
	),
	array(
		'name'       => 'wcj_product_addons_per_product_total_number',
		'tooltip'    => __( 'Save product after you change this number.', 'e-commerce-jetpack' ),
		'default'    => 0,
		'type'       => 'number',
		'title'      => __( 'Product Addons Total Number', 'e-commerce-jetpack' ),
	),
);

if ( ! function_exists( 'wcj_get_product_addons_enable_by_variation_option' ) ) {
	/**
	 * wcj_get_product_addons_enable_by_variation_option.
	 *
	 * @version 4.6.1
	 * @since   4.6.1
	 */
	function wcj_get_product_addons_enable_by_variation_option( $index ) {
		if (
			empty( $post_id = get_the_ID() ) ||
			! is_a( $product = wc_get_product( $post_id ), 'WC_Product_Variable' )
		) {
			return false;
		}
		$variations             = $product->get_available_variations();
		$variations_options     = wp_list_pluck( $variations, 'variation_id', 'variation_id' );
		$custom_attributes_data = apply_filters( 'booster_message', '', 'disabled' );
		$custom_attributes      = array();
		if ( is_array( $custom_attributes_data ) ) {
			foreach ( $custom_attributes_data as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}
		return array(
			'name'              => 'wcj_product_addons_per_product_enable_by_variation_' . $index,
			'type'              => 'select',
			'multiple'          => true,
			'tooltip'           => apply_filters( 'booster_message', '', 'desc' ),
			'css'               => 'width:100%',
			'class'             => 'chosen_select',
			'options'           => $variations_options,
			'custom_attributes' => implode( ' ', $custom_attributes ),
			'title'             => __( 'Enable by Variation', 'e-commerce-jetpack' ),
		);
	}
}

$total_number = get_post_meta( get_the_ID(), '_' . 'wcj_product_addons_per_product_total_number', true );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$options = array_merge( $options, array(
		array(
			'title'    => __( 'Product Addon', 'e-commerce-jetpack' ) . ' #' . $i . ' - ' . __( 'Enable', 'e-commerce-jetpack' ),
			'name'     => 'wcj_product_addons_per_product_enabled_' . $i,
			'default'  => 'yes',
			'type'     => 'select',
			'options'  => array(
				'yes' => __( 'Yes', 'e-commerce-jetpack' ),
				'no'  => __( 'No', 'e-commerce-jetpack' ),
			),
		),wcj_get_product_addons_enable_by_variation_option($i),
		array(
			'title'    => __( 'Type', 'e-commerce-jetpack' ),
			'name'     => 'wcj_product_addons_per_product_type_' . $i,
			'default'  => 'checkbox',
			'type'     => 'select',
			'options'  => array(
				'checkbox' => __( 'Checkbox', 'e-commerce-jetpack' ),
				'radio'    => __( 'Radio Buttons', 'e-commerce-jetpack' ),
				'select'   => __( 'Select Box', 'e-commerce-jetpack' ),
				'text'     => __( 'Text', 'e-commerce-jetpack' ),
			),
		),
		array(
			'title'    => __( 'Title', 'e-commerce-jetpack' ),
			'name'     => 'wcj_product_addons_per_product_title_' . $i,
			'default'  => '',
			'type'     => 'textarea',
			'css'      => 'width:100%;',
		),
		array(
			'title'    => __( 'Label(s)', 'e-commerce-jetpack' ),
			'tooltip'  => __( 'For radio and select enter one value per line.', 'e-commerce-jetpack' ),
			'name'     => 'wcj_product_addons_per_product_label_' . $i,
			'default'  => '',
			'type'     => 'textarea',
			'css'      => 'width:100%;height:100px;',
		),
		array(
			'title'    => __( 'Price(s)', 'e-commerce-jetpack' ),
			'tooltip'  => __( 'For radio and select enter one value per line.', 'e-commerce-jetpack' ) . '<br /><br />' . __( "You can use the % symbol to set a percentage of product's price, like 10%", 'e-commerce-jetpack' ),
			'name'     => 'wcj_product_addons_per_product_price_' . $i,
			'default'  => 0,
			'type'     => 'textarea',
			'css'      => 'height:100px;',
		),
		array(
			'title'    => __( 'Tooltip(s)', 'e-commerce-jetpack' ),
			'tooltip'  => __( 'For radio enter one value per line.', 'e-commerce-jetpack' ),
			'name'     => 'wcj_product_addons_per_product_tooltip_' . $i,
			'default'  => '',
			'type'     => 'textarea',
			'css'      => 'width:100%;height:100px;',
		),
		array(
			'title'    => __( 'Default Value', 'e-commerce-jetpack' ),
			'tooltip'  => __( 'For checkbox use \'checked\'; for radio and select enter default label. Leave blank for no default value.', 'e-commerce-jetpack' ),
			'name'     => 'wcj_product_addons_per_product_default_' . $i,
			'default'  => '',
			'type'     => 'text',
			'css'      => 'width:100%;',
		),
		array(
			'title'    => __( 'Placeholder', 'e-commerce-jetpack' ),
			'tooltip'  => __( 'For "Select Box" type only.', 'e-commerce-jetpack' ),
			'name'     => 'wcj_product_addons_per_product_placeholder_' . $i,
			'default'  => '',
			'type'     => 'text',
			'css'      => 'width:100%;',
		),
		array(
			'title'    => __( 'HTML Class', 'e-commerce-jetpack' ),
			'name'     => 'wcj_product_addons_per_product_class_' . $i,
			'default'  => '',
			'type'     => 'text',
			'css'      => 'width:100%;',
		),
		array(
			'title'    => __( 'Is required', 'e-commerce-jetpack' ),
			'name'     => 'wcj_product_addons_per_product_required_' . $i,
			'default'  => 'no',
			'type'     => 'select',
			'options'  => array(
				'no'  => __( 'No', 'e-commerce-jetpack' ),
				'yes' => __( 'Yes', 'e-commerce-jetpack' ),
			),
		),
		array(
			'title'    => __( 'Quantity', 'e-commerce-jetpack' ),
			'tooltip'  => __( 'Leave empty to disable quantity calculation for the addon. When set to zero - addon will be disabled.', 'e-commerce-jetpack' ),
			'name'     => 'wcj_product_addons_per_product_qty_' . $i,
			'default'  => '',
			'type'     => 'text',
		),
	) );
}
return $options;
