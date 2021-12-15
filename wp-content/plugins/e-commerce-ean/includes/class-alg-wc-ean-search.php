<?php
/**
 * EAN for WooCommerce - Search Class
 *
 * @version 2.4.2
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_EAN_Search' ) ) :

class Alg_WC_EAN_Search {

	/**
	 * Constructor.
	 *
	 * @version 2.1.0
	 * @since   2.0.0
	 *
	 * @todo    [next] [!] (dev) Flatsome to `class-alg-wc-ean-compatibility.php`?
	 * @todo    [next] (dev) remove `! is_admin()` and `is_admin()`?
	 * @todo    [next] (dev) `alg_wc_ean_frontend_search_ajax_flatsome`: better solution?
	 * @todo    [later] search by EAN in "New Order"?
	 * @todo    [maybe] make `alg_wc_ean_backend_search_ajax` independent from `alg_wc_ean_backend_search`?
	 * @todo    [maybe] make `alg_wc_ean_frontend_search_ajax_flatsome` independent from `alg_wc_ean_frontend_search`?
	 */
	function __construct() {
		if ( ! is_admin() ) {
			// Frontend
			if ( 'yes' === get_option( 'alg_wc_ean_frontend_search', 'yes' ) ) {
				add_action( 'pre_get_posts', array( $this, 'search' ), 10 );
			}
		} else {
			// Backend
			if ( 'yes' === get_option( 'alg_wc_ean_backend_search', 'yes' ) ) {
				add_action( 'pre_get_posts', array( $this, 'search_backend' ) );
				if ( 'yes' === get_option( 'alg_wc_ean_backend_search_ajax', 'yes' ) ) {
					add_filter( 'woocommerce_json_search_found_products', array( $this, 'json_search_found_products' ) );
				}
			}
		}
		// "Flatsome" theme
		if ( 'yes' === get_option( 'alg_wc_ean_frontend_search', 'yes' ) && 'yes' === get_option( 'alg_wc_ean_frontend_search_ajax_flatsome', 'no' ) ) {
			add_filter( 'theme_mod_search_by_sku',       array( $this, 'flatsome_search_ajax_mod' ),  PHP_INT_MAX );
			add_filter( 'flatsome_ajax_search_function', array( $this, 'flatsome_search_ajax_func' ), PHP_INT_MAX, 4 );
		}
	}

	/**
	 * flatsome_search_ajax_mod.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function flatsome_search_ajax_mod( $value ) {
		$this->flatsome_theme_mod_search_by_sku = $value;
		return 1;
	}

	/**
	 * flatsome_search_ajax.
	 *
	 * @version 2.4.2
	 * @since   2.1.0
	 */
	function flatsome_search_ajax( $search_query, $args, $defaults ) {
		if ( ! $this->flatsome_theme_mod_search_by_sku ) {
			$args['meta_query'][0]['key']     = alg_wc_ean()->core->ean_key;
			$args['meta_query'][0]['compare'] = 'LIKE';
		} else {
			$args['meta_query'][] = array(
				'key'     => alg_wc_ean()->core->ean_key,
				'value'   => $args['meta_query'][0]['value'],
				'compare' => 'LIKE',
			);
			$args['meta_query']['relation'] = 'OR';
		}
		return get_posts( $args );
	}

	/**
	 * flatsome_search_ajax_func.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function flatsome_search_ajax_func( $func, $search_query, $args, $defaults ) {
		return ( ! empty( $args['meta_query'][0]['key'] ) && '_sku' === $args['meta_query'][0]['key'] ? 'alg_wc_ean_flatsome_search_ajax' : $func );
	}

	/**
	 * search_backend.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    [maybe] rewrite?
	 */
	function search_backend( $query ) {
		if ( $query->is_main_query() && isset( $query->query['post_type'] ) && 'product' == $query->query['post_type'] ) {
			$new_query   = clone( $query );
			$search_term = isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';
			if ( empty( $search_term ) ) {
				return;
			}

			$new_query->query_vars['s'] = '';
			$old_product_in = $query->query_vars['post__in'];

			unset( $new_query->query['post__in'] );
			unset( $new_query->query_vars['post__in'] );

			$new_meta_query = array(
				'key'     => alg_wc_ean()->core->ean_key,
				'value'   => $search_term,
				'compare' => 'LIKE'
			);
			$old_meta_query = ( isset( $query->query_vars['meta_query'] ) ? $query->query_vars['meta_query'] : false );
			if ( ! empty( $old_meta_query ) ) {
				$meta_query = $old_meta_query;
				array_push( $meta_query, array( 'relation' => 'OR' ) );
				array_push( $meta_query, $new_meta_query );
			} else {
				$meta_query = array( $new_meta_query );
			}
			$new_query->set( 'meta_query', $meta_query );
			$new_query->set( 'fields', 'ids' );

			remove_action( 'pre_get_posts', array( $this, 'search_backend' ) );

			// Search for products
			$result  = get_posts( $new_query->query_vars );
			$new_ids = $old_product_in;
			if ( $result ) {
				$new_ids = array_merge( $new_ids, $result );
			}

			// Search for variation
			$new_query->set( 'post_type', 'product_variation' );
			$new_query->set( 'fields', 'id=>parent' );
			$result = get_posts( $new_query->query_vars );
			if ( $result ) {
				$new_ids = array_merge( $new_ids, $result );
			}

			$query->set( 'post__in', $new_ids );
		}
	}

	/**
	 * json_search_found_products.
	 *
	 * @version 1.0.2
	 * @since   1.0.2
	 *
	 * @todo    [maybe] customizable `meta_compare` (can be e.g. `=`)
	 * @todo    [maybe] append product_id to the title
	 */
	function json_search_found_products( $products ) {
		if ( isset( $_REQUEST['term'] ) && '' !== $_REQUEST['term'] ) {
			$key = alg_wc_ean()->core->ean_key;
			$found_products = wc_get_products( array(
				'type'         => array_merge( array_keys( wc_get_product_types() ), array( 'variation' ) ),
				'limit'        => -1,
				'meta_key'     => $key,
				'meta_value'   => wc_clean( $_REQUEST['term'] ),
				'meta_compare' => 'LIKE',
				'return'       => 'ids',
			) );
			foreach ( $found_products as $product_id ) {
				$ean = sprintf( __( 'EAN: %s', 'ean-for-woocommerce' ), get_post_meta( $product_id, $key, true ) );
				$products[ $product_id ] = get_the_title( $product_id ) . ' (' . $ean . ')';
			}
		}
		return $products;
	}

	/**
	 * search.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 *
	 * @todo    [maybe] (dev) rewrite?
	 */
	function search( $wp_query ) {
		global $wpdb;
		if ( ! isset( $wp_query->query['s'] ) || ! isset( $wp_query->query['post_type'] ) || 'product' != $wp_query->query['post_type'] ) {
			return;
		}
		$key   = alg_wc_ean()->core->ean_key;
		$posts = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='{$key}' AND meta_value LIKE %s;",
			esc_sql( '%' . $wp_query->query['s'] . '%' ) ) );
		if ( ! $posts ) {
			return;
		}
		unset( $wp_query->query['s'] );
		unset( $wp_query->query_vars['s'] );
		$wp_query->query['post__in'] = array();
		foreach ( $posts as $id ) {
			if ( ( $post = get_post( $id ) ) ) {
				if ( $post->post_type == 'product_variation' ) {
					$wp_query->query['post__in'][]      = $post->post_parent;
					$wp_query->query_vars['post__in'][] = $post->post_parent;
				} else {
					$wp_query->query_vars['post__in'][] = $post->ID;
				}
			}
		}
	}

}

endif;

if ( ! function_exists( 'alg_wc_ean_flatsome_search_ajax' ) ) {
	/**
	 * alg_wc_ean_flatsome_search_ajax.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function alg_wc_ean_flatsome_search_ajax( $search_query, $args, $defaults ) {
		return alg_wc_ean()->core->search->flatsome_search_ajax( $search_query, $args, $defaults );
	}
}

return new Alg_WC_EAN_Search();
