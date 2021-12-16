<?php
/**
 * Booster for WooCommerce - Settings - Offer Price
 *
 * @version 5.1.0
 * @since   2.9.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$type_options = array(
	'all_products'                 => __( 'Enable for all products', 'e-commerce-jetpack' ),
	'empty_prices'                 => __( 'Enable for all products with empty price', 'e-commerce-jetpack' ),
	'per_product'                  => __( 'Enable per product', 'e-commerce-jetpack' ),
	'per_category'                 => __( 'Enable per product category', 'e-commerce-jetpack' ),
	'per_product_and_per_category' => __( 'Enable per product and per product category', 'e-commerce-jetpack' ),
);

return array(
	array(
		'title'    => __( 'General Options', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_general_options',
		'type'     => 'title',
	),
	array(
		'title'    => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Possible values: %s.', 'e-commerce-jetpack' ), implode( '; ', $type_options ) ) . ' ' .
			__( 'If enable per product is selected, this will add new meta box to each product\'s edit page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_enabled_type',
		'type'     => 'select',
		'default'  => 'all_products',
		'options'  => $type_options,
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'desc'     => __( 'Product categories', 'e-commerce-jetpack' ) . '<br>' . apply_filters( 'booster_message', '', 'desc' ),
		'desc_tip' => __( 'Ignored if enable per product category is not selected above.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_enabled_cats',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'default'  => array(),
		'options'  => wcj_get_terms( 'product_cat' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Exclude', 'e-commerce-jetpack' ),
		'desc'     => __( 'Out of stock', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Excludes out of stock products.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_exclude_out_of_stock',
		'type'     => 'checkbox',
		'default'  => 'no',
	),
	array(
		'id'       => 'wcj_offer_price_general_options',
		'type'     => 'sectionend',
	),
	array(
		'title'    => __( 'Button Options', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_button_options',
		'type'     => 'title',
	),
	array(
		'title'    => __( 'Label', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_button_label',
		'type'     => 'text',
		'default'  => __( 'Make an offer', 'e-commerce-jetpack' ),
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'CSS Class', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_button_class',
		'type'     => 'text',
		'default'  => 'button',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'CSS Style', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_button_style',
		'type'     => 'text',
		'default'  => '',
		'css'      => 'width:100%;',
		'desc'     => sprintf( __( 'E.g.: %s', 'e-commerce-jetpack' ), '<code>background-color: #333333; border-color: #333333; color: #ffffff;</code>' ),
	),
	array(
		'title'    => __( 'Position On Single Product Page', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_button_position',
		'type'     => 'select',
		'default'  => 'woocommerce_single_product_summary',
		'options'  => array(
			'disable'                                   => __( 'Do not add', 'e-commerce-jetpack' ),
			'woocommerce_before_single_product'         => __( 'Before single product', 'e-commerce-jetpack' ),
			'woocommerce_before_single_product_summary' => __( 'Before single product summary', 'e-commerce-jetpack' ),
			'woocommerce_single_product_summary'        => __( 'Inside single product summary', 'e-commerce-jetpack' ),
			'woocommerce_before_add_to_cart_form'       => __( 'Before add to cart form', 'e-commerce-jetpack' ),
			'woocommerce_after_add_to_cart_form'        => __( 'After add to cart form', 'e-commerce-jetpack' ),
			'woocommerce_after_single_product_summary'  => __( 'After single product summary', 'e-commerce-jetpack' ),
			'woocommerce_after_single_product'          => __( 'After single product', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Position Priority (i.e. Order)', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_button_position_priority',
		'type'     => 'number',
		'default'  => 31,
	),
	array(
		'title'    => __( 'Position On Archive Pages', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Possible values: Do not add; Before product; After product.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_button_position_archives',
		'type'     => 'select',
		'default'  => 'disable',
		'options'  => array(
			'disable'                                 => __( 'Do not add', 'e-commerce-jetpack' ),
			'woocommerce_before_shop_loop_item'       => __( 'Before product', 'e-commerce-jetpack' ),
			'woocommerce_after_shop_loop_item'        => __( 'After product', 'e-commerce-jetpack' ),
		),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'desc'     => __( 'Position Priority (i.e. Order)', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_button_position_priority_archives',
		'type'     => 'number',
		'default'  => 10,
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Advanced: Custom Position(s)', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Add custom hook. If adding more than one hook, separate with vertical bar ( | ). Ignored if empty.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_button_position_custom',
		'type'     => 'textarea',
		'default'  => '',
		'css'      => 'width:100%;',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'desc'     => __( 'Custom Position Priority (i.e. Order)', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Add custom hook priority. If adding more than one hook, separate with vertical bar ( | ).', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_button_position_priority_custom',
		'type'     => 'textarea',
		'default'  => '',
		'css'      => 'width:100%;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'id'       => 'wcj_offer_price_button_options',
		'type'     => 'sectionend',
	),
	array(
		'title'    => __( 'Form and Notice Options', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_form_options',
		'type'     => 'title',
	),
	array(
		'title'    => __( 'Price Input', 'e-commerce-jetpack' ),
		'desc'     => __( 'Label', 'e-commerce-jetpack' ) .
			'. ' . wcj_message_replaced_values( array( '%currency_symbol%' ) ),
		'id'       => 'wcj_offer_price_price_label',
		'type'     => 'custom_textarea',
		'default'  => sprintf( __( 'Your price (%s)', 'e-commerce-jetpack' ), '%currency_symbol%' ),
		'css'      => 'width:100%;',
	),
	array(
		'desc'     => __( 'Price Step', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Number of decimals', 'woocommerce' ),
		'id'       => 'wcj_offer_price_price_step',
		'type'     => 'number',
		'default'  => wcj_get_option( 'woocommerce_price_num_decimals' ),
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'desc'     => __( 'Minimal Price', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_min_price',
		'type'     => 'number',
		'default'  => 0,
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'desc'     => __( 'Maximal Price', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set zero to disable.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_max_price',
		'type'     => 'number',
		'default'  => 0,
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'desc'     => __( 'Default Price', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set zero to disable.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_default_price',
		'type'     => 'number',
		'default'  => 0,
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'title'    => __( 'Customer Email', 'e-commerce-jetpack' ),
		'desc'     => __( 'Label', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_customer_email',
		'type'     => 'custom_textarea',
		'default'  => __( 'Your email', 'e-commerce-jetpack' ),
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Customer Name', 'e-commerce-jetpack' ),
		'desc'     => __( 'Label', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_customer_name',
		'type'     => 'custom_textarea',
		'default'  => __( 'Your name', 'e-commerce-jetpack' ),
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Customer Message', 'e-commerce-jetpack' ),
		'desc'     => __( 'Label', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_customer_message',
		'type'     => 'custom_textarea',
		'default'  => __( 'Your message', 'e-commerce-jetpack' ),
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Send a Copy to Customer Checkbox', 'e-commerce-jetpack' ),
		'desc'     => __( 'Label', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_customer_copy',
		'type'     => 'custom_textarea',
		'default'  => __( 'Send a copy to your email', 'e-commerce-jetpack' ),
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Form Header', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%product_title%' ) ),
		'id'       => 'wcj_offer_price_form_header_template',
		'type'     => 'custom_textarea',
		'default'  => '<h3>' . sprintf( __( 'Suggest your price for %s', 'e-commerce-jetpack' ), '%product_title%' ) . '</h3>',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Form Button Label', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_form_button_label',
		'type'     => 'text',
		'default'  => __( 'Send', 'e-commerce-jetpack' ),
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Form Footer', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_form_footer_template',
		'type'     => 'custom_textarea',
		'default'  => '',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Required HTML', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_form_required_html',
		'type'     => 'custom_textarea',
		'default'  => ' <abbr class="required" title="required">*</abbr>',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Customer Notice', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_customer_notice',
		'type'     => 'custom_textarea',
		'default'  => __( 'Your price offer has been sent.', 'e-commerce-jetpack' ),
		'css'      => 'width:100%;',
	),
	array(
		'id'       => 'wcj_offer_price_form_options',
		'type'     => 'sectionend',
	),
	array(
		'title'    => __( 'Styling Options', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_styling_options',
		'type'     => 'title',
	),
	array(
		'title'    => __( 'Form Width', 'e-commerce-jetpack' ),
		'id'       => "wcj_offer_price_styling[form_content_width]",
		'type'     => 'text',
		'default'  => '80%',
	),
	array(
		'title'    => __( 'Header Background Color', 'e-commerce-jetpack' ),
		'id'       => "wcj_offer_price_styling[form_header_back_color]",
		'type'     => 'color',
		'default'  => '#5cb85c',
	),
	array(
		'title'    => __( 'Header Text Color', 'e-commerce-jetpack' ),
		'id'       => "wcj_offer_price_styling[form_header_text_color]",
		'type'     => 'color',
		'default'  => '#ffffff',
	),
	array(
		'title'    => __( 'Footer Background Color', 'e-commerce-jetpack' ),
		'id'       => "wcj_offer_price_styling[form_footer_back_color]",
		'type'     => 'color',
		'default'  => '#5cb85c',
	),
	array(
		'title'    => __( 'Footer Text Color', 'e-commerce-jetpack' ),
		'id'       => "wcj_offer_price_styling[form_footer_text_color]",
		'type'     => 'color',
		'default'  => '#ffffff',
	),
	array(
		'id'       => 'wcj_offer_price_styling_options',
		'type'     => 'sectionend',
	),
	array(
		'title'    => __( 'Email Options', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_email_options',
		'type'     => 'title',
	),
	array(
		'title'    => __( 'Email Recipient', 'e-commerce-jetpack' ),
		'desc'     => __( 'Can be comma separated list.', 'e-commerce-jetpack' ) . ' ' .
			sprintf(
				__( 'Use %s to send to administrator email: %s.',
				'e-commerce-jetpack' ), '<code>' . '%admin_email%' . '</code>',
				'<code>' . wcj_get_option( 'admin_email' ) . '</code>'
			) . ' ' .
			wcj_message_replaced_values( array( '%admin_email%', '%product_author_email%' ) ),
		'id'       => 'wcj_offer_price_email_address',
		'type'     => 'custom_textarea',
		'default'  => '%admin_email%',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Email Subject', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_email_subject',
		'type'     => 'text',
		'default'  => __( 'Price Offer', 'e-commerce-jetpack' ),
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Email Template', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%product_title%', '%product_edit_link%', '%offered_price%', '%customer_name%', '%customer_email%', '%customer_message%', '%user_ip%', '%user_agent%' ) ),
		'id'       => 'wcj_offer_price_email_template',
		'type'     => 'custom_textarea',
		'default'  =>
			sprintf( __( 'Product: %s', 'e-commerce-jetpack' ),       '<a href="%product_edit_link%">%product_title%</a>' ) . '<br>' . PHP_EOL .
			sprintf( __( 'Offered price: %s', 'e-commerce-jetpack' ), '%offered_price%' ) . '<br>' . PHP_EOL .
			sprintf( __( 'From: %s %s', 'e-commerce-jetpack' ),       '%customer_name%', '%customer_email%' ) . '<br>' . PHP_EOL .
			sprintf( __( 'Message: %s', 'e-commerce-jetpack' ),       '%customer_message%' ),
		'css'      => 'width:100%;height:200px;',
	),
	array(
		'id'       => 'wcj_offer_price_email_options',
		'type'     => 'sectionend',
	),
	array(
		'title'    => __( 'Admin Options', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_admin_options',
		'type'     => 'title',
	),
	array(
		'title'    => __( 'Offer Price History Meta Box Columns', 'e-commerce-jetpack' ),
		'id'       => 'wcj_offer_price_admin_meta_box_columns',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'default'  => array( 'date', 'offered_price', 'customer_message', 'customer_name', 'customer_email', 'customer_id', 'user_ip', 'sent_to' ),
		'options'  => $this->get_admin_meta_box_columns(),
	),
	array(
		'id'       => 'wcj_offer_price_admin_options',
		'type'     => 'sectionend',
	),
);
