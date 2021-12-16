<?php
/*
Plugin Name: E-Commerce - EAN
Plugin URI: #
Description: Gestione el EAN del producto en su tienda.
Version: 1.0.0
Author: PACMEC
Author URI: #
Text Domain: e-commerce-ean
Domain Path: /langs
*/

defined( 'ABSPATH' ) || exit;

if ( 'e-commerce-ean' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	$plugin = 'e-commerce-ean/e-commerce-ean.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		return;
	}
}

defined( 'ALG_WC_EAN_VERSION' ) || define ( 'ALG_WC_EAN_VERSION', '2.7.0' );

defined( 'ALG_WC_EAN_FILE' ) || define ( 'ALG_WC_EAN_FILE', __FILE__ );

require_once( 'includes/class-alg-wc-ean.php' );

if ( ! function_exists( 'alg_wc_ean' ) ) {
	/**
	 * Returns the main instance of Alg_WC_EAN to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_wc_ean() {
		return Alg_WC_EAN::instance();
	}
}

add_action( 'plugins_loaded', 'alg_wc_ean' );
