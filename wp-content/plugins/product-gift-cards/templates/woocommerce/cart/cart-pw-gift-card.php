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

?>
<style>
    .pwgc-cart-subtitle {
        line-height: 1.4;
        font-size: 80%;
        font-weight: 300;
    }
</style>
<?php

$session_data = (array) WC()->session->get( PWGC_SESSION_KEY );
if ( isset( $session_data['gift_cards'] ) ) {

    foreach ( $session_data['gift_cards'] as $card_number => $discount_amount ) {
        $pw_gift_card = new PW_Gift_Card( $card_number );
        if ( $pw_gift_card->get_id() ) {
            $balance = apply_filters( 'pwgc_to_current_currency', $pw_gift_card->get_balance() ) - $discount_amount;
            ?>
            <tr class="pwgc-total">
                <th>
                    <?php _e( 'Gift card', 'pw-woocommerce-gift-cards' ); ?>
                    <div class="pwgc-cart-subtitle">
                        <?php echo $pw_gift_card->get_number(); ?><br />
                        <?php echo sprintf( __( 'Remaining balance is %s', 'pw-woocommerce-gift-cards' ), wc_price( $balance ) ); ?>
                        <?php
                            if ( $pw_gift_card->has_expired() ) {
                                ?>
                                <br />
                                <span style="color: red; font-weight: 600;">
                                    <?php _e( 'Expired', 'pw-woocommerce-gift-cards' ); ?>
                                </span>
                                <?php
                            }
                        ?>
                    </div>
                </th>
                <td data-title="<?php esc_attr_e( 'Gift Card Total', 'pw-woocommerce-gift-cards' ); ?>">
                    <?php echo wc_price( $discount_amount * -1 ); ?>
                    <a href="#" class="pwgc-remove-card" data-card-number="<?php esc_attr_e( $card_number ); ?>"><?php _e( '[Remove]', 'pw-woocommerce-gift-cards' ); ?></a>
                </td>
            </tr>
            <?php
        }
    }
}
