<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_REST_ADMIN_VERSION', '2021-01' );
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_ADMIN', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DIR . "admin" . DIRECTORY_SEPARATOR );
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_LANGUAGES', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DIR . "languages" . DIRECTORY_SEPARATOR );
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CACHE', WP_CONTENT_DIR . "/cache/import-shopify-to-woocommerce/" );
$plugin_url = plugins_url( '', __FILE__ );
$plugin_url = str_replace( '/includes', '', $plugin_url );
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS', $plugin_url . "/css/" );
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS_DIR', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DIR . "css" . DIRECTORY_SEPARATOR );
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS', $plugin_url . "/js/" );
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS_DIR', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DIR . "js" . DIRECTORY_SEPARATOR );
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_IMAGES', $plugin_url . "/images/" );
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_IMAGES_DIR', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DIR . "/images/" );

if ( is_file( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "functions.php" ) ) {
	require_once VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "functions.php";
}
if ( is_file( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "data.php" ) ) {
	require_once VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "data.php";
}

if ( is_file( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "support.php" ) ) {
	require_once VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "support.php";
}
if ( is_file( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "wp-async-request.php" ) ) {
	require_once VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "wp-async-request.php";
}
if ( is_file( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "wp-background-process.php" ) ) {
	require_once VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "wp-background-process.php";
}
if ( is_file( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "class-s2w-process-new.php" ) ) {
	require_once VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "class-s2w-process-new.php";
}
vi_include_folder( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_ADMIN, 'IMPORT_SHOPIFY_TO_WOOCOMMERCE_ADMIN_' );