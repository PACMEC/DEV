<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/admin/onboarding
 */

global $pagenow, $pfw_mwb_pfw_obj;
if ( empty( $pagenow ) || 'plugins.php' !== $pagenow ) {
	return false;
}

$pfw_onboarding_form_deactivate = apply_filters( 'mwb_pfw_deactivation_form_fields', array() );
?>
<?php if ( ! empty( $pfw_onboarding_form_deactivate ) ) : ?>
	<div class="mdc-dialog mdc-dialog--scrollable mwb-pfw-on-boarding-dialog">
		<div class="mwb-pfw-on-boarding-wrapper-background mdc-dialog__container">
			<div class="mwb-pfw-on-boarding-wrapper mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title" aria-describedby="my-dialog-content">
				<div class="mdc-dialog__content">
					<div class="mwb-pfw-on-boarding-close-btn">
						<a href="#">
							<span class="pfw-close-form material-icons mwb-pfw-close-icon mdc-dialog__button" data-mdc-dialog-action="close">clear</span>
						</a>
					</div>

					<h3 class="mwb-pfw-on-boarding-heading mdc-dialog__title"></h3>
					<p class="mwb-pfw-on-boarding-desc"><?php esc_html_e( 'May we have a little info about why you are deactivating?', 'mwb-point-of-sale-woocommerce' ); ?></p>
					<form action="#" method="post" class="mwb-pfw-on-boarding-form">
						<?php
						$pfw_onboarding_deactive_html = $pfw_mwb_pfw_obj->mwb_pos_plug_generate_html( $pfw_onboarding_form_deactivate );
						echo esc_html( $pfw_onboarding_deactive_html );
						?>
						<div class="mwb-pfw-on-boarding-form-btn__wrapper mdc-dialog__actions">
							<div class="mwb-pfw-on-boarding-form-submit mwb-pfw-on-boarding-form-verify ">
								<input type="submit" class="mwb-pfw-on-boarding-submit mwb-on-boarding-verify mdc-button mdc-button--raised" value="Send Us">
							</div>
							<div class="mwb-pfw-on-boarding-form-no_thanks">
								<a href="#" class="mwb-pfw-deactivation-no_thanks mdc-button"><?php esc_html_e( 'Skip and Deactivate Now', 'mwb-point-of-sale-woocommerce' ); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="mdc-dialog__scrim"></div>
	</div>
<?php endif; ?>
