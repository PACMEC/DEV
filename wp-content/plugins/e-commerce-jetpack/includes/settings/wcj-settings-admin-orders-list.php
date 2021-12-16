<?php
/**
 * Booster for WooCommerce - Settings - Admin Orders List
 *
 * @version 5.3.3
 * @since   3.2.4
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Custom Columns', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you add custom columns to WooCommerce orders list.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_list_custom_columns_options',
	),
	array(
		'title'    => __( 'Custom Columns', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_order_admin_list_custom_columns_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Billing Country', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add column and filtering', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_list_custom_columns_country',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Currency Code', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add column and filtering', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_list_custom_columns_currency',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Custom Columns Total Number', 'e-commerce-jetpack' ),
		'id'       => 'wcj_orders_list_custom_columns_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ? apply_filters( 'booster_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min'  => '0', )
		),
	),
);
$total_number = apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_orders_list_custom_columns_total_number', 1 ) );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Custom Column', 'e-commerce-jetpack' ) . ' #' . $i,
			'desc'     => __( 'Enabled', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Key:', 'e-commerce-jetpack' ) . ' <code>' . 'wcj_orders_custom_column_' . $i . '</code>',
			'id'       => 'wcj_orders_list_custom_columns_enabled_' . $i,
			'default'  => 'no',
			'type'     => 'checkbox',
		),
		array(
			'desc'     => __( 'Label', 'e-commerce-jetpack' ),
			'id'       => 'wcj_orders_list_custom_columns_label_' . $i,
			'default'  => '',
			'type'     => 'text',
			'css'      => 'width:100%;',
		),
		array(
			'desc'     => __( 'Value', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'You can use shortcodes and/or HTML here.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_orders_list_custom_columns_value_' . $i,
			'default'  => '',
			'type'     => 'custom_textarea',
			'css'      => 'width:100%;',
		),
		array(
			'desc'     => __( 'Sortable , Select "By meta (as text)" for date sorting', 'e-commerce-jetpack' ),
			'id'       => 'wcj_orders_list_custom_columns_sortable_' . $i,
			'default'  => 'no',
			'type'     => 'select',
			'options'  => array(
				'no'             => __( 'No', 'e-commerce-jetpack' ),
				'meta_value'     => __( 'By meta (as text)', 'e-commerce-jetpack' ),
				'meta_value_num' => __( 'By meta (as numbers)', 'e-commerce-jetpack' ),
			),
		),
		array(
			'desc'     => sprintf( __( 'Key (if sortable) %s Add "_" (underscore) before key if the key is from "Checkout Custom Fields module"') , '</br>' , 'e-commerce-jetpack' ),
			'id'       => 'wcj_orders_list_custom_columns_sortable_key_' . $i,
			'default'  => '',
			'type'     => 'text',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_orders_list_custom_columns_options',
	),
	array(
		'title'    => __( 'Multiple Status', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_order_admin_list_multiple_status_options',
	),
	array(
		'title'    => __( 'Multiple Status', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_order_admin_list_multiple_status_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Multiple Status Filtering', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_admin_list_multiple_status_filter',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'no'              => __( 'Do not add', 'e-commerce-jetpack' ),
			'multiple_select' => __( 'Add as multiple select', 'e-commerce-jetpack' ),
			'checkboxes'      => __( 'Add as checkboxes', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Hide Default Statuses Menu', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_admin_list_hide_default_statuses_menu',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Add "Not Completed" Status Link to Default Statuses Menu', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_admin_list_multiple_status_not_completed_link',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Add Presets to Admin Menu', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' .  __( 'Add presets', 'e-commerce-jetpack' ) . '</strong>',
		'desc_tip' => __( 'To add presets, "Multiple Status Filtering" option must be enabled (as multiple select or as checkboxes).', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_admin_list_multiple_status_admin_menu',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Add order counter', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_admin_list_multiple_status_admin_menu_counter',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Remove original "Orders" menu', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_admin_list_multiple_status_admin_menu_remove_original',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Total Presets', 'e-commerce-jetpack' ),
		'id'       => 'wcj_order_admin_list_multiple_status_presets_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
) );
$total_number = apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_order_admin_list_multiple_status_presets_total_number', 1 ) );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'desc'     => __( 'Title', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Must be not empty.', 'e-commerce-jetpack' ),
			'id'       => "wcj_order_admin_list_multiple_status_presets_titles[$i]",
			'default'  => __( 'Preset', 'e-commerce-jetpack' ) . ' #' . $i,
			'type'     => 'text',
		),
		array(
			'desc'     => __( 'Statuses', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Must be not empty.', 'e-commerce-jetpack' ),
			'id'       => "wcj_order_admin_list_multiple_status_presets_statuses[$i]",
			'default'  => array(),
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'options'  => wcj_get_order_statuses( false ),
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_order_admin_list_multiple_status_options',
	),
	array(
		'title'    => __( 'Columns Order', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_order_admin_list_columns_order_options',
	),
	array(
		'title'    => __( 'Columns Order', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_order_admin_list_columns_order_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'id'       => 'wcj_order_admin_list_columns_order',
		'desc_tip' => __( 'Default columns order', 'e-commerce-jetpack' ) . ':<br>' . str_replace( PHP_EOL, '<br>', $this->get_orders_default_columns_in_order() ),
		'default'  => $this->get_orders_default_columns_in_order(),
		'type'     => 'textarea',
		'css'      => 'height:300px;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_order_admin_list_columns_order_options',
	),
) );
return $settings;
