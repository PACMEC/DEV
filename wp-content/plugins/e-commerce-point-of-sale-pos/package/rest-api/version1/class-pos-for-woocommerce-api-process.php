<?php
/**
 * Fired during plugin activation
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Pos_For_Woocommerce_Api_Process' ) ) {

	/**
	 * The plugin API class.
	 *
	 * This is used to define the functions and data manipulation for custom endpoints.
	 *
	 * @since      1.0.0
	 * @package    MWB_Point_Of_Sale_Woocommerce
	 * @subpackage MWB_Point_Of_Sale_Woocommerce/includes
	 * @author     MakeWebBetter <makewebbetter.com>
	 */
	class Pos_For_Woocommerce_Api_Process {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

		}

		/**
		 * Define the function to process data for custom endpoint.
		 *
		 * @since    1.0.0
		 * @param   Array $pfw_request  data of requesting headers and other information.
		 * @return  Array $mwb_pfw_rest_response    returns processed data and status of operations.
		 */
		public function mwb_pos_default_process( $pfw_request ) {
			$mwb_pfw_rest_response = array();

			// Write your custom code here.

			$mwb_pfw_rest_response['status'] = 200;
			$mwb_pfw_rest_response['data']   = $pfw_request->get_headers();
			return $mwb_pfw_rest_response;
		}
	}
}
