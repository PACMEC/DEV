<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @author MakeWebBetter <webmaster@makewebbetter.com>
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/admin
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Pos_For_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param    string $hook      The plugin page slug.
	 */
	public function mwb_pos_admin_enqueue_styles( $hook ) {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'makewebbetter_page_pos_for_woocommerce_menu' === $screen->id ) {

			wp_enqueue_style( 'mwb-pfw-select2-css', POS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/pos-for-woocommerce-select2.css', array(), time(), 'all' );

			wp_enqueue_style( 'mwb-pfw-meterial-css', POS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-pfw-meterial-css2', POS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-pfw-meterial-lite', POS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.css', array(), time(), 'all' );

			wp_enqueue_style( 'mwb-pfw-meterial-icons-css', POS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/icon.css', array(), time(), 'all' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( $this->plugin_name . '-admin-global', POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/scss/pos-for-woocommerce-admin-global.css', array( 'mwb-pfw-meterial-icons-css' ), time(), 'all' );
		}

		wp_enqueue_style( $this->plugin_name, POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/scss/pos-for-woocommerce-admin.css', array(), time(), 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param    string $hook      The plugin page slug.
	 */
	public function mwb_pos_admin_enqueue_scripts( $hook ) {

		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'makewebbetter_page_pos_for_woocommerce_menu' === $screen->id ) {
			wp_enqueue_script( 'mwb-pfw-select2', POS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/pos-for-woocommerce-select2.js', array( 'jquery' ), time(), false );

			wp_enqueue_script( 'mwb-pfw-metarial-js', POS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-pfw-metarial-js2', POS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-pfw-metarial-lite', POS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.js', array(), time(), false );

			wp_enqueue_media();
			wp_enqueue_script( 'wp-color-picker' );

			wp_register_script( $this->plugin_name . 'admin-js', POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/pos-for-woocommerce-admin.js', array( 'jquery', 'mwb-pfw-select2', 'mwb-pfw-metarial-js', 'mwb-pfw-metarial-js2', 'mwb-pfw-metarial-lite', 'wp-color-picker' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name . 'admin-js',
				'pfw_admin_param',
				array(
					'ajaxurl'            => admin_url( 'admin-ajax.php' ),
					'reloadurl'          => admin_url( 'admin.php?page=pos_for_woocommerce_menu' ),
					'pfw_gen_tab_enable' => get_option( 'pfw_radio_switch_demo' ),
					'mwb_pfw_nonce'      => wp_create_nonce( 'pfw-security' ),
				)
			);

			wp_enqueue_script( $this->plugin_name . 'admin-js' );
		}
	}

	/**
	 * Adding settings menu for POS for Woocommerce.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_options_page() {
		global $submenu;
		if ( empty( $GLOBALS['admin_page_hooks']['mwb-plugins'] ) ) {
			add_menu_page( 'MakeWebBetter', 'MakeWebBetter', 'manage_options', 'mwb-plugins', array( $this, 'mwb_pos_plugins_listing_page' ), POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/mwb-logo.png', 15 );
			$pfw_menus = apply_filters( 'mwb_add_plugins_menus_array', array() );
			if ( is_array( $pfw_menus ) && ! empty( $pfw_menus ) ) {
				foreach ( $pfw_menus as $pfw_key => $pfw_value ) {
					add_submenu_page( 'mwb-plugins', $pfw_value['name'], $pfw_value['name'], 'manage_options', $pfw_value['menu_link'], array( $pfw_value['instance'], $pfw_value['function'] ) );
				}
			}
		}

		add_submenu_page( 'woocommerce', 'pos orders', 'pos orders', 'manage_options', 'mwb-pos-orders', array( $this, 'mwb_pos_orders_listing' ) );
	}

	/**
	 * Removing default submenu of parent menu in backend dashboard
	 *
	 * @since   1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_orders_listing() {
		?>
		<div class="mwb-pos-order-listing-container">
			<form method="POST">
				<h2><?php esc_html_e( 'POS Orders', 'mwb-point-of-sale-woocommerce' ); ?></h2>
				<input type="submit" class="button" name="mwb_pos_show_orders" id="mwb-pos-show-orders" value="<?php esc_attr_e( 'Show deleted orders', 'mwb-point-of-sale-woocommerce' ); ?>" />
				<?php wp_nonce_field( 'mwb_pfw__order_show', '_mwb_pfw_nonce', true, true ); ?>
				<?php
				include_once POS_FOR_WOOCOMMERCE_DIR_PATH . 'admin/class-pos-for-woocommerce-pos-orders.php';
				$mwb_pos_orders_obj = new Pos_For_Woocommerce_Pos_Orders();
				$mwb_pos_orders_obj->prepare_items();
				$mwb_pos_orders_obj->display();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Show all hidden orders.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_show_hidden_orders() {
		if ( ! isset( $_POST['_mwb_pfw_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_mwb_pfw_nonce'] ) ), 'mwb_pfw__order_show' ) ) {
			return;
		}
		if ( isset( $_POST['mwb_pos_show_orders'] ) ) {
			$orders = wc_get_orders( array( 'numberposts' => -1 ) );
			if ( is_array( $orders ) && ! empty( $orders ) ) {
				foreach ( $orders as $order ) {
					$order_id               = $order->get_id();
					$mwb_order_check_status = get_post_meta( $order_id, 'mwb_pos_order_hide_from_table', true );
					if ( ! isset( $mwb_order_check_status ) || ( 'yes' === $mwb_order_check_status || '' === $mwb_order_check_status ) ) {
						update_post_meta( $order_id, 'mwb_pos_order_hide_from_table', 'no' );
					}
				}
			}
		}
	}

	/**
	 * Remove WooCommerce default taxes for pos orders.
	 *
	 * @param    bool $tax_status  tax_status.
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @return boolean $tax_status return tax status.
	 */
	public function mwb_pos_wc_change_tax_class( $tax_status ) {
		global $post;
		if ( isset( $post->ID ) && 'shop_order' === $post->post_type ) {
			$order               = wc_get_order( $post->ID );
			$order_id            = $order->get_id();
			$check_for_pos_order = get_post_meta( $order_id, 'mwb_pos_order', true );
			if ( 'yes' === $check_for_pos_order ) {
				$tax_status = false;
				return $tax_status;
			} else {
				return $tax_status;
			}
		}
		return $tax_status;
	}

	/**
	 * Removing default submenu of parent menu in backend dashboard.
	 *
	 * @since   1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_remove_default_submenu() {
		global $submenu;
		if ( is_array( $submenu ) && array_key_exists( 'mwb-plugins', $submenu ) ) {
			if ( isset( $submenu['mwb-plugins'][0] ) ) {
				unset( $submenu['mwb-plugins'][0] );
			}
		}
	}


	/**
	 * POS for Woocommerce mwb_pos_admin_submenu_page.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param array $menus Marketplace menus.
	 */
	public function mwb_pos_admin_submenu_page( $menus = array() ) {
		$menus[] = array(
			'name'      => __( 'POS for WooCommerce', 'mwb-point-of-sale-woocommerce' ),
			'slug'      => 'pos_for_woocommerce_menu',
			'menu_link' => 'pos_for_woocommerce_menu',
			'instance'  => $this,
			'function'  => 'mwb_pos_options_menu_html',
		);
		return $menus;
	}

	/**
	 * Add tab for settings page.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param  array $mwb_pfw_default_tabs  mwb_pfw_default_tabs.
	 * @return array $mwb_pfw_default_tabs.
	 */
	public function mwb_pos_add_tabs( $mwb_pfw_default_tabs ) {
		$mwb_pfw_default_tabs['pos-for-woocommerce-login'] = array(
			'title' => esc_html__( 'Login Panel', 'mwb-point-of-sale-woocommerce' ),
			'name'  => 'pos-for-woocommerce-login',
		);
		return $mwb_pfw_default_tabs;
	}

	/**
	 * Add page template node for the pos panel.
	 *
	 * @param object $wp_admin_bar include template for the pos panel.
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_add_pos_panel_link( $wp_admin_bar ) {
		$mwb_pos_page_link = '';
		$mwb_pos_page      = get_option( 'mwb_pos_page_exists', false );
		if ( isset( $mwb_pos_page ) && '' !== $mwb_pos_page ) {
			$mwb_pos_page_link = get_permalink( $mwb_pos_page );
		}
		$args = array(
			'id'    => 'mwbposview',
			'title' => __( 'View POS', 'mwb-point-of-sale-woocommerce' ),
			'href'  => $mwb_pos_page_link,
			'meta'  => array(
				'class' => 'mwbposview',
				'title' => __( 'View POS Panel', 'mwb-point-of-sale-woocommerce' ),
			),
		);
		$wp_admin_bar->add_node( $args );
	}


	/**
	 * POS for Woocommerce mwb_pos_plugins_listing_page.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_plugins_listing_page() {
		$active_marketplaces = apply_filters( 'mwb_add_plugins_menus_array', array() );
		if ( is_array( $active_marketplaces ) && ! empty( $active_marketplaces ) ) {
			require POS_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/welcome.php';
		}
	}

	/**
	 * POS for Woocommerce admin menu page.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_options_menu_html() {

		include_once POS_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/pos-for-woocommerce-admin-dashboard.php';
	}


	/**
	 * POS for Woocommerce admin menu page.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param array $pfw_settings_general Settings fields.
	 */
	public function mwb_pos_admin_general_settings_page( $pfw_settings_general ) {
		$pfw_settings_general = array(
			array(
				'title'       => __( 'POS Header Text', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Add text for your pos header on the frontend.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_header_text',
				'value'       => get_option( 'mwb_pfw_header_text', '' ),
				'class'       => 'pfw-text-class',
				'placeholder' => __( 'Header text', 'mwb-point-of-sale-woocommerce' ),
			),
			array(
				'title'       => __( 'POS Header Color', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Choose a color for your pos header on the frontend.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_header_color',
				'value'       => get_option( 'mwb_pfw_header_color', '' ),
				'class'       => 'pfw-text-class mwb-pfw-colorpicker',
				'placeholder' => __( 'Header color', 'mwb-point-of-sale-woocommerce' ),
			),
			array(
				'title'       => __( 'POS Header Logo', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'imageupload',
				'description' => __( 'Choose a logo for your pos header on the frontend.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_header_logo',
				'value'       => get_option( 'mwb_pfw_header_logo', '' ),
				'class'       => 'pfw-text-class',
				'placeholder' => __( 'Header logo', 'mwb-point-of-sale-woocommerce' ),
				'style'       => 'height: 80px!important',
			),
			array(
				'title'       => __( 'POS Footer Text Color', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Choose the text color for your pos footer on the frontend.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_footer_text_color',
				'value'       => get_option( 'mwb_pfw_footer_text_color', '' ),
				'class'       => 'pfw-text-class mwb-pfw-colorpicker',
				'placeholder' => __( 'Footer Text Color', 'mwb-point-of-sale-woocommerce' ),
			),
			array(
				'title'       => __( 'POS Footer Background Color', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Choose a background color for your pos footer on the frontend.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_footer_color',
				'value'       => get_option( 'mwb_pfw_footer_color', '' ),
				'class'       => 'pfw-text-class mwb-pfw-colorpicker',
				'placeholder' => __( 'Footer Background color', 'mwb-point-of-sale-woocommerce' ),
			),
			array(
				'type'        => 'button',
				'id'          => 'mwb_pfw_save_gnrl_settings',
				'button_text' => __( 'Save Settings', 'mwb-point-of-sale-woocommerce' ),
				'class'       => 'pfw-button-class',
			),
		);
		return $pfw_settings_general;
	}

	/**
	 * POS for WooCommerce login page tabs.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param array $mwb_pfw_login Login panel array.
	 * @return array $mwb_pfw_login
	 */
	public function mwb_pos_admin_login_user_settings_page( $mwb_pfw_login ) {
		$mwb_pfw_login = array(
			array(
				'title'       => __( 'POS Login Panel Title', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Add title for your pos login page on frontend.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_login_heading',
				'value'       => get_option( 'mwb_pfw_login_heading', '' ),
				'class'       => 'pfw-text-class',
				'placeholder' => __( 'Login Page Title', 'mwb-point-of-sale-woocommerce' ),
			),
			array(
				'title'       => __( 'POS Login Panel Sub Title', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Add subheading for your pos login page on frontend.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_login_subtitle',
				'value'       => get_option( 'mwb_pfw_login_subtitle', '' ),
				'class'       => 'pfw-text-class',
				'placeholder' => __( 'Login Page Subtitle', 'mwb-point-of-sale-woocommerce' ),
			),
			array(
				'title'       => __( 'POS Login Panel Description', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( 'Add description for your pos login page on frontend.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_login_descp',
				'value'       => get_option( 'mwb_pfw_login_descp', '' ),
				'class'       => 'pfw-text-class',
				'placeholder' => __( 'Description', 'mwb-point-of-sale-woocommerce' ),
			),
			array(
				'title'       => __( 'Background Color', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Choose color for your pos login page background.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_login_bg',
				'value'       => get_option( 'mwb_pfw_login_bg', '' ),
				'class'       => 'pfw-text-class mwb-pfw-colorpicker',
				'placeholder' => __( 'Background color', 'mwb-point-of-sale-woocommerce' ),
			),
			array(
				'title'       => __( 'Button Color', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Choose the color to change pos login page button color.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_login_button_bg',
				'value'       => get_option( 'mwb_pfw_login_button_bg', '' ),
				'class'       => 'pfw-text-class mwb-pfw-colorpicker',
				'placeholder' => __( 'Button Color', 'mwb-point-of-sale-woocommerce' ),
			),
			array(
				'title'       => __( 'Button Text Color', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Choose the color to change pos login page button text color.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_login_button_txt_bg',
				'value'       => get_option( 'mwb_pfw_login_button_txt_bg', '' ),
				'class'       => 'pfw-text-class mwb-pfw-colorpicker',
				'placeholder' => __( 'Button Text Color', 'mwb-point-of-sale-woocommerce' ),
			),
			array(
				'title'       => __( 'POS Logo Image', 'mwb-point-of-sale-woocommerce' ),
				'type'        => 'imageupload',
				'description' => __( 'Choose a logo for the pos login page.', 'mwb-point-of-sale-woocommerce' ),
				'id'          => 'mwb_pfw_login_logo',
				'value'       => get_option( 'mwb_pfw_login_logo', '' ),
				'class'       => 'pfw-text-class',
				'placeholder' => __( 'Logo Image', 'mwb-point-of-sale-woocommerce' ),
				'style'       => 'height: 80px!important',
			),
			array(
				'type'        => 'button',
				'id'          => 'mwb_pfw_save_login_settings',
				'button_text' => __( 'Save Settings', 'mwb-point-of-sale-woocommerce' ),
				'class'       => 'pfw-button-class',
			),
		);
		return $mwb_pfw_login;
	}

	/**
	 * POS for Woocommerce support page tabs.
	 *
	 * @since    1.0.0
	 * @param    array $mwb_pfw_support Settings fields.
	 * @return   array  $mwb_pfw_support
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_admin_support_settings_page( $mwb_pfw_support ) {
		$mwb_pfw_support = array(
			array(
				'title'       => __( 'User Guide', 'mwb-point-of-sale-woocommerce' ),
				'description' => __( 'View the detailed guides and documentation to set up your plugin.', 'mwb-point-of-sale-woocommerce' ),
				'link-text'   => __( 'VIEW', 'mwb-point-of-sale-woocommerce' ),
				'link'        => 'https://docs.makewebbetter.com/mwb-point-of-sale-for-woocommerce/?utm_source=MWB-POS-org&utm_medium=MWB-org-backend &utm_campaign=MWB-POS-doc',
			),
			array(
				'title'       => __( 'Free Support', 'mwb-point-of-sale-woocommerce' ),
				'description' => __( 'Please submit a ticket, our team will respond within 24 hours.', 'mwb-point-of-sale-woocommerce' ),
				'link-text'   => __( 'SUBMIT', 'mwb-point-of-sale-woocommerce' ),
				'link'        => 'https://makewebbetter.com/submit-query/?utm_source=MWB-POS-org&utm_medium=MWB-org-backend &utm_campaign=MWB-POS-support',
			),
		);

		return apply_filters( 'mwb_pfw_add_support_content', $mwb_pfw_support );
	}

	/**
	 * Generate product barcode settings.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param  array $mwb_pfw_barcode  mwb_pfw_barcode.
	 * @return array $mwb_pfw_barcode.
	 */
	public function mwb_pos_generate_products_barcode( $mwb_pfw_barcode ) {
		$mwb_pfw_barcode = array(
			array(
				'type'        => 'button',
				'id'          => 'mwb_pfw_barcode_generate',
				'button_text' => __( 'Generate Product Barcode', 'mwb-point-of-sale-woocommerce' ),
				'class'       => 'pfw-button-class',
			),
		);

		return $mwb_pfw_barcode;
	}

	/**
	 * Column for QR code in product listing area.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param  array $mwb_product_columns  mwb_product_columns.
	 * @return array $mwb_product_columns.
	 */
	public function mwb_pos_add_qr_code_column( $mwb_product_columns ) {
		$mwb_product_columns['mwb_pfw_qrcode'] = esc_html__( 'Product QR', 'mwb-point-of-sale-woocommerce' );
		return $mwb_product_columns;
	}

	/**
	 * Populate QR code for products.
	 *
	 * @since 1.0.0.
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param  string $mwb_pro_column  mwb_pro_column.
	 */
	public function mwb_pos_populate_qr_column( $mwb_pro_column ) {
		global $post;

		if ( isset( $mwb_pro_column ) && 'mwb_pfw_qrcode' === $mwb_pro_column ) {
			$_product = wc_get_product( $post->ID );
			if ( isset( $_product ) ) {
				$product_barcode_name = str_replace( ' ', '-', $_product->get_name() ) . '-' . $_product->get_id();
				$image_file_existance = POS_FOR_WOOCOMMERCE_DIR_PATH . 'package/lib/product-barcode/' . $product_barcode_name . '.png';
				if ( file_exists( POS_FOR_WOOCOMMERCE_DIR_PATH . 'package/lib/product-barcode/' . $product_barcode_name . '.png' ) ) {
					?>
					<img src="<?php echo esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/product-barcode/' . $product_barcode_name . '.png' ); ?>" />
					<?php
				} else {
					?>
					<img height="85px" src="<?php echo esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/missing-img.jpg' ); ?>" />
					<?php
				}
			}
		}
	}

	/**
	 * Ajax handling for barcode creation.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_generate_pro_barcode() {
		check_ajax_referer( 'pfw-security', 'security' );
		$get_current_user = get_current_user_id();
		require POS_FOR_WOOCOMMERCE_DIR_PATH . 'package/lib/php-barcode-master/barcode.php';
		$path         = POS_FOR_WOOCOMMERCE_DIR_PATH . 'package/lib/product-barcode/';
		$products_arg = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
		);
		$all_products = $this->mwb_pos_get_all_products( $products_arg );

		if ( is_array( $all_products ) && ! empty( $all_products ) ) {
			foreach ( $all_products as $all_product_key => $all_product ) {
				$mwb_barcode_name = str_replace( ' ', '-', $all_product ) . '-' . $all_product_key;
				barcode( $path . $mwb_barcode_name . '.png', $mwb_barcode_name, 20, 'horizontal', 'code39', true, 1 );
			}
			echo 'success';
		}
		wp_die();
	}

	/**
	 * Get all woocommerce products.
	 *
	 * @since 1.0.0
	 * @param array $args args.
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_get_all_products( $args ) {
		if ( is_array( $args ) && ! empty( $args ) ) {
			$posts    = get_posts( $args );
			$_product = array();
			if ( is_array( $posts ) && ! empty( $posts ) ) {
				foreach ( $posts as $post ) {
					$product = wc_get_product( $post->ID );
					if ( 'simple' === $product->get_type() ) {
						$_product[ $product->get_id() ] = $product->get_name();
					} elseif ( 'variable' === $product->get_type() ) {
						$_product[ $product->get_id() ] = $product->get_name();
					}
				}
				return $_product;
			}
		}
	}


	/**
	 * POS for Woocommerce save tab settings.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_admin_save_general_settings() {
		global $pfw_mwb_pfw_obj;
		global $mwb_pfw_notices;
		if ( isset( $_POST['mwb_pfw_save_gnrl_settings'] ) && isset( $_POST['mwb-pfw-general-nonce-field'] ) ) {
			$mwb_pfw_general_nonce = sanitize_text_field( wp_unslash( $_POST['mwb-pfw-general-nonce-field'] ) );
			if ( wp_verify_nonce( $mwb_pfw_general_nonce, 'mwb-pfw-general-nonce' ) ) {

				$mwb_pfw_gen_flag     = false;
				$pfw_genaral_settings = apply_filters( 'pfw_general_settings_array', array() );

				$pfw_button_index = array_search( 'submit', array_column( $pfw_genaral_settings, 'type' ), true );

				if ( ! $pfw_button_index ) {
					$pfw_button_index = array_search( 'button', array_column( $pfw_genaral_settings, 'type' ), true );
				}
				if ( isset( $pfw_button_index ) && '' !== $pfw_button_index ) {
					unset( $pfw_genaral_settings[ $pfw_button_index ] );
					if ( is_array( $pfw_genaral_settings ) && ! empty( $pfw_genaral_settings ) ) {
						foreach ( $pfw_genaral_settings as $pfw_genaral_setting ) {
							if ( isset( $pfw_genaral_setting['id'] ) && '' !== $pfw_genaral_setting['id'] ) {
								if ( isset( $_POST[ $pfw_genaral_setting['id'] ] ) ) {//phpcs:disable
									$posted_value = sanitize_text_field( wp_unslash( $_POST[ $pfw_genaral_setting['id'] ] ) );
									update_option( $pfw_genaral_setting['id'], $posted_value );
								} else {
									update_option( $pfw_genaral_setting['id'], '' );
								}
							} else {
								$mwb_pfw_gen_flag = true;
							}
						}
						$this->mwb_pos_saved_settings_data( POS_FOR_WOOCOMMERCE_DIR_PATH . 'components/settings-data/general-settings.json', $pfw_genaral_settings );
					}
					if ( $mwb_pfw_gen_flag ) {
						$mwb_pfw_error_text = esc_html__( 'Id of some field is missing', 'mwb-point-of-sale-woocommerce' );
						$pfw_mwb_pfw_obj->mwb_pos_plug_admin_notice( $mwb_pfw_error_text, 'error' );
					} else {
						$mwb_pfw_notices = true;
					}
				}
			}
		}

		/**
		* POS for Woocommerce Save login page settings.
		*
		* @since 1.0.0
		* @author MakeWebBetter <webmaster@makewebbetter.com>
		*/
		if ( isset( $_POST['mwb_pfw_save_login_settings'] ) && isset( $_POST['mwb-pfw-login-nonce-field'] ) ) {//phpcs:disable
			$mwb_pfw_login_nonce = sanitize_text_field( wp_unslash( $_POST['mwb-pfw-login-nonce-field'] ) );
			if ( wp_verify_nonce( $mwb_pfw_login_nonce, 'mwb-pfw-login-nonce' ) ) {

				$mwb_pfw_login_flag     = false;
				$pfw_login_settings     = apply_filters( 'pfw_login_settings_array', array() );
				$pfw_login_button_index = array_search( 'submit', array_column( $pfw_login_settings, 'type' ), true );
				if ( ! $pfw_login_button_index ) {
					$pfw_login_button_index = array_search( 'button', array_column( $pfw_login_settings, 'type' ), true );
				}
				if ( isset( $pfw_login_button_index ) && '' !== $pfw_login_button_index ) {
					unset( $pfw_login_settings[ $pfw_login_button_index ] );

					if ( is_array( $pfw_login_settings ) && ! empty( $pfw_login_settings ) ) {
						foreach ( $pfw_login_settings as $pfw_login_setting ) {
							if ( isset( $pfw_login_setting['id'] ) && '' !== $pfw_login_setting['id'] ) {
								if ( isset( $_POST[ $pfw_login_setting['id'] ] ) ) {//phpcs:disable
									$posted_value_login = sanitize_text_field( wp_unslash( $_POST[ $pfw_login_setting['id'] ] ) );
									update_option( $pfw_login_setting['id'], $posted_value_login );
								} else {
									update_option( $pfw_login_setting['id'], '' );
								}
							} else {
								$mwb_pfw_login_flag = true;
							}
						}
						$this->mwb_pos_saved_settings_data( POS_FOR_WOOCOMMERCE_DIR_PATH . 'components/settings-data/login-settings.json', $pfw_login_settings );
					}
					if ( $mwb_pfw_login_flag ) {
						$mwb_pfw_error_text = esc_html__( 'Id of some field is missing', 'mwb-point-of-sale-woocommerce' );
						$pfw_mwb_pfw_obj->mwb_pos_plug_admin_notice( $mwb_pfw_error_text, 'error' );
					} else {
						$mwb_pfw_notices = true;
					}
				}
			}
		}
	}

	/**
	 * Save customisable data in plugin file.
	 *
	 * @param string $mwb_pfw_file_path mwb_pfw_file_path.
	 * @param array  $mwb_pfw_settings_data mwb_pfw_settings_data.
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_saved_settings_data( $mwb_pfw_file_path, $mwb_pfw_settings_data ) {
		global $wp_filesystem;
		WP_Filesystem();
		$mwb_pfw_data = array();
		if ( is_array( $mwb_pfw_settings_data ) && ! empty( $mwb_pfw_settings_data ) ) {
			foreach ( $mwb_pfw_settings_data as $mwb_pfw_setting_data ) {
				$mwb_pfw_data[ $mwb_pfw_setting_data['id'] ] = get_option( $mwb_pfw_setting_data['id'] );
			}

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				if ( ( $current_user instanceof WP_User ) ) {
					$mwb_pfw_data['mwb_user_name']   = esc_html__( 'Welcome: ', 'mwb-point-of-sale-woocommerce' ) . esc_html( $current_user->display_name );
					$mwb_pfw_data['mwb_user_avater'] = get_avatar_url( $current_user->ID, 32 );
				}
			}

			$mwb_pfw_data = wp_json_encode( $mwb_pfw_data );
			$wp_filesystem->put_contents( $mwb_pfw_file_path, $mwb_pfw_data );//phpcs:disable
		}
	}
}
