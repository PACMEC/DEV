<?php
/**
 * Booster for WooCommerce - Settings - SKU
 *
 * @version 4.7.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    deprecate `wcj_sku_prefix` and `wcj_sku_suffix` (as user can now add it directly to "Template")
 * @todo    tags (check SKU plugin); template: '{category_prefix}{tag_prefix}{prefix}{sku_number}{suffix}{tag_suffix}{category_suffix}{variation_suffix}'
 * @todo    add "Sequential Number Generation - By Category" to SKU plugin
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = array(
	array(
		'title'    => __( 'SKU Format Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_sku_format_options',
	),
	array(
		'title'    => __( 'Number Generation', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_number_generation',
		'default'  => 'product_id',
		'type'     => 'select',
		'options'  => array(
			'product_id' => __( 'From product ID', 'e-commerce-jetpack' ),
			'sequential' => __( 'Sequential', 'e-commerce-jetpack' ),
			'hash_crc32' => __( 'Pseudorandom - Hash (max 10 digits)', 'e-commerce-jetpack' ),
			'hashids'    => __( 'Hashids - Advanced options', 'e-commerce-jetpack' ),
		),
		'desc_tip' => __( 'Number generation method.', 'e-commerce-jetpack' ) . ' ' .
		              __( 'Possible values: from product ID, sequential, pseudorandom or Hashids.', 'e-commerce-jetpack' ) . '<br />' . __( 'If using Hashids please take a look at Hashids options below.', 'e-commerce-jetpack' ),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Sequential Number Generation - Counter', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you choose to use sequential number inside SKU, you can set current sequential number counter here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_number_generation_sequential',
		'default'  => 1,
		'type'     => 'number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ? apply_filters( 'booster_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min'  => '0', )
		),
	),
	array(
		'title'    => __( 'Sequential Number Generation - By Category', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Enables sequential number generation by category.', 'e-commerce-jetpack' ) . ' ' . apply_filters( 'booster_message', '', 'desc' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_number_generation_sequential_by_cat',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Prefix', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'SKU prefix.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_prefix',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Minimum Number Length', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Minimum length for SKU number part.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_minimum_number_length',
		'default'  => 0,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'Suffix', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'SKU suffix.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_suffix',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Attributes Separator', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Used in %s, %s, %s and %s.', 'e-commerce-jetpack' ),
			'<em>{variation_attributes}</em>', '<em>{variation_attribute=X}</em>', '<em>{attribute=X}</em>', '<em>{parent_attribute=X}' ),
		'id'       => 'wcj_sku_variations_product_slug_sep',
		'default'  => '-',
		'type'     => 'text',
		'css'      => 'width:50px;',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Characters Case', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_characters_case',
		'default'  => 'original',
		'type'     => 'select',
		'options'  => array(
			'original' => __( 'Original (no changes)', 'e-commerce-jetpack' ),
			'lower'    => __( 'Convert to lowercase', 'e-commerce-jetpack' ),
			'upper'    => __( 'Convert to uppercase', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Template', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'SKU template.', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array(
			'{category_prefix}',
			'{category_suffix}',
			'{prefix}',
			'{suffix}',
			'{variation_suffix}',
			'{sku_number}',
			'{product_slug}',
			'{product_slug_acronym}',
			'{parent_product_slug}',
			'{parent_product_slug_acronym}',
			'{variation_attributes}',
			'{variation_attribute=X}',
			'{attribute=X}',
			'{parent_attribute=X}',
		) ) . '<br>' . sprintf( __( 'You can also use shortcodes here, e.g.: %s etc.', 'e-commerce-jetpack' ), '<code>' . implode( '</code>, <code>', array(
			'[wcj_product_author]',
			'[wcj_product_title]',
		) ) . '</code>' ),
		'id'       => 'wcj_sku_template',
		'default'  => '{category_prefix}{prefix}{sku_number}{suffix}{category_suffix}{variation_suffix}',
		'type'     => 'text',
		'css'      => 'width:99%;',
	),
	array(
		'title'    => __( 'Variable Products Variations', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'SKU generation for variations. Please note, that if "Generate SKU for New Products Only on First Publish" option below is not checked, then on new variable product creation, variations will get same SKUs as parent product, and if you want variations to have different SKUs, you will need to run "Autogenerate SKUs" tool manually.' ) . ' ' .
			__( 'Possible values: SKU same as parent\'s product; Generate different SKU for each variation; SKU same as parent\'s product + variation letter suffix.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_variations_handling',
		'default'  => 'as_variable',
		'type'     => 'select',
		'options'  => array(
			'as_variable'             => __( 'SKU same as parent\'s product', 'e-commerce-jetpack' ),
			'as_variation'            => __( 'Generate different SKU for each variation', 'e-commerce-jetpack' ),
			'as_variable_with_suffix' => __( 'SKU same as parent\'s product + variation letter suffix', 'e-commerce-jetpack' ),
		),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_sku_format_options',
	),
	array(
		'title'    => __( 'Hashids options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_sku_hashids_options',
	),
	array(
		'title'    => __( 'Salt', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'A random string that will make your SKUs really unique.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_hashids_salt',
		'default'  => wcj_get_option( 'wcj_sku_hashids_salt_default', '' ),
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Numbers of characters in SKU', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_hashids_sku_length',
		'default'  => 6,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'SKU Format', 'e-commerce-jetpack' ),
		'type'     => 'select',
		'options'  => array(
			'only_numbers'             => __( 'Only Numbers, e.g.: 836237', 'e-commerce-jetpack' ),
			'only_letters'             => __( 'Only Letters, e.g.: HFuAbQ', 'e-commerce-jetpack' ),
			'letters_and_numbers'      => __( 'Letters and Numbers, e.g.: 8a3M19 ', 'e-commerce-jetpack' ),
		),
		'default'  => 'letters_and_numbers',
		'id'       => 'wcj_sku_hashids_sku_format',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_sku_hashids_options',
	)
);
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Categories Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_sku_categories_options',
	),
	array(
		'title'    => __( 'Multiple Categories', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This options defines how to handle category prefixes and suffixes, if product has multiple categories.', 'e-commerce-jetpack' ),
		'type'     => 'select',
		'id'       => 'wcj_sku_categories_multiple',
		'default'  => 'first',
		'options'  => array(
			'first' => __( 'Use first category', 'e-commerce-jetpack' ),
			'glue'  => __( '"Glue" categories', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Category separator (i.e. "glue")', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Ignored if "Use first category" is selected above.', 'e-commerce-jetpack' ),
		'type'     => 'text',
		'id'       => 'wcj_sku_categories_multiple_glue',
		'default'  => '',
	),
) );
$product_categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ){
	foreach ( $product_categories as $product_category ) {
		$settings = array_merge( $settings, array(
			array(
				'title'    => $product_category->name,
				'desc'     => __( 'Prefix', 'e-commerce-jetpack' ),
				'id'       => 'wcj_sku_prefix_cat_' . $product_category->term_id,
				'default'  => '',
				'type'     => 'text',
				'desc_tip' => apply_filters( 'booster_message', '', 'desc_no_link' ),
				'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
			),
			array(
				'title'    => '',
				'desc'     => __( 'Suffix', 'e-commerce-jetpack' ),
				'id'       => 'wcj_sku_suffix_cat_' . $product_category->term_id,
				'default'  => '',
				'type'     => 'text',
			),
			array(
				'title'    => '',
				'desc'     => __( 'Counter (Sequential)', 'e-commerce-jetpack' ),
				'id'       => 'wcj_sku_counter_cat_' . $product_category->term_id,
				'default'  => 1,
				'type'     => 'number',
			),
		) );
	}
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_sku_categories_options',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'More Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_sku_more_options',
	),
	array(
		'title'    => __( 'Automatically Generate SKU for New Products', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Alternatively you can use Autogenerate SKUs tool.', 'e-commerce-jetpack' ) . '<br />' . __( 'If you want to generate SKU for variations, please try "Automatically Generate SKU for Variations on Product Save" option.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_new_products_generate_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Automatically Generate SKU for Variations on Product Save', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Once enabled it\'s recommended to be used with "Generate SKUs Only for Products with Empty SKU" option or else the SKUs will be replaced on product save.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_generate_on_save',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Generate SKU for New Products Only on First Publish', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This is important if, for example, you are using category prefix and don\'t want SKU generated too early, before you set the category.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_new_products_generate_only_on_publish',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Allow Duplicate SKUs', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you wish to set SKUs manually, and you need to have same SKUs for different products, you can enable allow duplicate SKUs option here (which is disabled in WooCommerce by default).', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_allow_duplicates_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Search by SKU', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Add product searching by SKU on frontend.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_search_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Advanced: Search by SKU Hook', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you are experiencing any issues with searching by SKU, try changing this option.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_search_hook',
		'default'  => 'pre_get_posts',
		'type'     => 'select',
		'options'  => array(
			'pre_get_posts' => 'pre_get_posts',
			'posts_search'  => 'posts_search',
		),
	),
	array(
		'title'    => __( 'Generate SKUs Only for Products with Empty SKU', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This may help if you are going to use Autogenerate SKUs tool, but don\'t want to change your existing SKUs.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_generate_only_for_empty_sku',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Add SKU to Customer Emails', 'e-commerce-jetpack' ),
		'desc'     => __( 'Add', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Adds product SKU to customer\'s emails.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_add_to_customer_emails',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Remove SKU from Admin Emails', 'e-commerce-jetpack' ),
		'desc'     => __( 'Remove', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Removes product SKU from admin\'s emails.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_remove_from_admin_emails',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Disable SKUs', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Enable this option if you are not going to use SKUs in your shop at all.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_sku_disabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_sku_more_options',
	),
) );
return $settings;
