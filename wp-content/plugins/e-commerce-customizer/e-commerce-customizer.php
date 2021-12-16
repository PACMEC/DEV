<?php

/**
 * Plugin Name: E-Commerce - Personalizador de tienda
 * Version: 1.0.0
 * Plugin URI: #
 * Description: Un complemento de personalización de PACMEC, su respuesta para editar su E-Commerce y las páginas de productos, el carrito y las páginas de pago y también la página de su cuenta de usuario, todo dentro del Personalizador de la tienda.
 * Author: PACMEC
 * Author URI: #
 * Text Domain: e-commerce-customizer
 * Domain Path: /lang/
 * @package PACMEC
 * @author PACMEC
 * @since 1.0.0
 */
define( 'WCD_PLUGIN_VERSION', '2.3.5' );
define( 'WCD_PLUGIN_URL', plugins_url( '', __FILE__ ) );
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'wcz_fs' ) ) {
    wcz_fs()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( 'wcz_fs' ) ) {
        // Create a helper function for easy SDK access.
        function wcz_fs()
        {
            global  $wcz_fs ;
            
            if ( !isset( $wcz_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $wcz_fs = fs_dynamic_init( array(
                    'id'              => '9543',
                    'slug'            => 'ecommercecustomizer',
                    'premium_slug'    => 'ecommercecustomizer',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_a9515a9346cb7b9873a6fb9ae8906',
                    'is_premium'      => false,
                    'premium_suffix'  => 'Pro',
                    'has_addons'      => false,
                    'has_paid_plans'  => false,
                    'trial'           => array(
                    'days'               => 14,
                    'is_require_payment' => false,
                ),
                    'has_affiliation' => 'selected',
                    'menu'            => array(
											'slug'        => 'wcz_settings',
											'contact'     => false,
											'support'     => false,
											'affiliation' => false,
											'parent'      => array(
												'slug' => 'woocommerce',
											),
										),
                    'is_live'         => true,
                ) );
            }
            
            return $wcz_fs;
        }
        
        // Init Freemius.
        wcz_fs();
        // Signal that SDK was initiated.
        do_action( 'wcz_fs_loaded' );
    }
    
    // Load plugin class files.
    require_once 'includes/class-wcz.php';
    require_once 'includes/class-wcz-settings.php';
    // Load plugin libraries.
    require_once 'includes/class-wcz-admin-api.php';
    // Load Customizer Library files.
    require_once 'includes/customizer/customizer-options.php';
    require_once 'includes/customizer/customizer-library/customizer-library.php';
    require_once 'includes/customizer/styles.php';
    if ( WooCustomizer::wcz_is_plugin_active( 'woocommerce.php' ) ) {
        require_once 'includes/inc/woocommerce.php';
    }
    // Excluded from Pro Version
    
    if ( !WooCustomizer::wcz_is_plugin_active( 'woocommerce.php' ) ) {
        // Admin notice for if WooCommerce is not active
        function wcz_no_woocommerce_notice()
        {
            ?>
            <div class="error">
                <p><?php 
            esc_html_e( 'StoreCustomizer requires the WooCommerce plugin to be active to work', 'ecommercecustomizer' );
            ?></p>
            </div><?php 
        }
        
        add_action( 'admin_notices', 'wcz_no_woocommerce_notice' );
        return;
    }
    
    /**
     * Returns the main instance of WooCustomizer to prevent the need to use globals.
     *
     * @since  1.0.0
     * @return object WooCustomizer
     */
    function ecommercecustomizer()
    {
        $instance = WooCustomizer::instance( __FILE__, WCD_PLUGIN_VERSION );
        if ( is_null( $instance->settings ) ) {
            $instance->settings = WooCustomizer_Settings::instance( $instance );
        }
        return $instance;
    }
    
    ecommercecustomizer();
}
