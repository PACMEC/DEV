<?php
/**
 * Booster for WooCommerce - Settings - Stock
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Custom "In Stock" Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_stock_custom_in_stock_options',
	),
	array(
		'title'    => __( 'Custom "In Stock"', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_stock_custom_in_stock_section_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Custom "In Stock" Text', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_stock_custom_in_stock_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( '"In Stock" text.', 'e-commerce-jetpack' ) . ' ' .
			sprintf( __( 'If needed, use %s to insert stock quantity.', 'e-commerce-jetpack' ), '<code>%s</code>' ),
		'desc_tip' => __( 'You can also use shortcodes here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_stock_custom_in_stock',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;height:100px;',
	),
	array(
		'desc'     => __( '"Low amount" text.', 'e-commerce-jetpack' ) . ' ' . __( 'Ignored if empty.', 'e-commerce-jetpack' ) . ' ' .
			sprintf( __( 'If needed, use %s to insert stock quantity.', 'e-commerce-jetpack' ), '<code>%s</code>' ),
		'desc_tip' => __( 'You can also use shortcodes here.', 'e-commerce-jetpack' ) . ' ' .
			sprintf( __( 'Used only if %s is selected for %s in %s.', 'e-commerce-jetpack' ),
				'<em>' . __( 'Only show quantity remaining in stock when low', 'e-commerce-jetpack' ) . '</em>',
				'<em>' . __( 'Stock display format', 'woocommerce' ) . '</em>',
				'<em>' . __( 'WooCommerce > Settings > Products > Inventory', 'e-commerce-jetpack' ) ) . '</em>',
		'id'       => 'wcj_stock_custom_in_stock_low_amount',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;height:100px;',
	),
	array(
		'desc'     => __( '"Can be backordered" text.', 'e-commerce-jetpack' ) . ' ' . __( 'Ignored if empty.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'You can also use shortcodes here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_stock_custom_in_stock_can_be_backordered',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;height:100px;',
	),
	array(
		'title'    => __( 'Custom "In Stock" Class', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_stock_custom_in_stock_class_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => sprintf( __( 'Default: %s.', 'e-commerce-jetpack' ), '<code>in-stock</code>' ),
		'css'      => 'width:100%;',
		'id'       => 'wcj_stock_custom_in_stock_class',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_stock_custom_in_stock_options',
	),
	array(
		'title'    => __( 'Custom "Out of Stock" Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_stock_custom_out_of_stock_options',
	),
	array(
		'title'    => __( 'Custom "Out of Stock"', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_stock_custom_out_of_stock_section_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Custom "Out of Stock" Text', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_stock_custom_out_of_stock_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc_tip' => __( 'You can also use shortcodes here.', 'e-commerce-jetpack' ),
		'desc'     => sprintf( __( 'Default: %s.', 'e-commerce-jetpack' ), '<code>' . __( 'Out of stock', 'woocommerce' ) . '</code>' ),
		'id'       => 'wcj_stock_custom_out_of_stock',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;height:100px;',
	),
	array(
		'title'    => __( 'Custom "Out of Stock" Class', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_stock_custom_out_of_stock_class_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => sprintf( __( 'Default: %s.', 'e-commerce-jetpack' ), '<code>out-of-stock</code>' ),
		'css'      => 'width:100%;',
		'id'       => 'wcj_stock_custom_out_of_stock_class',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_stock_custom_out_of_stock_options',
	),
	array(
		'title'    => __( 'Custom "Available on backorder" Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_stock_custom_backorder_options',
		'desc'     => __( 'This option is used if the "Allow backorders?" is "Allow, but notify customer" in the product.', 'e-commerce-jetpack' ),
	),
	array(
		'title'    => __( 'Custom "Available on backorder"', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_stock_custom_backorder_section_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Custom "Available on backorder" Text', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_stock_custom_backorder_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc_tip' => __( 'You can also use shortcodes here.', 'e-commerce-jetpack' ),
		'desc'     => sprintf( __( 'Default: %s or empty string.', 'e-commerce-jetpack' ), '<code>' . __( 'Available on backorder', 'woocommerce' ) . '</code>' ),
		'id'       => 'wcj_stock_custom_backorder',
		'default'  => '',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;height:100px;',
	),
	array(
		'title'    => __( 'Custom "Available on backorder" Class', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_stock_custom_backorder_class_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => sprintf( __( 'Default: %s.', 'e-commerce-jetpack' ), '<code>available-on-backorder</code>' ),
		'css'      => 'width:100%;',
		'id'       => 'wcj_stock_custom_backorder_class',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_stock_custom_backorder_options',
	),
	array(
		'title'    => __( 'Custom Stock HTML', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_stock_custom_stock_html_options',
	),
	array(
		'title'    => __( 'Custom Stock HTML', 'e-commerce-jetpack' ),
		'desc'     => '<strong>' . __( 'Enable section', 'e-commerce-jetpack' ) . '</strong>',
		'id'       => 'wcj_stock_custom_stock_html_section_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'desc_tip' => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'HTML', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'You can also use shortcodes here.', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%class%', '%availability%' ) ) . '. ' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_stock_custom_stock_html',
		'default'  => '<p class="stock %class%">%availability%</p>',
		'type'     => 'textarea',
		'css'      => 'width:100%;height:100px;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_stock_custom_stock_html_options',
	),
	array(
		'title'    => __( 'More Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_stock_more_options',
	),
	array(
		'title'    => __( 'Remove Stock Display', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will remove stock display from frontend.', 'e-commerce-jetpack' ) . ' ' . apply_filters( 'booster_message', '', 'desc' ),
		'desc'     => __( 'Remove', 'e-commerce-jetpack' ),
		'id'       => 'wcj_stock_remove_frontend_display_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_stock_more_options',
	),
);
