<?php if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly
if ( ! isset( $payment_address ) || ! isset( $crypto_amount ) || $crypto_amount <= 0 ) {
	CW_Order_Processing::instance( $order_id )->add_customer_notice_unexpected_error();
	CW_Order_Processing_Tools::instance()->redirect_to_cart();

}
?>
<div class="ngh-pay cw-row">
	<div class="cw-row">
		<div class="cw-col-12">
		<?php
		// Notices
		if ( isset( $message ) && ! empty( $message ) ) {
			printf( '<span class="ngh-message">%s</span>', wp_kses_post( $message ) );

		}
		?>
		</div>
	</div>
	<div class="cw-row">
		<div class="cw-col-12">
		<?php
		// Custom payment page instructions
		if ( cw_get_option( 'payment_page_text' ) ) {
			echo wp_kses_post( CW_Formatting::format_payment_page_instructions( $payment_address, $crypto_amount, $payment_currency, $wallet_config ) );

		}
		?>
		</div>
	</div>
	<div class="cw-row">
		<?php $colsize = is_numeric( cw_get_option( 'payment_page_width' ) ) ? cw_get_option( 'payment_page_width' ) : 8; ?>
		<div id="cw-details-wrapper" class="cw-col-<?php echo esc_html( $colsize ); ?>">
			<div class="cw-row">
				<!-- Send -->
				<div class="cw-col-2 cw-bold cw_payment_details">
					<?php esc_html_e( 'Send:', 'cryptowoo' ); ?>
				</div>
				<div class="cw-col-5 cw_payment_details">
					<span class="ngh-blocktext copywrap-amount" id="amount" onclick="selectText('amount')" style="display:inline;"><?php echo esc_html( CW_Formatting::fbits( $crypto_amount, true, $wallet_config['decimals'], true, true ) ); ?></span> <?php esc_html_e( $payment_currency ); ?>
				</div>
			</div>
			<div class="cw-row">
				<!-- To -->
				<div class="cw-col-2 cw-bold cw_payment_details">
					<?php esc_html_e( 'To:', 'cryptowoo' ); ?>
				</div>
				<div class="cw-col-10 cw-label cw_payment_details">
					<span class="ngh-blocktext copywrap-address"><span id="payment-address" onclick="selectText('payment-address')"><?php esc_html_e( $payment_address ); ?></span><i id="copy_success" class="fas fa-check" style="color: green; display: none;"></i></span>
					<div class="ngh-smalltext nojs" id="cw-button-row">

						<!-- Pay with Trezor -->
						<?php
						if ( cw_get_option( 'cw_display_pay_with_trezor_button' ) ) {
							CW_TrezorConnect::print_pay_button( $payment_currency );

						}
						?>

						<?php do_action( 'cw_display_wallet_connect_button', $payment_currency ); ?>

						<!-- Copy address -->
						<a class="cw-tooltip" href="#" id="copy_address_a">
							<span class="fa-stack fa-lg"><i class="fa fa-square fa-stack-2x" aria-hidden="true"></i><i class="fas fa-copy fa-stack-1x fa-inverse" aria-hidden="true"></i></span>
							<span class="cw-tt-info"> <?php printf( esc_html__( 'Copy %s address', 'cryptowoo' ), esc_html( $wallet_config['coin_client'] ) ); ?> </span>
						</a>
						<!-- Open wallet client -->
						<a class="cw-tooltip" href="<?php echo esc_url( $qr_data, $coin_protocols, false ); ?>">
							<span class="fa-stack fa-lg"><i class="fa fa-square fa-stack-2x" aria-hidden="true"></i><i class="fas fa-wallet fa-stack-1x fa-inverse" aria-hidden="true"></i></span>
							<span class="cw-tt-info"> <?php printf( esc_html__( 'Open %s wallet', 'cryptowoo' ), esc_html( $wallet_config['coin_client'] ) ); ?> </span>
						</a>

						<!-- Link to block chain -->
						<?php if ( $url ) : ?>
						<a class="cw-tooltip" href="<?php echo esc_url( $url ); ?>" target="_blank">
							<span class="fa-stack fa-lg"><i class="fa fa-square fa-stack-2x" aria-hidden="true"></i><i class="far fa-eye fa-stack-1x fa-inverse" aria-hidden="true"></i></span>
							<span class="cw-tt-info"> <?php printf( esc_html__( 'View address on %s block chain.', 'cryptowoo' ), esc_html( $wallet_config['coin_client'] ) ); ?> </span>
						</a>
							<?php
						endif;
						?>
					</div>
					<div id="addr_img" class="cw-hidden">
						<?php
						// Security image
						if ( cw_get_option( 'sec_image' ) ) {
							if ( $order->get_total() >= (float) cw_get_option( 'sec_image' ) ) {
								echo wp_kses_post( CW_Formatting::generate_sec_image( $payment_address ) );

							}
						}
						?>
					</div>
				</div>
				<?php do_action( "cw_display_extra_details_payment_$payment_currency", $order, $payment_address, $crypto_amount, $payment_currency, $wallet_config ); ?>
			</div>
			<div class="cw-row">
				<div class="cw-col-7 cw-label" id="cw_tx_status">
					<!-- Unconfirmed -->
					<div class="cw-row">
						<div class="cw-col-6 cw-bold">
							<?php esc_html_e( 'Unconfirmed:', 'cryptowoo' ); ?>
						</div>
						<div class="cw-col-6">
							<span class="ngh-blocktext copywrap-amount"><span id="cw-unconfirmed" >0<?php echo esc_attr( $decimal_sep ); ?>00</span></span>
						</div>
					</div>
					<!-- Confirmed -->
					<div class="cw-row">
						<div class="cw-col-6 cw-bold">
							<?php esc_html_e( 'Confirmed:', 'cryptowoo' ); ?>
						</div>
						<div class="cw-col-6">
							<span class="ngh-blocktext copywrap-amount"><span id="cw-confirmed" >0<?php echo esc_textarea( $decimal_sep ); ?>00</span></span>
						</div>
					</div>
					<!-- Countdown -->
					<div class="cw-row" id="countdown-timer">
						<div class="cw-col-6 cw-bold">
							<?php esc_html_e( 'Time Left:', 'cryptowoo' ); ?>
						</div>
						<div class="cw-col-6">
							<span class="ngh-blocktext copywrap-amount"><span class="cw-countdown"></span></span>
						</div>
					</div>
				</div>
				<div class="cw-col-5 cw-label" id="cw_check_tx_status">
					<!-- Check Payment -->
					<a id="check" href="#" class="btn-check-payment"><span class="fa-stack fa-lg"><i class="fa fa-square fa-stack-2x" aria-hidden="true"></i><i id="cw-loading" class="fas fa-sync-alt fa-stack-1x fa-inverse" aria-hidden="true"></i></span></a> <?php esc_html_e( 'Check Status', 'cryptowoo' ); ?>
				</div>
				<div class="cw-col-12">
					<!-- Amount incoming -->
					<div id="amount-incoming" class="woocommerce-message cw-hidden">
						<?php esc_html_e( 'Incoming transaction detected. You will receive an email when your payment is fully confirmed.', 'cryptowoo' ); ?>
					</div>
				</div>
			</div>
		</div>

		<!-- QR Code -->
		<div class="cw-col-4">
			<div id="cw-qr-wrap" style="width:230px;">
				<a href="<?php echo esc_url( $qr_data, $coin_protocols, false ); ?>" title="Scan QR code or click to open wallet client."><div class="ngh-qr" id="qrcode"></div></a>
			</div>
		</div>
	</div>
	<!--  Show Receipt Button -->
	<?php if ( cw_get_option( 'cw_display_pay_later_button' ) ) { ?>
		<div class="cw-row">
			<div class="cw-col-6">
					<form action="<?php echo esc_url( $order->get_checkout_order_received_url() ); ?>" method="POST">
						<input type="hidden" name="order_id" value="<?php echo esc_attr( $order->get_id() ); ?>">
						<input type="submit" class="button medium" value="<?php esc_html_e( 'I have sent the payment - please process my order', 'cryptowoo' ); ?>">
					</form>
			</div>
		</div>
	<?php } ?>
	<!-- Progress Bar -->
	<div class="cw-row nojs"><div class="cw-col-<?php echo $colsize <= 9 ? (int) $colsize + 3 : 12; ?>" id="progress"><div id="timeoutBar"></div></div></div>
	<?php
	if ( cw_get_option( 'display_checkout_branding' ) ) {
		$cryptowoo_logo = CW_Formatting::get_coin_icon( 'cryptowoo' );
		printf(
			'<div class="cw-branding ngh-smalltext">
                    <div class="cw-row">
                        <div class="cw-col-12"><a class="about-cryptowoo" href="https://www.cryptowoo.com" target="_blank" title="Powered by CryptoWoo">%s</a></div>
                    </div>
				</div>',
			wp_kses_post( $cryptowoo_logo )
		);

	}
	?>
</div>
