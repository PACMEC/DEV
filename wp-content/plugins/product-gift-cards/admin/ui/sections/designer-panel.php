<?php

defined( 'ABSPATH' ) or exit;

global $pw_gift_cards;

?>
<div class="pwgc-designer-panel" style="max-width: 300px;">
    <form id="pwgc-designer-form">
        <input type="hidden" id="pwgc-design-id" name="design_id" value="<?php echo $design_id; ?>">
        <?php
            pwgc_color_picker_field( $design, 'gift_card_color', __( 'Gift card color', 'pw-woocommerce-gift-cards' ) );
            pwgc_color_picker_field( $design, 'redeem_button_background_color', __( 'Redeem button color', 'pw-woocommerce-gift-cards' ) );
            pwgc_color_picker_field( $design, 'redeem_button_color', __( 'Redeem button text color', 'pw-woocommerce-gift-cards' ) );
        ?>
        <p>
            <?php
                printf( __( 'Upgrade to <a href="%s" target="_blank">PW WooCommerce Gift Cards Pro</a> to change even more settings such image, gift card title, additional colors, and more!', 'pw-woocommerce-gift-cards' ), 'https://www.pimwick.com/gift-cards/' );
            ?>
        </p>
        <p>
            <a href="https://www.pimwick.com/gift-cards/" target="_blank"><img src="<?php echo $pw_gift_cards->relative_url( 'assets/images/pro-design-example.png' ); ?>" style="height: 225px;"></a>
        </p>
        <p class="form-field" style="margin-top: 32px;">
            <input type="button" id="pwgc-save-design-button" name="save_design" class="button button-primary" value="<?php _e( 'Save design', 'pw-woocommerce-gift-cards' ); ?>"></input>
            <div id="pwgc-save-design-message"></div>
        </p>
    </form>
</div>
<div id="pwgc-designer-preview" class="pwgc-designer-panel">
    <div>
        <?php
            $custom_template_path = get_stylesheet_directory() . '/' . apply_filters( 'woocommerce_template_directory', 'woocommerce', 'emails/customer-pw-gift-card.php' ) . '/emails/customer-pw-gift-card.php';
            if ( file_exists( $custom_template_path ) ) {
                ?>
                <div style="color: red; font-weight: bold;"><?php _e( 'Custom template file detected!', 'pw-woocommerce-gift-cards' ); ?></div>
                <div style="max-width: 500px; margin-bottom: 16px;">
                    <pre><?php echo $custom_template_path; ?></pre>
                    <?php _e( 'It appears as though you already have a custom email template file, your changes in the Designer may not appear when you actually send the email. Click the Send Preview Email button to confirm.', 'pw-woocommerce-gift-cards' ); ?>
                </div>
                <?php
            }
        ?>
        <span class="pwgc-preview-header"><?php _e( 'Preview', 'pw-woocommerce-gift-cards' ); ?></span>
        <div style="float: right;">
            <span id="pwgc-preview-email-message"></span>
            <button id="pwgc-preview-email-button" class="button" data-email="<?php echo esc_html( get_option( 'admin_email' ) ); ?>"><i class="fas fa-envelope"></i> <?php _e( 'Send a preview email', 'pw-woocommerce-gift-cards' ); ?></button>
        </div>
    </div>
    <div id="pwgc-preview-container" style="color: <?php echo get_option( 'woocommerce_email_text_color', '#3c3c3c' ); ?>; background-color: <?php echo get_option( 'woocommerce_email_body_background_color', '#ffffff' ); ?>">
        <?php
            $item_data = new PW_Gift_Card_Item_Data();
            $item_data->recipient_name = __( 'Recipient Name', 'pw-woocommerce-gift-cards' );
            $item_data->from = __( 'The Purchasing Customer', 'pw-woocommerce-gift-cards' );
            $item_data->message = __( 'Gift card message to the recipient from the sender.', 'pw-woocommerce-gift-cards' );
            $item_data->amount = '123.45';
            $item_data->gift_card_number = PW_Gift_Card::random_card_number();
            $item_data->redeem_url = pwgc_redeem_url( $item_data );
            $item_data->design = $design;
            $item_data->preview = true;
            $item_data->parent_product = $pw_gift_cards->get_gift_card_product();

            require_once( PWGC_PLUGIN_ROOT . 'templates/woocommerce/emails/customer-pw-gift-card.php' );
        ?>
    </div>
</div>
<script>
    jQuery(function() {
        jQuery('#pwgc-save-design-button').click(function(e) {
            pwgcSaveDesign();
            e.preventDefault();
            return false;
        });

        jQuery('#pwgc-preview-email-button').click(function(e) {
            pwgcSendEmailDesignPreview();
            e.preventDefault();
            return false;
        });

        jQuery('#pwgc-designer-advanced-toggle').click(function(e) {
            jQuery('#pwgc-designer-advanced').toggle();
            e.preventDefault();
            return false;
        });
    });
</script>
