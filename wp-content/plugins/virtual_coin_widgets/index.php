<?php defined( 'ABSPATH' ) or die( '' );

/*
Plugin Name: Virtual Coin Widgets
Plugin URI: https://codecanyon.net/item/virtual-coin-widgets-wordpress-shortcodes-for-600-coins/19343132
Description: Does your website need <strong>cryptocurrencies</strong> widgets? VCW provides you 10 shortcodes on the fly!</strong>.
Version: 2.2.2
Tags: cryptocurrency, bitcoin, ethereum, ripple, litecoin, dogecoin, finance, money, cash, market
Author: RunCoders
Author URI: https://runcoders.com
*/


define('VCW_VERSION',           '2.2.2');
define('VCW_URL',               plugin_dir_url(__FILE__));
define('VCW_ROOT',              plugin_dir_path(__FILE__));
define('VCW_INCLUDES_FOLDER',   VCW_ROOT.'includes/');
define('VCW_INDEX',             __FILE__);
define('VCW_AJAX_URL',          admin_url( 'admin-ajax.php' ));

function vcw_require_file($file)
{
    require_once VCW_INCLUDES_FOLDER."$file.php";
}

vcw_require_file('constants');
vcw_require_file('helpers');
vcw_require_file('data');
vcw_require_file('html');
vcw_require_file('widgets');
vcw_require_file('shortcodes');
vcw_require_file('admin');


