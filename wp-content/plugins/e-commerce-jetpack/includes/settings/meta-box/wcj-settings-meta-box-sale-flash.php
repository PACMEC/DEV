<?php
/**
 * Booster for WooCommerce - Settings Meta Box - Sale Flash
 *
 * @version 3.2.4
 * @since   3.2.4
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'   => __( 'Enable', 'e-commerce-jetpack' ),
		'name'    => 'wcj_sale_flash_enabled',
		'default' => 'no',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Yes', 'e-commerce-jetpack' ),
			'no'  => __( 'No', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'   => __( 'HTML', 'e-commerce-jetpack' ),
		'name'    => 'wcj_sale_flash',
		'default' => '<span class="onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>',
		'type'    => 'textarea',
		'css'     => 'width:100%;min-height:100px;',
	),
);
