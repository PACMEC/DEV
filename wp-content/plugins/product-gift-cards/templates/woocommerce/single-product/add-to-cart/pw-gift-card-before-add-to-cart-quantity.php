<?php

/*
Copyright (C) 2016-2017 Pimwick, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or exit;

global $product;

$pwgc_to = isset( $_REQUEST[ PWGC_TO_META_KEY ] ) ? stripslashes( htmlentities( $_REQUEST[ PWGC_TO_META_KEY ] ) ) : '';
$pwgc_message = isset( $_REQUEST[ PWGC_MESSAGE_META_KEY ] ) ? stripslashes( htmlentities( str_replace( '<br />', "\n", $_REQUEST[ PWGC_MESSAGE_META_KEY ] ) ) ) : '';

if ( isset( $_REQUEST[ PWGC_FROM_META_KEY ] ) ) {
    $pwgc_from = stripslashes( htmlentities( $_REQUEST[ PWGC_FROM_META_KEY ] ) );
} else {
    $current_user = wp_get_current_user();
    $pwgc_from = $current_user->display_name;
}

$selected = isset( $_REQUEST[ 'attribute_' . PWGC_DENOMINATION_ATTRIBUTE_SLUG ] );

?>
<style>
    .pwgc-field-container {
        margin-bottom: 14px;
    }

    .pwgc-label {
        font-weight: 600;
        display: block;
    }

    .pwgc-subtitle {
        font-size: 11px;
        line-height: 1.465;
        color: #767676;
    }

    .pwgc-input-text {
        width: 95%;
    }

    #pwgc-recipient-count {
        font-weight: 600;
    }

    #pwgc-quantity-one-per-recipient {
        display: none;
    }

    #pwgc-message {
        display: block;
        height: 100px;
        width: 95%;
    }

    .pwgc-hidden {
        display: none;
    }

    <?php
        if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) && ! apply_filters( 'pwgc_show_amount_price', $product->has_amount_on_sale() ) ) {
            // Don't really need to repeat the amount on the Product Page unless there is a sale.
            ?>
            .woocommerce-variation-price {
                display: none !important;
            }
            <?php
        }
    ?>

    .add_to_cart_wrapper {
        flex-wrap: wrap;
    }

    #pwgc-purchase-container {
        width: 100%;
        flex-basis: 100% !important;
        display: block;
    }

    /* Support for the Neve Theme */
    .woocommerce.single .single_variation_wrap > style, .woocommerce.single .woocommerce-variation-add-to-cart > style {
        display: none;
    }

    .single_add_to_cart_button {
        flex: 1;
    }

    .woocommerce-variation-add-to-cart {
        flex-wrap: wrap !important;
    }
</style>
<div id="pwgc-purchase-container" style="<?php echo $selected ? '' : 'display: none;'; ?>">
    <div class="pwgc-field-container">
        <label for="pwgc-to" class="pwgc-label"><?php echo __( PWGC_TO_META_DISPLAY_NAME, 'pw-woocommerce-gift-cards' ); ?></label>
        <input type="text" id="pwgc-to" name="<?php echo PWGC_TO_META_KEY; ?>" class="pwgc-input-text" placeholder="<?php _e( 'Enter an email address for each recipient', 'pw-woocommerce-gift-cards' ); ?>" value="<?php echo $pwgc_to; ?>" required>
        <div class="pwgc-subtitle"><?php _e( 'Separate multiple email addresses with a comma.', 'pw-woocommerce-gift-cards' ); ?></div>
    </div>

    <div class="pwgc-field-container">
        <label for="pwgc-from" class="pwgc-label"><?php echo __( PWGC_FROM_META_DISPLAY_NAME, 'pw-woocommerce-gift-cards' ); ?></label>
        <input type="text" id="pwgc-from" name="<?php echo PWGC_FROM_META_KEY; ?>" class="pwgc-input-text" placeholder="<?php _e( 'Your name', 'pw-woocommerce-gift-cards' ); ?>" value="<?php echo $pwgc_from; ?>" required>
    </div>

    <div class="pwgc-field-container">
        <label for="pwgc-message" class="pwgc-label"><?php echo __( PWGC_MESSAGE_META_DISPLAY_NAME, 'pw-woocommerce-gift-cards' ); ?> <?php _e( '(optional)', 'pw-woocommerce-gift-cards' ); ?></label>
        <textarea id="pwgc-message" name="<?php echo PWGC_MESSAGE_META_KEY; ?>" placeholder="<?php _e( 'Add a message', 'pw-woocommerce-gift-cards' ); ?>"><?php echo $pwgc_message; ?></textarea>
        <div class="pwgc-subtitle"><span id="pwgc-message-characters-remaining"><?php echo PWGC_MAX_MESSAGE_CHARACTERS; ?></span> <?php _e( 'characters remaining', 'pw-woocommerce-gift-cards' ); ?></div>
    </div>

    <div id="pwgc-quantity-one-per-recipient" class="pwgc-field-container">
        <div class="pwgc-label"><?php _e( 'Quantity', 'pw-woocommerce-gift-cards' ); ?>: <span id="pwgc-recipient-count">1</span></div>
        <div class="pwgc-subtitle"><?php _e( '1 to each recipient', 'pw-woocommerce-gift-cards' ); ?></div>
    </div>
</div>
<?php
