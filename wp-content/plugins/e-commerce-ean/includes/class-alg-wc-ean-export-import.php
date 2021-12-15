<?php
/**
 * EAN for WooCommerce - Export Import Class
 *
 * @version 2.0.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_EAN_Export_Import' ) ) :

class Alg_WC_EAN_Export_Import {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {
		// Export
		add_filter( 'woocommerce_product_export_column_names',                   array( $this, 'add_export_column' ) );
		add_filter( 'woocommerce_product_export_product_default_columns',        array( $this, 'add_export_column' ) );
		add_filter( 'woocommerce_product_export_product_column_alg_ean',         array( $this, 'add_export_data' ), 10, 2 );
		// Import
		add_filter( 'woocommerce_csv_product_import_mapping_options',            array( $this, 'add_import_mapping_option' ) );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns',    array( $this, 'set_import_mapping_option_default' ) );
		add_filter( 'woocommerce_product_importer_parsed_data',                  array( $this, 'parse_import_data' ), 10, 2 );
	}

	/**
	 * parse_import_data.
	 *
	 * @version 2.0.0
	 * @since   1.5.0
	 */
	function parse_import_data( $data, $importer ) {
		if ( isset( $data['alg_ean'] ) ) {
			if ( ! isset( $data['meta_data'] ) ) {
				$data['meta_data'] = array();
			}
			$data['meta_data'][] = array(
				'key'   => alg_wc_ean()->core->ean_key,
				'value' => $data['alg_ean'],
			);
			unset( $data['alg_ean'] );
		}
		return $data;
	}

	/**
	 * set_import_mapping_option_default.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 *
	 * @todo    [maybe] (dev) use `alg_wc_ean_title` option value instead of 'EAN' (same in `add_import_mapping_option()` and `add_export_column()`)?
	 */
	function set_import_mapping_option_default( $columns ) {
		$columns['EAN'] = 'alg_ean';
		return $columns;
	}

	/**
	 * add_import_mapping_option.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function add_import_mapping_option( $options ) {
		$options['alg_ean'] = 'EAN';
		return $options;
	}

	/**
	 * add_export_column.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function add_export_column( $columns ) {
		$columns['alg_ean'] = 'EAN';
		return $columns;
	}

	/**
	 * add_export_data.
	 *
	 * @version 2.0.0
	 * @since   1.5.0
	 */
	function add_export_data( $value, $product ) {
		return alg_wc_ean()->core->get_ean( $product->get_id() );
	}

}

endif;

return new Alg_WC_EAN_Export_Import();
