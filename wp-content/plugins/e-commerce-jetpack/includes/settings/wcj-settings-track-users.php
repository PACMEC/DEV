<?php
/**
 * Booster for WooCommerce - Settings - User Tracking
 *
 * @version 3.6.0
 * @since   3.1.3
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_track_users_general_options',
	),
	array(
		'title'    => __( 'Countries by Visits', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable admin dashboard widget', 'e-commerce-jetpack' ),
		'id'       => 'wcj_track_users_by_country_widget_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Info', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Select which info to show in admin dashboard widget.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_track_users_by_country_widget_scopes',
		'default'  => array( '1', '28' ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => $this->track_users_scopes,
	),
	array(
		'desc'     => __( 'Top Countries', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Select how many top countries to show.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_track_users_by_country_widget_top_count',
		'default'  => 10,
		'type'     => 'number',
		'custom_attributes' => ( array( 'min' => 0 ) ),
	),
	array(
		'title'    => __( 'Track Orders', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Save customer\'s acquisition source (i.e. HTTP referer) for orders.', 'e-commerce-jetpack' ) . ' ' .
			__( 'This will add "Booster: Acquisition Source" meta box to each order\'s edit page.', 'e-commerce-jetpack' ) . '<br>' .
			apply_filters( 'booster_message', '', 'desc' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_track_users_save_order_http_referer_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'desc'     => __( 'Order List Columns: Referer', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'This will add "Referer" column to the <a href="%s">orders list</a>.', 'e-commerce-jetpack' ),
			admin_url( 'edit.php?post_type=shop_order' ) ) . '<br>' .
			apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_track_users_shop_order_columns_referer',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'desc'     => __( 'Order List Columns: Referer Type', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'This will add "Referer Type" column to the <a href="%s">orders list</a>.', 'e-commerce-jetpack' ),
			admin_url( 'edit.php?post_type=shop_order' ) ) . '<br>' .
			apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_track_users_shop_order_columns_referer_type',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_track_users_general_options',
	),
);
