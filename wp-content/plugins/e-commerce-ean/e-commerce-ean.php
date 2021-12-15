<?php
/*
Plugin Name: EAN for WooCommerce
Plugin URI: https://wpfactory.com/item/ean-for-woocommerce/
Description: Manage product EAN in WooCommerce. Beautifully.
Version: 2.7.0
Author: Algoritmika Ltd
Author URI: https://algoritmika.com
Text Domain: ean-for-woocommerce
Domain Path: /langs
WC tested up to: 5.9
*/

defined( 'ABSPATH' ) || exit;

if ( 'ean-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	$plugin = 'ean-for-woocommerce-pro/ean-for-woocommerce-pro.php';
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
