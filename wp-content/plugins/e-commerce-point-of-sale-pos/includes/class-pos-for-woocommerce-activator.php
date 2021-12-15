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

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Pos_For_Woocommerce_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public static function pos_for_woocommerce_activate() {
		$email           = get_option( 'admin_email', false );
		$admin           = get_user_by( 'email', $email );
		$admin_id        = $admin->ID;
		$mwb_page_exists = get_page_by_path( 'point-of-sale' );
		if ( isset( $mwb_page_exists->ID ) && '' !== $mwb_page_exists->ID ) {
			return;
		}
		$mwb_pos_page    = array(
			'post_author' => $admin_id,
			'post_name'   => 'point-of-sale',
			'post_title'  => __( 'Point of Sale', 'mwb-point-of-sale-woocommerce' ),
			'post_type'   => 'page',
			'post_status' => 'publish',
		);
		$mwb_pos_page_id = wp_insert_post( $mwb_pos_page );
		if ( isset( $mwb_pos_page_id ) && '' !== $mwb_pos_page_id ) {
			update_option( 'mwb_pos_page_exists', $mwb_pos_page_id );
		}

		$mwb_pfw_plugin_details = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'plugin_dir_path' => POS_FOR_WOOCOMMERCE_DIR_PATH,
			'plugin_dir_url' => POS_FOR_WOOCOMMERCE_DIR_URL,
		);
		$mwb_pfw_plugin_details = wp_json_encode( $mwb_pfw_plugin_details );
		update_option( 'mwb_pos_configuration_data', $mwb_pfw_plugin_details );

	}
}
