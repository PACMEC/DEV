<?php
/**
 * Plugin Family Id: dangoodman/wc-weight-based-shipping
 * Plugin Name: E-Commerce - Envío basado en peso
 * Plugin URI: #
 * Description: Método de envío simple pero flexible para E-Commerce.
 * Version: 5.3.16
 * Author: PACMEC
 * Author URI: #
 * Requires PHP: 7.1
 */

if (!class_exists('WbsVendors_DgmWpPluginBootstrapGuard', false)) {
    require_once(__DIR__ .'/server/vendor/dangoodman/wp-plugin-bootstrap-guard/DgmWpPluginBootstrapGuard.php');
}

WbsVendors_DgmWpPluginBootstrapGuard::checkPrerequisitesAndBootstrap(
    'WooCommerce Weight Based Shipping',
    '7.1', '4.0', '3.2',
    __DIR__ .'/bootstrap.php'
);
