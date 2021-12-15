<?php
/**
 * EAN for WooCommerce - Advanced Section Settings
 *
 * @version 2.2.9
 * @since   2.2.9
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_EAN_Settings_Advanced' ) ) :

class Alg_WC_EAN_Settings_Advanced extends Alg_WC_EAN_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.2.9
	 * @since   2.2.9
	 */
	function __construct() {
		$this->id   = 'advanced';
		$this->desc = __( 'Advanced', 'ean-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.2.9
	 * @since   2.2.9
	 *
	 * @todo    [later] (desc) `alg_wc_ean_js_variations_form_closest`: better desc
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Advanced Options', 'ean-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ean_advanced_options',
			),
			array(
				'title'    => __( 'JS selector in variation', 'ean-for-woocommerce' ),
				'desc'     => sprintf( __( 'This is used only if "%s" option is set to "%s".', 'ean-for-woocommerce' ),
						__( 'Variable products: Position in variation', 'ean-for-woocommerce' ), __( 'Product meta', 'ean-for-woocommerce' ) ) . ' ' .
					__( 'Leave at the default value if unsure.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_ean_js_variations_form_closest',
				'default'  => '.summary',
				'type'     => 'text',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ean_advanced_options',
			),
		);
	}

}

endif;

return new Alg_WC_EAN_Settings_Advanced();
