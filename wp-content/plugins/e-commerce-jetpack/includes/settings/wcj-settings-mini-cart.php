<?php
/**
 * Booster for WooCommerce - Settings - Mini Cart Custom Info
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Mini Cart Custom Info Blocks', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_mini_cart_custom_info_options',
	),
	array(
		'title'    => __( 'Total Blocks', 'e-commerce-jetpack' ),
		'id'       => 'wcj_mini_cart_custom_info_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_mini_cart_custom_info_options',
	),
);
for ( $i = 1; $i <= apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_mini_cart_custom_info_total_number', 1 ) ); $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Info Block', 'e-commerce-jetpack' ) . ' #' . $i,
			'type'     => 'title',
			'id'       => 'wcj_mini_cart_custom_info_options_' . $i,
		),
		array(
			'title'    => __( 'Content', 'e-commerce-jetpack' ),
			'id'       => 'wcj_mini_cart_custom_info_content_' . $i,
			'default'  => '[wcj_cart_items_total_weight before="Total weight: " after=" kg"]',
			'type'     => 'textarea',
			'css'      => 'width:100%;height:100px;',
		),
		array(
			'title'    => __( 'Position', 'e-commerce-jetpack' ),
			'id'       => 'wcj_mini_cart_custom_info_hook_' . $i,
			'default'  => 'woocommerce_after_mini_cart',
			'type'     => 'select',
			'options'  => array(
				'woocommerce_before_mini_cart'                    => __( 'Before mini cart', 'e-commerce-jetpack' ),
				'woocommerce_widget_shopping_cart_before_buttons' => __( 'Before buttons', 'e-commerce-jetpack' ),
				'woocommerce_after_mini_cart'                     => __( 'After mini cart', 'e-commerce-jetpack' ),
			),
		),
		array(
			'title'    => __( 'Position Order (i.e. Priority)', 'e-commerce-jetpack' ),
			'id'       => 'wcj_mini_cart_custom_info_priority_' . $i,
			'default'  => 10,
			'type'     => 'number',
			'desc'    => __( 'Change the Priority to sequence of your custom blocks, Greater value for high priority & Lower value for low priority.', 'e-commerce-jetpack' ),
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'wcj_mini_cart_custom_info_options_' . $i,
		),
	) );
}
return $settings;
