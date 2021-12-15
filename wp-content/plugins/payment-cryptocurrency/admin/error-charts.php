<?php
/**
 * CryptoWoo Google chart visualization of exchange rate errors on Database Maintenance page in wp-admin
 *
 * @package    CryptoWoo
 * @subpackage Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}// Exit if accessed directly

// Prepare exchange rate error chart data.
$print_data       = '';
$print_error_data = '';
$exchange_errors  = array();
$add_columns      = array();
$currency_errors  = array();
$data             = get_transient( 'cryptowoo_detailed_rate_errors' );
$max_value        = 10;
if ( $data && cw_get_option( 'rate_error_charts' ) ) {

	foreach ( $data as $line => $line_data ) {

		// Counter start.
		$counting_since = gmdate( 'l jS \of F Y H:i:s', $line_data['counter_start'] );

		$full_hour = (int) round( $line_data['time'] / 60 / 60 ) * 60 * 60; // Count errors per hour.

		if ( ! isset( $currency_errors[ $full_hour ][ $line_data['coin'] ] ) || 0 === $currency_errors[ $full_hour ][ $line_data['coin'] ] ) {
			$currency_errors[ $full_hour ][ $line_data['coin'] ] = 1;
		} else {
			$currency_errors[ $full_hour ][ $line_data['coin'] ]++;
		}
		$max_value = $max_value < $currency_errors[ $full_hour ][ $line_data['coin'] ] ? intval( $currency_errors[ $full_hour ][ $line_data['coin'] ] * 1.5 ) : 10;

		/* phpcs:ignore
		// Prepare columns.
		if(!in_array($line_data['coin'], $add_columns)) {
			$add_columns[$line_data['coin']] = "data.addColumn('number', '{$line_data['coin']}');\n";
		} */

		// Errors per exchange API.
		if ( isset( $exchange_errors[ $line_data['preferred_exchange'] ] ) ) {
			$exchange_errors[ $line_data['preferred_exchange'] ]++;
		} else {
			$exchange_errors[ $line_data['preferred_exchange'] ] = 1;
		}
	}

	foreach ( $currency_errors as $date => $error_data ) {

		$imp_error_data[0] = isset( $error_data['BTC'] ) ? (string) $error_data['BTC'] : '0';
		$imp_error_data[0] = isset( $error_data['BCH'] ) ? (string) $error_data['BCH'] : '0';
		$imp_error_data[1] = isset( $error_data['DOGE'] ) ? (string) $error_data['DOGE'] : '0';
		$imp_error_data[2] = isset( $error_data['LTC'] ) ? (string) $error_data['LTC'] : '0';
		$imp_error_data[3] = isset( $error_data['BLK'] ) ? (string) $error_data['BLK'] : '0';

		$output_errors[ $date ] = implode( ',', $imp_error_data );
		// Date assumes "yyyy-MM-dd" format.
		$date_arr = explode( '-', gmdate( 'Y-m-d', (int) $date ) );
		$date_y   = (int) $date_arr[0];
		$date_m   = (int) $date_arr[1] - 1; // Subtract 1 to make month compatible with javascript months.
		$date_d   = (int) $date_arr[2];

		// Time assumes "hh:mm:ss" format.
		$time_arr = explode( ':', gmdate( 'H:m:i', (int) $date ) );
		$date_h   = (int) $time_arr[0];
		$date_m   = (int) $time_arr[1];
		$date_s   = (int) $time_arr[2];

		$print_error_data .= "\t\t\t[new Date({$date_y}, {$date_m}, {$date_d}, {$date_h}, {$date_m}, {$date_s}), {$output_errors[$date]}],\n";
		// phpcs:ignore // $output_errors_data .= "\t\t\t[new Date({$date}), {$output_errors[$date]}],\n";
	}

	foreach ( $exchange_errors as $exchange => $error_count ) {
		$print_data .= sprintf( "['%s', {$error_count}],\n", CW_ExchangeRates::tools()->get_exchange_nicename( $exchange ), $error_count );
	}
	cw_enqueue_script( 'charts_loader', 'https://www.gstatic.com/charts/loader.js' );
	?>
<script type="text/javascript">

	// Load Charts and the corechart package.
	google.charts.load('current', {'packages':['corechart', 'bar']});

	// Draw the bar chart for rate errors by exchange.
	google.charts.setOnLoadCallback(drawErrorChart);

	// Draw the line chart for the rate errors over time.
	google.charts.setOnLoadCallback(drawHistoryChart);

	// Callback that draws the bar chart.
	function drawErrorChart() {

		var data = google.visualization.arrayToDataTable([
			['Exchange', 'Error Count'],
			<?php echo wp_kses_post( $print_data ); ?>
		]);

		var options = {
			title: 'Errors by exchange',
			chartArea: {width: '70%'},
			legend: 'none',
			hAxis: {
				title: 'Error Count',
				minValue: 0
			},
			vAxis: {
				title: ''
			}
		};

		// Instantiate and draw the chart.
		var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
		chart.draw(data, options);
	}

	// Callback that draws the line chart.
	function drawHistoryChart() {

		var data = new google.visualization.DataTable();
		data.addColumn('datetime', 'Date');
		// Add currency columns
		data.addColumn('number', 'Bitcoin Errors');
		data.addColumn('number', 'Dogecoin Errors');
		data.addColumn('number', 'Litecoin Errors');
		<?php if ( cw_hd_active() ) { ?>
		data.addColumn('number', 'Blackcoin Errors');
		<?php } ?>
		<?php // phpcs:ignore // echo implode("\n", $add_columns); ?>

		data.addRows([
	<?php echo wp_kses_post( $print_error_data ); ?>
		]);

		var options = {
			title: 'Errors by currency',
			chartArea: {width: '70%'},
			pointSize: 2,
			lineWidth: 1,
			hAxis: {
				title: 'Date'
			},
			vAxis: {
				format:'##',
				title: '',
				maxValue: <?php echo esc_html( $max_value ); ?>
			}
		};

		var chart = new google.visualization.ScatterChart(document.getElementById('errors_over_time'));

		chart.draw(data, options);
	}
</script>
<?php } ?>
<div class="wrap postbox cw-postbox">
	<?php if ( ! isset( $counting_since ) ) { ?>
	<h3><?php esc_html_e( 'Exchange rate error charts', 'cryptowoo' ); ?></h3>
	<p><?php esc_html_e( 'No data available. Enable the error visualization on the "Debugging" tab in the settings to start collecting exchange rate error data.', 'cryptowoo' ); ?></p>
	<?php } else { ?>
	<h3>
		<?php
		/* translators: %s: date that counting was started */
		echo esc_html( sprintf( esc_html__( 'Exchange rate errors since %s', 'cryptowoo' ), $counting_since ) );
		?>
	</h3>
	<p>We are keeping only the last 7 days worth of detailed exchange error data.</p>
	<div id="chart_div" style="width: auto; height: auto;"></div>
	<div id="errors_over_time" style="width: auto; height: auto;"></div>
	<?php } ?>
	<pre><?php // phpcs:ignore // var_export($print_error_data); ?></pre>
</div>
