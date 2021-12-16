<?php
/**
 * Booster for WooCommerce - Product Input Fields - Options
 *
 * @version 3.4.0
 * @since   3.1.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

return array(
	array(
		'id'                => 'wcj_product_input_fields_enabled_' . $this->scope . '_',
		'title'             => __( 'Enabled', 'e-commerce-jetpack' ),
		'type'              => 'checkbox',
		'default'           => 'no',
	),
	array(
		'id'                => 'wcj_product_input_fields_order_' . $this->scope . '_',
		'short_title'       => __( 'Order', 'e-commerce-jetpack' ),
		'title'             => __( 'Set to zero for default order.', 'e-commerce-jetpack' ),
		'type'              => 'number',
		'default'           => 0,
	),
	array(
		'id'                => 'wcj_product_input_fields_type_' . $this->scope . '_',
		'title'             => __( 'Type', 'e-commerce-jetpack' ),
		'type'              => 'select',
		'default'           => 'text',
		'options'           => array(
			'text'       => __( 'Text', 'e-commerce-jetpack' ),
			'textarea'   => __( 'Textarea', 'e-commerce-jetpack' ),
			'number'     => __( 'Number', 'e-commerce-jetpack' ),
			'checkbox'   => __( 'Checkbox', 'e-commerce-jetpack' ),
			'file'       => __( 'File', 'e-commerce-jetpack' ),
			'datepicker' => __( 'Datepicker', 'e-commerce-jetpack' ),
			'weekpicker' => __( 'Weekpicker', 'e-commerce-jetpack' ),
			'timepicker' => __( 'Timepicker', 'e-commerce-jetpack' ),
			'select'     => __( 'Select', 'e-commerce-jetpack' ),
			'radio'      => __( 'Radio', 'e-commerce-jetpack' ),
			'password'   => __( 'Password', 'e-commerce-jetpack' ),
			'country'    => __( 'Country', 'e-commerce-jetpack' ),
//			'state'      => __( 'State', 'e-commerce-jetpack' ),
			'email'      => __( 'Email', 'e-commerce-jetpack' ),
			'tel'        => __( 'Phone', 'e-commerce-jetpack' ),
		),
	),
	array(
		'id'                => 'wcj_product_input_fields_title_' . $this->scope . '_',
		'title'             => __( 'Title', 'e-commerce-jetpack' ),
		'type'              => 'textarea',
		'default'           => '',
	),
	array(
		'id'                => 'wcj_product_input_fields_placeholder_' . $this->scope . '_',
		'title'             => __( 'Placeholder', 'e-commerce-jetpack' ),
		'type'              => 'textarea',
		'default'           => '',
	),
	array(
		'id'                => 'wcj_product_input_fields_required_' . $this->scope . '_',
		'title'             => __( 'Required', 'e-commerce-jetpack' ),
		'type'              => 'checkbox',
		'default'           => 'no',
	),
	array(
		'id'                => 'wcj_product_input_fields_required_message_' . $this->scope . '_',
		'title'             => __( 'Message on required', 'e-commerce-jetpack' ),
		'type'              => 'textarea',
		'default'           => '',
	),
	array(
		'id'                => 'wcj_product_input_fields_class_' . $this->scope . '_',
		'title'             => __( 'HTML Class', 'e-commerce-jetpack' ),
		'type'              => 'text',
		'default'           => '',
	),
	/* array(
		'id'                => 'wcj_product_input_fields_type_checkbox_' . $this->scope . '_',
		'title'             => __( 'If checkbox is selected, set possible pairs here.', 'e-commerce-jetpack' ),
		'type'              => 'select',
		'default'           => 'yes_no',
		'options'           => array(
			'yes_no' => __( 'Yes / No', 'e-commerce-jetpack' ),
			'on_off' => __( 'On / Off', 'e-commerce-jetpack' ),
		),
	), */
	array(
		'id'                => 'wcj_product_input_fields_type_checkbox_yes_' . $this->scope . '_',
		'title'             => __( 'If checkbox is selected, set value for ON here', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Checkbox: ON', 'e-commerce-jetpack' ),
		'type'              => 'text',
		'default'           => __( 'Yes', 'e-commerce-jetpack' ),
	),
	array(
		'id'                => 'wcj_product_input_fields_type_checkbox_no_' . $this->scope . '_',
		'title'             => __( 'If checkbox is selected, set value for OFF here', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Checkbox: OFF', 'e-commerce-jetpack' ),
		'type'              => 'text',
		'default'           => __( 'No', 'e-commerce-jetpack' ),
	),
	array(
		'id'                => 'wcj_product_input_fields_type_checkbox_default_' . $this->scope . '_',
		'title'             => __( 'If checkbox is selected, set default value here', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Checkbox: Default', 'e-commerce-jetpack' ),
		'type'              => 'select',
		'default'           => 'no',
		'options'           => array(
			'no'  => __( 'Not Checked', 'e-commerce-jetpack' ),
			'yes' => __( 'Checked', 'e-commerce-jetpack' ),
		),
	),
	array(
		'id'                => 'wcj_product_input_fields_type_file_accept_' . $this->scope . '_',
		'title'             => __( 'If file is selected, set accepted file types here. E.g.: ".jpg,.jpeg,.png". Leave blank to accept all files', 'e-commerce-jetpack' )
			. '. ' . __( 'Visit <a href="https://www.w3schools.com/tags/att_input_accept.asp" target="_blank">documentation on input accept attribute</a> for valid option formats', 'e-commerce-jetpack' ),
		'short_title'       => __( 'File: Accepted types', 'e-commerce-jetpack' ),
		'type'              => 'text',
		'default'           => __( '.jpg,.jpeg,.png', 'e-commerce-jetpack' ),
	),
	array(
		'id'                => 'wcj_product_input_fields_type_file_max_size_' . $this->scope . '_',
		'title'             => __( 'If file is selected, set max file size here. Set to zero to accept all files', 'e-commerce-jetpack' ),
		'short_title'       => __( 'File: Max size', 'e-commerce-jetpack' ),
		'type'              => 'number',
		'default'           => 0,
	),
	array(
		'id'                => 'wcj_product_input_fields_type_datepicker_format_' . $this->scope . '_',
		'title'             => __( 'If datepicker/weekpicker is selected, set date format here. Visit <a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">documentation on date and time formatting</a> for valid date formats', 'e-commerce-jetpack' ),
		'desc_tip'          => __( 'Leave blank to use your current WordPress format', 'e-commerce-jetpack' ) . ': ' . wcj_get_option( 'date_format' ),
		'short_title'       => __( 'Datepicker/Weekpicker: Date format', 'e-commerce-jetpack' ),
		'type'              => 'text',
		'default'           => '',
	),
	array(
		'id'                => 'wcj_product_input_fields_type_datepicker_mindate_' . $this->scope . '_',
		'title'             => __( 'If datepicker/weekpicker is selected, set min date (in days) here', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Datepicker/Weekpicker: Min date', 'e-commerce-jetpack' ),
		'type'              => 'number',
		'default'           => -365,
	),
	array(
		'id'                => 'wcj_product_input_fields_type_datepicker_maxdate_' . $this->scope . '_',
		'title'             => __( 'If datepicker/weekpicker is selected, set max date (in days) here', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Datepicker/Weekpicker: Max date', 'e-commerce-jetpack' ),
		'type'              => 'number',
		'default'           => 365,
	),
	array(
		'id'                => 'wcj_product_input_fields_type_datepicker_changeyear_' . $this->scope . '_',
		'title'             => __( 'If datepicker/weekpicker is selected, set if you want to add year selector', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Datepicker/Weekpicker: Change year', 'e-commerce-jetpack' ),
		'type'              => 'checkbox',
		'default'           => 'no',
	),
	array(
		'id'                => 'wcj_product_input_fields_type_datepicker_yearrange_' . $this->scope . '_',
		'title'             => __( 'If datepicker/weekpicker is selected, and year selector is enabled, set year range here', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Datepicker/Weekpicker: Year range', 'e-commerce-jetpack' ),
//		'desc_tip'          => __( 'The range of years displayed in the year drop-down: either relative to today\'s year ("-nn:+nn"), relative to the currently selected year ("c-nn:c+nn"), absolute ("nnnn:nnnn"), or combinations of these formats ("nnnn:-nn"). Note that this option only affects what appears in the drop-down, to restrict which dates may be selected use the minDate and/or maxDate options.', 'e-commerce-jetpack' ),
		'type'              => 'text',
		'default'           => 'c-10:c+10',
	),
	array(
		'id'                => 'wcj_product_input_fields_type_datepicker_firstday_' . $this->scope . '_',
		'title'             => __( 'If datepicker/weekpicker is selected, set first week day here', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Datepicker/Weekpicker: First week day', 'e-commerce-jetpack' ),
		'type'              => 'select',
		'default'           => 0,
		'options'           => array(
			__( 'Sunday', 'e-commerce-jetpack' ),
			__( 'Monday', 'e-commerce-jetpack' ),
			__( 'Tuesday', 'e-commerce-jetpack' ),
			__( 'Wednesday', 'e-commerce-jetpack' ),
			__( 'Thursday', 'e-commerce-jetpack' ),
			__( 'Friday', 'e-commerce-jetpack' ),
			__( 'Saturday', 'e-commerce-jetpack' ),
		),
	),
	array(
		'id'                => 'wcj_product_input_fields_type_timepicker_format_' . $this->scope . '_',
		'title'             => __( 'If timepicker is selected, set time format here. Visit <a href="http://timepicker.co/options/" target="_blank">timepicker options page</a> for valid time formats', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Timepicker: Time format', 'e-commerce-jetpack' ),
		'type'              => 'text',
		'default'           => 'hh:mm p',
	),
	array(
		'id'                => 'wcj_product_input_fields_type_timepicker_mintime_' . $this->scope . '_',
		'title'             => __( 'If timepicker is selected, set min time here. Visit <a href="http://timepicker.co/options/" target="_blank">timepicker options page</a> for valid option formats', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Timepicker: Min Time', 'e-commerce-jetpack' ),
		'type'              => 'text',
		'default'           => '',
	),
	array(
		'id'                => 'wcj_product_input_fields_type_timepicker_maxtime_' . $this->scope . '_',
		'title'             => __( 'If timepicker is selected, set max time here. Visit <a href="http://timepicker.co/options/" target="_blank">timepicker options page</a> for valid option formats', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Timepicker: Max Time', 'e-commerce-jetpack' ),
		'type'              => 'text',
		'default'           => '',
	),
	array(
		'id'                => 'wcj_product_input_fields_type_timepicker_interval_' . $this->scope . '_',
		'title'             => __( 'If timepicker is selected, set interval (in minutes) here', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Timepicker: Interval', 'e-commerce-jetpack' ),
		'type'              => 'number',
		'default'           => 15,
	),
	array(
		'id'                => 'wcj_product_input_fields_type_select_options_' . $this->scope . '_',
		'title'             => __( 'If select/radio is selected, set options here. One option per line', 'e-commerce-jetpack' ),
		'short_title'       => __( 'Select/Radio: Options', 'e-commerce-jetpack' ),
		'type'              => 'textarea',
		'default'           => '',
		'css'               => 'height:200px;',
	),
);
