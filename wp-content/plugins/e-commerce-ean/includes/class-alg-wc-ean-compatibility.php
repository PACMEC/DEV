<?php
/**
 * EAN for WooCommerce - Compatibility Class
 *
 * @version 2.6.0
 * @since   2.2.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_EAN_Compatibility' ) ) :

class Alg_WC_EAN_Compatibility {

	/**
	 * Constructor.
	 *
	 * @version 2.6.0
	 * @since   2.2.0
	 *
	 * @todo    [next] [!] (dev) "Point of Sale for WooCommerce": add `( 'yes' === get_option( 'alg_wc_ean_wc_pos', 'yes' ) )` / "This will add EAN field to the "Register > Scanning Fields" option of the %s plugin." / Point of Sale for WooCommerce / https://woocommerce.com/products/point-of-sale-for-woocommerce/
	 * @todo    [next] (feature) WCFM: customizable position, i.e. instead of right below the "SKU" field in "Inventory" tab
	 * @todo    [next] (feature) Dokan: customizable position, i.e. instead of `dokan_new_product_after_product_tags` and `dokan_product_edit_after_product_tags`
	 * @todo    [maybe] (feature) https://wordpress.org/plugins/woocommerce-xml-csv-product-import/ (WooCommerce add-on for "WP All Import")
	 */
	function __construct() {
		// "Point of Sale for WooCommerce" plugin
		add_filter( 'wc_pos_scanning_fields', array( $this, 'wc_pos_scanning_fields' ), PHP_INT_MAX );
		// "Dokan – Best WooCommerce Multivendor Marketplace Solution – Build Your Own Amazon, eBay, Etsy" plugin
		if ( 'yes' === get_option( 'alg_wc_ean_dokan', 'no' ) ) {
			add_action( 'dokan_new_product_after_product_tags',  array( $this, 'dokan_add_ean_field' ) );
			add_action( 'dokan_product_edit_after_product_tags', array( $this, 'dokan_add_ean_field' ), 10, 2 );
			add_action( 'dokan_new_product_added',               array( $this, 'dokan_save_ean_field' ), 10, 2 );
			add_action( 'dokan_product_updated',                 array( $this, 'dokan_save_ean_field' ), 10, 2 );
		}
		// WCFM
		if ( 'yes' === get_option( 'alg_wc_ean_wcfm', 'no' ) ) {
			add_filter( 'wcfm_product_fields_stock',            array( $this, 'wcfm_add_ean_field' ), 10, 3 );
			add_action( 'after_wcfm_products_manage_meta_save', array( $this, 'wcfm_save_ean_field' ), 10, 2 );
		}
		// "Print Invoice & Delivery Notes for WooCommerce" plugin
		if ( 'yes' === get_option( 'alg_wc_ean_wcdn', 'no' ) ) {
			add_action( 'wcdn_order_item_after', array( $this, 'add_to_wcdn_ean' ), 10, 3 );
		}
		// "WooCommerce PDF Invoices & Packing Slips" plugin
		if ( 'yes' === get_option( 'alg_wc_ean_wpo_wcpdf', 'no' ) ) {
			add_action( 'wpo_wcpdf_after_item_meta', array( $this, 'add_to_wpo_wcpdf_ean' ), 10, 3 );
		}
	}

	/**
	 * add_to_wcdn_ean.
	 *
	 * @version 2.6.0
	 * @since   1.4.0
	 *
	 * @todo    [next] (feature) customizable wrapper
	 * @todo    [next] (dev) check if valid?
	 */
	function add_to_wcdn_ean( $product, $order, $item ) {
		if ( false !== ( $ean = alg_wc_ean()->core->get_ean_from_order_item( $item ) ) ) {
			echo '<small class="ean_wrapper">' . esc_html( get_option( 'alg_wc_ean_title', __( 'EAN', 'ean-for-woocommerce' ) ) ) . ' ' .
					'<span class="ean">' . $ean . '</span>' .
				'</small>';
		}
	}

	/**
	 * add_to_wpo_wcpdf_ean.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 *
	 * @todo    [next] (feature) customizable position, e.g. `wpo_wcpdf_before_item_meta`?
	 * @todo    [next] (feature) customizable template?
	 * @todo    [next] (dev) check if valid?
	 */
	function add_to_wpo_wcpdf_ean( $type, $item, $order ) {
		if ( ! empty( $item['item_id'] ) && ( $item = new WC_Order_Item_Product( $item['item_id'] ) ) && false !== ( $ean = alg_wc_ean()->core->get_ean_from_order_item( $item ) ) ) {
			echo '<dl class="meta">' .
					'<dt class="ean">' . esc_html( get_option( 'alg_wc_ean_title', __( 'EAN', 'ean-for-woocommerce' ) ) ) . ':' . '</dt>' .
					'<dd class="ean">' . $ean . '</dd>' .
				'</dl>';
		}
	}

	/**
	 * wcfm_save_ean_field.
	 *
	 * @version 2.2.2
	 * @since   2.2.2
	 *
	 * @see     https://plugins.svn.wordpress.org/wc-frontend-manager/tags/6.5.10/controllers/products-manager/wcfm-controller-products-manage.php
	 */
	function wcfm_save_ean_field( $new_product_id, $wcfm_products_manage_form_data ) {
		$id = 'wcfm_' . alg_wc_ean()->core->ean_key;
		if ( isset( $wcfm_products_manage_form_data[ $id ] ) ) {
			update_post_meta( $new_product_id, alg_wc_ean()->core->ean_key, wc_clean( $wcfm_products_manage_form_data[ $id ] ) );
		}
	}

	/**
	 * wcfm_add_ean_field.
	 *
	 * @version 2.2.2
	 * @since   2.2.2
	 *
	 * @see     https://plugins.svn.wordpress.org/wc-frontend-manager/tags/6.5.10/views/products-manager/wcfm-view-products-manage-tabs.php
	 *
	 * @todo    [next] [!] (dev) do we need `esc_html` everywhere, e.g. in `hints`? (same for `dokan_add_ean_field()`)
	 * @todo    [next] (dev) variable products?
	 * @todo    [next] (feature) optional EAN validation
	 */
	function wcfm_add_ean_field( $fields, $product_id, $product_type ) {
		$_fields  = array();
		$is_added = false;
		$_key     = 'wcfm_' . alg_wc_ean()->core->ean_key;
		$_field   = array(
			'label'       => esc_html( get_option( 'alg_wc_ean_wcfm_title', __( 'EAN', 'ean-for-woocommerce' ) ) ),
			'type'        => 'text',
			'class'       => 'wcfm-text',
			'label_class' => 'wcfm_title',
			'value'       => alg_wc_ean()->core->get_ean( $product_id ),
			'hints'       => esc_html( get_option( 'alg_wc_ean_wcfm_hints', __( 'The International Article Number (also known as European Article Number or EAN) is a standard describing a barcode symbology and numbering system used in global trade to identify a specific retail product type, in a specific packaging configuration, from a specific manufacturer.', 'ean-for-woocommerce' ) ) ),
			'placeholder' => esc_html( get_option( 'alg_wc_ean_wcfm_placeholder', __( 'Product EAN...', 'ean-for-woocommerce' ) ) ),
		);
		foreach ( $fields as $key => $field ) {
			$_fields[ $key ] = $field;
			if ( 'sku' === $key ) {
				$_fields[ $_key ] = $_field;
				$is_added         = true;
			}
		}
		if ( ! $is_added ) {
			$_fields[ $_key ] = $_field; // fallback
		}
		return $_fields;
	}

	/**
	 * dokan_save_ean_field.
	 *
	 * @version 2.2.2
	 * @since   2.2.2
	 *
	 * @see     https://github.com/weDevsOfficial/dokan/blob/v3.2.8/includes/Dashboard/Templates/Products.php#L353
	 * @see     https://github.com/weDevsOfficial/dokan/blob/v3.2.8/includes/Dashboard/Templates/Products.php#L482
	 * @see     https://github.com/weDevsOfficial/dokan/blob/v3.2.8/includes/Product/functions.php#L127
	 * @see     https://github.com/weDevsOfficial/dokan/blob/v3.2.8/includes/Product/functions.php#L129
	 * @see     https://github.com/weDevsOfficial/dokan/blob/v3.2.8/includes/REST/Manager.php#L172
	 */
	function dokan_save_ean_field( $product_id, $data ) {
		$id = 'dokan_' . alg_wc_ean()->core->ean_key;
		if ( isset( $data[ $id ] ) ) {
			update_post_meta( $product_id, alg_wc_ean()->core->ean_key, wc_clean( $data[ $id ] ) );
		}
	}

	/**
	 * dokan_add_ean_field.
	 *
	 * @version 2.2.2
	 * @since   2.2.2
	 *
	 * @see     https://github.com/weDevsOfficial/dokan/blob/v3.2.8/templates/products/new-product.php#L257
	 * @see     https://github.com/weDevsOfficial/dokan/blob/v3.2.8/templates/products/tmpl-add-product-popup.php#L148
	 * @see     https://github.com/weDevsOfficial/dokan/blob/v3.2.8/templates/products/new-product-single.php#L338
	 *
	 * @todo    [next] (dev) variable products?
	 * @todo    [next] (feature) optional EAN validation
	 */
	function dokan_add_ean_field( $post = false, $post_id = false ) {
		$id          = 'dokan_' . alg_wc_ean()->core->ean_key;
		$value       = ( ! empty( $post_id ) ? alg_wc_ean()->core->get_ean( $post_id ) : ( isset( $_REQUEST[ $id ] ) ? esc_html( wc_clean( $_REQUEST[ $id ] ) ) : '' ) ); // Edit product vs Add product
		$title       = esc_html( get_option( 'alg_wc_ean_dokan_title', __( 'EAN', 'ean-for-woocommerce' ) ) );
		$placeholder = esc_html( get_option( 'alg_wc_ean_dokan_placeholder', __( 'Product EAN...', 'ean-for-woocommerce' ) ) );
		echo '<div class="dokan-form-group">' .
			'<label for="' . $id . '" class="form-label">' . $title . '</label>' .
			'<input type="text" name="' . $id . '" id="' . $id . '" class="dokan-form-control alg-wc-ean" placeholder="' . $placeholder . '" value="' . $value . '">' .
		'</div>';
	}

	/**
	 * wc_pos_scanning_fields.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 *
	 * @see     https://www.woocommerce.com/products/woocommerce-point-of-sale/
	 */
	function wc_pos_scanning_fields( $fields ) {
		$fields[ alg_wc_ean()->core->ean_key ] = __( 'EAN', 'ean-for-woocommerce' );
		return $fields;
	}

}

endif;

return new Alg_WC_EAN_Compatibility();
