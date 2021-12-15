<?php

defined( 'ABSPATH' ) or exit;

$gift_card_product = $pw_gift_cards->get_gift_card_product();

?>
<div id="pwgc-setup-container">
    <div id="pwgc-setup-error"></div>
    <div class="pwgc-setup-section">
        <div class="pwgc-setup-header">
            <?php printf( __( 'Step 1: Create a "%s" product to start selling Gift Cards.', 'pw-woocommerce-gift-cards' ), PWGC_PRODUCT_TYPE_NAME ); ?>
        </div>
        <?php
            if ( empty( $gift_card_product ) ) {
                $create_product_button_text = __( 'Create the Gift Card product', 'pw-woocommerce-gift-cards' );

                ?>
                <a href="#" id="pwgc-setup-create-product" class="button button-secondary" data-text="<?php echo esc_attr( $create_product_button_text ); ?>"><?php echo esc_attr( $create_product_button_text ); ?></a>
                <?php
            }
        ?>
        <div id="pwgc-setup-create-product-success" style="<?php echo empty( $gift_card_product ) ? '' : 'display: block;'; ?>">
            <div class="pwgc-setup-header" style="color: green;"><i class="far fa-check-circle"></i> <?php _e( 'Success!', 'pw-woocommerce-gift-cards' ); ?></div>
            <?php _e( 'The Gift Card product has been created. Click on the Products menu in the left to edit it.', 'pw-woocommerce-gift-cards' ); ?>
        </div>
    </div>
    <div class="pwgc-setup-section">
        <div class="pwgc-setup-header">
            <?php _e( 'Step 2: Let your customers check their gift card balances.', 'pw-woocommerce-gift-cards' ); ?>
        </div>
        <?php printf( __( 'Upgrade to <a href="%s" target="_blank">PW WooCommerce Gift Cards Pro</a> to create a Check Balance page for your customers.', 'pw-woocommerce-gift-cards' ), 'https://www.pimwick.com/gift-cards/' ); ?></a>
    </div>
</div>
<?php
