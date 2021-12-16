<?php
/**
 * El archivo de arranque del complemento
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           MWB_Point_Of_Sale_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       E-Commerce - Punto de venta (POS) MWB
 * Plugin URI:        #
 * Description:       El sistema de punto de venta (POS) para el complemento E-Commerce ayuda a los comerciantes a proporcionar una mejor manera de verificar los pedidos y proporcionar una manera fácil de rastrear sus pedidos tanto en línea como en las tiendas locales.
 * Version:           1.0.0
 * Author:            PACMEC
 * Author URI:        #
 * Text Domain:       e-commerce-point-of-sale-pos
 * Domain Path:       /languages
 *
 * Requires at least: 4.6
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// To Activate plugin only when WooCommerce is active.
$mwb_pos_active_woo = true;

// Check if WooCommerce is active.
require_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	$mwb_pos_active_woo = false;
}

if ( $mwb_pos_active_woo ) {

	/**
	 * Define plugin constants.
	 *
	 * @since             1.0.0
	 */
	function mwb_define_plugin_for_woocommerce_constants() {

		mwb_pos_for_woocommerce_constants( 'POS_FOR_WOOCOMMERCE_VERSION', '1.0.2' );
		mwb_pos_for_woocommerce_constants( 'POS_FOR_WOOCOMMERCE_DIR_PATH', plugin_dir_path( __FILE__ ) );
		mwb_pos_for_woocommerce_constants( 'POS_FOR_WOOCOMMERCE_DIR_URL', plugin_dir_url( __FILE__ ) );
		mwb_pos_for_woocommerce_constants( 'POS_FOR_WOOCOMMERCE_SERVER_URL', 'https://makewebbetter.com' );
		mwb_pos_for_woocommerce_constants( 'POS_FOR_WOOCOMMERCE_ITEM_REFERENCE', 'POS for Woocommerce' );
		mwb_pos_for_woocommerce_constants( 'POS_FOR_WC_VERSION', time() );
	}


	/**
	 * Callable function for defining plugin constants.
	 *
	 * @param   String $key    Key for contant.
	 * @param   String $value   value for contant.
	 * @since             1.0.0
	 */
	function mwb_pos_for_woocommerce_constants( $key, $value ) {

		if ( ! defined( $key ) ) {

			define( $key, $value );
		}
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-pos-for-woocommerce-activator.php
	 */
	function activate_mwb_pos_for_woocommerce() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-pos-for-woocommerce-activator.php';
		Pos_For_Woocommerce_Activator::pos_for_woocommerce_activate();
		$mwb_pfw_active_plugin = get_option( 'mwb_all_plugins_active', false );
		if ( is_array( $mwb_pfw_active_plugin ) && ! empty( $mwb_pfw_active_plugin ) ) {
			$mwb_pfw_active_plugin['mwb-point-of-sale-woocommerce'] = array(
				'plugin_name' => __( 'POS for Woocommerce', 'mwb-point-of-sale-woocommerce' ),
				'active' => '1',
			);
		} else {
			$mwb_pfw_active_plugin                        = array();
			$mwb_pfw_active_plugin['mwb-point-of-sale-woocommerce'] = array(
				'plugin_name' => __( 'POS for Woocommerce', 'mwb-point-of-sale-woocommerce' ),
				'active' => '1',
			);
		}
		update_option( 'mwb_all_plugins_active', $mwb_pfw_active_plugin );
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-pos-for-woocommerce-deactivator.php
	 */
	function deactivate_mwb_pos_for_woocommerce() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-pos-for-woocommerce-deactivator.php';
		Pos_For_Woocommerce_Deactivator::pos_for_woocommerce_deactivate();
		$mwb_pfw_deactive_plugin = get_option( 'mwb_all_plugins_active', false );
		if ( is_array( $mwb_pfw_deactive_plugin ) && ! empty( $mwb_pfw_deactive_plugin ) ) {
			foreach ( $mwb_pfw_deactive_plugin as $mwb_pfw_deactive_key => $mwb_pfw_deactive ) {
				if ( 'mwb-point-of-sale-woocommerce' === $mwb_pfw_deactive_key ) {
					$mwb_pfw_deactive_plugin[ $mwb_pfw_deactive_key ]['active'] = '0';
				}
			}
		}
		update_option( 'mwb_all_plugins_active', $mwb_pfw_deactive_plugin );
	}

	register_activation_hook( __FILE__, 'activate_mwb_pos_for_woocommerce' );
	register_deactivation_hook( __FILE__, 'deactivate_mwb_pos_for_woocommerce' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-pos-for-woocommerce.php';


	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_mwb_pos_for_woocommerce() {
		mwb_define_plugin_for_woocommerce_constants();

		$pfw_plugin_standard = new Pos_For_Woocommerce();
		$pfw_plugin_standard->mwb_pos_run();
		$GLOBALS['pfw_mwb_pfw_obj'] = $pfw_plugin_standard;
		$GLOBALS['mwb_pfw_notices'] = false;

	}
	run_mwb_pos_for_woocommerce();


	// Add settings link on plugin page.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mwb_pos_for_woocommerce_settings_link' );

	/**
	 * Settings link.
	 *
	 * @since    1.0.0
	 * @param   Array $links    Settings link array.
	 */
	function mwb_pos_for_woocommerce_settings_link( $links ) {

		$my_link = array(
			'<a href="' . admin_url( 'admin.php?page=pos_for_woocommerce_menu' ) . '">' . __( 'Settings', 'mwb-point-of-sale-woocommerce' ) . '</a>',
		);
		return array_merge( $my_link, $links );
	}

	add_filter( 'plugin_row_meta', 'mwb_pos_doc_and_premium_link', 10, 2 );

	/**
	 * Callable function for adding plugin row meta.
	 *
	 * @name mwb_pos_doc_and_premium_link.
	 * @param string $links link of the constant.
	 * @param array  $file name of the plugin.
	 */
	function mwb_pos_doc_and_premium_link( $links, $file ) {
		if ( strpos( $file, 'mwb-point-of-sale-woocommerce.php' ) !== false ) {

			$row_meta = array(
				'demo'    => '<a target="_blank" href="https://demo.makewebbetter.com/mwb-point-of-sale-for-woocommerce/point-of-sale/?utm_source=MWB-POS-org&utm_medium=MWB-org-backend-&utm_campaign=MWB-POS-demo"><img src="' . esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/Demo.svg' ) . '" style="width: 20px;padding-right: 5px;">' . esc_html__( 'Demo', 'mwb-point-of-sale-woocommerce' ) . '</a>',

				'docs'    => '<a target="_blank" href="https://docs.makewebbetter.com/mwb-point-of-sale-for-woocommerce/?utm_source=MWB-POS-org&utm_medium=MWB-org-backend &utm_campaign=MWB-POS-doc"><img src="' . esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/Documentation.svg' ) . '" style="width: 20px;padding-right: 5px;">' . esc_html__( 'Documentation', 'mwb-point-of-sale-woocommerce' ) . '</a>',

				'support' => '<a target="_blank" href="https://makewebbetter.com/submit-query/?utm_source=MWB-POS-org&utm_medium=MWB-org-backend &utm_campaign=MWB-POS-support"><img src="' . esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/Support.svg' ) . '" style="width: 20px;padding-right: 5px;">' . esc_html__( 'Support', 'mwb-point-of-sale-woocommerce' ) . '</a>',

			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
} else {
	// WooCommerce is not active so deactivate this plugin.
	add_action( 'admin_init', 'mwb_pos_activation_failure' );

	/**
	 * Deactivate this plugin.
	 */
	function mwb_pos_activation_failure() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	add_action( 'admin_notices', 'mwb_pos_activation_failure_admin_notice' );

	/**
	 * This function is used to display admin error notice when WooCommerce is not active.
	 */
	function mwb_pos_activation_failure_admin_notice() {

		// to hide Plugin activated notice.
		unset( $_GET['activate'] );
		?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'WooCommerce is not activated, Please activate WooCommerce first to activate POS For WooCommerce.', 'mwb-point-of-sale-woocommerce' ); ?></p>
			</div>
		<?php
	}
}
