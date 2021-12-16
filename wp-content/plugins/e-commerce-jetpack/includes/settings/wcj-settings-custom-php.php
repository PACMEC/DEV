<?php
/**
 * Booster for WooCommerce Settings - Custom PHP
 *
 * @version 4.0.0
 * @since   4.0.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$file_path = wcj_get_wcj_uploads_dir( 'custom_php', false ) . DIRECTORY_SEPARATOR . 'booster.php';

return array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_custom_php_options',
	),
	array(
		'title'    => __( 'Custom PHP', 'e-commerce-jetpack' ),
		'id'       => 'wcj_custom_php',
		'default'  => '',
		'type'     => 'textarea',
		'css'      => 'width:100%;height:500px;font-family:monospace;',
		'wcj_raw'  => true,
		'desc'     => sprintf( __( 'Without the %s tag.', 'e-commerce-jetpack' ), '<code>' . esc_html( '<?php' ) . '</code>' ) .
			( file_exists( $file_path ) ? '<br>' . sprintf(
				__( 'Automatically created file: %s.', 'e-commerce-jetpack' ), '<code>' . $file_path . '</code>' ) : '' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_custom_php_options',
	),
);
