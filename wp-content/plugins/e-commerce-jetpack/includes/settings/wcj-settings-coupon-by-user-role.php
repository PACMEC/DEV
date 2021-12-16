<?php
/**
 * Booster for WooCommerce - Settings - Coupon by User Role
 *
 * @version 3.6.0
 * @since   3.6.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'All Coupons', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_coupon_by_user_role_all_coupons_options',
	),
	array(
		'title'    => __( 'Disable All Coupons for Selected User Roles', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will disable all coupons for selected user roles. Coupons will be disabled completely, including coupon code input on the cart page.', 'e-commerce-jetpack' ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'default'  => '',
		'id'       => 'wcj_coupon_by_user_role_disabled',
		'options'  => wcj_get_user_roles_options(),
	),
	array(
		'title'    => __( 'Invalidate All Coupons for Selected User Roles', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will invalidate all coupons for selected user roles. Coupon code input will still be available on the cart page.', 'e-commerce-jetpack' ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'default'  => '',
		'id'       => 'wcj_coupon_by_user_role_invalid',
		'options'  => wcj_get_user_roles_options(),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_coupon_by_user_role_all_coupons_options',
	),
	array(
		'title'    => __( 'Per Coupon', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_coupon_by_user_role_per_coupon_options',
	),
	array(
		'title'    => __( 'Invalidate per Coupon', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will add "Booster: Coupon by User Role" meta box to each coupon\'s admin edit page.', 'e-commerce-jetpack' ) . '<br>' .
			apply_filters( 'booster_message', '', 'desc' ),
		'type'     => 'checkbox',
		'default'  => 'no',
		'id'       => 'wcj_coupon_by_user_role_invalid_per_coupon',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_coupon_by_user_role_per_coupon_options',
	),
	array(
		'title'    => __( 'Message', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_coupon_by_user_role_messages_options',
	),
	array(
		'title'    => __( '"Coupon is not valid" Message', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Message that will be displayed for invalid coupons by user role.', 'e-commerce-jetpack' ),
		'type'     => 'custom_textarea',
		'default'  => __( 'Coupon is not valid for your user role.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_coupon_by_user_role_invalid_message',
		'css'      => 'width:100%;',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_coupon_by_user_role_messages_options',
	),
);
