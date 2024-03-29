<?php
/**
 * Booster for WooCommerce - Settings - EU VAT Number
 *
 * @version 4.9.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    set default value for "wcj_eu_vat_number_add_progress_text" to "yes"
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_eu_vat_number_options',
	),
	array(
		'title'    => __( 'Field Label', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_field_label',
		'default'  => __( 'EU VAT Number', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Placeholder', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_field_placeholder',
		'default'  => __( 'EU VAT Number', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Description', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_field_description',
		'default'  => '',
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	/*
	array(
		'title'    => __( 'Require Country Code in VAT Number', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_field_require_country_code',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	*/
	array(
		'title'    => __( 'Required', 'e-commerce-jetpack' ),
		'desc'     => __( 'Yes', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_field_required',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Clear', 'e-commerce-jetpack' ),
		'desc'     => __( 'Yes', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_field_clear',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Class', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_field_class',
		'default'  => 'form-row-wide',
		'type'     => 'select',
		'options'  => array(
			'form-row-wide'  => __( 'Wide', 'e-commerce-jetpack' ),
			'form-row-first' => __( 'First', 'e-commerce-jetpack' ),
			'form-row-last'  => __( 'Last', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Validate', 'e-commerce-jetpack' ),
		'desc'     => __( 'Yes', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_validate',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Message on not valid', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_not_valid_message',
		'default'  => __( '<strong>EU VAT Number</strong> is not valid.', 'e-commerce-jetpack' ),
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'First Validation Method', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Change this if you are having issues when validating VAT. This only selects first method to try - if not succeeded, remaining methods will be used for validation.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_first_method',
		'default'  => 'soap',
		'type'     => 'select',
		'options'  => array(
			'soap'              => __( 'SOAP', 'e-commerce-jetpack' ),
			'curl'              => __( 'cURL', 'e-commerce-jetpack' ),
			'file_get_contents' => __( 'Simple', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Exempt VAT for Valid Numbers', 'e-commerce-jetpack' ),
		'desc'     => __( 'Yes', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_disable_for_valid',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Exempt VAT on Cart', 'e-commerce-jetpack' ),
		'desc'     => __( 'Yes', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Exempts VAT even on Cart page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_disable_for_valid_on_cart',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Show VAT field for EU countries only', 'e-commerce-jetpack' ),
		'desc'     => __( 'Yes', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_show_vat_field_for_eu_only',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Exempt VAT by Customer\'s EU VAT', 'e-commerce-jetpack' ),
		'desc'     => __( 'Yes', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Exempts VAT by checking previously registered EU VAT numbers from customers.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_disable_for_valid_by_user_vat',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Preserve VAT in Base Country', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'This will validate the VAT, but won\'t exempt VAT for base country VAT numbers. Base (i.e. store) country is set in %s.', 'e-commerce-jetpack' ),
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=general' ) . '">' . __( 'WooCommerce > Settings > General', 'e-commerce-jetpack' ) . '</a>' ) . '<br>' .
			apply_filters( 'booster_message', '', 'desc' ),
		'desc'     => __( 'Yes', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_preserve_in_base_country',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	/*
	array(
		'desc'     => __( 'Message if customer is in base country and VAT is NOT exempted.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_preserve_in_base_country_message',
		'default'  => __( 'EU VAT Number is valid, however VAT is not exempted.', 'e-commerce-jetpack' ),
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	*/
	array(
		'title'    => __( 'Check for IP Location Country', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will check if customer\'s country (located by customer\'s IP) matches the country in entered VAT number.', 'e-commerce-jetpack' ) . '<br>' .
			apply_filters( 'booster_message', '', 'desc' ),
		'desc'     => __( 'Yes', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_check_ip_location_country',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	/*
	array(
		'desc'     => __( 'Message if customer\'s check for IP location country has failed.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_check_ip_location_country_message',
		'default'  => __( 'IP must be from same country as VAT ID.', 'e-commerce-jetpack' ),
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	*/
	array(
		'title'    => __( 'Display', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_display_position',
		'default'  => 'after_order_table',
		'type'     => 'select',
		'options'  => array(
			'after_order_table'  => __( 'After order table', 'e-commerce-jetpack' ),
			'in_billing_address' => __( 'In billing address', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Add Progress Messages', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_add_progress_text',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Progress Message: Validating', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_progress_text_validating',
		'default'  => __( 'Validating VAT. Please wait...', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Progress Message: Valid', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_progress_text_valid',
		'default'  => __( 'VAT is valid.', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Progress Message: Not Valid', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_progress_text_not_valid',
		'default'  => __( 'VAT is not valid.', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Progress Message: Validation Failed', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Message on VAT validation server timeout etc.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_progress_text_validation_failed',
		'default'  => __( 'Validation failed. Please try again.', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Add EU VAT Number Summary Meta Box to Admin Order Edit Page', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_add_order_edit_metabox',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'             => __( 'Restrictive Loading', 'e-commerce-jetpack' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enqueues module scripts on some conditions.', 'e-commerce-jetpack' ) . '<br />' . __( 'Probably the best options are <code>Is Cart</code> and <code>Is Checkout</code>', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => __( 'Leave it empty to load it in all situations.', 'e-commerce-jetpack' ),
		'id'                => 'wcj_eu_vat_number_restrictive_loading',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'default'           => '',
		'class'             => 'chosen_select',
		'options'           => array(
			'is_woocommerce'      => __( 'Is WooCommerce', 'e-commerce-jetpack' ),
			'is_shop'             => __( 'Is Shop', 'e-commerce-jetpack' ),
			'is_product_category' => __( 'Is Product Category', 'e-commerce-jetpack' ),
			'is_product_tag'      => __( 'Is Product Tag', 'e-commerce-jetpack' ),
			'is_product'          => __( 'Is Product', 'e-commerce-jetpack' ),
			'is_cart'             => __( 'Is Cart', 'e-commerce-jetpack' ),
			'is_checkout'         => __( 'Is Checkout', 'e-commerce-jetpack' ),
			'is_account_page'     => __( 'Is Account Page', 'e-commerce-jetpack' ),
			'is_wc_endpoint_url'  => __( 'Is WC Endpoint URL', 'e-commerce-jetpack' ),
			'is_ajax'             => __( 'Is AJAX', 'e-commerce-jetpack' ),
		),
		'type'              => 'multiselect',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_eu_vat_number_options',
	),
	array(
		'title'    => __( 'Advanced Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_eu_vat_number_advanced_options',
	),
	array(
		'title'    => __( 'Skip VAT Validation for Selected Countries', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'List all countries you want VAT validation to be skipped for (i.e. VAT always valid). Ignored if empty.', 'e-commerce-jetpack' ),
		'desc'     => sprintf( __( 'Enter country codes as comma separated list, e.g. %s.', 'e-commerce-jetpack' ), '<code>IT,NL</code>' ),
		'id'       => 'wcj_eu_vat_number_advanced_skip_countries',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'title'    => __( "Read '_vat_number' meta", 'e-commerce-jetpack' ),
		'desc_tip' => sprintf(__( "Try to add compatibility with <a href='%s' target='_blank'>EU VAT Number</a> plugin, reading meta from '_vat_number'.", 'e-commerce-jetpack' ),'https://woocommerce.com/products/eu-vat-number/'),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_eu_vat_number_read_vat_number_meta',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_eu_vat_number_advanced_options',
	),
);
return $settings;
