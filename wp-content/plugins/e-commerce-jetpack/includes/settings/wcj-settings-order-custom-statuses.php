<?php
/**
 * Booster for WooCommerce Settings - Order Custom Statuses
 *
 * @version 5.3.3
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Custom Statuses', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_orders_custom_statuses_options',
	),
	array(
		'title'    => __( 'Default Order Status', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable the module to add custom statuses to the list.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'You can change the default order status here. However payment gateways can change this status immediately on order creation. E.g. BACS gateway will change status to On-hold.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_custom_statuses_default_status',
		'default'  => 'wcj_no_changes',
		'type'     => 'select',
		'options'  => array_merge( array( 'wcj_no_changes' => __( 'No changes', 'e-commerce-jetpack' ) ), wcj_get_order_statuses() ),
	),
	array(
		'title'    => __( 'Set Default Order Status Forcefully', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'You can set the default order status forcefully from here. Forcing the status can result in unpredictable consequences, enable the checkbox here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_custom_statuses_default_status_forcefully',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Add All Statuses to Admin Order Bulk Actions', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you wish to add custom statuses to admin Orders page bulk actions, enable the checkbox here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_custom_statuses_add_to_bulk_actions',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Add Custom Statuses to Admin Reports', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you wish to add custom statuses to admin reports, enable the checkbox here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_custom_statuses_add_to_reports',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Make Custom Status Orders Editable', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'By default orders with custom statuses are not editable (same like with standard WooCommerce Completed status). If you wish to make custom status orders editable, enable the checkbox here.', 'e-commerce-jetpack' ) . ' ' .
			apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_orders_custom_statuses_is_order_editable',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Remove Status Prefix', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Removes the <code>wc-</code> prefix from custom statuses.', 'e-commerce-jetpack' ) . ' ' . __( 'Enable it if you can\'t see the orders or the statuses.', 'e-commerce-jetpack' ) . ' ' .
		              apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_orders_custom_statuses_remove_prefix',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( '"Processing" and "Complete" Action Buttons', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'By default, when order has custom status, "Processing" and "Complete" action buttons are hidden. You can enable it here. Possible values are: Show both; Show "Processing" only; Show "Complete" only; Hide (default).', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_custom_statuses_processing_and_completed_actions',
		'default'  => 'hide',
		'type'     => 'select',
		'options'  => array(
			'show_both'       => __( 'Show both', 'e-commerce-jetpack' ),
			'show_processing' => __( 'Show "Processing" only', 'e-commerce-jetpack' ),
			'show_complete'   => __( 'Show "Complete" only', 'e-commerce-jetpack' ),
			'hide'            => __( 'Hide', 'e-commerce-jetpack' ),
		),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Add Custom Statuses to Admin Order List Action Buttons', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you wish to add custom statuses buttons to the admin Orders page action buttons (Actions column), enable the checkbox here.', 'e-commerce-jetpack' ) . ' ' .
			apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_orders_custom_statuses_add_to_order_list_actions',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'desc'     => __( 'Enable Colors', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Choose if you want the buttons to have colors.', 'e-commerce-jetpack' ) . ' ' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_orders_custom_statuses_add_to_order_list_actions_colored',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Enable Colors in Status Column', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Enable this if you want the statuses in Status column to have colors.', 'e-commerce-jetpack' ) . ' ' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_orders_custom_statuses_column_colored',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Add Custom Statuses Buttons to Admin Order Preview Actions', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you wish to add custom statuses buttons to the admin orders preview page, enable the checkbox here.', 'e-commerce-jetpack' ) . ' ' .
			apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_orders_custom_statuses_add_to_order_preview_actions',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_orders_custom_statuses_options',
	),
);
