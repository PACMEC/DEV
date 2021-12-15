<?php
/**
 * Exit if accessed directly
 *
 * @since      1.0.0
 * @package    MWB_Point_Of_Sale_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $pfw_mwb_pfw_obj;
$pfw_login_settings = apply_filters( 'pfw_login_settings_array', array() );
?>
<form action="" method="POST" class="mwb-pfw-login-section-form">
	<div class="pfw-secion-wrap">
		<?php
		$pfw_login_settings = $pfw_mwb_pfw_obj->mwb_pos_plug_generate_html( $pfw_login_settings );
		echo esc_html( $pfw_login_settings );
		wp_nonce_field( 'mwb-pfw-login-nonce', 'mwb-pfw-login-nonce-field' );
		?>
	</div>
</form>
