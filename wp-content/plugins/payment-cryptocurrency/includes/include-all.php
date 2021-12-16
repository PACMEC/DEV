<?php

// WooCommerce Order status
require_once CWOO_PLUGIN_DIR . 'includes/wc-order-status.php';

// Script and Style files dependencies
require_once CWOO_PLUGIN_DIR . 'includes/functions.wp-scripts.php';

// Add-on compatibility check
require_once CWOO_PLUGIN_DIR . 'admin/class-cw-versions.php';

// Admin notices
require_once CWOO_PLUGIN_DIR . 'admin/class-cw-admin-notice.php';

// Address list
require_once CWOO_PLUGIN_DIR . 'includes/CW_AddressList.php';

// Block.io wrapper
require_once CWOO_PLUGIN_DIR . 'includes/block_io.php';

// Trezor Connect Helper
require_once CWOO_PLUGIN_DIR . 'includes/CW_TrezorConnect.php';

// Bitcoin PHP library
require_once CWOO_PLUGIN_DIR . 'vendor/autoload.php';

if ( version_compare( phpversion(), '7.0.0', '<' ) ) {
	define( 'CWOO_SHOW_REFUND_QR', true );
	// kazuhikoarase PHP QR Code
	include_once CWOO_PLUGIN_DIR . 'includes/qrcode.php';
}

// Addresses
require_once CWOO_PLUGIN_DIR . 'includes/class.address.php';

// Maybe include HD Wallet Address class
if ( file_exists( WP_PLUGIN_DIR . '/cryptopay-hd-wallet-addon/class.hdwallet.php' ) ) {
	include_once WP_PLUGIN_DIR . '/cryptopay-hd-wallet-addon/class.hdwallet.php';
}

// Exchange rates
require_once CWOO_PLUGIN_DIR . 'includes/class.exchange-rates.php';
require_once CWOO_PLUGIN_DIR . 'includes/pricing/class.exchange-rate-processing.php';
require_once CWOO_PLUGIN_DIR . 'includes/pricing/class.exchange-rate-tools.php';
require_once CWOO_PLUGIN_DIR . 'includes/pricing/class.exchange-base.php';

spl_autoload_register( 'cw_exchange_autoloader' );
/**
 *
 * Autoload CryptoPay exchange rate classes
 *
 * @param string $class_name Class name.
 */
function cw_exchange_autoloader( $class_name ) {
	if ( false !== strpos( $class_name, 'CW_Exchange' ) ) {
		$classes_dir = CWOO_PLUGIN_DIR . 'includes/pricing/exchanges/';
		$class_file  = strtolower( 'class-' . str_replace( '_', '-', $class_name ) . '.php' );
		$file_path   = $classes_dir . $class_file;
		if ( file_exists( $file_path ) ) {
			include_once $file_path;
		} else {
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, "Cannot include class $class_name because $file_path does not exist", 'critical' );
		}
	}
}

// Validations
require_once CWOO_PLUGIN_DIR . 'includes/class.validate.php';

// Order Sorting
require_once CWOO_PLUGIN_DIR . 'includes/class.order-sorting.php';

// Factory
require_once CWOO_PLUGIN_DIR . 'includes/factory/class-cw-singleton.php';
require_once CWOO_PLUGIN_DIR . 'includes/factory/class-cw-singleton-array.php';

// Database
require_once CWOO_PLUGIN_DIR . 'includes/processing/database/class-cw-database-base.php';
require_once CWOO_PLUGIN_DIR . 'includes/processing/database/class-cw-database-cryptopay.php';
require_once CWOO_PLUGIN_DIR . 'includes/processing/database/class-cw-database-woocommerce.php';
require_once CWOO_PLUGIN_DIR . 'includes/processing/database/class-cw-payment-details-object.php';

// Order Processing
require_once CWOO_PLUGIN_DIR . 'includes/class.order-processing.php';
require_once CWOO_PLUGIN_DIR . 'includes/processing/class-cw-order-processing.php';
require_once CWOO_PLUGIN_DIR . 'includes/processing/class-cw-order-processing-tools.php';
require_once CWOO_PLUGIN_DIR . 'includes/processing/class-cw-block-explorer-processing.php';
require_once CWOO_PLUGIN_DIR . 'includes/processing/class-cw-block-explorer-tools.php';
require_once CWOO_PLUGIN_DIR . 'includes/processing/class-cw-block-explorer-base.php';

spl_autoload_register( 'cw_block_explorer_autoloader' );
/**
 *
 * Autoload CryptoPay block explorer classes
 *
 * @param string $class_name Class name.
 */
function cw_block_explorer_autoloader( $class_name ) {
	if ( false !== strpos( $class_name, 'CW_Block_Explorer_' ) ) {
		$class_path  = 'includes/processing/';
		$class_path .= false !== strpos( $class_name, 'CW_Block_Explorer_API' ) ? 'blockexplorerapis/' : 'blockexplorers/';
		$classes_dir = CWOO_PLUGIN_DIR . $class_path;
		$class_file  = strtolower( 'class-' . str_replace( '_', '-', $class_name ) . '.php' );
		$file_path   = $classes_dir . $class_file;
		if ( file_exists( $file_path ) ) {
			include_once $file_path;
		} else {
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, "Cannot include class $class_name because $file_path does not exist", 'critical' );
		}
	}
}

spl_autoload_register( 'cw_block_explorer_old_autoloader' );
/**
 *
 * Autoload old CryptoPay block explorer classes for backwards compatibility for addons
 *
 * @param string $class_name Class name.
 *
 *                           TODO: Remove this autoloader when no more usage of old classes and remove files in processing/blockexplorers/old/
 */
function cw_block_explorer_old_autoloader( $class_name ) {
	if ( false !== strpos( $class_name, 'CW_' ) && in_array( strtolower( $class_name ), array( 'cw_blockcypher', 'cw_blockio', 'cw_chainso', 'cw_esplora', 'cw_insight', 'cw_smartbit' ), true ) ) {
		$classes_dir = CWOO_PLUGIN_DIR . 'includes/processing/blockexplorers/old/';
		$class_file  = strtolower( 'class.' . str_replace( array( 'CW', '_' ), '', $class_name ) . '.php' );
		$file_path   = $classes_dir . $class_file;
		if ( file_exists( $file_path ) ) {
			include_once $file_path;
		} else {
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, "Cannot include class $class_name because $file_path does not exist", 'critical' );
		}
	}
}

// Admin backend
require_once CWOO_PLUGIN_DIR . 'admin/class-cw-adminmain.php';
require_once CWOO_PLUGIN_DIR . 'admin/admin-menus.php';

// Internationalization
cryptowoo_textdomain(); // Run this without hook to load before Redux Framework https://github.com/reduxframework/redux-framework/issues/1546#issuecomment-51727596

// Redux Framework via TGM
require_once CWOO_PLUGIN_DIR . 'admin/admin-init.php';

// Template modifications
require_once CWOO_PLUGIN_DIR . 'includes/class.formatting.php';

// API Helpers
$apis = array(
	'blockio',
	'blockcypher',
	'chainso',
	'insight',
	'smartbit',
	'esplora',
);

function cryptowoo_load_api_helpers( $apis ) {
	foreach ( $apis as $api ) {
		$file = sprintf( '%sincludes/processing/class.%s.php', CWOO_PLUGIN_DIR, $api );
		if ( file_exists( $file ) ) {
			include_once $file;
		}
	}
}

cryptowoo_load_api_helpers( $apis );

do_action( 'cw_include_all' );

