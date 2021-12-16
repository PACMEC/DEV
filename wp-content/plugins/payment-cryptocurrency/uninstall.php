<?php
// Do some cleanup during plugin uninstall
// if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Must be required for the cw_get_option function to exist.
require_once plugin_dir_path( __FILE__ ) . '/cryptopay.php';

global $wpdb;

if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	// We are on a multisite - get all blogs
	$blogs = wp_get_sites();
	foreach ( $blogs as $blog ) {

		switch_to_blog( $blog['blog_id'] );

		// Maybe delete tables
		if ( '1' !== cw_get_option( 'keep_tables' ) ) {
			$tables = array(
				$wpdb->prefix . 'cryptowoo_exchange_rates',
				$wpdb->prefix . 'cryptowoo_payments_temp',
			);

			// Drop tables
			foreach ( $tables as $table ) {
				$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
			}
		}
		// Maybe clean up options table
		if ( '1' !== cw_get_option( 'keep_options' ) ) {
			// Get all options containing *cryptopay*
			$all_options = $wpdb->get_results( 'SELECT option_name FROM ' . $wpdb->prefix . "options WHERE option_name LIKE '%cryptopay%'" );

			// Delete options
			foreach ( $all_options as $option ) {
				delete_option( $option->option_name );
			}
		}
	}
} else {
	// Single site

	// Maybe delete tables
	if ( '1' !== cw_get_option( 'keep_tables' ) ) {
		$tables = array(
			$wpdb->prefix . 'cryptowoo_exchange_rates',
			$wpdb->prefix . 'cryptowoo_payments_temp',
		);

		// Drop tables
		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		}
	}
	// Maybe clean up options table
	if ( '1' !== cw_get_option( 'keep_options' ) ) {
		// Get all options containing *cryptopay*
		$all_options = $wpdb->get_results( 'SELECT option_name FROM ' . $wpdb->prefix . "options WHERE option_name LIKE '%cryptopay%'" );

		// Delete options
		foreach ( $all_options as $option ) {
			delete_option( $option->option_name );
		}
	}
}

// Remove integrity check file
if ( cw_get_option( 'cw_filename' ) ) {
	$path = trailingslashit( wp_upload_dir()['basedir'] ) . sanitize_file_name( cw_get_option( 'cw_filename' ) );
	if ( file_exists( $path ) ) {
		unlink( $path );
	}
}





