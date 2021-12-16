<?php
/**
 * Booster for WooCommerce - Settings - Product Addons
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    (maybe) add `woocommerce_payment_complete` to `$qty_triggers` (also maybe add this trigger to "PDF Invoicing" module)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$products = wcj_get_products();

$qty_triggers = array();
$qty_triggers['woocommerce_new_order'] = __( 'New order', 'e-commerce-jetpack' );
$order_statuses = wcj_get_order_statuses();
foreach ( $order_statuses as $status => $desc ) {
	$qty_triggers[ 'woocommerce_order_status_' . $status ] = sprintf( __( 'Order status "%s"', 'e-commerce-jetpack' ), $desc );
}

$settings = array();
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Per Product Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_addons_per_product_options',
	),
	array(
		'title'    => __( 'Enable per Product Addons', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'When enabled, this will add new "Booster: Product Addons" meta box to each product\'s edit page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_per_product_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_addons_per_product_options',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'All Product Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_addons_all_products_options',
	),
	array(
		'title'    => __( 'Enable All Products Addons', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'When enabled, this will add addons below to all products.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_all_products_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Product Addons Total Number', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Save changes after you change this number.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_all_products_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ? apply_filters( 'booster_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min'  => '0', )
		),
	),
) );
$total_number = apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_product_addons_all_products_total_number', 1 ) );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Product Addon', 'e-commerce-jetpack' ) . ' #' . $i,
			'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_enabled_' . $i,
			'default'  => 'yes',
			'type'     => 'checkbox',
		),
		array(
			'desc'     => __( 'Type', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_type_' . $i,
			'default'  => 'checkbox',
			'type'     => 'select',
			'options'  => array(
				'checkbox' => __( 'Checkbox', 'e-commerce-jetpack' ),
				'radio'    => __( 'Radio Buttons', 'e-commerce-jetpack' ),
				'select'   => __( 'Select Box', 'e-commerce-jetpack' ),
				'text'     => __( 'Text', 'e-commerce-jetpack' ),
			),
		),
		array(
			'desc'     => __( 'Title', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_title_' . $i,
			'default'  => '',
			'type'     => 'textarea',
		),
		array(
			'desc'     => __( 'Label(s)', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'For radio and select enter one value per line.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_label_' . $i,
			'default'  => '',
			'type'     => 'textarea',
			'css'      => 'height:100px;',
		),
		array(
			'desc'     => __( 'Price(s)', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'For radio and select enter one value per line.', 'e-commerce-jetpack' ) . '<br /><br />' . __( "You can use the % symbol to set a percentage of product's price, like 10%", 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_price_' . $i,
			'default'  => 0,
			'type'     => 'textarea',
			'css'      => 'height:100px;',
			'custom_attributes' => array( 'step' => '0.0001' ),
		),
		array(
			'desc'     => __( 'Tooltip(s)', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'For radio enter one value per line.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_tooltip_' . $i,
			'default'  => '',
			'type'     => 'textarea',
			'css'      => 'height:100px;',
		),
		array(
			'desc'     => __( 'Default Value', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'For checkbox use \'checked\'; for radio and select enter default label. Leave blank for no default value.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_default_' . $i,
			'default'  => '',
			'type'     => 'text',
		),
		array(
			'desc'    => __( 'Placeholder', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'For "Select Box" type only.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_placeholder_' . $i,
			'default'  => '',
			'type'     => 'text',
		),
		array(
			'desc'    => __( 'HTML Class', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_class_' . $i,
			'default'  => '',
			'type'     => 'text',
		),
		array(
			'desc'     => __( 'Is Required', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_required_' . $i,
			'default'  => 'no',
			'type'     => 'checkbox',
		),
		array(
			'desc'     => __( 'Exclude Products', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_exclude_products_' . $i,
			'default'  => '',
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'options'  => $products,
		),
		array(
			'desc'     => __( 'Quantity', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Leave empty to disable quantity calculation for the addon. When set to zero - addon will be disabled.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_addons_all_products_qty_' . $i,
			'default'  => '',
			'type'     => 'text',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_addons_all_products_options',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_addons_options',
	),
	array(
		'title'    => __( 'Enable AJAX on Single Product Page', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_ajax_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Ignore Strikethrough Price', 'e-commerce-jetpack' ),
		'desc'     => __( 'Ignore Strikethrough Price', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If a product has both regular and sale prices, only the sale price will be updated on AJAX. The regular price will be ignored', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_ajax_ignore_st_price',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Decrease Quantity', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you select multiple triggers to decrease quantity, it will be decreased only once (on whichever trigger is executed first).', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_qty_decrease_triggers',
		'default'  => '',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => $qty_triggers,
	),
	array(
		'title'    => __( 'Admin Order Page', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide all addons', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_hide_on_admin_order_page',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Position on Frontend', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_position',
		'default'  => 'woocommerce_before_add_to_cart_button',
		'type'     => 'select',
		'options'  => array(
			'woocommerce_before_add_to_cart_button' => __( 'Before add to cart button', 'e-commerce-jetpack' ),
			'woocommerce_after_add_to_cart_button'  => __( 'After add to cart button', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Position Priority on Frontend', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set to zero to use the default priority.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_position_priority',
		'default'  => 0,
		'type'     => 'number',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_addons_options',
	),
	array(
		'title'    => __( 'Advanced', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_addons_advanced_options',
	),
	array(
		'title'    => __( 'Apply Price Filter', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_apply_price_filters',
		'default'  => 'by_module',
		'type'     => 'select',
		'options'  => array(
			'by_module' => __( 'By module', 'e-commerce-jetpack' ),
			'yes'       => __( 'Yes', 'e-commerce-jetpack' ),
			'no'        => __( 'No', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc_tip' => __( 'If you have selected "By module" for "Advanced: Apply Price Filter" option, you can set which modules to apply here. Leave empty to apply all modules.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_apply_price_filters_by_module',
		'default'  => array(),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => array(
			'multicurrency_base_price'   => __( 'Multicurrency Product Base Price', 'e-commerce-jetpack' ),
			'multicurrency'              => __( 'Multicurrency (Currency Switcher)', 'e-commerce-jetpack' ),
			'global_discount'            => __( 'Global Discount', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Price Filters Priority', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Priority for all module\'s price filters. If you face pricing issues while using another plugin or booster module, You can change the Priority, Greater value for high priority & Lower value for low priority. Set to zero to use default priority.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_advanced_price_hooks_priority',
		'default'  => 0,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'Check for Outputted Data', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Ensures that data outputted only once. Enable this if you see data outputted on frontend twice. Disable if you see no data outputted.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_check_for_outputted_data',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Export and Import "Enable by Variation"', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Exports/Imports "Enable by Variation" meta when using WooCommerce product Exporter/Importer', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_enable_by_variation_export_import',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_addons_advanced_options',
	),
	array(
		'title'    => __( 'Frontend Templates', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_addons_template_options',
	),
	array(
		'title'    => __( 'Hide Percentage Price', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Hide percentage price when % is set on prices', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_template_hide_percentage_price',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Each Addon - Title', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%addon_id%', '%addon_title%' ) ),
		'id'       => 'wcj_product_addons_template_title',
		'default'  => '<p><label for="%addon_id%">%addon_title%</label></p>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Each Addon - Type: Checkbox', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%addon_input%', '%addon_id%', '%addon_label%', '%addon_price%', '%addon_tooltip%' ) ),
		'id'       => 'wcj_product_addons_template_type_checkbox',
		'default'  => '<p>%addon_input% <label for="%addon_id%">%addon_label% (%addon_price%)</label>%addon_tooltip%</p>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Each Addon - Type: Text', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%addon_input%', '%addon_id%', '%addon_label%', '%addon_price%', '%addon_tooltip%' ) ),
		'id'       => 'wcj_product_addons_template_type_text',
		'default'  => '<p><label for="%addon_id%">%addon_label% (%addon_price%)</label> %addon_input%%addon_tooltip%</p>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Each Addon - Type: Select Box', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%addon_input%', '%addon_tooltip%' ) ),
		'id'       => 'wcj_product_addons_template_type_select',
		'default'  => '<p>%addon_input%%addon_tooltip%</p>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Each Addon - Type: Select Box (Each Option)', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%addon_label%', '%addon_price%' ) ),
		'id'       => 'wcj_product_addons_template_type_select_option',
		'default'  => '%addon_label% (%addon_price%)',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Each Addon - Type: Radio Button (Each)', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%addon_input%', '%addon_id%', '%addon_label%', '%addon_price%', '%addon_tooltip%' ) ),
		'id'       => 'wcj_product_addons_template_type_radio',
		'default'  => '<p>%addon_input% <label for="%addon_id%">%addon_label% (%addon_price%)</label>%addon_tooltip%</p>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'All Addons - Final', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%addons_html%' ) ),
		'id'       => 'wcj_product_addons_template_final',
		'default'  => '<div id="wcj_product_addons">%addons_html%</div>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_addons_template_options',
	),
	array(
		'title'    => __( 'Cart Template', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_addons_template_cart_options',
	),
	array(
		'title'    => __( 'Before', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_cart_format_start',
		'default'  => '<dl class="variation">',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Each Addon', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%addon_label%', '%addon_price%', '%addon_title%' ) ),
		'id'       => 'wcj_product_addons_cart_format_each_addon',
		'default'  => '<dt>%addon_label%:</dt><dd>%addon_price%</dd>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'After', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_cart_format_end',
		'default'  => '</dl>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_addons_template_cart_options',
	),
	array(
		'title'    => __( 'Order Details Table Template', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_addons_template_order_details_options',
	),
	array(
		'title'    => __( 'Before', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_order_details_format_start',
		'default'  => '',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Each Addon', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%addon_label%', '%addon_price%', '%addon_title%' ) ),
		'id'       => 'wcj_product_addons_order_details_format_each_addon',
		'default'  => '&nbsp;| %addon_label%: %addon_price%',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'After', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_addons_order_details_format_end',
		'default'  => '',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_addons_template_order_details_options',
	),
) );
return $settings;
