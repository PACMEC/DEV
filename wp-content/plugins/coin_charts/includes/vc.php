<?php

defined( 'CCHARTS_INDEX' ) or die( '' );

class CCharts_VC {

    static $icon_url;

    static public function load()
    {
        self::$icon_url = CCHARTS_URL.'images/vc_icon.png';

        add_action( 'vc_before_init', array(get_class(), 'initForVC' ) );
    }


    static public function initForVC()
    {

        if ( defined( 'WPB_VC_VERSION' ) && function_exists('vc_map')) {
            self::mapChart();
        }
    }

    static public function mapChart()
    {

        $symbols = array();

        foreach (CCharts_Constants::$currencies as $symbol => $info){
            $symbols[$info['name']] = $symbol;
        }


        vc_map( array(
            'name' => __('Coin Chart', CCharts_Constants::$text_domain),
            'description' => __('Cryptocurrency Historical Rates', CCharts_Constants::$text_domain),
            'base' => 'coin-chart',
            'class' => '',
            'controls' => 'full',
            'icon' => self::$icon_url,
            'category' => __('Coin Charts', CCharts_Constants::$text_domain),
            'params' => array(
                'theme' => array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __('Theme', CCharts_Constants::$text_domain),
                    'param_name' => 'theme',
                    'value' => array(
                        __('Light', CCharts_Constants::$text_domain) => 'light',
                        __('Dark', CCharts_Constants::$text_domain) => 'dark'
                    ),
                    'description' => __('Choose a theme', CCharts_Constants::$text_domain),
                    'std' => 'light'
                ),
                'symbol' => array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __('Symbol', CCharts_Constants::$text_domain),
                    'param_name' => 'symbol',
                    'value' => $symbols,
                    'description' => __('Choose the cryptocurrency', CCharts_Constants::$text_domain),
                    'std' => 'BTC'
                ),
                'window' => array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __('Window', CCharts_Constants::$text_domain),
                    'param_name' => 'window',
                    'value' => array(
                        __('1 Day', CCharts_Constants::$text_domain) => '1d',
                        __('7 Days', CCharts_Constants::$text_domain) => '7d',
                        __('1 Month', CCharts_Constants::$text_domain) => '1m',
                        __('3 Months', CCharts_Constants::$text_domain) => '3m',
                        __('6 Months', CCharts_Constants::$text_domain) => '6m',
                        __('All-time', CCharts_Constants::$text_domain) => 'all'
                    ),
                    'description' => __('Choose a time window', CCharts_Constants::$text_domain),
                    'std' => '7d'
                ),
            )
        ) );
    }

}

CCharts_VC::load();