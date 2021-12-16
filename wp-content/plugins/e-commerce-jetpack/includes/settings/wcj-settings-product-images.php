<?php
/**
 * Booster for WooCommerce - Settings - Product Images
 *
 * @version 5.4.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Product Image and Thumbnails', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_images_and_thumbnails_options',
	),
	array(
		'title'    => __( 'Image and Thumbnails on Single', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_and_thumbnails_hide_on_single',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Image on Single', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_hide_on_single',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Thumbnails on Single', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_thumbnails_hide_on_single',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Image on Archives', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_hide_on_archive',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Replace Image on Single', 'e-commerce-jetpack' ),
		'desc'     => __( 'Replace image on single product page with custom HTML. Leave blank to disable.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'You can use shortcodes here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_custom_on_single',
		'default'  => '',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Replace Thumbnails on Single', 'e-commerce-jetpack' ),
		'desc'     => __( 'Replace thumbnails on single product page with custom HTML. Leave blank to disable.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'You can use shortcodes here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_thumbnails_custom_on_single',
		'default'  => '',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Replace Image on Archive', 'e-commerce-jetpack' ),
		'desc'     => __( 'Replace image on archive pages with custom HTML. Leave blank to disable.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'You can use shortcodes here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_custom_on_archives',
		'default'  => '',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Single Product Thumbnails Columns', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_thumbnails_columns',
		'default'  => 3,
		'type'     => 'number',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_images_and_thumbnails_options',
	),
	array(
		'title'    => __( 'Placeholder Image', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_images_placeholder_options',
	),
	array(
		'title'    => __( 'Custom Placeholder Image URL', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Leave blank to use the default placeholder image.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_placeholder_src',
		'default'  => '',
		'type'     => 'text',
		'css'      => 'width:100%',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_images_placeholder_options',
	),
	array(
		'title'    => __( 'Callbacks', 'e-commerce-jetpack' ),
		'desc'     => __( 'Callback functions used by WooCommerce and the current theme in order to customize images and thumbnails', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_product_images_cb',
	),
	array(
		'title'    => __( 'Loop Thumbnail', 'e-commerce-jetpack' ),
		'desc'     => __( 'Used on hook <strong>woocommerce_before_shop_loop_item_title</strong>', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_cb_loop_product_thumbnail',
		'default'  => 'woocommerce_template_loop_product_thumbnail',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Loop Thumbnail Priority', 'e-commerce-jetpack' ),
		'desc'     => __( 'Priority for Loop Thumbnail. If you want to change the priority you can set Greater value for high priority & Lower value for low priority. Set to zero to use default priority.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_cb_loop_product_thumbnail_priority',
		'default'  => 10,
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Show Images', 'e-commerce-jetpack' ),
		'desc'     => __( 'Used on hook <strong>woocommerce_before_single_product_summary</strong>', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_cb_show_product_images',
		'default'  => 'woocommerce_show_product_images',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Show Images Priority', 'e-commerce-jetpack' ),
		'desc'     => __( 'Priority for Show Images. If you want to change the priority you can set Greater value for high priority & Lower value for low priority. Set to zero to use default priority.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_product_images_cb_show_product_images_priority',
		'default'  => 20,
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_product_images_cb',
	),
);
