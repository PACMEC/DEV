<?php
/**
 * CryptoPay Database Maintenance page in wp-admin
 *
 * @package    CryptoPay
 * @subpackage Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

$update_success = null;
// if form has been posted process data.
if ( isset( $_GET['update_exchange_data'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_key( $_GET['nonce'] ), 'update_exchange_data' ) ) {
	$admin_main     = new CW_AdminMain();
	$update_success = $admin_main->update_exchange_data();
	if ( $update_success ) {

		$update_message = print_r( $update_success, true ); // phpcs:disable WordPress.PHP.DevelopmentFunctions
		$notice_type    = 'notice notice-info';
	} else {
		$update_message = __( 'There was a problem inserting the live data. Please try again.', 'cryptopay' );
		$notice_type    = 'error';
	}
}
?>
<div class="wrap">
<h2><?php echo esc_html( __( 'CryptoPay Database Maintenance', 'cryptopay' ) ); ?></h2>
<div class="wrap postbox cw-postbox">
	<div class="wrap postbox cw-postbox"><h3><?php echo esc_html( __( 'Update exchange rates in database', 'cryptopay' ) ); ?></h3>
	<form id="update_exchange_data" action="" method="GET">
		<input type="hidden" name="update_exchange_data" id="update_exchange_data" value="true" />
		<input type="hidden" name="page" id="page" value="cryptowoo_database_maintenance" />
		<input id="update_exchange_data" type="submit" name="submit" class="button" value="<?php echo esc_html( __( 'Update Exchange Rate Data', 'cryptopay' ) ); ?>" onClick="" />
		<?php echo wp_kses_post( wp_nonce_field( 'update_exchange_data', 'nonce' ) ); ?>
	</form>
	<p><div class="button" id="reset-error-counter" href="#"><?php echo esc_html( __( 'Reset Error Counter', 'cryptopay' ) ); ?></div><div id="reset-error-counter-response"></div></p>
	<?php if ( null !== $update_success ) : ?>
		<div id='message' class='<?php echo esc_attr( $notice_type ); ?> fade'><p class="flash_message"><pre><?php echo esc_html( $update_message ); ?></pre></p></div><br>
		<?php
	endif;

	/*
	// 19.10.2015 Manual rate update via AJAX collides with WooCommerce Currency Switcher Plugin - TODO Revisit later

		<a class="button" id="update-exchange-rates" href="#"><?php echo __('Update Exchange Rates', 'cryptopay'); ?></a>
		<div id="cw-rates-loading"></div>
		<div id="cw-rates-response"></div>
	*/
	// Display current exchange rates in database.
	echo wp_kses_post( CW_ExchangeRates::processing()->get_exchange_rates() );
	?>
		<p>Current time: <?php echo esc_html( gmdate( 'Y-m-d H:i:s' ) ); ?></p>
	</div>
	<?php
	// Ajax url.
	$admin_url = admin_url( 'admin-ajax.php' );

	// Register script.
	cw_register_script( 'cw_admin', CWOO_PLUGIN_PATH . '/assets/js/admin-js.js' );

	// Localize the script with new data.
	$php_vars_array = array( 'admin_url' => $admin_url );
	wp_localize_script( 'cw_admin', 'CryptoWooAdmin', $php_vars_array );

	// Enqueued script with localized data.
	cw_enqueue_script( 'cw_admin' );

	// Maybe include error visualization via Google charts.
	$charts_file = sprintf( '%sadmin/error-charts.php', CWOO_PLUGIN_DIR );
	if ( file_exists( $charts_file ) ) {
		include_once $charts_file;
	}
	?>

	<div class="wrap postbox cw-postbox">
		<h3><?php echo esc_html( __( 'Process open orders', 'cryptopay' ) ); ?></h3>
		<p><a class="button" id="update-tx-details" href="#"><?php echo esc_html( __( 'Update incoming payments for open orders', 'cryptopay' ) ); ?></a> <?php echo esc_html( __( 'This will make a request to the selected processing API, so better wait a while before clicking again!', 'cryptopay' ) ); ?></p>
		<p><a class="button" id="process-open-orders" href="#"><?php echo esc_html( __( 'Process order data', 'cryptopay' ) ); ?></a> <?php echo esc_html( __( 'Check if any open orders have been paid or timed out.', 'cryptopay' ) ); ?></p>
		<p><div id="cw-processing-response"></div>
	</div>
	<div class="wrap postbox cw-postbox">
	<h3><?php echo esc_html( __( 'Table reset', 'cryptopay' ) ); ?></h3>
		<div class="button" id="reset-exchange-rate-table" href="#"><?php echo esc_html( __( 'Reset Exchange Rate Table', 'cryptopay' ) ); ?></div>
		<div class="button" id="reset-payments-table" href="#"><?php echo esc_html( __( 'Reset Payments Table', 'cryptopay' ) ); ?></div> <span style="color:#B94A48; font-size:85%;"><?php echo esc_html( __( 'This will remove all currently open orders from the queue.', 'cryptopay' ) ); ?></span>
		<div id="reset-table-response"></div>
	</div>
	<div class="wrap postbox cw-postbox">
		<h3>Cronjob Setup Info</h3>
		<?php
			// Display cronjob setup info according to the selected cron interval.
			echo wp_kses_post( CW_AdminMain::get_cronjob_info( in_array( cw_get_option( 'soft_cron_interval' ), array( 'seconds_60', 'seconds_120', 'seconds_300' ), true ) ) );
		?>
	</div>
	<?php
	// Include debug information.
	require CWOO_PLUGIN_DIR . 'admin/debug.php';
	?>
</div>
