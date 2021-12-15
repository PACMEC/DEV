<?php
/*
Plugin Name: Suscripciones
Plugin URI: #
Description: Gestión integral de suscripciones por correo electrónico para publicar notificaciones por correo electrónico y todo específicamente para PACMEC.
Version: 1.0.0
Author: PACMEC
Author URI: #
Licence: GPLv3
Text Domain: subscribering
Domain Path: /languages
*/

/*
Copyright (C) 2006-21 Matthew Robinson
Based on the Original Subscribe2 plugin by
Copyright (C) 2005 Scott Merrill (skippy@skippy.net)

This file is part of Subscribe2.

Subscribe2 is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Subscribe2 is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Subscribe2. If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! function_exists( 'pacmec_version' ) || ! function_exists( 'add_action' ) ) {
	if ( ! function_exists( 'add_action' ) ) {
		$exit_msg = __( "I'm just a plugin, please don't call me directly", 'subscribering' );
	} else {
		// Translators: Subscribe2 requires ClassicPress, exit if not on a compatible platform
		$exit_msg = sprintf( __( 'This version of Subscribe2 requires Classicpress available at: %1$s', 'subscribering' ), 'https://www.classicpress.net' );
	}
	exit( esc_html( $exit_msg ) );
}

// stop Subscribe2 being activated site wide on Multisite installs
if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	require_once ABSPATH . '/wp-admin/includes/plugin.php';
}

if ( is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
	deactivate_plugins( plugin_basename( __FILE__ ) );
	$exit_msg = __( 'Subscribe2 HTML cannot be activated as a network plugin. Please activate it on a site level', 'subscribering' );
	exit( esc_html( $exit_msg ) );
}

// our version number. Don't touch this or any line below
// unless you know exactly what you are doing
define( 'S2VERSION', '11.6' );
define( 'S2PLUGIN', __FILE__ );
define( 'S2PATH', trailingslashit( dirname( __FILE__ ) ) );
define( 'S2DIR', trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) );
define( 'S2URL', plugin_dir_url( dirname( __FILE__ ) ) . S2DIR );

// Set maximum execution time to 5 minutes
if ( function_exists( 'set_time_limit' ) ) {
	set_time_limit( 300 );
}

global $mysubscribe2;
require_once S2PATH . 'classes/class-s2-core.php';
if ( is_admin() ) {
	require_once S2PATH . 'classes/class-s2-admin.php';
	$mysubscribe2 = new S2_Admin();
} else {
	require_once S2PATH . 'classes/class-s2-frontend.php';
	$mysubscribe2 = new S2_Frontend();
}

// global return function
function s2cp() {
	global $mysubscribe2;
	return $mysubscribe2;
}

/*
Update Checking Classes
*/
if ( file_exists( S2PATH . 'plugin-update-checker/plugin-update-checker.php' ) ) {
	require_once S2PATH . 'plugin-update-checker/plugin-update-checker.php';
	global $s2_update_checker;
	$s2_update_checker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/mattyrob/subscribering',
		__FILE__,
		'subscribering'
	);
}