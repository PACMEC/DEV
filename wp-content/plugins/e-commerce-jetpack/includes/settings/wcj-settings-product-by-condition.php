<?php
/**
 * Booster for WooCommerce - Settings - Product Visibility by Condition
 *
 * @version 5.4.0
 * @since   3.6.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Visibility Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_' . $this->id . '_options',
	),
	array(
		'title'    => __( 'Hide Visibility', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will hide selected products in shop and search results. However product still will be accessible via direct link.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_' . $this->id . '_visibility',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Make Non-Purchasable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will make selected products non-purchasable (i.e. product can\'t be added to the cart).', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_' . $this->id . '_purchasable',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Modify Query', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will hide selected products completely (including direct link).', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_' . $this->id . '_query',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc_tip' => __( 'Enable this if you are still seeing hidden products in "Products" widgets.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_' . $this->id . '_query_widgets',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_' . $this->id . '_options',
	),
);
$settings = array_merge( $settings, $this->maybe_add_extra_settings() );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Admin Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_' . $this->id . '_admin_options',
	),
	array(
		'title'    => __( 'Visibility Method', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This option sets how do you want to set product\'s visibility.', 'e-commerce-jetpack' ) . ' ' .
			__( 'Possible values: "Set visible", "Set invisible" or "Set both".', 'e-commerce-jetpack' ),
		'id'       => 'wcj_' . $this->id . '_visibility_method',
		'default'  => 'visible',
		'type'     => 'select',
		'options'  => array(
			'visible'   => __( 'Set visible', 'e-commerce-jetpack' ),
			'invisible' => __( 'Set invisible', 'e-commerce-jetpack' ),
			'both'      => __( 'Set both', 'e-commerce-jetpack' ),
		),
		'desc'     => __( 'Set Visible: Select values in which you want to visible product', 'e-commerce-jetpack' ) .
			'<br>'. __( 'Set Invisible: Select values in which you want an invisible product', 'e-commerce-jetpack' ) .
			'<br>'. __( 'Set Both: There will be 2 select box for each above option Visible & Invisible', 'e-commerce-jetpack' ) .
			'<br>' . apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Select Box Style', 'e-commerce-jetpack' ),
		'id'       => 'wcj_' . $this->id . '_select_style',
		'default'  => 'chosen_select',
		'type'     => 'select',
		'options'  => array(
			'chosen_select' => __( 'Chosen select', 'e-commerce-jetpack' ),
			'standard'      => __( 'Standard', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Quick Edit', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will add options to the "Quick Edit".', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_' . $this->id . '_admin_quick_edit',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Bulk Edit', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_' . $this->id . '_admin_bulk_edit',
		'default'  => 'no',
		'type'     => 'checkbox',
		'desc_tip' => __( 'This will add options to the "Bulk Actions > Edit".', 'e-commerce-jetpack' ) . '<br>' .
			apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Products List Column', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will add column to the admin products list.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'id'       => 'wcj_' . $this->id . '_admin_add_column',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_' . $this->id . '_admin_options',
	),
) );
return $settings;
