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

function pwgc_order_item_woocommerce_data_stores( $stores ) {
    if ( !isset( $stores[ 'order-item-pw_gift_card' ] ) ) {
        $stores[ 'order-item-pw_gift_card' ] = 'WC_Order_Item_PW_Gift_Card_Data_Store';
    }

    return $stores;
}
add_filter( 'woocommerce_data_stores', 'pwgc_order_item_woocommerce_data_stores' );

class WC_Order_Item_PW_Gift_Card_Data_Store extends Abstract_WC_Order_Item_Type_Data_Store implements WC_Object_Data_Store_Interface, WC_Order_Item_Type_Data_Store_Interface {

    protected $internal_meta_keys = array( 'card_number', 'amount' );

    public function read( &$item ) {
        parent::read( $item );
        $id = $item->get_id();
        $item->set_props( array(
            'card_number'   => get_metadata( 'order_item', $id, 'card_number', true ),
            'amount'        => get_metadata( 'order_item', $id, 'amount', true ),
        ) );
        $item->set_object_read( true );
    }

    public function save_item_data( &$item ) {
        $id          = $item->get_id();
        $save_values = array(
            'card_number'   => addslashes( $item->get_card_number( 'edit' ) ),
            'amount'        => $item->get_amount( 'edit' ),
        );
        foreach ( $save_values as $key => $value ) {
            update_metadata( 'order_item', $id, $key, $value );
        }
    }
}
