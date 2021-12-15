<?php
/**
 * EAN for WooCommerce - Compatibility Section Settings
 *
 * @version 2.6.0
 * @since   2.2.9
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_EAN_Settings_Compatibility' ) ) :

class Alg_WC_EAN_Settings_Compatibility extends Alg_WC_EAN_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.2.9
	 * @since   2.2.9
	 */
	function __construct() {
		$this->id   = 'compatibility';
		$this->desc = __( 'Compatibility', 'ean-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.6.0
	 * @since   2.2.9
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Plugin Compatibility Options', 'ean-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_plugin_compatibility_options',
			),
			array(
				'title'    => __( 'Print Invoice & Delivery Notes for WooCommerce', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'This will show EAN in PDF documents of the %s plugin.', 'ean-for-woocommerce' ),
					'<a target="_blank" href="' . 'https://wordpress.org/plugins/woocommerce-delivery-notes/' . '">' .
						__( 'Print Invoice & Delivery Notes for WooCommerce', 'ean-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_ean_wcdn',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'WooCommerce PDF Invoices & Packing Slips', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'This will show EAN in PDF documents of the %s plugin.', 'ean-for-woocommerce' ),
					'<a target="_blank" href="' . 'https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/' . '">' .
						__( 'WooCommerce PDF Invoices & Packing Slips', 'ean-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_ean_wpo_wcpdf',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Dokan', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'This will show EAN in vendor product form of the %s plugin.', 'ean-for-woocommerce' ),
					'<a target="_blank" href="https://wordpress.org/plugins/dokan-lite/">' . __( 'Dokan', 'ean-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_ean_dokan',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Title', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_dokan_title',
				'default'  => __( 'EAN', 'ean-for-woocommerce' ),
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Placeholder', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_dokan_placeholder',
				'default'  => __( 'Product EAN...', 'ean-for-woocommerce' ),
				'type'     => 'text',
			),
			array(
				'title'    => __( 'WCFM', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'This will show EAN in product forms of the %s and %s plugins.', 'ean-for-woocommerce' ),
					'<a target="_blank" href="https://wordpress.org/plugins/wc-frontend-manager/">' . __( 'WCFM', 'ean-for-woocommerce' ) . '</a>',
					'<a target="_blank" href="https://wordpress.org/plugins/wc-multivendor-marketplace/">' . __( 'WCFM Marketplace', 'ean-for-woocommerce' ) . '</a>' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_wcfm',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Title', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_wcfm_title',
				'default'  => __( 'EAN', 'ean-for-woocommerce' ),
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Placeholder', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_wcfm_placeholder',
				'default'  => __( 'Product EAN...', 'ean-for-woocommerce' ),
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Hints', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_wcfm_hints',
				'default'  => __( 'The International Article Number (also known as European Article Number or EAN) is a standard describing a barcode symbology and numbering system used in global trade to identify a specific retail product type, in a specific packaging configuration, from a specific manufacturer.', 'ean-for-woocommerce' ),
				'type'     => 'textarea',
				'css'      => 'height:110px;',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_plugin_compatibility_options',
			),
		);
	}

}

endif;

return new Alg_WC_EAN_Settings_Compatibility();
