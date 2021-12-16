<?php
/**
 * Booster for WooCommerce - Settings - Custom Shipping
 *
 * @version 2.9.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$wocommerce_shipping_settings_url = admin_url( 'admin.php?page=wc-settings&tab=shipping' );
$wocommerce_shipping_settings_url = '<a href="' . $wocommerce_shipping_settings_url . '">' . __( 'WooCommerce > Settings > Shipping', 'e-commerce-jetpack' ) . '</a>';
$settings = array(
	array(
		'title'    => __( 'Custom Shipping', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_shipping_custom_shipping_w_zones_options',
		'desc'     => __( 'This section lets you add custom shipping method.', 'e-commerce-jetpack' )
			. ' ' . sprintf( __( 'Visit %s to set method\'s options.', 'e-commerce-jetpack' ), $wocommerce_shipping_settings_url ),
	),
	array(
		'title'    => __( 'Custom Shipping', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_custom_shipping_w_zones_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Admin Title', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_custom_shipping_w_zones_admin_title',
		'default'  => __( 'Booster: Custom Shipping', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'css'      => 'width:300px;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_shipping_custom_shipping_w_zones_options',
	),
);
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Custom Shipping (Legacy - without Shipping Zones)', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_shipping_custom_shipping_options',
		'desc'     => __( 'This section lets you set number of custom shipping methods to add.', 'e-commerce-jetpack' )
			. ' ' . sprintf( __( 'After setting the number, visit %s to set each method options.', 'e-commerce-jetpack' ), $wocommerce_shipping_settings_url ),
	),
	array(
		'title'    => __( 'Custom Shipping Methods Number', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Save module\'s settings after changing this option to see new settings fields.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_shipping_custom_shipping_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'custom_attributes' => array( 'step' => '1', 'min' => '0' ),
	),
) );
for ( $i = 1; $i <= wcj_get_option( 'wcj_shipping_custom_shipping_total_number', 1 ); $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Admin Title Custom Shipping', 'e-commerce-jetpack' ) . ' #' . $i,
			'id'       => 'wcj_shipping_custom_shipping_admin_title_' . $i,
			'default'  => __( 'Custom', 'e-commerce-jetpack' ) . ' #' . $i,
			'type'     => 'text',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_shipping_custom_shipping_options',
	),
) );
return $settings;
