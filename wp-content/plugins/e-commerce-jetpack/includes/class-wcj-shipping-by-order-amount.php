<?php
/**
 * Booster for WooCommerce - Module - Shipping Methods by Min/Max Order Amount
 *
 * @version 5.2.0
 * @since   3.2.1
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Shipping_By_Order_Amount' ) ) :

class WCJ_Shipping_By_Order_Amount extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 5.2.0
	 * @since   3.2.1
	 * @todo    (maybe) add customer messages on cart and checkout pages (if some shipping method is not available)
	 */
	function __construct() {

		$this->id         = 'shipping_by_order_amount';
		$this->short_desc = __( 'Shipping Methods by Min/Max Order Amount', 'e-commerce-jetpack' );
		$this->desc       = __( 'Set minimum and/or maximum order amount for shipping methods to show up (Local pickup available in Plus).', 'e-commerce-jetpack' );
		$this->desc_pro   = __( 'Set minimum and/or maximum order amount for shipping methods to show up.', 'e-commerce-jetpack' );
		$this->link_slug  = 'woocommerce-shipping-methods-by-min-max-order-amount';
		parent::__construct();

		if ( $this->is_enabled() ) {
			$this->use_shipping_instances = ( 'yes' === wcj_get_option( 'wcj_shipping_by_order_amount_use_shipping_instance', 'no' ) );
			add_filter( 'woocommerce_package_rates', array( $this, 'available_shipping_methods' ), PHP_INT_MAX, 2 );
		}
	}

	/**
	 * available_shipping_methods.
	 *
	 * @version 3.5.0
	 * @since   3.2.1
	 * @todo    [dev] currency conversion
	 * @todo    apply_filters( 'booster_option' )
	 * @todo    (maybe) add option to include or exclude taxes when calculating cart total
	 */
	function available_shipping_methods( $rates, $package ) {
		if ( ! isset( WC()->cart ) || WC()->cart->is_empty() ) {
			return $rates;
		}
		$total_in_cart = WC()->cart->cart_contents_total;
		foreach ( $rates as $rate_key => $rate ) {
			$min = ( $this->use_shipping_instances ?
				get_option( 'wcj_shipping_by_order_amount_min_instance_' . $rate->instance_id, 0 ) : wcj_get_option( 'wcj_shipping_by_order_amount_min_' . $rate->method_id, 0 ) );
			$max = ( $this->use_shipping_instances ?
				get_option( 'wcj_shipping_by_order_amount_max_instance_' . $rate->instance_id, 0 ) : wcj_get_option( 'wcj_shipping_by_order_amount_max_' . $rate->method_id, 0 ) );
			if ( 0 != $min && $total_in_cart < $min ) {
				unset( $rates[ $rate_key ] );
			} elseif ( 0 != $max && $total_in_cart > $max ) {
				unset( $rates[ $rate_key ] );
			}
		}
		return $rates;
	}

}

endif;

return new WCJ_Shipping_By_Order_Amount();
