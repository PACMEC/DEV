<?php
/**
 * Booster for WooCommerce - Settings - Custom Gateways
 *
 * @version 3.3.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$wocommerce_checkout_settings_url = admin_url( 'admin.php?page=wc-settings&tab=checkout' );
$wocommerce_checkout_settings_url = '<a href="' . $wocommerce_checkout_settings_url . '">' . __( 'WooCommerce > Settings > Checkout', 'e-commerce-jetpack' ) . '</a>';
$settings = array(
	array(
		'title'    => __( 'Custom Payment Gateways Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_custom_payment_gateways_options',
		'desc'     => __( 'This section lets you set number of custom payment gateways to add.', 'e-commerce-jetpack' )
			. ' ' . sprintf( __( 'After setting the number, visit %s to set each gateway options.', 'e-commerce-jetpack' ), $wocommerce_checkout_settings_url ),
	),
	array(
		'title'    => __( 'Number of Gateways', 'e-commerce-jetpack' ),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'desc_tip' => __( 'Number of custom payments gateways to be added. All settings for each new gateway are in WooCommerce > Settings > Checkout.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_custom_payment_gateways_number',
		'default'  => 1,
		'type'     => 'number',
		'custom_attributes' => is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ?
			apply_filters( 'booster_message', '', 'readonly' ) : array( 'step' => '1', 'min' => '1' ),
	),
);
for ( $i = 1; $i <= apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_custom_payment_gateways_number', 1 ) ); $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Admin Title Custom Gateway', 'e-commerce-jetpack' ) . ' #' . $i,
			'id'       => 'wcj_custom_payment_gateways_admin_title_' . $i,
			'default'  => __( 'Custom Gateway', 'e-commerce-jetpack' ) . ' #' . $i,
			'type'     => 'text',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_custom_payment_gateways_options',
	),
	array(
		'title'    => __( 'Advanced Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_custom_payment_gateways_advanced_options',
	),
	array(
		'title'    => __( 'Gateways Input Fields', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add "Delete" button', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will add "Delete" button to custom payment gateways input fields admin order meta box.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_custom_payment_gateways_input_fields_delete_button',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_custom_payment_gateways_advanced_options',
	),
) );
return $settings;
