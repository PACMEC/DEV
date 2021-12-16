<?php
/**
 * Booster for WooCommerce - PDF Invoicing - Advanced
 *
 * @version 3.3.0
 * @since   3.3.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_PDF_Invoicing_Advanced' ) ) :

class WCJ_PDF_Invoicing_Advanced extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 3.3.0
	 * @since   3.3.0
	 */
	function __construct() {
		$this->id         = 'pdf_invoicing_advanced';
		$this->parent_id  = 'pdf_invoicing';
		$this->short_desc = __( 'Advanced', 'e-commerce-jetpack' );
		$this->desc       = '';
		parent::__construct( 'submodule' );
	}

	/**
	 * get_report_default_columns.
	 *
	 * @version 3.3.0
	 * @since   3.3.0
	 */
	function get_report_default_columns() {
		return array_keys( array(
			'document_number'                    => __( 'Document Number', 'e-commerce-jetpack' ),
			'document_date'                      => __( 'Document Date', 'e-commerce-jetpack' ),
			'order_id'                           => __( 'Order ID', 'e-commerce-jetpack' ),
			'customer_country'                   => __( 'Customer Country', 'e-commerce-jetpack' ),
			'customer_vat_id'                    => __( 'Customer VAT ID', 'e-commerce-jetpack' ),
			'tax_percent'                        => __( 'Tax %', 'e-commerce-jetpack' ),
			'order_total_tax_excluding'          => __( 'Order Total Excl. Tax', 'e-commerce-jetpack' ),
			'order_taxes'                        => __( 'Order Taxes', 'e-commerce-jetpack' ),
			'order_total'                        => __( 'Order Total', 'e-commerce-jetpack' ),
			'order_currency'                     => __( 'Order Currency', 'e-commerce-jetpack' ),
			'payment_gateway'                    => __( 'Payment Gateway', 'e-commerce-jetpack' ),
			'refunds'                            => __( 'Refunds', 'e-commerce-jetpack' ),
		) );
	}

	/**
	 * get_report_columns.
	 *
	 * @version 3.3.0
	 * @since   3.3.0
	 * @todo    (maybe) `order_discount_tax`
	 */
	function get_report_columns() {
		return array(
			'document_number'                    => __( 'Document Number', 'e-commerce-jetpack' ),
			'document_date'                      => __( 'Document Date', 'e-commerce-jetpack' ),
			'order_id'                           => __( 'Order ID', 'e-commerce-jetpack' ),
			'customer_country'                   => __( 'Customer Country', 'e-commerce-jetpack' ),
			'customer_vat_id'                    => __( 'Customer VAT ID', 'e-commerce-jetpack' ),
			'tax_percent'                        => __( 'Tax %', 'e-commerce-jetpack' ),
			'order_total_tax_excluding'          => __( 'Order Total Excl. Tax', 'e-commerce-jetpack' ),
			'order_taxes'                        => __( 'Order Taxes', 'e-commerce-jetpack' ),
			'order_cart_total_excl_tax'          => __( 'Cart Total Excl. Tax', 'e-commerce-jetpack' ),
			'order_cart_tax'                     => __( 'Cart Tax', 'e-commerce-jetpack' ),
			'order_cart_tax_percent'             => __( 'Cart Tax %', 'e-commerce-jetpack' ),
			'order_shipping_total_excl_tax'      => __( 'Shipping Total Excl. Tax', 'e-commerce-jetpack' ),
			'order_shipping_tax'                 => __( 'Shipping Tax', 'e-commerce-jetpack' ),
			'order_shipping_tax_percent'         => __( 'Shipping Tax %', 'e-commerce-jetpack' ),
			'order_total'                        => __( 'Order Total', 'e-commerce-jetpack' ),
			'order_currency'                     => __( 'Order Currency', 'e-commerce-jetpack' ),
			'payment_gateway'                    => __( 'Payment Gateway', 'e-commerce-jetpack' ),
			'refunds'                            => __( 'Refunds', 'e-commerce-jetpack' ),
		);
	}

}

endif;

return new WCJ_PDF_Invoicing_Advanced();
