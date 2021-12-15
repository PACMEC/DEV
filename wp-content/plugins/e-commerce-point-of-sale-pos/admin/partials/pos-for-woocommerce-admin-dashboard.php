<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}

global $pfw_mwb_pfw_obj;
global $mwb_pfw_notices;
$pfw_active_tab   = isset( $_GET['pfw_tab'] ) ? sanitize_key( $_GET['pfw_tab'] ) : 'pos-for-woocommerce-general'; //phpcs:disable
$pfw_default_tabs = $pfw_mwb_pfw_obj->mwb_pos_plug_default_tabs();
?>
<header>
	<div class="mwb-header-container mwb-bg-white mwb-r-8">
		<h1 class="mwb-header-title"><?php esc_html_e( 'MWB Point of Sale (POS) for WooCommerce', 'mwb-point-of-sale-woocommerce' ); ?></h1>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=pos_for_woocommerce_menu' ) . '&pfw_tab=' . esc_attr( 'pos-for-woocommerce-support' ) ); ?>" class="mwb-link"><?php esc_html_e( 'Support', 'mwb-point-of-sale-woocommerce' ); ?></a>
	</div>
</header>

<main class="mwb-main mwb-bg-white mwb-r-8">
	<?php
	if ( $mwb_pfw_notices ) {
		$mwb_pfw_error_text = esc_html__( 'Settings saved !', 'mwb-point-of-sale-woocommerce' );
		$pfw_mwb_pfw_obj->mwb_pos_plug_admin_notice( $mwb_pfw_error_text, 'success' );
	}
	?>
		
	<nav class="mwb-navbar">
		<ul class="mwb-navbar__items">
			<?php
			if ( is_array( $pfw_default_tabs ) && ! empty( $pfw_default_tabs ) ) {

				foreach ( $pfw_default_tabs as $pfw_tab_key => $pfw_default_tabs ) {

					$pfw_tab_classes = 'mwb-link ';

					if ( ! empty( $pfw_active_tab ) && $pfw_active_tab === $pfw_tab_key ) {
						$pfw_tab_classes .= 'active';
					}
					?>
					<li>
						<a id="<?php echo esc_attr( $pfw_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=pos_for_woocommerce_menu' ) . '&pfw_tab=' . esc_attr( $pfw_tab_key ) ); ?>" class="<?php echo esc_attr( $pfw_tab_classes ); ?>"><?php echo esc_html( $pfw_default_tabs['title'] ); ?></a>
					</li>
					<?php
				}
			}
			?>
		</ul>
	</nav>

	<section class="mwb-section">
		<div>
			<?php
			do_action( 'mwb_pfw_before_general_settings_form' );
					// if submenu is directly clicked on woocommerce.
			if ( empty( $pfw_active_tab ) ) {
				$pfw_active_tab = 'mwb_pfw_plug_general';
			}

					// look for the path based on the tab id in the admin templates.
			$pfw_tab_content_path = 'admin/partials/' . $pfw_active_tab . '.php';

			$pfw_mwb_pfw_obj->mwb_pos_plug_load_template( $pfw_tab_content_path );

			do_action( 'mwb_pfw_after_general_settings_form' );
			?>
		</div>
	</section>
