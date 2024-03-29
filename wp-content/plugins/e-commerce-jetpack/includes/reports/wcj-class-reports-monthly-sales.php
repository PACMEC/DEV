<?php
/**
 * Booster for WooCommerce - Reports - Monthly Sales (with Currency Conversion)
 *
 * @version 3.6.0
 * @since   2.4.7
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Reports_Monthly_Sales' ) ) :

class WCJ_Reports_Monthly_Sales {

	/**
	 * Constructor.
	 *
	 * @version 2.7.0
	 * @since   2.4.7
	 */
	function __construct( $args = null ) {
		return true;
	}

	/**
	 * get_report.
	 *
	 * @version 2.9.1
	 * @since   2.4.7
	 */
	function get_report() {
		$html = '';
		if ( isset( $_POST['wcj_save_currency_rates'] ) && isset( $_POST['wcj_save_currency_rates_array'] ) && is_array( $_POST['wcj_save_currency_rates_array'] ) ) {
			// Save rates
			update_option( 'wcj_reports_currency_rates', array_replace_recursive( wcj_get_option( 'wcj_reports_currency_rates', array() ), $_POST['wcj_save_currency_rates_array'] ) );
			$html .= '<div class="notice notice-success is-dismissible"><p><strong>' . __( 'Currency rates saved.', 'e-commerce-jetpack' ) . '</strong></p></div>';
		} elseif ( isset( $_POST['wcj_reset_currency_rates'] ) ) {
			// Delete rates
			delete_option( 'wcj_reports_currency_rates' );
			$html .= '<div class="notice notice-success is-dismissible"><p><strong>' . __( 'Currency rates deleted.', 'e-commerce-jetpack' ) . '</strong></p></div>';
		}
		// Show report
		$this->year = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y' );
		$html .= $this->get_monthly_sales_report();
		return $html;
	}

	/*
	 * get_saved_exchange_rate.
	 *
	 * @version 2.9.1
	 * @since   2.4.7
	 */
	function get_saved_exchange_rate( $currency_from, $currency_to, $start_date, $end_date ) {
		// Same currency
		if ( $currency_from == $currency_to ) {
			return 1.0;
		}
		// Saved values
		$saved_rates = wcj_get_option( 'wcj_reports_currency_rates', array() );
		if ( ! empty( $saved_rates ) ) {
			if ( isset( $saved_rates[ $currency_from ][ $currency_to ][ $start_date ][ $end_date ] ) ) {
				return $saved_rates[ $currency_from ][ $currency_to ][ $start_date ][ $end_date ];
			}
		}
		// Fallback rate
		return 1.0;
	}

	/*
	 * get_monthly_sales_report.
	 *
	 * @version 3.6.0
	 * @since   2.4.7
	 * @todo    (maybe) visible rows selection by admin (as option)
	 * @todo    (maybe) take not monthly average, but "Close" of closest day (probably create new "Daily Sales (with Currency Conversion)" report)
	 */
	function get_monthly_sales_report() {

		$execution_time_start = microtime( true );

		$months_array                          = array( '' );
		$months_days_array                     = array( __( 'Days', 'e-commerce-jetpack' ) );
		$total_orders_array                    = array( __( 'Total Orders', 'e-commerce-jetpack' ) );
		$total_orders_average_array            = array( __( 'Orders Average / Day', 'e-commerce-jetpack' ) );
		$total_orders_sum_array                = array( __( 'Total Sum', 'e-commerce-jetpack' ) );
		$total_orders_sum_excl_tax_array       = array( __( 'Total Sum (excl. TAX)', 'e-commerce-jetpack' ) );
		$total_orders_sum_average_order_array  = array( __( 'Average / Order (excl. TAX)', 'e-commerce-jetpack' ) );
		$total_orders_sum_average_array        = array( __( 'Average / Day (excl. TAX)', 'e-commerce-jetpack' ) );
		$currency_rates_array                  = array( __( 'Currency Rates', 'e-commerce-jetpack' ) );
		$orders_by_currency_array              = array( __( 'Orders by Currency', 'e-commerce-jetpack' ) );

		$total_months_days               = 0;
		$total_orders_total              = 0;
		$total_orders_sum_total          = 0;
		$total_orders_sum_excl_tax_total = 0;

		$order_currencies_array          = array();
		$order_currencies_array_totals   = array();
		$report_currency                 = ( isset( $_GET['currency'] ) && 'merge' != $_GET['currency'] ) ? $_GET['currency'] : get_woocommerce_currency();
		$block_size                      = 256;
		$table_data                      = array();
		$do_forecast                     = ( 'yes' === wcj_get_option( 'wcj_reports_orders_monthly_sales_forecast', 'no' ) );
		$do_include_today                = ( 'yes' === wcj_get_option( 'wcj_reports_orders_monthly_sales_include_today', 'no' ) );
		for ( $i = 1; $i <= 12; $i++ ) {
			$current_months_averages = array();
			$total_orders              = 0;
			$total_orders_sum          = 0;
			$total_orders_sum_excl_tax = 0;
			$offset                    = 0;
			$is_current_month          = ( $i == date( 'm' ) && $this->year == date( 'Y' ) );
			$day_for_average           = ( $is_current_month ?
				( $do_include_today ? date( 'd' ) : ( date( 'd' ) - 1 ) ) :                      // today or yesterday
				date( 't', strtotime( $this->year . '-' . sprintf( '%02d', $i ) . '-' . '01' ) ) // last day of the month
			);
			if ( 0 == $day_for_average ) {
				$months_array[]                          = date_i18n( 'F', mktime( 0, 0, 0, $i, 1, $this->year ) );
				$months_days_array[]                     = '-';
				$total_orders_array[]                    = '-';
				$total_orders_average_array[]            = '-';
				$total_orders_sum_array[]                = '-';
				$total_orders_sum_excl_tax_array[]       = '-';
				$total_orders_sum_average_order_array[]  = '-';
				$total_orders_sum_average_array[]        = '-';
				$currency_rates_array[]                  = '';
				$orders_by_currency_array[]              = '';
				continue;
			}
			while( true ) {
				$args_orders = array(
					'post_type'      => 'shop_order',
					'post_status'    => 'wc-completed',
					'posts_per_page' => $block_size,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'offset'         => $offset,
					'fields'         => 'ids',
					'date_query'     => array(
						'after' => array(
							'year'  => $this->year,
							'month' => $i,
							'day'   => 1,
						),
						'before' => array(
							'year'  => $this->year,
							'month' => $i,
							'day'   => $day_for_average,
						),
						'inclusive' => true,
					),
				);
				$loop_orders = new WP_Query( $args_orders );
				if ( ! $loop_orders->have_posts() ) {
					break;
				}
				foreach ( $loop_orders->posts as $order_id ) {
					$order          = wc_get_order( $order_id );
					$order_currency = wcj_get_order_currency( $order );
					// Orders by currency by month
					if ( ! isset( $order_currencies_array[ $i ][ $order_currency ] ) ) {
						$order_currencies_array[ $i ][ $order_currency ] = 0;
					}
					$order_currencies_array[ $i ][ $order_currency ]++;
					// Orders by currency total
					if ( ! isset( $order_currencies_array_totals[ $order_currency ] ) ) {
						$order_currencies_array_totals[ $order_currency ] = 0;
					}
					$order_currencies_array_totals[ $order_currency ]++;
					$total_orders++;
					$order_total = $order->get_total();
					$order_total_excl_tax = $order->get_total() - $order->get_total_tax();
					if ( ! isset( $current_months_averages[ $order_currency ][ $report_currency ] ) ) {
						$start_date = $this->year . '-' . sprintf( '%02d', $i ) . '-' . '01';
						$end_date   = date( 'Y-m-t', strtotime( $start_date ) );
						$the_rate   = $this->get_saved_exchange_rate( $order_currency, $report_currency, $start_date, $end_date );
						if ( 0 == $the_rate ) {
							$the_rate = 1;
						}
						$current_months_averages[ $order_currency ][ $report_currency ] = $the_rate;
					}
					$total_orders_sum          += $order_total * $current_months_averages[ $order_currency ][ $report_currency ];
					$total_orders_sum_excl_tax += $order_total_excl_tax * $current_months_averages[ $order_currency ][ $report_currency ];
				}
				$offset += $block_size;
			}

			// Month Name
			$months_array[]      = date_i18n( 'F', mktime( 0, 0, 0, $i, 1, $this->year ) );
			// Month Days
			$months_days_array[] = ( date( 'm' ) >= $i || $this->year != date( 'Y' ) ? $day_for_average : '-' );
			$total_months_days  += ( date( 'm' ) >= $i || $this->year != date( 'Y' ) ? $day_for_average : 0 );

			// Sales
			$average_sales_result = $total_orders / $day_for_average;
			if ( $is_current_month && $do_forecast ) {
				$forecast_total_orders = ( $average_sales_result ) * ( date( 't', strtotime( $this->year . '-' . sprintf( '%02d', $i ) . '-' . '01' ) ) );
			}
			$total_orders_array[] = ( $total_orders > 0 ? $total_orders . ( $is_current_month && $do_forecast ?
				wc_help_tip( sprintf( __( 'Forecast: %s', 'e-commerce-jetpack' ), round( $forecast_total_orders ) ), true ) : ''
				) : ''
			);
			$total_orders_total  += $total_orders;
			// Sales Average
			$total_orders_average_array[] = ( $average_sales_result > 0 ? number_format( $average_sales_result, 2, '.', ',' ) : '-' );

			// Sum
			$total_orders_sum_array[] = ( $total_orders_sum > 0 ? $report_currency . ' ' . number_format( $total_orders_sum, 2, '.', ',' ) : '-' );
			$total_orders_sum_total  += $total_orders_sum;
			// Sum excl. Tax
			$total_orders_sum_excl_tax_array[] = ( $total_orders_sum_excl_tax > 0 ?
				$report_currency . ' ' . number_format( $total_orders_sum_excl_tax, 2, '.', ',' ) . ( $is_current_month && $do_forecast ?
					wc_help_tip( sprintf( __( 'Forecast: %s', 'e-commerce-jetpack' ), $report_currency . ' ' .
						number_format( $forecast_total_orders * $total_orders_sum_excl_tax / $total_orders, 2 ) ), true ) : ''
				) : ''
			);
			$total_orders_sum_excl_tax_total  += $total_orders_sum_excl_tax;

			// Order Average
			$total_orders_sum_average_order_array[] = ( $total_orders_sum_excl_tax > 0 && $total_orders > 0 ?
				$report_currency . ' ' . number_format( $total_orders_sum_excl_tax / $total_orders, 2, '.', ',' ) : '-' );
			// Sum Average
			$average_result = $total_orders_sum_excl_tax / $day_for_average;
			$total_orders_sum_average_array[] = ( $average_result > 0 ? $report_currency . ' ' . number_format( $average_result, 2, '.', ',' ) : '-' );

			// Currency Rates
			ksort( $current_months_averages );
			$currency_rates_html = '<pre style="font-size:x-small;">';
			foreach ( $current_months_averages as $currency_from => $currencies_to ) {
				foreach ( $currencies_to as $currency_to => $rate ) {
					if ( $currency_from != $currency_to ) {
						$input_id = sanitize_title( $currency_from . '_' . $currency_to . '_' . $start_date . '_' . $end_date );
						$currency_rates_html .= '<a class="wcj_grab_average_currency_exchange_rate" href="#" title="' . __( 'Grab average rate', 'e-commerce-jetpack' ) .
							'" currency_from="' . $currency_from .
							'" currency_to="' . $currency_to .
							'" start_date="' . $start_date .
							'" end_date="' . $end_date .
							'" input_id="' . $input_id .
						'">' . $currency_from . $currency_to .
						'</a> ' .
							'<input id="' . $input_id . '" style="width:50px;font-size:x-small;" type="number" ' .
								'name="wcj_save_currency_rates_array[' . $currency_from . '][' . $currency_to . '][' . $start_date . '][' . $end_date . ']" ' .
								'value="' . $rate . '" step="0.000001">' .
							'<br>';
					}
				}
			}
			$currency_rates_html .= '</pre>';
			$currency_rates_array[] = $currency_rates_html;

			// Orders by Currency by Month
			if ( isset( $order_currencies_array[ $i ] ) ) {
				ksort( $order_currencies_array[ $i ] );
				$orders_by_currency_html = '<pre style="font-size:x-small;">';
				foreach ( $order_currencies_array[ $i ] as $_order_currency => $total_orders_by_currency ) {
					$orders_by_currency_html .= $_order_currency . ' ' . $total_orders_by_currency . '<br>';
				}
				$orders_by_currency_html .= '</pre>';
				$orders_by_currency_array[] = $orders_by_currency_html;
			} else {
				$orders_by_currency_array[] = '';
			}
		}

		// Orders by Currency Total
		$order_currencies_totals_html = '';
		if ( ! empty ( $order_currencies_array_totals ) ) {
			ksort( $order_currencies_array_totals );
			$order_currencies_totals_html = '<pre style="font-size:x-small;">';
			foreach ( $order_currencies_array_totals as $_order_currency => $total_orders_by_currency ) {
				$order_currencies_totals_html .= $_order_currency . ' ' . $total_orders_by_currency . '<br>';
			}
			$order_currencies_totals_html .= '</pre>';
		}

		// Totals
		if ( $do_forecast ) {
			$part_of_the_year_for_forecast            = ( $total_months_days > 0 ? ( date( 'z', strtotime( date( 'Y-12-31' ) ) ) + 1 ) / $total_months_days : 0 );
			$forecast_total_orders_year               = ( $part_of_the_year_for_forecast > 0 && date( 'Y' ) == $this->year ?
				wc_help_tip( sprintf( __( 'Forecast: %s', 'e-commerce-jetpack' ), round( $part_of_the_year_for_forecast * $total_orders_total ) ), true ) : '' );
			$forecast_total_orders_sum_excl_tax_total = ( $part_of_the_year_for_forecast > 0 && date( 'Y' ) == $this->year ?
				wc_help_tip( sprintf( __( 'Forecast: %s', 'e-commerce-jetpack' ),
					$report_currency . ' ' . number_format( $part_of_the_year_for_forecast * $total_orders_sum_excl_tax_total, 2, '.', ',' ) ), true ) : '' );
		}
		$months_array[]                          = __( 'Totals', 'e-commerce-jetpack' );
		$months_days_array[]                     = $total_months_days;
		$total_orders_array[]                    = $total_orders_total .
			( $do_forecast ? $forecast_total_orders_year : '' );
		$total_orders_average_array[]            = ( $total_months_days > 0 ? number_format( ( $total_orders_total / $total_months_days ), 2, '.', ',' ) : '-' );
		$total_orders_sum_array[]                = $report_currency . ' ' . number_format( $total_orders_sum_total, 2, '.', ',' );
		$total_orders_sum_excl_tax_array[]       = $report_currency . ' ' . number_format( $total_orders_sum_excl_tax_total, 2, '.', ',' ) .
			( $do_forecast ? $forecast_total_orders_sum_excl_tax_total : '' );
		$total_orders_sum_average_order_array[]  = ( $total_orders_total > 0 ?
			$report_currency . ' ' . number_format( ( $total_orders_sum_excl_tax_total / $total_orders_total ), 2, '.', ',' ) : '-' );
		$total_orders_sum_average_array[]        = ( $total_months_days  > 0 ?
			$report_currency . ' ' . number_format( ( $total_orders_sum_excl_tax_total / $total_months_days ),  2, '.', ',' ) : '-' );
		$currency_rates_array[]                  = '';
		$orders_by_currency_array[]              = $order_currencies_totals_html;

		// Table
		$table_data[] = $months_array;
		$table_data[] = $months_days_array;
		$table_data[] = $total_orders_array;
		$table_data[] = $total_orders_average_array;
		$table_data[] = $total_orders_sum_array;
		$table_data[] = $total_orders_sum_excl_tax_array;
		$table_data[] = $total_orders_sum_average_order_array;
		$table_data[] = $total_orders_sum_average_array;
		$table_data[] = $currency_rates_array;
		$table_data[] = $orders_by_currency_array;

		$execution_time_end = microtime( true );

		// HTML
		$html = '';
		$menu = '';
		$menu .= '<p>';
		$menu .= '<ul class="subsubsub">';
		$menu .= '<li><a href="' . add_query_arg( 'year', date( 'Y' ) )         . '" class="' .
			( ( $this->year == date( 'Y' ) ) ? 'current' : '' ) . '">' . date( 'Y' ) . '</a> | </li>';
		$menu .= '<li><a href="' . add_query_arg( 'year', ( date( 'Y' ) - 1 ) ) . '" class="' .
			( ( $this->year == ( date( 'Y' ) - 1 ) ) ? 'current' : '' ) . '">' . ( date( 'Y' ) - 1 ) . '</a> | </li>';
		$menu .= '<li><a href="' . add_query_arg( 'year', ( date( 'Y' ) - 2 ) ) . '" class="' .
			( ( $this->year == ( date( 'Y' ) - 2 ) ) ? 'current' : '' ) . '">' . ( date( 'Y' ) - 2 ) . '</a> | </li>';
		$menu .= '</ul>';
		$menu .= '</p>';
		$menu .= '<br class="clear">';
		$html .= $menu;
		$html .= '<h4>' . __( 'Report currency', 'e-commerce-jetpack' ) . ': ' . $report_currency . '</h4>';
		$months_styles = array();
		for ( $i = 1; $i <= 12; $i++ ) {
			$months_styles[] = ( $i == date( 'm' ) && $this->year == date( 'Y' ) ? 'width:8%;' : 'width:6%;' );
		}
		$html .= '<form method="post" action="">';
		$html .= wcj_get_table_html( $table_data, array(
				'table_class'        => 'widefat striped',
				'table_heading_type' => 'horizontal',
				'columns_styles'     => array_merge(
					array( 'width:16%;' ),
					$months_styles,
					array( ( $this->year == date( 'Y' ) ? 'width:10%;' : 'width:12%;' ) . 'font-weight:bold;' )
				),
		) );
		$html .= '<p style="font-size:x-small;"><em>' . sprintf( __( 'Report generated in: %s s', 'e-commerce-jetpack' ),
			number_format( ( $execution_time_end - $execution_time_start ), 2, '.', ',' ) ) . '</em></p>';
		$html .= '<p><input name="wcj_save_currency_rates" type="submit" class="button button-primary" value="' .
			__( 'Save Currency Rates', 'e-commerce-jetpack' ) . '"></p>';
		$html .= '</form>';
		$html .= '<form method="post" action="">' .
			'<input name="wcj_reset_currency_rates" type="submit" class="button button-primary" value="' .
				__( 'Reset Currency Rates', 'e-commerce-jetpack' ) . '" onclick="return confirm(\'' . __( 'Are you sure?', 'e-commerce-jetpack' ) . '\')">' .
		'</form>';
		return $html;
	}
}

endif;
