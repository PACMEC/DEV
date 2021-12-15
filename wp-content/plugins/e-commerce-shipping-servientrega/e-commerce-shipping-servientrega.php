<?php
/**
 * Plugin Name: Shipping Servientrega Woocommerce
 * Description: Shipping Servientrega Woocommerce is available for Colombia
 * Version: 5.0.24
 * Author: Saul Morales Pacheco
 * Author URI: https://saulmoralespa.com
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC tested up to: 4.8
 * WC requires at least: 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if(!defined('SHIPPING_SERVIENTREGA_WC_SS_VERSION')){
    define('SHIPPING_SERVIENTREGA_WC_SS_VERSION', '5.0.24');
}

add_action( 'plugins_loaded', 'shipping_servientrega_wc_ss_init', 1 );

function shipping_servientrega_wc_ss_init()
{
    if ( ! shipping_servientrega_wc_ss_requirements() )
        return;

    shipping_servientrega_wc_ss()->run_servientrega_wc();

}

function shipping_servientrega_wc_ss_notices( $notice ) {
    ?>
    <div class="error notice">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function shipping_servientrega_wc_ss_requirements(){

    if ( ! function_exists( 'is_plugin_active' ) ) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );


    $openssl_warning = 'Shipping Servientrega Woocommerce requiere la extensión OpenSSL 1.0.1 o superior se encuentre instalada';

    if ( ! defined( 'OPENSSL_VERSION_TEXT' ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() use ( $openssl_warning ) {
                    shipping_servientrega_wc_ss_notices($openssl_warning );
                }
            );
        }
        return false;
    }

    preg_match( '/^(?:Libre|Open)SSL ([\d.]+)/', OPENSSL_VERSION_TEXT, $matches );
    if ( empty( $matches[1] ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() use ( $openssl_warning ) {
                    shipping_servientrega_wc_ss_notices( $openssl_warning );
                }
            );
        }
        return false;
    }

    if ( ! version_compare( $matches[1], '1.0.1', '>=' ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() use ( $openssl_warning ) {
                    shipping_servientrega_wc_ss_notices( $openssl_warning );
                }
            );
        }
        return false;
    }

    if ( ! extension_loaded( 'soap' ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    shipping_servientrega_wc_ss_notices( 'Shipping Servientrega Woocommerce requiere la extensión soap se encuentre instalada' );
                }
            );
        }
        return false;
    }

    if ( ! is_plugin_active(
        'woocommerce/woocommerce.php'
    ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    shipping_servientrega_wc_ss_notices( 'Shipping Servientrega Woocommerce requiere que se encuentre instalado y activo el plugin: Woocommerce' );
                }
            );
        }
        return false;
    }

    $plugin_path_departamentos_ciudades_colombia_woo = 'departamentos-y-ciudades-de-colombia-para-woocommerce/departamentos-y-ciudades-de-colombia-para-woocommerce.php';


    if ( ! is_plugin_active(
        $plugin_path_departamentos_ciudades_colombia_woo
    ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    $action = 'install-plugin';
                    $slug = 'departamentos-y-ciudades-de-colombia-para-woocommerce';
                    $plugin_install_url = wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => $action,
                                'plugin' => $slug
                            ),
                            admin_url( 'update.php' )
                        ),
                        $action.'_'.$slug
                    );
                    $plugin = 'Shipping Servientrega Woocommerce requiere que se encuentre instalado y activo el plugin: '  .
                        sprintf(
                            '%s',
                            "<a class='button button-primary' href='$plugin_install_url'>Departamentos y ciudades de Colombia para Woocommerce</a>" );

                    shipping_servientrega_wc_ss_notices( $plugin );
                }
            );
        }
        return false;
    }

    $departamentos_ciudades_colombia_woo_data = get_plugin_data( trailingslashit( WP_PLUGIN_DIR) . $plugin_path_departamentos_ciudades_colombia_woo);

    if (!version_compare ($departamentos_ciudades_colombia_woo_data['Version'] , '2.0.0', '>=')){
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    shipping_servientrega_wc_ss_notices( 'Shipping Servientrega Woocommerce requiere que el plugin <strong>Departamentos y Ciudades de Colombia para Woocommerce</strong> se encuentre actualizado' );
                }
            );
        }
        return false;
    }

    $woo_countries   = new WC_Countries();
    $default_country = $woo_countries->get_base_country();

    if ( ! in_array( $default_country, array( 'CO' ), true ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    $country = 'Shipping Servientrega Woocommerce requiere que el país donde se encuentra ubicada la tienda sea Colombia '  .
                        sprintf(
                            '%s',
                            '<a href="' . admin_url() .
                            'admin.php?page=wc-settings&tab=general#s2id_woocommerce_currency">' .
                            'Click para establecer</a>' );
                    shipping_servientrega_wc_ss_notices( $country );
                }
            );
        }
        return false;
    }

    $wc_main_settings = get_option('woocommerce_servientrega_shipping_settings');
    $license = $wc_main_settings['servientrega_license'] ?? '';

    if(empty($license)){
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    $plugin_license = 'Shipping Servientrega Woocommerce requiere una licencia para poder generar guías entre otras funciones: '  .
                        sprintf(
                            '%s',
                            "<a class='button button-primary' target='_blank' href='https://shop.saulmoralespa.com/producto/plugin-shipping-servientrega-woocommerce/'>Obtener licencia</a>" );
                    shipping_servientrega_wc_ss_notices( $plugin_license );
                }
            );
        }
    }

    return true;
}

function shipping_servientrega_wc_ss(){
    static $plugin;
    if (!isset($plugin)){
        require_once('includes/class-shipping-servientrega-wc-plugin.php');
        $plugin = new Shipping_Servientrega_WC_Plugin(__FILE__, SHIPPING_SERVIENTREGA_WC_SS_VERSION);
    }
    return $plugin;
}

function activate_shipping_servientrega_wc_ss(){
    wp_schedule_event( time(), 'twicedaily', 'shipping_servientrega_wc_ss_schedule' );
}

function deactivation_shipping_servientrega_wc_ss(){
    delete_option('servientrega_validation_error');
    wp_clear_scheduled_hook( 'shipping_servientrega_wc_ss_schedule' );
}

register_activation_hook( __FILE__, 'activate_shipping_servientrega_wc_ss' );
register_deactivation_hook( __FILE__, 'deactivation_shipping_servientrega_wc_ss' );
add_action( 'woocommerce_product_after_variable_attributes', array('Shipping_Servientrega_WC_Plugin', 'variation_settings_fields'), 10, 3 );
add_action( 'woocommerce_product_options_shipping', array('Shipping_Servientrega_WC_Plugin', 'add_custom_shipping_option_to_products'), 10);