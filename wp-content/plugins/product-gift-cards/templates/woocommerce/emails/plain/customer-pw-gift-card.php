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

global $pw_gift_cards;

echo $email_heading . "\n\n";

echo sprintf( __( '%s Gift Card', 'pw-woocommerce-gift-cards' ), get_option( 'blogname' ) ) . "\n";
echo sprintf( __( 'Amount: %s', 'pw-woocommerce-gift-cards' ), $pw_gift_cards->pretty_price( $item_data->amount ) ) . "\n";
echo sprintf( __( 'Gift card number: %s', 'pw-woocommerce-gift-cards' ), $item_data->gift_card_number ) . "\n";
echo sprintf( __( 'Link: %s', 'pw-woocommerce-gift-cards' ), $item_data->redeem_url ) . "\n";

if ( !empty( $item_data->message ) ) {
    echo "\n";
    echo __( 'Message:', 'pw-woocommerce-gift-cards' ) . "\n";
    echo $item_data->message . "\n";
    echo "\n";
}

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
