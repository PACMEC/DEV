<?php
/**
 * Booster for WooCommerce - Settings - Max Products per User
 *
 * @version 4.2.0
 * @since   3.5.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'All Products', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_max_products_per_user_global_options',
	),
	array(
		'title'    => __( 'All Products', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_max_products_per_user_global_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Maximum Allowed Each Product\'s Quantity per User', 'e-commerce-jetpack' ),
		'id'       => 'wcj_max_products_per_user_global_max_qty',
		'default'  => 1,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 1 ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_max_products_per_user_global_options',
	),
	array(
		'title'    => __( 'Per Product', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_max_products_per_user_local_options',
	),
	array(
		'title'    => __( 'Per Product', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'desc_tip' => __( 'This will add new meta box to each product\'s edit page.', 'e-commerce-jetpack' ) . ' ' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_max_products_per_user_local_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_max_products_per_user_local_options',
	),
	array(
		'title'    => __( 'General Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_max_products_per_user_general_options',
	),
	array(
		'title'    => __( 'Order Status', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This sets when (i.e. on which order status) users\' quantities should be updated.', 'e-commerce-jetpack' ) . ' ' .
			__( 'You can select multiple order status here - quantities will be updated only once, on whichever status is triggered first.', 'e-commerce-jetpack' ) . ' ' .
			__( 'If no status are selected - "Completed" order status is used.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_max_products_per_user_order_status',
		'default'  => array( 'wc-completed' ),
		'options'  => wcj_get_order_statuses( false ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
	),
	array(
		'title'    => __( 'Customer Message', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%max_qty%', '%product_title%', '%qty_already_bought%', '%remaining_qty%' ) ),
		'id'       => 'wcj_max_products_per_user_message',
		'default'  => __( 'You can only buy maximum %max_qty% pcs. of %product_title% (you already bought %qty_already_bought% pcs.).', 'e-commerce-jetpack' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;height:100px;',
	),
	array(
		'title'    => __( 'Block Add to Cart', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will stop customer from adding product to cart on exceeded quantities.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_max_products_per_user_stop_from_adding_to_cart',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Block Checkout Page', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will stop customer from accessing the checkout page on exceeded quantities. Customer will be redirected to the cart page.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_max_products_per_user_stop_from_seeing_checkout',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Calculate Data', 'e-commerce-jetpack' ),
		'id'       => 'wcj_max_products_per_user_calculate_data',
		'default'  => '',
		'type'     => 'custom_link',
		'link'     => '<a class="button" href="' .
			add_query_arg( 'wcj_max_products_per_user_calculate_data', '1', remove_query_arg( 'wcj_max_products_per_user_calculate_data_finished' ) ) . '">' .
				__( 'Calculate Data', 'e-commerce-jetpack' ) .
			'</a>',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_max_products_per_user_general_options',
	),
);
