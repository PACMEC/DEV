<?php
/**
 * Booster for WooCommerce Export Fields Helper
 *
 * @version 2.7.0
 * @since   2.5.9
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Export_Fields_Helper' ) ) :

class WCJ_Export_Fields_Helper {

	/**
	 * Constructor.
	 *
	 * @version 2.7.0
	 * @since   2.5.9
	 */
	function __construct() {
		return true;
	}

	/**
	 * get_customer_from_order_export_fields.
	 *
	 * @version 2.5.9
	 * @since   2.5.9
	 */
	function get_customer_from_order_export_fields() {
		return array(
			'customer-nr'                 => __( 'Customer Nr.', 'e-commerce-jetpack' ),
			'customer-billing-email'      => __( 'Billing Email', 'e-commerce-jetpack' ),
			'customer-billing-first-name' => __( 'Billing First Name', 'e-commerce-jetpack' ),
			'customer-billing-last-name'  => __( 'Billing Last Name', 'e-commerce-jetpack' ),
			'customer-billing-company'    => __( 'Billing Company', 'e-commerce-jetpack' ),
			'customer-billing-address-1'  => __( 'Billing Address 1', 'e-commerce-jetpack' ),
			'customer-billing-address-2'  => __( 'Billing Address 2', 'e-commerce-jetpack' ),
			'customer-billing-city'       => __( 'Billing City', 'e-commerce-jetpack' ),
			'customer-billing-state'      => __( 'Billing State', 'e-commerce-jetpack' ),
			'customer-billing-postcode'   => __( 'Billing Postcode', 'e-commerce-jetpack' ),
			'customer-billing-country'    => __( 'Billing Country', 'e-commerce-jetpack' ),
			'customer-billing-phone'      => __( 'Billing Phone', 'e-commerce-jetpack' ),
			'customer-last-order-date'    => __( 'Last Order Date', 'e-commerce-jetpack' ),
		);
	}

	/**
	 * get_customer_from_order_export_default_fields_ids.
	 *
	 * @version 2.5.9
	 * @since   2.5.9
	 */
	function get_customer_from_order_export_default_fields_ids() {
		return array(
			'customer-nr',
			'customer-billing-email',
			'customer-billing-first-name',
			'customer-billing-last-name',
			'customer-last-order-date',
		);
	}

	/**
	 * get_customer_export_fields.
	 *
	 * @version 2.5.9
	 * @since   2.5.9
	 */
	function get_customer_export_fields() {
		return array(
			'customer-id'           => __( 'Customer ID', 'e-commerce-jetpack' ),
			'customer-email'        => __( 'Email', 'e-commerce-jetpack' ),
			'customer-first-name'   => __( 'First Name', 'e-commerce-jetpack' ),
			'customer-last-name'    => __( 'Last Name', 'e-commerce-jetpack' ),
			'customer-login'        => __( 'Login', 'e-commerce-jetpack' ),
			'customer-nicename'     => __( 'Nicename', 'e-commerce-jetpack' ),
			'customer-url'          => __( 'URL', 'e-commerce-jetpack' ),
			'customer-registered'   => __( 'Registered', 'e-commerce-jetpack' ),
			'customer-display-name' => __( 'Display Name', 'e-commerce-jetpack' ),
//			'customer-debug'        => __( 'Debug', 'e-commerce-jetpack' ),
		);
	}

	/**
	 * get_customer_export_default_fields_ids.
	 *
	 * @version 2.5.9
	 * @since   2.5.9
	 */
	function get_customer_export_default_fields_ids() {
		return array(
			'customer-id',
			'customer-email',
			'customer-first-name',
			'customer-last-name',
		);
	}

	/**
	 * get_order_items_export_fields.
	 *
	 * @version 2.5.9
	 * @since   2.5.9
	 */
	function get_order_items_export_fields() {
		return array(
			'order-id'                    => __( 'Order ID', 'e-commerce-jetpack' ),
			'order-number'                => __( 'Order Number', 'e-commerce-jetpack' ),
			'order-status'                => __( 'Order Status', 'e-commerce-jetpack' ),
			'order-date'                  => __( 'Order Date', 'e-commerce-jetpack' ),
			'order-time'                  => __( 'Order Time', 'e-commerce-jetpack' ),
			'order-item-count'            => __( 'Order Item Count', 'e-commerce-jetpack' ),
			'order-currency'              => __( 'Order Currency', 'e-commerce-jetpack' ),
			'order-total'                 => __( 'Order Total', 'e-commerce-jetpack' ),
			'order-total-tax'             => __( 'Order Total Tax', 'e-commerce-jetpack' ),
			'order-payment-method'        => __( 'Order Payment Method', 'e-commerce-jetpack' ),
			'order-notes'                 => __( 'Order Notes', 'e-commerce-jetpack' ),
			'billing-first-name'          => __( 'Billing First Name', 'e-commerce-jetpack' ),
			'billing-last-name'           => __( 'Billing Last Name', 'e-commerce-jetpack' ),
			'billing-company'             => __( 'Billing Company', 'e-commerce-jetpack' ),
			'billing-address-1'           => __( 'Billing Address 1', 'e-commerce-jetpack' ),
			'billing-address-2'           => __( 'Billing Address 2', 'e-commerce-jetpack' ),
			'billing-city'                => __( 'Billing City', 'e-commerce-jetpack' ),
			'billing-state'               => __( 'Billing State', 'e-commerce-jetpack' ),
			'billing-postcode'            => __( 'Billing Postcode', 'e-commerce-jetpack' ),
			'billing-country'             => __( 'Billing Country', 'e-commerce-jetpack' ),
			'billing-phone'               => __( 'Billing Phone', 'e-commerce-jetpack' ),
			'billing-email'               => __( 'Billing Email', 'e-commerce-jetpack' ),
			'shipping-first-name'         => __( 'Shipping First Name', 'e-commerce-jetpack' ),
			'shipping-last-name'          => __( 'Shipping Last Name', 'e-commerce-jetpack' ),
			'shipping-company'            => __( 'Shipping Company', 'e-commerce-jetpack' ),
			'shipping-address-1'          => __( 'Shipping Address 1', 'e-commerce-jetpack' ),
			'shipping-address-2'          => __( 'Shipping Address 2', 'e-commerce-jetpack' ),
			'shipping-city'               => __( 'Shipping City', 'e-commerce-jetpack' ),
			'shipping-state'              => __( 'Shipping State', 'e-commerce-jetpack' ),
			'shipping-postcode'           => __( 'Shipping Postcode', 'e-commerce-jetpack' ),
			'shipping-country'            => __( 'Shipping Country', 'e-commerce-jetpack' ),

			'item-name'                   => __( 'Item Name', 'e-commerce-jetpack' ),
			'item-meta'                   => __( 'Item Meta', 'e-commerce-jetpack' ),
			'item-variation-meta'         => __( 'Item Variation Meta', 'e-commerce-jetpack' ),
			'item-qty'                    => __( 'Item Quantity', 'e-commerce-jetpack' ),
			'item-tax-class'              => __( 'Item Tax Class', 'e-commerce-jetpack' ),
			'item-product-id'             => __( 'Item Product ID', 'e-commerce-jetpack' ),
			'item-variation-id'           => __( 'Item Variation ID', 'e-commerce-jetpack' ),
			'item-line-subtotal'          => __( 'Item Line Subtotal', 'e-commerce-jetpack' ),
			'item-line-total'             => __( 'Item Line Total', 'e-commerce-jetpack' ),
			'item-line-subtotal-tax'      => __( 'Item Line Subtotal Tax', 'e-commerce-jetpack' ),
			'item-line-tax'               => __( 'Item Line Tax', 'e-commerce-jetpack' ),
			'item-line-subtotal-plus-tax' => __( 'Item Line Subtotal Plus Tax', 'e-commerce-jetpack' ),
			'item-line-total-plus-tax'    => __( 'Item Line Total Plus Tax', 'e-commerce-jetpack' ),
			'item-product-input-fields'   => __( 'Item Product Input Fields', 'e-commerce-jetpack' ),
//			'item-debug'                  => __( 'Item Debug', 'e-commerce-jetpack' ),
		);
	}

	/**
	 * get_order_items_export_default_fields_ids.
	 *
	 * @version 2.5.9
	 * @since   2.5.9
	 */
	function get_order_items_export_default_fields_ids() {
		return array(
			'order-number',
			'order-status',
			'order-date',
			'order-currency',
			'order-payment-method',
			'item-name',
			'item-variation-meta',
			'item-qty',
			'item-tax-class',
			'item-product-id',
			'item-variation-id',
			'item-line-total',
			'item-line-tax',
			'item-line-total-plus-tax',
		);
	}

	/**
	 * get_order_export_fields.
	 *
	 * @version 2.5.7
	 * @since   2.5.6
	 */
	function get_order_export_fields() {
		return array(
			'order-id'                         => __( 'Order ID', 'e-commerce-jetpack' ),
			'order-number'                     => __( 'Order Number', 'e-commerce-jetpack' ),
			'order-status'                     => __( 'Order Status', 'e-commerce-jetpack' ),
			'order-date'                       => __( 'Order Date', 'e-commerce-jetpack' ),
			'order-time'                       => __( 'Order Time', 'e-commerce-jetpack' ),
			'order-item-count'                 => __( 'Order Item Count', 'e-commerce-jetpack' ),
			'order-items'                      => __( 'Order Items', 'e-commerce-jetpack' ),
			'order-items-product-input-fields' => __( 'Order Items Product Input Fields', 'e-commerce-jetpack' ),
			'order-currency'                   => __( 'Order Currency', 'e-commerce-jetpack' ),
			'order-total'                      => __( 'Order Total', 'e-commerce-jetpack' ),
			'order-total-tax'                  => __( 'Order Total Tax', 'e-commerce-jetpack' ),
			'order-payment-method'             => __( 'Order Payment Method', 'e-commerce-jetpack' ),
			'order-notes'                      => __( 'Order Notes', 'e-commerce-jetpack' ),
			'billing-first-name'               => __( 'Billing First Name', 'e-commerce-jetpack' ),
			'billing-last-name'                => __( 'Billing Last Name', 'e-commerce-jetpack' ),
			'billing-company'                  => __( 'Billing Company', 'e-commerce-jetpack' ),
			'billing-address-1'                => __( 'Billing Address 1', 'e-commerce-jetpack' ),
			'billing-address-2'                => __( 'Billing Address 2', 'e-commerce-jetpack' ),
			'billing-city'                     => __( 'Billing City', 'e-commerce-jetpack' ),
			'billing-state'                    => __( 'Billing State', 'e-commerce-jetpack' ),
			'billing-postcode'                 => __( 'Billing Postcode', 'e-commerce-jetpack' ),
			'billing-country'                  => __( 'Billing Country', 'e-commerce-jetpack' ),
			'billing-phone'                    => __( 'Billing Phone', 'e-commerce-jetpack' ),
			'billing-email'                    => __( 'Billing Email', 'e-commerce-jetpack' ),
			'shipping-first-name'              => __( 'Shipping First Name', 'e-commerce-jetpack' ),
			'shipping-last-name'               => __( 'Shipping Last Name', 'e-commerce-jetpack' ),
			'shipping-company'                 => __( 'Shipping Company', 'e-commerce-jetpack' ),
			'shipping-address-1'               => __( 'Shipping Address 1', 'e-commerce-jetpack' ),
			'shipping-address-2'               => __( 'Shipping Address 2', 'e-commerce-jetpack' ),
			'shipping-city'                    => __( 'Shipping City', 'e-commerce-jetpack' ),
			'shipping-state'                   => __( 'Shipping State', 'e-commerce-jetpack' ),
			'shipping-postcode'                => __( 'Shipping Postcode', 'e-commerce-jetpack' ),
			'shipping-country'                 => __( 'Shipping Country', 'e-commerce-jetpack' ),
		);
	}

	/**
	 * get_order_export_default_fields_ids.
	 *
	 * @version 2.5.7
	 * @since   2.5.6
	 */
	function get_order_export_default_fields_ids() {
		return array(
			'order-id',
			'order-number',
			'order-status',
			'order-date',
			'order-time',
			'order-item-count',
			'order-items',
			'order-currency',
			'order-total',
			'order-total-tax',
			'order-payment-method',
			'order-notes',
			'billing-first-name',
			'billing-last-name',
			'billing-company',
			'billing-address-1',
			'billing-address-2',
			'billing-city',
			'billing-state',
			'billing-postcode',
			'billing-country',
			'billing-phone',
			'billing-email',
			'shipping-first-name',
			'shipping-last-name',
			'shipping-company',
			'shipping-address-1',
			'shipping-address-2',
			'shipping-city',
			'shipping-state',
			'shipping-postcode',
			'shipping-country',
		);
	}

	/**
	 * get_product_export_fields.
	 *
	 * @version 2.6.0
	 * @since   2.5.7
	 */
	function get_product_export_fields() {
		return array(
			'product-id'                         => __( 'Product ID', 'e-commerce-jetpack' ),
			'parent-product-id'                  => __( 'Parent Product ID', 'e-commerce-jetpack' ),
			'product-name'                       => __( 'Name', 'e-commerce-jetpack' ),
			'product-sku'                        => __( 'SKU', 'e-commerce-jetpack' ),
			'product-stock'                      => __( 'Total Stock', 'e-commerce-jetpack' ),
			'product-stock-quantity'             => __( 'Stock Quantity', 'e-commerce-jetpack' ),
			'product-regular-price'              => __( 'Regular Price', 'e-commerce-jetpack' ),
			'product-sale-price'                 => __( 'Sale Price', 'e-commerce-jetpack' ),
			'product-price'                      => __( 'Price', 'e-commerce-jetpack' ),
			'product-type'                       => __( 'Type', 'e-commerce-jetpack' ),
//			'product-attributes'                 => __( 'Attributes', 'e-commerce-jetpack' ),
			'product-image-url'                  => __( 'Image URL', 'e-commerce-jetpack' ),
			'product-short-description'          => __( 'Short Description', 'e-commerce-jetpack' ),
			'product-description'                => __( 'Description', 'e-commerce-jetpack' ),
			'product-status'                     => __( 'Status', 'e-commerce-jetpack' ),
			'product-url'                        => __( 'URL', 'e-commerce-jetpack' ),
			'product-shipping-class'             => __( 'Shipping Class', 'e-commerce-jetpack' ),
			'product-shipping-class-id'          => __( 'Shipping Class ID', 'e-commerce-jetpack' ),
			'product-width'                      => __( 'Width', 'e-commerce-jetpack' ),
			'product-length'                     => __( 'Length', 'e-commerce-jetpack' ),
			'product-height'                     => __( 'Height', 'e-commerce-jetpack' ),
			'product-weight'                     => __( 'Weight', 'e-commerce-jetpack' ),
			'product-downloadable'               => __( 'Downloadable', 'e-commerce-jetpack' ),
			'product-virtual'                    => __( 'Virtual', 'e-commerce-jetpack' ),
			'product-sold-individually'          => __( 'Sold Individually', 'e-commerce-jetpack' ),
			'product-tax-status'                 => __( 'Tax Status', 'e-commerce-jetpack' ),
			'product-tax-class'                  => __( 'Tax Class', 'e-commerce-jetpack' ),
			'product-manage-stock'               => __( 'Manage Stock', 'e-commerce-jetpack' ),
			'product-stock-status'               => __( 'Stock Status', 'e-commerce-jetpack' ),
			'product-backorders'                 => __( 'Backorders', 'e-commerce-jetpack' ),
			'product-featured'                   => __( 'Featured', 'e-commerce-jetpack' ),
			'product-visibility'                 => __( 'Visibility', 'e-commerce-jetpack' ),
			'product-price-including-tax'        => __( 'Price Including Tax', 'e-commerce-jetpack' ),
			'product-price-excluding-tax'        => __( 'Price Excluding Tax', 'e-commerce-jetpack' ),
			'product-display-price'              => __( 'Display Price', 'e-commerce-jetpack' ),
			'product-average-rating'             => __( 'Average Rating', 'e-commerce-jetpack' ),
			'product-rating-count'               => __( 'Rating Count', 'e-commerce-jetpack' ),
			'product-review-count'               => __( 'Review Count', 'e-commerce-jetpack' ),
			'product-categories'                 => __( 'Categories', 'e-commerce-jetpack' ),
			'product-tags'                       => __( 'Tags', 'e-commerce-jetpack' ),
			'product-dimensions'                 => __( 'Dimensions', 'e-commerce-jetpack' ),
			'product-formatted-name'             => __( 'Formatted Name', 'e-commerce-jetpack' ),
			'product-availability'               => __( 'Availability', 'e-commerce-jetpack' ),
			'product-availability-class'         => __( 'Availability Class', 'e-commerce-jetpack' ),
		);
	}

	/**
	 * get_product_export_default_fields_ids.
	 *
	 * @version 2.5.7
	 * @since   2.5.7
	 */
	function get_product_export_default_fields_ids() {
		return array(
			'product-id',
			'product-name',
			'product-sku',
			'product-stock',
			'product-regular-price',
			'product-sale-price',
			'product-price',
			'product-type',
			'product-image-url',
			'product-short-description',
			'product-status',
			'product-url',
		);
	}

}

endif;

return new WCJ_Export_Fields_Helper();
