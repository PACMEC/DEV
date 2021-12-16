<?php
/*
Plugin Name: Formularios de contacto
Plugin URI: #
Description: Solo otro complemento de formulario de contacto. Sencillo pero flexible.
Author: FelipheGomez
Author URI: https://github.com/FelipheGomez
Text Domain: pacmec-contact-form
Domain Path: /languages/
Version: 1.0.0
*/
define( 'PCF_VERSION', '1.0.0' );
define( 'PCF_REQUIRED_WP_VERSION', '4.7' );
define( 'PCF_PLUGIN', __FILE__ );
define( 'PCF_PLUGIN_BASENAME', plugin_basename( PCF_PLUGIN ) );
define( 'PCF_PLUGIN_NAME', trim( dirname( PCF_PLUGIN_BASENAME ), '/' ) );
define( 'PCF_PLUGIN_DIR', untrailingslashit( dirname( PCF_PLUGIN ) ) );
define( 'PCF_PLUGIN_MODULES_DIR', PCF_PLUGIN_DIR . '/modules' );

if ( ! defined( 'PCF_LOAD_JS' ) ) define( 'PCF_LOAD_JS', true );
if ( ! defined( 'PCF_LOAD_CSS' ) ) define( 'PCF_LOAD_CSS', true );
if ( ! defined( 'PCF_AUTOP' ) ) define( 'PCF_AUTOP', true );
if ( ! defined( 'PCF_USE_PIPE' ) ) define( 'PCF_USE_PIPE', true );
if ( ! defined( 'PCF_ADMIN_READ_CAPABILITY' ) ) define( 'PCF_ADMIN_READ_CAPABILITY', 'edit_posts' );
if ( ! defined( 'PCF_ADMIN_READ_WRITE_CAPABILITY' ) ) define( 'PCF_ADMIN_READ_WRITE_CAPABILITY', 'publish_pages' );
if ( ! defined( 'PCF_VERIFY_NONCE' ) ) define( 'PCF_VERIFY_NONCE', false );
if ( ! defined( 'PCF_USE_REALLY_SIMPLE_CAPTCHA' ) ) define( 'PCF_USE_REALLY_SIMPLE_CAPTCHA', false );
if ( ! defined( 'PCF_VALIDATE_CONFIGURATION' ) ) define( 'PCF_VALIDATE_CONFIGURATION', true );
// Deprecated, not used in the plugin core. Use pcf_plugin_url() instead.
define( 'PCF_PLUGIN_URL', untrailingslashit( plugins_url( '', PCF_PLUGIN ) ) );

require_once PCF_PLUGIN_DIR . '/settings.php';