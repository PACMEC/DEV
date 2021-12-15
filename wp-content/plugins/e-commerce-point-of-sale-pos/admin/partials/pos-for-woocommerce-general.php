<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $pfw_mwb_pfw_obj;
$pfw_genaral_settings = apply_filters( 'pfw_general_settings_array', array() );
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="mwb-pfw-gen-section-form">
	<div class="pfw-secion-wrap">
		<?php
		$pfw_general_html = $pfw_mwb_pfw_obj->mwb_pos_plug_generate_html( $pfw_genaral_settings );
		echo esc_html( $pfw_general_html );
		wp_nonce_field( 'mwb-pfw-general-nonce', 'mwb-pfw-general-nonce-field' );
		?>
	</div>
</form>

<div class="pfw-secion-wrap" id="mwb-pfw-barcode-settings">
	<h5><?php esc_html_e( 'Barcode Generator Settings', 'mwb-point-of-sale-woocommerce' ); ?></h5>
	<?php
		echo esc_html( $pfw_mwb_pfw_obj->mwb_pos_plug_generate_html( apply_filters( 'pfw_generate_product_barcode', array() ) ) );
	?>
	<div class="mwb-form-group mwb-pos-notify"><div class="mwb-form-group__label"></div><p><?php echo esc_html( 'Barcode generated successfully !', 'mwb-point-of-sale-woocommerce' ); ?></p></div>
</div>
