<?php
/**
 * Plugin Name: E-Commerce
 * Plugin URI: https://github.com/PACMEC-plugins/e-commerce
 * Description: Una plataforma de comercio electrónico simple, potente e independiente. Vende cualquier cosa con facilidad
 * Version: 1.0.0
 * Author: PACMEC
 * Author URI: https://github.com/PACMEC-plugins/e-commerce
 * Text Domain: e-commerce
 * Domain Path: /i18n/woocommerce/
 *
 * @package WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Shows an error message when WooCommerce is detected as currently active.
 *
 * WooCommerce and E-Commerce cannot both be active at once.
 *
 * @return void
 */
function cc_wc_already_active_notice() {
	echo '<div class="notice error is-dismissible">';
	echo '<p><strong>';
	echo esc_html__( 'You must deactivate WooCommerce before activating E-Commerce.', 'e-commerce' );
	echo '</strong></p>';
	echo '<p>';
	echo esc_html__( 'E-Commerce has not been activated.', 'e-commerce' );
	echo '</p>';
	echo '</div>';
}

/**
 * Shows an error message when E-Commerce is active and the user attempts
 * to activate WooCommerce.
 *
 * WooCommerce and E-Commerce cannot both be active at once.
 *
 * @return void
 */
function cc_wc_activate_attempted_notice() {
	echo '<div class="notice error is-dismissible">';
	echo '<p><strong>';
	echo esc_html__( 'You must deactivate E-Commerce before activating WooCommerce.', 'e-commerce' );
	echo '</strong></p>';
	echo '<p>';
	echo esc_html__( 'WooCommerce has not been activated.', 'e-commerce' );
	echo '</p>';
	echo '</div>';
}

$_cc_can_load = true;

// Check if WooCommerce is already active.  In this case we need to block
// E-Commerce from being activated to avoid fatal errors.
if (
	file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) &&
	// Make sure we are really looking at WooCommerce and not the compatibility plugin!
	file_exists( WP_PLUGIN_DIR . '/woocommerce/includes/class-woocommerce.php' ) &&
	is_plugin_active( 'woocommerce/woocommerce.php' )
) {

	// WooCommerce is already active. Show an admin notice.
	add_action( 'admin_notices', 'cc_wc_already_active_notice' );

	// Deactivate E-Commerce.
	deactivate_plugins( array( 'e-commerce/e-commerce.php' ) );

	// Avoid showing a "Plugin activated" message in the admin screen.
	// See also src/wp-admin/plugins.php in core.
	unset( $_GET['activate'] );

	// Do not proceed further with E-Commerce loading.
	$_cc_can_load = false;

} else if (
	// Check if this is a request that would activate WooCommerce.  Since
	// E-Commerce is already active then we also need to prevent
	// WooCommerce from being activated, again to avoid fatal errors.
	//
	// Plugin activation happens after plugins load and after `init` so we need
	// to check for the presence of the related request parameters here.
	//
	// See also src/wp-admin/plugins.php in core.
	is_admin() &&
	strpos( $_SERVER['REQUEST_URI'], '/plugins.php' ) !== false &&
	( isset( $_REQUEST['action'] ) || isset( $_REQUEST['action2'] ) ) &&
	// Make sure we are really looking at WooCommerce and not the compatibility plugin!
	file_exists( WP_PLUGIN_DIR . '/woocommerce/includes/class-woocommerce.php' )
) {
	$is_activate_woo_request = false;

	// Check if the user tried to activate WooCommerce by itself.
	if (
		isset( $_GET['action'] ) &&
		$_GET['action'] === 'activate' &&
		isset( $_GET['plugin'] ) &&
		$_GET['plugin'] === 'woocommerce/woocommerce.php'
	) {
		$is_activate_woo_request = true;
	}

	// Check if the user tried to activate WooCommerce using either of the two
	// "Bulk Actions" dropdown boxes.
	if (
		(
			( isset( $_POST['action'] ) && $_POST['action'] === 'activate-selected' ) ||
			( isset( $_POST['action2'] ) && $_POST['action2'] === 'activate-selected' )
		) &&
		isset( $_POST['checked'] ) &&
		in_array( 'woocommerce/woocommerce.php', (array) wp_unslash( $_POST['checked'] ) )
	) {
		$is_activate_woo_request = true;
	}

	if ( $is_activate_woo_request ) {
		// Show an admin notice.
		add_action( 'admin_notices', 'cc_wc_activate_attempted_notice' );

		// Block WooCommerce from being activated.
		unset( $_GET['action'] );
		unset( $_POST['action'] );
		unset( $_REQUEST['action'] );
		unset( $_POST['action2'] );
		unset( $_REQUEST['action2'] );

		// Proceed normally with the E-Commerce loading process below.
	}
}

if ( $_cc_can_load ) {

	////////////////////////////////////////////
	// BEGIN CLASSIC COMMERCE LOADING PROCESS //
	////////////////////////////////////////////

	// Load the Update Client to manage E-Commerce updates.
	include_once dirname( __FILE__ ) . '/includes/class-wc-update-client.php';

	// Define WC_PLUGIN_FILE.
	if ( ! defined( 'WC_PLUGIN_FILE' ) ) {
		define( 'WC_PLUGIN_FILE', __FILE__ );
	}

	// Include the main WooCommerce class.
	if ( ! class_exists( 'WooCommerce' ) ) {
		include_once dirname( __FILE__ ) . '/includes/class-woocommerce.php';
	}

	/**
	 * Main instance of WooCommerce.
	 *
	 * Returns the main instance of WC to prevent the need to use globals.
	 *
	 * @since  2.1
	 * @return WooCommerce
	 */
	function wc() {
		return WooCommerce::instance();
	}

	// Global for backwards compatibility.
	$GLOBALS['woocommerce'] = wc();

	//////////////////////////////////////////
	// END CLASSIC COMMERCE LOADING PROCESS //
	//////////////////////////////////////////

}

// Do not add any new code here!  All code required to load E-Commerce
// must go inside the "CLASSIC COMMERCE LOADING PROCESS" block above.
