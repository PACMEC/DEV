<?php
/**
 * EAN for WooCommerce - Print Section Settings
 *
 * @version 2.7.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_EAN_Settings_Print' ) ) :

class Alg_WC_EAN_Settings_Print extends Alg_WC_EAN_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {
		$this->id   = 'print';
		$this->desc = __( 'Print', 'ean-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.7.0
	 * @since   2.0.0
	 *
	 * @see     https://www.avery.com/templates/6879 (default margins etc.)
	 *
	 * @todo    [next] (dev) mark placeholder sections as "deprecated" (at least "2D barcode content"); update "Template" default value?
	 * @todo    [next] (desc) `children`: better desc?
	 * @todo    [next] (desc) better section desc?
	 * @todo    [maybe] (desc) Page format: better desc?
	 */
	function get_settings() {

		$units = array(
			'mm' => __( 'millimeters', 'ean-for-woocommerce' ),
			'cm' => __( 'centimeters', 'ean-for-woocommerce' ),
			'in' => __( 'inches', 'ean-for-woocommerce' ),
			'pt' => __( 'points', 'ean-for-woocommerce' ),
		);
		$options = get_option( 'alg_wc_ean_print_barcodes_to_pdf_settings', array() );
		$unit_title = $units[ ( isset( $options['unit'] ) ? $options['unit'] : 'in' ) ];

		$point_desc = __( 'A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.', 'ean-for-woocommerce' );

		return array(
			array(
				'title'    => __( 'Print Barcodes (PDF)', 'ean-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_options',
			),
			array(
				'title'    => __( 'Print barcodes (PDF)', 'ean-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'ean-for-woocommerce' ) . '</strong>',
				'desc_tip' => sprintf( __( 'This will add "Print barcodes" to the "Bulk actions" in %s.', 'ean-for-woocommerce' ),
						'<a href="' . admin_url( 'edit.php?post_type=product' ) . '">' . __( 'admin products list', 'ean-for-woocommerce' ) . '</a>' ) . $this->pro_msg(),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf',
				'default'  => 'no',
				'type'     => 'checkbox',
				
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_options',
			),
			array(
				'title'    => __( 'General Options', 'ean-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_general_options',
			),
			array(
				'title'    => __( 'Page orientation', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[page_orientation]',
				'default'  => 'P',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'P' => __( 'Portrait', 'ean-for-woocommerce' ),
					'L' => __( 'Landscape', 'ean-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Unit', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'User measure unit.', 'ean-for-woocommerce' ) . '<br><br>' .
					sprintf( __( 'Used in %s options.', 'ean-for-woocommerce' ), '"' . implode( '", "', array(
							__( 'Page format: Custom: Width', 'ean-for-woocommerce' ),
							__( 'Page format: Custom: Height', 'ean-for-woocommerce' ),
							__( 'Cell width', 'ean-for-woocommerce' ),
							__( 'Cell height', 'ean-for-woocommerce' ),
							__( 'Top margin', 'ean-for-woocommerce' ),
							__( 'Left margin', 'ean-for-woocommerce' ),
							__( 'Right margin', 'ean-for-woocommerce' ),
							__( 'Page break margin', 'ean-for-woocommerce' ),
						) ) . '"' ) . '<br><br>' .
					'* ' . $point_desc,
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[unit]',
				'default'  => 'in',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => $units,
			),
			array(
				'title'    => __( 'Page format', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[page_format]',
				'default'  => 'LETTER',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array_merge( array( 'custom' => __( 'Custom', 'ean-for-woocommerce' ) ),
					array_combine( array_keys( $this->get_pdf_page_formats() ), array_keys( $this->get_pdf_page_formats() ) ) ),
			),
			array(
				'desc'     => __( 'Page format: Custom: Width', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'In %s.', 'ean-for-woocommerce' ), $unit_title ) . '<br><br>' .
					sprintf( __( 'Ignored unless "%s" option is set to "%s".', 'ean-for-woocommerce' ),
						__( 'Page format', 'ean-for-woocommerce' ), __( 'Custom', 'ean-for-woocommerce' ) ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[page_format_custom_width]',
				'default'  => 0,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0, 'step' => 0.000001 ),
			),
			array(
				'desc'     => __( 'Page format: Custom: Height', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'In %s.', 'ean-for-woocommerce' ), $unit_title ) . '<br><br>' .
					sprintf( __( 'Ignored unless "%s" option is set to "%s".', 'ean-for-woocommerce' ),
						__( 'Page format', 'ean-for-woocommerce' ), __( 'Custom', 'ean-for-woocommerce' ) ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[page_format_custom_height]',
				'default'  => 0,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0, 'step' => 0.000001 ),
			),
			array(
				'title'    => __( 'Max barcodes per page', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[per_page]',
				'default'  => 12,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'title'    => __( 'Columns', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[columns]',
				'default'  => 2,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'title'    => __( 'Cell width', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'In %s.', 'ean-for-woocommerce' ), $unit_title ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[cell_width]',
				'default'  => 4,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0.000001, 'step' => 0.000001 ),
			),
			array(
				'title'    => __( 'Cell height', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'In %s.', 'ean-for-woocommerce' ), $unit_title ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[cell_height]',
				'default'  => 1.5,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0.000001, 'step' => 0.000001 ),
			),
			array(
				'title'    => __( 'Top margin', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'In %s.', 'ean-for-woocommerce' ), $unit_title ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[margin_top]',
				'default'  => 1.13,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0, 'step' => 0.000001 ),
			),
			array(
				'title'    => __( 'Left margin', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'In %s.', 'ean-for-woocommerce' ), $unit_title ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[margin_left]',
				'default'  => 0.46,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0, 'step' => 0.000001 ),
			),
			array(
				'title'    => __( 'Right margin', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'In %s.', 'ean-for-woocommerce' ), $unit_title ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[margin_right]',
				'default'  => 0.31,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0, 'step' => 0.000001 ),
			),
			array(
				'title'    => __( 'Page break margin', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'In %s.', 'ean-for-woocommerce' ), $unit_title ) . '<br><br>' .
					__( 'Distance from the bottom of the page that defines the automatic page breaking triggering limit.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[page_break_margin]',
				'default'  => 0.79,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0, 'step' => 0.000001 ),
			),
			array(
				'title'    => __( 'Font', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'If you are having issues displaying your language specific letters, select "%s" font.', 'ean-for-woocommerce' ),
					'DejaVu Sans (Unicode)' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[font_family]',
				'default'  => 'dejavusans',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'dejavusans' => 'DejaVu Sans (Unicode)',
					'times'      => 'Times New Roman',
					'helvetica'  => 'Helvetica',
					'courier'    => 'Courier',
				),
			),
			array(
				'title'    => __( 'Font size', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[font_size]',
				'default'  => 11,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'title'    => __( 'Template', 'ean-for-woocommerce' ),
				'desc'     => sprintf( __( 'Available placeholders: %s.', 'ean-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array(
						'%barcode%',
						'%barcode_2d%',
						'%ean%',
						'%product_image%',
						'%product_name%',
						'%product_title%',
						'%product_sku%',
						'%product_id%',
						'%product_parent_title%',
						'%product_parent_sku%',
					) ) . '</code>' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[template]',
				'default'  => '%barcode%<br>%ean%',
				'type'     => 'textarea',
				'css'      => 'width:100%;height:150px;',
			),
			array(
				'title'    => __( 'Variations', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[children]',
				'default'  => 'no',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'no'      => __( 'Do not include', 'ean-for-woocommerce' ),
					'yes'     => __( 'Add', 'ean-for-woocommerce' ),
					'replace' => __( 'Replace', 'ean-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Use quantity', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Print separate label for each product inventory item.', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[use_qty]',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_general_options',
			),
			array(
				'title'    => __( 'Barcode Options', 'ean-for-woocommerce' ),
				'desc'     => sprintf( __( 'For the %s placeholder.', 'ean-for-woocommerce' ), '<code>' . '%barcode%' . '</code>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_barcode_options',
			),
			array(
				'title'    => __( 'Barcode width', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Width of a single bar element in pixels.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[barcode_width_px]',
				'default'  => 2,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'title'    => __( 'Barcode height', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Height of a single bar element in pixels.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[barcode_height_px]',
				'default'  => 30,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'title'    => __( 'Barcode color', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[barcode_color]',
				'default'  => '#000000',
				'type'     => 'color',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_barcode_options',
			),
			array(
				'title'    => __( '2D Barcode Options', 'ean-for-woocommerce' ),
				'desc'     => sprintf( __( 'For the %s placeholder.', 'ean-for-woocommerce' ), '<code>' . '%barcode_2d%' . '</code>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_barcode_2d_options',
			),
			array(
				'title'    => __( '2D barcode width', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Width of a single rectangle element in pixels.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[barcode_2d_width_px]',
				'default'  => 3,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'title'    => __( '2D barcode height', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Height of a single rectangle element in pixels.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[barcode_2d_height_px]',
				'default'  => 3,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'title'    => __( '2D barcode color', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[barcode_2d_color]',
				'default'  => '#000000',
				'type'     => 'color',
			),
			array(
				'title'    => __( '2D barcode content', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[barcode_2d_content]',
				'default'  => 'ean',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'ean' => __( 'Product EAN', 'ean-for-woocommerce' ),
					'url' => __( 'Product URL', 'ean-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_barcode_2d_options',
			),
			array(
				'title'    => __( 'Product Image Options', 'ean-for-woocommerce' ),
				'desc'     => sprintf( __( 'For the %s placeholder.', 'ean-for-woocommerce' ), '<code>' . '%product_image%' . '</code>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_product_image_options',
			),
			array(
				'title'    => __( 'Product image width', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'In pixels.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[product_image_width_px]',
				'default'  => 30,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'title'    => __( 'Product image height', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'In pixels.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[product_image_height_px]',
				'default'  => 30,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'title'    => __( 'Product image size', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Accepts any registered image size name.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[product_image_size]',
				'default'  => 'woocommerce_thumbnail',
				'type'     => 'text',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_product_image_options',
			),
			array(
				'title'    => __( 'Admin Options', 'ean-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_admin_options',
			),
			array(
				'title'    => __( 'Print buttons', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings_print_buttons',
				'default'  => array( 'bulk_actions' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => array(
					'bulk_actions'   => __( 'Products > Bulk actions', 'ean-for-woocommerce' ),
					'single_product' => __( 'Single product', 'ean-for-woocommerce' ),
					'single_order'   => __( 'Single order', 'ean-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_admin_options',
			),
			array(
				'title'    => __( 'Advanced Options', 'ean-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_advanced_options',
			),
			array(
				'title'    => __( 'Skip products without EAN', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Skip products without EAN when generating PDF.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings_skip_empty_ean',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Use Print.js', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Use %s library for printing PDFs.', 'ean-for-woocommerce' ),
					'<a href="https://printjs.crabbly.com/" target="_blank">Print.js</a>' ),
				'id'       => 'alg_wc_ean_print_use_print_js',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Suppress errors', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ) . ' (' . __( 'recommended', 'ean-for-woocommerce' ) . ')',
				'desc_tip' => __( 'Suppress PHP errors when generating PDF.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_settings[suppress_errors]',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_print_barcodes_to_pdf_advanced_options',
			),
		);

	}

	/**
	 * get_pdf_page_formats.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.5/include/tcpdf_static.php#L2129
	 *
	 * @todo    [now] (dev) test `unit`, e.g. `mm`?
	 * @todo    [next] [!] add dimensions to the desc?
	 */
	function get_pdf_page_formats() {
		return array(
			// ISO 216 A Series + 2 SIS 014711 extensions
			'A0'                     => array( 2383.937,  3370.394), // = (  841 x 1189 ) mm  = ( 33.11 x 46.81 ) in
			'A1'                     => array( 1683.780,  2383.937), // = (  594 x 841  ) mm  = ( 23.39 x 33.11 ) in
			'A2'                     => array( 1190.551,  1683.780), // = (  420 x 594  ) mm  = ( 16.54 x 23.39 ) in
			'A3'                     => array(  841.890,  1190.551), // = (  297 x 420  ) mm  = ( 11.69 x 16.54 ) in
			'A4'                     => array(  595.276,   841.890), // = (  210 x 297  ) mm  = (  8.27 x 11.69 ) in
			'A5'                     => array(  419.528,   595.276), // = (  148 x 210  ) mm  = (  5.83 x 8.27  ) in
			'A6'                     => array(  297.638,   419.528), // = (  105 x 148  ) mm  = (  4.13 x 5.83  ) in
			'A7'                     => array(  209.764,   297.638), // = (   74 x 105  ) mm  = (  2.91 x 4.13  ) in
			'A8'                     => array(  147.402,   209.764), // = (   52 x 74   ) mm  = (  2.05 x 2.91  ) in
			'A9'                     => array(  104.882,   147.402), // = (   37 x 52   ) mm  = (  1.46 x 2.05  ) in
			'A10'                    => array(   73.701,   104.882), // = (   26 x 37   ) mm  = (  1.02 x 1.46  ) in
			'A11'                    => array(   51.024,    73.701), // = (   18 x 26   ) mm  = (  0.71 x 1.02  ) in
			'A12'                    => array(   36.850,    51.024), // = (   13 x 18   ) mm  = (  0.51 x 0.71  ) in
			// ISO 216 B Series + 2 SIS 014711 extensions
			'B0'                     => array( 2834.646,  4008.189), // = ( 1000 x 1414 ) mm  = ( 39.37 x 55.67 ) in
			'B1'                     => array( 2004.094,  2834.646), // = (  707 x 1000 ) mm  = ( 27.83 x 39.37 ) in
			'B2'                     => array( 1417.323,  2004.094), // = (  500 x 707  ) mm  = ( 19.69 x 27.83 ) in
			'B3'                     => array( 1000.630,  1417.323), // = (  353 x 500  ) mm  = ( 13.90 x 19.69 ) in
			'B4'                     => array(  708.661,  1000.630), // = (  250 x 353  ) mm  = (  9.84 x 13.90 ) in
			'B5'                     => array(  498.898,   708.661), // = (  176 x 250  ) mm  = (  6.93 x 9.84  ) in
			'B6'                     => array(  354.331,   498.898), // = (  125 x 176  ) mm  = (  4.92 x 6.93  ) in
			'B7'                     => array(  249.449,   354.331), // = (   88 x 125  ) mm  = (  3.46 x 4.92  ) in
			'B8'                     => array(  175.748,   249.449), // = (   62 x 88   ) mm  = (  2.44 x 3.46  ) in
			'B9'                     => array(  124.724,   175.748), // = (   44 x 62   ) mm  = (  1.73 x 2.44  ) in
			'B10'                    => array(   87.874,   124.724), // = (   31 x 44   ) mm  = (  1.22 x 1.73  ) in
			'B11'                    => array(   62.362,    87.874), // = (   22 x 31   ) mm  = (  0.87 x 1.22  ) in
			'B12'                    => array(   42.520,    62.362), // = (   15 x 22   ) mm  = (  0.59 x 0.87  ) in
			// ISO 216 C Series + 2 SIS 014711 extensions + 5 EXTENSION
			'C0'                     => array( 2599.370,  3676.535), // = (  917 x 1297 ) mm  = ( 36.10 x 51.06 ) in
			'C1'                     => array( 1836.850,  2599.370), // = (  648 x 917  ) mm  = ( 25.51 x 36.10 ) in
			'C2'                     => array( 1298.268,  1836.850), // = (  458 x 648  ) mm  = ( 18.03 x 25.51 ) in
			'C3'                     => array(  918.425,  1298.268), // = (  324 x 458  ) mm  = ( 12.76 x 18.03 ) in
			'C4'                     => array(  649.134,   918.425), // = (  229 x 324  ) mm  = (  9.02 x 12.76 ) in
			'C5'                     => array(  459.213,   649.134), // = (  162 x 229  ) mm  = (  6.38 x 9.02  ) in
			'C6'                     => array(  323.150,   459.213), // = (  114 x 162  ) mm  = (  4.49 x 6.38  ) in
			'C7'                     => array(  229.606,   323.150), // = (   81 x 114  ) mm  = (  3.19 x 4.49  ) in
			'C8'                     => array(  161.575,   229.606), // = (   57 x 81   ) mm  = (  2.24 x 3.19  ) in
			'C9'                     => array(  113.386,   161.575), // = (   40 x 57   ) mm  = (  1.57 x 2.24  ) in
			'C10'                    => array(   79.370,   113.386), // = (   28 x 40   ) mm  = (  1.10 x 1.57  ) in
			'C11'                    => array(   56.693,    79.370), // = (   20 x 28   ) mm  = (  0.79 x 1.10  ) in
			'C12'                    => array(   39.685,    56.693), // = (   14 x 20   ) mm  = (  0.55 x 0.79  ) in
			'C76'                    => array(  229.606,   459.213), // = (   81 x 162  ) mm  = (  3.19 x 6.38  ) in
			'DL'                     => array(  311.811,   623.622), // = (  110 x 220  ) mm  = (  4.33 x 8.66  ) in
			'DLE'                    => array(  323.150,   637.795), // = (  114 x 225  ) mm  = (  4.49 x 8.86  ) in
			'DLX'                    => array(  340.158,   666.142), // = (  120 x 235  ) mm  = (  4.72 x 9.25  ) in
			'DLP'                    => array(  280.630,   595.276), // = (   99 x 210  ) mm  = (  3.90 x 8.27  ) in (1/3 A4)
			// SIS 014711 E Series
			'E0'                     => array( 2491.654,  3517.795), // = (  879 x 1241 ) mm  = ( 34.61 x 48.86 ) in
			'E1'                     => array( 1757.480,  2491.654), // = (  620 x 879  ) mm  = ( 24.41 x 34.61 ) in
			'E2'                     => array( 1247.244,  1757.480), // = (  440 x 620  ) mm  = ( 17.32 x 24.41 ) in
			'E3'                     => array(  878.740,  1247.244), // = (  310 x 440  ) mm  = ( 12.20 x 17.32 ) in
			'E4'                     => array(  623.622,   878.740), // = (  220 x 310  ) mm  = (  8.66 x 12.20 ) in
			'E5'                     => array(  439.370,   623.622), // = (  155 x 220  ) mm  = (  6.10 x 8.66  ) in
			'E6'                     => array(  311.811,   439.370), // = (  110 x 155  ) mm  = (  4.33 x 6.10  ) in
			'E7'                     => array(  221.102,   311.811), // = (   78 x 110  ) mm  = (  3.07 x 4.33  ) in
			'E8'                     => array(  155.906,   221.102), // = (   55 x 78   ) mm  = (  2.17 x 3.07  ) in
			'E9'                     => array(  110.551,   155.906), // = (   39 x 55   ) mm  = (  1.54 x 2.17  ) in
			'E10'                    => array(   76.535,   110.551), // = (   27 x 39   ) mm  = (  1.06 x 1.54  ) in
			'E11'                    => array(   53.858,    76.535), // = (   19 x 27   ) mm  = (  0.75 x 1.06  ) in
			'E12'                    => array(   36.850,    53.858), // = (   13 x 19   ) mm  = (  0.51 x 0.75  ) in
			// SIS 014711 G Series
			'G0'                     => array( 2715.591,  3838.110), // = (  958 x 1354 ) mm  = ( 37.72 x 53.31 ) in
			'G1'                     => array( 1919.055,  2715.591), // = (  677 x 958  ) mm  = ( 26.65 x 37.72 ) in
			'G2'                     => array( 1357.795,  1919.055), // = (  479 x 677  ) mm  = ( 18.86 x 26.65 ) in
			'G3'                     => array(  958.110,  1357.795), // = (  338 x 479  ) mm  = ( 13.31 x 18.86 ) in
			'G4'                     => array(  677.480,   958.110), // = (  239 x 338  ) mm  = (  9.41 x 13.31 ) in
			'G5'                     => array(  479.055,   677.480), // = (  169 x 239  ) mm  = (  6.65 x 9.41  ) in
			'G6'                     => array(  337.323,   479.055), // = (  119 x 169  ) mm  = (  4.69 x 6.65  ) in
			'G7'                     => array(  238.110,   337.323), // = (   84 x 119  ) mm  = (  3.31 x 4.69  ) in
			'G8'                     => array(  167.244,   238.110), // = (   59 x 84   ) mm  = (  2.32 x 3.31  ) in
			'G9'                     => array(  119.055,   167.244), // = (   42 x 59   ) mm  = (  1.65 x 2.32  ) in
			'G10'                    => array(   82.205,   119.055), // = (   29 x 42   ) mm  = (  1.14 x 1.65  ) in
			'G11'                    => array(   59.528,    82.205), // = (   21 x 29   ) mm  = (  0.83 x 1.14  ) in
			'G12'                    => array(   39.685,    59.528), // = (   14 x 21   ) mm  = (  0.55 x 0.83  ) in
			// ISO Press
			'RA0'                    => array( 2437.795,  3458.268), // = (  860 x 1220 ) mm  = ( 33.86 x 48.03 ) in
			'RA1'                    => array( 1729.134,  2437.795), // = (  610 x 860  ) mm  = ( 24.02 x 33.86 ) in
			'RA2'                    => array( 1218.898,  1729.134), // = (  430 x 610  ) mm  = ( 16.93 x 24.02 ) in
			'RA3'                    => array(  864.567,  1218.898), // = (  305 x 430  ) mm  = ( 12.01 x 16.93 ) in
			'RA4'                    => array(  609.449,   864.567), // = (  215 x 305  ) mm  = (  8.46 x 12.01 ) in
			'SRA0'                   => array( 2551.181,  3628.346), // = (  900 x 1280 ) mm  = ( 35.43 x 50.39 ) in
			'SRA1'                   => array( 1814.173,  2551.181), // = (  640 x 900  ) mm  = ( 25.20 x 35.43 ) in
			'SRA2'                   => array( 1275.591,  1814.173), // = (  450 x 640  ) mm  = ( 17.72 x 25.20 ) in
			'SRA3'                   => array(  907.087,  1275.591), // = (  320 x 450  ) mm  = ( 12.60 x 17.72 ) in
			'SRA4'                   => array(  637.795,   907.087), // = (  225 x 320  ) mm  = (  8.86 x 12.60 ) in
			// German DIN 476
			'4A0'                    => array( 4767.874,  6740.787), // = ( 1682 x 2378 ) mm  = ( 66.22 x 93.62 ) in
			'2A0'                    => array( 3370.394,  4767.874), // = ( 1189 x 1682 ) mm  = ( 46.81 x 66.22 ) in
			// Variations on the ISO Standard
			'A2_EXTRA'               => array( 1261.417,  1754.646), // = (  445 x 619  ) mm  = ( 17.52 x 24.37 ) in
			'A3+'                    => array(  932.598,  1369.134), // = (  329 x 483  ) mm  = ( 12.95 x 19.02 ) in
			'A3_EXTRA'               => array(  912.756,  1261.417), // = (  322 x 445  ) mm  = ( 12.68 x 17.52 ) in
			'A3_SUPER'               => array(  864.567,  1440.000), // = (  305 x 508  ) mm  = ( 12.01 x 20.00 ) in
			'SUPER_A3'               => array(  864.567,  1380.472), // = (  305 x 487  ) mm  = ( 12.01 x 19.17 ) in
			'A4_EXTRA'               => array(  666.142,   912.756), // = (  235 x 322  ) mm  = (  9.25 x 12.68 ) in
			'A4_SUPER'               => array(  649.134,   912.756), // = (  229 x 322  ) mm  = (  9.02 x 12.68 ) in
			'SUPER_A4'               => array(  643.465,  1009.134), // = (  227 x 356  ) mm  = (  8.94 x 14.02 ) in
			'A4_LONG'                => array(  595.276,   986.457), // = (  210 x 348  ) mm  = (  8.27 x 13.70 ) in
			'F4'                     => array(  595.276,   935.433), // = (  210 x 330  ) mm  = (  8.27 x 12.99 ) in
			'SO_B5_EXTRA'            => array(  572.598,   782.362), // = (  202 x 276  ) mm  = (  7.95 x 10.87 ) in
			'A5_EXTRA'               => array(  490.394,   666.142), // = (  173 x 235  ) mm  = (  6.81 x 9.25  ) in
			// ANSI Series
			'ANSI_E'                 => array( 2448.000,  3168.000), // = (  864 x 1118 ) mm  = ( 34.00 x 44.00 ) in
			'ANSI_D'                 => array( 1584.000,  2448.000), // = (  559 x 864  ) mm  = ( 22.00 x 34.00 ) in
			'ANSI_C'                 => array( 1224.000,  1584.000), // = (  432 x 559  ) mm  = ( 17.00 x 22.00 ) in
			'ANSI_B'                 => array(  792.000,  1224.000), // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
			'ANSI_A'                 => array(  612.000,   792.000), // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
			// Traditional 'Loose' North American Paper Sizes
			'USLEDGER'               => array( 1224.000,   792.000), // = (  432 x 279  ) mm  = ( 17.00 x 11.00 ) in
			'LEDGER'                 => array( 1224.000,   792.000), // = (  432 x 279  ) mm  = ( 17.00 x 11.00 ) in
			'ORGANIZERK'             => array(  792.000,  1224.000), // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
			'BIBLE'                  => array(  792.000,  1224.000), // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
			'USTABLOID'              => array(  792.000,  1224.000), // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
			'TABLOID'                => array(  792.000,  1224.000), // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
			'ORGANIZERM'             => array(  612.000,   792.000), // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
			'USLETTER'               => array(  612.000,   792.000), // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
			'LETTER'                 => array(  612.000,   792.000), // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
			'USLEGAL'                => array(  612.000,  1008.000), // = (  216 x 356  ) mm  = (  8.50 x 14.00 ) in
			'LEGAL'                  => array(  612.000,  1008.000), // = (  216 x 356  ) mm  = (  8.50 x 14.00 ) in
			'GOVERNMENTLETTER'       => array(  576.000,   756.000), // = (  203 x 267  ) mm  = (  8.00 x 10.50 ) in
			'GLETTER'                => array(  576.000,   756.000), // = (  203 x 267  ) mm  = (  8.00 x 10.50 ) in
			'JUNIORLEGAL'            => array(  576.000,   360.000), // = (  203 x 127  ) mm  = (  8.00 x 5.00  ) in
			'JLEGAL'                 => array(  576.000,   360.000), // = (  203 x 127  ) mm  = (  8.00 x 5.00  ) in
			// Other North American Paper Sizes
			'QUADDEMY'               => array( 2520.000,  3240.000), // = (  889 x 1143 ) mm  = ( 35.00 x 45.00 ) in
			'SUPER_B'                => array(  936.000,  1368.000), // = (  330 x 483  ) mm  = ( 13.00 x 19.00 ) in
			'QUARTO'                 => array(  648.000,   792.000), // = (  229 x 279  ) mm  = (  9.00 x 11.00 ) in
			'GOVERNMENTLEGAL'        => array(  612.000,   936.000), // = (  216 x 330  ) mm  = (  8.50 x 13.00 ) in
			'FOLIO'                  => array(  612.000,   936.000), // = (  216 x 330  ) mm  = (  8.50 x 13.00 ) in
			'MONARCH'                => array(  522.000,   756.000), // = (  184 x 267  ) mm  = (  7.25 x 10.50 ) in
			'EXECUTIVE'              => array(  522.000,   756.000), // = (  184 x 267  ) mm  = (  7.25 x 10.50 ) in
			'ORGANIZERL'             => array(  396.000,   612.000), // = (  140 x 216  ) mm  = (  5.50 x 8.50  ) in
			'STATEMENT'              => array(  396.000,   612.000), // = (  140 x 216  ) mm  = (  5.50 x 8.50  ) in
			'MEMO'                   => array(  396.000,   612.000), // = (  140 x 216  ) mm  = (  5.50 x 8.50  ) in
			'FOOLSCAP'               => array(  595.440,   936.000), // = (  210 x 330  ) mm  = (  8.27 x 13.00 ) in
			'COMPACT'                => array(  306.000,   486.000), // = (  108 x 171  ) mm  = (  4.25 x 6.75  ) in
			'ORGANIZERJ'             => array(  198.000,   360.000), // = (   70 x 127  ) mm  = (  2.75 x 5.00  ) in
			// Canadian standard CAN 2-9.60M
			'P1'                     => array( 1587.402,  2437.795), // = (  560 x 860  ) mm  = ( 22.05 x 33.86 ) in
			'P2'                     => array( 1218.898,  1587.402), // = (  430 x 560  ) mm  = ( 16.93 x 22.05 ) in
			'P3'                     => array(  793.701,  1218.898), // = (  280 x 430  ) mm  = ( 11.02 x 16.93 ) in
			'P4'                     => array(  609.449,   793.701), // = (  215 x 280  ) mm  = (  8.46 x 11.02 ) in
			'P5'                     => array(  396.850,   609.449), // = (  140 x 215  ) mm  = (  5.51 x 8.46  ) in
			'P6'                     => array(  303.307,   396.850), // = (  107 x 140  ) mm  = (  4.21 x 5.51  ) in
			// North American Architectural Sizes
			'ARCH_E'                 => array( 2592.000,  3456.000), // = (  914 x 1219 ) mm  = ( 36.00 x 48.00 ) in
			'ARCH_E1'                => array( 2160.000,  3024.000), // = (  762 x 1067 ) mm  = ( 30.00 x 42.00 ) in
			'ARCH_D'                 => array( 1728.000,  2592.000), // = (  610 x 914  ) mm  = ( 24.00 x 36.00 ) in
			'BROADSHEET'             => array( 1296.000,  1728.000), // = (  457 x 610  ) mm  = ( 18.00 x 24.00 ) in
			'ARCH_C'                 => array( 1296.000,  1728.000), // = (  457 x 610  ) mm  = ( 18.00 x 24.00 ) in
			'ARCH_B'                 => array(  864.000,  1296.000), // = (  305 x 457  ) mm  = ( 12.00 x 18.00 ) in
			'ARCH_A'                 => array(  648.000,   864.000), // = (  229 x 305  ) mm  = (  9.00 x 12.00 ) in
			// -- North American Envelope Sizes
			// - Announcement Envelopes
			'ANNENV_A2'              => array(  314.640,   414.000), // = (  111 x 146  ) mm  = (  4.37 x 5.75  ) in
			'ANNENV_A6'              => array(  342.000,   468.000), // = (  121 x 165  ) mm  = (  4.75 x 6.50  ) in
			'ANNENV_A7'              => array(  378.000,   522.000), // = (  133 x 184  ) mm  = (  5.25 x 7.25  ) in
			'ANNENV_A8'              => array(  396.000,   584.640), // = (  140 x 206  ) mm  = (  5.50 x 8.12  ) in
			'ANNENV_A10'             => array(  450.000,   692.640), // = (  159 x 244  ) mm  = (  6.25 x 9.62  ) in
			'ANNENV_SLIM'            => array(  278.640,   638.640), // = (   98 x 225  ) mm  = (  3.87 x 8.87  ) in
			// - Commercial Envelopes
			'COMMENV_N6_1/4'         => array(  252.000,   432.000), // = (   89 x 152  ) mm  = (  3.50 x 6.00  ) in
			'COMMENV_N6_3/4'         => array(  260.640,   468.000), // = (   92 x 165  ) mm  = (  3.62 x 6.50  ) in
			'COMMENV_N8'             => array(  278.640,   540.000), // = (   98 x 191  ) mm  = (  3.87 x 7.50  ) in
			'COMMENV_N9'             => array(  278.640,   638.640), // = (   98 x 225  ) mm  = (  3.87 x 8.87  ) in
			'COMMENV_N10'            => array(  296.640,   684.000), // = (  105 x 241  ) mm  = (  4.12 x 9.50  ) in
			'COMMENV_N11'            => array(  324.000,   746.640), // = (  114 x 263  ) mm  = (  4.50 x 10.37 ) in
			'COMMENV_N12'            => array(  342.000,   792.000), // = (  121 x 279  ) mm  = (  4.75 x 11.00 ) in
			'COMMENV_N14'            => array(  360.000,   828.000), // = (  127 x 292  ) mm  = (  5.00 x 11.50 ) in
			// - Catalogue Envelopes
			'CATENV_N1'              => array(  432.000,   648.000), // = (  152 x 229  ) mm  = (  6.00 x 9.00  ) in
			'CATENV_N1_3/4'          => array(  468.000,   684.000), // = (  165 x 241  ) mm  = (  6.50 x 9.50  ) in
			'CATENV_N2'              => array(  468.000,   720.000), // = (  165 x 254  ) mm  = (  6.50 x 10.00 ) in
			'CATENV_N3'              => array(  504.000,   720.000), // = (  178 x 254  ) mm  = (  7.00 x 10.00 ) in
			'CATENV_N6'              => array(  540.000,   756.000), // = (  191 x 267  ) mm  = (  7.50 x 10.50 ) in
			'CATENV_N7'              => array(  576.000,   792.000), // = (  203 x 279  ) mm  = (  8.00 x 11.00 ) in
			'CATENV_N8'              => array(  594.000,   810.000), // = (  210 x 286  ) mm  = (  8.25 x 11.25 ) in
			'CATENV_N9_1/2'          => array(  612.000,   756.000), // = (  216 x 267  ) mm  = (  8.50 x 10.50 ) in
			'CATENV_N9_3/4'          => array(  630.000,   810.000), // = (  222 x 286  ) mm  = (  8.75 x 11.25 ) in
			'CATENV_N10_1/2'         => array(  648.000,   864.000), // = (  229 x 305  ) mm  = (  9.00 x 12.00 ) in
			'CATENV_N12_1/2'         => array(  684.000,   900.000), // = (  241 x 318  ) mm  = (  9.50 x 12.50 ) in
			'CATENV_N13_1/2'         => array(  720.000,   936.000), // = (  254 x 330  ) mm  = ( 10.00 x 13.00 ) in
			'CATENV_N14_1/4'         => array(  810.000,   882.000), // = (  286 x 311  ) mm  = ( 11.25 x 12.25 ) in
			'CATENV_N14_1/2'         => array(  828.000,  1044.000), // = (  292 x 368  ) mm  = ( 11.50 x 14.50 ) in
			// Japanese (JIS P 0138-61) Standard B-Series
			'JIS_B0'                 => array( 2919.685,  4127.244), // = ( 1030 x 1456 ) mm  = ( 40.55 x 57.32 ) in
			'JIS_B1'                 => array( 2063.622,  2919.685), // = (  728 x 1030 ) mm  = ( 28.66 x 40.55 ) in
			'JIS_B2'                 => array( 1459.843,  2063.622), // = (  515 x 728  ) mm  = ( 20.28 x 28.66 ) in
			'JIS_B3'                 => array( 1031.811,  1459.843), // = (  364 x 515  ) mm  = ( 14.33 x 20.28 ) in
			'JIS_B4'                 => array(  728.504,  1031.811), // = (  257 x 364  ) mm  = ( 10.12 x 14.33 ) in
			'JIS_B5'                 => array(  515.906,   728.504), // = (  182 x 257  ) mm  = (  7.17 x 10.12 ) in
			'JIS_B6'                 => array(  362.835,   515.906), // = (  128 x 182  ) mm  = (  5.04 x 7.17  ) in
			'JIS_B7'                 => array(  257.953,   362.835), // = (   91 x 128  ) mm  = (  3.58 x 5.04  ) in
			'JIS_B8'                 => array(  181.417,   257.953), // = (   64 x 91   ) mm  = (  2.52 x 3.58  ) in
			'JIS_B9'                 => array(  127.559,   181.417), // = (   45 x 64   ) mm  = (  1.77 x 2.52  ) in
			'JIS_B10'                => array(   90.709,   127.559), // = (   32 x 45   ) mm  = (  1.26 x 1.77  ) in
			'JIS_B11'                => array(   62.362,    90.709), // = (   22 x 32   ) mm  = (  0.87 x 1.26  ) in
			'JIS_B12'                => array(   45.354,    62.362), // = (   16 x 22   ) mm  = (  0.63 x 0.87  ) in
			// PA Series
			'PA0'                    => array( 2381.102,  3174.803), // = (  840 x 1120 ) mm  = ( 33.07 x 44.09 ) in
			'PA1'                    => array( 1587.402,  2381.102), // = (  560 x 840  ) mm  = ( 22.05 x 33.07 ) in
			'PA2'                    => array( 1190.551,  1587.402), // = (  420 x 560  ) mm  = ( 16.54 x 22.05 ) in
			'PA3'                    => array(  793.701,  1190.551), // = (  280 x 420  ) mm  = ( 11.02 x 16.54 ) in
			'PA4'                    => array(  595.276,   793.701), // = (  210 x 280  ) mm  = (  8.27 x 11.02 ) in
			'PA5'                    => array(  396.850,   595.276), // = (  140 x 210  ) mm  = (  5.51 x 8.27  ) in
			'PA6'                    => array(  297.638,   396.850), // = (  105 x 140  ) mm  = (  4.13 x 5.51  ) in
			'PA7'                    => array(  198.425,   297.638), // = (   70 x 105  ) mm  = (  2.76 x 4.13  ) in
			'PA8'                    => array(  147.402,   198.425), // = (   52 x 70   ) mm  = (  2.05 x 2.76  ) in
			'PA9'                    => array(   99.213,   147.402), // = (   35 x 52   ) mm  = (  1.38 x 2.05  ) in
			'PA10'                   => array(   73.701,    99.213), // = (   26 x 35   ) mm  = (  1.02 x 1.38  ) in
			// Standard Photographic Print Sizes
			'PASSPORT_PHOTO'         => array(   99.213,   127.559), // = (   35 x 45   ) mm  = (  1.38 x 1.77  ) in
			'E'                      => array(  233.858,   340.157), // = (   82 x 120  ) mm  = (  3.25 x 4.72  ) in
			'L'                      => array(  252.283,   360.000), // = (   89 x 127  ) mm  = (  3.50 x 5.00  ) in
			'3R'                     => array(  252.283,   360.000), // = (   89 x 127  ) mm  = (  3.50 x 5.00  ) in
			'KG'                     => array(  289.134,   430.866), // = (  102 x 152  ) mm  = (  4.02 x 5.98  ) in
			'4R'                     => array(  289.134,   430.866), // = (  102 x 152  ) mm  = (  4.02 x 5.98  ) in
			'4D'                     => array(  340.157,   430.866), // = (  120 x 152  ) mm  = (  4.72 x 5.98  ) in
			'2L'                     => array(  360.000,   504.567), // = (  127 x 178  ) mm  = (  5.00 x 7.01  ) in
			'5R'                     => array(  360.000,   504.567), // = (  127 x 178  ) mm  = (  5.00 x 7.01  ) in
			'8P'                     => array(  430.866,   575.433), // = (  152 x 203  ) mm  = (  5.98 x 7.99  ) in
			'6R'                     => array(  430.866,   575.433), // = (  152 x 203  ) mm  = (  5.98 x 7.99  ) in
			'6P'                     => array(  575.433,   720.000), // = (  203 x 254  ) mm  = (  7.99 x 10.00 ) in
			'8R'                     => array(  575.433,   720.000), // = (  203 x 254  ) mm  = (  7.99 x 10.00 ) in
			'6PW'                    => array(  575.433,   864.567), // = (  203 x 305  ) mm  = (  7.99 x 12.01 ) in
			'S8R'                    => array(  575.433,   864.567), // = (  203 x 305  ) mm  = (  7.99 x 12.01 ) in
			'4P'                     => array(  720.000,   864.567), // = (  254 x 305  ) mm  = ( 10.00 x 12.01 ) in
			'10R'                    => array(  720.000,   864.567), // = (  254 x 305  ) mm  = ( 10.00 x 12.01 ) in
			'4PW'                    => array(  720.000,  1080.000), // = (  254 x 381  ) mm  = ( 10.00 x 15.00 ) in
			'S10R'                   => array(  720.000,  1080.000), // = (  254 x 381  ) mm  = ( 10.00 x 15.00 ) in
			'11R'                    => array(  790.866,  1009.134), // = (  279 x 356  ) mm  = ( 10.98 x 14.02 ) in
			'S11R'                   => array(  790.866,  1224.567), // = (  279 x 432  ) mm  = ( 10.98 x 17.01 ) in
			'12R'                    => array(  864.567,  1080.000), // = (  305 x 381  ) mm  = ( 12.01 x 15.00 ) in
			'S12R'                   => array(  864.567,  1292.598), // = (  305 x 456  ) mm  = ( 12.01 x 17.95 ) in
			// Common Newspaper Sizes
			'NEWSPAPER_BROADSHEET'   => array( 2125.984,  1700.787), // = (  750 x 600  ) mm  = ( 29.53 x 23.62 ) in
			'NEWSPAPER_BERLINER'     => array( 1332.283,   892.913), // = (  470 x 315  ) mm  = ( 18.50 x 12.40 ) in
			'NEWSPAPER_TABLOID'      => array( 1218.898,   793.701), // = (  430 x 280  ) mm  = ( 16.93 x 11.02 ) in
			'NEWSPAPER_COMPACT'      => array( 1218.898,   793.701), // = (  430 x 280  ) mm  = ( 16.93 x 11.02 ) in
			// Business Cards
			'CREDIT_CARD'            => array(  153.014,   242.646), // = (   54 x 86   ) mm  = (  2.13 x 3.37  ) in
			'BUSINESS_CARD'          => array(  153.014,   242.646), // = (   54 x 86   ) mm  = (  2.13 x 3.37  ) in
			'BUSINESS_CARD_ISO7810'  => array(  153.014,   242.646), // = (   54 x 86   ) mm  = (  2.13 x 3.37  ) in
			'BUSINESS_CARD_ISO216'   => array(  147.402,   209.764), // = (   52 x 74   ) mm  = (  2.05 x 2.91  ) in
			'BUSINESS_CARD_IT'       => array(  155.906,   240.945), // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
			'BUSINESS_CARD_UK'       => array(  155.906,   240.945), // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
			'BUSINESS_CARD_FR'       => array(  155.906,   240.945), // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
			'BUSINESS_CARD_DE'       => array(  155.906,   240.945), // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
			'BUSINESS_CARD_ES'       => array(  155.906,   240.945), // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
			'BUSINESS_CARD_CA'       => array(  144.567,   252.283), // = (   51 x 89   ) mm  = (  2.01 x 3.50  ) in
			'BUSINESS_CARD_US'       => array(  144.567,   252.283), // = (   51 x 89   ) mm  = (  2.01 x 3.50  ) in
			'BUSINESS_CARD_JP'       => array(  155.906,   257.953), // = (   55 x 91   ) mm  = (  2.17 x 3.58  ) in
			'BUSINESS_CARD_HK'       => array(  153.071,   255.118), // = (   54 x 90   ) mm  = (  2.13 x 3.54  ) in
			'BUSINESS_CARD_AU'       => array(  155.906,   255.118), // = (   55 x 90   ) mm  = (  2.17 x 3.54  ) in
			'BUSINESS_CARD_DK'       => array(  155.906,   255.118), // = (   55 x 90   ) mm  = (  2.17 x 3.54  ) in
			'BUSINESS_CARD_SE'       => array(  155.906,   255.118), // = (   55 x 90   ) mm  = (  2.17 x 3.54  ) in
			'BUSINESS_CARD_RU'       => array(  141.732,   255.118), // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
			'BUSINESS_CARD_CZ'       => array(  141.732,   255.118), // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
			'BUSINESS_CARD_FI'       => array(  141.732,   255.118), // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
			'BUSINESS_CARD_HU'       => array(  141.732,   255.118), // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
			'BUSINESS_CARD_IL'       => array(  141.732,   255.118), // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
			// Billboards
			'4SHEET'                 => array( 2880.000,  4320.000), // = ( 1016 x 1524 ) mm  = ( 40.00 x 60.00 ) in
			'6SHEET'                 => array( 3401.575,  5102.362), // = ( 1200 x 1800 ) mm  = ( 47.24 x 70.87 ) in
			'12SHEET'                => array( 8640.000,  4320.000), // = ( 3048 x 1524 ) mm  = (120.00 x 60.00 ) in
			'16SHEET'                => array( 5760.000,  8640.000), // = ( 2032 x 3048 ) mm  = ( 80.00 x 120.00) in
			'32SHEET'                => array(11520.000,  8640.000), // = ( 4064 x 3048 ) mm  = (160.00 x 120.00) in
			'48SHEET'                => array(17280.000,  8640.000), // = ( 6096 x 3048 ) mm  = (240.00 x 120.00) in
			'64SHEET'                => array(23040.000,  8640.000), // = ( 8128 x 3048 ) mm  = (320.00 x 120.00) in
			'96SHEET'                => array(34560.000,  8640.000), // = (12192 x 3048 ) mm  = (480.00 x 120.00) in
			// -- Old European Sizes
			// - Old Imperial English Sizes
			'EN_EMPEROR'             => array( 3456.000,  5184.000), // = ( 1219 x 1829 ) mm  = ( 48.00 x 72.00 ) in
			'EN_ANTIQUARIAN'         => array( 2232.000,  3816.000), // = (  787 x 1346 ) mm  = ( 31.00 x 53.00 ) in
			'EN_GRAND_EAGLE'         => array( 2070.000,  3024.000), // = (  730 x 1067 ) mm  = ( 28.75 x 42.00 ) in
			'EN_DOUBLE_ELEPHANT'     => array( 1926.000,  2880.000), // = (  679 x 1016 ) mm  = ( 26.75 x 40.00 ) in
			'EN_ATLAS'               => array( 1872.000,  2448.000), // = (  660 x 864  ) mm  = ( 26.00 x 34.00 ) in
			'EN_COLOMBIER'           => array( 1692.000,  2484.000), // = (  597 x 876  ) mm  = ( 23.50 x 34.50 ) in
			'EN_ELEPHANT'            => array( 1656.000,  2016.000), // = (  584 x 711  ) mm  = ( 23.00 x 28.00 ) in
			'EN_DOUBLE_DEMY'         => array( 1620.000,  2556.000), // = (  572 x 902  ) mm  = ( 22.50 x 35.50 ) in
			'EN_IMPERIAL'            => array( 1584.000,  2160.000), // = (  559 x 762  ) mm  = ( 22.00 x 30.00 ) in
			'EN_PRINCESS'            => array( 1548.000,  2016.000), // = (  546 x 711  ) mm  = ( 21.50 x 28.00 ) in
			'EN_CARTRIDGE'           => array( 1512.000,  1872.000), // = (  533 x 660  ) mm  = ( 21.00 x 26.00 ) in
			'EN_DOUBLE_LARGE_POST'   => array( 1512.000,  2376.000), // = (  533 x 838  ) mm  = ( 21.00 x 33.00 ) in
			'EN_ROYAL'               => array( 1440.000,  1800.000), // = (  508 x 635  ) mm  = ( 20.00 x 25.00 ) in
			'EN_SHEET'               => array( 1404.000,  1692.000), // = (  495 x 597  ) mm  = ( 19.50 x 23.50 ) in
			'EN_HALF_POST'           => array( 1404.000,  1692.000), // = (  495 x 597  ) mm  = ( 19.50 x 23.50 ) in
			'EN_SUPER_ROYAL'         => array( 1368.000,  1944.000), // = (  483 x 686  ) mm  = ( 19.00 x 27.00 ) in
			'EN_DOUBLE_POST'         => array( 1368.000,  2196.000), // = (  483 x 775  ) mm  = ( 19.00 x 30.50 ) in
			'EN_MEDIUM'              => array( 1260.000,  1656.000), // = (  445 x 584  ) mm  = ( 17.50 x 23.00 ) in
			'EN_DEMY'                => array( 1260.000,  1620.000), // = (  445 x 572  ) mm  = ( 17.50 x 22.50 ) in
			'EN_LARGE_POST'          => array( 1188.000,  1512.000), // = (  419 x 533  ) mm  = ( 16.50 x 21.00 ) in
			'EN_COPY_DRAUGHT'        => array( 1152.000,  1440.000), // = (  406 x 508  ) mm  = ( 16.00 x 20.00 ) in
			'EN_POST'                => array( 1116.000,  1386.000), // = (  394 x 489  ) mm  = ( 15.50 x 19.25 ) in
			'EN_CROWN'               => array( 1080.000,  1440.000), // = (  381 x 508  ) mm  = ( 15.00 x 20.00 ) in
			'EN_PINCHED_POST'        => array( 1062.000,  1332.000), // = (  375 x 470  ) mm  = ( 14.75 x 18.50 ) in
			'EN_BRIEF'               => array(  972.000,  1152.000), // = (  343 x 406  ) mm  = ( 13.50 x 16.00 ) in
			'EN_FOOLSCAP'            => array(  972.000,  1224.000), // = (  343 x 432  ) mm  = ( 13.50 x 17.00 ) in
			'EN_SMALL_FOOLSCAP'      => array(  954.000,  1188.000), // = (  337 x 419  ) mm  = ( 13.25 x 16.50 ) in
			'EN_POTT'                => array(  900.000,  1080.000), // = (  318 x 381  ) mm  = ( 12.50 x 15.00 ) in
			// - Old Imperial Belgian Sizes
			'BE_GRAND_AIGLE'         => array( 1984.252,  2948.031), // = (  700 x 1040 ) mm  = ( 27.56 x 40.94 ) in
			'BE_COLOMBIER'           => array( 1757.480,  2409.449), // = (  620 x 850  ) mm  = ( 24.41 x 33.46 ) in
			'BE_DOUBLE_CARRE'        => array( 1757.480,  2607.874), // = (  620 x 920  ) mm  = ( 24.41 x 36.22 ) in
			'BE_ELEPHANT'            => array( 1746.142,  2182.677), // = (  616 x 770  ) mm  = ( 24.25 x 30.31 ) in
			'BE_PETIT_AIGLE'         => array( 1700.787,  2381.102), // = (  600 x 840  ) mm  = ( 23.62 x 33.07 ) in
			'BE_GRAND_JESUS'         => array( 1559.055,  2069.291), // = (  550 x 730  ) mm  = ( 21.65 x 28.74 ) in
			'BE_JESUS'               => array( 1530.709,  2069.291), // = (  540 x 730  ) mm  = ( 21.26 x 28.74 ) in
			'BE_RAISIN'              => array( 1417.323,  1842.520), // = (  500 x 650  ) mm  = ( 19.69 x 25.59 ) in
			'BE_GRAND_MEDIAN'        => array( 1303.937,  1714.961), // = (  460 x 605  ) mm  = ( 18.11 x 23.82 ) in
			'BE_DOUBLE_POSTE'        => array( 1233.071,  1601.575), // = (  435 x 565  ) mm  = ( 17.13 x 22.24 ) in
			'BE_COQUILLE'            => array( 1218.898,  1587.402), // = (  430 x 560  ) mm  = ( 16.93 x 22.05 ) in
			'BE_PETIT_MEDIAN'        => array( 1176.378,  1502.362), // = (  415 x 530  ) mm  = ( 16.34 x 20.87 ) in
			'BE_RUCHE'               => array( 1020.472,  1303.937), // = (  360 x 460  ) mm  = ( 14.17 x 18.11 ) in
			'BE_PROPATRIA'           => array(  977.953,  1218.898), // = (  345 x 430  ) mm  = ( 13.58 x 16.93 ) in
			'BE_LYS'                 => array(  898.583,  1125.354), // = (  317 x 397  ) mm  = ( 12.48 x 15.63 ) in
			'BE_POT'                 => array(  870.236,  1088.504), // = (  307 x 384  ) mm  = ( 12.09 x 15.12 ) in
			'BE_ROSETTE'             => array(  765.354,   983.622), // = (  270 x 347  ) mm  = ( 10.63 x 13.66 ) in
			// - Old Imperial French Sizes
			'FR_UNIVERS'             => array( 2834.646,  3685.039), // = ( 1000 x 1300 ) mm  = ( 39.37 x 51.18 ) in
			'FR_DOUBLE_COLOMBIER'    => array( 2551.181,  3571.654), // = (  900 x 1260 ) mm  = ( 35.43 x 49.61 ) in
			'FR_GRANDE_MONDE'        => array( 2551.181,  3571.654), // = (  900 x 1260 ) mm  = ( 35.43 x 49.61 ) in
			'FR_DOUBLE_SOLEIL'       => array( 2267.717,  3401.575), // = (  800 x 1200 ) mm  = ( 31.50 x 47.24 ) in
			'FR_DOUBLE_JESUS'        => array( 2154.331,  3174.803), // = (  760 x 1120 ) mm  = ( 29.92 x 44.09 ) in
			'FR_GRAND_AIGLE'         => array( 2125.984,  3004.724), // = (  750 x 1060 ) mm  = ( 29.53 x 41.73 ) in
			'FR_PETIT_AIGLE'         => array( 1984.252,  2664.567), // = (  700 x 940  ) mm  = ( 27.56 x 37.01 ) in
			'FR_DOUBLE_RAISIN'       => array( 1842.520,  2834.646), // = (  650 x 1000 ) mm  = ( 25.59 x 39.37 ) in
			'FR_JOURNAL'             => array( 1842.520,  2664.567), // = (  650 x 940  ) mm  = ( 25.59 x 37.01 ) in
			'FR_COLOMBIER_AFFICHE'   => array( 1785.827,  2551.181), // = (  630 x 900  ) mm  = ( 24.80 x 35.43 ) in
			'FR_DOUBLE_CAVALIER'     => array( 1757.480,  2607.874), // = (  620 x 920  ) mm  = ( 24.41 x 36.22 ) in
			'FR_CLOCHE'              => array( 1700.787,  2267.717), // = (  600 x 800  ) mm  = ( 23.62 x 31.50 ) in
			'FR_SOLEIL'              => array( 1700.787,  2267.717), // = (  600 x 800  ) mm  = ( 23.62 x 31.50 ) in
			'FR_DOUBLE_CARRE'        => array( 1587.402,  2551.181), // = (  560 x 900  ) mm  = ( 22.05 x 35.43 ) in
			'FR_DOUBLE_COQUILLE'     => array( 1587.402,  2494.488), // = (  560 x 880  ) mm  = ( 22.05 x 34.65 ) in
			'FR_JESUS'               => array( 1587.402,  2154.331), // = (  560 x 760  ) mm  = ( 22.05 x 29.92 ) in
			'FR_RAISIN'              => array( 1417.323,  1842.520), // = (  500 x 650  ) mm  = ( 19.69 x 25.59 ) in
			'FR_CAVALIER'            => array( 1303.937,  1757.480), // = (  460 x 620  ) mm  = ( 18.11 x 24.41 ) in
			'FR_DOUBLE_COURONNE'     => array( 1303.937,  2040.945), // = (  460 x 720  ) mm  = ( 18.11 x 28.35 ) in
			'FR_CARRE'               => array( 1275.591,  1587.402), // = (  450 x 560  ) mm  = ( 17.72 x 22.05 ) in
			'FR_COQUILLE'            => array( 1247.244,  1587.402), // = (  440 x 560  ) mm  = ( 17.32 x 22.05 ) in
			'FR_DOUBLE_TELLIERE'     => array( 1247.244,  1927.559), // = (  440 x 680  ) mm  = ( 17.32 x 26.77 ) in
			'FR_DOUBLE_CLOCHE'       => array( 1133.858,  1700.787), // = (  400 x 600  ) mm  = ( 15.75 x 23.62 ) in
			'FR_DOUBLE_POT'          => array( 1133.858,  1757.480), // = (  400 x 620  ) mm  = ( 15.75 x 24.41 ) in
			'FR_ECU'                 => array( 1133.858,  1474.016), // = (  400 x 520  ) mm  = ( 15.75 x 20.47 ) in
			'FR_COURONNE'            => array( 1020.472,  1303.937), // = (  360 x 460  ) mm  = ( 14.17 x 18.11 ) in
			'FR_TELLIERE'            => array(  963.780,  1247.244), // = (  340 x 440  ) mm  = ( 13.39 x 17.32 ) in
			'FR_POT'                 => array(  878.740,  1133.858), // = (  310 x 400  ) mm  = ( 12.20 x 15.75 ) in
		);
	}

}

endif;

return new Alg_WC_EAN_Settings_Print();
