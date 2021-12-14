<?php

defined( 'CCHARTS_INDEX' ) or die( '' );

class CCharts_ShortCodes {

    public static function load()
    {
        add_action( 'wp_enqueue_scripts', array(get_class(), 'enqueueAssets') );

        add_filter('widget_text','do_shortcode');

        add_shortcode( 'coin-chart', array(get_class(), 'chartShortCode'));
    }

    public static function chartShortCode($attrs)
    {

        extract(shortcode_atts( array(
            'symbol' => 'BTC',
            'theme' => 'light',
            'window' => '7d'
        ), $attrs ));

        return "<div class=\"coin-chart\" data-symbol=\"$symbol\" data-theme=\"$theme\" data-window=\"$window\">".
                    '<div class="coin-chart-buttons">'.
                        '<div class="coin-button" data-action="1d"><span>1D</span></div>'.
                        '<div class="coin-button" data-action="7d"><span>7D</span></div>'.
                        '<div class="coin-button" data-action="1m"><span>1M</span></div>'.
                        '<div class="coin-button" data-action="3m"><span>3M</span></div>'.
                        '<div class="coin-button" data-action="6m"><span>6M</span></div>'.
                        '<div class="coin-button" data-action="all"><span>ALL</span></div>'.
                    '</div>'.
                    '<div class="coin-chart-plot"></div>'.
                '</div>';
    }

    public static function enqueueAssets()
    {
        wp_enqueue_script('jquery', '', array(), false, true);
        wp_enqueue_script('google-charts','https://www.gstatic.com/charts/loader.js', array(), false, true);
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0');
        wp_enqueue_style('ccharts-style', CCHARTS_URL.'css/coincharts.css', array(), '1.1');
        wp_register_script('ccharts-script', CCHARTS_URL.'js/coincharts.min.js', array('jquery','google-charts'), '1.1', true);
        wp_localize_script('ccharts-script', 'CChartsConstants', array(
            'urls' => array(
                'ajax' => CCHARTS_AJAX_URL
            )
        ));
        wp_enqueue_script('ccharts-script');

    }

}

CCharts_ShortCodes::load();

