<?php

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'PW_Gift_Card_Item_Data' ) ) :

class PW_Gift_Card_Item_Data {

    public $wc_price_args = array();

    // For compatibility with a bug in "WooCommerce PDF Invoice Builder" by RedNao
    function get_id() {
        return 0;
    }

    // For compatibility with a bug in "MPesa For WooCommerce" by Osen Concepts Kenya
    function has_downloadable_item() {
        return false;
    }
}

endif;
