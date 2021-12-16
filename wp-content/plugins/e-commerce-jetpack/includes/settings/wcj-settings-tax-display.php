<?php
/**
 * Booster for WooCommerce - Settings - Tax Display
 *
 * @version 4.1.0
 * @since   3.2.4
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Prepare products
$products = wcj_get_products();

// Prepare categories
$product_cats = wcj_get_terms( 'product_cat' );

$settings = array(
	array(
		'title'    => __( 'TAX Display - Toggle Button', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => sprintf( __( 'Use %s shortcode to display the button on frontend.', 'e-commerce-jetpack' ), '<code>[wcj_button_toggle_tax_display]</code>' ),
		'id'       => 'wcj_tax_display_toggle_options',
	),
	array(
		'title'    => __( 'TAX Toggle Button', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_tax_display_toggle_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_tax_display_toggle_options',
	),
	array(
		'title'    => __( 'TAX Display by Product', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'If you want to display part of your products including TAX and another part excluding TAX, you can set it here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_listings_display_taxes_options',
	),
	array(
		'title'    => __( 'TAX Display by Product', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_product_listings_display_taxes_by_products_enabled',
		'type'     => 'checkbox',
		'default'  => 'no',
	),
	array(
		'title'    => __( 'Products - Including TAX', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_listings_display_taxes_products_incl_tax',
		'desc_tip' => __( 'Select products to display including TAX.', 'e-commerce-jetpack' ),
		'default'  => '',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'css'      => 'width: 450px;',
		'options'  => $products,
	),
	array(
		'title'    => __( 'Products - Excluding TAX', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_listings_display_taxes_products_excl_tax',
		'desc_tip' => __( 'Select products to display excluding TAX.', 'e-commerce-jetpack' ),
		'default'  => '',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'css'      => 'width: 450px;',
		'options'  => $products,
	),
	array(
		'title'    => __( 'Product Categories - Including TAX', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_listings_display_taxes_product_cats_incl_tax',
		'desc_tip' => __( 'Select product categories to display including TAX.', 'e-commerce-jetpack' ),
		'default'  => '',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'css'      => 'width: 450px;',
		'options'  => $product_cats,
	),
	array(
		'title'    => __( 'Product Categories - Excluding TAX', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_listings_display_taxes_product_cats_excl_tax',
		'desc_tip' => __( 'Select product categories to display excluding TAX.', 'e-commerce-jetpack' ),
		'default'  => '',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'css'      => 'width: 450px;',
		'options'  => $product_cats,
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_listings_display_taxes_options',
	),
	array(
		'title'    => __( 'TAX Display by User Role', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'If you want to display prices including TAX or excluding TAX for different user roles, you can set it here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_listings_display_taxes_by_user_role_options',
	),
	array(
		'title'    => __( 'TAX Display by User Role', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_product_listings_display_taxes_by_user_role_enabled',
		'type'     => 'checkbox',
		'default'  => 'no',
	),
	array(
		'title'    => __( 'User Roles', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Save changes after you change this option and new settings fields will appear.', 'e-commerce-jetpack' ),
		'desc'     => '<br>' . sprintf( __( 'Select user roles that you want to change tax display for. For all remaining (i.e. not selected) user roles - default TAX display (set in %s) will be applied.', 'e-commerce-jetpack' ),
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=tax' ) . '">' . __( 'WooCommerce > Settings > Tax', 'e-commerce-jetpack' ) . '</a>' ),
		'id'       => 'wcj_product_listings_display_taxes_by_user_role_roles',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'default'  => '',
		'options'  => wcj_get_user_roles_options(),
	),
);
if ( '' != ( $display_taxes_by_user_role_roles = wcj_get_option( 'wcj_product_listings_display_taxes_by_user_role_roles', '' ) ) ) {
	foreach ( $display_taxes_by_user_role_roles as $display_taxes_by_user_role_role ) {
		$settings = array_merge( $settings, array(
			array(
				'title'    => sprintf( __( 'Role: %s - shop', 'e-commerce-jetpack' ), $display_taxes_by_user_role_role ),
				'id'       => 'wcj_product_listings_display_taxes_by_user_role_' . $display_taxes_by_user_role_role,
				'desc_tip' => __( 'Setup how taxes will be applied during in the shop.', 'e-commerce-jetpack' ),
				'default'  => 'no_changes',
				'type'     => 'select',
				'options'  => array(
					'no_changes' => __( 'Default TAX display (no changes)', 'e-commerce-jetpack' ),
					'incl'       => __( 'Including tax', 'e-commerce-jetpack' ),
					'excl'       => __( 'Excluding tax', 'e-commerce-jetpack' ),
				),
			),
			array(
				'title'    => sprintf( __( 'Role: %s - cart', 'e-commerce-jetpack' ), $display_taxes_by_user_role_role ),
				'id'       => 'wcj_product_listings_display_taxes_on_cart_by_user_role_' . $display_taxes_by_user_role_role,
				'desc_tip' => __( 'Setup how taxes will be applied during cart and checkout.', 'e-commerce-jetpack' ),
				'default'  => 'no_changes',
				'type'     => 'select',
				'options'  => array(
					'no_changes' => __( 'Default TAX display (no changes)', 'e-commerce-jetpack' ),
					'incl'       => __( 'Including tax', 'e-commerce-jetpack' ),
					'excl'       => __( 'Excluding tax', 'e-commerce-jetpack' ),
				),
			),
		) );
	}
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_listings_display_taxes_by_user_role_options',
	),
) );
return $settings;
