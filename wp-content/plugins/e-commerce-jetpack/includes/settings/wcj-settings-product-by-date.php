<?php
/**
 * Booster for WooCommerce - Settings - Product Availability by Date
 *
 * @version 5.3.8
 * @since   2.9.1
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'All Products Options', 'e-commerce-jetpack' ),
		'desc'     => '<span id="local-date">' . sprintf( __( 'Today is <code>%s</code>.', 'e-commerce-jetpack' ), date( 'F j', $this->time_now ) ) . '</span>',
		'type'     => 'title',
		'id'       => 'wcj_product_by_date_all_products_options',
	),
	array(
		'title'    => __( 'All Products', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable', 'e-commerce-jetpack' ) . '</strong>',
		'desc_tip' => __( 'Date formats:', 'e-commerce-jetpack' ) . ' ' . '<code>DD-DD</code>' . ', ' . '<code>DD-DD,DD-DD</code>' . ', ' . '<code>-</code>' . '.',
		'id'       => 'wcj_product_by_date_section_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
);
$_timestamp = 1; //  January 1 1970
for ( $i = 1; $i <= 12; $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => date_i18n( 'F', $_timestamp ),
			'id'       => 'wcj_product_by_date_' . $i,
			'default'  => $this->get_default_date( $i ),
			'type'     => 'text',
			'css'      => 'width:300px;',
		),
	) );
	$_timestamp = strtotime( '+1 month', $_timestamp );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_by_date_all_products_options',
	),
	array(
		'title'    => __( 'Per Product Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_by_date_per_product_options',
	),
	array(
		'title'    => __( 'Per Product', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable', 'e-commerce-jetpack' ) . '</strong>',
		'desc_tip' => __( 'This will add new meta box to each product\'s edit page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_by_date_per_product_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Direct Date Admin Input Date Format', 'e-commerce-jetpack' ),
		'desc'     => sprintf( __( 'E.g. %s.', 'e-commerce-jetpack' ), '<code>Y-m-d</code>' ),
		'desc_tip' => __( 'Leave blank to use the default date format.', 'e-commerce-jetpack' ) . '<br /><br />' . __( 'If you are not using english, please set some numeric format like m/d/Y', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_by_date_direct_date_format',
		'default'  => 'm/d/Y',
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_by_date_per_product_options',
	),
	array(
		'title'    => __( 'Frontend Messages Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_by_date_messages_options',
	),
	array(
		'title'    => __( 'Message (Monthly)', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Message when product is not available by date (monthly).', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%product_title%', '%date_this_month%' ) ) . '.' .
			' ' . __( 'You can also use shortcodes here.', 'e-commerce-jetpack' ) .
			' ' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_product_by_date_unavailable_message',
		'default'  => __( '<p style="color:red;">%product_title% is available only on %date_this_month% this month.</p>', 'e-commerce-jetpack' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Message (Monthly - Month Off)', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Message when product is not available by date (month off).', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%product_title%' ) ) . '.' .
			' ' . __( 'You can also use shortcodes here.', 'e-commerce-jetpack' ) .
			' ' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_product_by_date_unavailable_message_month_off',
		'default'  => __( '<p style="color:red;">%product_title% is not available this month.</p>', 'e-commerce-jetpack' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Message (Direct Date)', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Message when product is not available by direct date.', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%product_title%', '%direct_date%' ) ) . '.' .
			' ' . __( 'You can also use shortcodes here.', 'e-commerce-jetpack' ) .
			' ' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_product_by_date_unavailable_message_direct_date',
		'default'  => '<p style="color:red;">' . __( '%product_title% is not available until %direct_date%.', 'e-commerce-jetpack' ) . '</p>',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_by_date_messages_options',
	),
	array(
		'title'    => __( 'Advanced Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_by_date_advanced_options',
	),
	array(
		'title'    => __( 'Show Message on Category/shop Page', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Show Message on shop Page', 'e-commerce-jetpack' ) . '</strong>',
		'desc_tip' => __( 'Enable this if you also want to show message on shop page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_by_date_show_message_on_shop_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Action', 'e-commerce-jetpack' ),
		'desc'     => '<br>' . __( 'Action to be taken, when product is not available by date.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_by_date_action',
		'default'  => 'non_purchasable',
		'type'     => 'select',
		'options'  => array(
			'non_purchasable' => __( 'Make product non-purchasable', 'e-commerce-jetpack' ),
			'blank'           => __( 'Only output message', 'e-commerce-jetpack' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_by_date_advanced_options',
	),
) );
return $settings;
