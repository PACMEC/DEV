<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elements PACMEC maintenance.
 *
 * Elements PACMEC maintenance handler class is responsible for setting up Elements PACMEC
 * activation and uninstallation hooks.
 *
 * @since 1.0.0
 */
class Maintenance {

	/**
	 * Activate Elements PACMEC.
	 *
	 * Set Elements PACMEC activation hook.
	 *
	 * Fired by `register_activation_hook` when the plugin is activated.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function activation( $network_wide ) {
		wp_clear_scheduled_hook( 'elementor/tracker/send_event' );

		wp_schedule_event( time(), 'daily', 'elementor/tracker/send_event' );
		flush_rewrite_rules();

		if ( is_multisite() && $network_wide ) {
			return;
		}

		set_transient( 'elementor_activation_redirect', true, MINUTE_IN_SECONDS );
	}

	/**
	 * Uninstall Elements PACMEC.
	 *
	 * Set Elements PACMEC uninstallation hook.
	 *
	 * Fired by `register_uninstall_hook` when the plugin is uninstalled.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function uninstall() {
		wp_clear_scheduled_hook( 'elementor/tracker/send_event' );
	}

	/**
	 * Init.
	 *
	 * Initialize Elements PACMEC Maintenance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		register_activation_hook( ELEMENTOR_PLUGIN_BASE, [ __CLASS__, 'activation' ] );
		register_uninstall_hook( ELEMENTOR_PLUGIN_BASE, [ __CLASS__, 'uninstall' ] );
	}
}
