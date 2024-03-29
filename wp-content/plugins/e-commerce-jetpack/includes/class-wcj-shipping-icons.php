<?php
/**
 * Booster for WooCommerce - Module - Shipping Icons
 *
 * @version 5.2.0
 * @since   3.4.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Shipping_Icons' ) ) :

class WCJ_Shipping_Icons extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 5.2.0
	 * @since   3.4.0
	 */
	function __construct() {

		$this->id         = 'shipping_icons';
		$this->short_desc = __( 'Shipping Icons', 'e-commerce-jetpack' );
		$this->desc       = __( 'Add icons to shipping methods on frontend. Icon Visibility (Plus)', 'e-commerce-jetpack' );
		$this->desc_pro   = __( 'Add icons to shipping methods on frontend.', 'e-commerce-jetpack' );
		$this->link_slug  = 'woocommerce-shipping-icons';
		parent::__construct();

		if ( $this->is_enabled() ) {
			add_filter( 'woocommerce_cart_shipping_method_full_label', array( $this, 'shipping_icon' ), PHP_INT_MAX, 2 );
		}
	}

	/**
	 * shipping_icon.
	 *
	 * @version 3.6.0
	 * @since   2.5.6
	 */
	function shipping_icon( $label, $method ) {
		$shipping_icons_visibility = apply_filters( 'booster_option', 'both', wcj_get_option( 'wcj_shipping_icons_visibility', 'both' ) );
		if ( 'checkout_only' === $shipping_icons_visibility && is_cart() ) {
			return $label;
		}
		if ( 'cart_only' === $shipping_icons_visibility && is_checkout() ) {
			return $label;
		}
		$use_shipping_instances = ( 'yes' === wcj_get_option( 'wcj_shipping_icons_use_shipping_instance', 'no' ) );
		$option_id              = 'wcj_shipping_icon_' . ( $use_shipping_instances ? 'instance_' . $method->instance_id : $method->method_id );
		if ( '' != ( $icon_url = wcj_get_option( $option_id, '' ) ) ) {
			$style_html = ( '' != ( $style = wcj_get_option( 'wcj_shipping_icons_style', 'display:inline;' ) ) ) ?  'style="' . $style . '" ' : '';
			$img = '<img ' . $style_html . 'class="wcj_shipping_icon" id="' . $option_id . '" src="' . $icon_url . '">';
			$label = ( 'before' === wcj_get_option( 'wcj_shipping_icons_position', 'before' ) ) ? $img . ' ' . $label : $label . ' ' . $img;
		}
		return $label;
	}

}

endif;

return new WCJ_Shipping_Icons();
