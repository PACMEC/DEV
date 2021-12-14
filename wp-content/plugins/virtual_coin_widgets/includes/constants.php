<?php defined( 'VCW_INDEX' ) or die( '' );

if(!class_exists('VCW_Constants')) {

    class VCW_Constants {

        static public $color_schemas = array(
            'red'           => 'Red',
            'pink'          => 'Pink',
            'purple'        => 'Purple',
            'deep-purple'   => 'Deep Purple',
            'indigo'        => 'Indigo',
            'blue'          => 'Blue',
            'light-blue'    => 'Light Blue',
            'cyan'          => 'Cyan',
            'teal'          => 'Teal',
            'green'         => 'Green',
            'light-green'   => 'Light Green',
            'lime'          => 'Lime',
            'yellow'        => 'Yellow',
            'amber'         => 'Amber',
            'orange'        => 'Orange',
            'deep-orange'   => 'Deep Orange',
            'brown'         => 'Brown',
            'grey'          => 'Grey',
            'blue-grey'     => 'Blue Grey',
            'black'         => 'Black',
            'white'         => 'White'
        );

        static public $widgets_change_headers = array(
            'change_1h'     => '% 1h',
            'change_24h'    => '% 24h',
            'change_7d'     => '% 7d',
        );

        static public $table_widget_cols = array(
            'name'          => 'CryptoCurrency',
            'change_1h'     => 'Change 1h',
            'change_24h'    => 'Change 24h',
            'change_7d'     => 'Change 7d',
        );

        static public $small_table_widget_cols = array(
            'symbol'        => 'Symbol',
            'change_1h'     => '% 1h',
            'change_24h'    => '% 24h',
            'change_7d'     => '% 7d',
        );

        static public $converter_widget_priority = array(
            'BTC','ETH','USD','EUR','GBP','KRW','XRP','BCH'
        );
    }
}