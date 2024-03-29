<?php
/**
 * Booster for WooCommerce - Settings - Price based on User Role
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$settings = array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_price_by_user_role_options',
	),
	array(
		'title'    => __( 'Enable per Product Settings', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'When enabled, this will add new "Booster: Price based on User Role" meta box to each product\'s edit page.', 'e-commerce-jetpack' ),
		'type'     => 'checkbox',
		'id'       => 'wcj_price_by_user_role_per_product_enabled',
		'default'  => 'yes',
	),
	array(
		'title'    => __( 'Per Product Settings Type', 'e-commerce-jetpack' ),
		'type'     => 'select',
		'id'       => 'wcj_price_by_user_role_per_product_type',
		'default'  => 'fixed',
		'options'  => array(
			'fixed'      => __( 'Fixed', 'e-commerce-jetpack' ),
			'multiplier' => __( 'Multiplier', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Show Roles on per Product Settings', 'e-commerce-jetpack' ),
		'desc'     => __( 'If per product settings are enabled, you can choose which roles to show on product\'s edit page. Leave blank to show all roles.', 'e-commerce-jetpack' ),
		'type'     => 'multiselect',
		'id'       => 'wcj_price_by_user_role_per_product_show_roles',
		'default'  => '',
		'class'    => 'chosen_select',
		'options'  => wcj_get_user_roles_options(),
	),
	array(
		'title'    => __( 'Shipping', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'When enabled, this will apply user role multipliers to shipping calculations.', 'e-commerce-jetpack' ),
		'type'     => 'checkbox',
		'id'       => 'wcj_price_by_user_role_shipping_enabled',
		'default'  => 'no',
	),
	array(
		'title'    => __( 'Disable Price based on User Role for Regular Price', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Disable price by user role for regular price when using multipliers (global or per product).', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_user_role_disable_for_regular_price',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Search Engine Bots', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable Price based on User Role for Bots', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_user_role_for_bots_disabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Show Empty Price Variations', 'e-commerce-jetpack' ),
		'desc'     => __( 'Show', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Show "empty price" variations. This will also hide out of stock messages.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_user_role_show_empty_price_variations',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Remove Empty Price Variation Callback', 'e-commerce-jetpack' ),
		'desc'     => __( 'Remove', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Remove "woocommerce_single_variation" callback from "woocommerce_single_variation" hook on "empty price" variations.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_user_role_remove_single_variation',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Remove Empty Price Add to Cart Button Callback', 'e-commerce-jetpack' ),
		'desc'     => __( 'Remove', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Remove "woocommerce_single_variation_add_to_cart_button" callback from "woocommerce_single_variation" hook on "empty price" variations.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_user_role_remove_add_to_cart_btn',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Check Child Categories', 'e-commerce-jetpack' ),
		'desc'     => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip' => __( 'Enable to also consider the child categories.', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'id'       => 'wcj_price_by_user_role_check_child_categories',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_price_by_user_role_options',
	),

	array(
		'title'    => __( 'Advanced', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_price_by_user_role_options_adv',
	),
	array(
		'title'    => __( 'Price Filters Priority', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Priority for all module\'s price filters. If you face pricing issues while using another plugin or booster module, You can change the Priority, Greater value for high priority & Lower value for low priority. Set to zero to use default priority.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_user_role_advanced_price_hooks_priority',
		'default'  => 0,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'Price Changes', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable price based on user role for products with "Price Changes"', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Try enabling this checkbox, if you are having compatibility issues with other plugins.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_user_role_check_for_product_changes_price',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	$this->get_wpml_terms_in_all_languages_setting(),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_price_by_user_role_options_adv',
	),
	array(
		'title'    => __( 'Compatibility', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_price_by_user_role_compatibility',
	),
	array(
		'title'             => __( 'WooCommerce Product Bundles', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => sprintf( __( 'Enable this option if there is compatibility with <a href="%s" target="_blank">WooCommerce Product Bundles</a> plugin.', 'e-commerce-jetpack' ), 'https://woocommerce.com/products/product-bundles/' ),
		'id'                => 'wcj_price_by_user_role_compatibility_wc_product_bundles',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_price_by_user_role_compatibility',
	),
	array(
		'title'    => __( 'Roles & Multipliers', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => sprintf( __( 'Custom roles can be added via "Add/Manage Custom Roles" tool in Booster\'s <a href="%s">General</a> module.', 'e-commerce-jetpack' ),
			admin_url( 'admin.php?page=wc-settings&tab=jetpack&wcj-cat=emails_and_misc&section=general' ) ),
		'id'       => 'wcj_price_by_user_role_multipliers_options',
	),
	array(
		'title'    => __( 'Disable Price based on User Role for Products on Sale', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_user_role_disable_for_products_on_sale',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
);
foreach ( wcj_get_user_roles() as $role_key => $role_data ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => $role_data['name'],
			'id'       => 'wcj_price_by_user_role_' . $role_key,
			'default'  => 1,
			'type'     => 'wcj_number_plus_checkbox_start',
			'custom_attributes' => array( 'step' => '0.000001', 'min'  => '0', ),
		),
		array(
			'desc'     => __( 'Make Empty Price', 'e-commerce-jetpack' ),
			'id'       => 'wcj_price_by_user_role_empty_price_' . $role_key,
			'default'  => 'no',
			'type'     => 'wcj_number_plus_checkbox_end',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_price_by_user_role_multipliers_options',
	),
) );
$taxonomies = array(
	array(
		'title'     => __( 'Products Categories', 'e-commerce-jetpack' ),
		'name'      => 'categories',
		'id'        => 'product_cat',
		'option_id' => 'cat',
	),
	array(
		'title'     => __( 'Products Tags', 'e-commerce-jetpack' ),
		'name'      => 'tags',
		'id'        => 'product_tag',
		'option_id' => 'tag',
	),
);

do_action( 'wcj_before_get_terms', $this->id );
foreach ( $taxonomies as $taxonomy ) {
	$product_taxonomies_options = array();
	$product_taxonomies = get_terms( $taxonomy['id'], 'orderby=name&hide_empty=0' );
	if ( ! empty( $product_taxonomies ) && ! is_wp_error( $product_taxonomies ) ){
		foreach ( $product_taxonomies as $product_taxonomy ) {
			$product_taxonomies_options[ $product_taxonomy->term_id ] = $product_taxonomy->name;
		}
	}
	$settings = array_merge( $settings, array(
		array(
			'title'    => sprintf( __( 'Price based on User Role by %s', 'e-commerce-jetpack' ), $taxonomy['title'] ),
			'type'     => 'title',
			'id'       => 'wcj_price_by_user_role_' . $taxonomy['name'] . '_options',
		),
		array(
			'title'    => $taxonomy['title'],
			'desc_tip' => __( 'Save module\'s settings after changing this option to see new settings fields.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_price_by_user_role_' . $taxonomy['name'],
			'default'  => '',
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'options'  => $product_taxonomies_options,
			'desc'     => apply_filters( 'booster_message', '', 'desc' ),
			'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		),
	) );
	$_taxonomies = apply_filters( 'booster_option', '', wcj_get_option( 'wcj_price_by_user_role_' . $taxonomy['name'], '' ) );
	if ( ! empty( $_taxonomies ) ) {
		foreach ( $_taxonomies as $_taxonomy ) {
			foreach ( wcj_get_user_roles() as $role_key => $role_data ) {
				$settings = array_merge( $settings, array(
					array(
						'title'    => $product_taxonomies_options[ $_taxonomy ] . ': ' . $role_data['name'],
						'desc_tip' => __( 'Multiplier is ignored if set to negative number (e.g.: -1). Global multiplier will be used instead.', 'e-commerce-jetpack' ),
						'id'       => 'wcj_price_by_user_role_' . $taxonomy['option_id'] . '_' . $_taxonomy . '_' . $role_key,
						'default'  => -1,
						'type'     => 'wcj_number_plus_checkbox_start',
						'custom_attributes' => array( 'step' => '0.000001', 'min' => -1 ),
					),
					array(
						'desc'     => __( 'Make Empty Price', 'e-commerce-jetpack' ),
						'id'       => 'wcj_price_by_user_role_' . $taxonomy['option_id'] . '_empty_price_' . $_taxonomy . '_' . $role_key,
						'default'  => 'no',
						'type'     => 'wcj_number_plus_checkbox_end',
					),
				) );
			}
		}
	}
	$settings = array_merge( $settings, array(
		array(
			'type'     => 'sectionend',
			'id'       => 'wcj_price_by_user_role_' . $taxonomy['name'] . '_options',
		),
	) );
}
do_action('wcj_after_get_terms', $this->id );

return $settings;
