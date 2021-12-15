<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @author MakeWebBetter <webmaster@makewebbetter.com>
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @author MakeWebBetter <webmaster@makewebbetter.com>
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Pos_For_Woocommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   protected
	 * @var      Pos_For_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   protected
	 * @var      string    $pfw_onboard    To initializsed the object of class onboard.
	 */
	protected $pfw_onboard;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function __construct() {

		if ( defined( 'POS_FOR_WOOCOMMERCE_VERSION' ) ) {

			$this->version = POS_FOR_WOOCOMMERCE_VERSION;
		} else {

			$this->version = '1.0.0';
		}

		$this->plugin_name = 'mwb-point-of-sale-woocommerce';

		$this->mwb_pos_for_woocommerce_dependencies();
		$this->mwb_pos_for_woocommerce_locale();
		if ( is_admin() ) {
			$this->mwb_pos_for_woocommerce_admin_hooks();
		}
		$this->mwb_pos_for_woocommerce_public_hooks();

		$this->mwb_pos_for_woocommerce_api_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Pos_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Pos_For_Woocommerce_i18n. Defines internationalization functionality.
	 * - Pos_For_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Pos_For_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   private
	 */
	private function mwb_pos_for_woocommerce_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pos-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pos-for-woocommerce-i18n.php';

		if ( is_admin() ) {
			// The class responsible for defining all actions that occur in the admin area.
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pos-for-woocommerce-admin.php';

			// The class responsible for on-boarding steps for plugin.
			if ( is_dir( plugin_dir_path( dirname( __FILE__ ) ) . 'onboarding' ) && ! class_exists( 'Pos_For_Woocommerce_Onboarding_Steps' ) ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pos-for-woocommerce-onboarding-steps.php';
			}

			if ( class_exists( 'Pos_For_Woocommerce_Onboarding_Steps' ) ) {
				$pfw_onboard_steps = new Pos_For_Woocommerce_Onboarding_Steps();
			}
		}

		// The class responsible for defining all actions that occur in the public-facing side of the site.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pos-for-woocommerce-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'package/rest-api/class-pos-for-woocommerce-rest-api.php';

		$this->loader = new Pos_For_Woocommerce_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Pos_For_Woocommerce_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   private
	 */
	private function mwb_pos_for_woocommerce_locale() {

		$plugin_i18n = new Pos_For_Woocommerce_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mwb_pos_for_woocommerce_admin_hooks() {

		$pfw_plugin_admin = new Pos_For_Woocommerce_Admin( $this->mwb_pos_get_plugin_name(), $this->mwb_pos_get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $pfw_plugin_admin, 'mwb_pos_admin_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $pfw_plugin_admin, 'mwb_pos_admin_enqueue_scripts' );

		// Add settings menu for POS for Woocommerce.
		$this->loader->add_action( 'admin_menu', $pfw_plugin_admin, 'mwb_pos_options_page' );
		$this->loader->add_action( 'admin_menu', $pfw_plugin_admin, 'mwb_pos_remove_default_submenu', 50 );

		// Add node in admin top bar for POS.
		$this->loader->add_action( 'admin_bar_menu', $pfw_plugin_admin, 'mwb_add_pos_panel_link', 99 );

		// All admin actions and filters after License Validation goes here.
		$this->loader->add_filter( 'mwb_add_plugins_menus_array', $pfw_plugin_admin, 'mwb_pos_admin_submenu_page', 15 );
		$this->loader->add_filter( 'pfw_general_settings_array', $pfw_plugin_admin, 'mwb_pos_admin_general_settings_page', 10 );
		$this->loader->add_filter( 'pfw_login_settings_array', $pfw_plugin_admin, 'mwb_pos_admin_login_user_settings_page' );
		$this->loader->add_filter( 'pfw_supprot_tab_settings_array', $pfw_plugin_admin, 'mwb_pos_admin_support_settings_page', 10 );
		$this->loader->add_filter( 'pfw_generate_product_barcode', $pfw_plugin_admin, 'mwb_pos_generate_products_barcode' );

		// Add settings tab.
		$this->loader->add_filter( 'mwb_pfw_plugin_standard_admin_settings_tabs', $pfw_plugin_admin, 'mwb_pos_add_tabs' );

		// Saving tab settings.
		$this->loader->add_action( 'admin_init', $pfw_plugin_admin, 'mwb_pos_admin_save_general_settings' );
		$this->loader->add_action( 'admin_init', $pfw_plugin_admin, 'mwb_pos_show_hidden_orders' );
		// Add custom column in woocommerce product listing area.
		$this->loader->add_filter( 'manage_edit-product_columns', $pfw_plugin_admin, 'mwb_pos_add_qr_code_column' );
		$this->loader->add_action( 'manage_product_posts_custom_column', $pfw_plugin_admin, 'mwb_pos_populate_qr_column' );

		// Ajax handler.
		$this->loader->add_action( 'wp_ajax_mwb_pfw_generate_pro_barcode', $pfw_plugin_admin, 'mwb_pos_generate_pro_barcode' );

		// Handle product tax class.
		$this->loader->add_filter( 'wc_tax_enabled', $pfw_plugin_admin, 'mwb_pos_wc_change_tax_class', 10 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   private
	 */
	private function mwb_pos_for_woocommerce_public_hooks() {

		$pfw_plugin_public = new Pos_For_Woocommerce_Public( $this->mwb_pos_get_plugin_name(), $this->mwb_pos_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $pfw_plugin_public, 'mwb_pos_public_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $pfw_plugin_public, 'mwb_pos_public_enqueue_scripts' );

		$this->loader->add_filter( 'template_include', $pfw_plugin_public, 'mwb_pos_panel_template', 10 );

		// Handle ajax request.
		$this->loader->add_action( 'wp_ajax_mwb_pos_config', $pfw_plugin_public, 'mwb_pos_config' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_config', $pfw_plugin_public, 'mwb_pos_config' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_get_currency_symb', $pfw_plugin_public, 'mwb_pos_get_currency_symb' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_get_currency_symb', $pfw_plugin_public, 'mwb_pos_get_currency_symb' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_get_products', $pfw_plugin_public, 'mwb_pos_get_products' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_get_products', $pfw_plugin_public, 'mwb_pos_get_products' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_validate_user', $pfw_plugin_public, 'mwb_pos_validate_user' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_validate_user', $pfw_plugin_public, 'mwb_pos_validate_user' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_login_page_settings', $pfw_plugin_public, 'mwb_pos_login_page_settings' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_login_page_settings', $pfw_plugin_public, 'mwb_pos_login_page_settings' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_check_user_existence', $pfw_plugin_public, 'mwb_pos_check_user_existence' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_check_user_existence', $pfw_plugin_public, 'mwb_pos_check_user_existence' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_logout_user', $pfw_plugin_public, 'mwb_pos_logout_user' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_logout_user', $pfw_plugin_public, 'mwb_pos_logout_user' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_prod_category', $pfw_plugin_public, 'mwb_pos_prod_category' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_prod_category', $pfw_plugin_public, 'mwb_pos_prod_category' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_get_category_prod', $pfw_plugin_public, 'mwb_pos_get_category_prod' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_get_category_prod', $pfw_plugin_public, 'mwb_pos_get_category_prod' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_get_search_prod', $pfw_plugin_public, 'mwb_pos_get_search_prod' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_get_search_prod', $pfw_plugin_public, 'mwb_pos_get_search_prod' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_order_details', $pfw_plugin_public, 'mwb_pos_order_details' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_order_details', $pfw_plugin_public, 'mwb_pos_order_details' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_current_manager_data', $pfw_plugin_public, 'mwb_pos_current_manager_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_current_manager_data', $pfw_plugin_public, 'mwb_pos_current_manager_data' );

		$this->loader->add_action( 'wp_ajax_mwb_pos_update_manager_profile', $pfw_plugin_public, 'mwb_pos_update_manager_profile' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_pos_update_manager_profile', $pfw_plugin_public, 'mwb_pos_update_manager_profile' );

		$this->loader->add_filter( 'show_admin_bar', $pfw_plugin_public, 'mwb_pos_panel_remove_admin_bar', 10 );
		$this->loader->add_action( 'wp_head', $pfw_plugin_public, 'mwb_pos_remove_theme_styles', 99 );
		$this->loader->add_filter( 'print_styles_array', $pfw_plugin_public, 'mwb_pos_remve_inline_styles', 99 );
	}


	/**
	 * Register all of the hooks related to the api functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   private
	 */
	private function mwb_pos_for_woocommerce_api_hooks() {

		$pfw_plugin_api = new Pos_For_Woocommerce_Rest_Api( $this->mwb_pos_get_plugin_name(), $this->mwb_pos_get_version() );

		$this->loader->add_action( 'rest_api_init', $pfw_plugin_api, 'mwb_pos_add_endpoint' );

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_run() {
		$this->loader->mwb_pos_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @return    string    The name of the plugin.
	 */
	public function mwb_pos_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @return    Pos_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function pfw_get_loader() {
		return $this->loader;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @return    Pos_For_Woocommerce_Onboard    Orchestrates the hooks of the plugin.
	 */
	public function pfw_get_onboard() {
		return $this->pfw_onboard;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @return    string    The version number of the plugin.
	 */
	public function mwb_pos_get_version() {
		return $this->version;
	}

	/**
	 * Predefined default mwb_pfw_plug tabs.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @return  Array       An key=>value pair of POS for Woocommerce tabs.
	 */
	public function mwb_pos_plug_default_tabs() {

		$pfw_default_tabs                                 = array();
		$pfw_default_tabs['pos-for-woocommerce-overview'] = array(
			'title'       => esc_html__( 'Overview', 'mwb-point-of-sale-woocommerce' ),
			'name'        => 'pos-for-woocommerce-overview',
		);
		$pfw_default_tabs['pos-for-woocommerce-general']  = array(
			'title'       => esc_html__( 'General Setting', 'mwb-point-of-sale-woocommerce' ),
			'name'        => 'pos-for-woocommerce-general',
		);
		$pfw_default_tabs                                 = apply_filters( 'mwb_pfw_plugin_standard_admin_settings_tabs', $pfw_default_tabs );

		$pfw_default_tabs['pos-for-woocommerce-system-status'] = array(
			'title'       => esc_html__( 'System Status', 'mwb-point-of-sale-woocommerce' ),
			'name'        => 'pos-for-woocommerce-system-status',
		);

		return $pfw_default_tabs;
	}

	/**
	 * Locate and load appropriate tempate.
	 *
	 * @since   1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param string $path path file for inclusion.
	 * @param array  $params parameters to pass to the file for access.
	 */
	public function mwb_pos_plug_load_template( $path, $params = array() ) {

		$pfw_file_path = POS_FOR_WOOCOMMERCE_DIR_PATH . $path;

		if ( file_exists( $pfw_file_path ) ) {

			include $pfw_file_path;
		} else {

			/* translators: %s: file path */
			$pfw_notice = sprintf( esc_html__( 'Unable to locate file at location "%s". Some features may not work properly in this plugin. Please contact us!', 'mwb-point-of-sale-woocommerce' ), $pfw_file_path );
			$this->mwb_pos_plug_admin_notice( $pfw_notice, 'error' );
		}
	}

	/**
	 * Show admin notices.
	 *
	 * @param  string $pfw_message    Message to display.
	 * @param  string $type       notice type, accepted values - error/update/update-nag.
	 * @since  1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public static function mwb_pos_plug_admin_notice( $pfw_message, $type = 'error' ) {

		$pfw_classes = 'notice ';

		switch ( $type ) {

			case 'update':
				$pfw_classes .= 'updated is-dismissible';
				break;

			case 'update-nag':
				$pfw_classes .= 'update-nag is-dismissible';
				break;

			case 'success':
				$pfw_classes .= 'notice-success is-dismissible';
				break;

			default:
				$pfw_classes .= 'notice-error is-dismissible';
		}

		$pfw_notice  = '<div class="' . esc_attr( $pfw_classes ) . ' mwb-errorr-8">';
		$pfw_notice .= '<p>' . esc_html( $pfw_message ) . '</p>';
		$pfw_notice .= '</div>';

		echo wp_kses_post( $pfw_notice );
	}


	/**
	 * Show WordPress and server info.
	 *
	 * @return  Array $pfw_system_data       returns array of all WordPress and server related information.
	 * @since  1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_plug_system_status() {
		global $wpdb;
		$pfw_system_status    = array();
		$pfw_wordpress_status = array();
		$pfw_system_data      = array();

		// Get the web server.
		$pfw_system_status['web_server'] = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

		// Get PHP version.
		$pfw_system_status['php_version'] = function_exists( 'phpversion' ) ? phpversion() : __( 'N/A (phpversion function does not exist)', 'mwb-point-of-sale-woocommerce' );

		// Get the server's IP address.
		$pfw_system_status['server_ip'] = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';

		// Get the server's port.
		$pfw_system_status['server_port'] = isset( $_SERVER['SERVER_PORT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_PORT'] ) ) : '';

		// Get the uptime.
		$pfw_system_status['uptime'] = function_exists( 'exec' ) ? @exec( 'uptime -p' ) : __( 'N/A (make sure exec function is enabled)', 'mwb-point-of-sale-woocommerce' ); //phpcs:disabled

		// Get the server path.
		$pfw_system_status['server_path'] = defined( 'ABSPATH' ) ? ABSPATH : __( 'N/A (ABSPATH constant not defined)', 'mwb-point-of-sale-woocommerce' );

		// Get the OS.
		$pfw_system_status['os'] = function_exists( 'php_uname' ) ? php_uname( 's' ) : __( 'N/A (php_uname function does not exist)', 'mwb-point-of-sale-woocommerce' );

		// Get WordPress version.
		$pfw_wordpress_status['wp_version'] = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : __( 'N/A (get_bloginfo function does not exist)', 'mwb-point-of-sale-woocommerce' );

		// Get and count active WordPress plugins.
		$pfw_wordpress_status['wp_active_plugins'] = function_exists( 'get_option' ) ? count( get_option( 'active_plugins' ) ) : __( 'N/A (get_option function does not exist)', 'mwb-point-of-sale-woocommerce' );

		// See if this site is multisite or not.
		$pfw_wordpress_status['wp_multisite'] = function_exists( 'is_multisite' ) && is_multisite() ? __( 'Yes', 'mwb-point-of-sale-woocommerce' ) : __( 'No', 'mwb-point-of-sale-woocommerce' );

		// See if WP Debug is enabled.
		$pfw_wordpress_status['wp_debug_enabled'] = defined( 'WP_DEBUG' ) ? __( 'Yes', 'mwb-point-of-sale-woocommerce' ) : __( 'No', 'mwb-point-of-sale-woocommerce' );

		// See if WP Cache is enabled.
		$pfw_wordpress_status['wp_cache_enabled'] = defined( 'WP_CACHE' ) ? __( 'Yes', 'mwb-point-of-sale-woocommerce' ) : __( 'No', 'mwb-point-of-sale-woocommerce' );

		// Get the total number of WordPress users on the site.
		$pfw_wordpress_status['wp_users'] = function_exists( 'count_users' ) ? count_users() : __( 'N/A (count_users function does not exist)', 'mwb-point-of-sale-woocommerce' );

		// Get the number of published WordPress posts.
		$pfw_wordpress_status['wp_posts'] = wp_count_posts()->publish >= 1 ? wp_count_posts()->publish : __( '0', 'mwb-point-of-sale-woocommerce' );

		// Get PHP memory limit.
		$pfw_system_status['php_memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'mwb-point-of-sale-woocommerce' );

		// Get the PHP error log path.
		$pfw_system_status['php_error_log_path'] = ! ini_get( 'error_log' ) ? __( 'N/A', 'mwb-point-of-sale-woocommerce' ) : ini_get( 'error_log' );

		// Get PHP max upload size.
		$pfw_system_status['php_max_upload'] = function_exists( 'ini_get' ) ? (int) ini_get( 'upload_max_filesize' ) : __( 'N/A (ini_get function does not exist)', 'mwb-point-of-sale-woocommerce' );

		// Get PHP max post size.
		$pfw_system_status['php_max_post'] = function_exists( 'ini_get' ) ? (int) ini_get( 'post_max_size' ) : __( 'N/A (ini_get function does not exist)', 'mwb-point-of-sale-woocommerce' );

		// Get the PHP architecture.
		if ( PHP_INT_SIZE === 4 ) {
			$pfw_system_status['php_architecture'] = '32-bit';
		} elseif ( PHP_INT_SIZE === 8 ) {
			$pfw_system_status['php_architecture'] = '64-bit';
		} else {
			$pfw_system_status['php_architecture'] = 'N/A';
		}

		// Get server host name.
		$pfw_system_status['server_hostname'] = function_exists( 'gethostname' ) ? gethostname() : __( 'N/A (gethostname function does not exist)', 'mwb-point-of-sale-woocommerce' );

		// Show the number of processes currently running on the server.
		$pfw_system_status['processes'] = function_exists( 'exec' ) ? @exec( 'ps aux | wc -l' ) : __( 'N/A (make sure exec is enabled)', 'mwb-point-of-sale-woocommerce' );

		// Get the memory usage.
		$pfw_system_status['memory_usage'] = function_exists( 'memory_get_peak_usage' ) ? round( memory_get_peak_usage( true ) / 1024 / 1024, 2 ) : 0;

		// Get CPU usage.
		// Check to see if system is Windows, if so then use an alternative since sys_getloadavg() won't work.
		if ( stristr( PHP_OS, 'win' ) ) {
			$pfw_system_status['is_windows']        = true;
			$pfw_system_status['windows_cpu_usage'] = function_exists( 'exec' ) ? @exec( 'wmic cpu get loadpercentage /all' ) : __( 'N/A (make sure exec is enabled)', 'mwb-point-of-sale-woocommerce' );
		}

		// Get the memory limit.
		$pfw_system_status['memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'mwb-point-of-sale-woocommerce' );

		// Get the PHP maximum execution time.
		$pfw_system_status['php_max_execution_time'] = function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : __( 'N/A (ini_get function does not exist)', 'mwb-point-of-sale-woocommerce' );

		$pfw_system_data['php'] = $pfw_system_status;
		$pfw_system_data['wp']  = $pfw_wordpress_status;

		return $pfw_system_data;
	}

	/**
	 * Generate html components.
	 *
	 * @param  string $pfw_components    html to display.
	 * @since  1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_plug_generate_html( $pfw_components = array() ) {
		if ( is_array( $pfw_components ) && ! empty( $pfw_components ) ) {
			foreach ( $pfw_components as $pfw_component ) {
				$mwb_pfw_name = array_key_exists( 'name', $pfw_component ) ? $pfw_component['name'] : $pfw_component['id'];
				switch ( $pfw_component['type'] ) {

					case 'hidden':
					case 'number':
					case 'email':
					case 'text':
						?>
						<div class="mwb-form-group mwb-pfw-<?php echo esc_attr( $pfw_component['type'] ); ?>">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $pfw_component['id'] ); ?>" class="mwb-form-label"><?php echo esc_html( $pfw_component['title'] ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<?php if ( 'number' != $pfw_component['type'] ) { ?>
												<span class="mdc-floating-label" id="my-label-id" style=""><?php echo esc_attr( $pfw_component['placeholder'] ); ?></span>
											<?php } ?>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input 
									class="mdc-text-field__input <?php echo esc_attr( $pfw_component['class'] ); ?>" 
									name="<?php echo esc_attr( $mwb_pfw_name ); ?>"
									id="<?php echo esc_attr( $pfw_component['id'] ); ?>"
									type="<?php echo esc_attr( $pfw_component['type'] ); ?>"
									value="<?php echo esc_attr( $pfw_component['value'] ); ?>"
									placeholder="<?php echo esc_attr( $pfw_component['placeholder'] ); ?>"
									>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo esc_attr( $pfw_component['description'] ); ?></div>
								</div>
							</div>
						</div>
						<?php
						break;

					case 'password':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $pfw_component['id'] ); ?>" class="mwb-form-label"><?php echo esc_html( $pfw_component['title'] ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input 
									class="mdc-text-field__input <?php echo esc_attr( $pfw_component['class'] ); ?> mwb-form__password" 
									name="<?php echo esc_attr( $mwb_pfw_name ); ?>"
									id="<?php echo esc_attr( $pfw_component['id'] ); ?>"
									type="<?php echo esc_attr( $pfw_component['type'] ); ?>"
									value="<?php echo esc_attr( $pfw_component['value'] ); ?>"
									placeholder="<?php echo esc_attr( $pfw_component['placeholder'] ); ?>"
									>
									<i class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing mwb-password-hidden" tabindex="0" role="button">visibility</i>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo esc_attr( $pfw_component['description'] ); ?></div>
								</div>
							</div>
						</div>
						<?php
						break;

					case 'textarea':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label class="mwb-form-label" for="<?php echo esc_attr( $pfw_component['id'] ); ?>"><?php echo esc_attr( $pfw_component['title'] ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--textarea"  	for="text-field-hero-input">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<span class="mdc-floating-label"><?php echo esc_attr( $pfw_component['placeholder'] ); ?></span>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<span class="mdc-text-field__resizer">
										<textarea class="mdc-text-field__input <?php echo esc_attr( $pfw_component['class'] ); ?>" rows="2" cols="25" aria-label="Label" name="<?php echo esc_attr( $mwb_pfw_name ); ?>" id="<?php echo esc_attr( $pfw_component['id'] ); ?>" placeholder="<?php echo esc_attr( $pfw_component['placeholder'] ); ?>"><?php echo esc_textarea( $pfw_component['value'] ); // WPCS: XSS ok. ?></textarea>
									</span>
								</label>

							</div>
						</div>

						<?php
						break;

					case 'select':
					case 'multiselect':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label class="mwb-form-label" for="<?php echo esc_attr( $pfw_component['id'] ); ?>"><?php echo esc_html( $pfw_component['title'] ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<div class="mwb-form-select">
									<select name="<?php echo esc_attr( $mwb_pfw_name ); ?><?php echo ( 'multiselect' === $pfw_component['type'] ) ? '[]' : ''; ?>" id="<?php echo esc_attr( $pfw_component['id'] ); ?>" class="mdl-textfield__input <?php echo esc_attr( $pfw_component['class'] ); ?>" <?php echo 'multiselect' === $pfw_component['type'] ? 'multiple="multiple"' : ''; ?> >
										<?php
										foreach ( $pfw_component['options'] as $pfw_key => $pfw_val ) {
											?>
											<option value="<?php echo esc_attr( $pfw_key ); ?>"
												<?php
												if ( is_array( $pfw_component['value'] ) ) {
													selected( in_array( (string) $pfw_key, $pfw_component['value'], true ), true );
												} else {
													selected( $pfw_component['value'], (string) $pfw_key );
												}
												?>
												>
												<?php echo esc_html( $pfw_val ); ?>
											</option>
											<?php
										}
										?>
									</select>
									<label class="mdl-textfield__label" for="octane"><?php echo esc_html( $pfw_component['description'] ); ?></label>
								</div>
							</div>
						</div>

						<?php
						break;

					case 'checkbox':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $pfw_component['id'] ); ?>" class="mwb-form-label"><?php echo esc_html( $pfw_component['title'] ); ?></label>
							</div>
							<div class="mwb-form-group__control mwb-pl-4">
								<div class="mdc-form-field">
									<div class="mdc-checkbox">
										<input 
										name="<?php echo esc_attr( $mwb_pfw_name ); ?>"
										id="<?php echo esc_attr( $pfw_component['id'] ); ?>"
										type="checkbox"
										class="mdc-checkbox__native-control <?php echo esc_attr( isset( $pfw_component['class'] ) ? $pfw_component['class'] : '' ); ?>"
										value="<?php echo esc_attr( $pfw_component['value'] ); ?>"
										<?php checked( $pfw_component['value'], '1' ); ?>
										/>
										<div class="mdc-checkbox__background">
											<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
												<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
											</svg>
											<div class="mdc-checkbox__mixedmark"></div>
										</div>
										<div class="mdc-checkbox__ripple"></div>
									</div>
									<label for="checkbox-1"><?php echo esc_html( $pfw_component['description'] ); // WPCS: XSS ok. ?></label>
								</div>
							</div>
						</div>
						<?php
						break;

					case 'radio':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $pfw_component['id'] ); ?>" class="mwb-form-label"><?php echo esc_html( $pfw_component['title'] ); ?></label>
							</div>
							<div class="mwb-form-group__control mwb-pl-4">
								<div class="mwb-flex-col">
									<?php
									foreach ( $pfw_component['options'] as $pfw_radio_key => $pfw_radio_val ) {
										?>
										<div class="mdc-form-field">
											<div class="mdc-radio">
												<input
												name="<?php echo esc_attr( $mwb_pfw_name ); ?>"
												value="<?php echo esc_attr( $pfw_radio_key ); ?>"
												type="radio"
												class="mdc-radio__native-control <?php echo esc_attr( $pfw_component['class'] ); ?>"
												<?php checked( $pfw_radio_key, $pfw_component['value'] ); ?>
												>
												<div class="mdc-radio__background">
													<div class="mdc-radio__outer-circle"></div>
													<div class="mdc-radio__inner-circle"></div>
												</div>
												<div class="mdc-radio__ripple"></div>
											</div>
											<label for="radio-1"><?php echo esc_html( $pfw_radio_val ); ?></label>
										</div>	
										<?php
									}
									?>
								</div>
							</div>
						</div>
						<?php
						break;

					case 'radio-switch':
						?>

						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="" class="mwb-form-label"><?php echo esc_html( $pfw_component['title'] ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<div>
									<div class="mdc-switch">
										<div class="mdc-switch__track"></div>
										<div class="mdc-switch__thumb-underlay">
											<div class="mdc-switch__thumb"></div>
											<input name="<?php echo esc_html( $mwb_pfw_name ); ?>" type="checkbox" id="basic-switch" value="on" class="mdc-switch__native-control" role="switch" aria-checked="<?php echo 'on' === $pfw_component['value'] ? 'true' : 'false'; ?>"
											<?php checked( $pfw_component['value'], 'on' ); ?>
											>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
						break;

					case 'imageupload':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="" class="mwb-form-label"><?php echo esc_html( $pfw_component['title'] ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<img style="<?php echo esc_attr( $pfw_component['style'] ); ?>" class="<?php echo esc_attr( $pfw_component['id'] ); ?>_img" src="<?php echo esc_attr( $pfw_component['value'] ); ?>" />
								<input
								name="<?php echo esc_attr( $mwb_pfw_name ); ?>"
								id="<?php echo esc_attr( $pfw_component['id'] ); ?>"
								type="hidden"
								class="<?php echo esc_attr( isset( $pfw_component['class'] ) ? $pfw_component['class'] : '' ); ?>"
								value="<?php echo esc_attr( $pfw_component['value'] ); ?>" /> 
								<input type="button" name="mwb-pfw-img-upload" id="<?php echo esc_attr( $pfw_component['id'] ); ?>_upload" class="button-secondary" value="<?php esc_html_e( 'Upload', 'mwb-point-of-sale-woocommerce' ); ?>">
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo esc_attr( $pfw_component['description'] ); ?></div>
								</div>
							</div>
						</div>
						<?php
						break;

					case 'button':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label"></div>
							<div class="mwb-form-group__control">
								<button class="mdc-button mdc-button--raised" name="<?php echo esc_attr( $mwb_pfw_name ); ?>"
									id="<?php echo esc_attr( $pfw_component['id'] ); ?>"> <span class="mdc-button__ripple"></span>
									<span class="mdc-button__label"><?php echo esc_attr( $pfw_component['button_text'] ); ?></span>
								</button>
							</div>
						</div>

						<?php
						break;

					case 'submit':
						?>
						<tr valign="top">
							<td scope="row">
								<input type="submit" class="button button-primary" 
								name="<?php echo esc_attr( $mwb_pfw_name ); ?>"
								id="<?php echo esc_attr( $pfw_component['id'] ); ?>"
								value="<?php echo esc_attr( $pfw_component['button_text'] ); ?>"
								/>
							</td>
						</tr>
						<?php
						break;

					default:
						break;
				}
			}
		}
	}
}
