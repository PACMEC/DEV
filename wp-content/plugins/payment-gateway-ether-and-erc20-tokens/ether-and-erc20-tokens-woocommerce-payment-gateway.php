<?php

/*
Plugin Name: Ether and ERC20 tokens WooCommerce Payment Gateway
Plugin URI: https://wordpress.org/plugins/ether-and-erc20-tokens-woocommerce-payment-gateway
Description: Ether and ERC20 tokens WooCommerce Payment Gateway enables customers to pay with Ether or any ERC20 or ERC223 token on your WooCommerce store.
Version: 4.12.5
WC requires at least: 5.5.1
WC tested up to: 5.9.0
Author: ethereumicoio
Author URI: https://ethereumico.io
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ether-and-erc20-tokens-woocommerce-payment-gateway
Domain Path: /languages
*/
if ( !function_exists( 'epg_plugin_deactivate' ) ) {
    function epg_plugin_deactivate()
    {
        if ( !current_user_can( 'activate_plugins' ) ) {
            return;
        }
        deactivate_plugins( plugin_basename( __FILE__ ) );
    }

}

if ( PHP_INT_SIZE != 8 ) {
    add_action( 'admin_init', 'epg_plugin_deactivate' );
    add_action( 'admin_notices', 'epg_plugin_admin_notice' );
    function epg_plugin_admin_notice()
    {
        if ( !current_user_can( 'activate_plugins' ) ) {
            return;
        }
        echo  '<div class="error"><p><strong>WooCommerce Ethereum ERC20 Payment Gateway</strong> requires 64 bit architecture server.</p></div>' ;
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }

} else {
    
    if ( version_compare( phpversion(), '7.0', '<' ) ) {
        add_action( 'admin_init', 'epg_plugin_deactivate' );
        add_action( 'admin_notices', 'epg_plugin_admin_notice' );
        function epg_plugin_admin_notice()
        {
            if ( !current_user_can( 'activate_plugins' ) ) {
                return;
            }
            echo  '<div class="error"><p><strong>WooCommerce Ethereum ERC20 Payment Gateway</strong> requires PHP version 7.0 or above.</p></div>' ;
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
        }
    
    } else {
        
        if ( !function_exists( 'gmp_init' ) ) {
            add_action( 'admin_init', 'epg_plugin_deactivate' );
            add_action( 'admin_notices', 'epg_plugin_admin_notice_gmp' );
            function epg_plugin_admin_notice_gmp()
            {
                if ( !current_user_can( 'activate_plugins' ) ) {
                    return;
                }
                echo  '<div class="error"><p><strong>WooCommerce Ethereum ERC20 Payment Gateway</strong> requires <a href="http://php.net/manual/en/book.gmp.php">GMP</a> module to be installed.</p></div>' ;
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
        
        } else {
            
            if ( !function_exists( 'mb_strtolower' ) ) {
                add_action( 'admin_init', 'epg_plugin_deactivate' );
                add_action( 'admin_notices', 'epg_plugin_admin_notice_mbstring' );
                function epg_plugin_admin_notice_mbstring()
                {
                    if ( !current_user_can( 'activate_plugins' ) ) {
                        return;
                    }
                    echo  '<div class="error"><p><strong>WooCommerce Ethereum ERC20 Payment Gateway</strong> requires <a href="http://php.net/manual/en/book.mbstring.php">Multibyte String (mbstring)</a> module to be installed.</p></div>' ;
                    if ( isset( $_GET['activate'] ) ) {
                        unset( $_GET['activate'] );
                    }
                }
            
            } else {
                
                if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && !('Jobster' == wp_get_theme()->name || 'Jobster' == wp_get_theme()->parent_theme) ) {
                    add_action( 'admin_init', 'epg_plugin_deactivate' );
                    add_action( 'admin_notices', 'epg_plugin_admin_notice_woocommerce' );
                    function epg_plugin_admin_notice_woocommerce()
                    {
                        if ( !current_user_can( 'activate_plugins' ) ) {
                            return;
                        }
                        echo  '<div class="error"><p><strong>WooCommerce Ethereum ERC20 Payment Gateway</strong> requires <a href="https://woocommerce.com/" target="_blank">WooCommerce</a> plugin or <a href="https://wpjobster.com/" target="_blank">Jobster</a> theme to be installed and activated.</p></div>' ;
                        if ( isset( $_GET['activate'] ) ) {
                            unset( $_GET['activate'] );
                        }
                    }
                
                } else {
                    require_once dirname( __FILE__ ) . '/vendor/woocommerce/action-scheduler/action-scheduler.php';
                    
                    if ( function_exists( 'ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init' ) ) {
                        ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init()->set_basename( false, __FILE__ );
                    } else {
                        // Create a helper function for easy SDK access.
                        function ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init()
                        {
                            global  $ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init ;
                            
                            if ( !isset( $ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init ) ) {
                                // Include Freemius SDK.
                                require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
                                $ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init = fs_dynamic_init( array(
                                    'id'              => '4817',
                                    'slug'            => 'ether-and-erc20-tokens-woocommerce-payment-gateway',
                                    'type'            => 'plugin',
                                    'public_key'      => 'pk_dac5fb3fab50ef382e06aa4089747',
                                    'is_premium'      => false,
                                    'premium_suffix'  => 'Business',
                                    'has_addons'      => false,
                                    'has_paid_plans'  => true,
                                    'trial'           => array(
                                    'days'               => 14,
                                    'is_require_payment' => true,
                                ),
                                    'has_affiliation' => 'all',
                                    'menu'            => array(
                                    'slug'           => 'wc-settings',
                                    'override_exact' => true,
                                    'first-path'     => 'admin.php?page=wc-settings&tab=checkout&section=ether-and-erc20-tokens-woocommerce-payment-gateway',
                                    'parent'         => array(
                                    'slug' => 'woocommerce',
                                ),
                                ),
                                    'is_live'         => true,
                                ) );
                            }
                            
                            return $ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init;
                        }
                        
                        // Init Freemius.
                        ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init();
                        // Signal that SDK was initiated.
                        do_action( 'ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init_loaded' );
                        function ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init_settings_url()
                        {
                            return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ether-and-erc20-tokens-woocommerce-payment-gateway' );
                        }
                        
                        ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init()->add_filter( 'connect_url', 'ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init_settings_url' );
                        ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init()->add_filter( 'after_skip_url', 'ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init_settings_url' );
                        ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init()->add_filter( 'after_connect_url', 'ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init_settings_url' );
                        ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init()->add_filter( 'after_pending_connect_url', 'ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init_settings_url' );
                        // ... Your plugin's main file logic ...
                        function ether_and_erc20_tokens_woocommerce_payment_gateway_load_textdomain()
                        {
                            /**
                             * Localize.
                             */
                            load_plugin_textdomain( 'ether-and-erc20-tokens-woocommerce-payment-gateway', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
                        }
                        
                        add_action( 'plugins_loaded', 'ether_and_erc20_tokens_woocommerce_payment_gateway_load_textdomain' );
                        // Add autoloaders, and load up the plugin.
                        require_once dirname( __FILE__ ) . '/vendor/autoload.php';
                        require_once dirname( __FILE__ ) . '/autoload.php';
                        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway'] = new \Ethereumico\Epg\Main( plugins_url( '', __FILE__ ), plugin_dir_path( __FILE__ ) );
                        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->run();
                        // Place in Option List on Settings > Plugins page
                        function ether_and_erc20_tokens_woocommerce_payment_gateway_actlinks( $links, $file )
                        {
                            // Static so we don't call plugin_basename on every plugin row.
                            static  $this_plugin ;
                            if ( !$this_plugin ) {
                                $this_plugin = plugin_basename( __FILE__ );
                            }
                            
                            if ( $file == $this_plugin ) {
                                $settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=' . 'ether-and-erc20-tokens-woocommerce-payment-gateway">' . __( 'Settings', 'woocommerce' ) . '</a>';
                                array_unshift( $links, $settings_link );
                                // before other links
                            }
                            
                            return $links;
                        }
                        
                        add_filter(
                            'plugin_action_links',
                            'ether_and_erc20_tokens_woocommerce_payment_gateway_actlinks',
                            10,
                            2
                        );
                        function ether_and_erc20_tokens_woocommerce_payment_gateway_complete_order( $order_id )
                        {
                            
                            if ( 'Jobster' == wp_get_theme()->name || 'Jobster' == wp_get_theme()->parent_theme ) {
                                $gateway = null;
                                if ( !isset( $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway1'] ) ) {
                                    $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway1'] = new \Ethereumico\Epg\Gateway();
                                }
                                $gateway = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway1'];
                                $paymentSuccess = $gateway->complete_order( $order_id );
                            } else {
                                
                                if ( function_exists( 'wc_get_payment_gateway_by_order' ) ) {
                                    $payment_gateway = wc_get_payment_gateway_by_order( $order_id );
                                    
                                    if ( !$payment_gateway ) {
                                        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log( "ether_and_erc20_tokens_woocommerce_payment_gateway_complete_order failed to get payment gateway for order: {$order_id}" );
                                        return;
                                    }
                                    
                                    if ( $payment_gateway instanceof \Ethereumico\Epg\Gateway ) {
                                        $payment_gateway->complete_order( $order_id );
                                    }
                                }
                            
                            }
                        
                        }
                        
                        add_action(
                            "ether_and_erc20_tokens_woocommerce_payment_gateway_complete_order",
                            'ether_and_erc20_tokens_woocommerce_payment_gateway_complete_order',
                            0,
                            1
                        );
                    }
                    
                    //if ( ! function_exists( 'ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init' ) ) {
                }
            
            }
        
        }
    
    }

}
