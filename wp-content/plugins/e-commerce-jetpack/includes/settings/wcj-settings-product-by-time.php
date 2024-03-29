<?php
/**
 * Booster for WooCommerce - Settings - Product Availability by Time
 *
 * @version 2.9.1
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'All Products Options', 'e-commerce-jetpack' ),
		'desc'     => '<span id="local-time">' . sprintf( __( 'Local time is <code>%s</code>.', 'e-commerce-jetpack' ), date( 'l, H:i:s', $this->time_now ) ) . '</span>',
		'type'     => 'title',
		'id'       => 'wcj_product_by_time_all_products_options',
	),
	array(
		'title'    => __( 'Product by Time', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable', 'e-commerce-jetpack' ) . '</strong>',
		'desc_tip' => __( 'Time formats:', 'e-commerce-jetpack' ) . ' ' . '<code>HH:MM-HH:MM</code>' . ', ' . '<code>HH:MM-HH:MM,HH:MM-HH:MM</code>' . ', ' . '<code>-</code>' . '.',
		'id'       => 'wcj_product_by_time_section_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
);
$timestamp = strtotime( 'next Sunday' );
for ( $i = 0; $i < 7; $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => date_i18n( 'l', $timestamp ),
			'id'       => 'wcj_product_by_time_' . $i,
			'default'  => $this->get_default_time( $i ),
			'type'     => 'text',
			'css'      => 'width:300px;',
		),
	) );
	$timestamp = strtotime( '+1 day', $timestamp );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_by_time_all_products_options',
	),
	array(
		'title'    => __( 'Per Product Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_by_time_per_product_options',
	),
	array(
		'title'    => __( 'Per Product', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable', 'e-commerce-jetpack' ) . '</strong>',
		'desc_tip' => __( 'This will add new meta box to each product\'s edit page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_by_time_per_product_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_by_time_per_product_options',
	),
	array(
		'title'    => __( 'Frontend Messages Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_by_time_messages_options',
	),
	array(
		'title'    => __( 'Message', 'e-commerce-jetpack' ),
		'desc'     => __( 'Message when product is not available by time.', 'e-commerce-jetpack' ) .
			' ' . __( 'Replaceable values:', 'e-commerce-jetpack' ) . ' ' . '<code>%product_title%</code>' . ', ' . '<code>%time_today%</code>' . '.' .
			' ' . __( 'You can also use shortcodes here.', 'e-commerce-jetpack' ) .
			' ' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_product_by_time_unavailable_message',
		'default'  => __( '<p style="color:red;">%product_title% is available only at %time_today% today.</p>', 'e-commerce-jetpack' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:66%;min-width:300px;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Message (Day Off)', 'e-commerce-jetpack' ),
		'desc'     => __( 'Message when product is not available by time (day off).', 'e-commerce-jetpack' ) .
			' ' . __( 'Replaceable value:', 'e-commerce-jetpack' ) . ' ' . '<code>%product_title%</code>' . '.' .
			' ' . __( 'You can also use shortcodes here.', 'e-commerce-jetpack' ) .
			' ' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_product_by_time_unavailable_message_day_off',
		'default'  => __( '<p style="color:red;">%product_title% is not available today.</p>', 'e-commerce-jetpack' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:66%;min-width:300px;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_by_time_messages_options',
	),
) );
return $settings;
