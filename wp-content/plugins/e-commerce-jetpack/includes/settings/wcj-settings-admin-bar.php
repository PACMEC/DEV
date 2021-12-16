<?php
/**
 * Booster for WooCommerce - Settings - Admin Bar
 *
 * @version 4.1.0
 * @since   2.9.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_admin_bar_options',
	),
	array(
		'title'    => __( '"WooCommerce" Admin Bar', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_admin_bar_wc_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
		'checkboxgroup' => 'start',
	),
	array(
		'desc'     => __( 'List product categories in "WooCommerce > Products > Categories"', 'e-commerce-jetpack' ),
		'id'       => 'wcj_admin_bar_wc_list_cats',
		'default'  => 'no',
		'type'     => 'checkbox',
		'checkboxgroup' => '',
	),
	array(
		'desc'     => __( 'List product tags in "WooCommerce > Products > Tags"', 'e-commerce-jetpack' ),
		'id'       => 'wcj_admin_bar_wc_list_tags',
		'default'  => 'no',
		'type'     => 'checkbox',
		'checkboxgroup' => 'end',
	),
	array(
		'title'    => __( '"Booster" Admin Bar', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_admin_bar_booster_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( '"Booster: Active" Admin Bar', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_admin_bar_booster_active_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_admin_bar_options',
	),
);
