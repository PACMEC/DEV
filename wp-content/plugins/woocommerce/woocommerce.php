<?php
/**
 * Plugin Name: E-Commerce + Woo Addons
 * Description: Un complemento de compatibilidad para que algunos complementos de WooCommerce funcionen con el E-Commerce.
 * Author: PACMEC
 * Version: 9999.1
 * Author URI: #
 */
defined( 'ABSPATH' ) || exit;
// Load the Update Client to manage updates for the CC Compatibility for Woo Addons plugin
include_once dirname( __FILE__ ) . '/includes/UpdateClient.class.php';
define( 'CCWOOADDONSCOMPAT_VERSION', '9999.1' );  // Make sure the version number (and in the headers) is higher then current Woo version
define( 'CCWOOADDONSCOMPAT__FILE__', __FILE__ );
define( 'CCWOOADDONSCOMPAT_PATH', plugin_dir_path( CCWOOADDONSCOMPAT__FILE__ ) );
if( !defined( 'CCWOOADDONSCOMPAT_PLUGIN_BASE' ) ) define( 'CCWOOADDONSCOMPAT_PLUGIN_BASE', plugin_basename( CCWOOADDONSCOMPAT__FILE__ ) );
function ccwooaddonscompat_hide_view_details( $plugin_meta, $plugin_file, $plugin_data, $status ) {
	if( CCWOOADDONSCOMPAT_PLUGIN_BASE == $plugin_file ) unset( $plugin_meta[2] );
	return $plugin_meta;
}
add_filter( 'plugin_row_meta', 'ccwooaddonscompat_hide_view_details', 10, 4 );
