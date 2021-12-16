<?php
/**
 * Booster for WooCommerce - Module - Product Visibility by User Role
 *
 * @version 5.2.0
 * @since   2.5.5
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Product_By_User_Role' ) ) :

class WCJ_Product_By_User_Role extends WCJ_Module_Product_By_Condition {

	/**
	 * Constructor.
	 *
	 * @version 5.2.0
	 * @since   2.5.5
	 */
	function __construct() {

		$this->id         = 'product_by_user_role';
		$this->short_desc = __( 'Product Visibility by User Role', 'e-commerce-jetpack' );
		$this->desc       = __( 'Display products by customer\'s user role. Visibility method options (Plus)', 'e-commerce-jetpack' );
		$this->desc_pro   = __( 'Display products by customer\'s user role.', 'e-commerce-jetpack' );
		$this->link_slug  = 'woocommerce-product-visibility-by-user-role';
		$this->extra_desc = __( 'When enabled, module will add new "Booster: Product Visibility by User Role" meta box to each product\'s edit page.', 'e-commerce-jetpack' );

		$this->title      = __( 'User Roles', 'e-commerce-jetpack' );

		parent::__construct();

	}

	/**
	 * maybe_add_extra_settings.
	 *
	 * @version 4.9.0
	 * @since   4.9.0
	 *
	 * @return array
	 */
	public function maybe_add_extra_settings() {
		return array(
			array(
				'title' => __( 'User Options', 'e-commerce-jetpack' ),
				'type'  => 'title',
				'id'    => 'wcj_' . $this->id . '_user_options',
			),
			array(
				'title'             => __( 'Skip Editable Roles Filter', 'e-commerce-jetpack' ),
				'desc_tip'          => __( 'Ignores <code>editable_roles</code> filter on admin.', 'e-commerce-jetpack' ) . '<br />' . sprintf( __( 'Enable this option for example if the shop manager can\'t see some role but only if you\'ve already tried the <strong>Shop Manager Editable Roles</strong> on <a href="%s">Admin Tools</a> module.', 'e-commerce-jetpack' ), admin_url( 'admin.php?page=wc-settings&tab=jetpack&wcj-cat=emails_and_misc&section=admin_tools' ) ),
				'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
				'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
				'id'                => 'wcj_' . $this->id . '_user_options_skip_editable_roles',
				'default'           => 'no',
				'type'              => 'checkbox',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wcj_' . $this->id . '_user_options',
			),
		);
	}

	/**
	 * get_options_list.
	 *
	 * @version 4.9.0
	 * @since   3.6.0
	 */
	function get_options_list() {
		$user_roles_options_args = 'no' === wcj_get_option( 'wcj_' . $this->id . '_user_options_skip_editable_roles', 'no' ) ? null : array( 'skip_editable_roles_filter' => true );
		return wcj_get_user_roles_options( $user_roles_options_args );
	}

	/**
	 * get_check_option.
	 *
	 * @version 3.6.0
	 * @since   3.6.0
	 */
	function get_check_option() {
		return wcj_get_current_user_all_roles();
	}

}

endif;

return new WCJ_Product_By_User_Role();
