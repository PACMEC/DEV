<?php
/**
 * Booster for WooCommerce - Functions - Reports
 *
 * @version 3.2.4
 * @since   2.9.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'wcj_get_product_sales_daily_report_columns' ) ) {
	/*
	 * wcj_get_product_sales_daily_report_columns.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 */
	function wcj_get_product_sales_daily_report_columns() {
		return array(
			'date'                   => __( 'Date', 'e-commerce-jetpack' ),
			'daily_total_sum'        => __( 'Daily Total Sum', 'e-commerce-jetpack' ),
			'daily_total_quantity'   => __( 'Daily Total Quantity', 'e-commerce-jetpack' ),
			'product_id'             => __( 'Product ID', 'e-commerce-jetpack' ),
			'item_title'             => __( 'Item Title', 'e-commerce-jetpack' ),
			'item_quantity'          => __( 'Quantity', 'e-commerce-jetpack' ),
			'sum'                    => __( 'Sum', 'e-commerce-jetpack' ),
			'profit'                 => __( 'Profit', 'e-commerce-jetpack' ),
			'last_sale'              => __( 'Last Sale Date', 'e-commerce-jetpack' ),
			'last_sale_order_id'     => __( 'Last Sale Order ID', 'e-commerce-jetpack' ),
			'last_sale_order_status' => __( 'Last Sale Order Status', 'e-commerce-jetpack' ),
		);
	}
}

if ( ! function_exists( 'wcj_get_reports_standard_ranges' ) ) {
	/*
	 * wcj_get_reports_standard_ranges.
	 *
	 * @version 3.2.4
	 * @since   2.9.0
	 */
	function wcj_get_reports_standard_ranges() {
		$current_time = (int) current_time( 'timestamp' );
		return array(
			'year' => array(
				'title'      => __( 'Year', 'woocommerce' ),
				'start_date' => date( 'Y-01-01', $current_time ),
				'end_date'   => date( 'Y-m-d', $current_time ),
			),
			'last_month' => array(
				'title'      => __( 'Last month', 'woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( 'first day of previous month', $current_time ) ),
				'end_date'   => date( 'Y-m-d', strtotime( 'last day of previous month', $current_time )  ),
			),
			'this_month' => array(
				'title'      => __( 'This month', 'woocommerce' ),
				'start_date' => date( 'Y-m-01', $current_time ),
				'end_date'   => date( 'Y-m-d', $current_time ),
			),
			'last_7_days' => array(
				'title'      => __( 'Last 7 days', 'woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( '-7 days', $current_time ) ),
				'end_date'   => date( 'Y-m-d', $current_time ),
			),
		);
	}
}

if ( ! function_exists( 'wcj_get_reports_custom_ranges' ) ) {
	/*
	 * wcj_get_reports_custom_ranges.
	 *
	 * @version 3.2.4
	 * @since   2.9.0
	 * @todo    fix `-1 month` - sometimes it produces the wrong result (e.g. on current date = "2018.03.30")
	 */
	function wcj_get_reports_custom_ranges() {
		$current_time = (int) current_time( 'timestamp' );
		return array(
			'last_14_days' => array(
				'title'      => __( 'Last 14 days', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-d', strtotime( '-14 days', $current_time ) ),
				'end_date'   => date( 'Y-m-d', $current_time ),
			),
			'last_30_days' => array(
				'title'      => __( 'Last 30 days', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-d', strtotime( '-30 days', $current_time ) ),
				'end_date'   => date( 'Y-m-d', $current_time ),
			),
			'last_3_months' => array(
				'title'      => __( 'Last 3 months', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-d', strtotime( '-3 months', $current_time ) ),
				'end_date'   => date( 'Y-m-d', $current_time ),
			),
			'last_6_months' => array(
				'title'      => __( 'Last 6 months', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-d', strtotime( '-6 months', $current_time ) ),
				'end_date'   => date( 'Y-m-d', $current_time ),
			),
			'last_12_months' => array(
				'title'      => __( 'Last 12 months', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-d', strtotime( '-12 months', $current_time ) ),
				'end_date'   => date( 'Y-m-d', $current_time ),
			),
			'last_24_months' => array(
				'title'      => __( 'Last 24 months', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-d', strtotime( '-24 months', $current_time ) ),
				'end_date'   => date( 'Y-m-d', $current_time ),
			),
			'last_36_months' => array(
				'title'      => __( 'Last 36 months', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-d', strtotime( '-36 months', $current_time ) ),
				'end_date'   => date( 'Y-m-d', $current_time ),
			),
			'same_days_last_month' => array(
				'title'      => __( 'Same days last month', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-01', strtotime( '-1 month', $current_time ) ),
				'end_date'   => date( 'Y-m-d',  strtotime( '-1 month', $current_time ) ),
			),
			'same_days_last_year' => array(
				'title'      => __( 'Same days last year', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-01', strtotime( '-1 year', $current_time ) ),
				'end_date'   => date( 'Y-m-d',  strtotime( '-1 year', $current_time ) ),
			),
			'last_year' => array(
				'title'      => __( 'Last year', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-01-01', strtotime( '-1 year', $current_time ) ),
				'end_date'   => date( 'Y-12-31', strtotime( '-1 year', $current_time ) ),
			),
			'yesterday' => array(
				'title'      => __( 'Yesterday', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-d', strtotime( '-1 day', $current_time ) ),
				'end_date'   => date( 'Y-m-d', strtotime( '-1 day', $current_time ) ),
			),
			'today' => array(
				'title'      => __( 'Today', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-d', $current_time ),
				'end_date'   => date( 'Y-m-d', $current_time ),
			),
			/*
			'last_week' => array(
				'title'      => __( 'Last week', 'e-commerce-jetpack' ),
				'start_date' => date( 'Y-m-d', strtotime( 'last monday', $current_time ) ),
				'end_date'   => date( 'Y-m-d', strtotime( 'last sunday', $current_time ) ),
			),
			*/
		);
	}
}
