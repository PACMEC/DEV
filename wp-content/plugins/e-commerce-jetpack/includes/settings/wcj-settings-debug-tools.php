<?php
/**
 * Booster for WooCommerce - Settings - Debug Tools
 *
 * @version 4.1.0
 * @since   4.1.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Debug Tools Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_debug_tools_options',
	),
	array(
		'title'    => __( 'Log', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Enables logging to Booster log.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_logging_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'WooCommerce Log', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Enables logging to WooCommerce log.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_wc_logging_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Debug', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Enables debug mode.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_debuging_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'System Info', 'e-commerce-jetpack' ),
		'id'       => 'wcj_debug_tools_system_info',
		'default'  => '',
		'type'     => 'custom_link',
		'link'     => '<a href="' . add_query_arg( 'wcj_debug', true ) . '">' . __( 'Show extended info', 'e-commerce-jetpack' ) . '</a>' .
			'<pre style="background-color: white; padding: 5px;">' . wcj_get_table_html( $this->get_system_info_table_array(),
				array( 'columns_styles' => array( 'padding:0;', 'padding:0;' ), 'table_heading_type' => 'vertical' ) ) . '</pre>',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_debug_tools_options',
	),
);
