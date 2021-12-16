<?php
/**
 * Booster for WooCommerce - Settings - Reports
 *
 * @version 3.9.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$reports_and_settings = array(
	array(
		'title'     => __( 'Orders Reports', 'e-commerce-jetpack' ),
		'type'      => 'title',
		'id'        => 'wcj_reports_orders_options',
	),
	array(
		'title'     => __( 'Product Sales (Daily)', 'e-commerce-jetpack' ),
		'tab'       => 'orders',
		'tab_title' => __( 'Orders', 'e-commerce-jetpack' ),
		'report'    => 'booster_products_sales_daily',
	),
	array(
		'id'        => 'wcj_reports_products_sales_daily_columns',
		'desc'      => __( 'Report columns', 'e-commerce-jetpack' ),
		'desc_tip'  => __( 'Set empty to include all columns.', 'e-commerce-jetpack' ),
		'type'      => 'multiselect',
		'default'   => '',
		'options'   => wcj_get_product_sales_daily_report_columns(),
		'class'     => 'chosen_select',
	),
	array(
		'id'        => 'wcj_reports_products_sales_daily_order_statuses',
		'desc'      => __( 'Order statuses', 'e-commerce-jetpack' ),
		'desc_tip'  => __( 'Set empty to include all statuses.', 'e-commerce-jetpack' ),
		'type'      => 'multiselect',
		'default'   => '',
		'options'   => wc_get_order_statuses(),
		'class'     => 'chosen_select',
	),
	array(
		'id'        => 'wcj_reports_products_sales_daily_include_taxes',
		'desc'      => __( 'Include taxes', 'e-commerce-jetpack' ),
		'type'      => 'checkbox',
		'default'   => 'no',
		'checkboxgroup' => 'start',
	),
	array(
		'id'        => 'wcj_reports_products_sales_daily_count_variations',
		'desc'      => __( 'Count variations for variable products', 'e-commerce-jetpack' ),
		'type'      => 'checkbox',
		'default'   => 'no',
		'checkboxgroup' => 'end',
	),
	array(
		'title'     => __( 'Product Sales (Monthly)', 'e-commerce-jetpack' ),
		'tab'       => 'orders',
		'tab_title' => __( 'Orders', 'e-commerce-jetpack' ),
		'report'    => 'booster_products_sales',
	),
	array(
		'id'        => 'wcj_reports_products_sales_display_sales',
		'desc'      => __( 'Display item sales', 'e-commerce-jetpack' ),
		'type'      => 'checkbox',
		'checkboxgroup' => 'start',
		'default'   => 'yes',
	),
	array(
		'id'        => 'wcj_reports_products_sales_display_sales_sum',
		'desc'      => __( 'Display sales sum', 'e-commerce-jetpack' ),
		'type'      => 'checkbox',
		'default'   => 'yes',
		'checkboxgroup' => '',
	),
	array(
		'id'        => 'wcj_reports_products_sales_display_profit',
		'desc'      => __( 'Display profit', 'e-commerce-jetpack' ),
		'type'      => 'checkbox',
		'default'   => 'no',
		'checkboxgroup' => '',
	),
	array(
		'id'        => 'wcj_reports_products_sales_include_taxes',
		'desc'      => __( 'Include taxes', 'e-commerce-jetpack' ),
		'type'      => 'checkbox',
		'default'   => 'no',
		'checkboxgroup' => '',
	),
	array(
		'id'        => 'wcj_reports_products_sales_count_variations',
		'desc'      => __( 'Count variations for variable products', 'e-commerce-jetpack' ),
		'type'      => 'checkbox',
		'default'   => 'no',
		'checkboxgroup' => 'end',
	),
	array(
		'title'     => __( 'Monthly Sales (with Currency Conversion)', 'e-commerce-jetpack' ),
		'tab'       => 'orders',
		'tab_title' => __( 'Orders', 'e-commerce-jetpack' ),
		'report'    => 'booster_monthly_sales',
	),
	array(
		'id'        => 'wcj_reports_orders_monthly_sales_include_today',
		'desc'      => __( 'Include current day for current month', 'e-commerce-jetpack' ),
		'type'      => 'checkbox',
		'default'   => 'no',
		'checkboxgroup' => 'start',
	),
	array(
		'id'        => 'wcj_reports_orders_monthly_sales_forecast',
		'desc'      => __( 'Forecast total orders and sum (excl. TAX) for current month and year', 'e-commerce-jetpack' ),
		'type'      => 'checkbox',
		'default'   => 'no',
		'checkboxgroup' => 'end',
	),
	array(
		'title'     => __( 'Payment Gateways', 'e-commerce-jetpack' ),
		'tab'       => 'orders',
		'tab_title' => __( 'Orders', 'e-commerce-jetpack' ),
		'report'    => 'booster_gateways',
	),
	array(
		'id'        => 'wcj_reports_orders_options',
		'type'      => 'sectionend',
	),
	array(
		'title'     => __( 'Customers Reports', 'e-commerce-jetpack' ),
		'type'      => 'title',
		'id'        => 'wcj_reports_customers_options',
	),
	array(
		'title'     => __( 'Customers by Country', 'e-commerce-jetpack' ),
		'tab'       => 'customers',
		'tab_title' => __( 'Customers', 'e-commerce-jetpack' ),
		'report'    => 'customers_by_country',
	),
	array(
		'title'     => __( 'Customers by Country Sets', 'e-commerce-jetpack' ),
		'tab'       => 'customers',
		'tab_title' => __( 'Customers', 'e-commerce-jetpack' ),
		'report'    => 'customers_by_country_sets',
	),
	array(
		'id'        => 'wcj_reports_customers_options',
		'type'      => 'sectionend',
	),
	array(
		'title'     => __( 'Stock Reports', 'e-commerce-jetpack' ),
		'type'      => 'title',
		'id'        => 'wcj_reports_stock_options',
	),
	array(
		'title'     => __( 'All in Stock with sales data', 'e-commerce-jetpack' ),
		'tab'       => 'stock',
		'tab_title' => __( 'Stock', 'e-commerce-jetpack' ),
		'report'    => 'on_stock',
	),
	array(
		'title'     => __( 'Understocked products (calculated by sales data)', 'e-commerce-jetpack' ),
		'tab'       => 'stock',
		'tab_title' => __( 'Stock', 'e-commerce-jetpack' ),
		'report'    => 'understocked',
	),
	array(
		'title'     => __( 'Overstocked products (calculated by sales data)', 'e-commerce-jetpack' ),
		'tab'       => 'stock',
		'tab_title' => __( 'Stock', 'e-commerce-jetpack' ),
		'report'    => 'overstocked',
	),
	array(
		'id'        => 'wcj_reports_stock_product_type',
		'desc'      => __( 'product type', 'e-commerce-jetpack' ),
		'desc_tip'  => __( 'Product type for all "Stock" reports.', 'e-commerce-jetpack' ),
		'type'      => 'select',
		'default'   => 'product',
		'options'   => array(
			'product'           => __( 'Products', 'e-commerce-jetpack' ),
			'product_variation' => __( 'Variations', 'e-commerce-jetpack' ),
			'both'              => __( 'Both products and variations', 'e-commerce-jetpack' ),
		),
	),
	array(
		'id'        => 'wcj_reports_stock_include_deleted_products',
		'desc'      => __( 'Include deleted products', 'e-commerce-jetpack' ),
		'desc_tip'  => __( 'Include deleted products in all "Stock" reports.', 'e-commerce-jetpack' ),
		'type'      => 'checkbox',
		'default'   => 'yes',
	),
	array(
		'id'        => 'wcj_reports_stock_options',
		'type'      => 'sectionend',
	),
);
$settings     = array();
$button_style = "background: orange; border-color: orange; box-shadow: 0 1px 0 orange; text-shadow: 0 -1px 1px orange,1px 0 1px orange,0 1px 1px orange,-1px 0 1px orange;";
foreach ( $reports_and_settings as $report ) {
	if ( isset( $report['report'] ) ) {
		$settings = array_merge( $settings, array(
			array(
				'title'    => $report['title'],
				'desc_tip' => 'WooCommerce > Reports > ' . $report['tab_title'] . ' > ' . $report['title'],
				'id'       => 'wcj_' . $report['report'] . '_link',
				'type'     => 'custom_link',
				'link'     => '<a class="button-primary" '
					. 'style="' . $button_style . '" '
					. 'href="' . get_admin_url() . 'admin.php?page=wc-reports&tab=' . $report['tab'] . '&report=' . $report['report'] . '">'
					. __( 'View report', 'e-commerce-jetpack' ) . '</a>',
			),
		) );
	} else {
		$settings = array_merge( $settings, array ( $report ) );
	}
}
return $settings;
