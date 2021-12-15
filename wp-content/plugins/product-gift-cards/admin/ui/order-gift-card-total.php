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

global $pw_gift_cards;

foreach( $order->get_items( 'pw_gift_card' ) as $line ) {
    $gift_card = new PW_Gift_Card( $line->get_card_number() );
    if ( $gift_card->get_id() ) {
        $check_balance_url = $gift_card->check_balance_url();
    } else {
        $check_balance_url = admin_url();
    }

    ?>
    <tr>
        <td class="label">
            <?php _e( 'PW Gift Card', 'pw-woocommerce-gift-cards' ); ?> <a href="<?php echo $check_balance_url; ?>"><?php echo $line->get_card_number(); ?></a>:
        </td>
        <td width="1%"></td>
        <td class="total">
            <?php
                $amount = apply_filters( 'pwgc_to_order_currency', $line->get_amount() * -1, $order );

                // Aelia Currency Switcher
                if ( class_exists( 'WC_Aelia_CurrencySwitcher' ) && isset( $GLOBALS['woocommerce-aelia-currencyswitcher'] ) ) {
                    ?>
                    <span class="woocommerce-Price-amount amount"><?php echo $amount; ?></span>
                    <?php
                } else {
                    $args = array();

                    // Multi-Currency for WooCommerce by TIV.NET INC
                    if ( is_a( $order, 'WC_Order' ) && class_exists( 'WOOMC\App' ) ) {
                        $args['currency'] = get_post_meta( $order->get_id(), '_order_currency', true );
                    }

                    // Currency Switcher for WooCommerce by WP Wham
                    if ( is_a( $order, 'WC_Order' ) && function_exists( 'alg_get_current_currency_code' ) ) {
                        $args['currency'] = get_post_meta( $order->get_id(), '_order_currency', true );
                    }

                    echo wc_price( $amount, $args );
                }
            ?>
        </td>
    </tr>
    <?php
}
