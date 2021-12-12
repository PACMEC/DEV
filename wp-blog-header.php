<?php
/**
 * Loads the PACMEC environment and template.
 *
 * @package PACMEC
 */

if ( !isset($wp_did_header) ) {

	$wp_did_header = true;

	// Load the PACMEC library.
	require_once( dirname(__FILE__) . '/wp-load.php' );

	// Set up the PACMEC query.
	wp();

	// Load the theme template.
	require_once( ABSPATH . WPINC . '/template-loader.php' );

}
