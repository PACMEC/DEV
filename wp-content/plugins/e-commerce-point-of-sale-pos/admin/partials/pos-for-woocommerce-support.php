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
$pfw_support_settings = apply_filters( 'pfw_supprot_tab_settings_array', array() );
?>
<!--  template file for admin settings. -->
<div class="pfw-section-wrap">
	<?php if ( is_array( $pfw_support_settings ) && ! empty( $pfw_support_settings ) ) { ?>
		<?php foreach ( $pfw_support_settings as $pfw_support_setting ) { ?>
		<div class="mwb-col-wrap">
			<div class="mwb-shadow-panel">
				<div class="content-wrap">
					<div class="content">
						<h3><?php echo esc_html( $pfw_support_setting['title'] ); ?></h3>
						<p><?php echo esc_html( $pfw_support_setting['description'] ); ?></p>
					</div>
					<div class=""><span class="mdc-button__ripple"></span>
						<a href="<?php echo esc_url( $pfw_support_setting['link'] ); ?>" class="mwb-btn mwb-btn-primary"><?php echo esc_html( $pfw_support_setting['link-text'] ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	<?php } ?>
</div>
