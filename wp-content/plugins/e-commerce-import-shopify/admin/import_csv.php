<?php

/**
 * Class S2W_IMPORT_SHOPIFY_TO_WOOCOMMERCE_ADMIN_Import_Csv
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
ini_set( 'auto_detect_line_endings', true );

class IMPORT_SHOPIFY_TO_WOOCOMMERCE_ADMIN_Import_Csv {
	protected $settings;

	public function __construct() {
		$this->settings = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_instance();
		add_action( 'admin_menu', array( $this, 'add_menu' ), 24 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public static function set( $name, $set_name = false ) {
		return VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::set( $name, $set_name );
	}

	public function add_menu() {
		add_submenu_page(
			'import-shopify-to-woocommerce', __( 'Import CSV', 'import-shopify-to-woocommerce' ), __( 'Import CSV', 'import-shopify-to-woocommerce' ), 'manage_options', 'import-shopify-to-woocommerce-import-csv', array(
				$this,
				'import_csv_callback'
			)
		);
	}

	public function admin_enqueue_scripts() {
		global $pagenow;
		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
		if ( $pagenow === 'admin.php' && $page === 'import-shopify-to-woocommerce-import-csv' ) {
			global $wp_scripts;
			$scripts = $wp_scripts->registered;
			foreach ( $scripts as $k => $script ) {
				preg_match( '/select2/i', $k, $result );
				if ( count( array_filter( $result ) ) ) {
					unset( $wp_scripts->registered[ $k ] );
					wp_dequeue_script( $script->handle );
				}
				preg_match( '/bootstrap/i', $k, $result );
				if ( count( array_filter( $result ) ) ) {
					unset( $wp_scripts->registered[ $k ] );
					wp_dequeue_script( $script->handle );
				}
			}
			wp_enqueue_script( 'import-shopify-to-woocommerce-semantic-js-form', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'form.min.js', array( 'jquery' ) );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-css-form', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'form.min.css' );
			wp_enqueue_script( 'import-shopify-to-woocommerce-semantic-js-progress', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'progress.min.js', array( 'jquery' ) );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-css-progress', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'progress.min.css' );
			wp_enqueue_script( 'import-shopify-to-woocommerce-semantic-js-checkbox', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'checkbox.min.js', array( 'jquery' ) );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-css-checkbox', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'checkbox.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-css-input', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'input.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-css-table', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'table.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-css-segment', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'segment.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-css-label', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'label.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-css-menu', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'menu.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-css-button', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'button.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-css-dropdown', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'dropdown.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-transition-css', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'transition.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-message-css', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'message.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-semantic-icon-css', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'icon.min.css' );
			/*Color picker*/
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script(
				'iris', admin_url( 'js/iris.min.js' ), array(
				'jquery-ui-draggable',
				'jquery-ui-slider',
				'jquery-touch-punch'
			), false, 1 );
			wp_enqueue_style( 'import-shopify-to-woocommerce-transition-css', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'transition.min.css' );
			wp_enqueue_script( 'import-shopify-to-woocommerce-transition', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'transition.min.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
			wp_enqueue_script( 'import-shopify-to-woocommerce-dropdown', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'dropdown.min.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
		}
	}

	public function import_csv_callback() {
		?>
        <div class="wrap">
            <h2><?php esc_html_e( 'Import products from CSV', 'import-shopify-to-woocommerce' ) ?></h2>
            <div class="vi-ui segment">
                <div class="vi-ui negative message">
					<?php esc_html_e( 'This is a Premium feature.', 'import-shopify-to-woocommerce' ); ?>
                    <?php IMPORT_SHOPIFY_TO_WOOCOMMERCE::upgrade_button();?>
                </div>
            </div>
        </div>
		<?php
	}
}
