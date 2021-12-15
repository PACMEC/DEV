<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class VI_WOO_PRODUCT_VARIATIONS_SWATCHES_Admin_Admin {
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );

		add_filter(
			'plugin_action_links_product-variations-swatches-for-woocommerce/product-variations-swatches-for-woocommerce.php', array(
				$this,
				'settings_link'
			)
		);
	}

	public function settings_link( $links ) {
		$settings_link = sprintf( '<a href="%s?page=woocommerce-product-variations-swatches" title="%s">%s</a>', esc_url( admin_url( 'admin.php' ) ),
			esc_attr__( 'Settings', 'product-variations-swatches-for-woocommerce' ),
			esc_html__( 'Settings', 'product-variations-swatches-for-woocommerce' )
		);
		array_unshift( $links, $settings_link );

		return $links;
	}

	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'product-variations-swatches-for-woocommerce' );
		load_textdomain( 'product-variations-swatches-for-woocommerce', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_LANGUAGES . "product-variations-swatches-for-woocommerce-$locale.mo" );
		load_plugin_textdomain( 'product-variations-swatches-for-woocommerce', false, VI_WOO_PRODUCT_VARIATIONS_SWATCHES_LANGUAGES );

	}

	public function init() {
		$this->load_plugin_textdomain();
		if ( class_exists( 'VillaTheme_Support' ) ) {
			new VillaTheme_Support(
				array(
					'support'   => 'https://wordpress.org/support/plugin/product-variations-swatches-for-woocommerce/',
					'docs'      => 'http://docs.villatheme.com/?item=woo-product-variations-swatches',
					'review'    => 'https://wordpress.org/support/plugin/product-variations-swatches-for-woocommerce/reviews/?rate=5#rate-response',
					'pro_url'   => 'https://1.envato.market/bd0ek',
					'css'       => VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS,
					'image'     => VI_WOO_PRODUCT_VARIATIONS_SWATCHES_IMAGES,
					'slug'      => 'product-variations-swatches-for-woocommerce',
					'menu_slug' => 'woocommerce-product-variations-swatches',
					'version'   => VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION
				)
			);
		}
	}
}