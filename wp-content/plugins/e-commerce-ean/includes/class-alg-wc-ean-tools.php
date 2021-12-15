<?php
/**
 * EAN for WooCommerce - Tools Class
 *
 * @version 2.7.0
 * @since   2.1.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_EAN_Tools' ) ) :

class Alg_WC_EAN_Tools {

	/**
	 * Constructor.
	 *
	 * @version 2.7.0
	 * @since   2.1.0
	 */
	function __construct() {
		// Products Tools
		add_action( 'alg_wc_ean_settings_saved', array( $this, 'products_delete' ) );
		add_action( 'alg_wc_ean_settings_saved', array( $this, 'products_generate' ) );
		add_action( 'wp_insert_post',            array( $this, 'product_generate_on_insert_post' ), 10, 3 );
		// "Products > Bulk actions"
		add_filter( 'bulk_actions-edit-product',        array( $this, 'add_product_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-edit-product', array( $this, 'handle_product_bulk_actions' ), 10, 3 );
		// Orders Tools
		add_action( 'alg_wc_ean_settings_saved', array( $this, 'orders_add' ) );
		add_action( 'alg_wc_ean_settings_saved', array( $this, 'orders_delete' ) );
	}

	/**
	 * handle_product_bulk_actions.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 *
	 * @todo    [now] (feature) all other actions, e.g. "Copy EAN from SKU", etc.
	 * @todo    [now] (dev) notices
	 * @todo    [now] (dev) merge with `products_generate()`?
	 */
	function handle_product_bulk_actions( $redirect_to, $action, $post_ids ) {
		if ( in_array( $action, array( 'alg_wc_ean_generate', 'alg_wc_ean_delete' ) ) ) {
			$data  = ( 'alg_wc_ean_generate' === $action ? $this->get_generate_data() : false );
			$count = 0;
			foreach ( $post_ids as $post_id ) {
				$variations  = array_keys( get_children( array( 'post_parent' => $post_id, 'posts_per_page' => -1, 'post_type' => 'product_variation' ), 'ARRAY_N' ) );
				$product_ids = array_merge( array( $post_id ), $variations );
				foreach ( $product_ids as $product_id ) {
					switch ( $action ) {
						case 'alg_wc_ean_generate':
							$result = (
									'' === get_post_meta( $product_id, alg_wc_ean()->core->ean_key, true ) &&
									'' !== ( $ean = $this->generate_ean( $product_id, $data ) ) &&
									update_post_meta( $product_id, alg_wc_ean()->core->ean_key, $ean )
								);
							break;
						case 'alg_wc_ean_delete':
							$result = delete_post_meta( $product_id, alg_wc_ean()->core->ean_key );
							break;
					}
					if ( $result ) {
						$count++;
					}
				}
			}
		}
		return $redirect_to;
	}

	/**
	 * add_product_bulk_actions.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 */
	function add_product_bulk_actions( $actions ) {
		return array_merge( $actions, array_intersect_key( array(
				'alg_wc_ean_generate' => __( 'Generate EAN', 'order-minimum-amount-for-woocommerce' ),
				'alg_wc_ean_delete'   => __( 'Delete EAN', 'order-minimum-amount-for-woocommerce' ),
			), array_flip( get_option( 'alg_wc_ean_product_bulk_actions', array( 'alg_wc_ean_delete', 'alg_wc_ean_generate' ) ) ) ) );
	}

	/**
	 * get_orders.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query
	 */
	function get_orders() {
		return wc_get_orders( array( 'limit' => -1, 'return' => 'ids' ) );
	}

	/**
	 * orders_delete.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function orders_delete() {
		if ( 'yes' === get_option( 'alg_wc_ean_tool_orders_delete', 'no' ) ) {
			update_option( 'alg_wc_ean_tool_orders_delete', 'no' );
			if ( current_user_can( 'manage_woocommerce' ) ) {
				$count = 0;
				foreach ( $this->get_orders() as $order_id ) {
					$order = wc_get_order( $order_id );
					if ( ! $order ) {
						continue;
					}
					foreach ( $order->get_items() as $item_id => $item ) {
						if ( wc_delete_order_item_meta( $item_id, alg_wc_ean()->core->ean_key ) ) {
							$count++;
						}
					}
				}
				if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
					WC_Admin_Settings::add_message( sprintf( __( 'EAN deleted for %s order items.', 'order-minimum-amount-for-woocommerce' ), $count ) );
				}
			}
		}
	}

	/**
	 * orders_add.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function orders_add() {
		if ( 'yes' === get_option( 'alg_wc_ean_tool_orders_add', 'no' ) ) {
			update_option( 'alg_wc_ean_tool_orders_add', 'no' );
			if ( current_user_can( 'manage_woocommerce' ) ) {
				$count = 0;
				foreach ( $this->get_orders() as $order_id ) {
					$count += alg_wc_ean()->core->orders->add_ean_to_order_items_meta( $order_id );
				}
				if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
					WC_Admin_Settings::add_message( sprintf( __( 'EAN added for %s order items.', 'order-minimum-amount-for-woocommerce' ), $count ) );
				}
			}
		}
	}

	/**
	 * get_products.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query
	 */
	function get_products() {
		return wc_get_products( array( 'limit' => -1, 'return' => 'ids', 'type' => array_merge( array_keys( wc_get_product_types() ), array( 'variation' ) ) ) );
	}

	/**
	 * products_delete.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 *
	 * @todo    [next] (dev) delete directly with SQL from the `meta` table
	 * @todo    [maybe] (dev) better notice(s)?
	 */
	function products_delete() {
		if ( 'yes' === get_option( 'alg_wc_ean_tool_delete_product_meta', 'no' ) ) {
			update_option( 'alg_wc_ean_tool_delete_product_meta', 'no' );
			if ( current_user_can( 'manage_woocommerce' ) ) {
				$count = 0;
				foreach ( $this->get_products() as $product_id ) {
					if ( delete_post_meta( $product_id, alg_wc_ean()->core->ean_key ) ) {
						$count++;
					}
				}
				if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
					WC_Admin_Settings::add_message( sprintf( __( 'EAN deleted for %s products.', 'order-minimum-amount-for-woocommerce' ), $count ) );
				}
			}
		}
	}

	/**
	 * product_generate_on_insert_post.
	 *
	 * @version 2.2.8
	 * @since   2.2.8
	 *
	 * @todo    [next] (feature) `product_variation`: make it optional?
	 * @todo    [next] (feature) copy ID?
	 */
	function product_generate_on_insert_post( $post_id, $post, $update ) {
		$options = array_replace( array( 'insert_product' => 'no', 'update_product' => 'no' ), get_option( 'alg_wc_ean_tool_product_generate_on', array() ) );
		if (
			( ( ! $update && 'yes' === $options['insert_product'] ) || ( $update && 'yes' === $options['update_product'] ) ) &&
			in_array( $post->post_type, array( 'product', 'product_variation' ) ) &&
			'' === get_post_meta( $post_id, alg_wc_ean()->core->ean_key, true )
		) {
			update_post_meta( $post_id, alg_wc_ean()->core->ean_key, $this->generate_ean( $post_id, $this->get_generate_data() ) );
		}
	}

	/**
	 * products_generate.
	 *
	 * @version 2.2.8
	 * @since   2.1.0
	 *
	 * @todo    [next] (feature) per individual product (JS or AJAX?)
	 * @todo    [maybe] (dev) better notice(s)?
	 */
	function products_generate() {
		$tools = array_replace( array( 'generate' => 'no', 'copy_sku' => 'no', 'copy_id' => 'no', 'copy_meta' => 'no' ), get_option( 'alg_wc_ean_tool_product', array() ) );
		if ( in_array( 'yes', $tools ) ) {
			delete_option( 'alg_wc_ean_tool_product' );
			if ( current_user_can( 'manage_woocommerce' ) ) {
				// Prepare (and validate) data
				$do_generate  = ( 'yes' === $tools['generate'] );
				$do_copy_sku  = ( 'yes' === $tools['copy_sku'] );
				$do_copy_id   = ( 'yes' === $tools['copy_id'] );
				$do_copy_meta = ( 'yes' === $tools['copy_meta'] );
				if ( $do_generate ) {
					$data = $this->get_generate_data();
				} elseif ( $do_copy_meta ) {
					$data = array_replace( array( 'key' => '' ), get_option( 'alg_wc_ean_tool_product_copy_meta', array() ) );
					if ( '' === $data['key'] ) {
						if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
							WC_Admin_Settings::add_message( __( 'Please set the "Meta key" option.', 'order-minimum-amount-for-woocommerce' ) );
						}
						return;
					}
				}
				// Product loop
				$count = 0;
				foreach ( $this->get_products() as $product_id ) {
					if ( '' !== get_post_meta( $product_id, alg_wc_ean()->core->ean_key, true ) ) {
						continue;
					}
					$ean = '';
					if ( $do_generate ) {
						$ean = $this->generate_ean( $product_id, $data );
					} elseif ( $do_copy_meta ) {
						$ean = get_post_meta( $product_id, $data['key'], true );
					} elseif ( $do_copy_sku ) {
						if ( ( $product = wc_get_product( $product_id ) ) ) {
							$ean = $product->get_sku();
						}
					} elseif ( $do_copy_id ) {
						$ean = $product_id;
					}
					if ( '' !== $ean && update_post_meta( $product_id, alg_wc_ean()->core->ean_key, $ean ) ) {
						$count++;
					}
				}
				// Notice
				if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
					$message = ( $do_generate ?
						__( 'EAN generated for %s products.', 'order-minimum-amount-for-woocommerce' ) :
						__( 'EAN copied for %s products.', 'order-minimum-amount-for-woocommerce' ) );
					WC_Admin_Settings::add_message( sprintf( $message, $count ) );
				}
			}
		}
	}

	/**
	 * get_generate_data.
	 *
	 * @version 2.7.0
	 * @since   2.2.8
	 */
	function get_generate_data() {
		$res = array();
		$data = array_replace( array( 'type' => 'EAN13', 'prefix' => 200, 'prefix_to' => '', 'seed_prefix' => '' ), get_option( 'alg_wc_ean_tool_product_generate', array() ) );
		// Seed length
		switch ( $data['type'] ) {
			case 'EAN8':
				$length = 8;
				break;
			case 'UPCA':
				$length = 12;
				break;
			default: // 'EAN13'
				$length = 13;
		}
		$res['seed_length'] = ( $length - 3 - 1 );
		// Seed prefix
		$seed_prefix         = ( strlen( $data['seed_prefix'] ) > $res['seed_length'] ? substr( $data['seed_prefix'], 0, $res['seed_length'] ) : $data['seed_prefix'] );
		$res['seed_length'] -= strlen( $seed_prefix );
		$res['seed_prefix']  = $seed_prefix;
		// Prefix
		if ( '' === $data['prefix'] ) {
			$data['prefix'] = 0;
		}
		$res['is_rand_prefix'] = ( '' !== $data['prefix_to'] && $data['prefix'] != $data['prefix_to'] );
		$res['prefix'] = ( $res['is_rand_prefix'] ?
			array(
				'from' => ( $data['prefix_to'] > $data['prefix'] ? $data['prefix'] : $data['prefix_to'] ),
				'to'   => ( $data['prefix_to'] > $data['prefix'] ? $data['prefix_to'] : $data['prefix'] ),
			) :
			str_pad( $data['prefix'], 3, '0', STR_PAD_LEFT )
		);
		return $res;
	}

	/**
	 * generate_ean.
	 *
	 * @version 2.7.0
	 * @since   2.2.8
	 */
	function generate_ean( $product_id, $data ) {
		$ean = ( $data['is_rand_prefix'] ? str_pad( rand( $data['prefix']['from'], $data['prefix']['to'] ), 3, '0', STR_PAD_LEFT ) : $data['prefix'] ) .
			$data['seed_prefix'] . str_pad( substr( $product_id, 0, $data['seed_length'] ), $data['seed_length'], '0', STR_PAD_LEFT );
		return $ean . $this->get_checksum( $ean );
	}

	/**
	 * get_checksum.
	 *
	 * @version 2.2.7
	 * @since   2.2.5
	 *
	 * @see     https://stackoverflow.com/questions/19890144/generate-valid-ean13-in-php
	 *
	 * @todo    [next] (feature) customizable seed (i.e. not product ID), e.g. random?; etc.
	 */
	function get_checksum( $code ) {
		$flag = true;
		$sum  = 0;
		for ( $i = strlen( $code ) - 1; $i >= 0; $i-- ) {
			$sum += (int) $code[ $i ] * ( $flag ? 3 : 1 );
			$flag = ! $flag;
		}
		return ( 10 - ( $sum % 10 ) ) % 10;
	}

}

endif;

return new Alg_WC_EAN_Tools();
