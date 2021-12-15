<?php
/**
 * Include files for wp-admin
 *
 * @package CryptoWoo
 * @subpackage Admin
 */

// Load the embedded Redux Framework.
if ( file_exists( dirname( __FILE__ ) . '/redux-framework/framework.php' ) ) {
	include_once dirname( __FILE__ ) . '/redux-framework/framework.php';
}

// Load the theme/plugin options.
if ( file_exists( dirname( __FILE__ ) . '/options-init.php' ) ) {
	include_once dirname( __FILE__ ) . '/options-init.php';
}

// Load Redux extensions.
if ( file_exists( dirname( __FILE__ ) . '/redux-extensions/extensions-init.php' ) ) {
	include_once dirname( __FILE__ ) . '/redux-extensions/extensions-init.php';
}
