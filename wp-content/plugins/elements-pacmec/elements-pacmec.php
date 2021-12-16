<?php
/**
 * Plugin Name: Elements PACMEC
 * Description: El creador de páginas frontend de arrastrar y soltar más avanzado. Cree sitios web de alta gama con píxeles perfectos a velocidades récord. Cualquier tema, cualquier página, cualquier diseño.
 * Plugin URI: #
 * Author: PACMEC
 * Version: 99.2-upstream2.7.6
 * Author URI: #
 * Text Domain: elements-pacmec
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ELEMENTOR_VERSION', '99.2-upstream2.7.6' );
define( 'ELEMENTOR_PREVIOUS_STABLE_VERSION', '2.6.8' );

define( 'ELEMENTOR__FILE__', __FILE__ );
define( 'ELEMENTOR_PLUGIN_BASE', plugin_basename( ELEMENTOR__FILE__ ) );
define( 'ELEMENTOR_PATH', plugin_dir_path( ELEMENTOR__FILE__ ) );

if ( defined( 'ELEMENTOR_TESTS' ) && ELEMENTOR_TESTS ) {
	define( 'ELEMENTOR_URL', 'file://' . ELEMENTOR_PATH );
} else {
	define( 'ELEMENTOR_URL', plugins_url( '/', ELEMENTOR__FILE__ ) );
}

define( 'ELEMENTOR_MODULES_PATH', plugin_dir_path( ELEMENTOR__FILE__ ) . '/modules' );
define( 'ELEMENTOR_ASSETS_PATH', ELEMENTOR_PATH . 'assets/' );
define( 'ELEMENTOR_ASSETS_URL', ELEMENTOR_URL . 'assets/' );

add_action( 'plugins_loaded', 'elements_pacmec_load_plugin_textdomain' );

if ( ! version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	add_action( 'admin_notices', 'elements_pacmec_fail_php_version' );
} else {
	require ELEMENTOR_PATH . 'includes/plugin.php';
}

/**
 * Load Classic Elements textdomain.
 *
 * Load gettext translate for Classic Elements text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elements_pacmec_load_plugin_textdomain() {
	load_plugin_textdomain( 'elements-pacmec' );
}

/**
 * Classic Elements admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elements_pacmec_fail_php_version() {
	/* translators: %s: PHP version */
	$message = sprintf( esc_html__( 'Classic Elements requires PHP version %s+, plugin is currently NOT RUNNING.', 'elements-pacmec' ), '5.4' );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}
