<?php
/**
 * Booster for WooCommerce - Settings - Order Numbers
 *
 * @version 5.1.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    (maybe) add `wcj_order_number_counter_previous_order_date` as `hidden` field (for proper module reset)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Order Numbers', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you enable sequential order numbering, set custom number prefix, suffix and width.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_numbers_options',
	),
	array(
		'title'    => __( 'Number Generation', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_sequential_enabled',
		'default'  => 'yes',
		'type'     => 'select',
		'options'  => array(
			'yes'        => __( 'Sequential', 'e-commerce-jetpack' ),
			'no'         => __( 'Order ID', 'e-commerce-jetpack' ),
			'hash_crc32' => __( 'Pseudorandom - Hash (max 10 digits)', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Sequential: Next Order Number', 'e-commerce-jetpack' ),
		'desc'     => '<br>' . __( 'Next new order will be given this number.', 'e-commerce-jetpack' ) . ' ' . __( 'Use Renumerate Orders tool for existing orders.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will be ignored if sequential order numbering is disabled.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_counter',
		'default'  => 1,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'Sequential: Reset Counter', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will be ignored if sequential order numbering is disabled.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_counter_reset_enabled',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'no'      => __( 'Disabled', 'e-commerce-jetpack' ),
			'daily'   => __( 'Daily', 'e-commerce-jetpack' ),
			'monthly' => __( 'Monthly', 'e-commerce-jetpack' ),
			'yearly'  => __( 'Yearly', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Order Number Custom Prefix', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Prefix before order number (optional). This will change the prefixes for all existing orders.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_prefix',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Order Number Date Prefix', 'e-commerce-jetpack' ),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'desc_tip' => __( 'Date prefix before order number (optional). This will change the prefixes for all existing orders. Value is passed directly to PHP `date` function, so most of PHP date formats can be used. The only exception is using `\` symbol in date format, as this symbol will be excluded from date. Try: Y-m-d- or mdy.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_date_prefix',
		'default'  => '',
		'type'     => 'text',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Order Number Width', 'e-commerce-jetpack' ),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'desc_tip' => __( 'Minimum width of number without prefix (zeros will be added to the left side). This will change the minimum width of order number for all existing orders. E.g. set to 5 to have order number displayed as 00001 instead of 1. Leave zero to disable.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_min_width',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Order Number Custom Suffix', 'e-commerce-jetpack' ),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'desc_tip' => __( 'Suffix after order number (optional). This will change the suffixes for all existing orders.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_suffix',
		'default'  => '',
		'type'     => 'text',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Order Number Date Suffix', 'e-commerce-jetpack' ),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'desc_tip' => __( 'Date suffix after order number (optional). This will change the suffixes for all existing orders. Value is passed directly to PHP `date` function, so most of PHP date formats can be used. The only exception is using `\` symbol in date format, as this symbol will be excluded from date. Try: Y-m-d- or mdy.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_date_suffix',
		'default'  => '',
		'type'     => 'text',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Use MySQL Transaction', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This should be enabled if you have a lot of simultaneous orders in your shop - to prevent duplicate order numbers (sequential).', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_use_mysql_transaction_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Enable Order Tracking by Custom Number', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_order_tracking_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Enable Order Admin Search by Custom Number', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_search_by_custom_number_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Enable Editable Order Number Meta Box', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_number_editable_order_number_meta_box_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'desc_tip' => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Minimal Order ID', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you wish to disable order numbering for some (older) orders, you can set order ID to start here.', 'e-commerce-jetpack' ) . ' ' .
			__( 'Set to zero to enable numbering for all orders.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_numbers_min_order_id',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_order_numbers_options',
	),
	array(
		'title'    => __( 'Compatibility', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_order_numbers_compatibility',
	),
	array(
		'title'             => __( 'WPNotif', 'e-commerce-jetpack' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => sprintf( __( 'Adds compatibility with <a href="%s" target="_blank">WPNotif: WordPress SMS & WhatsApp Notifications</a> plugin fixing the <code>{{wc-tracking-link}}</code> variable.', 'e-commerce-jetpack' ), 'https://wpnotif.unitedover.com/' ),
		'id'                => 'wcj_order_numbers_compatibility_wpnotif',
		'default'           => 'no',
		'type'              => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_order_numbers_compatibility',
	),
	array(
		'title'    => __( 'Orders Renumerate Tool Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_order_numbers_renumerate_tool_options',
	),
	array(
		'title'    => __( 'Sort by', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_numbers_renumerate_tool_orderby',
		'default'  => 'date',
		'type'     => 'select',
		'options'  => array(
			'ID'       => __( 'ID', 'e-commerce-jetpack' ),
			'date'     => __( 'Date', 'e-commerce-jetpack' ),
			'modified' => __( 'Last modified date', 'e-commerce-jetpack' ),
			'rand'     => __( 'Random', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Sort Ascending or Descending', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_numbers_renumerate_tool_order',
		'default'  => 'ASC',
		'type'     => 'select',
		'options'  => array(
			'ASC'  => __( 'Ascending', 'e-commerce-jetpack' ),
			'DESC' => __( 'Descending', 'e-commerce-jetpack' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_order_numbers_renumerate_tool_options',
	),
);
