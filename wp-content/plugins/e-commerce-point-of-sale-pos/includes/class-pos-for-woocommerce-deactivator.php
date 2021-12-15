<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Pos_For_Woocommerce_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function pos_for_woocommerce_deactivate() {
		$mwb_pos_page = get_option( 'mwb_pos_page_exists', false );
		if ( isset( $mwb_pos_page ) && '' !== $mwb_pos_page ) {
			$mwb_page_exists = get_page_by_path( 'point-of-sale' );
			if ( isset( $mwb_page_exists->ID ) && '' !== $mwb_page_exists->ID ) {
				wp_delete_post( $mwb_page_exists->ID );
				delete_option( 'mwb_pos_page_exists' );
			}
		}
		delete_option( 'mwb_pos_configuration_data' );
	}
}
