<?php
/**
 * Booster for WooCommerce - Settings - General
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Shortcodes Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_general_shortcodes_options',
	),
	array(
		'title'    => __( 'Shortcodes in WordPress Text Widgets', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will enable all (including non Booster\'s) shortcodes in WordPress text widgets.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_shortcodes_in_text_widgets_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Booster\'s Shortcodes', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Disable all <a href="%s" target="_blank">Booster\'s shortcodes</a> (for memory saving).', 'e-commerce-jetpack' ),
			'https://booster.io/shortcodes/' ),
		'desc'     => __( 'Disable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_shortcodes_disable_booster_shortcodes',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_general_shortcodes_options',
	),
	array(
		'title'    => __( 'Ip Detection', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_general_ip',
	),
	array(
		'title'    => __( 'Overwrite WooCommerce IP Detection', 'e-commerce-jetpack' ),
		'desc'     => __( 'Try to overwrite WooCommerce IP detection', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'The "HTTP_X_REAL_IP" param on $_SERVER variable will be replaced by IP detected from Booster', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_overwrite_wc_ip',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Detection Methods', 'e-commerce-jetpack' ),
		'desc'     => __( 'IP Detection Methods used by some Booster modules when not using IP detection from WooCommerce. Change order for different results.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Default values are:', 'e-commerce-jetpack' ).'<br />'.implode( PHP_EOL, array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ) ),
		'id'       => 'wcj_general_advanced_ip_detection',
		'default'  => implode( PHP_EOL, array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ) ),
		'type'     => 'textarea',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_general_ip',
	),
	array(
		'title'    => __( 'Advanced Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_general_advanced_options',
	),
	array(
		'title'    => __( 'Recalculate Cart Totals', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Will recalculate cart totals on every page load.', 'e-commerce-jetpack' ) . ' ' .
			__( 'This may solve multicurrency issues with wrong currency symbol in mini-cart.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_advanced_recalculate_cart_totals',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Session Type in Booster', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_advanced_session_type',
		'default'  => 'standard',
		'type'     => 'select',
		'options'  => array(
			'standard' => __( 'Standard PHP sessions', 'e-commerce-jetpack' ),
			'wc'       => __( 'WC sessions', 'e-commerce-jetpack' ),
		),
		'desc'     => __( 'If you are having issues with currency related modules, You can change the session type', 'e-commerce-jetpack' ),
	),
	array(
		'title'    => __( 'Read and Close', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable <strong>Read and Close</strong> parameter on <strong>session_start()</strong>.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Requires Session Type option set as Standard PHP Sessions and PHP version >= 7.0', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_advanced_session_read_and_close',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Datepicker/Weekpicker CSS Loading', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Disables datepicker/weekpicker CSS loading.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_advanced_disable_datepicker_css',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Datepicker/Weekpicker CSS Source', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_advanced_datepicker_css',
		'default'  => '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',
		'type'     => 'text',
		'css'      => 'width:66%;min-width:300px;',
	),
	array(
		'title'    => __( 'Datepicker/Weekpicker JavaScript Loading', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Disables datepicker/weekpicker JavaScript loading.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_advanced_disable_datepicker_js',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Timepicker CSS Loading', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Disables timepicker CSS loading.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_advanced_disable_timepicker_css',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Timepicker JavaScript Loading', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Disables timepicker JavaScript loading.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_advanced_disable_timepicker_js',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_general_advanced_options',
	),
	array(
		'title'    => __( 'PayPal Email per Product Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_paypal_email_per_product_options',
	),
	array(
		'title'    => __( 'PayPal Email per Product', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will add new meta box to each product\'s edit page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_paypal_email_per_product_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_paypal_email_per_product_options',
	),
	array(
		'title'    => __( 'Session Expiration Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_session_expiration_options',
	),
	array(
		'title'    => __( 'Session Expiration', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable Section', 'e-commerce-jetpack' ),
		'id'       => 'wcj_session_expiration_section_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Session Expiring', 'e-commerce-jetpack' ),
		'desc'     => __( 'In seconds. Default: 47 hours (60 * 60 * 47)', 'e-commerce-jetpack' ),
		'id'       => 'wcj_session_expiring',
		'default'  => 47 * 60 * 60,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'title'    => __( 'Session Expiration', 'e-commerce-jetpack' ),
		'desc'     => __( 'In seconds. Default: 48 hours (60 * 60 * 48)', 'e-commerce-jetpack' ),
		'id'       => 'wcj_session_expiration',
		'default'  => 48 * 60 * 60,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_session_expiration_options',
	),
	array(
		'title'    => __( 'Booster User Roles Changer Options', 'e-commerce-jetpack' ),
		'desc'     => __( 'This will add user roles changer tool to admin bar.', 'e-commerce-jetpack' )/*  . ' ' .
			__( 'You will be able to change user roles for Booster modules (e.g. when creating orders manually by admin for "Price based on User Role" module).', 'e-commerce-jetpack' ) */,
		'type'     => 'title',
		'id'       => 'wcj_general_user_role_changer_options',
	),
	array(
		'title'    => __( 'Booster User Roles Changer', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_user_role_changer_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'desc_tip' => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Enabled for', 'e-commerce-jetpack' ),
		'id'       => 'wcj_general_user_role_changer_enabled_for',
		'default'  => array( 'administrator', 'shop_manager' ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => wcj_get_user_roles_options(),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_general_user_role_changer_options',
	),
	array(
		'title'    => __( 'PHP Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_admin_tools_php_options',
	),
	array(
		'title'    => __( 'PHP Memory Limit', 'e-commerce-jetpack' ),
		'desc'     => __( 'megabytes.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set zero to disable.', 'e-commerce-jetpack' ) . $this->current_php_memory_limit,
		'id'       => 'wcj_admin_tools_php_memory_limit',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'title'    => __( 'PHP Time Limit', 'e-commerce-jetpack' ),
		'desc'     => __( 'seconds.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set zero to disable.', 'e-commerce-jetpack' ) . $this->current_php_time_limit,
		'id'       => 'wcj_admin_tools_php_time_limit',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_admin_tools_php_options',
	),
);
return $settings;
