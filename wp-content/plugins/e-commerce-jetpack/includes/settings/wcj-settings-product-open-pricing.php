<?php
/**
 * Booster for WooCommerce - Settings - Product Open Pricing
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    set default for "wcj_product_open_price_enable_js_validation" to "yes"
 * @todo    (maybe) `$positions` - add `woocommerce_before_add_to_cart_quantity` and `woocommerce_after_add_to_cart_quantity` (on `! WCJ_IS_WC_VERSION_BELOW_3` and also recheck "Grouped product add to cart" template)
 * @todo    (maybe) `$positions` - add "Frontend Position Priority" option
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$default_price_step = 1 / pow( 10, absint( wcj_get_option( 'woocommerce_price_num_decimals', 2 ) ) );
$positions = array(
	'woocommerce_before_add_to_cart_button' => __( 'Before add to cart button', 'e-commerce-jetpack' ), // since WC v2.1.0
	'woocommerce_after_add_to_cart_button'  => __( 'After add to cart button', 'e-commerce-jetpack' ),  // since WC v2.1.0
);
return array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_open_price_options',
	),
	array(
		'title'    => __( 'Frontend Label', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_label_frontend',
		'default'  => __( 'Name Your Price', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Frontend Template', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Here you can use' ) . ': ' .
			'%frontend_label%, %open_price_input%, %currency_symbol%, %min_price_simple%, %max_price_simple%, %default_price_simple%, %min_price%, %max_price%, %default_price%.',
		'id'       => 'wcj_product_open_price_frontend_template',
		'default'  => '<label for="wcj_open_price">%frontend_label%</label> %open_price_input% %currency_symbol%',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Frontend Input Style', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_input_style',
		'default'  => 'width:75px;text-align:center;',
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Frontend Input Placeholder', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_input_placeholder',
		'default'  => '',
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Frontend Input Price Step', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_price_step',
		'default'  => $default_price_step,
		'type'     => 'number',
		'custom_attributes' => array( 'step' => '0.0001', 'min' => '0.0001' ),
	),
	array(
		'title'    => __( 'Frontend Position', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_position',
		'default'  => 'woocommerce_before_add_to_cart_button',
		'type'     => 'select',
		'options'  => $positions,
	),
	array(
		'title'    => __( 'Message on Empty Price', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_messages_required',
		'default'  => __( 'Price is required!', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Message on Price too Small', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%price%', '%min_price%' ) ),
		'id'       => 'wcj_product_open_price_messages_to_small',
		'default'  => __( 'Entered price is too small!', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Message on Price too Big', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%price%', '%max_price%' ) ),
		'id'       => 'wcj_product_open_price_messages_to_big',
		'default'  => __( 'Entered price is too big!', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Disable Quantity Input', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_disable_quantity',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Enable JS Min/Max Validation', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_enable_js_validation',
		'default'  => 'no',
		'type'     => 'checkbox',
		'desc_tip' => __( 'To do validation by JS instead of page load, Enable this if you want to validate the price box by Javascript', 'e-commerce-jetpack' ),
	),
	array(
		'title'    => __( 'Display Product Price Info in Archives', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_enable_loop_price_info',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Product price info in archives template. Replaceable values: <code>%default_price%</code>, <code>%min_price%</code>, <code>%max_price%</code>.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_loop_price_info_template',
		'default'  => '<span class="price">%default_price%</span>',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Add "Open Pricing" Column to Admin Product List', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_enable_admin_product_list_column',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Advanced: Multicurrency (Currency Switcher) Module', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_currency_switcher',
		'default'  => 'shop_currency',
		'type'     => 'select',
		'options'  => array(
			'shop_currency'     => __( 'Allow price entering in default shop currency only', 'e-commerce-jetpack' ),
			'switched_currency' => __( 'Allow price entering in switched currency', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Advanced: Check for Outputted Data', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Ensures that data outputted only once. Enable this if you see data outputted on frontend twice. Disable if you see no data outputted.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_check_for_outputted_data',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Advanced: Price Changes', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable open pricing for products with "Price Changes"', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Try enabling this checkbox, if you are having compatibility issues with other plugins.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_check_for_product_changes_price',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_open_price_options',
	),
	array(
		'title'    => __( 'Product Bundles', 'e-commerce-jetpack' ),
		'desc'     => sprintf( __( 'Change below settings if there are compatibility issues with <a href="%s">"WPC Product Bundles for WooCommerce"</a> plugin.', 'e-commerce-jetpack' ), 'https://wordpress.org/plugins/woo-product-bundle/' ),
		'type'     => 'title',
		'id'       => 'wcj_product_open_price_woosb_product_bundles',
	),
	array(
		'title'    => __( 'Remove "add to cart" hook', 'e-commerce-jetpack' ),
		'desc'     => __( 'Remove', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Try to remove "add to cart" hook from Product Bundles', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_woosb_product_bundles_remove_atc',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Replace Prices', 'e-commerce-jetpack' ),
		'desc'     => __( 'Replace', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Try to replace Product Bundles prices', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_woosb_product_bundles_replace_prices',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Product Bundles', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_open_price_woosb_product_bundles_divide',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'no'             => __( 'No not divide', 'e-commerce-jetpack' ),
			'yes'            => __( 'Divide by number of products in a bundle', 'e-commerce-jetpack' ),
			'proportionally' => __( 'Divide proportionally to the original price', 'e-commerce-jetpack' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_open_price_woosb_product_bundles',
	),
);
