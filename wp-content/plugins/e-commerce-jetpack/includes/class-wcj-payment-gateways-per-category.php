<?php
/**
 * Booster for WooCommerce - Module - Gateways per Product or Category
 *
 * @version 4.6.0
 * @since   2.2.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Payment_Gateways_Per_Category' ) ) :

class WCJ_Payment_Gateways_Per_Category extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 4.1.0
	 * @todo    [dev] (maybe) add `$this->do_use_variations_and_variable`
	 * @todo    [dev] (maybe) `add_filter( 'woocommerce_payment_gateways_settings',  array( $this, 'add_per_category_settings' ), 100 );`
	 */
	function __construct() {

		$this->id         = 'payment_gateways_per_category';
		$this->short_desc = __( 'Gateways per Product or Category', 'e-commerce-jetpack' );
		$this->desc       = __( 'Show payment gateway only if there is selected product or product category in cart.', 'e-commerce-jetpack' );
		$this->link_slug  = 'woocommerce-payment-gateways-per-product-or-category';
		parent::__construct();

		if ( $this->is_enabled() ) {
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'filter_available_payment_gateways_per_category' ), 100 );
			$this->do_use_variations = ( 'yes' === wcj_get_option( 'wcj_gateways_per_category_use_variations', 'no' ) );
		}

	}

	/**
	 * is_gateway_allowed.
	 *
	 * @version 4.6.0
	 * @since   4.6.0
	 *
	 * @param $gateway_id
	 * @param array $products
	 *
	 * @return bool
	 */
	function is_gateway_allowed( $gateway_id, $products = array() ) {
		// Including by categories
		$categories_in = wcj_get_option( 'wcj_gateways_per_category_' . $gateway_id );
		if ( ! empty( $categories_in ) ) {
			$current_check = false;
			foreach ( $products as $product_id ) {
				$product_categories = get_the_terms( $product_id, 'product_cat' );
				if ( empty( $product_categories ) ) {
					continue; // ... to next product
				}
				foreach ( $product_categories as $product_category ) {
					if ( in_array( $product_category->term_id, $categories_in ) ) {
						$current_check = true;
						break;
					}
				}
			}
			if ( ! $current_check ) {
				return false;
			}
		}

		// Excluding by categories
		$categories_excl = wcj_get_option( 'wcj_gateways_per_category_excl_' . $gateway_id );
		if ( ! empty( $categories_excl ) ) {
			$current_check = true;
			foreach ( $products as $product_id ) {
				$product_categories = get_the_terms( $product_id, 'product_cat' );
				if ( empty( $product_categories ) ) {
					continue; // ... to next product
				}
				foreach ( $product_categories as $product_category ) {
					if ( in_array( $product_category->term_id, $categories_excl ) ) {
						$current_check = false;
						break;
					}
				}
			}
			if ( ! $current_check ) {
				return false;
			}
		}

		// Including by products
		$products_in = wcj_maybe_convert_string_to_array( apply_filters( 'booster_option', array(), wcj_get_option( 'wcj_gateways_per_products_' . $gateway_id ) ) );
		if ( ! empty( $products_in ) ) {
			$current_check = false;
			foreach ( $products as $product_id ) {
				if ( in_array( $product_id, $products_in ) ) {
					// Current gateway is OK
					$current_check = true;
					break;
				}
			}
			if ( ! $current_check ) {
				return false;
			}
		}

		// Excluding by products
		$products_excl = wcj_maybe_convert_string_to_array( apply_filters( 'booster_option', array(), wcj_get_option( 'wcj_gateways_per_products_excl_' . $gateway_id ) ) );
		if ( ! empty( $products_excl ) ) {
			$current_check = true;
			foreach ( $products as $product_id ) {
				if ( in_array( $product_id, $products_excl ) ) {
					$current_check = false;
					break;
				}
			}
			if ( ! $current_check ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * filter_available_payment_gateways_per_category.
	 *
	 * @version 4.6.0
	 * @todo    [dev] (maybe) `if ( ! is_checkout() ) { return $available_gateways; }`
	 */
	function filter_available_payment_gateways_per_category( $available_gateways ) {
		$cart_products  = array();
		$order_products = array();

		// Check if it is on Checkout Page
		if ( is_checkout() && ! is_wc_endpoint_url( 'order-pay' ) ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$cart_products[] = $product_id = ( $this->do_use_variations ? ( ! empty( $values['variation_id'] ) ? $values['variation_id'] : $values['product_id'] ) : $values['product_id'] );
			}

		// Check if it is on checkout/order-pay/xxx page
		} elseif ( is_wc_endpoint_url( 'order-pay' ) ) {
			$url_arr = preg_split( '/[\/\?]/', $_SERVER['REQUEST_URI'] );
			if ( in_array( 'order-pay', $url_arr ) ) {
				$order_pay_index = array_search( 'order-pay', $url_arr );
				$order_id        = intval( $url_arr[ $order_pay_index + 1 ] );
				$order           = wc_get_order( $order_id );
				foreach ( $order->get_items() as $item_id => $values ) {
					$order_products[] = $product_id = ( $this->do_use_variations ? ( ! empty( $values['variation_id'] ) ? $values['variation_id'] : $values['product_id'] ) : $values['product_id'] );
				}
			}
		}

		// Check gateways
		$unavailable_gateways = array();
		foreach ( $available_gateways as $gateway_id => $gateway ) {
			if ( ! empty( $cart_products ) ) {
				if ( ! $this->is_gateway_allowed( $gateway_id, $cart_products ) ) {
					$unavailable_gateways[] = $gateway_id;
				}
			} elseif ( ! empty( $order_products ) ) {
				if ( ! $this->is_gateway_allowed( $gateway_id, $order_products ) ) {
					$unavailable_gateways[] = $gateway_id;
				}
			}
		}

		// Remove invalid gateways
		if ( count( $unavailable_gateways ) > 0 ) {
			$available_gateways = array_diff_key( $available_gateways, array_flip( $unavailable_gateways ) );
		}

		return $available_gateways;
	}

}

endif;

return new WCJ_Payment_Gateways_Per_Category();
