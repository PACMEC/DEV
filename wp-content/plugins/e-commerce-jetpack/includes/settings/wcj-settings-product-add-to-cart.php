<?php
/**
 * Booster for WooCommerce - Settings - Product Add To Cart
 *
 * @version 3.3.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Add to Cart Local Redirect', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you set any local URL to redirect to after successfully adding product to cart.', 'e-commerce-jetpack' ) . ' ' .
			sprintf(
				__( 'For archives - "Enable AJAX add to cart buttons on archives" checkbox in <a href="%s">WooCommerce > Settings > Products > Display</a> must be disabled.', 'e-commerce-jetpack' ),
				admin_url( 'admin.php?page=wc-settings&tab=products&section=display' )
			),
		'id'       => 'wcj_add_to_cart_redirect_options',
	),
	array(
		'title'    => __( 'All Products', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_redirect_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'URL - All Products', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Redirect URL. Leave empty to redirect to checkout page (skipping the cart page).', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_redirect_url',
		'default'  => '',
		'type'     => 'text',
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'Per Product', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will add meta boxes to each product\'s edit page.', 'e-commerce-jetpack' ) . ' ' . apply_filters( 'booster_message', '', 'desc' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_redirect_per_product_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_add_to_cart_redirect_options',
	),
	array(
		'title'    => __( 'Add to Cart on Visit', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you enable automatically adding product to cart on visiting the product page. Product is only added once, so if it is already in cart - duplicate product is not added. ', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_on_visit_options',
	),
	array(
		'title'    => __( 'Add to Cart on Visit', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If "Per Product" is selected - meta box will be added to each product\'s edit page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_on_visit_enabled',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'no'          => __( 'Disabled', 'e-commerce-jetpack' ),
			'yes'         => __( 'All products', 'e-commerce-jetpack' ),
			'per_product' => __( 'Per product', 'e-commerce-jetpack' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_add_to_cart_on_visit_options',
	),
	array(
		'title'    => __( 'Add to Cart Variable Product', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_add_to_cart_variable_options',
	),
	array(
		'title'    => __( 'Display Radio Buttons Instead of Drop Box', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_variable_as_radio_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc_tip' => apply_filters( 'booster_message', '', 'desc' ),
	),
	array(
		'title'    => __( 'Variation Label Template', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%variation_title%', '%variation_price%' ) ),
		'id'       => 'wcj_add_to_cart_variable_as_radio_variation_label_template',
		'default'  => '%variation_title% (%variation_price%)',
		'type'     => 'custom_textarea',
		'css'      => 'width:99%;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
		'desc_tip' => apply_filters( 'booster_message', '', 'desc_no_link' ),
	),
	array(
		'title'    => __( 'Variation Description Template', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%variation_description%' ) ),
		'id'       => 'wcj_add_to_cart_variable_as_radio_variation_desc_template',
		'default'  => '<br><small>%variation_description%</small>',
		'type'     => 'custom_textarea',
		'css'      => 'width:99%;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
		'desc_tip' => apply_filters( 'booster_message', '', 'desc_no_link' ),
	),
	array(
		'title'    => __( 'Variation Radio Input td Style', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_variable_as_radio_input_td_style',
		'default'  => 'width:10%;',
		'type'     => 'text',
		'css'      => 'width:99%;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
		'desc_tip' => apply_filters( 'booster_message', '', 'desc_no_link' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_add_to_cart_variable_options',
	),
	array(
		'title'    => __( 'Replace Add to Cart Button on Archives with Single', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_add_to_cart_replace_loop_w_single_options',
	),
	array(
		'title'    => __( 'Replace Add to Cart Button on Archives with Button from Single Product Pages', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_replace_loop_w_single_enabled',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'no'            => __( 'Disable', 'e-commerce-jetpack' ),
			'yes'           => __( 'Enable', 'e-commerce-jetpack' ),
			'variable_only' => __( 'Variable products only', 'e-commerce-jetpack' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_add_to_cart_replace_loop_w_single_options',
	),
	array(
		'title'    => __( 'Add to Cart Quantity', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_add_to_cart_quantity_options',
	),
	array(
		'title'    => __( 'Disable Quantity Field for All Products', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable on Single Product Page', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_quantity_disable',
		'default'  => 'no',
		'type'     => 'checkbox',
		'checkboxgroup' => 'start',
	),
	array(
		'desc'     => __( 'Disable on Cart Page', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_quantity_disable_cart',
		'default'  => 'no',
		'type'     => 'checkbox',
		'checkboxgroup' => 'end',
	),
	array(
		'title'    => __( 'Set All Products to "Sold individually"', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_quantity_sold_individually_all',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_add_to_cart_quantity_options',
	),
	array(
		'title'    => __( 'Add to Cart Button Custom URL', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_add_to_cart_button_custom_url_options',
	),
	array(
		'title'    => __( 'Custom Add to Cart Buttons URL on Archives on per Product Basis', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will add meta box to each product\'s edit page', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_custom_loop_url_per_product_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_add_to_cart_button_custom_url_options',
	),
	array(
		'title'    => __( 'Add to Cart Button AJAX', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_add_to_cart_button_ajax_options',
	),
	array(
		'title'    => __( 'Disable/Enable Add to Cart Button AJAX on per Product Basis', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will add meta box to each product\'s edit page', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_ajax_per_product_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_add_to_cart_button_ajax_options',
	),
	array(
		'title'    => __( 'External Products', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_add_to_cart_button_external_product_options',
	),
	array(
		'title'    => __( 'Open External Products on Add to Cart in New Window', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable on Single Product Pages', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_external_open_new_window_single',
		'default'  => 'no',
		'type'     => 'checkbox',
		'checkboxgroup' => 'start',
	),
	array(
		'desc'     => __( 'Enable on Category/Archive Pages', 'e-commerce-jetpack' ),
		'id'       => 'wcj_add_to_cart_button_external_open_new_window_loop',
		'default'  => 'no',
		'type'     => 'checkbox',
		'checkboxgroup' => 'end',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_add_to_cart_button_external_product_options',
	),
	array(
		'title'    => __( 'Add to Cart Message Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_add_to_cart_message_options',
	),
	array(
		'title'    => __( 'Change "Continue shopping" Text', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_add_to_cart_message_continue_shopping_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc_tip' => apply_filters( 'booster_message', '', 'desc' ),
	),
	array(
		'id'       => 'wcj_product_add_to_cart_message_continue_shopping_text',
		'default'  => __( 'Continue shopping', 'woocommerce' ),
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Change "View cart" Text', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_add_to_cart_message_view_cart_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc_tip' => apply_filters( 'booster_message', '', 'desc' ),
	),
	array(
		'id'       => 'wcj_product_add_to_cart_message_view_cart_text',
		'default'  => __( 'View cart', 'woocommerce' ),
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_add_to_cart_message_options',
	),
	array(
		'title'    => __( 'Add to Cart Button Position Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_add_to_cart_button_position_options',
	),
	array(
		'title'    => __( 'Add to Cart Button Position', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_product_add_to_cart_button_position_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Reposition Button on Single Product Pages', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_add_to_cart_button_position_single_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Position', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_add_to_cart_button_position_hook_single',
		'default'  => 'woocommerce_single_product_summary',
		'type'     => 'select',
		'options'  => array(
			'woocommerce_before_single_product_summary' => __( 'Before single product summary', 'e-commerce-jetpack' ),
			'woocommerce_single_product_summary'        => __( 'Inside single product summary', 'e-commerce-jetpack' ),
			'woocommerce_after_single_product_summary'  => __( 'After single product summary', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Priority', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Here are the default WooCommerce priorities for "Inside single product summary" position: %s', 'e-commerce-jetpack' ),
			implode( ', ', array(
				'5 - '  . __( 'Title', 'e-commerce-jetpack' ),
				'10 - ' . __( 'Rating', 'e-commerce-jetpack' ),
				'10 - ' . __( 'Price', 'e-commerce-jetpack' ),
				'20 - ' . __( 'Excerpt', 'e-commerce-jetpack' ),
				'40 - ' . __( 'Meta', 'e-commerce-jetpack' ),
				'50 - ' . __( 'Sharing', 'e-commerce-jetpack' ),
				'30 - ' . __( 'Add to Cart', 'e-commerce-jetpack' ),
			)
		) ),
		'id'       => 'wcj_product_add_to_cart_button_position_single',
		'default'  => 30,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'Reposition Button on Category/Archive Pages', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_add_to_cart_button_position_loop_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Position', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_add_to_cart_button_position_hook_loop',
		'default'  => 'woocommerce_after_shop_loop_item',
		'type'     => 'select',
		'options'  => array(
			'woocommerce_before_shop_loop_item'       => __( 'Before product', 'e-commerce-jetpack' ),
			'woocommerce_before_shop_loop_item_title' => __( 'Before product title', 'e-commerce-jetpack' ),
			'woocommerce_after_shop_loop_item'        => __( 'After product', 'e-commerce-jetpack' ),
			'woocommerce_after_shop_loop_item_title'  => __( 'After product title', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Priority', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_add_to_cart_button_position_loop',
		'default'  => 10,
		'type'     => 'number',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_add_to_cart_button_position_options',
	),
);
