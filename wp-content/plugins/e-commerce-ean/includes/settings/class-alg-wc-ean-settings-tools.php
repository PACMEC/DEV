<?php
/**
 * EAN for WooCommerce - Tools Section Settings
 *
 * @version 2.7.0
 * @since   2.2.5
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_EAN_Settings_Tools' ) ) :

class Alg_WC_EAN_Settings_Tools extends Alg_WC_EAN_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.2.5
	 * @since   2.2.5
	 */
	function __construct() {
		$this->id   = 'tools';
		$this->desc = __( 'Tools', 'ean-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.7.0
	 * @since   2.2.5
	 *
	 * @todo    [now] (desc) `seed_prefix`
	 * @todo    [next] (desc) "Order Tools": add info about "General > Orders" options (i.e. "Add EAN to new order items meta", etc.)
	 * @todo    [maybe] (desc) better desc for all tools?
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'    => __( 'Tools', 'ean-for-woocommerce' ),
				'desc'     => sprintf( __( 'Check the %s box and "Save changes" to run the tool. Please note that there is no undo for these tools.', 'ean-for-woocommerce' ),
					'<span class="dashicons dashicons-admin-generic"></span>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_tools',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_tools',
			),
			array(
				'title'    => __( 'Product Tools', 'ean-for-woocommerce' ),
				'desc'     => sprintf( __( 'Please note that %s and %s tools will <strong>not</strong> overwrite EANs for products with existing EANs.', 'ean-for-woocommerce' ),
						'<strong>' . __( 'Generate', 'ean-for-woocommerce' ) . '</strong>',
						'<strong>' . __( 'Copy', 'ean-for-woocommerce' ) . '</strong>' ) . ' ' .
					sprintf( __( 'You can use the %s tool to clear the existing EANs before generating or copying.', 'ean-for-woocommerce' ),
						'<strong>' . __( 'Delete', 'ean-for-woocommerce' ) . '</strong>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_tools_products',
			),
			array(
				'title'    => __( 'Generate', 'ean-for-woocommerce' ),
				'desc'     => '<span class="dashicons dashicons-admin-generic"></span> ' . __( 'Generate EAN for all products', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_tool_product[generate]',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Type', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_tool_product_generate[type]',
				'default'  => 'EAN13',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'EAN8'  => 'EAN-8',
					'UPCA'  => 'UPC-A',
					'EAN13' => 'EAN-13',
				),
			),
			array(
				'desc'     => __( 'Country prefix (from)', 'ean-for-woocommerce' ) . ' ' .
					sprintf( '<a target="_blank" title="%s" style="text-decoration:none;" href="%s">%s</a>',
						__( 'List of GS1 country codes.', 'ean-for-woocommerce' ),
						'https://en.wikipedia.org/wiki/List_of_GS1_country_codes',
						'<span class="dashicons dashicons-external"></span>' ),
				'id'       => 'alg_wc_ean_tool_product_generate[prefix]',
				'default'  => 200,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0, 'max' => 999 ),
			),
			array(
				'desc'     => __( 'County prefix (to)', 'ean-for-woocommerce' ) . ' (' . __( 'optional', 'ean-for-woocommerce' ) . ')',
				'desc_tip' => sprintf( __( 'If set, prefix will be generated randomly between "%s" and "%s" values.', 'ean-for-woocommerce' ),
					__( 'Prefix from', 'ean-for-woocommerce' ), __( 'Prefix to', 'ean-for-woocommerce' ) ),
				'id'       => 'alg_wc_ean_tool_product_generate[prefix_to]',
				'default'  => '',
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0, 'max' => 999 ),
			),
			array(
				'desc'     => __( 'Seed prefix', 'ean-for-woocommerce' ) . ' (' . __( 'optional', 'ean-for-woocommerce' ) . ')',
				'id'       => 'alg_wc_ean_tool_product_generate[seed_prefix]',
				'default'  => '',
				'type'     => 'text',
				'custom_attributes' => array( 'pattern' => '[0-9]+' ),
			),
			array(
				'desc'     => __( 'Automatically generate EAN for new products', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_tool_product_generate_on[insert_product]',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'Automatically generate EAN on product update', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_tool_product_generate_on[update_product]',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'title'    => __( 'Copy', 'ean-for-woocommerce' ),
				'desc'     => '<span class="dashicons dashicons-admin-generic"></span> ' . __( 'Copy EAN from product SKU for all products', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_tool_product[copy_sku]',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => '<span class="dashicons dashicons-admin-generic"></span> ' . __( 'Copy EAN from product ID for all products', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_tool_product[copy_id]',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => '<span class="dashicons dashicons-admin-generic"></span> ' . __( 'Copy EAN from product meta for all products', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_tool_product[copy_meta]',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => sprintf( __( 'Meta key, e.g. %s', 'ean-for-woocommerce' ), '<code>_gtin</code>' ),
				'desc_tip' => __( 'Product meta key to copy from.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_tool_product_copy_meta[key]',
				'default'  => '',
				'type'     => 'text',
			),
			array(
				'title'    => __( 'Delete', 'ean-for-woocommerce' ),
				'desc'     => '<span class="dashicons dashicons-admin-generic"></span> ' . __( 'Delete all EANs for all products', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_tool_delete_product_meta',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( '"Products > Bulk actions"', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Sets actions to be added to the "Products > Bulk actions" dropdown.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_product_bulk_actions',
				'default'  => array( 'alg_wc_ean_delete', 'alg_wc_ean_generate' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => array(
					'alg_wc_ean_generate' => __( 'Generate EAN', 'order-minimum-amount-for-woocommerce' ),
					'alg_wc_ean_delete'   => __( 'Delete EAN', 'order-minimum-amount-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_tools_products',
			),
			array(
				'title'    => __( 'Order Tools', 'ean-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_tools_orders',
			),
			array(
				'title'    => __( 'Add', 'ean-for-woocommerce' ),
				'desc'     => '<span class="dashicons dashicons-admin-generic"></span> ' . __( 'Add EANs to all order items', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_tool_orders_add',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Delete', 'ean-for-woocommerce' ),
				'desc'     => '<span class="dashicons dashicons-admin-generic"></span> ' . __( 'Delete EANs from all order items', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_tool_orders_delete',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_tools_orders',
			),
		);
		return $settings;
	}

}

endif;

return new Alg_WC_EAN_Settings_Tools();
