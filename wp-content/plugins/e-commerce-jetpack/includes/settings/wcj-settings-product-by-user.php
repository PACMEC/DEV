<?php
/**
 * Booster for WooCommerce Settings - Product by User
 *
 * @version 2.8.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$fields = array(
	'desc'          => __( 'Description', 'e-commerce-jetpack' ),
	'short_desc'    => __( 'Short Description', 'e-commerce-jetpack' ),
	'image'         => __( 'Image', 'e-commerce-jetpack' ),
	'regular_price' => __( 'Regular Price', 'e-commerce-jetpack' ),
	'sale_price'    => __( 'Sale Price', 'e-commerce-jetpack' ),
	'external_url'  => __( 'Product URL (for "External/Affiliate" product type only)', 'e-commerce-jetpack' ),
	'cats'          => __( 'Categories', 'e-commerce-jetpack' ),
	'tags'          => __( 'Tags', 'e-commerce-jetpack' ),
);
$fields_enabled_options  = array();
$fields_required_options = array();
$i = 0;
$total_fields = count( $fields );
foreach ( $fields as $field_id => $field_desc ) {
	$i++;
	$checkboxgroup = '';
	if ( 1 === $i ) {
		$checkboxgroup = 'start';
	} elseif ( $total_fields === $i ) {
		$checkboxgroup = 'end';
	}
	$fields_enabled_options[] = array(
		'title'    => ( ( 1 === $i ) ? __( 'Additional Fields', 'e-commerce-jetpack' ) : '' ),
		'desc'     => $field_desc,
		'id'       => 'wcj_product_by_user_' . $field_id . '_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'checkboxgroup' => $checkboxgroup,
		'custom_attributes' => ( ( 'image' === $field_id ) ? apply_filters( 'booster_message', '', 'disabled' ) : '' ),
		'desc_tip' => ( ( 'image' === $field_id ) ? apply_filters( 'booster_message', '', 'desc' ) : '' ),
	);
	$fields_required_options[] = array(
		'title'    => ( ( 1 === $i ) ? __( 'Is Required', 'e-commerce-jetpack' ) : '' ),
		'desc'     => $field_desc,
		'id'       => 'wcj_product_by_user_' . $field_id . '_required',
		'default'  => 'no',
		'type'     => 'checkbox',
		'checkboxgroup' => $checkboxgroup,
		'custom_attributes' => ( ( 'image' === $field_id ) ? apply_filters( 'booster_message', '', 'disabled' ) : '' ),
		'desc_tip' => ( ( 'image' === $field_id ) ? apply_filters( 'booster_message', '', 'desc' ) : '' ),
	);
}

$settings = array_merge(
	array(
		array(
			'title'    => __( 'Options', 'e-commerce-jetpack' ),
			'type'     => 'title',
			'desc'     => __( '<em>Title</em> field is always enabled and required.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_by_user_options',
		),
	),
	$fields_enabled_options,
	$fields_required_options,
	array(
		array(
			'title'    => __( 'Price Step', 'e-commerce-jetpack' ),
			'desc'     => __( 'Number of decimals', 'woocommerce' ),
			'desc_tip' => __( 'Used for price fields only.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_by_user_price_step',
			'default'  => wcj_get_option( 'woocommerce_price_num_decimals', 2 ),
			'type'     => 'number',
			'custom_attributes' => array( 'step' => '1', 'min'  => '0' ),
		),
		array(
			'title'    => __( 'User Visibility', 'e-commerce-jetpack' ),
			'desc'     => sprintf( __( 'Custom roles can be added via "Add/Manage Custom Roles" tool in Booster\'s <a href="%s">General</a> module', 'e-commerce-jetpack' ),
				admin_url( 'admin.php?page=wc-settings&tab=jetpack&wcj-cat=emails_and_misc&section=general' ) ),
			'id'       => 'wcj_product_by_user_user_visibility',
			'default'  => array(),
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'options'  => wcj_get_user_roles_options(),
		),
		array(
			'title'    => __( 'Product Type', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_by_user_product_type',
			'default'  => 'simple',
			'type'     => 'select',
			'options'  => array(
				'simple'   => __( 'Simple product', 'e-commerce-jetpack' ),
				'external' => __( 'External/Affiliate product', 'e-commerce-jetpack' ),
			),
//			'desc'     =>  apply_filters( 'booster_message', '', 'desc_advanced', array( 'option' => __( 'Variable product', 'e-commerce-jetpack' ) ) ),
		),
		array(
			'title'    => __( 'Product Status', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_by_user_status',
			'default'  => 'draft',
			'type'     => 'select',
			'options'  => get_post_statuses(),
		),
		array(
			'title'    => __( 'Require Unique Title', 'e-commerce-jetpack' ),
			'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_by_user_require_unique_title',
			'default'  => 'no',
			'type'     => 'checkbox',
		),
		array(
			'title'    => __( 'Add "My Products" Tab to User\'s My Account Page', 'e-commerce-jetpack' ),
			'desc'     => __( 'Add', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_by_user_add_to_my_account',
			'default'  => 'yes',
			'type'     => 'checkbox',
		),
		array(
			'title'    => __( 'Message: Product Successfully Added', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_by_user_message_product_successfully_added',
			'default'  => __( '"%product_title%" successfully added!', 'e-commerce-jetpack' ),
			'type'     => 'text',
			'css'      => 'width:300px;',
		),
		array(
			'title'    => __( 'Message: Product Successfully Edited', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_by_user_message_product_successfully_edited',
			'default'  => __( '"%product_title%" successfully edited!', 'e-commerce-jetpack' ),
			'type'     => 'text',
			'css'      => 'width:300px;',
		),
		array(
			'title'    => __( 'Total Custom Taxonomies', 'e-commerce-jetpack' ),
			'id'       => 'wcj_product_by_user_custom_taxonomies_total',
			'default'  => 1,
			'type'     => 'custom_number',
			'desc_tip' => __( 'Press Save changes after you change this number.', 'e-commerce-jetpack' ),
			'desc'     => apply_filters( 'booster_message', '', 'desc' ),
			'custom_attributes' => is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ?
				apply_filters( 'booster_message', '', 'readonly' ) : array( 'step' => '1', 'min'  => '1' ),
		),
	)
);
for ( $i = 1; $i <= apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_product_by_user_custom_taxonomies_total', 1 ) ); $i++ ) {
	$settings = array_merge( $settings, array(
			array(
				'title'    => __( 'Custom Taxonomy', 'e-commerce-jetpack' ) . ' #' . $i,
				'desc'     => __( 'Enabled', 'e-commerce-jetpack' ),
				'id'       => 'wcj_product_by_user_custom_taxonomy_' . $i . '_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Required', 'e-commerce-jetpack' ),
				'id'       => 'wcj_product_by_user_custom_taxonomy_' . $i . '_required',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'ID', 'e-commerce-jetpack' ),
				'id'       => 'wcj_product_by_user_custom_taxonomy_' . $i . '_id',
				'default'  => '',
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Title', 'e-commerce-jetpack' ),
				'id'       => 'wcj_product_by_user_custom_taxonomy_' . $i . '_title',
				'default'  => '',
				'type'     => 'text',
			),
		)
	);
}
$settings = array_merge( $settings, array(
		array(
			'type'     => 'sectionend',
			'id'       => 'wcj_product_by_user_options',
		),
	)
);
return $settings;
