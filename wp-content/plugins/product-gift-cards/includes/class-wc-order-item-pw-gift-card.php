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

function pwgc_order_item_woocommerce_get_items_key( $key, $item ) {
    if ( is_a( $item, 'WC_Order_Item_PW_Gift_Card' ) ) {
        return 'pw_gift_card_lines';
    }

    return $key;
}
add_filter( 'woocommerce_get_items_key', 'pwgc_order_item_woocommerce_get_items_key', 10, 2 );


function pwgc_woocommerce_order_type_to_group( $groups ) {
    $groups['pw_gift_card'] = 'pw_gift_card_lines';
    return $groups;
}
add_filter( 'woocommerce_order_type_to_group', 'pwgc_woocommerce_order_type_to_group', 10, 2 );


function pwgc_woocommerce_get_order_item_classname( $classname, $item_type, $id ) {
    if ( $item_type == 'pw_gift_card' ) {
        return 'WC_Order_Item_PW_Gift_Card';
    }
    return $classname;
}
add_filter( 'woocommerce_get_order_item_classname', 'pwgc_woocommerce_get_order_item_classname', 10, 3 );


class WC_Order_Item_PW_Gift_Card extends WC_Order_Item {

    protected $extra_data = array(
        'card_number'   => '',
        'amount'        => 0,
    );

    public function set_name( $value ) {
        return $this->set_card_number( $value );
    }

    public function set_card_number( $value ) {
        $this->set_prop( 'card_number', wc_clean( $value ) );
    }

    public function set_amount( $value ) {
        $this->set_prop( 'amount', wc_format_decimal( $value ) );
    }

    public function get_name( $context = 'view' ) {
        return $this->get_card_number( $context );
    }

    public function get_type() {
        return 'pw_gift_card';
    }

    public function get_card_number( $context = 'view' ) {
        return $this->get_prop( 'card_number', $context );
    }

    public function get_amount( $context = 'view' ) {
        return $this->get_prop( 'amount', $context );
    }
}
