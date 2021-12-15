<?php
/**
 * EAN for WooCommerce - Main Class
 *
 * @version 2.2.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_EAN' ) ) :

final class Alg_WC_EAN {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = ALG_WC_EAN_VERSION;

	/**
	 * @var   Alg_WC_EAN The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_EAN Instance
	 *
	 * Ensures only one instance of Alg_WC_EAN is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @static
	 * @return  Alg_WC_EAN - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_EAN Constructor.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	function __construct() {

		// Check for active WooCommerce plugin
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		// Set up localisation
		add_action( 'init', array( $this, 'localize' ) );

		// Pro
		if ( 'ean-for-woocommerce-pro.php' === basename( ALG_WC_EAN_FILE ) ) {
			$this->pro = require_once( 'pro/class-alg-wc-ean-pro.php' );
		}

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}

	}

	/**
	 * localize.
	 *
	 * @version 2.2.0
	 * @since   2.0.0
	 */
	function localize() {
		load_plugin_textdomain( 'ean-for-woocommerce', false, dirname( plugin_basename( ALG_WC_EAN_FILE ) ) . '/langs/' );
	}

	/**
	 * includes.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function includes() {
		$this->core = require_once( 'class-alg-wc-ean-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( ALG_WC_EAN_FILE ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		// Version update
		if ( get_option( 'alg_wc_ean_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * action_links.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_ean' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'ean-for-woocommerce.php' === basename( ALG_WC_EAN_FILE ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/ean-for-woocommerce/">' .
				__( 'Go Pro', 'ean-for-woocommerce' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * add_woocommerce_settings_tab.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once( 'settings/class-alg-wc-ean-settings.php' );
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function version_updated() {
		update_option( 'alg_wc_ean_version', $this->version );
	}

	/**
	 * plugin_url.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( ALG_WC_EAN_FILE ) );
	}

	/**
	 * plugin_path.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( ALG_WC_EAN_FILE ) );
	}

}

endif;
