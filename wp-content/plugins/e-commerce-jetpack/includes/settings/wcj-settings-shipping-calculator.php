<?php
/**
 * Booster for WooCommerce - Settings - Shipping Calculator
 *
 * @version 4.6.1
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Shipping Calculator Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_shipping_calculator_options',
	),
	array(
		'title'    => __( 'Enable City', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_calculator_enable_city',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Enable Postcode', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_calculator_enable_postcode',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Enable State', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_calculator_enable_state',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Force Block Open', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_calculator_enable_force_block_open',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => '',
		'desc'     => __( 'Calculate Shipping button', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'When "Force Block Open" options is enabled, set Calculate Shipping button options.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_calculator_enable_force_block_open_button',
		'default'  => 'hide',
		'type'     => 'select',
		'options'  => array(
			'hide'    => __( 'Hide', 'e-commerce-jetpack' ),
			'noclick' => __( 'Make non clickable', 'e-commerce-jetpack' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_shipping_calculator_options',
	),
	array(
		'title'    => __( 'Labels Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_shipping_calculator_labels_options',
	),
	array(
		'title'    => __( 'Labels', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable Section', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_calculator_labels_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'desc_tip' => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Label for Calculate Shipping', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_calculator_label_calculate_shipping',
		'default'  => __( 'Calculate Shipping', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'desc_tip' => apply_filters( 'booster_message', '', 'desc_no_link' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Label for Update Totals', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_calculator_label_update_totals',
		'default'  => __( 'Update Totals', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'desc_tip' => apply_filters( 'booster_message', '', 'desc_no_link' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_shipping_calculator_labels_options',
	),
);
