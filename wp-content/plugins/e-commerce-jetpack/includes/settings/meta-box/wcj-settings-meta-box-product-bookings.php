<?php
/**
 * Booster for WooCommerce - Settings Meta Box - Bookings
 *
 * @version 2.8.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Enabled', 'e-commerce-jetpack' ),
		'name'     => 'wcj_product_bookings_enabled',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'yes' => __( 'Yes', 'e-commerce-jetpack' ),
			'no'  => __( 'No', 'e-commerce-jetpack' ),
		),
	),
);
