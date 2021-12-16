<?php
/**
 * Booster for WooCommerce - Settings - Multicurrency (Currency Switcher)
 *
 * @version 5.4.3
 * @since   2.8.0
 * @author  Pluggabl LLC.
 * @todo    "pretty prices"
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$currency_from  = get_woocommerce_currency();
$all_currencies = wcj_get_woocommerce_currencies_and_symbols();
$settings = array(
	array(
		'title'    => __( 'General Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_multicurrency_options',
	),
	array(
		'title'    => __( 'Exchange Rates Updates', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Select how you want currency exchange rates to be updated. Possible options are: manually or automatically via Currency Exchange Rates module.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_exchange_rate_update_auto',
		'default'  => 'manual',
		'type'     => 'select',
		'options'  => array(
			'manual' => __( 'Enter Rates Manually', 'e-commerce-jetpack' ),
			'auto'   => __( 'Automatically via Currency Exchange Rates module', 'e-commerce-jetpack' ),
		),
		'desc'     => ( '' == apply_filters( 'booster_message', '', 'desc' ) ) ?
			__( 'Visit', 'e-commerce-jetpack' ) . ' <a href="' . admin_url( 'admin.php?page=wc-settings&tab=jetpack&wcj-cat=prices_and_currencies&section=currency_exchange_rates' ) . '">' . __( 'Currency Exchange Rates module', 'e-commerce-jetpack' ) . '</a>'
			:
			apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Multicurrency on per Product Basis', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If you enable this option, you will be able to enter prices for products in different currencies directly (i.e. without exchange rates). This will add meta boxes in product edit.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_per_product_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Variable products: list available/active variations only', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Defines which variations are listed on admin product edit page in Multicurrency meta box. Ignored if "Multicurrency on per Product Basis" option is disabled.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_per_product_list_available_variations_only',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Add option to make empty price', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_per_product_make_empty',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Revert Currency to Shop\'s Default', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Enable this if you want to revert the prices to your shop default currency, when customer reaches the cart and / or checkout page', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_revert',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'no'                => __( 'Do not revert', 'e-commerce-jetpack' ),
			'cart_only'         => __( 'Revert on cart page only', 'e-commerce-jetpack' ),
			'yes'               => __( 'Revert on checkout page only', 'e-commerce-jetpack' ),
			'cart_and_checkout' => __( 'Revert on both cart & checkout pages', 'e-commerce-jetpack' ),
		),
		'desc' => __( 'The customer selected currency as &#8364; and your shop currency is &#36;, So if you want to show &#36; on cart and / or checkout page you can use the above option.', 'e-commerce-jetpack' ),
	),
	array(
		'title'    => __( 'Rounding', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If using exchange rates, choose rounding here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_rounding',
		'default'  => 'no_round',
		'type'     => 'select',
		'options'  => array(
			'no_round'   => __( 'No rounding', 'e-commerce-jetpack' ),
			'round'      => __( 'Round', 'e-commerce-jetpack' ),
			'round_up'   => __( 'Round up', 'e-commerce-jetpack' ),
			'round_down' => __( 'Round down', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Rounding Precision', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If rounding is enabled, set rounding precision here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_rounding_precision',
		'default'  => absint( wcj_get_option( 'woocommerce_price_num_decimals', 2 ) ),
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'title'    => __( 'Currency Switcher Template', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Set how you want currency switcher to be displayed on frontend.', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%currency_name%', '%currency_symbol%', '%currency_code%' ) ),
		'id'       => 'wcj_multicurrency_switcher_template',
		'default'  => '%currency_name% (%currency_symbol%)',
		'type'     => 'text',
		'class'    => 'widefat',
	),
	array(
		'title'             => __( 'Convert Shipping Values', 'e-commerce-jetpack' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => __( 'Disable it if you have some other plugin already converting it like WPML.', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'id'                => 'wcj_multicurrency_convert_shipping_values',
		'default'           => 'yes',
		'type'              => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_multicurrency_options',
	),
	array(
		'title'    => __( 'Compatibility', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_multicurrency_compatibility',
	),
	array(
		'title'             => __( 'Free Shipping', 'e-commerce-jetpack' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc_tip'          => __( 'Converts minimum amount from WooCommerce Free Shipping native method.', 'e-commerce-jetpack' ),
		'id'                => 'wcj_multicurrency_compatibility_free_shipping',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	/*array(
		'title'    => __( 'Prices and Currencies by Country Module', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Switches currency according to country.', 'e-commerce-jetpack' ) . '<br />' . sprintf( __( 'Once Enabled, please set all the currency values from the <a href="%s">Country</a> module as 1. The MultiCurrency module values will be used instead.', 'e-commerce-jetpack' ), admin_url( 'admin.php?page=wc-settings&tab=jetpack&wcj-cat=prices_and_currencies&section=price_by_country' ) ),
		'id'       => 'wcj_multicurrency_compatibility_price_by_country_module',
		'default'  => 'no',
		'type'     => 'checkbox',
	),*/
	array(
		'title'    => __( 'WooCommerce Fixed Coupons', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'When a fixed coupon is used its value changes according to the current currency.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_compatibility_wc_coupons',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'             => __( 'WooCommerce Coupons - Min & Max amount', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => __( 'Converts min and max amount values from WooCommerce coupons.', 'e-commerce-jetpack' ),
		'id'                => 'wcj_multicurrency_compatibility_wc_coupons_min_max',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'title'    => __( 'WooCommerce Smart Coupons', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_compatibility_wc_smart_coupons',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'WooCommerce Price Filter', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Adds Compatibility with Price Filter widget.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_compatibility_wc_price_filter',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Price Sorting with Per Product', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Fixes Price Sorting if Per Product option is enabled.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_compatibility_price_sorting_per_product',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'WooCommerce Import', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Fixes WooCommerce Import Tool preventing it from converting some uppercase meta to lowercase.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_compatibility_wc_import',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'WPC Product Bundles', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Adds compatibility with <a href="%s" target="_blank">WPC Product Bundles</a> plugin.', 'e-commerce-jetpack' ), 'https://wordpress.org/plugins/woo-product-bundle/' ),
		'id'       => 'wcj_multicurrency_compatibility_wpc_product_bundle',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'             => __( 'WooCommerce Tree Table Rate Shipping', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => sprintf( __( 'Adds compatibility with <a href="%s" target="_blank">WooCommerce Tree Table Rate Shipping</a> plugin.', 'e-commerce-jetpack' ), 'https://tablerateshipping.com' ),
		'id'                => 'wcj_multicurrency_compatibility_wc_ttrs',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'title'             => __( 'Flexible Shipping', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => sprintf( __( 'Adds compatibility with <a href="%s" target="_blank">Flexible Shipping</a> plugin.', 'e-commerce-jetpack' ), 'https://flexibleshipping.com/' ),
		'id'                => 'wcj_multicurrency_compatibility_flexible_shipping',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'title'             => __( 'Pricing Deals Plugin', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => sprintf( __( 'Adds compatibility with <a href="%s" target="_blank">Pricing Deals</a> plugin.', 'e-commerce-jetpack' ), 'https://www.varktech.com/woocommerce/woocommerce-dynamic-pricing-discounts-pro/' ),
		'id'                => 'wcj_multicurrency_compatibility_pricing_deals',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'title'             => __( 'Product Add-Ons Plugin', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => sprintf( __( 'Adds compatibility with <a href="%s" target="_blank">Product Add-Ons</a> plugin.', 'e-commerce-jetpack' ), 'https://woocommerce.com/products/product-add-ons/' ) . '<br />' . __( 'Only works with <code>Multicurrency on per Product Basis</code> option disabled.', 'e-commerce-jetpack' ),
		'id'                => 'wcj_multicurrency_compatibility_product_addons',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'title'             => __( 'WooCommerce Attribute Swatches by Iconic Plugin', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => __( 'Fixes cart item price issue if the <code>WooCommerce Attribute Swatches by Iconic</code> Plugin is activated', 'e-commerce-jetpack'),
		'id'                => 'wcj_multicurrency_compatibility_wc_attribute_swatches_premium_variable_cart_item_price',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	/*array(
		'title'             => __( 'Advanced Dynamic Pricing For Woocommerce', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => sprintf( __( 'Adds compatibility with <a href="%s" target="_blank">Advanced Dynamic Pricing For Woocommerce</a> plugin.', 'e-commerce-jetpack' ), 'https://algolplus.com/plugins/downloads/advanced-dynamic-pricing-woocommerce-pro/' ) . '<br />' . __( 'Calculation option <code>Use prices modified by other plugins</code> must be ON.', 'e-commerce-jetpack' ). '<br />' . __( 'System option <code>Suppress other pricing plugins in frontend</code> must be OFF.', 'e-commerce-jetpack' ),
		'id'                => 'wcj_multicurrency_compatibility_adv_dyn_pricing_wc',
		'default'           => 'no',
		'type'              => 'checkbox',
	),*/
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_multicurrency_compatibility',
	),
	array(
		'title'    => __( 'Advanced', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_multicurrency_adv',
	),
	array(
		'title'    => __( 'Additional Price Filters', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Add additional price filters here. One per line. Leave blank if not sure.' ),
		'desc'     => sprintf( __( 'E.g.: %s' ), '<code>' . 'woocommerce_subscriptions_product_price' . '</code>' . ', ' .'<code>' . 'woocommerce_get_price' . '</code>' . '.' ),
		'id'       => 'wcj_multicurrency_switcher_additional_price_filters',
		'default'  => '',
		'type'     => 'textarea',
		'css'      => 'min-width:300px;height:150px;',
	),
	array(
		'title'    => __( 'Price Filters Priority', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Priority for all module\'s price filters. If you face pricing issues while using another plugin or booster module, You can change the Priority, Greater value for high priority & Lower value for low priority. Set to zero to use default priority.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_advanced_price_hooks_priority',
		'default'  => 0,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'Save Prices on Exchange Update', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Save min and max prices on exchange rate update, via background processing.', 'e-commerce-jetpack' ) . '<br />' . __( 'All products with "per product" options registered related to the currency will be affected.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_update_prices_on_exch_update',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Save Calculated Products Prices', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'This may help if you are experiencing compatibility issues with other plugins.', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_multicurrency_save_prices',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_multicurrency_options_adv',
	),
	array(
		'title'    => __( 'Currencies Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'One currency probably should be set to current (original) shop currency with an exchange rate of 1.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_currencies_options',
	),
	array(
		'title'    => __( 'Total Currencies', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Press Save changes after setting this option, so new settings fields will be added.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_total_number',
		'default'  => 2,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ? apply_filters( 'booster_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min'  => '2', )
		),
	),
);
$total_number = apply_filters( 'booster_option', 2, wcj_get_option( 'wcj_multicurrency_total_number', 2 ) );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$currency_to = wcj_get_option( 'wcj_multicurrency_currency_' . $i, $currency_from );
	$custom_attributes = array(
		'currency_from'        => $currency_from,
		'currency_to'          => $currency_to,
		'multiply_by_field_id' => 'wcj_multicurrency_exchange_rate_' . $i,
	);
	if ( $currency_from == $currency_to ) {
		$custom_attributes['disabled'] = 'disabled';
	}
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Currency', 'e-commerce-jetpack' ) . ' #' . $i,
			'id'       => 'wcj_multicurrency_currency_' . $i,
			'default'  => $currency_from,
			'type'     => 'select',
			'options'  => $all_currencies,
			'css'      => 'width:250px;',
		),
		array(
			'title'                    => '',
			'id'                       => 'wcj_multicurrency_exchange_rate_' . $i,
			'default'                  => 1,
			'type'                     => 'exchange_rate',
			'custom_attributes_button' => $custom_attributes,
			'value'                    => $currency_from . '/' . $currency_to,
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_multicurrency_currencies_options',
	),
) );

// Default currency
$currencies = array();
for ( $i = 1; $i <= $total_number; $i ++ ) {
	$currency_to             = wcj_get_option( 'wcj_multicurrency_currency_' . $i, $currency_from );
	$currencies[ $i ] = $all_currencies[ $currency_to ];
}
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Default Currency', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => __( 'The default currency displayed on frontend.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_default_currency_opt',
	),
	array(
		'title'    => __( 'Currency', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_default_currency',
		'default'  => 1,
		'type'     => 'select',
		'options'  => $currencies,
		'desc'     => '',
		'desc_tip' => __( 'The default currency will only be set if the current user hasn\'t selected it yet.', 'e-commerce-jetpack' ),
	),
	array(
		'title'    => __( 'Force', 'e-commerce-jetpack' ),
		'id'       => 'wcj_multicurrency_default_currency_force',
		'default'  => 'no',
		'type'     => 'checkbox',
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'If enabled, the default currency will be fixed and users won\'t be able to change it.', 'e-commerce-jetpack' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_multicurrency_default_currency_opt',
	),
));

$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Role Defaults', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => sprintf( __( 'Custom roles can be added via "Add/Manage Custom Roles" tool in Booster\'s <a href="%s">General</a> module.', 'e-commerce-jetpack' ),
			admin_url( 'admin.php?page=wc-settings&tab=jetpack&wcj-cat=emails_and_misc&section=general' ) ),
		'id'       => 'wcj_multicurrency_role_defaults_options',
	),
	array(
		'title'    => __( 'Roles', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Save settings after you change this option. Leave blank to disable.', 'e-commerce-jetpack' ),
		'type'     => 'multiselect',
		'id'       => 'wcj_multicurrency_role_defaults_roles',
		'default'  => '',
		'class'    => 'chosen_select',
		'options'  => wcj_get_user_roles_options(),
	),
) );
$module_currencies = array();
for ( $i = 1; $i <= $total_number; $i++ ) {
	$currency_code = wcj_get_option( 'wcj_multicurrency_currency_' . $i, $currency_from );
	$module_currencies[ $currency_code ] = $all_currencies[ $currency_code ];
}
$module_currencies = array_unique( $module_currencies );
$module_roles = wcj_get_option( 'wcj_multicurrency_role_defaults_roles', '' );
if ( ! empty( $module_roles ) ) {
	foreach ( $module_roles as $role_key ) { // wcj_get_user_roles() as $role_key => $role_data
		$settings = array_merge( $settings, array(
			array(
				'title'    => $role_key, // $role_data['name'],
				'id'       => 'wcj_multicurrency_role_defaults_' . $role_key,
				'default'  => '',
				'type'     => 'select',
				'options'  => array_merge( array( '' => __( 'No default currency', 'e-commerce-jetpack' ) ), $module_currencies ),
			),
		) );
	}
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_multicurrency_role_defaults_options',
	),
) );
return $settings;
