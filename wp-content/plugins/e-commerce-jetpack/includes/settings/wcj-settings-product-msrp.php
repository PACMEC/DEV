<?php
/**
 * Booster for WooCommerce - Settings - Product MSRP
 *
 * @version 5.1.0
 * @since   3.6.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$sections = array(
	'single'   => __( 'Single Product Page', 'e-commerce-jetpack' ),
	'archives' => __( 'Archives', 'e-commerce-jetpack' ),
);
$settings = array();
foreach ( $sections as $section_id => $section_title ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => sprintf( __( '%s Display Options', 'e-commerce-jetpack' ), $section_title ),
			'type'     => 'title',
			'id'       => 'wcj_product_msrp_display_on_' . $section_id . '_options',
		),
		array(
			'title'    => __( 'Display', 'e-commerce-jetpack' ),
			'type'     => 'select',
			'id'       => 'wcj_product_msrp_display_on_' . $section_id,
			'default'  => 'show',
			'options'  => array(
				'hide'           => __( 'Do not show', 'e-commerce-jetpack' ),
				'show'           => __( 'Show', 'e-commerce-jetpack' ),
				'show_if_higher' => __( 'Only show if MSRP is higher than the standard price', 'e-commerce-jetpack' ),
				'show_if_diff'   => __( 'Only show if MSRP differs from the standard price', 'e-commerce-jetpack' ),
			),
		),
		array(
			'title'    => __( 'Position', 'e-commerce-jetpack' ),
			'type'     => 'select',
			'id'       => 'wcj_product_msrp_display_on_' . $section_id . '_position',
			'default'  => 'after_price',
			'options'  => array(
				'before_price' => __( 'Before the standard price', 'e-commerce-jetpack' ),
				'after_price'  => __( 'After the standard price', 'e-commerce-jetpack' ),
			),
		),
		array(
			'title'    => __( 'Savings', 'e-commerce-jetpack' ),
			'desc'     => sprintf( __( 'Savings amount. To display this, use %s in "Final Template"', 'e-commerce-jetpack' ), '<code>' . '%you_save%' . '</code>' ) . ' ' .
				wcj_message_replaced_values( array( '%you_save_raw%' ) ),
			'type'     => 'custom_textarea',
			'id'       => 'wcj_product_msrp_display_on_' . $section_id . '_you_save',
			'default'  => ' (%you_save_raw%)',
		),
		array(
			'desc'     => sprintf( __( 'Savings amount in percent. To display this, use %s in "Final Template"', 'e-commerce-jetpack' ), '<code>' . '%you_save_percent%' . '</code>' ) . ' ' .
				wcj_message_replaced_values( array( '%you_save_percent_raw%' ) ),
			'type'     => 'custom_textarea',
			'id'       => 'wcj_product_msrp_display_on_' . $section_id . '_you_save_percent',
			'default'  => ' (%you_save_percent_raw% %)',
		),
		array(
			'desc'     => __( 'Savings amount in percent rounding precision', 'e-commerce-jetpack' ),
			'type'     => 'number',
			'id'       => 'wcj_product_msrp_display_on_' . $section_id . '_you_save_percent_round',
			'default'  => 0,
			'custom_attributes' => array( 'min' => 0 ),
		),
		array(
			'title'    => __( 'Final Template', 'e-commerce-jetpack' ),
			'desc_tip' => apply_filters( 'booster_message', '', 'desc_no_link' ),
			'desc'     => wcj_message_replaced_values( array( '%msrp%', '%you_save%', '%you_save_percent%' ) ),
			'type'     => 'custom_textarea',
			'id'       => 'wcj_product_msrp_display_on_' . $section_id . '_template',
			'default'  => '<div class="price"><label for="wcj_product_msrp">MSRP</label>: <span id="wcj_product_msrp"><del>%msrp%</del>%you_save%</span></div>',
			'css'      => 'width:100%;',
			'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'wcj_product_msrp_display_' . $section_id . '_options',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Admin Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_msrp_admin_options',
	),
	array(
		'title'    => __( 'Admin MSRP Input Display', 'e-commerce-jetpack' ),
		'type'     => 'select',
		'id'       => 'wcj_product_msrp_admin_view',
		'default'  => 'inline',
		'options'  => array(
			'inline'   => __( 'Inline', 'e-commerce-jetpack' ),
			'meta_box' => __( 'As separate meta box', 'e-commerce-jetpack' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_msrp_admin_options',
	),
	array(
		'title'    => __( 'Compatibility', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_payment_msrp_comp',
	),
	array(
		'title'    => __( 'Multicurrency', 'e-commerce-jetpack'),
		'desc'     => __( 'Enable compatibility with Multicurrency module', 'e-commerce-jetpack'),
		'id'       => 'wcj_payment_msrp_comp_mc',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_payment_msrp_comp',
	),
	array(
		'title'    => __( 'Other Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_msrp_other_options',
	),
	array(
		'title'    => __( 'Treat Variable Products as Simple Products', 'e-commerce-jetpack'),
		'desc'     => __( 'Enable', 'e-commerce-jetpack'),
		'id'       => 'wcj_product_msrp_variable_as_simple_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'             => __( 'Archive Field', 'e-commerce-jetpack' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc_tip'          => __( 'Adds a MSRP field that will be displayed on the product archive.', 'e-commerce-jetpack' ),
		'id'                => 'wcj_product_msrp_archive_page_field',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'title'             => __( 'Archive Detection Method', 'e-commerce-jetpack' ),
		'desc_tip'          => __( 'Template strings used to detect the loop.', 'e-commerce-jetpack' ).'<br />'.__( 'Use 1 string per line.', 'e-commerce-jetpack' ),
		'id'                => 'wcj_product_msrp_archive_detection_method',
		'default'           => 'loop',
		'type'              => 'textarea',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_msrp_other_options',
	),
	array(
		'title'    => __( 'Template Variable Formulas', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_msrp_template_variables_formulas',
	),
	array(
		'title'             => __( 'You Save', 'e-commerce-jetpack' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Variable: ', 'e-commerce-jetpack' ) . '<code>%you_save%</code><br />' . wcj_message_replaced_values( array( '%msrp%', '%product_price%' ) ) : $message,
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc_tip'          => __( '%you_save%', 'e-commerce-jetpack' ),
		'id'                => 'wcj_product_msrp_formula_you_save',
		'default'           => '%msrp% - %product_price%',
		'type'              => 'text',
	),
	array(
		'title'             => __( 'You Save Percent', 'e-commerce-jetpack' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Variable: ', 'e-commerce-jetpack' ) . '<code>%you_save_percent%</code><br />' . wcj_message_replaced_values( array( '%msrp%', '%product_price%' ) ) : $message,
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc_tip'          => __( '%you_save_percent%', 'e-commerce-jetpack' ),
		'id'                => 'wcj_product_msrp_formula_you_save_percent',
		'default'           => '(%msrp% - %product_price%) / %msrp% * 100',
		'type'              => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_msrp_template_variables_formulas',
	),
) );
return $settings;
