<?php
/**
 * Booster for WooCommerce - Settings - Prices and Currencies by Country
 *
 * @version 5.4.0
 * @since   2.8.1
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$autogenerate_buttons      = array();
$autogenerate_buttons_data = array(
	'all'           => __( 'All countries and currencies', 'e-commerce-jetpack' ),
	'paypal_only'   => __( 'PayPal supported currencies only', 'e-commerce-jetpack' ),
);
foreach ( $autogenerate_buttons_data as $autogenerate_button_id => $autogenerate_button_desc ) {
	$autogenerate_buttons[] = ( 1 === apply_filters( 'booster_option', 1, '' ) ?
	'<a class="button" disabled title="' . __( 'Available in Booster Plus only.', 'e-commerce-jetpack' ) . '">' . $autogenerate_button_desc . '</a>' :
	'<a class="button" href="' .
		esc_url( add_query_arg( 'wcj_generate_country_groups', $autogenerate_button_id, remove_query_arg( 'recalculate_price_filter_products_prices' ) ) ) . '"' .
		wcj_get_js_confirmation( __( 'All existing country groups will be deleted and new groups will be created. Are you sure?', 'e-commerce-jetpack' ) ) . '>' .
			$autogenerate_button_desc .
	'</a>' );
}
$autogenerate_buttons = implode( ' ', $autogenerate_buttons );

$settings = array(
	array(
		'title'    => __( 'Price by Country Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'Change product\'s price and currency by customer\'s country. Customer\'s country is detected automatically by IP, or selected by customer manually.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_options',
	),
	array(
		'title'    => __( 'Customer Country Detection Method', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_customer_country_detection_method',
		'desc'     => __( 'If you choose "by user selection", use [wcj_country_select_drop_down_list] shortcode to display country selection list on frontend.', 'e-commerce-jetpack' ),
		'default'  => 'by_ip',
		'type'     => 'select',
		'options'  => array(
			'by_ip'                        => __( 'by IP', 'e-commerce-jetpack' ),
			'by_ip_then_by_user_selection' => __( 'by IP, then by user selection', 'e-commerce-jetpack' ),
			'by_user_selection'            => __( 'by user selection', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Override Country Options', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_override_on_checkout_with_billing_country',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'no'               => __( 'No Override', 'e-commerce-jetpack' ),
			'yes'              => __( 'Override Country with Customer\'s Checkout Billing Country', 'e-commerce-jetpack' ),
			'shipping_country' => __( 'Override Country with Customer\'s Checkout Shipping Country', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Override Scope', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_override_scope',
		'default'  => 'all',
		'type'     => 'select',
		'options'  => array(
			'all'        => __( 'All site', 'e-commerce-jetpack' ),
			'checkout'   => __( 'Checkout only', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Revert Currency to Default on Checkout', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_revert',
		'default'  => 'no',
		'type'     => 'checkbox',
		'desc_tip' => __( 'If selected currency is &#8364; and your shop currency is &#36;, So if you want to show &#36; on the checkout page you can enable the option.', 'e-commerce-jetpack' ),
	),
	array(
		'title'    => __( 'Auto set default checkout billing country', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_set_dft_checkout_billing_country',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Price Rounding', 'e-commerce-jetpack' ),
		'desc'     => __( 'If you choose to multiply price, set rounding options here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_rounding',
		'default'  => 'none',
		'type'     => 'select',
		'options'  => array(
			'none'  => __( 'No rounding', 'e-commerce-jetpack' ),
			'round' => __( 'Round', 'e-commerce-jetpack' ),
			'floor' => __( 'Round down', 'e-commerce-jetpack' ),
			'ceil'  => __( 'Round up', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Make Pretty Price', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If enabled, this will be applied if exchange rates are used. Final converted price will be rounded, then decreased by smallest possible value. For example: $9,75 -> $10,00 -> $9,99. Please note that as smallest possible value is calculated from shop\'s "Precision" option, this option must be above zero.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_make_pretty',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Discount Min Amount Multiplier', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If "Make Pretty Price" is enabled, here you can set by how many smallest possible values (e.g. cents) final price should be decreased.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_make_pretty_min_amount_multiplier',
		'default'  => 1,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => '1' ),
	),
	array(
		'title'    => __( 'Price by Country on per Product Basis', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This will add product data fields in product edit.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_local_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Per product options - backend style', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_local_options_style',
		'default'  => 'inline',
		'type'     => 'select',
		'options'  => array(
			'inline'   => __( 'Inline', 'e-commerce-jetpack' ),
			'meta_box' => __( 'Separate meta box', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Per product options - backend user role visibility', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Leave empty to show to all user roles.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_backend_user_roles',
		'default'  => '',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => wcj_get_user_roles_options(),
	),
	array(
		'title'    => __( 'Add Countries Flags Images to Select Drop-Down Box', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you are using [wcj_country_select_drop_down_list] shortcode or "Booster: Country Switcher" widget, this will add country flags to these select boxes.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_jquery_wselect_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Search Engine Bots', 'e-commerce-jetpack' ),
		'desc'     => __( 'Disable Price by Country for Bots', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_for_bots_disabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	/*array(
		'title'    => __( 'Currency Code on Admin', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Changes the currency code on admin based on the current country group id.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_curr_code_admin',
		'default'  => 'no',
		'type'     => 'checkbox',
	),*/
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_price_by_country_options',
	),
	array(
		'title'    => __( 'Compatibility', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_price_by_country_compatibility',
	),


    array(
        'title' => __('Disable Quick Edit Product For Admin Scope', 'e-commerce-jetpack'),
        'desc' => __('Disable For Admin Quick Edit Scope.', 'e-commerce-jetpack'),
        'desc_tip' => __("Disable module on Edit Product For Admin scope.", 'e-commerce-jetpack') . '<br />' . __('For example if you use Quick Edit Product  and donot want change the deafult price then the  box ticked', 'e-commerce-jetpack'),
        'type' => 'checkbox',
        'id' => 'wcj_price_by_country_admin_quick_edit_product_scope',
        'default' => 'no',
    ),

	array(
		'title'             => __( 'Price Filter Widget and Sorting by Price Support', 'e-commerce-jetpack' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc_tip'          => '<a href="' . add_query_arg( 'recalculate_price_filter_products_prices', '1', remove_query_arg( array( 'wcj_generate_country_groups' ) ) ) . '">' .
		                       __( 'Recalculate price filter widget and sorting by price product prices.', 'e-commerce-jetpack' ) . '</a>',
		'id'                => 'wcj_price_by_country_price_filter_widget_support_enabled',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'title'             => __( 'Free Shipping', 'e-commerce-jetpack' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc_tip'          => __( 'Converts minimum amount from WooCommerce Free Shipping native method.', 'e-commerce-jetpack' ),
		'id'                => 'wcj_price_by_country_compatibility_free_shipping',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'title'    => __( 'WooCommerce Coupons', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'When a fixed coupon is used its value changes according to the current currency.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_compatibility_wc_coupons',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'             => __( 'Woo Discount Rules', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => sprintf( __( 'Adds compatibility with <a href="%s" target="_blank">Woo Discount Rules</a> plugin.', 'e-commerce-jetpack' ), 'https://www.flycart.org/products/wordpress/woocommerce-discount-rules' ).'<br />'. sprintf( __( 'If it doesn\'t work properly try to enable <a href="%s">redirect to the cart page after successful addition</a> option.', 'e-commerce-jetpack' ), admin_url( 'admin.php?page=wc-settings&tab=products' ) ),
		'id'                => 'wcj_price_by_country_compatibility_woo_discount_rules',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'title'             => __( 'WooCommerce Points and Rewards', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => sprintf( __( 'Adds compatibility with <a href="%s" target="_blank">WooCommerce Points and Rewards</a> plugin.', 'e-commerce-jetpack' ), 'https://woocommerce.com/products/woocommerce-points-and-rewards/' ),
		'id'                => 'wcj_price_by_country_comp_woo_points_rewards',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_price_by_country_compatibility',
	),
	array(
		'title'    => __( 'Advanced', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_price_by_country_country_advanced',
	),
	array(
		'title'    => __( 'Price Filters Priority', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Priority for all module\'s price filters. If you face pricing issues while using another plugin or booster module, You can change the Priority, Greater value for high priority & Lower value for low priority. Set to zero to use default priority.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_advanced_price_hooks_priority',
		'default'  => 0,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'User IP Detection Method', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_ip_detection_method',
		'default'  => 'wc',
		'type'     => 'select',
		'options'  => array(
			'wc'      => __( 'WooCommerce', 'e-commerce-jetpack' ),
			'booster' => __( 'Booster', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Price Format Method', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'The moment "Pretty Price" and "Rounding" will be applied' ),
		'id'       => 'wcj_price_by_country_price_format_method',
		'default'  => 'get_price',
		'type'     => 'select',
		'options'  => array(
			'get_price'               => __( 'get_price()', 'e-commerce-jetpack' ),
			'wc_get_price_to_display' => __( 'wc_get_price_to_display()', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Save Calculated Products Prices', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This may help if you are experiencing compatibility issues with other plugins.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_save_prices',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Save Country Group ID', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Try to disable it if the country detection is not correct, most probably if "Override Country Options" is enabled.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_save_country_group_id',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_price_by_country_country_advanced',
	),
	array(
		'title'    => __( 'Country Groups', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_price_by_country_country_groups_options',
	),
	array(
		'title'    => __( 'Countries Selection', 'e-commerce-jetpack' ),
		'desc'     => __( 'Choose how do you want to enter countries groups in admin.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_selection',
		'default'  => 'chosen_select',
		'type'     => 'select',
		'options'  => array(
			'comma_list'    => __( 'Comma separated list', 'e-commerce-jetpack' ),
			'multiselect'   => __( 'Multiselect', 'e-commerce-jetpack' ),
			'chosen_select' => __( 'Chosen select', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Autogenerate Groups', 'e-commerce-jetpack' ),
		'id'       => 'wcj_' . $this->id . '_module_tools',
		'type'     => 'custom_link',
		'link'     => $autogenerate_buttons,
	),
	array(
		'title'    => __( 'Groups Number', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_total_groups_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ? apply_filters( 'booster_message', '', 'readonly' ) : array(),
			array('step' => '1', 'min' => '1', ) ),
		'css'      => 'width:100px;',
	),
);
for ( $i = 1; $i <= apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {
	$admin_title = wcj_get_option( 'wcj_price_by_country_countries_group_admin_title_' . $i, __( 'Group', 'e-commerce-jetpack' ) . ' #' . $i );
	if ( __( 'Group', 'e-commerce-jetpack' ) . ' #' . $i == $admin_title ) {
		$admin_title = '';
	} else {
		$admin_title = ': ' . $admin_title;
	}
	$admin_title = __( 'Group', 'e-commerce-jetpack' ) . ' #' . $i . $admin_title;
	switch ( wcj_get_option( 'wcj_price_by_country_selection', 'comma_list' ) ) {
		case 'comma_list':
			$settings[] = array(
				'title'    => $admin_title . ( '' != wcj_get_option( 'wcj_price_by_country_exchange_rate_countries_group_' . $i, '' ) ?
					' (' . count( explode( ',', wcj_get_option( 'wcj_price_by_country_exchange_rate_countries_group_' . $i, '' ) ) ) . ')' : '' ),
				'desc'     => __( 'Countries. List of comma separated country codes.<br>For country codes and predefined sets visit <a href="https://booster.io/country-codes/" target="_blank">https://booster.io/country-codes/</a>', 'e-commerce-jetpack' ),
				'id'       => 'wcj_price_by_country_exchange_rate_countries_group_' . $i,
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'width:50%;min-width:300px;height:100px;',
			);
			break;
		case 'multiselect':
			$settings[] = array(
				'title'    => $admin_title . ( is_array( wcj_get_option( 'wcj_price_by_country_countries_group_' . $i, '' ) ) ?
					' (' . count( wcj_get_option( 'wcj_price_by_country_countries_group_' . $i, '' ) ) . ')' : '' ),
				'id'       => 'wcj_price_by_country_countries_group_' . $i,
				'default'  => '',
				'type'     => 'multiselect',
				'options'  => wcj_get_countries(),
				'css'      => 'width:50%;min-width:300px;height:100px;',
			);
			break;
		case 'chosen_select':
			$settings[] = array(
				'title'    => $admin_title . ( is_array( wcj_get_option( 'wcj_price_by_country_countries_group_chosen_select_' . $i, '' ) ) ?
					' (' . count( wcj_get_option( 'wcj_price_by_country_countries_group_chosen_select_' . $i, '' ) ) . ')' : '' ),
				'id'       => 'wcj_price_by_country_countries_group_chosen_select_' . $i,
				'default'  => '',
				'type'     => 'multiselect',
				'options'  => wcj_get_countries(),
				'class'    => 'chosen_select',
				'css'      => 'width:50%;min-width:300px;',
			);
			break;
	}
	$settings = array_merge( $settings, array(
		array(
			'desc'     => __( 'Currency', 'e-commerce-jetpack' ),
			'id'       => 'wcj_price_by_country_exchange_rate_currency_group_' . $i,
			'default'  => 'EUR',
			'type'     => 'select',
			'options'  => wcj_get_woocommerce_currencies_and_symbols(),
		),
		array(
			'desc'     => __( 'Admin Title', 'e-commerce-jetpack' ),
			'id'       => 'wcj_price_by_country_countries_group_admin_title_' . $i,
			'default'  => __( 'Group', 'e-commerce-jetpack' ) . ' #' . $i,
			'type'     => 'text',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_price_by_country_country_groups_options',
	),
	array(
		'title'    => __( 'Exchange Rates', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_price_by_country_exchange_rate_options',
	),
	array(
		'title'    => __( 'Exchange Rates Updates', 'e-commerce-jetpack' ),
		'id'       => 'wcj_price_by_country_auto_exchange_rates',
		'default'  => 'manual',
		'type'     => 'select',
		'options'  => array(
			'manual'   => __( 'Enter Rates Manually', 'e-commerce-jetpack' ),
			'auto'     => __( 'Automatically via Currency Exchange Rates module', 'e-commerce-jetpack' ),
		),
		'desc'     => ( '' == apply_filters( 'booster_message', '', 'desc' ) )
			? __( 'Visit', 'e-commerce-jetpack' ) . ' <a href="' . admin_url( 'admin.php?page=wc-settings&tab=jetpack&wcj-cat=prices_and_currencies&section=currency_exchange_rates' ) . '">' . __( 'Currency Exchange Rates module', 'e-commerce-jetpack' ) . '</a>'
			: apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
) );
$currency_from = apply_filters( 'woocommerce_currency', wcj_get_option('woocommerce_currency') );
for ( $i = 1; $i <= apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {
	$currency_to = wcj_get_option( 'wcj_price_by_country_exchange_rate_currency_group_' . $i );
	$custom_attributes = array(
		'currency_from' => $currency_from,
		'currency_to'   => $currency_to,
		'multiply_by_field_id'   => 'wcj_price_by_country_exchange_rate_group_' . $i,
	);
	if ( $currency_from == $currency_to ) {
		$custom_attributes['disabled'] = 'disabled';
	}
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Group', 'e-commerce-jetpack' ) . ' #' . $i,
			'desc'     => __( 'Multiply Price by', 'e-commerce-jetpack' ),
			'id'       => 'wcj_price_by_country_exchange_rate_group_' . $i,
			'default'  => 1,
			'type'     => 'exchange_rate',
			'custom_attributes_button' => $custom_attributes,
			'value'    => $currency_from . '/' . $currency_to,
		),
		array(
			'desc'     => __( 'Make empty price', 'e-commerce-jetpack' ),
			'id'       => 'wcj_price_by_country_make_empty_price_group_' . $i,
			'default'  => 'no',
			'type'     => 'checkbox',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_price_by_country_exchange_rate_options',
	),
) );
return $settings;
