<?php
/**
 * Booster for WooCommerce - Settings - Cross-sells
 *
 * @version 3.9.0
 * @since   3.5.3
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_cross_sells_options',
	),
	array(
		'title'    => __( 'Cross-sells Total', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set to zero for WooCommerce default.', 'e-commerce-jetpack' ) . ' ' . __( 'Set to -1 for unlimited.', 'e-commerce-jetpack' ),
		'type'     => 'number',
		'id'       => 'wcj_cross_sells_total',
		'default'  => 0,
		'custom_attributes' => array( 'min' => -1 ),
	),
	array(
		'title'    => __( 'Cross-sells Columns', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set to zero for WooCommerce default.', 'e-commerce-jetpack' ),
		'type'     => 'number',
		'id'       => 'wcj_cross_sells_columns',
		'default'  => 0,
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'title'    => __( 'Cross-sells Order By', 'e-commerce-jetpack' ),
		'type'     => 'select',
		'id'       => 'wcj_cross_sells_orderby',
		'default'  => 'no_changes',
		'options'  => array(
			'no_changes'  => __( 'No changes (default behaviour)', 'e-commerce-jetpack' ),
			'rand'        => __( 'Random', 'e-commerce-jetpack' ),
			'title'       => __( 'Title', 'e-commerce-jetpack' ),
			'id'          => __( 'ID', 'e-commerce-jetpack' ),
			'date'        => __( 'Date', 'e-commerce-jetpack' ),
			'modified'    => __( 'Modified', 'e-commerce-jetpack' ),
			'menu_order'  => __( 'Menu order', 'e-commerce-jetpack' ),
			'price'       => __( 'Price', 'e-commerce-jetpack' ),
		),
	),
);
if ( ! WCJ_IS_WC_VERSION_BELOW_3_3_0 ) {
	$settings[] = array(
		'title'    => __( 'Cross-sells Order', 'e-commerce-jetpack' ),
		'type'     => 'select',
		'id'       => 'wcj_cross_sells_order',
		'default'  => 'no_changes',
		'options'  => array(
			'no_changes'  => __( 'No changes (default behaviour)', 'e-commerce-jetpack' ),
			'desc'        => __( 'Descending', 'e-commerce-jetpack' ),
			'asc'         => __( 'Ascending', 'e-commerce-jetpack' ),
		),
	);
};
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Cross-sells Position', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Cross-sells position in cart.', 'e-commerce-jetpack' ),
		'type'     => 'select',
		'id'       => 'wcj_cross_sells_position',
		'default'  => 'no_changes',
		'options'  => array(
			'no_changes'                   => __( 'No changes (default)', 'e-commerce-jetpack' ),
			'woocommerce_before_cart'      => __( 'Before cart', 'e-commerce-jetpack' ),
			'woocommerce_cart_collaterals' => __( 'Cart collaterals', 'e-commerce-jetpack' ),
			'woocommerce_after_cart'       => __( 'After cart', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Position priority', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Ignored if "Cross-sells Position" option above is set to "No changes (default)".', 'e-commerce-jetpack' ),
		'type'     => 'number',
		'id'       => 'wcj_cross_sells_position_priority',
		'default'  => 10,
	),
	array(
		'title'    => __( 'Hide Cross-sells', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'type'     => 'checkbox',
		'id'       => 'wcj_cross_sells_hide',
		'default'  => 'no',
	),
) );
if ( ! WCJ_IS_WC_VERSION_BELOW_3 ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Global Cross-sells', 'e-commerce-jetpack' ),
			'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Enable this section, if you want to add same cross-sells to all products.', 'e-commerce-jetpack' ) . '<br>' .
				apply_filters( 'booster_message', '', 'desc' ),
			'type'     => 'checkbox',
			'id'       => 'wcj_cross_sells_global_enabled',
			'default'  => 'no',
			'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		),
		array(
			'desc'     => __( 'Global cross-sells', 'e-commerce-jetpack' ),
			'type'     => 'multiselect',
			'id'       => 'wcj_cross_sells_global_ids',
			'default'  => '',
			'class'    => 'chosen_select',
			'options'  => wcj_get_products(),
		),
		array(
			'title'    => __( 'Exclude "Not in Stock" Products', 'e-commerce-jetpack' ),
			'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Enable this, if you want to exclude "not in stock" products from cross-sells.', 'e-commerce-jetpack' ) . '<br>' .
				apply_filters( 'booster_message', '', 'desc' ),
			'type'     => 'checkbox',
			'id'       => 'wcj_cross_sells_exclude_not_in_stock',
			'default'  => 'no',
			'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		),
		array(
			'title'    => __( 'Replace Cart Products with Cross-sells', 'e-commerce-jetpack' ),
			'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Enable this, if you want original products to be removed from cart and replaced with cross-sells, if customer adds cross-sells on cart page.', 'e-commerce-jetpack' ) . '<br>' .
				sprintf( __( 'Please note that this option will work only if "%s" option is disabled in %s.', 'e-commerce-jetpack' ),
					__( 'Enable AJAX add to cart buttons on archives', 'woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products' ) . '">' .
						__( 'WooCommerce > Settings > Products > General > Shop pages > Add to cart behaviour', 'e-commerce-jetpack' ) . '</a>' ) . '<br>' .
				apply_filters( 'booster_message', '', 'desc' ),
			'type'     => 'checkbox',
			'id'       => 'wcj_cross_sells_replace_with_cross_sells',
			'default'  => 'no',
			'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_cross_sells_options',
	),
) );
return $settings;
