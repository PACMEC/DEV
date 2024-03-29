<?php
/*
Plugin Name: E-Commerce - Jetpack
Plugin URI: #
Description: Potencie su sitio E-Commerce con estas increíbles y poderosas funciones. Más de 100 módulos. Todo en un complemento de PACMEC.
Version: 5.4.9
Author: PACMEC
Author URI: https://booster.io
Text Domain: e-commerce-jetpack
Domain Path: /langs
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// Core functions
require_once('includes/functions/wcj-functions-core.php');

// Check if WooCommerce is active
if (!wcj_is_plugin_activated('woocommerce', 'woocommerce.php')) {
	return;
}

// Check if Plus is active
if ('e-commerce-jetpack.php' === basename(__FILE__) && wcj_is_plugin_activated('booster-plus-for-woocommerce', 'booster-plus-for-woocommerce.php')) {
	return;
}

if (!defined('WCJ_PLUGIN_FILE')) {
	/**
	 * WCJ_PLUGIN_FILE.
	 *
	 * @since 3.2.4
	 */
	define('WCJ_PLUGIN_FILE', __FILE__);
}

if (!class_exists('WC_Jetpack')) :

	/**
	 * Main WC_Jetpack Class
	 *
	 * @class   WC_Jetpack
	 * @version 3.2.4
	 * @since   1.0.0
	 */
	final class WC_Jetpack
	{

		/**
		 * Booster for WooCommerce version.
		 *
		 * @var   string
		 * @since 2.4.7
		 */
		public $version = '5.4.9-dev';

		/**
		 * @var WC_Jetpack The single instance of the class
		 */
		protected static $_instance = null;

		/**
		 * @version 5.3.3
		 * @since   5.3.3
		 *
		 * @var array
		 */
		public $options = array();

		/**
		 * Main WC_Jetpack Instance.
		 *
		 * Ensures only one instance of WC_Jetpack is loaded or can be loaded.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @static
		 * @see    WCJ()
		 * @return WC_Jetpack - Main instance
		 */
		public static function instance()
		{
			if (is_null(self::$_instance)) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * WC_Jetpack Constructor.
		 *
		 * @version 3.2.4
		 * @since   1.0.0
		 * @access  public
		 */
		function __construct()
		{
			require_once('includes/core/wcj-loader.php');
		}
	}

endif;

if (!function_exists('WCJ')) {
	/**
	 * Returns the main instance of WC_Jetpack to prevent the need to use globals.
	 *
	 * @version 2.5.7
	 * @since   1.0.0
	 * @return  WC_Jetpack
	 */
	function WCJ()
	{
		return WC_Jetpack::instance();
	}
}

WCJ();

/**
 * wc jetpack activation hook.
 *
 * @version 5.4.1
 */
function wcj_activation_hook()
{
	// Add transient to trigger redirect.
	set_transient('_wcj_activation_redirect', 1, 30);
}
register_activation_hook(__FILE__, 'wcj_activation_hook');
