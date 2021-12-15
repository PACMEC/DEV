<?php
/**
 * EAN for WooCommerce - Barcodes Section Settings
 *
 * @version 2.6.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_EAN_Settings_Barcodes' ) ) :

class Alg_WC_EAN_Settings_Barcodes extends Alg_WC_EAN_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.1.0
	 * @since   2.0.0
	 */
	function __construct( $dim, $id, $desc ) {
		$this->dim        = $dim;
		$this->dim_suffix = ( '1d' === $this->dim ? '' : '_2d' );
		$this->id         = $id;
		$this->desc       = $desc;
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.6.0
	 * @since   2.0.0
	 *
	 * @todo    [next] (desc) Enable section: better desc
	 * @todo    [maybe] (desc) `alg_wc_ean_order_items_table_barcode`: "... *may* not work..."?
	 * @todo    [maybe] (dev) `alg_wc_ean_backend_column_barcode_data[]`?
	 * @todo    [maybe] (desc) `alg_wc_ean_backend_column_barcode_template`: better desc?
	 * @todo    [maybe] (dev) Barcode type: add all types from https://github.com/tecnickcom/TCPDF/blob/6.3.5/tcpdf_barcodes_2d.php#L66, i.e. `QRCODE` and `PDF417,a,e,t,s,f,o0,o1,o2,o3,o4,o5,o6` (last one maybe as an additional "Custom type" option?)
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'    => sprintf( __( '%s Options', 'ean-for-woocommerce' ), $this->desc ),
				'type'     => 'title',
				'id'       => "alg_wc_ean_barcode{$this->dim_suffix}_options",
			),
			array(
				'title'    => $this->desc,
				'desc'     => '<strong>' . __( 'Enable section', 'ean-for-woocommerce' ) . '</strong>',
				'desc_tip' => $this->pro_msg(),
				'id'       => "alg_wc_ean_barcode{$this->dim_suffix}",
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_ean_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Admin product edit page', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Show barcode image on admin product edit page.', 'ean-for-woocommerce' ),
				'id'       => "alg_wc_ean_backend_barcode{$this->dim_suffix}",
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Admin products list column', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Add "Barcode" column to %s.', 'ean-for-woocommerce' ),
						'<a href="' . admin_url( 'edit.php?post_type=product' ) . '">' . __( 'admin products list', 'ean-for-woocommerce' ) . '</a>' ),
				'id'       => "alg_wc_ean_backend_column_barcode{$this->dim_suffix}",
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Column title', 'ean-for-woocommerce' ),
				'id'       => "alg_wc_ean_backend_column_barcode_title{$this->dim_suffix}",
				'default'  => ( '1d' === $this->dim ? __( 'Barcode', 'ean-for-woocommerce' ) : __( '2D barcode', 'ean-for-woocommerce' ) ),
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Column template', 'ean-for-woocommerce' ) . '<br>' .
					sprintf( __( 'You should use %s shortcode here.', 'ean-for-woocommerce' ), '<code>[alg_wc_ean_barcode' . $this->dim_suffix . ']</code>' ),
				'id'       => "alg_wc_ean_backend_column_barcode_template{$this->dim_suffix}",
				'default'  => '[alg_wc_ean_barcode' . $this->dim_suffix . ' content="ean" w="' . ( '1d' === $this->dim ? 1  : 1 ) . '" h="' . ( '1d' === $this->dim ? 15 : 1 ) . '" children="yes"]',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Single product page', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Show barcode image on single product page on frontend.', 'ean-for-woocommerce' ),
				'id'       => "alg_wc_ean_frontend_barcode{$this->dim_suffix}",
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Variable products: Position in variation', 'ean-for-woocommerce' ),
				'id'       => "alg_wc_ean_frontend_variation_position_barcode{$this->dim_suffix}",
				'default'  => 'product_meta',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'product_meta'          => __( 'Product meta', 'ean-for-woocommerce' ),
					'variation_description' => __( 'Description', 'ean-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Shop pages', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Show barcode on shop (e.g. category) pages on frontend.', 'ean-for-woocommerce' ) . ' ' . $this->variable_products_note(),
				'id'       => "alg_wc_ean_frontend_loop_barcode{$this->dim_suffix}",
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Cart', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Show barcode on cart page on frontend.', 'ean-for-woocommerce' ),
				'id'       => "alg_wc_ean_frontend_cart_barcode{$this->dim_suffix}",
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Order items table', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Show barcode image in order items table.', 'ean-for-woocommerce' ) . ' ' .
					__( 'This will affect all places where order items table is displayed, including <strong>emails</strong> (both admin and customer), <strong>"thank you" page</strong> (i.e. "order received" page), <strong>"view order" page</strong> (in "my account").', 'ean-for-woocommerce' ) . '<br>' .
					__( '<strong>Please note</strong> that this option won\'t work on <code>localhost</code> environment.', 'ean-for-woocommerce' ),
				'id'       => "alg_wc_ean_order_items_table_barcode{$this->dim_suffix}",
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => "alg_wc_ean_barcode{$this->dim_suffix}_options",
			),
			array(
				'title'    => __( 'Plugin Compatibility Options', 'ean-for-woocommerce' ),
				'type'     => 'title',
				'id'       => "alg_wc_ean_barcode{$this->dim_suffix}_compatibility_options",
			),
			array(
				'title'    => __( 'Print Invoice & Delivery Notes for WooCommerce', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Show barcode image in PDF documents of the %s plugin.', 'ean-for-woocommerce' ),
					'<a target="_blank" href="' . 'https://wordpress.org/plugins/woocommerce-delivery-notes/' . '">' .
						__( 'Print Invoice & Delivery Notes for WooCommerce', 'ean-for-woocommerce' ) . '</a>' ),
				'id'       => "alg_wc_ean_wcdn_barcode{$this->dim_suffix}",
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'WooCommerce PDF Invoices & Packing Slips', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Show barcode image in PDF documents of the %s plugin.', 'ean-for-woocommerce' ),
					'<a target="_blank" href="' . 'https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/' . '">' .
						__( 'WooCommerce PDF Invoices & Packing Slips', 'ean-for-woocommerce' ) . '</a>' ),
				'id'       => "alg_wc_ean_wpo_wcpdf_barcode{$this->dim_suffix}",
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => "alg_wc_ean_barcode{$this->dim_suffix}_compatibility_options",
			),
		);
		$settings = array_merge( $settings, array(
			array(
				'title'    => __( 'Advanced Options', 'ean-for-woocommerce' ),
				'type'     => 'title',
				'id'       => "alg_wc_ean_barcode{$this->dim_suffix}_advanced_options",
			),
		) );
		if ( '2d' === $this->dim ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => __( 'Barcode type', 'ean-for-woocommerce' ),
					'id'       => "alg_wc_ean_barcode{$this->dim_suffix}_barcode_type",
					'default'  => 'QRCODE,L',
					'type'     => 'select',
					'class'    => 'chosen_select',
					'options'  => array(
						'QRCODE,L'   => __( 'QR code: Low error correction', 'ean-for-woocommerce' ),
						'QRCODE,M'   => __( 'QR code: Medium error correction', 'ean-for-woocommerce' ),
						'QRCODE,Q'   => __( 'QR code: Better error correction', 'ean-for-woocommerce' ),
						'QRCODE,H'   => __( 'QR code: Best error correction', 'ean-for-woocommerce' ),
						'DATAMATRIX' => __( 'Datamatrix (ISO/IEC 16022)', 'ean-for-woocommerce' ),
						'PDF417'     => __( 'PDF417 (ISO/IEC 15438:2006)', 'ean-for-woocommerce' ),
					),
				),
			) );
		}
		$settings = array_merge( $settings, array(
			array(
				'title'    => __( 'Suppress errors', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ) . ' (' . __( 'recommended', 'ean-for-woocommerce' ) . ')',
				'desc_tip' => __( 'Suppress PHP errors when generating barcode.', 'ean-for-woocommerce' ),
				'id'       => "alg_wc_ean_suppress_errors_barcode{$this->dim_suffix}",
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => "alg_wc_ean_barcode{$this->dim_suffix}_advanced_options",
			),
		) );
		$settings = array_merge( $settings, array(
			array(
				'title'    => __( 'Notes', 'ean-for-woocommerce' ),
				'desc'     => '<span class="dashicons dashicons-info"></span> ' . sprintf( __( 'You can also output barcode with %s shortcode.', 'ean-for-woocommerce' ),
					"<code>[alg_wc_ean_barcode{$this->dim_suffix}]</code>" ),
				'type'     => 'title',
				'id'       => "alg_wc_ean_barcode{$this->dim_suffix}_notes",
			),
			array(
				'type'     => 'sectionend',
				'id'       => "alg_wc_ean_barcode{$this->dim_suffix}_notes",
			),
		) );
		return $settings;
	}

}

endif;
