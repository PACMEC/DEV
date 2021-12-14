<?php

/*
Plugin Name: Coin Charts
Plugin URI: https://codecanyon.net/user/runcoders/portfolio
Description: Made for cryptocurrencies historical data display
Version: 1.2
Tags: cryptocurrencies, finance, bitcoin, ethereum, ripple, litecoin, dogecoin, market cap
Author: RunCoders
Author URI: https://codecanyon.net/user/runcoders
*/

defined( 'ABSPATH' ) or die( '' );

define('CCHARTS_VERSION', '1.2');
define('CCHARTS_URL', plugin_dir_url(__FILE__));
define('CCHARTS_ROOT', plugin_dir_path(__FILE__));
define('CCHARTS_INCLUDES', CCHARTS_ROOT.'includes/');
define('CCHARTS_JSON', CCHARTS_ROOT.'json/');
define('CCHARTS_INDEX', __FILE__);
define('CCHARTS_ADMIN_PAGES_URL', admin_url( 'admin.php?page=' ));
define('CCHARTS_AJAX_URL', admin_url( 'admin-ajax.php' ));



class CCharts_Init
{

    public static function load()
    {

        require_once CCHARTS_INCLUDES.'constants.php';
        require_once CCHARTS_INCLUDES.'requests.php';
        require_once CCHARTS_INCLUDES.'database.php';
        require_once CCHARTS_INCLUDES.'scheduler.php';
        require_once CCHARTS_INCLUDES.'api.php';
        require_once CCHARTS_INCLUDES.'shortcodes.php';
        require_once CCHARTS_INCLUDES.'admin.php';
        require_once CCHARTS_INCLUDES.'vc.php';

    }

}

CCharts_Init::load();