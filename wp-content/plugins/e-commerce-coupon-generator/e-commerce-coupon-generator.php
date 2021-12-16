<?php
/**
 * Plugin Name: 	E-Commerce - Generador de cupones
 * Plugin URI:		#
 * Description:		Genere fácilmente <strong> MILLONES </strong> de cupones únicos para su tienda en línea. ¡Utilice todos los ajustes de cupón con los que esté familiarizado!
 * Version: 		  1.0.0
 * Author: 			  PACMEC
 * Author URI: 	  #
 * Text Domain: 	e-commerce-coupon-generator
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class E_Commerce_Coupon_Generator.
 *
 * Main E_Commerce_Coupon_Generator class initializes the plugin.
 *
 * @class		E_Commerce_Coupon_Generator
 * @version		1.0.0
 * @author		PACMEC
 */
class E_Commerce_Coupon_Generator {


	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string $version Plugin version number.
	 */
	public $version = '1.0.0';


	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 * @var string $file Plugin file path.
	 */
	public $file = __FILE__;


	/**
	 * Instance of E_Commerce_Coupon_Generator.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance The instance of E_Commerce_Coupon_Generator.
	 */
	private static $instance;


	/**
	 * Construct.
	 *
	 * Initialize the class and plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}


	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 * @return object Instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Init.
	 *
	 * Initialize plugin parts.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Check if E-Commerce is active
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) && ! function_exists( 'WC' ) ) {
			return;
		}


		if ( is_admin() ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/admin/wccg-core-functions.php';

			// Classes
			require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-wccg-admin.php';
			$this->admin = new WCCG_Admin();
		}

		$this->load_textdomain();
	}


	/**
	 * Textdomain.
	 *
	 * Load the textdomain based on WP language.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'coupon-generator-for-woocommerce', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}


}


if ( ! function_exists( 'E_Commerce_Coupon_Generator' ) ) {

	/**
	 * The main function responsible for returning the E_Commerce_Coupon_Generator object.
	 *
	 * Use this function like you would a global variable, except without needing to declare the global.
	 *
	 * Example: <?php E_Commerce_Coupon_Generator()->method_name(); ?>
	 *
	 * @since 1.0.0
	 *
	 * @return object E_Commerce_Coupon_Generator class object.
	 */
	function E_Commerce_Coupon_Generator() {
		return E_Commerce_Coupon_Generator::instance();
	}
}

E_Commerce_Coupon_Generator();
