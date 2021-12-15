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

global $pw_gift_cards_redeeming;
if ( $pw_gift_cards_redeeming->cart_contains_gift_card() && 'yes' !== get_option( 'pwgc_allow_gift_card_purchasing', 'yes' ) ) {
    return;
}

if ( 'proceed_to_checkout' === get_option( 'pwgc_redeem_cart_location', 'proceed_to_checkout' ) ) {
    ?>
    <style>
        #pwgc-redeem-gift-card-container {
            margin-bottom: 1.0em;
        }

        #pwgc-redeem-gift-card-number {
            width: auto;
        }

        #pwgc-redeem-button {
            display: inline-block;
        }

        #pwgc-redeem-error {
            color: red;
        }
    </style>
    <div id="pwgc-redeem-gift-card-form">
        <form id="pwgc-redeem-form">
            <div id="pwgc-redeem-gift-card-container">
                <label for="pwgc-redeem-gift-card-number"><?php _e( 'Have a gift card?', 'pw-woocommerce-gift-cards' ); ?></label><br>
                <div id="pwgc-redeem-error"></div>
                <input type="text" id="pwgc-redeem-gift-card-number" name="card_number" autocomplete="off" placeholder="<?php esc_html_e( 'Gift card number', 'pw-woocommerce-gift-cards' ); ?>">
                <input type="submit" id="pwgc-redeem-button" class="button" name="redeem_gift_card" value="<?php esc_html_e( 'Apply', 'pw-woocommerce-gift-cards' ); ?>" data-wait-text="<?php esc_html_e( 'Please wait...', 'pw-woocommerce-gift-cards' ); ?>">
            </div>
        </form>
    </div>
    <?php
}
