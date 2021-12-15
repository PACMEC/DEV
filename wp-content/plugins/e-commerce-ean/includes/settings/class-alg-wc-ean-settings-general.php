<?php
/**
 * EAN for WooCommerce - General Section Settings
 *
 * @version 2.4.1
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_EAN_Settings_General' ) ) :

class Alg_WC_EAN_Settings_General extends Alg_WC_EAN_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'ean-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.4.1
	 * @since   1.0.0
	 *
	 * @see     https://www.keyence.com/ss/products/auto_id/barcode_lecture/basic/barcode-types/
	 *
	 * @todo    [next] [!] (desc) remove "This will" everywhere
	 * @todo    [next] (dev) `alg_wc_ean_wcfm_hints`: better default value?
	 * @todo    [next] (dev) `alg_wc_ean_order_items_meta_admin`: default to `yes` || merge with `alg_wc_ean_order_items_meta`
	 * @todo    [next] (dev) `alg_wc_ean_order_items_meta`: default to `yes`
	 * @todo    [next] (desc) `alg_wc_ean_order_items_meta_admin`: better desc
	 * @todo    [next] (desc) `alg_wc_ean_order_items_meta`: better desc
	 * @todo    [later] (dev) `$single_product_page_positions`: add more options, and maybe add `custom` hook option?
	 * @todo    [next] (desc) `alg_wc_ean_frontend_positions_priorities`: better desc, e.g. add "known priorities"
	 * @todo    [maybe] (desc) `alg_wc_ean_frontend_search_ajax_flatsome`: add link to the theme?
	 * @todo    [next] (desc) Type: add more info (and maybe links) about all types
	 * @todo    [later] (desc) add shortcode examples
	 * @todo    [maybe] (desc) Type: rename to "Standard"?
	 * @todo    [maybe] (desc) Shop pages: better title/desc?
	 * @todo    [maybe] (desc) Cart: better desc?
	 * @todo    [maybe] (desc) `$wcdn_settings`: better desc?
	 * @todo    [maybe] (dev) `alg_wc_ean_backend_position`: add more positions?
	 * @todo    [maybe] (dev) `alg_wc_ean_backend_search_ajax`: remove (i.e. always `yes`)?
	 */
	function get_settings() {

		$single_product_page_positions = array(
			'woocommerce_product_meta_start'            => __( 'Product meta start', 'ean-for-woocommerce' ),
			'woocommerce_product_meta_end'              => __( 'Product meta end', 'ean-for-woocommerce' ),
			'woocommerce_before_single_product'         => __( 'Before single product', 'ean-for-woocommerce' ),
			'woocommerce_before_single_product_summary' => __( 'Before single product summary', 'ean-for-woocommerce' ),
			'woocommerce_single_product_summary'        => __( 'Single product summary', 'ean-for-woocommerce' ),
			'woocommerce_after_single_product_summary'  => __( 'After single product summary', 'ean-for-woocommerce' ),
			'woocommerce_after_single_product'          => __( 'After single product', 'ean-for-woocommerce' ),
		);

		$settings = array(
			array(
				'title'    => __( 'EAN Options', 'ean-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_plugin_options',
			),
			array(
				'title'    => __( 'EAN', 'ean-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable plugin', 'ean-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_ean_plugin_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Type', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'The "Type" will be used for: %s', 'ean-for-woocommerce' ),
						'<br><br>' . implode( ',<br><br>', array(
							__( 'EAN validation (on the admin product edit pages, and in the admin products column)', 'ean-for-woocommerce' ),
							__( 'EAN input pattern (on the admin product edit pages)', 'ean-for-woocommerce' ),
							__( 'product structured data (e.g. for Google Search Console)', 'ean-for-woocommerce' ),
							__( 'outputting 1D barcodes', 'ean-for-woocommerce' ),
						) ) . '.'
					),
				'id'       => 'alg_wc_ean_type',
				'default'  => 'EAN13',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'AUTO'  => __( 'Automatic', 'ean-for-woocommerce' ) . ' (' . implode( ', ', array( 'EAN-13', 'UPC-A', 'EAN-8' ) ) . ')',
					'EAN8'  => 'EAN-8',
					'UPCA'  => 'UPC-A',
					'EAN13' => 'EAN-13',
					'C128'  => 'CODE 128',
				),
			),
			array(
				'title'    => __( 'Title', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'This title will be used for the EAN input fields on admin product edit pages, in admin products list column, etc.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_title',
				'default'  => __( 'EAN', 'ean-for-woocommerce' ),
				'type'     => 'text',
			),
			array(
				'title'    => __( 'Admin product edit page', 'ean-for-woocommerce' ),
				'desc'     => __( 'Position', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Set to which product data tab EAN field should be added.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_backend_position',
				'default'  => 'woocommerce_product_options_sku',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'woocommerce_product_options_general_product_data'   => __( 'General', 'ean-for-woocommerce' ),
					'woocommerce_product_options_inventory_product_data' => __( 'Inventory', 'ean-for-woocommerce' ),
					'woocommerce_product_options_sku'                    => __( 'Inventory: SKU', 'ean-for-woocommerce' ),
					'woocommerce_product_options_advanced'               => __( 'Advanced', 'ean-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Admin search', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'This will enable searching by EAN in admin area.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_backend_search',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'AJAX search', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'This will enable searching by EAN in AJAX.', 'ean-for-woocommerce' ) . ' ' .
					__( 'E.g. when searching for a product when creating new order in admin area.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_backend_search_ajax',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'title'    => __( 'Admin products list column', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'This will add "%s" column to %s.', 'ean-for-woocommerce' ),
					get_option( 'alg_wc_ean_title', __( 'EAN', 'ean-for-woocommerce' ) ),
					'<a href="' . admin_url( 'edit.php?post_type=product' ) . '">' . __( 'admin products list', 'ean-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_ean_backend_column',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'Validate', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Validate EAN in column.', 'ean-for-woocommerce' ) . ' ' . __( 'Invalid EANs will be marked red.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_backend_column_validate',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'title'    => __( 'Orders', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Add EAN to new order items meta.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_order_items_meta',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'Admin order', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Add EAN to new order items meta for orders created by admin.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_order_items_meta_admin',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'title'    => __( 'Single product page', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'This will show EAN on single product page on frontend.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_frontend',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Template', 'ean-for-woocommerce' ),
				'desc_tip' =>  sprintf( __( 'Available placeholder: %s.', 'ean-for-woocommerce' ), '%ean%' ),
				'id'       => 'alg_wc_ean_template',
				'default'  => __( 'EAN: %ean%', 'ean-for-woocommerce' ),
				'type'     => 'textarea',
			),
			array(
				'desc'     => __( 'Positions', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'You can select multiple positions at once.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_frontend_positions',
				'default'  => array( 'woocommerce_product_meta_start' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $single_product_page_positions,
			),
		);
		foreach ( get_option( 'alg_wc_ean_frontend_positions', array( 'woocommerce_product_meta_start' ) ) as $position ) {
			$position_title = ( isset( $single_product_page_positions[ $position ] ) ? $single_product_page_positions[ $position ] : $position );
			$settings = array_merge( $settings, array(
				array(
					'desc'     => sprintf( __( 'Position priority: "%s"', 'ean-for-woocommerce' ), $position_title ),
					'desc_tip' => __( 'Fine-tune the position.', 'ean-for-woocommerce' ),
					'id'       => "alg_wc_ean_frontend_positions_priorities[{$position}]",
					'default'  => 10,
					'type'     => 'number',
				),
			) );
		}
		$settings = array_merge( $settings, array(
			array(
				'desc'     => __( 'Variable products: Position in variation', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_frontend_variation_position',
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
				'desc_tip' => __( 'This will show EAN on shop (e.g. category) pages on frontend.', 'ean-for-woocommerce' ) . ' ' . $this->variable_products_note(),
				'id'       => 'alg_wc_ean_frontend_loop',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Cart', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'This will show EAN on cart page on frontend.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_frontend_cart',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Search', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'This will enable searching by EAN on frontend.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_frontend_search',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( '"Flatsome" theme', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'This will enable searching by EAN in "Flatsome" theme\'s "LIVE SEARCH".', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_frontend_search_ajax_flatsome',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'title'    => __( 'Product structured data', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_frontend_product_structured_data',
				'desc_tip' => __( 'This will add EAN to the product structured data, e.g. for Google Search Console.', 'ean-for-woocommerce' ),
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Order items table', 'ean-for-woocommerce' ),
				'desc'     => __( 'Enable', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'This will show EAN in order items table.', 'ean-for-woocommerce' ) . ' ' .
					__( 'This will affect all places where order items table is displayed, including <strong>emails</strong> (both admin and customer), <strong>"thank you" page</strong> (i.e. "order received" page), <strong>"view order" page</strong> (in "my account").', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_order_items_table',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_plugin_options',
			),
			array(
				'title'    => __( 'Notes', 'ean-for-woocommerce' ),
				'desc'     => implode( '<br>', array(
					'<span class="dashicons dashicons-info"></span> ' . sprintf( __( 'You can also output EAN with %s shortcode.', 'ean-for-woocommerce' ),
						'<code>[alg_wc_ean]</code>' ),
					'<span class="dashicons dashicons-info"></span> ' . sprintf( __( 'EAN is stored in product meta with %s key. You may need this for some third-party plugins, e.g. for product import.', 'ean-for-woocommerce' ),
						'<code>' . alg_wc_ean()->core->ean_key . '</code>' ),
				) ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_notes',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_notes',
			),
		) );

		return $settings;
	}

}

endif;

return new Alg_WC_EAN_Settings_General();
