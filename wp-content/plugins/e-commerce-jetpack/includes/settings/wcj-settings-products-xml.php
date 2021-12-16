<?php
/**
 * Booster for WooCommerce - Settings - Products XML
 *
 * @version 3.6.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    recheck "URL" in `'wcj_products_xml_file_path_' . $i`
 * @todo    (maybe) add more options to `wcj_products_xml_orderby_` (see https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$product_cats_options    = wcj_get_terms( 'product_cat' );
$product_tags_options    = wcj_get_terms( 'product_tag' );
$products_options        = wcj_get_products();
$is_multiselect_products = ( 'yes' === wcj_get_option( 'wcj_list_for_products', 'yes' ) );
$settings = array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_products_xml_options',
	),
	array(
		'title'    => __( 'Total Files', 'e-commerce-jetpack' ),
		'id'       => 'wcj_products_xml_total_files',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc_tip' => __( 'Press Save changes after you change this number.', 'e-commerce-jetpack' ),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ?
			apply_filters( 'booster_message', '', 'readonly' ) : array( 'step' => '1', 'min'  => '1', ),
	),
	array(
		'title'    => __( 'Advanced: Block Size', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you have large number of products you may want to modify block size for WP_Query call. Leave default value if not sure.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_products_xml_block_size',
		'default'  => 256,
		'type'     => 'number',
		'custom_attributes' => array( 'step' => '1', 'min'  => '1', ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_products_xml_options',
	),
);
for ( $i = 1; $i <= apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_products_xml_total_files', 1 ) ); $i++ ) {
	wcj_maybe_convert_and_update_option_value( array(
		array( 'id' => 'wcj_products_xml_products_incl_' . $i, 'default' => '' ),
		array( 'id' => 'wcj_products_xml_products_excl_' . $i, 'default' => '' ),
	), $is_multiselect_products );
	$products_xml_cron_desc = '';
	if ( $this->is_enabled() ) {
		$products_xml_cron_desc = '<a class="button" title="' .
			__( 'If you\'ve made any changes in module\'s settings - don\'t forget to save changes before clicking this button.', 'e-commerce-jetpack' ) . '"' .
			' href="' . add_query_arg( 'wcj_create_products_xml', $i, remove_query_arg( 'wcj_create_products_xml_result' ) ) . '">' .
			__( 'Create Now', 'e-commerce-jetpack' ) . '</a>' .
		wcj_crons_get_next_event_time_message( 'wcj_create_products_xml_cron_time_' . $i );
	}
	$products_time_file_created_desc = '';
	if ( '' != wcj_get_option( 'wcj_products_time_file_created_' . $i, '' ) ) {
		$products_time_file_created_desc = sprintf(
			__( 'Recent file was created on %s', 'e-commerce-jetpack' ),
			'<code>' . date_i18n( wcj_get_option( 'date_format' ) . ' ' . wcj_get_option( 'time_format' ), wcj_get_option( 'wcj_products_time_file_created_' . $i, '' ) ) . '</code>'
		);
	}
	$default_file_name = ( ( 1 == $i ) ? 'products.xml' : 'products_' . $i . '.xml' );
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'XML File', 'e-commerce-jetpack' ) . ' #' . $i,
			'type'     => 'title',
			'desc'     => $products_time_file_created_desc,
			'id'       => 'wcj_products_xml_options_' . $i,
		),
		array(
			'title'    => __( 'Enabled', 'e-commerce-jetpack' ),
			'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
			'id'       => 'wcj_products_xml_enabled_' . $i,
			'default'  => 'yes',
			'type'     => 'checkbox',
		),
		array(
			'title'    => __( 'XML Header', 'e-commerce-jetpack' ),
			'desc'     => sprintf( __( 'You can use shortcodes here. For example %s.', 'e-commerce-jetpack' ), '<code>[wcj_current_datetime]</code>' ),
			'id'       => 'wcj_products_xml_header_' . $i,
			'default'  => '<?xml version = "1.0" encoding = "utf-8" ?>' . PHP_EOL . '<root>' . PHP_EOL,
			'type'     => 'custom_textarea',
			'css'      => 'width:66%;min-width:300px;min-height:150px;',
		),
		array(
			'title'    => __( 'XML Item', 'e-commerce-jetpack' ),
			'desc'     => sprintf(
				__( 'You can use shortcodes here. Please take a look at <a target="_blank" href="%s">Booster\'s products shortcodes</a>.', 'e-commerce-jetpack' ),
				'https://booster.io/category/shortcodes/products-shortcodes/'
			),
			'id'       => 'wcj_products_xml_item_' . $i,
			'default'  =>
				'<item>' . PHP_EOL .
					"\t" . '<name>[wcj_product_title strip_tags="yes"]</name>' . PHP_EOL .
					"\t" . '<link>[wcj_product_url strip_tags="yes"]</link>' . PHP_EOL .
					"\t" . '<price>[wcj_product_price hide_currency="yes" strip_tags="yes"]</price>' . PHP_EOL .
					"\t" . '<image>[wcj_product_image_url image_size="full" strip_tags="yes"]</image>' . PHP_EOL .
					"\t" . '<category_full>[wcj_product_categories_names strip_tags="yes"]</category_full>' . PHP_EOL .
					"\t" . '<category_link>[wcj_product_categories_urls strip_tags="yes"]</category_link>' . PHP_EOL .
				'</item>' . PHP_EOL,
			'type'     => 'custom_textarea',
			'css'      => 'width:66%;min-width:300px;min-height:300px;',
		),
		array(
			'title'    => __( 'XML Footer', 'e-commerce-jetpack' ),
			'desc'     => __( 'You can use shortcodes here.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_products_xml_footer_' . $i,
			'default'  => '</root>',
			'type'     => 'custom_textarea',
			'css'      => 'width:66%;min-width:300px;min-height:150px;',
		),
		array(
			'title'    => __( 'XML File Path and Name', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Path on server:', 'e-commerce-jetpack' ) . ' ' . ABSPATH . wcj_get_option( 'wcj_products_xml_file_path_' . $i, $default_file_name ),
			'desc'     => __( 'URL:', 'e-commerce-jetpack' ) . ' ' .
				'<a target="_blank" href="' . site_url() . '/' . wcj_get_option( 'wcj_products_xml_file_path_' . $i, $default_file_name ) . '">' .
					site_url() . '/' . wcj_get_option( 'wcj_products_xml_file_path_' . $i, $default_file_name ) . '</a>',
			'id'       => 'wcj_products_xml_file_path_' . $i,
			'default'  => $default_file_name,
			'type'     => 'text',
			'css'      => 'width:66%;min-width:300px;',
		),
		array(
			'title'    => __( 'Update Period', 'e-commerce-jetpack' ),
			'desc'     => $products_xml_cron_desc,
			'id'       => 'wcj_create_products_xml_period_' . $i,
			'default'  => 'weekly',
			'type'     => 'select',
			'options'  => array(
				'minutely'   => __( 'Update Every Minute', 'e-commerce-jetpack' ),
				'hourly'     => __( 'Update Hourly', 'e-commerce-jetpack' ),
				'twicedaily' => __( 'Update Twice Daily', 'e-commerce-jetpack' ),
				'daily'      => __( 'Update Daily', 'e-commerce-jetpack' ),
				'weekly'     => __( 'Update Weekly', 'e-commerce-jetpack' ),
			),
			'desc_tip' => __( 'Possible update periods are: every minute, hourly, twice daily, daily and weekly.', 'e-commerce-jetpack' ) . ' ' .
				apply_filters( 'booster_message', '', 'desc_no_link' ),
			'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		),
		wcj_get_settings_as_multiselect_or_text(
			array(
				'title'    => __( 'Products to Include', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'To include selected products only, enter products here. Leave blank to include all products.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_products_xml_products_incl_' . $i,
				'default'  => '',
			),
			$products_options,
			$is_multiselect_products
		),
		wcj_get_settings_as_multiselect_or_text(
			array(
				'title'    => __( 'Products to Exclude', 'e-commerce-jetpack' ),
				'desc_tip' => __( 'To exclude selected products, enter products here. Leave blank to include all products.', 'e-commerce-jetpack' ),
				'id'       => 'wcj_products_xml_products_excl_' . $i,
				'default'  => '',
			),
			$products_options,
			$is_multiselect_products
		),
		array(
			'title'    => __( 'Categories to Include', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'To include products from selected categories only, enter categories here. Leave blank to include all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_products_xml_cats_incl_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_cats_options,
		),
		array(
			'title'    => __( 'Categories to Exclude', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'To exclude products from selected categories, enter categories here. Leave blank to include all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_products_xml_cats_excl_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_cats_options,
		),
		array(
			'title'    => __( 'Tags to Include', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'To include products from selected tags only, enter tags here. Leave blank to include all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_products_xml_tags_incl_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_tags_options,
		),
		array(
			'title'    => __( 'Tags to Exclude', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'To exclude products from selected tags, enter tags here. Leave blank to include all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_products_xml_tags_excl_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_tags_options,
		),
		array(
			'title'    => __( 'Products Scope', 'e-commerce-jetpack' ),
			'id'       => 'wcj_products_xml_scope_' . $i,
			'default'  => 'all',
			'type'     => 'select',
			'options'  => array(
				'all'               => __( 'All products', 'e-commerce-jetpack' ),
				'sale_only'         => __( 'Only products that are on sale', 'e-commerce-jetpack' ),
				'not_sale_only'     => __( 'Only products that are not on sale', 'e-commerce-jetpack' ),
				'featured_only'     => __( 'Only products that are featured', 'e-commerce-jetpack' ),
				'not_featured_only' => __( 'Only products that are not featured', 'e-commerce-jetpack' ),
			),
		),
		array(
			'title'    => __( 'Sort Products by', 'e-commerce-jetpack' ),
			'id'       => 'wcj_products_xml_orderby_' . $i,
			'default'  => 'date',
			'type'     => 'select',
			'options'  => array(
				'date'              => __( 'Date', 'e-commerce-jetpack' ),
				'ID'                => __( 'ID', 'e-commerce-jetpack' ),
				'author'            => __( 'Author', 'e-commerce-jetpack' ),
				'title'             => __( 'Title', 'e-commerce-jetpack' ),
				'name'              => __( 'Slug', 'e-commerce-jetpack' ),
				'modified'          => __( 'Modified', 'e-commerce-jetpack' ),
				'rand'              => __( 'Rand', 'e-commerce-jetpack' ),
			),
		),
		array(
			'title'    => __( 'Sorting Order', 'e-commerce-jetpack' ),
			'id'       => 'wcj_products_xml_order_' . $i,
			'default'  => 'DESC',
			'type'     => 'select',
			'options'  => array(
				'DESC'              => __( 'Descending', 'e-commerce-jetpack' ),
				'ASC'               => __( 'Ascending', 'e-commerce-jetpack' ),
			),
		),
		array(
			'title'    => __( 'Max Products', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Set to -1 to include all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_products_xml_max_' . $i,
			'default'  => -1,
			'type'     => 'number',
			'custom_attributes' => array( 'min' => -1 ),
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'wcj_products_xml_options_' . $i,
		),
	) );
}
return $settings;
