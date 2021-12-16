<?php
/**
 * CryptoPay debug info on Database Maintenance page in wp-admin
 *
 * @package    CryptoPay
 * @subpackage Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

if ( ! isset( $options ) ) {
	$options = cw_get_options();
}

if ( CW_AdminMain::debug_is_enabled() ) { ?>
	<div class="wrap postbox cw-postbox">
		<h3><?php esc_html_e( 'Debug Information', 'cryptopay' ); ?></h3>
		<p><?php esc_html_e( 'Please copy/paste the information below into your ticket when contacting support.', 'cryptopay' ); ?> </p>
<table>
	<?php
	echo wp_kses_post( sprintf( '<tr><td>PHP version</td><td>%s</td></tr>', phpversion() ) );
	echo wp_kses_post( sprintf( '<tr><td>WooCommerce Version</td><td>%s</td></tr>', WC_VERSION ) );
	echo wp_kses_post( sprintf( '<tr><td>CryptoPay Version</td><td>%s</td></tr>', CWOO_VERSION ) );

	$unpaid_addresses = CW_Database_CryptoWoo::get_unpaid_orders_payment_details();
	printf( '<tr><td>Unpaid addresses</td><td>%s</td></tr>', count( $unpaid_addresses ) );
	// Uncomment to see the details for all unpaid addresses
	// print_r($unpaid_addresses);.

	echo '<tr><td><b>PHP Extensions</b></td></tr>';
		$required_extensions = array( 'curl', 'gmp', 'bcmath' );
		$loaded_extensions   = get_loaded_extensions();
	foreach ( $required_extensions as $required_extension ) {
		if ( in_array( $required_extension, $loaded_extensions, true ) ) {
			echo wp_kses_post( sprintf( '<tr><td>%s</td><td><span style="font-weight: bold; color: green;"><i class="fa fa-check"></i></span> enabled</td></tr>', $required_extension ) );
		} else {
			echo wp_kses_post( sprintf( '<tr><td>%s</td><td><span style="font-weight: bold; color: red;"><i class="fa fa-warning"></i></span> not found | <a href="https://www.cryptopay.com/enable-required-php-extensions/?ref=cw_status" target="_blank">More Info</a></td></tr>', $required_extension ) );
		}
	}
	// exchange rate update error info.
	$error_transient = get_transient( 'cryptowoo_rate_errors' );
	/* translators: %s: html <i> element inside a <span> element */
	$rate_error_info = $error_transient ? str_replace( 'Array', '<span style="font-weight: bold; background-color: yellow;"><i class="fa fa-warning"></i></span> Errors:', print_r( $error_transient, true ) ) : sprintf( esc_html__( '%s None', 'cryptopay' ), '<span style="font-weight: bold; color: green;"><i class="fa fa-check"></i></span>' ); // phpcs:disable WordPress.PHP.DevelopmentFunctions
	/* translators: %1$s: <tr><td><b>, %2$s: </b></td><td><pre>, %3$s: rate error info , %4$s: </pre></td></tr> */
	echo wp_kses_post( sprintf( esc_html__( '%1$sExchange rate errors%2$s%3$s%4$s', 'cryptopay' ), '<tr><td><b>', '</b></td><td><pre>', $rate_error_info, '</pre></td></tr>' ) );

	CW_ExchangeRates::processing()->get_exchange_rates();

	echo '<tr><td><b>Plugin Settings</b></td></tr>';

	foreach ( $options as $key => $value ) {

		// Don't display API keys and MPKs.
		$secrets = array(
			'cryptowoo_btc_api',
			'cryptowoo_btctest_api',
			'cryptowoo_ltc_api',
			'cryptowoo_doge_api',
			'cryptowoo_dogetest_api',
			'cryptowoo_btc_mpk',
			'cryptowoo_btctest_mpk',
			'cryptowoo_ltc_mpk',
			'cryptowoo_ltc_mpk_xpub',
			'cryptowoo_blk_mpk',
			'cryptowoo_blk_mpk_xpub',
			'cryptowoo_doge_mpk',
			'cryptowoo_doge_mpk_xpub',
			'cryptowoo_dogetest_mpk',
			'cryptoid_api_key',
			'blockcypher_token',
		);

		$option_value        = '' !== $value ? $value : 'not set';
		$debug_array[ $key ] = 'not set' === $option_value || ! in_array( $key, $secrets, true ) ? $option_value : '********';

	}
	// print_r($debug_array);
	// Maybe include HD Wallet Add-on Settings.
	if ( file_exists( WP_PLUGIN_DIR . '/cryptopay-hd-wallet-addon/cryptopay-hd-wallet-addon.php' ) ) {

		// Add HD wallet info.
		$index_keys = array(
			'BTC'      => 'cryptowoo_btc_index',
			'BTCTEST'  => 'cryptowoo_btctest_index',
			'DOGE'     => 'cryptowoo_doge_index',
			'DOGETEST' => 'cryptowoo_dogetest_index',
			'LTC'      => 'cryptowoo_ltc_index',
			'BLK'      => 'cryptowoo_blk_index',
		);

		$hd_index_title                    = '<b>HD Wallet Index</b>';
		$hd_wallet_info[ $hd_index_title ] = '';

		foreach ( $index_keys as $coin => $index_key ) {
			$current_index           = get_option( $index_key );
			$hd_wallet_info[ $coin ] = false !== $current_index && '' !== $current_index ? $current_index : 'not set';
		}
	} else {
		$hd_index_title                    = '<b>HD Wallet Index</b>';
		$hd_wallet_info[ $hd_index_title ] = 'CryptoPay HD Wallet Add-on not found. <a href="http://www.cryptopay.com/shop/cryptopay-hd-wallet-addon/?ref=cw_status" target="_blank">Get it here!</a>';
	}
	$debug_array = array_merge( $debug_array, $hd_wallet_info );
	foreach ( $debug_array as $option_name => $option_value ) {
		if ( is_array( $option_value ) ) {
			$option_value = sprintf( '<pre>%s</pre>', print_r( $option_value, true ) );
		}
		echo wp_kses_post( sprintf( '<tr><td>%s</td><td>%s</td></tr>', $option_name, $option_value ) );
	}
	?>
	</table></div>
	<?php
}
