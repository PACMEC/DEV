<?php
/**
 * Booster for WooCommerce - Settings - Checkout Custom Fields
 *
 * @version 5.3.3
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$product_cats = wcj_get_terms( 'product_cat' );
$products     = wcj_get_products();
$settings = array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_custom_fields_options',
	),
	array(
		'title'    => __( 'Add All Fields to Admin Emails', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_custom_fields_email_all_to_admin',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Add All Fields to Customers Emails', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_custom_fields_email_all_to_customer',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Emails Fields Template', 'e-commerce-jetpack' ),
		'desc'     => __( 'Before the fields', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_custom_fields_emails_template_before',
		'default'  => '',
		'type'     => 'textarea',
	),
	array(
		'desc'     => __( 'Each field', 'e-commerce-jetpack' ) . '. ' . wcj_message_replaced_values( array( '%label%', '%value%' ) ),
		'id'       => 'wcj_checkout_custom_fields_emails_template_field',
		'default'  => '<p><strong>%label%:</strong> %value%</p>',
		'type'     => 'textarea',
	),
	array(
		'desc'     => __( 'After the fields', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_custom_fields_emails_template_after',
		'default'  => '',
		'type'     => 'textarea',
	),
	array(
		'title'    => __( 'Add All Fields to "Order Received" (i.e. "Thank You") and "View Order" Pages', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_custom_fields_add_to_order_received',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( '"Order Received" Fields Template', 'e-commerce-jetpack' ),
		'desc'     => __( 'Before the fields', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_custom_fields_order_received_template_before',
		'default'  => '',
		'type'     => 'textarea',
	),
	array(
		'desc'     => __( 'Each field', 'e-commerce-jetpack' ) . '. ' . wcj_message_replaced_values( array( '%label%', '%value%' ) ),
		'id'       => 'wcj_checkout_custom_fields_order_received_template_field',
		'default'  => '<p><strong>%label%:</strong> %value%</p>',
		'type'     => 'textarea',
	),
	array(
		'desc'     => __( 'After the fields', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_custom_fields_order_received_template_after',
		'default'  => '',
		'type'     => 'textarea',
	),
	array(
		'title'    => __( 'Textarea Field Values', 'e-commerce-jetpack' ),
		'desc'     => __( 'When saving, "clean" textarea field values', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_custom_fields_textarea_clean',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Textarea Line Breaks', 'e-commerce-jetpack' ),
		'desc'     => sprintf( __( 'When displaying, replace line breaks with %s in textarea field values', 'e-commerce-jetpack' ), '<code>&lt;br&gt;</code>' ),
		'desc_tip' => __( 'Does <strong>not</strong> affect admin order edit page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_custom_fields_textarea_replace_line_breaks',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Custom Fields Number', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Click Save changes after you change this number.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_custom_fields_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ? apply_filters( 'booster_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min' => '1' )
		),
		'css'      => 'width:100px;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_custom_fields_options',
	),
);
for ( $i = 1; $i <= apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_checkout_custom_fields_total_number', 1 ) ); $i++ ) {
	$settings = array_merge( $settings,
		array(
			array(
				'title'    => __( 'Custom Field', 'e-commerce-jetpack' ) . ' #' . $i,
				'type'     => 'title',
				'id'       => 'wcj_checkout_custom_fields_options_' . $i,
			),
			array(
				'title'    => __( 'Enable/Disable', 'e-commerce-jetpack' ),
				'desc'     => '<strong>' . __( 'Enable', 'e-commerce-jetpack' ) . '</strong>',
				'desc_tip' => __( 'Key', 'e-commerce-jetpack' ) . ': ' .
					'<code>' . wcj_get_option( 'wcj_checkout_custom_field_section_' . $i, 'billing' ) . '_' . 'wcj_checkout_field_' . $i . '</code>',
				'id'       => 'wcj_checkout_custom_field_enabled_' . $i,
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Type', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_type_' . $i,
				'default'  => 'text',
				'type'     => 'select',
				'options'  => array(
					'text'       => __( 'Text', 'e-commerce-jetpack' ),
					'textarea'   => __( 'Textarea', 'e-commerce-jetpack' ),
					'number'     => __( 'Number', 'e-commerce-jetpack' ),
					'checkbox'   => __( 'Checkbox', 'e-commerce-jetpack' ),
					'datepicker' => __( 'Datepicker', 'e-commerce-jetpack' ),
					'weekpicker' => __( 'Weekpicker', 'e-commerce-jetpack' ),
					'timepicker' => __( 'Timepicker', 'e-commerce-jetpack' ),
					'select'     => __( 'Select', 'e-commerce-jetpack' ),
					'radio'      => __( 'Radio', 'e-commerce-jetpack' ),
					'password'   => __( 'Password', 'e-commerce-jetpack' ),
					'country'    => __( 'Country', 'e-commerce-jetpack' ),
					'state'      => __( 'State', 'e-commerce-jetpack' ),
					'email'      => __( 'Email', 'e-commerce-jetpack' ),
					'tel'        => __( 'Phone', 'e-commerce-jetpack' ),
				),
			),
			array(
				'title'    => __( 'Required', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_required_' . $i,
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Label', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_label_' . $i,
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'min-width:300px;',
			),
			array(
				'title'    => __( 'Placeholder', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_placeholder_' . $i,
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'min-width:300px;',
			),
			array(
				'title'    => __( 'Description', 'e-commerce-jetpack' ),
				'desc'     => __( 'You can use HTML here.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_description_' . $i,
				'default'  => '',
				'type'     => 'custom_textarea',
				'css'      => 'min-width:300px;',
			),
			array(
				'title'    => __( 'Priority (i.e. Order)', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_priority_' . $i,
				'default'  => '',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
			),
			array(
				'title'    => __( 'Section', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_section_' . $i,
				'default'  => 'billing',
				'type'     => 'select',
				'options'  => array(
					'billing'   => __( 'Billing', 'e-commerce-jetpack' ),
					'shipping'  => __( 'Shipping', 'e-commerce-jetpack' ),
					'order'     => __( 'Order Notes', 'e-commerce-jetpack' ),
					'account'   => __( 'Account', 'e-commerce-jetpack' ),
				),
			),
			array(
				'title'    => __( 'Class', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_class_' . $i,
				'default'  => 'form-row-wide',
				'type'     => 'select',
				'options'  => array(
					'form-row-wide'  => __( 'Wide', 'e-commerce-jetpack' ),
					'form-row-first' => __( 'First', 'e-commerce-jetpack' ),
					'form-row-last'  => __( 'Last', 'e-commerce-jetpack' ),
				),
			),
			array(
				'title'    => __( 'Clear', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_clear_' . $i,
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Customer Meta Fields', 'e-commerce-jetpack' ),
				'desc'     => __( 'Add', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_customer_meta_fields_' . $i,
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Select/Radio: Options', 'e-commerce-jetpack' ),
				'desc'     => __( 'One option per line', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_select_options_' . $i,
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'min-width:300px;height:150px;',
			),
			array(
				'title'    => __( 'Select: Use select2 Library', 'e-commerce-jetpack' ),
				'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_select_select2_' . $i,
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'select2: min input length', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'select2: Number of characters necessary to start a search.', 'e-commerce-jetpack' ) . ' ' .
					__( 'Ignored if set to zero.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_select_select2_min_input_length' . $i,
				'default'  => 0,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0 ),
			),
			array(
				'desc'     => __( 'select2: max input length', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'select2: Maximum number of characters that can be entered for an input.', 'e-commerce-jetpack' ) . ' ' .
					__( 'Ignored if set to zero.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_select_select2_max_input_length' . $i,
				'default'  => 0,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0 ),
			),
			array(
				'id'       => 'wcj_checkout_custom_field_checkbox_yes_' . $i,
				'title'    => __( 'Checkbox: Value for ON', 'e-commerce-jetpack' ),
				'type'     => 'text',
				'default'  => __( 'Yes', 'e-commerce-jetpack' ),
			),
			array(
				'id'       => 'wcj_checkout_custom_field_checkbox_no_' . $i,
				'title'    => __( 'Checkbox: Value for OFF', 'e-commerce-jetpack' ),
				'type'     => 'text',
				'default'  => __( 'No', 'e-commerce-jetpack' ),
			),
			array(
				'id'       => 'wcj_checkout_custom_field_checkbox_default_' . $i,
				'title'    => __( 'Checkbox: Default Value', 'e-commerce-jetpack' ),
				'type'     => 'select',
				'default'  => 'no',
				'options'  => array(
					'no'  => __( 'Not Checked', 'e-commerce-jetpack' ),
					'yes' => __( 'Checked', 'e-commerce-jetpack' ),
				),
			),
			array(
				'title'    => __( 'Datepicker/Weekpicker: Date Format', 'e-commerce-jetpack' ),
				'desc'     => __( 'Visit <a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">documentation on date and time formatting</a> for valid date formats', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'Leave blank to use your current WordPress format', 'e-commerce-jetpack' ) . ': ' . get_option( 'date_format' ) . "</br>" . __( 'Use Y-m-d format if you want to use this field for sorting', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_datepicker_format_' . $i,
				'type'     => 'text',
				'default'  => '',
			),
			array(
				'title'    => __( 'Datepicker/Weekpicker: Min Date', 'e-commerce-jetpack' ),
				'desc'     => __( 'days', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_datepicker_mindate_' . $i,
				'type'     => 'number',
				'default'  => -365,
			),
			array(
				'title'    => __( 'Datepicker/Weekpicker: Current day time limit', 'e-commerce-jetpack' ),
				'desc_tip'     => __( 'If the Min Date is 0, Today\'s date will be no longer available after selected time limit.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_datepicker_current_day_time_limit_' . $i,
				'type'     => 'time',
				'default'  => "10:00",
				'css'      => "width:400px",
			),
			array(
				'title'    => __( 'Datepicker/Weekpicker: Max Date', 'e-commerce-jetpack' ),
				'desc'     => __( 'days', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_datepicker_maxdate_' . $i,
				'type'     => 'number',
				'default'  => 365,
			),
			array(
				'title'    => __( 'Datepicker/Weekpicker: Add Year Selector', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_datepicker_changeyear_' . $i,
				'type'     => 'checkbox',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Datepicker/Weekpicker: Year Selector: Year Range', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'The range of years displayed in the year drop-down: either relative to today\'s year ("-nn:+nn"), relative to the currently selected year ("c-nn:c+nn"), absolute ("nnnn:nnnn"), or combinations of these formats ("nnnn:-nn"). Note that this option only affects what appears in the drop-down, to restrict which dates may be selected use the minDate and/or maxDate options.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_datepicker_yearrange_' . $i,
				'type'     => 'text',
				'default'  => 'c-10:c+10',
			),
			array(
				'title'    => __( 'Datepicker/Weekpicker: First Week Day', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_datepicker_firstday_' . $i,
				'type'     => 'select',
				'default'  => 0,
				'options'  => array(
					__( 'Sunday', 'e-commerce-jetpack' ),
					__( 'Monday', 'e-commerce-jetpack' ),
					__( 'Tuesday', 'e-commerce-jetpack' ),
					__( 'Wednesday', 'e-commerce-jetpack' ),
					__( 'Thursday', 'e-commerce-jetpack' ),
					__( 'Friday', 'e-commerce-jetpack' ),
					__( 'Saturday', 'e-commerce-jetpack' ),
				),
			),

			//Block Dates
			array(
				'title'             => __( 'Datepicker/Weekpicker: Block Dates Format', 'e-commerce-jetpack' ),
				'desc'              => apply_filters( 'booster_message', '', 'desc' ),
				'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
				'desc_tip'          => __( 'Date format used to block dates.', 'e-commerce-jetpack' ) . '<br />' . __( 'Use yy-mm-dd if you want to include the year.', 'e-commerce-jetpack' ),
				'id'                => 'wcj_checkout_custom_field_datepicker_blockeddates_format_' . $i,
				'type'              => 'text',
				'default'           => 'mm-dd',
			),
			array(
				'title'             => __( 'Datepicker/Weekpicker: Block Dates', 'e-commerce-jetpack' ),
				'desc'              => apply_filters( 'booster_message', '', 'desc' ),
				'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
				'desc_tip'          => __( 'Use one date per line.', 'e-commerce-jetpack' ) . '<br />' . __( 'E.g 12-25 for Christmas, if block dates format is mm-dd', 'e-commerce-jetpack' ),
				'id'                => 'wcj_checkout_custom_field_datepicker_blockeddates_' . $i,
				'type'              => 'textarea',
				'default'           => '',
			),

			// Timepicker
			array(
				'title'    => __( 'Timepicker: Time Format', 'e-commerce-jetpack' ),
				'desc'     => __( 'Visit <a href="http://timepicker.co/options/" target="_blank">timepicker options page</a> for valid time formats', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_timepicker_format_' . $i,
				'type'     => 'text',
				'default'  => 'hh:mm p',
			),
			array(
				'title'    => __( 'Timepicker: Interval', 'e-commerce-jetpack' ),
				'desc'     => __( 'minutes', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_timepicker_interval_' . $i,
				'type'     => 'number',
				'default'  => 15,
			),
			array(
				'title'    => __( 'Exclude Categories', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'Hide this field if there is a product of selected category in cart.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_categories_ex_' . $i,
				'default'  => '',
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $product_cats,
			),
			array(
				'title'    => __( 'Include Categories', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'Show this field only if there is a product of selected category in cart.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_categories_in_' . $i,
				'default'  => '',
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $product_cats,
			),
			array(
				'title'    => __( 'Exclude Products', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'Hide this field if there is a selected product in cart.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_products_ex_' . $i,
				'default'  => '',
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $products,
			),
			array(
				'title'    => __( 'Include Products', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'Show this field only if there is a selected product in cart.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_products_in_' . $i,
				'default'  => '',
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $products,
			),
			array(
				'title'    => __( 'Min Cart Amount', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'Show this field only if cart total is at least this amount. Set zero to disable.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_min_cart_amount_' . $i,
				'default'  => 0,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0, 'step' => wcj_get_wc_price_step() ),
			),
			array(
				'title'    => __( 'Max Cart Amount', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'Show this field only if cart total is not more than this amount. Set zero to disable.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_field_max_cart_amount_' . $i,
				'default'  => 0,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0, 'step' => wcj_get_wc_price_step() ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'wcj_checkout_custom_fields_options_' . $i,
			),
		)
	);
}
return $settings;
