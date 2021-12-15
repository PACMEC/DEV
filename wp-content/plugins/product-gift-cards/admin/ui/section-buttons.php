<?php

defined( 'ABSPATH' ) or exit;

?>
<div>
    <div style="display: inline-block;">
        <div id="pwgc-dashboard" class="pwgc-container">
            <div class="pwgc-dashboard-item <?php pwgc_dashboard_helper( 'balances' ); ?>" data-section="balances">
                <i class="fas fa-credit-card fa-3x"></i>
                <div class="pwgc-dashboard-item-title"><?php _e( 'Balances', 'pw-woocommerce-gift-cards' ); ?></div>
            </div>
            <div class="pwgc-dashboard-item <?php pwgc_dashboard_helper( 'designer' ); ?>" data-section="designer">
                <i class="fas fa-paint-brush fa-3x"></i>
                <div class="pwgc-dashboard-item-title"><?php _e( 'Email Designer', 'pw-woocommerce-gift-cards' ); ?></div>
            </div>
            <div class="pwgc-dashboard-item <?php pwgc_dashboard_helper( 'create' ); ?>" data-section="create">
                <i class="fas fa-plus-square fa-3x"></i>
                <div class="pwgc-dashboard-item-title"><?php _e( 'Create Gift Cards', 'pw-woocommerce-gift-cards' ); ?></div>
            </div>
            <div class="pwgc-dashboard-item <?php pwgc_dashboard_helper( 'import' ); ?>" data-section="import">
                <i class="fas fa-cloud-upload-alt fa-3x"></i>
                <div class="pwgc-dashboard-item-title"><?php _e( 'Import Card Numbers', 'pw-woocommerce-gift-cards' ); ?></div>
            </div>
            <div class="pwgc-dashboard-item <?php pwgc_dashboard_helper( 'settings' ); ?>" data-section="settings">
                <i class="fas fa-cog fa-3x"></i>
                <div class="pwgc-dashboard-item-title"><?php _e( 'Settings', 'pw-woocommerce-gift-cards' ); ?></div>
            </div>
        </div>
    </div>
</div>
