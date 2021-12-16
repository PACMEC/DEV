<?php
/**
 * Booster for WooCommerce Settings - Orders
 *
 * @version 4.0.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$bulk_regenerate_download_permissions_all_orders_cron_desc = '';
if ( $this->is_enabled() && 'yes' === apply_filters( 'booster_option', 'no', wcj_get_option( 'wcj_order_bulk_regenerate_download_permissions_enabled', 'no' ) ) ) {
	$bulk_regenerate_download_permissions_all_orders_cron_desc = wcj_crons_get_next_event_time_message( 'wcj_bulk_regenerate_download_permissions_all_orders_cron_time' );
}

$payment_gateways_options = array();
if ( function_exists( 'WC' ) && is_callable( array( WC()->payment_gateways, 'payment_gateways' ) ) ) {
	foreach ( WC()->payment_gateways->payment_gateways() as $payment_gateway_key => $payment_gateway_data ) {
		$payment_gateways_options[ $payment_gateway_key ] = $payment_gateway_data->title;
	}
}

$settings = array(
	array(
		'title'    => __( 'Admin Order Currency', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_order_admin_currency_options',
	),
	array(
		'title'    => __( 'Admin Order Currency', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'When enabled this will add "Booster: Orders" metabox to each order\'s edit page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_admin_currency',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Admin Order Currency Method', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Choose if you want changed order currency to be saved directly to DB, or if you want to use filter. When using <em>filter</em> method, changes will be active only when "Admin Order Currency" section is enabled. When using <em>directly to DB</em> method, changes will be permanent, that is even if Booster plugin is removed.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_admin_currency_method',
		'default'  => 'filter',
		'type'     => 'select',
		'options'  => array(
			'filter' => __( 'Filter', 'e-commerce-jetpack' ),
			'db'     => __( 'Directly to DB', 'e-commerce-jetpack' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_order_admin_currency_options',
	),
	array(
		'title'    => __( 'Admin Order Navigation', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_orders_navigation_options',
	),
	array(
		'title'    => __( 'Admin Order Navigation', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'When enabled, this will add "Booster: Order Navigation" metabox to each order\'s admin edit page.', 'e-commerce-jetpack' ) . ' ' .
			__( 'Metabox will contain "Previous order" and "Next order" links.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_navigation_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_orders_navigation_options',
	),
	array(
		'title'    => __( 'Editable Orders', 'e-commerce-jetpack' ),
		'desc'     => __( 'This section allows you to set which order statuses are editable.', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_orders_editable_status_options',
	),
	array(
		'title'    => __( 'Editable Orders Statuses', 'e-commerce-jetpack' ),
		'desc_tip' => apply_filters( 'booster_message', '', 'desc' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_editable_status_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'id'       => 'wcj_orders_editable_status',
		'default'  => array( 'pending', 'on-hold', 'auto-draft' ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => array_merge( wcj_get_order_statuses(), array( 'auto-draft' => __( 'Auto-draft', 'e-commerce-jetpack' ) ) ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_orders_editable_status_options',
	),
	array(
		'title'    => __( 'Orders Auto-Complete', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you enable orders auto-complete function.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_auto_complete_options',
	),
	array(
		'title'    => __( 'Auto-complete all WooCommerce orders', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'E.g. if you sell digital products then you are not shipping anything and you may want auto-complete all your orders.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_auto_complete_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Payment methods', 'e-commerce-jetpack' ) . '<br>' . apply_filters( 'booster_message', '', 'desc' ),
		'desc_tip' => __( 'Fill this, if you want orders to be auto-completed for selected payment methods only. Leave blank to auto-complete all orders.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_auto_complete_payment_methods',
		'default'  => array(),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => $payment_gateways_options,
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_order_auto_complete_options',
	),
	array(
		'title'    => __( 'Country by IP', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_orders_country_by_ip_options',
	),
	array(
		'title'    => __( 'Add Country by IP Meta Box', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'When enabled this will add "Booster: Country by IP" metabox to each order\'s edit page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_country_by_ip_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_orders_country_by_ip_options',
	),
	array(
		'title'    => __( 'Bulk Regenerate Download Permissions for Orders', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_order_bulk_regenerate_download_permissions_options',
	),
	array(
		'title'    => __( 'Bulk Regenerate Download Permissions', 'e-commerce-jetpack' ),
		'desc_tip' => apply_filters( 'booster_message', '', 'desc' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_order_bulk_regenerate_download_permissions_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Bulk Actions', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'When enabled this will add "Regenerate download permissions" action to "Bulk Actions" select box on admin orders page.', 'e-commerce-jetpack' ) . ' ' . apply_filters( 'booster_message', '', 'desc' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_bulk_regenerate_download_permissions_actions',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'All Orders - Now', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Check this box and press "Save changes" button to start regeneration. Please note that both module and current section must be enabled before that.', 'e-commerce-jetpack' ) . ' ' . apply_filters( 'booster_message', '', 'desc' ),
		'desc'     => __( 'Regenerate now', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_bulk_regenerate_download_permissions_all_orders',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'All Orders - Periodically', 'e-commerce-jetpack' ),
		'desc'     => $bulk_regenerate_download_permissions_all_orders_cron_desc . ' ' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_order_bulk_regenerate_download_permissions_all_orders_cron',
		'default'  => 'disabled',
		'type'     => 'select',
		'options'  => array_merge( array( 'disabled' => __( 'Disabled', 'e-commerce-jetpack' ) ),
			wcj_crons_get_all_intervals( __( 'Regenerate', 'e-commerce-jetpack' ), array( 'minutely' ) ) ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_order_bulk_regenerate_download_permissions_options',
	),
);
return $settings;
