<?php
/**
 * Booster for WooCommerce - Settings - Product Input Fields
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    [dev] maybe set "Strip slashes" option to `yes` by default (or even remove the option completely and always strip slashes)
 * @todo    [dev] maybe set "Replace Field ID with Field Label" option (i.e. `wcj_product_input_fields_make_nicer_name_enabled`) to `no` by default
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Product Input Fields per Product Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'Add custom input fields to product\'s single page for customer to fill before adding product to cart.', 'e-commerce-jetpack' ) . ' '
			. __( 'When enabled this module will add "Product Input Fields" tab to each product\'s "Edit" page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_local_options',
	),
	array(
		'title'    => __( 'Product Input Fields - per Product', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable', 'e-commerce-jetpack' ) . '</strong>',
		'desc_tip' => __( 'Add custom input field on per product basis. <br>key: <code>_wcj_product_input_fields_global_&ltfield_id&gt</code><br> field_id will available in meta box of <b>Booster: Product Input Fields</b> on product page ', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_local_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Default Number of Product Input Fields per Product', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_local_total_number_default',
		'desc_tip' => __( 'You will be able to change this number later as well as define the fields, for each product individually, in product\'s "Edit".', 'e-commerce-jetpack' ),
		'default'  => 1,
		'type'     => 'number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ? apply_filters( 'booster_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min'  => '1' )
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_input_fields_local_options',
	),
	array(
		'title'    => __( 'Product Input Fields Global Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'Add custom input fields to product\'s single page for customer to fill before adding product to cart.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_global_options',
	),
	array(
		'title'    => __( 'Product Input Fields - All Products', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable', 'e-commerce-jetpack' ) . '</strong>',
		'desc_tip' => __( 'Add custom input fields to all products.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_global_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Product Input Fields Number', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Click Save changes after you change this number.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_global_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ? apply_filters( 'booster_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min'  => '1' )
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_input_fields_global_options',
	),
);
$is_multiselect_products     = ( 'yes' === wcj_get_option( 'wcj_list_for_products', 'yes' ) );
$product_cats                = wcj_get_terms( 'product_cat' );
$product_tags                = wcj_get_terms( 'product_tag' );
$options                     = $this->get_global_product_fields_options();
for ( $i = 1; $i <= apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_product_input_fields_global_total_number', 1 ) ); $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Product Input Field', 'e-commerce-jetpack' ) . ' #' . $i,
			'type'     => 'title',
			'id'       => 'wcj_product_input_fields_global_options_' . $i,
		),
	) );
	foreach( $options as $option ) {
		if( !isset( $option['desc_tip'] ) && $option['id'] == 'wcj_product_input_fields_enabled_global_' ){
			$option['desc_tip'] = __('key: <code>_wcj_product_input_fields_global_' . $i . '</code>' , 'e-commerce-jetpack' );
		}
		$settings = array_merge( $settings, array(
			array(
				'title'    => ( isset( $option['short_title'] ) ? $option['short_title'] : $option['title'] ),
				'desc'     => ( ( 'checkbox' === $option['type'] ) || isset( $option['short_title'] ) && $option['short_title'] != $option['title'] ? $option['title'] : '' ),
				'desc_tip' => ( isset( $option['desc_tip'] ) ) ? $option['desc_tip'] : '',
				'id'       => $option['id'] . $i,
				'default'  => $option['default'],
				'type'     => $option['type'],
				'options'  => isset( $option['options'] ) ? $option['options'] : '',
				'css'      => ( 'wcj_product_input_fields_type_select_options_global_' === $option['id'] ?
					'width:30%;min-width:300px;height:200px;' : 'width:30%;min-width:300px;' ),
			),
		) );
	}
	wcj_maybe_convert_and_update_option_value( array(
		array( 'id' => 'wcj_product_input_fields_in_products_' . 'global' . '_' . $i, 'default' => '' ),
		array( 'id' => 'wcj_product_input_fields_ex_products_' . 'global' . '_' . $i, 'default' => '' ),
	), $is_multiselect_products );
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Product Categories - Include', 'e-commerce-jetpack' ),
			'desc'     => __( 'Product categories to include.', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Leave blank to include all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_input_fields_in_cats_' . 'global' . '_' . $i,
			'default'  => '',
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'css'      => 'width: 450px;',
			'options'  => $product_cats,
		),
		array(
			'title'    => __( 'Product Categories - Exclude', 'e-commerce-jetpack' ),
			'desc'     => __( 'Product categories to exclude.', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Leave blank to include all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_input_fields_ex_cats_' . 'global' . '_' . $i,
			'default'  => '',
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'css'      => 'width: 450px;',
			'options'  => $product_cats,
		),
		array(
			'title'    => __( 'Product Tags - Include', 'e-commerce-jetpack' ),
			'desc'     => __( 'Product tags to include.', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Leave blank to include all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_input_fields_in_tags_' . 'global' . '_' . $i,
			'default'  => '',
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'css'      => 'width: 450px;',
			'options'  => $product_tags,
		),
		array(
			'title'    => __( 'Product Tags - Exclude', 'e-commerce-jetpack' ),
			'desc'     => __( 'Product tags to exclude.', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Leave blank to include all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_input_fields_ex_tags_' . 'global' . '_' . $i,
			'default'  => '',
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'css'      => 'width: 450px;',
			'options'  => $product_tags,
		),
		wcj_get_settings_as_multiselect_or_text(
			array(
				'title'    => __( 'Products - Include', 'e-commerce-jetpack' ),
				'desc'     => __( 'Products to include.', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'Leave blank to include all products.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_product_input_fields_in_products_' . 'global' . '_' . $i,
				'default'  => '',
				'css'      => 'width: 450px;',
			),
			'',
			$is_multiselect_products
		),
		wcj_get_settings_as_multiselect_or_text(
			array(
				'title'    => __( 'Products - Exclude', 'e-commerce-jetpack' ),
				'desc'     => __( 'Products to exclude.', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'Leave blank to include all products.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_product_input_fields_ex_products_' . 'global' . '_' . $i,
				'default'  => '',
				'css'      => 'width: 450px;',
			),
			'',
			$is_multiselect_products
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'wcj_product_input_fields_global_options_' . $i,
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Frontend View Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_input_fields_frontend_view_options',
	),
	array(
		'title'    => __( 'Position on Single Product Page', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_position',
		'default'  => 'woocommerce_before_add_to_cart_button',
		'type'     => 'select',
		'options'  => array(
			'woocommerce_before_add_to_cart_button'     => __( 'Before add to cart button', 'e-commerce-jetpack' ),
			'woocommerce_after_add_to_cart_button'      => __( 'After add to cart button', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Position priority', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_position_priority',
		'default'  => 100,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'HTML Template - Start', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_start_template',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'HTML Template - Each Field', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_field_template',
		'default'  => '<p><label for="%field_id%">%field_title%</label> %field_html%</p>',
		'type'     => 'custom_textarea',
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'HTML Template - End', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_end_template',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'HTML Template - Radio Field', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_field_template_radio',
		'default'  => '%radio_field_html%<label for="%radio_field_id%" class="radio">%radio_field_title%</label><br>',
		'type'     => 'custom_textarea',
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'HTML to add after required field title', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_frontend_view_required_html',
		'default'  => '&nbsp;<abbr class="required" title="required">*</abbr>',
		'type'     => 'custom_textarea',
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'Cart Display Options', 'e-commerce-jetpack' ),
		'desc'     => __( 'When "Add to cart item data" is selected, "Cart HTML Template" options below will be ignored.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_display_options',
		'default'  => 'name',
		'type'     => 'select',
		'options'  => array(
			'name' => __( 'Add to cart item name', 'e-commerce-jetpack' ),
			'data' => __( 'Add to cart item data', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Cart HTML Template - Start', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_cart_start_template',
		'default'  => '<dl style="font-size:smaller;">',
		'type'     => 'custom_textarea',
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'Cart HTML Template - Each Field', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_cart_field_template',
		'default'  => '<dt>%title%</dt><dd>%value%</dd>',
		'type'     => 'custom_textarea',
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'Cart HTML Template - End', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_cart_end_template',
		'default'  => '</dl>',
		'type'     => 'custom_textarea',
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'Order Table Template - Each Field', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Affects Order received page, Emails and Admin Orders View', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_frontend_view_order_table_format',
		'default'  => '&nbsp;| %title% %value%',
		'type'     => 'custom_textarea',
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'Preserve Line Breaks', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Preserves line breaks on frontend, making some inputs like textarea more legible', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_frontend_linebreaks',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_input_fields_frontend_view_options',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Emails Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_input_fields_emails_options',
	),
	array(
		'title'    => __( 'Attach Files to Admin\'s New Order Emails', 'e-commerce-jetpack' ),
		'desc'     => __( 'Attach', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_attach_to_admin_new_order',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Attach Files to Customer\'s Processing Order Emails', 'e-commerce-jetpack' ),
		'desc'     => __( 'Attach', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_attach_to_customer_processing_order',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_input_fields_emails_options',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Admin Order View Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_input_fields_admin_view_options',
	),
	array(
		'title'    => __( 'Replace Field ID with Field Label', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Please note: %s', 'e-commerce-jetpack' ),
			__( 'When checked - will disable input fields editing on admin order edit page.', 'e-commerce-jetpack' ) ),
		'id'       => 'wcj_product_input_fields_make_nicer_name_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Preserve Line Breaks', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Preserves line breaks on admin, making some inputs like textarea more legible', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_admin_linebreaks',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_input_fields_admin_view_options',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Advanced Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_input_fields_advanced_options',
	),
	array(
		'title'    => __( 'Check for Outputted Data', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Ensures that data outputted only once. Enable this if you see data outputted on frontend twice. Disable if you see no data outputted.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_input_fields_check_for_outputted_data',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Strip Slashes', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Enable this if you have single quotes %s converted to %s.', 'e-commerce-jetpack' ), '<code>\'</code>', '<code>\\\'</code>' ),
		'id'       => 'wcj_product_input_fields_stripslashes',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_input_fields_advanced_options',
	),
) );
return $settings;
