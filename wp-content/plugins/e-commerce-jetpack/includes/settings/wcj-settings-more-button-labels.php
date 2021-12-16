<?php
/**
 * Booster for WooCommerce Settings - More Button Labels
 *
 * @version 3.3.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Place order (Order now) Button', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_place_order_button_options',
	),
	array(
		'title'    => __( 'Text', 'e-commerce-jetpack' ),
		'desc'     => __( 'Leave blank for WooCommerce default.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Button on the checkout page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_place_order_button_text',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Override Default Text', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Enable this if button text is not changing for some payment gateway (e.g. PayPal).', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_place_order_button_override',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_place_order_button_options',
	),
);
