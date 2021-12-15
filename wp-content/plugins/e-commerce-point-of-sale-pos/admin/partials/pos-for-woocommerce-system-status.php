<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html for system status.
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
// Template for showing information about system status.
global $pfw_mwb_pfw_obj;
$pfw_default_status    = $pfw_mwb_pfw_obj->mwb_pos_plug_system_status();
$pfw_wordpress_details = is_array( $pfw_default_status['wp'] ) && ! empty( $pfw_default_status['wp'] ) ? $pfw_default_status['wp'] : array();
$pfw_php_details       = is_array( $pfw_default_status['php'] ) && ! empty( $pfw_default_status['php'] ) ? $pfw_default_status['php'] : array();
?>
<div class="mwb-pfw-table-wrap">
	<div class="mwb-col-wrap">
		<div id="mwb-pfw-table-inner-container" class="table-responsive mdc-data-table">
			<div class="mdc-data-table__table-container">
				<table class="mwb-pfw-table mdc-data-table__table mwb-table" id="mwb-pfw-wp">
					<thead>
						<tr>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'WP Variables', 'mwb-point-of-sale-woocommerce' ); ?></th>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'WP Values', 'mwb-point-of-sale-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody class="mdc-data-table__content">
						<?php if ( is_array( $pfw_wordpress_details ) && ! empty( $pfw_wordpress_details ) ) { ?>
							<?php foreach ( $pfw_wordpress_details as $wp_key => $wp_value ) { ?>
								<?php if ( isset( $wp_key ) && 'wp_users' !== $wp_key ) { ?>
									<tr class="mdc-data-table__row">
										<td class="mdc-data-table__cell"><?php echo esc_html( $wp_key ); ?></td>
										<td class="mdc-data-table__cell"><?php echo esc_html( $wp_value ); ?></td>
									</tr>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="mwb-col-wrap">
		<div id="mwb-pfw-table-inner-container" class="table-responsive mdc-data-table">
			<div class="mdc-data-table__table-container">
				<table class="mwb-pfw-table mdc-data-table__table mwb-table" id="mwb-pfw-sys">
					<thead>
						<tr>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'System Variables', 'mwb-point-of-sale-woocommerce' ); ?></th>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'System Values', 'mwb-point-of-sale-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody class="mdc-data-table__content">
						<?php if ( is_array( $pfw_php_details ) && ! empty( $pfw_php_details ) ) { ?>
							<?php foreach ( $pfw_php_details as $php_key => $php_value ) { ?>
								<tr class="mdc-data-table__row">
									<td class="mdc-data-table__cell"><?php echo esc_html( $php_key ); ?></td>
									<td class="mdc-data-table__cell"><?php echo esc_html( $php_value ); ?></td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
