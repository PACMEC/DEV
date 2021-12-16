<?php
/**
 * Plugin Name: Creador de páginas - JSComposer
 * Plugin URI: #
 * Description: Constructor de páginas de arrastrar y soltar para PACMEC. Tome el control total de su sitio de PACMEC, cree cualquier diseño que pueda imaginar, sin necesidad de conocimientos de programación.
 * Version: 1.0.0
 * Author: PACMEC
 * Author URI: #
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Current WPBakery Page Builder version
 */
if ( ! defined( 'WPB_VC_VERSION' ) ) {
	/**
	 *
	 */
	define( 'WPB_VC_VERSION', '6.5.0' );
}

$dir = dirname( __FILE__ );
define( 'WPB_PLUGIN_DIR', $dir );
define( 'WPB_PLUGIN_FILE', __FILE__ );

require_once $dir . '/include/classes/core/class-vc-manager.php';
/**
 * Main WPBakery Page Builder manager.
 * @var Vc_Manager $vc_manager - instance of composer management.
 * @since 4.2
 */
global $vc_manager;
if ( ! $vc_manager ) {
	$vc_manager = Vc_Manager::getInstance();
	// Load components
	$vc_manager->loadComponents();
}
