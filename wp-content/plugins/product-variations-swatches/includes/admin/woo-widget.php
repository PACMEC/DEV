<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class VI_WOO_PRODUCT_VARIATIONS_SWATCHES_Admin_Woo_Widget {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 30 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), PHP_INT_MAX );
	}
	public function admin_menu() {
		add_submenu_page(
			'woocommerce-product-variations-swatches',
			esc_html__( 'Swatches Settings for WooCommerce Filter Widget', 'product-variations-swatches-for-woocommerce' ),
			esc_html__( 'Woo Filter Widget', 'product-variations-swatches-for-woocommerce' ),
			'manage_options',
			'woocommerce-product-variations-swatches-woo-widget',
			array( $this, 'settings_callback' )
		);
	}
	public function settings_callback(){
		?>
		<div class="wrap">
			<h2 class=""><?php esc_html_e( 'Swatches Settings for WooCommerce Filter Widget', 'product-variations-swatches-for-woocommerce' ) ?></h2>
			<div class="vi-ui raised">
				<form class="vi-ui form" method="post">
					<div class="vi-ui vi-ui-main top tabular attached menu">
						<a class="item active" data-tab="general"><?php esc_html_e( 'General Settings', 'product-variations-swatches-for-woocommerce' ); ?></a>
					</div>
					<div class="vi-ui bottom attached tab segment active" data-tab="general">
						<div class="vi-ui blue message">
							<?php esc_html_e( 'Settings the Swatches for \'Filter Products by Attribute\' WooCommerce: ', 'product-variations-swatches-for-woocommerce' ); ?>
							<ul class="list">
								<li><?php esc_html_e( 'Change  \'Display type\' on Widget settings to  \'List\' to use the below settings', 'product-variations-swatches-for-woocommerce' ); ?></li>
								<li><?php esc_html_e( 'For special taxonomy, please go to \'Global Attributes\' to set display type', 'product-variations-swatches-for-woocommerce' ); ?></li>
							</ul>
						</div>
						<table class="form-table">
							<tr>
								<th>
									<label for="vi-wpvs-woo_widget_enable-checkbox"><?php esc_html_e( 'Enable', 'product-variations-swatches-for-woocommerce' ); ?></label>
								</th>
								<td>
									<a class="vi-ui button" href="https://1.envato.market/bd0ek"
									   target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
								</td>
							</tr>
							<tr>
								<th>
									<label><?php esc_html_e( 'Preview', 'product-variations-swatches-for-woocommerce' ); ?></label>
								</th>
								<td>
									<a  href="https://1.envato.market/bd0ek"
									   target="_blank">
										<img class="aligncenter wp-image-72516 size-full lazyloaded"
										     src="<?php echo esc_url(VI_WOO_PRODUCT_VARIATIONS_SWATCHES_IMAGES.'woo-widget.gif');?>"
										     data-src="<?php ?>"
										     alt="WooCommerce Product Variation Swatches - WooCommerce Filter Widgets" width="616" height="275">
									</a>
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
	public function admin_enqueue_scripts(){
		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';
		if ( $page === 'woocommerce-product-variations-swatches-woo-widget' ) {
			wp_enqueue_style( 'semantic-ui-button', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'button.min.css', array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-form', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'form.min.css', array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-label', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'label.min.css', array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-menu', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'menu.min.css', array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-message', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'message.min.css', array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-segment', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'segment.min.css',array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-tab', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'tab.css', array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
		}
	}
}