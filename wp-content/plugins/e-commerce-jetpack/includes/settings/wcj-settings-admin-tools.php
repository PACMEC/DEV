<?php
/**
 * Booster for WooCommerce - Settings - Admin Tools
 *
 * @version 4.9.0
 * @since   2.7.2
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Admin Tools Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_admin_tools_general_options',
	),
	array(
		'title'    => __( 'Show Booster Menus Only to Admin', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Will require %s capability to see Booster menus (instead of %s capability).', 'e-commerce-jetpack' ),
			'<code>manage_options</code>', '<code>manage_woocommerce</code>' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_admin_tools_show_menus_to_admin_only',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Suppress Admin Connect Notice', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Will remove "%s" admin notice.', 'e-commerce-jetpack' ),
			__( 'Connect your store to WooCommerce.com to receive extensions updates and support.', 'e-commerce-jetpack' ) ),
		'id'       => 'wcj_admin_tools_suppress_connect_notice',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Suppress Admin Notices', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Will remove admin notices (including the Connect notice).', 'e-commerce-jetpack' ),
		'id'       => 'wcj_admin_tools_suppress_admin_notices',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'             => __( 'Enable Interface By User Roles', 'e-commerce-jetpack' ),
		'desc_tip'          => __( 'The interface can\'t be disabled for The Administrator role.', 'e-commerce-jetpack' ) . '<br /><br />' . __( 'Leave it empty to enable the interface for all the roles.', 'e-commerce-jetpack' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Disables the whole Booster admin interface for not selected roles.', 'e-commerce-jetpack' ) : $message,
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'id'                => 'wcj_admin_tools_enable_interface_by_role',
		'default'           => '',
		'type'              => 'multiselect',
		'class'             => 'chosen_select',
		'options'           => wcj_get_user_roles_options(),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_admin_tools_general_options',
	),
	array(
		'title'    => __( 'Orders Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_admin_tools_orders_options',
	),
	array(
		'title'    => __( 'Show Order Meta', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Will show order meta table in meta box.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_admin_tools_show_order_meta_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_admin_tools_orders_options',
	),
	array(
		'title'    => __( 'Products Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_admin_tools_products_options',
	),
	array(
		'title'    => __( 'Show Product Meta', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Will show product meta table in meta box.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_admin_tools_show_product_meta_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Show Variable Product Pricing Table', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Will allow to set all variations prices in single meta box.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_admin_tools_variable_product_pricing_table_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Product Revisions', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Will enable product revisions.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_revisions_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'JSON Product Search Limit', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will set the maximum number of products to return on JSON search (e.g. when setting Upsells and Cross-sells on product edit page).', 'e-commerce-jetpack' ) . ' ' .
			__( 'Ignored if set to zero.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_json_search_limit',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_admin_tools_products_options',
	),
	array(
		'title'    => __( 'Users Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_admin_tools_users_options',
	),
	array(
		'title'             => __( 'Shop Manager Editable Roles', 'e-commerce-jetpack' ),
		'desc_tip'          => __( 'Changes the roles the Shop Manager role can edit.', 'e-commerce-jetpack' ),
		'desc'              => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'id'                => 'wcj_admin_tools_shop_manager_editable_roles',
		'default'           => apply_filters( 'woocommerce_shop_manager_editable_roles', array( 'customer' ) ),
		'type'              => 'multiselect',
		'class'             => 'chosen_select',
		'options'           => wcj_get_user_roles_options(),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_admin_tools_users_options',
	),
);
