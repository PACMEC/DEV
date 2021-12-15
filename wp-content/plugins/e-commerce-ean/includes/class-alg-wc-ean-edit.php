<?php
/**
 * EAN for WooCommerce - Edit Class
 *
 * @version 2.4.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_EAN_Edit' ) ) :

class Alg_WC_EAN_Edit {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {
		if ( is_admin() ) {
			// Admin product edit page
			add_action( get_option( 'alg_wc_ean_backend_position', 'woocommerce_product_options_sku' ), array( $this, 'add_ean_input' ) );
			add_action( 'save_post_product',                                                            array( $this, 'save_ean_input' ), 10, 2 );
			// Variations
			add_action( 'woocommerce_variation_options_pricing', array( $this, 'add_ean_input_variation' ), 10, 3 );
			add_action( 'woocommerce_save_product_variation',    array( $this, 'save_ean_input_variation' ), 10, 2 );
			// Quick and Bulk edit
			add_action( 'woocommerce_product_quick_edit_end',      array( $this, 'add_bulk_and_quick_edit_fields' ), PHP_INT_MAX );
			add_action( 'woocommerce_product_bulk_edit_end',       array( $this, 'add_bulk_and_quick_edit_fields' ), PHP_INT_MAX );
			add_action( 'woocommerce_product_bulk_and_quick_edit', array( $this, 'save_bulk_and_quick_edit_fields' ), PHP_INT_MAX, 2 );
		}
	}

	/**
	 * add_bulk_and_quick_edit_fields.
	 *
	 * @version 2.2.7
	 * @since   1.5.0
	 *
	 * @todo    [maybe] reposition this (e.g. right after the "SKU" field)?
	 * @todo    [maybe] actual value (instead of "No change" placeholder)? (probably need to add value to `woocommerce_inline_`) (quick edit only?)
	 */
	function add_bulk_and_quick_edit_fields() {
		echo ( 'woocommerce_product_quick_edit_end' === current_filter() ? '<br class="clear" />' : '' ) .
			'<label>' .
				'<span class="title">' . esc_html( get_option( 'alg_wc_ean_title', __( 'EAN', 'ean-for-woocommerce' ) ) ) . '</span>' .
				'<span class="input-text-wrap">' .
					'<input type="text" name="_alg_ean_qb" class="text" placeholder="' . __( '- No change -', 'ean-for-woocommerce' ) . '" value="">' .
				'</span>' .
			'</label>';
	}

	/**
	 * save_bulk_and_quick_edit_fields.
	 *
	 * @version 2.1.0
	 * @since   1.5.0
	 */
	function save_bulk_and_quick_edit_fields( $post_id, $post ) {
		// If this is an autosave, our form has not been submitted, so we don't want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// Don't save revisions and autosaves
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || 'product' !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		// Check nonce
		if ( ! isset( $_REQUEST['woocommerce_quick_edit_nonce'] ) || ! wp_verify_nonce( $_REQUEST['woocommerce_quick_edit_nonce'], 'woocommerce_quick_edit_nonce' ) ) { // WPCS: input var ok, sanitization ok.
			return $post_id;
		}
		// Save
		if ( isset( $_REQUEST['_alg_ean_qb'] ) && '' !== $_REQUEST['_alg_ean_qb'] ) {
			update_post_meta( $post_id, alg_wc_ean()->core->ean_key, wc_clean( $_REQUEST['_alg_ean_qb'] ) );
		}
		return $post_id;
	}

	/**
	 * get_ean_input_desc.
	 *
	 * @version 2.4.0
	 * @since   1.0.1
	 */
	function get_ean_input_desc( $ean, $product_id = false ) {
		return ( alg_wc_ean()->core->is_valid_ean( $ean, $product_id ) ?
			'<span style="color:green;">' . esc_html__( 'Valid EAN', 'ean-for-woocommerce' )   . '</span>' :
			'<span style="color:red;">'   . esc_html__( 'Invalid EAN', 'ean-for-woocommerce' ) . '</span>' );
	}

	/**
	 * get_ean_input_pattern.
	 *
	 * @version 2.4.0
	 * @since   1.0.1
	 *
	 * @todo    [next] `AUTO`: better maxlength (13); add minlength (8)
	 * @todo    [maybe] `ean-13`: `array( 'pattern' => '.{0}|[0-9]{13}', 'maxlength' => '13' ) )`
	 * @todo    [maybe] `ean-13`: `array( 'pattern' => '.{0}|[0-9]+', 'minlength' => '13', 'maxlength' => '13' )`
	 */
	function get_ean_input_pattern( $product_id = false, $atts = array() ) {
		$type = alg_wc_ean()->core->get_type( false, false, $product_id );
		switch ( $type ) {
			case 'EAN8':
			case 'UPCA':
			case 'EAN13':
			case 'AUTO':
				$result = array_merge( $atts, array( 'pattern' => '[0-9]+', 'maxlength' => ( 'AUTO' === $type ? 13 : alg_wc_ean()->core->get_ean_type_length( $type ) ) ) );
				break;
			default:
				$result = $atts;
		}
		return apply_filters( 'alg_wc_ean_input_pattern', $result, $atts, $type );
	}

	/**
	 * add_ean_input_variation.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 */
	function add_ean_input_variation( $loop, $variation_data, $variation ) {
		$key = alg_wc_ean()->core->ean_key;
		woocommerce_wp_text_input( array(
			'id'                => "variable{$key}_{$loop}",
			'name'              => "variable{$key}[{$loop}]",
			'value'             => ( isset( $variation_data[ $key ][0] ) ? $variation_data[ $key ][0] : '' ),
			'label'             => esc_html( get_option( 'alg_wc_ean_title', __( 'EAN', 'ean-for-woocommerce' ) ) ),
			'wrapper_class'     => 'form-row form-row-full',
			'placeholder'       => alg_wc_ean()->core->get_ean( $variation->post_parent ),
			'description'       => ( ! empty( $variation_data[ $key ][0] ) ? $this->get_ean_input_desc( $variation_data[ $key ][0], $variation->ID ) : '' ),
			'custom_attributes' => $this->get_ean_input_pattern( $variation->ID ),
		) );
	}

	/**
	 * save_ean_input_variation.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function save_ean_input_variation( $variation_id, $i ) {
		$key = alg_wc_ean()->core->ean_key;
		if ( isset( $_POST[ 'variable' . $key ][ $i ] ) ) {
			update_post_meta( $variation_id, $key, wc_clean( $_POST[ 'variable' . $key ][ $i ] ) );
		}
	}

	/**
	 * add_ean_input.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 */
	function add_ean_input() {
		$product_id = get_the_ID();
		$value      = alg_wc_ean()->core->get_ean( $product_id );
		woocommerce_wp_text_input( array(
			'id'                => alg_wc_ean()->core->ean_key,
			'value'             => $value,
			'label'             => esc_html( get_option( 'alg_wc_ean_title', __( 'EAN', 'ean-for-woocommerce' ) ) ),
			'description'       => ( ! empty( $value ) ? $this->get_ean_input_desc( $value, $product_id ) : '' ),
			'custom_attributes' => $this->get_ean_input_pattern( $product_id ),
		) );
	}

	/**
	 * save_ean_input.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    [maybe] save `$key . '_is_valid'` (same in `save_ean_input_variation()`)
	 */
	function save_ean_input( $post_id, $__post ) {
		if ( isset( $_POST[ alg_wc_ean()->core->ean_key ] ) && empty( $_REQUEST['woocommerce_quick_edit'] ) ) {
			update_post_meta( $post_id, alg_wc_ean()->core->ean_key, wc_clean( $_POST[ alg_wc_ean()->core->ean_key ] ) );
		}
	}

}

endif;

return new Alg_WC_EAN_Edit();
