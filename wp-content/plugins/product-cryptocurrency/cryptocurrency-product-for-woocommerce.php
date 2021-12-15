<?php

/*
Plugin Name: Cryptocurrency Product for WooCommerce
Plugin URI: https://wordpress.org/plugins/cryptocurrency-product-for-woocommerce
Description: Cryptocurrency Product for WooCommerce enables customers to buy Ether or any ERC20 or ERC223 token on your WooCommerce store for fiat, bitcoin or any other currency supported by WooCommerce.
Version: 3.14.0
WC requires at least: 5.5.0
WC tested up to: 5.8
Author: ethereumicoio
Author URI: https://ethereumico.io
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: cryptocurrency-product-for-woocommerce
Domain Path: /languages
*/
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Explicitly globalize to support bootstrapped WordPress
global 
    $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_basename,
    $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options,
    $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir,
    $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_url_path,
    $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_product
;
if ( !function_exists( 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_deactivate' ) ) {
    function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_deactivate()
    {
        if ( !current_user_can( 'activate_plugins' ) ) {
            return;
        }
        deactivate_plugins( plugin_basename( __FILE__ ) );
    }

}

if ( PHP_INT_SIZE != 8 ) {
    add_action( 'admin_init', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_deactivate' );
    add_action( 'admin_notices', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_admin_notice' );
    function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_admin_notice()
    {
        if ( !current_user_can( 'activate_plugins' ) ) {
            return;
        }
        echo  '<div class="error"><p><strong>Cryptocurrency Product for WooCommerce</strong> requires 64 bit architecture server.</p></div>' ;
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }

} else {
    
    if ( version_compare( phpversion(), '7.0', '<' ) ) {
        add_action( 'admin_init', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_deactivate' );
        add_action( 'admin_notices', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_admin_notice' );
        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_admin_notice()
        {
            if ( !current_user_can( 'activate_plugins' ) ) {
                return;
            }
            echo  '<div class="error"><p><strong>Cryptocurrency Product for WooCommerce</strong> requires PHP version 7.0 or above.</p></div>' ;
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
        }
    
    } else {
        
        if ( !function_exists( 'gmp_init' ) ) {
            add_action( 'admin_init', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_deactivate' );
            add_action( 'admin_notices', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_admin_notice_gmp' );
            function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_admin_notice_gmp()
            {
                if ( !current_user_can( 'activate_plugins' ) ) {
                    return;
                }
                echo  '<div class="error"><p><strong>Cryptocurrency Product for WooCommerce</strong> requires  <a target="_blank" href="http://php.net/manual/en/book.gmp.php">GMP</a> module to be installed.</p></div>' ;
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
        
        } else {
            
            if ( !function_exists( 'mb_strtolower' ) ) {
                add_action( 'admin_init', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_deactivate' );
                add_action( 'admin_notices', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_admin_notice_mbstring' );
                function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_admin_notice_mbstring()
                {
                    if ( !current_user_can( 'activate_plugins' ) ) {
                        return;
                    }
                    echo  '<div class="error"><p><strong>Cryptocurrency Product for WooCommerce</strong> requires  <a target="_blank" href="http://php.net/manual/en/book.mbstring.php">Multibyte String (mbstring)</a> module to be installed.</p></div>' ;
                    if ( isset( $_GET['activate'] ) ) {
                        unset( $_GET['activate'] );
                    }
                }
            
            } else {
                /**
                 * Check if WooCommerce is active
                 * https://wordpress.stackexchange.com/a/193908/137915
                 **/
                
                if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                    add_action( 'admin_init', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_deactivate' );
                    add_action( 'admin_notices', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_admin_notice_woocommerce' );
                    function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_admin_notice_woocommerce()
                    {
                        if ( !current_user_can( 'activate_plugins' ) ) {
                            return;
                        }
                        echo  '<div class="error"><p><strong>Cryptocurrency Product for WooCommerce</strong> requires WooCommerce plugin to be installed and activated.</p></div>' ;
                        if ( isset( $_GET['activate'] ) ) {
                            unset( $_GET['activate'] );
                        }
                    }
                
                } else {
                    
                    if ( function_exists( 'cryptocurrency_product_for_woocommerce_freemius_init' ) ) {
                        cryptocurrency_product_for_woocommerce_freemius_init()->set_basename( false, __FILE__ );
                    } else {
                        // Create a helper function for easy SDK access.
                        function cryptocurrency_product_for_woocommerce_freemius_init()
                        {
                            global  $cryptocurrency_product_for_woocommerce_freemius_init ;
                            
                            if ( !isset( $cryptocurrency_product_for_woocommerce_freemius_init ) ) {
                                // Include Freemius SDK.
                                require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
                                $cryptocurrency_product_for_woocommerce_freemius_init = fs_dynamic_init( array(
                                    'id'              => '4418',
                                    'slug'            => 'cryptocurrency-product-for-woocommerce',
                                    'type'            => 'plugin',
                                    'public_key'      => 'pk_ad7ad2f13633e6e97e62528e0259b',
                                    'is_premium'      => false,
                                    'premium_suffix'  => 'Professional',
                                    'has_addons'      => true,
                                    'has_paid_plans'  => true,
                                    'trial'           => array(
                                    'days'               => 7,
                                    'is_require_payment' => true,
                                ),
                                    'has_affiliation' => 'all',
                                    'menu'            => array(
                                    'slug'   => 'cryptocurrency-product-for-woocommerce',
                                    'parent' => array(
                                    'slug' => 'options-general.php',
                                ),
                                ),
                                    'is_live'         => true,
                                ) );
                            }
                            
                            return $cryptocurrency_product_for_woocommerce_freemius_init;
                        }
                        
                        // Init Freemius.
                        cryptocurrency_product_for_woocommerce_freemius_init();
                        // Signal that SDK was initiated.
                        do_action( 'cryptocurrency_product_for_woocommerce_freemius_init_loaded' );
                        // ... Your plugin's main file logic ...
                        $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_basename = plugin_basename( dirname( __FILE__ ) );
                        $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
                        $plugin_url_path = untrailingslashit( plugin_dir_url( __FILE__ ) );
                        // HTTPS?
                        $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_url_path = ( is_ssl() ? str_replace( 'http:', 'https:', $plugin_url_path ) : $plugin_url_path );
                        // Set plugin options
                        $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options = get_option( 'cryptocurrency-product-for-woocommerce_options', array() );
                        require $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir . '/vendor/autoload.php';
                        require_once $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir . '/vendor/woocommerce/action-scheduler/action-scheduler.php';
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_product_type_options( $product_type_options )
                        {
                            $cryptocurrency = array(
                                'cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' => array(
                                'id'            => '_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type',
                                'wrapper_class' => 'show_if_simple show_if_variable show_if_auction',
                                'label'         => __( 'Cryptocurrency', 'cryptocurrency-product-for-woocommerce' ),
                                'description'   => __( 'Make product a cryptocurrency.', 'cryptocurrency-product-for-woocommerce' ),
                                'default'       => 'no',
                            ),
                            );
                            // combine the two arrays
                            $product_type_options = array_merge( $cryptocurrency, $product_type_options );
                            //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('product_type_options=' . print_r($product_type_options, true));
                            return apply_filters( 'cryptocurrency_product_for_woocommerce_product_type_options', $product_type_options );
                        }
                        
                        add_filter( 'product_type_options', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_product_type_options' );
                        // Function to check if a product is a cryptocurrency
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product_id )
                        {
                            $cryptocurrency = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, '_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type', true );
                            $is_cryptocurrency = ( !empty($cryptocurrency) ? 'yes' : 'no' );
                            return $is_cryptocurrency === 'yes';
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_meta( $post_id, $post )
                        {
                            global 
                                $wpdb,
                                $woocommerce,
                                $woocommerce_errors,
                                $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options
                            ;
                            $product = wc_get_product( $post_id );
                            if ( !$product ) {
                                return $post_id;
                            }
                            if ( get_post_type( $post_id ) != 'product' ) {
                                return $post_id;
                            }
                            // check if we are called from the product settings page
                            // fix from: https://stackoverflow.com/questions/5434219/problem-with-wordpress-save-post-action#comment6729746_5849143
                            if ( !isset( $_POST['_text_input_cryptocurrency_flag'] ) ) {
                                return $post_id;
                            }
                            if ( !current_user_can( 'edit_product', $post_id ) ) {
                                return $post_id;
                            }
                            $is_cryptocurrency = ( isset( $_POST['_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type'] ) ? 'yes' : 'no' );
                            
                            if ( $is_cryptocurrency != 'yes' ) {
                                delete_post_meta( $post_id, '_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' );
                                return $post_id;
                            }
                            
                            update_post_meta( $post_id, '_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type', $is_cryptocurrency );
                            //    if ( get_option( "woocommerce_enable_multiples") != "yes" ) {
                            //        update_post_meta( $post_id, '_sold_individually', $is_cryptocurrency );
                            //    }
                            $want_physical = get_option( 'woocommerce_enable_physical' );
                            if ( $want_physical == "no" ) {
                                update_post_meta( $post_id, '_virtual', $is_cryptocurrency );
                            }
                            //
                            // Handle first save
                            //
                            // Select
                            $cryptocurrency_option = $_POST['_select_cryptocurrency_option'];
                            
                            if ( !empty($cryptocurrency_option) ) {
                                update_post_meta( $post_id, '_select_cryptocurrency_option', esc_attr( $cryptocurrency_option ) );
                            } else {
                                update_post_meta( $post_id, '_select_cryptocurrency_option', '' );
                            }
                            
                            //    if ( isset( $_POST['_text_input_cryptocurrency_data'] ) ) {
                            //        update_post_meta( $post_id, '_text_input_cryptocurrency_data', sanitize_text_field( $_POST['_text_input_cryptocurrency_data'] ) );
                            //    }
                            if ( isset( $_POST['_text_input_cryptocurrency_minimum_value'] ) ) {
                                update_post_meta( $post_id, '_text_input_cryptocurrency_minimum_value', sanitize_text_field( $_POST['_text_input_cryptocurrency_minimum_value'] ) );
                            }
                            if ( isset( $_POST['_text_input_cryptocurrency_step'] ) ) {
                                update_post_meta( $post_id, '_text_input_cryptocurrency_step', sanitize_text_field( $_POST['_text_input_cryptocurrency_step'] ) );
                            }
                            do_action(
                                'cryptocurrency_product_for_woocommerce_save_option_field',
                                $cryptocurrency_option,
                                $post_id,
                                $product
                            );
                            $product_id = $post_id;
                            $vendor_id = get_post_field( 'post_author_override', $product_id );
                            if ( empty($vendor_id) ) {
                                $vendor_id = get_post_field( 'post_author', $product_id );
                            }
                            if ( !user_can( $vendor_id, 'vendor' ) ) {
                                // process only vendor's products
                                return $post_id;
                            }
                            $vendor_fee = 0;
                            if ( $vendor_fee <= 0 ) {
                                return $post_id;
                            }
                            // Ether rate
                            $rate = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_ETH_rate( 1 );
                            
                            if ( is_null( $rate ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( 'Failed to get Ether rate' );
                                return $post_id;
                            }
                            
                            $eth_value = $vendor_fee / $rate;
                            $eth_value_wei = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_double_int_multiply( $eth_value, pow( 10, 18 ) );
                            // 1. check balance
                            $thisWalletAddress = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress( $product_id );
                            $providerUrl = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getWeb3Endpoint();
                            try {
                                $eth_balance = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBalanceEth( $thisWalletAddress, $providerUrl );
                                
                                if ( null === $eth_balance || $eth_balance->compare( $eth_value_wei ) < 0 ) {
                                    // @see https://wordpress.stackexchange.com/a/42178/137915
                                    // unhook this function to prevent indefinite loop
                                    remove_action( 'save_post', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_meta' );
                                    // update the post to change post status
                                    wp_update_post( array(
                                        'ID'          => $post_id,
                                        'post_status' => 'draft',
                                    ) );
                                    // re-hook this function again
                                    add_action( 'save_post', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_meta' );
                                }
                            
                            } catch ( Exception $ex ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_meta: " . $ex->getMessage() );
                            }
                        }
                        
                        add_action(
                            'save_post',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_meta',
                            10,
                            2
                        );
                        // @see https://wordpress.stackexchange.com/a/42178/137915
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_redirect_location( $location, $post_id )
                        {
                            //If post was published...
                            
                            if ( isset( $_POST['publish'] ) ) {
                                //obtain current post status
                                $status = get_post_status( $post_id );
                                //The post was 'published', but if it is still a draft, display draft message (10).
                                if ( $status == 'draft' ) {
                                    $location = add_query_arg( 'message', 10, $location );
                                }
                            }
                            
                            return $location;
                        }
                        
                        add_filter(
                            'redirect_post_location',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_redirect_location',
                            10,
                            2
                        );
                        /**
                         * Show pricing fields for cryptocurrency product.
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_custom_js()
                        {
                            global  $post ;
                            if ( 'product' != get_post_type() ) {
                                return;
                            }
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_custom_js_aux();
                        }
                        
                        /**
                         * Show pricing fields for cryptocurrency product.
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_custom_js_aux()
                        {
                            global  $post ;
                            //    $post_id = $post->ID;
                            //    $product = wc_get_product( $post_id );
                            //    if (!$product) {
                            //        return;
                            //    }
                            //    if (!_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product->get_id() )) {
                            //        return;
                            //    }
                            ?><script type='text/javascript'>
        <?php 
                            ?>

		jQuery( document ).ready( function() {
            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_init();
//			jQuery( '.options_group.pricing' ).addClass( 'show_if_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' ).show();
			jQuery( '#_select_cryptocurrency_option' ).on( 'change', CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_select_cryptocurrency_option_change);
            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_select_cryptocurrency_option_change();
			jQuery( '#_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' ).on( 'change', CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_select_cryptocurrency_product_type);
            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_select_cryptocurrency_product_type();
            <?php 
                            ?>

		});

	</script><?php 
                        }
                        
                        add_action( 'admin_footer', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_custom_js' );
                        /**
                         * Amount in base crypto for one $
                         *
                         * @param int $product_id
                         * @return double
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_( $product_id )
                        {
                            $_product = wc_get_product( $product_id );
                            
                            if ( !$_product ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_({$product_id}) not a product" );
                                return 1;
                            }
                            
                            $price = doubleval( $_product->get_price() );
                            
                            if ( 0 == $price ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_({$product_id}) zero product price" );
                                return 1;
                            }
                            
                            return 1 / $price;
                        }
                        
                        /**
                         * Amount in crypto for the item specified
                         *
                         * @param int $product_id
                         * @param object $item
                         * @return double
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_quantity_for_item( $product_id, $item )
                        {
                            return $item['qty'];
                        }
                        
                        /**
                         * Product price in $
                         *
                         * @param double $orig_price
                         * @param type $product
                         * @param bool $sale
                         * @return double
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_price( $orig_price, $product, $sale = false )
                        {
                            $product_id = ( !is_null( $product ) ? $product->get_id() : null );
                            if ( $sale && empty($orig_price) ) {
                                return $orig_price;
                            }
                            return $orig_price;
                        }
                        
                        /**
                         * ETH price in $
                         *
                         * @param double $orig_price
                         * @param type $product
                         * @param bool $sale
                         * @return double
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_ETH_rate( $orig_price )
                        {
                            $product = null;
                            $product_id = null;
                            $sale = false;
                            return $orig_price;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_base_cryptocurrency_symbol( $product_id )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir ;
                            $baseCurrency = '';
                            $_select_dynamic_price_source_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, '_select_dynamic_price_source_option', true );
                            if ( empty($_select_dynamic_price_source_option) ) {
                                return $baseCurrency;
                            }
                            return apply_filters(
                                'cryptocurrency_product_for_woocommerce_get_base_cryptocurrency_symbol',
                                $baseCurrency,
                                $_select_dynamic_price_source_option,
                                $product_id
                            );
                        }
                        
                        // @see https://www.php.net/manual/en/function.debug-backtrace.php#112238
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_generateCallTrace()
                        {
                            $e = new Exception();
                            $trace = explode( "\n", $e->getTraceAsString() );
                            // reverse array to make steps line up chronologically
                            $trace = array_reverse( $trace );
                            array_shift( $trace );
                            // remove {main}
                            array_pop( $trace );
                            // remove call to this method
                            $length = count( $trace );
                            $result = array();
                            for ( $i = 0 ;  $i < $length ;  $i++ ) {
                                $result[] = $i + 1 . ')' . substr( $trace[$i], strpos( $trace[$i], ' ' ) );
                                // replace '#someNum' with '$i)', set the right ordering
                            }
                            return "\t" . implode( "\n\t", $result );
                        }
                        
                        // @see https://stackoverflow.com/a/47788626/4256005
                        add_filter(
                            'woocommerce_product_get_price',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_price',
                            10,
                            2
                        );
                        add_filter(
                            'woocommerce_product_get_sale_price',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_sale_price',
                            10,
                            2
                        );
                        add_filter(
                            'woocommerce_product_get_regular_price',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_regular_price',
                            10,
                            2
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_price( $price, $product )
                        {
                            global  $post ;
                            //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_price $price: ' . $price);
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product->get_id() ) ) {
                                return $price;
                            }
                            //    if( is_shop() || is_product_category() || is_product_tag() || is_product() || is_cart() || is_checkout() || (wp_doing_ajax() && !is_checkout()) ||
                            //        ( function_exists('get_current_screen') &&
                            //        get_current_screen() && get_current_screen()->parent_base == 'woocommerce' &&
                            //        'shop_order' == get_post_type($post) )
                            //    ) {
                            
                            if ( $product->is_on_sale() ) {
                                if ( empty($price) ) {
                                    return $price;
                                }
                                $sale_price = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_price( $price, $product, true );
                                // / CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product->get_id());
                                //           CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_price $sale_price: ' . $sale_price);
                                return $sale_price;
                            } else {
                                $regular_price = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_price( $price, $product );
                                // / CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product->get_id());
                                //           CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_price $regular_price: ' . $regular_price);
                                return $regular_price;
                            }
                            
                            //    }
                            return $price;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_sale_price( $price, $product )
                        {
                            global  $post ;
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product->get_id() ) ) {
                                return $price;
                            }
                            
                            if ( is_shop() || is_product_category() || is_product_tag() || is_product() || wp_doing_ajax() && !is_checkout() || function_exists( 'get_current_screen' ) && get_current_screen() && get_current_screen()->parent_base == 'woocommerce' && 'shop_order' == get_post_type( $post ) ) {
                                if ( empty($price) ) {
                                    return $price;
                                }
                                $sale_price = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_price( $price, $product, true );
                                // / CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product->get_id());
                                //       CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_sale_price $sale_price: ' . $sale_price);
                                return $sale_price;
                            }
                            
                            return $price;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_regular_price( $price, $product )
                        {
                            global  $post ;
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product->get_id() ) ) {
                                return $price;
                            }
                            
                            if ( is_shop() || is_product_category() || is_product_tag() || is_product() || wp_doing_ajax() && !is_checkout() || function_exists( 'get_current_screen' ) && get_current_screen() && get_current_screen()->parent_base == 'woocommerce' && 'shop_order' == get_post_type( $post ) ) {
                                $regular_price = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_price( $price, $product );
                                // / CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product->get_id());
                                //       CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_regular_price $regular_price: ' . $regular_price);
                                return $regular_price;
                            }
                            
                            return $price;
                        }
                        
                        //add_filter( 'woocommerce_add_cart_item', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_add_cart_item', 20, 2 );
                        //function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_add_cart_item( $cart_data, $cart_item_key ) {
                        //    $product_id = $cart_data['product_id'];
                        //    if (!_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product_id )) {
                        //        return $cart_data;
                        //    }
                        //    $product = wc_get_product($product_id);
                        //    $new_price = $cart_data['data']->get_price();
                        //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_add_cart_item $cart_data[data]->get_price(): ' . $cart_data['data']->get_price());
                        //    // Price calculation
                        //    if ( $product->is_on_sale() ) {
                        //        if (empty($new_price)) {
                        //            return $new_price;
                        //        }
                        //        $product_price = $product->get_sale_price();
                        //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_add_cart_item $product_price: ' . $product_price);
                        //        if ($new_price < $product_price) {
                        //            $new_price = $product_price;
                        //        }
                        ////        $new_price = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_price( $new_price, $product, true ) / CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product_id);
                        //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_add_cart_item CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product_id): ' . CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product_id));
                        //        $new_price = $new_price / CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product_id);
                        //    } else {
                        //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_add_cart_item CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_price( $new_price, $product ): ' . CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_price( $new_price, $product ));
                        //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_add_cart_item CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product_id): ' . CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product_id));
                        //        $product_price = $product->get_regular_price();
                        //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_add_cart_item $product_price: ' . $product_price);
                        //        if ($new_price < $product_price) {
                        //            $new_price = $product_price;
                        //        }
                        ////        $new_price = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_price( $new_price, $product ) / CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product_id);
                        //        $new_price = $new_price / CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_product_rate_($product_id);
                        //    }
                        //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_add_cart_item $new_price: ' . $new_price);
                        //
                        //    // Set and register the new calculated price
                        //    $cart_data['data']->set_price( $new_price );
                        //    $cart_data['new_price'] = $new_price;
                        //
                        //    return $cart_data;
                        //}
                        add_filter(
                            'woocommerce_get_cart_item_from_session',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_cart_item_from_session',
                            20,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_cart_item_from_session( $session_data, $values, $key )
                        {
                            if ( !isset( $session_data['new_price'] ) || empty($session_data['new_price']) ) {
                                return $session_data;
                            }
                            //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_cart_item_from_session $session_data[new_price]: ' . $session_data['new_price']);
                            // Get the new calculated price and update cart session item price
                            $session_data['data']->set_price( $session_data['new_price'] );
                            return $session_data;
                        }
                        
                        // // @see https://github.com/woocommerce/woocommerce/blob/e2b59d44ee88c131e044c49e508767620415e1e6/includes/wc-formatting-functions.php#L557
                        // /**
                        //  * Format the price with a currency symbol.
                        //  *
                        //  * @param  float $price Formatted price.
                        //  * @param  array $args  Arguments to format a price {
                        //  *     Array of arguments.
                        //  *     Defaults to empty array.
                        //  *
                        //  *     @type bool   $ex_tax_label       Adds exclude tax label.
                        //  *                                      Defaults to false.
                        //  *     @type string $currency           Currency code.
                        //  *                                      Defaults to empty string (Use the result from get_woocommerce_currency()).
                        //  *     @type string $decimal_separator  Decimal separator.
                        //  *                                      Defaults the result of wc_get_price_decimal_separator().
                        //  *     @type string $thousand_separator Thousand separator.
                        //  *                                      Defaults the result of wc_get_price_thousand_separator().
                        //  *     @type string $decimals           Number of decimals.
                        //  *                                      Defaults the result of wc_get_price_decimals().
                        //  *     @type string $price_format       Price format depending on the currency position.
                        //  *                                      Defaults the result of get_woocommerce_price_format().
                        //  * }
                        //  * @param  float $unformatted_price Raw price.
                        //  * @return string
                        //  */
                        // function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wc_price( $return, $price, $args, $unformatted_price ) {
                        //     global $wp, $woocommerce, $post, $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_product;
                        // //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wc_price: return=' . $return . '; price=' . $price . '; unformatted_price=' . $unformatted_price . '; is_shop=' . is_shop() . '; is_cart=' . is_cart() . '; is_checkout=' . is_checkout() . '; wp_doing_ajax=' . wp_doing_ajax());
                        //     if ( cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() ) {
                        //         if ( cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
                        //
                        //     $product = null;
                        //     if (wp_doing_ajax() && !is_checkout() && !is_null(WC()->cart)) {
                        // //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wc_price($post_id) is_cart() || is_checkout()");
                        //         foreach( WC()->cart->get_cart() as $cart_item_key => $p ) {
                        //             if (_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency($p['product_id'])) {
                        //                 $product = wc_get_product( $p['product_id'] );
                        //                 break;
                        //             }
                        //         }
                        //     }
                        //     if (!$post) {
                        //         if (is_null($product)) {
                        //             return $return;
                        //         }
                        //     }
                        //     $post_id = $post->ID;
                        //     if (!$product) {
                        //         $product = wc_get_product( $post_id );
                        //     }
                        //     if (!$product && isset($CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_product) &&
                        //         !is_null($CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_product)
                        //     ) {
                        //         $product = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_product;
                        //     }
                        //     if (!$product && !is_null(WC()->cart)/* && (is_cart() || is_checkout() || is_shop())*/) {
                        // //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wc_price($post_id) is_cart() || is_checkout()");
                        //         foreach( WC()->cart->get_cart() as $cart_item_key => $p ) {
                        //             if (_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency($p['product_id'])) {
                        //                 $product = wc_get_product( $p['product_id'] );
                        //                 break;
                        //             }
                        //         }
                        //     }
                        //     if (!$product && is_checkout_pay_page() && isset($wp->query_vars['order-pay'])) {
                        //         $order = wc_get_order($wp->query_vars['order-pay']);
                        //         if ($order) {
                        //             $order_items = $order->get_items();
                        //             foreach( $order_items as $p ) {
                        //                 $product_id = $p['product_id'];
                        //                 if (_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency($product_id)) {
                        //                     $product = wc_get_product( $product_id );
                        //                     break;
                        //                 }
                        //             }
                        //         }
                        //     }
                        //     if (!$product && is_order_received_page() && isset($wp->query_vars['order-received'])) {
                        //         $order = wc_get_order($wp->query_vars['order-received']);
                        //         if ($order) {
                        //             $order_items = $order->get_items();
                        //             foreach( $order_items as $p ) {
                        //                 $product_id = $p['product_id'];
                        //                 if (_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency($product_id)) {
                        //                     $product = wc_get_product( $product_id );
                        //                     break;
                        //                 }
                        //             }
                        //         }
                        //     }
                        //     if (!$product) {
                        //         $order_id = $post_id;
                        //         $order = wc_get_order($order_id);
                        //         if ($order) {
                        //             $order_items = $order->get_items();
                        //             foreach( $order_items as $p ) {
                        //                 $product_id = $p['product_id'];
                        //                 if (_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency($product_id)) {
                        //                     $product = wc_get_product( $product_id );
                        //                     break;
                        //                 }
                        //             }
                        //         }
                        //     }
                        //
                        //     if (!$product) {
                        // //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wc_price($post_id) not a product");
                        //         return $return;
                        //     }
                        //     $return = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html_ex( $return, $price, $args, $unformatted_price, $product );
                        //
                        //     	}
                        //     }
                        //     return $return;
                        // }
                        // add_filter( 'wc_price', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wc_price', 100, 5 );
                        add_filter(
                            'woocommerce_cart_product_price',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_product_price',
                            PHP_INT_MAX,
                            2
                        );
                        add_filter(
                            'woocommerce_get_price_html',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html',
                            PHP_INT_MAX,
                            2
                        );
                        // add_filter( 'woocommerce_get_variation_price_html',  'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_variation_price_html', PHP_INT_MAX, 2 ); // used only in below WooCommerce v3.0.0
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_product_price( $price, $product )
                        {
                            if ( is_null( WC()->cart ) ) {
                                //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_product_price: is_null(WC()->cart)");
                                return $price;
                            }
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product->get_id() ) ) {
                                //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_product_price: not a cryptocurrency: " . $product->get_id());
                                return $price;
                            }
                            
                            if ( WC()->cart->display_prices_including_tax() ) {
                                $product_price = wc_get_price_including_tax( $product );
                            } else {
                                $product_price = wc_get_price_excluding_tax( $product );
                            }
                            
                            $price = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $product_price, $product );
                            remove_filter( 'woocommerce_cart_product_price', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_product_price', PHP_INT_MAX );
                            $price = apply_filters( 'woocommerce_cart_product_price', $price, $product );
                            add_filter(
                                'woocommerce_cart_product_price',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_product_price',
                                PHP_INT_MAX,
                                2
                            );
                            return $price;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html( $price, $product )
                        {
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product->get_id() ) ) {
                                return $price;
                            }
                            
                            if ( class_exists( 'WC_Product_Variable' ) && $product instanceof WC_Product_Variable ) {
                                return CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html_WC_Product_Variable( $price, $product );
                            } else {
                                if ( class_exists( 'WC_Product_Grouped' ) && $product instanceof WC_Product_Grouped ) {
                                    return CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html_WC_Product_Grouped( $price, $product );
                                }
                            }
                            
                            return CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html_WC_Product( $price, $product );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html_WC_Product( $price, $product )
                        {
                            
                            if ( '' === $product->get_price() ) {
                                $price = apply_filters( 'woocommerce_empty_price_html', '', $product );
                            } else {
                                $simple_price = wc_get_price_to_display( $product );
                                $simple_price = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $simple_price, $product );
                                
                                if ( $product->is_on_sale() ) {
                                    $regular_price = wc_get_price_to_display( $product, array(
                                        'price' => $product->get_regular_price(),
                                    ) );
                                    $regular_price = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $regular_price, $product );
                                    $price = wc_format_sale_price( $regular_price, $simple_price ) . $product->get_price_suffix();
                                } else {
                                    $price = $simple_price . $product->get_price_suffix();
                                }
                            
                            }
                            
                            remove_filter( 'woocommerce_get_price_html', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html', PHP_INT_MAX );
                            $price = apply_filters( 'woocommerce_get_price_html', $price, $product );
                            add_filter(
                                'woocommerce_get_price_html',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html',
                                PHP_INT_MAX,
                                2
                            );
                            return $price;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html_WC_Product_Variable( $price, $product )
                        {
                            $prices = $product->get_variation_prices( true );
                            
                            if ( empty($prices['price']) ) {
                                $price = apply_filters( 'woocommerce_variable_empty_price_html', '', $product );
                            } else {
                                $min_price = current( $prices['price'] );
                                $max_price = end( $prices['price'] );
                                $min_reg_price = current( $prices['regular_price'] );
                                $max_reg_price = end( $prices['regular_price'] );
                                $min_price_display = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $min_price, $product );
                                
                                if ( $min_price !== $max_price ) {
                                    $max_price_display = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $max_price, $product );
                                    $price = wc_format_price_range( $min_price_display, $max_price_display );
                                } elseif ( $product->is_on_sale() && $min_reg_price === $max_reg_price ) {
                                    $max_reg_price_display = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $max_reg_price, $product );
                                    $price = wc_format_sale_price( $max_reg_price_display, $min_price_display );
                                } else {
                                    $price = $min_price_display;
                                }
                                
                                $price = apply_filters( 'woocommerce_variable_price_html', $price . $product->get_price_suffix(), $product );
                            }
                            
                            remove_filter( 'woocommerce_get_price_html', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html', PHP_INT_MAX );
                            $price = apply_filters( 'woocommerce_get_price_html', $price, $product );
                            add_filter(
                                'woocommerce_get_price_html',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html',
                                PHP_INT_MAX,
                                2
                            );
                            return $price;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html_WC_Product_Grouped( $price, $product )
                        {
                            $tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
                            $child_prices = array();
                            $children = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );
                            foreach ( $children as $child ) {
                                if ( '' !== $child->get_price() ) {
                                    $child_prices[] = ( 'incl' === $tax_display_mode ? wc_get_price_including_tax( $child ) : wc_get_price_excluding_tax( $child ) );
                                }
                            }
                            
                            if ( !empty($child_prices) ) {
                                $min_price = min( $child_prices );
                                $max_price = max( $child_prices );
                            } else {
                                $min_price = '';
                                $max_price = '';
                            }
                            
                            
                            if ( '' !== $min_price ) {
                                $min_price_display = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $min_price, $product );
                                
                                if ( $min_price !== $max_price ) {
                                    $max_price_display = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $max_price, $product );
                                    $price = wc_format_price_range( $min_price_display, $max_price_display );
                                } else {
                                    $price = $min_price_display;
                                }
                                
                                $is_free = 0 === $min_price && 0 === $max_price;
                                
                                if ( $is_free ) {
                                    $price = apply_filters( 'woocommerce_grouped_free_price_html', __( 'Free!', 'woocommerce' ), $product );
                                } else {
                                    $price = apply_filters(
                                        'woocommerce_grouped_price_html',
                                        $price . $product->get_price_suffix(),
                                        $product,
                                        $child_prices
                                    );
                                }
                            
                            } else {
                                $price = apply_filters( 'woocommerce_grouped_empty_price_html', '', $product );
                            }
                            
                            remove_filter( 'woocommerce_get_price_html', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html', PHP_INT_MAX );
                            $price = apply_filters( 'woocommerce_get_price_html', $price, $product );
                            add_filter(
                                'woocommerce_get_price_html',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_price_html',
                                PHP_INT_MAX,
                                2
                            );
                            return $price;
                        }
                        
                        add_filter(
                            'woocommerce_cart_product_subtotal',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_product_subtotal',
                            PHP_INT_MAX,
                            4
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_product_subtotal(
                            $product_subtotal,
                            $product,
                            $quantity,
                            $cart
                        )
                        {
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product->get_id() ) ) {
                                return $product_subtotal;
                            }
                            $price = $product->get_price();
                            
                            if ( $product->is_taxable() ) {
                                
                                if ( $cart->display_prices_including_tax() ) {
                                    $row_price = wc_get_price_including_tax( $product, array(
                                        'qty' => $quantity,
                                    ) );
                                    $product_subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $row_price, $product );
                                    if ( !wc_prices_include_tax() && $cart->get_subtotal_tax() > 0 ) {
                                        $product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                                    }
                                } else {
                                    $row_price = wc_get_price_excluding_tax( $product, array(
                                        'qty' => $quantity,
                                    ) );
                                    $product_subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $row_price, $product );
                                    if ( wc_prices_include_tax() && $cart->get_subtotal_tax() > 0 ) {
                                        $product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                                    }
                                }
                            
                            } else {
                                $row_price = $price * $quantity;
                                $product_subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $row_price, $product );
                            }
                            
                            remove_filter( 'woocommerce_cart_product_subtotal', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_product_subtotal', PHP_INT_MAX );
                            $product_subtotal = apply_filters(
                                'woocommerce_cart_product_subtotal',
                                $product_subtotal,
                                $product,
                                $quantity,
                                $cart
                            );
                            add_filter(
                                'woocommerce_cart_product_subtotal',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_product_subtotal',
                                PHP_INT_MAX,
                                4
                            );
                            return $product_subtotal;
                        }
                        
                        add_filter(
                            'woocommerce_cart_subtotal',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_subtotal',
                            PHP_INT_MAX,
                            3
                        );
                        // bool $compound whether to include compound taxes.
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_subtotal( $cart_subtotal, $compound, $cart )
                        {
                            $product = null;
                            foreach ( $cart->get_cart() as $cart_item_key => $p ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $p['product_id'] ) ) {
                                    $product = wc_get_product( $p['product_id'] );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $cart_subtotal;
                            }
                            /**
                             * If the cart has compound tax, we want to show the subtotal as cart + shipping + non-compound taxes (after discount).
                             */
                            
                            if ( $compound ) {
                                $cart_subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $cart->get_cart_contents_total() + $cart->get_shipping_total() + $cart->get_taxes_total( false, false ), $product );
                            } elseif ( $cart->display_prices_including_tax() ) {
                                $cart_subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $cart->get_subtotal() + $cart->get_subtotal_tax(), $product );
                                if ( $cart->get_subtotal_tax() > 0 && !wc_prices_include_tax() ) {
                                    $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                                }
                            } else {
                                $cart_subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $cart->get_subtotal(), $product );
                                if ( $cart->get_subtotal_tax() > 0 && wc_prices_include_tax() ) {
                                    $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                                }
                            }
                            
                            remove_filter( 'woocommerce_cart_subtotal', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_subtotal', PHP_INT_MAX );
                            $cart_subtotal = apply_filters(
                                'woocommerce_cart_subtotal',
                                $cart_subtotal,
                                $compound,
                                $cart
                            );
                            add_filter(
                                'woocommerce_cart_subtotal',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_subtotal',
                                PHP_INT_MAX,
                                3
                            );
                            return $cart_subtotal;
                        }
                        
                        add_filter(
                            'woocommerce_cart_contents_total',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_contents_total',
                            PHP_INT_MAX,
                            1
                        );
                        // bool $compound whether to include compound taxes.
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_contents_total( $cart_total )
                        {
                            if ( is_null( WC()->cart ) ) {
                                return $cart_total;
                            }
                            $cart = WC()->cart;
                            $product = null;
                            foreach ( WC()->cart->get_cart() as $cart_item_key => $p ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $p['product_id'] ) ) {
                                    $product = wc_get_product( $p['product_id'] );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $cart_total;
                            }
                            $total_raw = ( wc_prices_include_tax() ? $cart->get_cart_contents_total() + $cart->get_cart_contents_tax() : $cart->get_cart_contents_total() );
                            $cart_total = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $total_raw, $product );
                            remove_filter( 'woocommerce_cart_contents_total', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_contents_total', PHP_INT_MAX );
                            $cart_total = apply_filters( 'woocommerce_cart_contents_total', $cart_total );
                            add_filter(
                                'woocommerce_cart_contents_total',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_contents_total',
                                PHP_INT_MAX,
                                1
                            );
                            return $cart_total;
                        }
                        
                        add_filter(
                            'woocommerce_cart_total_ex_tax',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total_ex_tax',
                            PHP_INT_MAX,
                            1
                        );
                        // bool $compound whether to include compound taxes.
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total_ex_tax( $cart_total )
                        {
                            if ( is_null( WC()->cart ) ) {
                                return $cart_total;
                            }
                            $cart = WC()->cart;
                            $product = null;
                            foreach ( WC()->cart->get_cart() as $cart_item_key => $p ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $p['product_id'] ) ) {
                                    $product = wc_get_product( $p['product_id'] );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $cart_total;
                            }
                            $total_raw = max( 0, $cart->get_total( 'edit' ) - $cart->get_total_tax() );
                            $cart_total = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $total_raw, $product );
                            remove_filter( 'woocommerce_cart_total_ex_tax', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total_ex_tax', PHP_INT_MAX );
                            $cart_total = apply_filters( 'woocommerce_cart_total_ex_tax', $cart_total );
                            add_filter(
                                'woocommerce_cart_total_ex_tax',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total_ex_tax',
                                PHP_INT_MAX,
                                1
                            );
                            return $cart_total;
                        }
                        
                        add_filter(
                            'woocommerce_cart_shipping_total',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_shipping_total',
                            PHP_INT_MAX,
                            2
                        );
                        // bool $compound whether to include compound taxes.
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_shipping_total( $total, $cart )
                        {
                            $product = null;
                            foreach ( $cart->get_cart() as $cart_item_key => $p ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $p['product_id'] ) ) {
                                    $product = wc_get_product( $p['product_id'] );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $total;
                            }
                            // Default total assumes Free shipping.
                            $total = __( 'Free!', 'woocommerce' );
                            if ( 0 < $cart->get_shipping_total() ) {
                                
                                if ( $cart->display_prices_including_tax() ) {
                                    $total_raw = $cart->shipping_total + $cart->shipping_tax_total;
                                    $total = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $total_raw, $product );
                                    if ( $cart->shipping_tax_total > 0 && !wc_prices_include_tax() ) {
                                        $total .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                                    }
                                } else {
                                    $total_raw = $cart->shipping_total;
                                    $total = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $total_raw, $product );
                                    if ( $cart->shipping_tax_total > 0 && wc_prices_include_tax() ) {
                                        $total .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                                    }
                                }
                            
                            }
                            remove_filter( 'woocommerce_cart_shipping_total', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_shipping_total', PHP_INT_MAX );
                            $total = apply_filters( 'woocommerce_cart_shipping_total', $total, $cart );
                            add_filter(
                                'woocommerce_cart_shipping_total',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_shipping_total',
                                PHP_INT_MAX,
                                2
                            );
                            return $total;
                        }
                        
                        add_filter(
                            'woocommerce_cart_tax_totals',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_tax_totals',
                            PHP_INT_MAX,
                            2
                        );
                        // bool $compound whether to include compound taxes.
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_tax_totals( $tax_totals, $cart )
                        {
                            $product = null;
                            foreach ( $cart->get_cart() as $cart_item_key => $p ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $p['product_id'] ) ) {
                                    $product = wc_get_product( $p['product_id'] );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $tax_totals;
                            }
                            $shipping_taxes = $cart->get_shipping_taxes();
                            // Shipping taxes are rounded differently, so we will subtract from all taxes, then round and then add them back.
                            $taxes = $cart->get_taxes();
                            $tax_totals = array();
                            foreach ( $taxes as $key => $tax ) {
                                $code = WC_Tax::get_rate_code( $key );
                                
                                if ( $code || apply_filters( 'woocommerce_cart_remove_taxes_zero_rate_id', 'zero-rated' ) === $key ) {
                                    
                                    if ( !isset( $tax_totals[$code] ) ) {
                                        $tax_totals[$code] = new stdClass();
                                        $tax_totals[$code]->amount = 0;
                                    }
                                    
                                    $tax_totals[$code]->tax_rate_id = $key;
                                    $tax_totals[$code]->is_compound = WC_Tax::is_compound( $key );
                                    $tax_totals[$code]->label = WC_Tax::get_rate_label( $key );
                                    
                                    if ( isset( $shipping_taxes[$key] ) ) {
                                        $tax -= $shipping_taxes[$key];
                                        $tax = wc_round_tax_total( $tax );
                                        $tax += NumberUtil::round( $shipping_taxes[$key], wc_get_price_decimals() );
                                        unset( $shipping_taxes[$key] );
                                    }
                                    
                                    $tax_totals[$code]->amount += wc_round_tax_total( $tax );
                                    $tax_totals[$code]->formatted_amount = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $tax_totals[$code]->amount, $product );
                                }
                            
                            }
                            
                            if ( apply_filters( 'woocommerce_cart_hide_zero_taxes', true ) ) {
                                $amounts = array_filter( wp_list_pluck( $tax_totals, 'amount' ) );
                                $tax_totals = array_intersect_key( $tax_totals, $amounts );
                            }
                            
                            remove_filter( 'woocommerce_cart_tax_totals', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_tax_totals', PHP_INT_MAX );
                            $tax_totals = apply_filters( 'woocommerce_cart_tax_totals', $tax_totals, $cart );
                            add_filter(
                                'woocommerce_cart_tax_totals',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_tax_totals',
                                PHP_INT_MAX,
                                2
                            );
                            return $tax_totals;
                        }
                        
                        add_filter(
                            'woocommerce_cart_total',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total',
                            PHP_INT_MAX,
                            1
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total( $cart_total )
                        {
                            if ( is_null( WC()->cart ) ) {
                                return $cart_total;
                            }
                            $cart = WC()->cart;
                            $product = null;
                            foreach ( WC()->cart->get_cart() as $cart_item_key => $p ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $p['product_id'] ) ) {
                                    $product = wc_get_product( $p['product_id'] );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $cart_total;
                            }
                            $totals = WC()->cart->get_totals();
                            $total = ( isset( $totals['total'] ) ? $totals['total'] : 0 );
                            $total = apply_filters( 'woocommerce_cart_get_total', $total );
                            $total_display = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $total, $product );
                            remove_filter( 'woocommerce_cart_total', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total', PHP_INT_MAX );
                            $total_return = apply_filters( 'woocommerce_cart_total', $total_display );
                            add_filter(
                                'woocommerce_cart_total',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total',
                                PHP_INT_MAX,
                                1
                            );
                            return $total_return;
                        }
                        
                        add_filter(
                            'woocommerce_cart_total_discount',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total_discount',
                            PHP_INT_MAX,
                            2
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total_discount( $cart_total, $cart )
                        {
                            $product = null;
                            foreach ( $cart->get_cart() as $cart_item_key => $p ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $p['product_id'] ) ) {
                                    $product = wc_get_product( $p['product_id'] );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $cart_total;
                            }
                            $total_display = ( $cart->get_discount_total() ? CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $cart->get_discount_total(), $product ) : false );
                            remove_filter( 'woocommerce_cart_total_discount', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total_discount', PHP_INT_MAX );
                            $total_return = apply_filters( 'woocommerce_cart_total_discount', $total_display );
                            add_filter(
                                'woocommerce_cart_total_discount',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_total_discount',
                                PHP_INT_MAX,
                                2
                            );
                            return $total_return;
                        }
                        
                        add_filter(
                            'woocommerce_cart_shipping_method_full_label',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_shipping_method_full_label',
                            PHP_INT_MAX,
                            2
                        );
                        // bool $compound whether to include compound taxes.
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_shipping_method_full_label( $label, $method )
                        {
                            if ( is_null( WC()->cart ) ) {
                                return $label;
                            }
                            $cart = WC()->cart;
                            $product = null;
                            foreach ( WC()->cart->get_cart() as $cart_item_key => $p ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $p['product_id'] ) ) {
                                    $product = wc_get_product( $p['product_id'] );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $label;
                            }
                            $label = $method->get_label();
                            $has_cost = 0 < $method->cost;
                            $hide_cost = !$has_cost && in_array( $method->get_method_id(), array( 'free_shipping', 'local_pickup' ), true );
                            if ( $has_cost && !$hide_cost ) {
                                
                                if ( WC()->cart->display_prices_including_tax() ) {
                                    $cost_display = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $method->cost + $method->get_shipping_tax(), $product );
                                    $label .= ': ' . $cost_display;
                                    if ( $method->get_shipping_tax() > 0 && !wc_prices_include_tax() ) {
                                        $label .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                                    }
                                } else {
                                    $cost_display = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $method->cost, $product );
                                    $label .= ': ' . $cost_display;
                                    if ( $method->get_shipping_tax() > 0 && wc_prices_include_tax() ) {
                                        $label .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                                    }
                                }
                            
                            }
                            remove_filter( 'woocommerce_cart_shipping_method_full_label', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_shipping_method_full_label', PHP_INT_MAX );
                            $label = apply_filters( 'woocommerce_cart_shipping_method_full_label', $label, $method );
                            add_filter(
                                'woocommerce_cart_shipping_method_full_label',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_cart_shipping_method_full_label',
                                PHP_INT_MAX,
                                2
                            );
                            return $label;
                        }
                        
                        add_filter(
                            'woocommerce_get_formatted_order_total',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_formatted_order_total',
                            PHP_INT_MAX,
                            4
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_formatted_order_total(
                            $formatted_total,
                            $order,
                            $tax_display,
                            $display_refunded
                        )
                        {
                            $product = null;
                            foreach ( $order->get_items() as $item ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $item->get_product_id() ) ) {
                                    $product = wc_get_product( $item->get_product_id() );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $formatted_total;
                            }
                            $formatted_total = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $order->get_total(), $product, array(
                                'currency' => $order->get_currency(),
                            ) );
                            $order_total = $order->get_total();
                            $total_refunded = $order->get_total_refunded();
                            $tax_string = '';
                            // Tax for inclusive prices.
                            
                            if ( wc_tax_enabled() && 'incl' === $tax_display ) {
                                $tax_string_array = array();
                                $tax_totals = $order->get_tax_totals();
                                
                                if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
                                    foreach ( $tax_totals as $code => $tax ) {
                                        $tax_amount = ( $total_refunded && $display_refunded ? CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( WC_Tax::round( $tax->amount - $order->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ), $product, array(
                                            'currency' => $order->get_currency(),
                                        ) ) : CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $tax->amount, $product, array(
                                            'currency' => $order->get_currency(),
                                        ) ) );
                                        $tax_string_array[] = sprintf( '%s %s', $tax_amount, $tax->label );
                                    }
                                } elseif ( !empty($tax_totals) ) {
                                    $tax_amount = ( $total_refunded && $display_refunded ? $order->get_total_tax() - $order->get_total_tax_refunded() : $order->get_total_tax() );
                                    $tax_string_array[] = sprintf( '%s %s', CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $tax_amount, $product, array(
                                        'currency' => $order->get_currency(),
                                    ) ), WC()->countries->tax_or_vat() );
                                }
                                
                                if ( !empty($tax_string_array) ) {
                                    /* translators: %s: taxes */
                                    $tax_string = ' <small class="includes_tax">' . sprintf( __( '(includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) ) . '</small>';
                                }
                            }
                            
                            
                            if ( $total_refunded && $display_refunded ) {
                                $formatted_total = '<del aria-hidden="true">' . wp_strip_all_tags( $formatted_total ) . '</del> <ins>' . CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $order_total - $total_refunded, $product, array(
                                    'currency' => $order->get_currency(),
                                ) ) . $tax_string . '</ins>';
                            } else {
                                $formatted_total .= $tax_string;
                            }
                            
                            remove_filter( 'woocommerce_get_formatted_order_total', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_formatted_order_total', PHP_INT_MAX );
                            $formatted_total = apply_filters(
                                'woocommerce_get_formatted_order_total',
                                $formatted_total,
                                $order,
                                $tax_display,
                                $display_refunded
                            );
                            add_filter(
                                'woocommerce_get_formatted_order_total',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_formatted_order_total',
                                PHP_INT_MAX,
                                4
                            );
                            return $formatted_total;
                        }
                        
                        add_filter(
                            'woocommerce_order_formatted_line_subtotal',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_formatted_line_subtotal',
                            PHP_INT_MAX,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_formatted_line_subtotal( $subtotal, $item, $order )
                        {
                            $product = null;
                            if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $item->get_product_id() ) ) {
                                $product = wc_get_product( $item->get_product_id() );
                            }
                            if ( is_null( $product ) ) {
                                return $subtotal;
                            }
                            $tax_display = get_option( 'woocommerce_tax_display_cart' );
                            
                            if ( 'excl' === $tax_display ) {
                                $ex_tax_label = ( $order->get_prices_include_tax() ? 1 : 0 );
                                $subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $order->get_line_subtotal( $item ), $product, array(
                                    'ex_tax_label' => $ex_tax_label,
                                    'currency'     => $order->get_currency(),
                                ) );
                            } else {
                                $subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $order->get_line_subtotal( $item, true ), $product, array(
                                    'currency' => $order->get_currency(),
                                ) );
                            }
                            
                            remove_filter( 'woocommerce_order_formatted_line_subtotal', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_formatted_line_subtotal', PHP_INT_MAX );
                            $subtotal = apply_filters(
                                'woocommerce_order_formatted_line_subtotal',
                                $subtotal,
                                $item,
                                $order
                            );
                            add_filter(
                                'woocommerce_order_formatted_line_subtotal',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_formatted_line_subtotal',
                                PHP_INT_MAX,
                                3
                            );
                            return $subtotal;
                        }
                        
                        add_filter(
                            'woocommerce_order_subtotal_to_display',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_subtotal_to_display',
                            PHP_INT_MAX,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_subtotal_to_display( $subtotal, $compound, $order )
                        {
                            $product = null;
                            foreach ( $order->get_items() as $item ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $item->get_product_id() ) ) {
                                    $product = wc_get_product( $item->get_product_id() );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $subtotal;
                            }
                            $tax_display = get_option( 'woocommerce_tax_display_cart' );
                            // $subtotal    = $order->get_cart_subtotal_for_order();
                            $subtotal = wc_remove_number_precision( $order->get_rounded_items_total(
                                // $order->get_values_for_total( 'subtotal' )
                                array_map( function ( $item ) {
                                    return wc_add_number_precision( $item['subtotal'], false );
                                }, array_values( $order->get_items() ) )
                            ) );
                            
                            if ( !$compound ) {
                                
                                if ( 'incl' === $tax_display ) {
                                    $subtotal_taxes = 0;
                                    $round_at_subtotal = get_option( 'woocommerce_tax_round_at_subtotal' );
                                    $in_cents = false;
                                    foreach ( $order->get_items() as $item ) {
                                        // $subtotal_taxes += self::round_line_tax( $item->get_subtotal_tax(), false );
                                        
                                        if ( 'yes' !== $round_at_subtotal ) {
                                            $subtotal_taxes += wc_round_tax_total( $item->get_subtotal_tax(), ( $in_cents ? 0 : null ) );
                                        } else {
                                            $subtotal_taxes += $item->get_subtotal_tax();
                                        }
                                    
                                    }
                                    $subtotal += wc_round_tax_total( $subtotal_taxes );
                                }
                                
                                $subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $subtotal, $product, array(
                                    'currency' => $order->get_currency(),
                                ) );
                                if ( 'excl' === $tax_display && $order->get_prices_include_tax() && wc_tax_enabled() ) {
                                    $subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                                }
                            } else {
                                if ( 'incl' === $tax_display ) {
                                    return '';
                                }
                                // Add Shipping Costs.
                                $subtotal += $order->get_shipping_total();
                                // Remove non-compound taxes.
                                foreach ( $order->get_taxes() as $tax ) {
                                    if ( $tax->is_compound() ) {
                                        continue;
                                    }
                                    $subtotal = $subtotal + $tax->get_tax_total() + $tax->get_shipping_tax_total();
                                }
                                // Remove discounts.
                                $subtotal = $subtotal - $order->get_total_discount();
                                $subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $subtotal, $product, array(
                                    'currency' => $order->get_currency(),
                                ) );
                            }
                            
                            remove_filter( 'woocommerce_order_subtotal_to_display', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_subtotal_to_display', PHP_INT_MAX );
                            $subtotal = apply_filters(
                                'woocommerce_order_subtotal_to_display',
                                $subtotal,
                                $item,
                                $order
                            );
                            add_filter(
                                'woocommerce_order_subtotal_to_display',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_subtotal_to_display',
                                PHP_INT_MAX,
                                3
                            );
                            return $subtotal;
                        }
                        
                        add_filter(
                            'woocommerce_order_discount_to_display',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_discount_to_display',
                            PHP_INT_MAX,
                            2
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_discount_to_display( $discount, $order )
                        {
                            $product = null;
                            foreach ( $order->get_items() as $item ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $item->get_product_id() ) ) {
                                    $product = wc_get_product( $item->get_product_id() );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $discount;
                            }
                            $tax_display = get_option( 'woocommerce_tax_display_cart' );
                            $discount = $order->get_total_discount( 'excl' === $tax_display && 'excl' === get_option( 'woocommerce_tax_display_cart' ) );
                            $discount = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $discount, $product, array(
                                'currency' => $order->get_currency(),
                            ) );
                            remove_filter( 'woocommerce_order_discount_to_display', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_discount_to_display', PHP_INT_MAX );
                            $discount = apply_filters( 'woocommerce_order_discount_to_display', $discount, $order );
                            add_filter(
                                'woocommerce_order_discount_to_display',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_discount_to_display',
                                PHP_INT_MAX,
                                2
                            );
                            return $discount;
                        }
                        
                        add_filter(
                            'woocommerce_order_shipping_to_display',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_shipping_to_display',
                            PHP_INT_MAX,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_shipping_to_display( $shipping, $order, $tax_display )
                        {
                            $product = null;
                            foreach ( $order->get_items() as $item ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $item->get_product_id() ) ) {
                                    $product = wc_get_product( $item->get_product_id() );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $shipping;
                            }
                            $tax_display = ( $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' ) );
                            
                            if ( 0 < abs( (double) $order->get_shipping_total() ) ) {
                                
                                if ( 'excl' === $tax_display ) {
                                    // Show shipping excluding tax.
                                    $shipping = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $order->get_shipping_total(), $product, array(
                                        'currency' => $order->get_currency(),
                                    ) );
                                    if ( (double) $order->get_shipping_tax() > 0 && $order->get_prices_include_tax() ) {
                                        $shipping .= apply_filters(
                                            'woocommerce_order_shipping_to_display_tax_label',
                                            '&nbsp;<small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>',
                                            $order,
                                            $tax_display
                                        );
                                    }
                                } else {
                                    // Show shipping including tax.
                                    $shipping = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $order->get_shipping_total() + $order->get_shipping_tax(), $product, array(
                                        'currency' => $order->get_currency(),
                                    ) );
                                    if ( (double) $order->get_shipping_tax() > 0 && !$order->get_prices_include_tax() ) {
                                        $shipping .= apply_filters(
                                            'woocommerce_order_shipping_to_display_tax_label',
                                            '&nbsp;<small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>',
                                            $order,
                                            $tax_display
                                        );
                                    }
                                }
                                
                                /* translators: %s: method */
                                $shipping .= apply_filters( 'woocommerce_order_shipping_to_display_shipped_via', '&nbsp;<small class="shipped_via">' . sprintf( __( 'via %s', 'woocommerce' ), $order->get_shipping_method() ) . '</small>', $order );
                            } elseif ( $order->get_shipping_method() ) {
                                $shipping = $order->get_shipping_method();
                            } else {
                                $shipping = __( 'Free!', 'woocommerce' );
                            }
                            
                            remove_filter( 'woocommerce_order_shipping_to_display', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_shipping_to_display', PHP_INT_MAX );
                            $shipping = apply_filters(
                                'woocommerce_order_shipping_to_display',
                                $shipping,
                                $order,
                                $tax_display
                            );
                            add_filter(
                                'woocommerce_order_shipping_to_display',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_shipping_to_display',
                                PHP_INT_MAX,
                                3
                            );
                            return $shipping;
                        }
                        
                        add_filter(
                            'woocommerce_get_order_item_totals',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_order_item_totals',
                            PHP_INT_MAX,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_get_order_item_totals( $total_rows, $order, $tax_display )
                        {
                            $product = null;
                            foreach ( $order->get_items() as $item ) {
                                
                                if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $item->get_product_id() ) ) {
                                    $product = wc_get_product( $item->get_product_id() );
                                    break;
                                }
                            
                            }
                            if ( is_null( $product ) ) {
                                return $total_rows;
                            }
                            // $order->add_order_item_totals_fee_rows( $total_rows, $tax_display );
                            $fees = $order->get_fees();
                            if ( $fees ) {
                                foreach ( $fees as $id => $fee ) {
                                    if ( apply_filters( 'woocommerce_get_order_item_totals_excl_free_fees', empty($fee['line_total']) && empty($fee['line_tax']), $id ) ) {
                                        continue;
                                    }
                                    $total_rows['fee_' . $fee->get_id()] = array(
                                        'label' => $fee->get_name() . ':',
                                        'value' => CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( ( 'excl' === $tax_display ? $fee->get_total() : $fee->get_total() + $fee->get_total_tax() ), $product, array(
                                        'currency' => $order->get_currency(),
                                    ) ),
                                    );
                                }
                            }
                            // $order->add_order_item_totals_tax_rows( $total_rows, $tax_display );
                            if ( 'excl' === $tax_display && wc_tax_enabled() ) {
                                
                                if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
                                    foreach ( $order->get_tax_totals() as $code => $tax ) {
                                        $total_rows[sanitize_title( $code )] = array(
                                            'label' => $tax->label . ':',
                                            'value' => CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $tax->amount, $product, array(
                                            'currency' => $order->get_currency(),
                                        ) ),
                                        );
                                    }
                                } else {
                                    $total_rows['tax'] = array(
                                        'label' => WC()->countries->tax_or_vat() . ':',
                                        'value' => CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $order->get_total_tax(), $product, array(
                                        'currency' => $order->get_currency(),
                                    ) ),
                                    );
                                }
                            
                            }
                            // $order->add_order_item_totals_refund_rows( $total_rows, $tax_display );
                            $refunds = $order->get_refunds();
                            if ( $refunds ) {
                                foreach ( $refunds as $id => $refund ) {
                                    $total_rows['refund_' . $id] = array(
                                        'label' => ( $refund->get_reason() ? $refund->get_reason() : __( 'Refund', 'woocommerce' ) . ':' ),
                                        'value' => CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( '-' . $refund->get_amount(), $product, array(
                                        'currency' => $order->get_currency(),
                                    ) ),
                                    );
                                }
                            }
                            return $total_rows;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html( $price, $product, $args = array() )
                        {
                            global 
                                $wp,
                                $woocommerce,
                                $post,
                                $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_product
                            ;
                            return wc_price( $price );
                        }
                        
                        /**
                        * If option not found in a product, look it in a _POST
                        */
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, $option_name, $single = true )
                        {
                            $option = get_post_meta( $product_id, $option_name, $single );
                            if ( empty($option) && isset( $_POST[$option_name] ) ) {
                                $option = $_POST[$option_name];
                            }
                            return $option;
                        }
                        
                        /**
                        * Save option in a _POST also
                        */
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_update_post_meta( $product_id, $option_name, $option_value )
                        {
                            update_post_meta( $product_id, $option_name, $option_value );
                            $_POST[$option_name] = $option_value;
                        }
                        
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_wc_price_html_ex(
                            $return,
                            $price,
                            $args,
                            $unformatted_price,
                            $product
                        )
                        {
                            global 
                                $wp,
                                $woocommerce,
                                $post,
                                $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_product
                            ;
                            return $return;
                        }
                        
                        /**
                         * Add a custom product tab.
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_custom_product_tabs( $tabs )
                        {
                            $tabs['cryptocurrency'] = array(
                                'label'  => __( 'Cryptocurrency', 'cryptocurrency-product-for-woocommerce' ),
                                'target' => 'cryptocurrency_product_data',
                                'class'  => array( 'show_if_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type', 'show_if_variable_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type', 'show_if_auction_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' ),
                            );
                            //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log('tabs=' . print_r($tabs, true));
                            return $tabs;
                        }
                        
                        add_filter( 'woocommerce_product_data_tabs', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_custom_product_tabs' );
                        // define the woocommerce_product_options_general_product_data callback
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_options_general_product_data_aux( $post_id )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir ;
                            $settings = [];
                            return $settings;
                        }
                        
                        // define the woocommerce_product_options_general_product_data callback
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_options_general_product_data()
                        {
                            global  $post ;
                            $post_id = $post->ID;
                            $product = wc_get_product( $post_id );
                            
                            if ( !$product ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "woocommerce_product_options_general_product_data({$post_id}) not a product" );
                                return;
                            }
                            
                            
                            if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
                                ?>
    <div class="options_group show_if_simple cryptocurrency-product-for-woocommerce-settings-wrapper show_if_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type show_if_variable_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type show_if_auction_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type">
        <h3 class="cryptocurrency-product-for-woocommerce-settings-header" style="display: block;"><?php 
                                _e( 'Cryptocurrency Product Settings', 'cryptocurrency-product-for-woocommerce' );
                                ?></h3>
        <div class="options_group show_if_simple"><p><?php 
                                echo  sprintf(
                                    __( '%1$sUpgrade Now!%2$s to enable "%3$s" feature.', 'cryptocurrency-product-for-woocommerce' ),
                                    '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">',
                                    '</a>',
                                    __( 'The dynamic price source', 'cryptocurrency-product-for-woocommerce' )
                                ) ;
                                ?>
        </p></div>
    </div>
    <?php 
                            }
                            
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_scripts_();
                        }
                        
                        // add the action
                        add_action( 'woocommerce_product_options_general_product_data', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_options_general_product_data' );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_display_options( $settings )
                        {
                            foreach ( $settings as $s ) {
                                switch ( $s['_cryptocurrency_product_setting_type'] ) {
                                    case 'text_input':
                                        woocommerce_wp_text_input( $s );
                                        break;
                                    case 'textarea_input':
                                        woocommerce_wp_textarea_input( $s );
                                        break;
                                    case 'checkbox':
                                        woocommerce_wp_checkbox( $s );
                                        break;
                                    case 'select':
                                        woocommerce_wp_select( $s );
                                        break;
                                    case 'hidden':
                                        woocommerce_wp_hidden_input( $s );
                                        break;
                                    default:
                                        throw new Exception( "Unknown _cryptocurrency_product_setting_type: " . $s['_cryptocurrency_product_setting_type'] );
                                }
                            }
                        }
                        
                        /**
                         * Contents of the cryptocurrency options product tab.
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options_product_tab_content( $object_id = null )
                        {
                            global  $post ;
                            $post_id = ( is_null( $object_id ) ? $post->ID : $object_id );
                            // CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options_product_tab_content($object_id): $post_id");
                            // Get the selected value
                            $_select_cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $post_id, '_select_cryptocurrency_option', true );
                            if ( empty($_select_cryptocurrency_option) ) {
                                $_select_cryptocurrency_option = '';
                            }
                            $options = [];
                            $options[''] = __( 'Select a value', 'woocommerce' );
                            // default value
                            if ( !CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_ether_product_type_disabled() ) {
                                $options['Ether'] = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainCurrencyTickerName();
                            }
                            $options = apply_filters( 'cryptocurrency_product_for_woocommerce_get_product_symbols', $options );
                            $s = pow( 10, -18 );
                            $settings = [
                                //        [
                                //            'id'			=> '_text_input_cryptocurrency_data',
                                //            'label'			=> __( 'Token address', 'cryptocurrency-product-for-woocommerce' ),
                                //            'desc_tip'		=> 'true',
                                //            'description'	=> __( 'The ethereum address of the Token to sell', 'cryptocurrency-product-for-woocommerce' ),
                                //            'type' 			=> 'text',
                                //            'wrapper_class' => 'hidden',
                                //            '_cryptocurrency_product_setting_type' => 'text_input',
                                //        ],
                                [
                                    'id'                                   => '_select_cryptocurrency_option',
                                    'label'                                => __( 'The cryptocurrency', 'cryptocurrency-product-for-woocommerce' ),
                                    'options'                              => $options,
                                    '_cryptocurrency_product_setting_type' => 'select',
                                ],
                                [
                                    'id'                                   => '_text_input_cryptocurrency_minimum_value',
                                    'label'                                => __( 'Minimum amount', 'cryptocurrency-product-for-woocommerce' ),
                                    'desc_tip'                             => 'true',
                                    'description'                          => __( 'The minimum amount of cryptocurrency user can buy', 'cryptocurrency-product-for-woocommerce' ),
                                    'wrapper_class'                        => '_text_input_cryptocurrency_minimum_value_field hidden',
                                    'custom_attributes'                    => [
                                    'min'        => 0,
                                    'step'       => $s,
                                    'novalidate' => 'novalidate',
                                    'type'       => 'number',
                                ],
                                    '_cryptocurrency_product_setting_type' => 'text_input',
                                ],
                                [
                                    'id'                                   => '_text_input_cryptocurrency_step',
                                    'label'                                => __( 'Step', 'cryptocurrency-product-for-woocommerce' ),
                                    'desc_tip'                             => 'true',
                                    'description'                          => __( 'The increment/decrement step', 'cryptocurrency-product-for-woocommerce' ),
                                    'wrapper_class'                        => '_text_input_cryptocurrency_step_field hidden',
                                    'custom_attributes'                    => [
                                    'min'        => $s,
                                    'step'       => $s,
                                    'novalidate' => 'novalidate',
                                    'type'       => 'number',
                                ],
                                    '_cryptocurrency_product_setting_type' => 'text_input',
                                ],
                                [
                                    'id'                                   => '_text_input_cryptocurrency_balance',
                                    'label'                                => __( 'Balance', 'cryptocurrency-product-for-woocommerce' ),
                                    'desc_tip'                             => 'true',
                                    'description'                          => __( 'The wallet balance', 'cryptocurrency-product-for-woocommerce' ),
                                    'wrapper_class'                        => '_text_input_cryptocurrency_balance_field hidden',
                                    'custom_attributes'                    => array(
                                    'disabled' => 'disabled',
                                ),
                                    '_cryptocurrency_product_setting_type' => 'text_input',
                                ],
                                // fix for save_post: https://stackoverflow.com/questions/5434219/problem-with-wordpress-save-post-action#comment6729746_5849143
                                [
                                    'id'                                   => '_text_input_cryptocurrency_flag',
                                    'value'                                => 'yes',
                                    '_cryptocurrency_product_setting_type' => 'hidden',
                                ],
                            ];
                            $settings = apply_filters(
                                'cryptocurrency_product_for_woocommerce_product_type_settings',
                                $settings,
                                $_select_cryptocurrency_option,
                                $post_id
                            );
                            return $settings;
                        }
                        
                        /**
                         * Contents of the cryptocurrency options product tab.
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options_product_tab_content_wc()
                        {
                            global  $post ;
                            global  $product ;
                            $post_id = ( $product ? $product->get_id() : $post->ID );
                            ?><div id="cryptocurrency_product_data" class="panel woocommerce_options_panel hidden"><?php 
                            ?><div class="options_group"><?php 
                            $settings = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options_product_tab_content( $post_id );
                            //            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("options_product_tab_content_wc($post_id): " . print_r($settings, true));
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_display_options( $settings );
                            ?></div>

	</div><?php 
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_scripts_();
                        }
                        
                        add_action( 'woocommerce_product_data_panels', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options_product_tab_content_wc' );
                        add_action(
                            'cryptocurrency_product_for_woocommerce_save_option_field',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_save_option_field_hook',
                            10,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_save_option_field_hook( $cryptocurrency_option, $post_id, $product )
                        {
                            if ( 'Ether' !== $cryptocurrency_option ) {
                                return;
                            }
                        }
                        
                        // @see https://wordpress.stackexchange.com/a/110052/137915
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_transition_post_status( $new_status, $old_status, $post )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            if ( !current_user_can( 'administrator' ) ) {
                                return;
                            }
                            if ( !($old_status != 'publish' && $new_status == 'publish' && !empty($post->ID) && in_array( $post->post_type, [ 'product' ] )) ) {
                                return;
                            }
                            $product_id = $post->ID;
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product_id ) ) {
                                return;
                            }
                            $vendor_id = get_post_field( 'post_author_override', $product_id );
                            if ( empty($vendor_id) ) {
                                $vendor_id = get_post_field( 'post_author', $product_id );
                            }
                            if ( !user_can( $vendor_id, 'vendor' ) ) {
                                // process only vendor's products
                                return;
                            }
                            $vendor_fee = 0;
                            if ( $vendor_fee <= 0 ) {
                                return;
                            }
                            // Ether rate
                            $rate = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_ETH_rate( 1 );
                            
                            if ( is_null( $rate ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( 'Failed to get Ether rate' );
                                return;
                            }
                            
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( 'Ether rate: ' . $rate );
                            $eth_value = $vendor_fee / $rate;
                            $eth_value_wei = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_double_int_multiply( $eth_value, pow( 10, 18 ) );
                            // 1. check balance
                            $thisWalletAddress = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress( $product_id );
                            $providerUrl = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getWeb3Endpoint();
                            try {
                                $eth_balance = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBalanceEth( $thisWalletAddress, $providerUrl );
                                
                                if ( null === $eth_balance || $eth_balance->compare( $eth_value_wei ) < 0 ) {
                                    $eth_balance_str = $eth_balance->toString();
                                    $eth_value_wei_str = $eth_value_wei->toString();
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to take vendor fee: insufficient Ether balance: eth_balance_wei({$eth_balance_str}) < eth_value_wei({$eth_value_wei_str})" );
                                    return;
                                }
                                
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_enqueue_send_ether_task(
                                    null,
                                    null,
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress(),
                                    $eth_value,
                                    $providerUrl,
                                    0,
                                    $vendor_id
                                );
                            } catch ( Exception $ex ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_transition_post_status: " . $ex->getMessage() );
                            }
                        }
                        
                        add_action(
                            'transition_post_status',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_transition_post_status',
                            10,
                            3
                        );
                        /**
                         * Save the custom fields.
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_save_option_field( $post_id, $product = null )
                        {
                            //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("save_option_field($post_id): " . print_r($_POST, true));
                            $product = ( is_null( $product ) ? wc_get_product( $post_id ) : $product );
                            
                            if ( !$product ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "save_option_field({$post_id}) not a product" );
                                return;
                            }
                            
                            // Fix for: Call to undefined method WP_Post::get_id()
                            if ( 'WP_Post' === get_class( $product ) ) {
                                $product = wc_get_product( $product->ID );
                            }
                            
                            if ( !$product ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "save_option_field({$post_id}) not a product" );
                                return;
                            }
                            
                            
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product->get_id() ) ) {
                                $is_cryptocurrency = ( isset( $_POST['_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type'] ) ? 'yes' : 'no' );
                                if ( $is_cryptocurrency == 'no' ) {
                                    return;
                                }
                            }
                            
                            // Select
                            $cryptocurrency_option = $_POST['_select_cryptocurrency_option'];
                            
                            if ( !empty($cryptocurrency_option) ) {
                                update_post_meta( $post_id, '_select_cryptocurrency_option', esc_attr( $cryptocurrency_option ) );
                            } else {
                                update_post_meta( $post_id, '_select_cryptocurrency_option', '' );
                            }
                            
                            //	if ( isset( $_POST['_text_input_cryptocurrency_data'] ) ) {
                            //		update_post_meta( $post_id, '_text_input_cryptocurrency_data', sanitize_text_field( $_POST['_text_input_cryptocurrency_data'] ) );
                            //    }
                            if ( isset( $_POST['_text_input_cryptocurrency_minimum_value'] ) ) {
                                update_post_meta( $post_id, '_text_input_cryptocurrency_minimum_value', sanitize_text_field( $_POST['_text_input_cryptocurrency_minimum_value'] ) );
                            }
                            if ( isset( $_POST['_text_input_cryptocurrency_step'] ) ) {
                                update_post_meta( $post_id, '_text_input_cryptocurrency_step', sanitize_text_field( $_POST['_text_input_cryptocurrency_step'] ) );
                            }
                            do_action(
                                'cryptocurrency_product_for_woocommerce_save_option_field',
                                $cryptocurrency_option,
                                $post_id,
                                $product
                            );
                        }
                        
                        add_action(
                            'woocommerce_process_product_meta',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_save_option_field',
                            10,
                            2
                        );
                        add_action(
                            'woocommerce_process_product_meta_variable',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_save_option_field',
                            10,
                            2
                        );
                        add_action(
                            'woocommerce_process_product_meta_auction',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_save_option_field',
                            10,
                            2
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_after_product_object_save( $product, $data_store )
                        {
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product->get_id() ) ) {
                                return;
                            }
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product->get_id(), '_select_cryptocurrency_option', true );
                            do_action(
                                'cryptocurrency_product_for_woocommerce_woocommerce_after_product_object_save',
                                $cryptocurrency_option,
                                $product,
                                $data_store
                            );
                        }
                        
                        add_action(
                            'woocommerce_after_product_object_save',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_after_product_object_save',
                            10,
                            2
                        );
                        // define the woocommerce_save_product_variation callback
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_save_product_variation( $variation_id, $i )
                        {
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_save_product_variation( {$variation_id}, {$i} )" );
                            $variation = wc_get_product_object( 'variation', $variation_id );
                            
                            if ( !$variation ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to get product object for variation_id: ", $variation_id );
                                return;
                            }
                            
                            //    $variation = wc_get_product($variation_id);
                            $product = wc_get_product( $variation->get_parent_id() );
                            
                            if ( !$product ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to get parent product object for variation_id: ", $variation_id );
                                return;
                            }
                            
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product->get_id(), '_select_cryptocurrency_option', true );
                            do_action(
                                'cryptocurrency_product_for_woocommerce_save_option_field',
                                $cryptocurrency_option,
                                $variation->get_id(),
                                $variation
                            );
                        }
                        
                        // add the action
                        add_action(
                            'woocommerce_save_product_variation',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_save_product_variation',
                            10,
                            2
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_default_gas_price_wei()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $gasPriceMaxGwei = doubleval( ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['gas_price'] ) ? $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['gas_price'] : '21' ) );
                            return array(
                                'tm'            => time(),
                                'gas_price'     => intval( floatval( $gasPriceMaxGwei ) * 1000000000 ),
                                'gas_price_tip' => null,
                            );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_web3_gas_price_wei()
                        {
                            $providerUrl = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getWeb3Endpoint();
                            try {
                                $requestManager = new \Web3\RequestManagers\HttpRequestManager( $providerUrl, 10 );
                                $web3 = new \Web3\Web3( new \Web3\Providers\HttpProvider( $requestManager ) );
                                $eth = $web3->eth;
                                $ret = null;
                                $eth->gasPrice( function ( $err, $gasPrice ) use( &$ret ) {
                                    
                                    if ( $err !== null ) {
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to get gasPrice: ", $err );
                                        return;
                                    }
                                    
                                    $ret = $gasPrice;
                                } );
                                if ( is_null( $ret ) ) {
                                    return null;
                                }
                                return $ret->toString();
                            } catch ( Exception $ex ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_web3_gas_price_wei: " . $ex->getMessage() );
                            }
                            return 0;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_gas_price_wei()
                        {
                            $gasPriceWei = null;
                            $gasPriceTipWei = null;
                            $providerUrl = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getWeb3Endpoint();
                            try {
                                $requestManager = new \Web3\RequestManagers\HttpRequestManager( $providerUrl, 10 );
                                $web3 = new \Web3\Web3( new \Web3\Providers\HttpProvider( $requestManager ) );
                                $eth = $web3->eth;
                                $isEIP1559 = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_isEIP1559( $eth );
                                
                                if ( !$isEIP1559 ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_gas_price_wei: !isEIP1559" );
                                    $gasPriceWei = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_web3_gas_price_wei();
                                } else {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_gas_price_wei: isEIP1559" );
                                    list( $error, $block ) = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getLatestBlock( $eth );
                                    
                                    if ( !is_null( $error ) ) {
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_gas_price_wei: Failed to get block: " . $error );
                                        return CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_default_gas_price_wei();
                                    }
                                    
                                    $gasPriceTipWei = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_web3_gas_price_wei();
                                    $gasPriceWei = ( new phpseclib3\Math\BigInteger( $block->baseFeePerGas, 16 ) )->multiply( new phpseclib3\Math\BigInteger( 2 ) )->add( new phpseclib3\Math\BigInteger( $gasPriceTipWei ) );
                                    $gasPriceWei = $gasPriceWei->toString();
                                    
                                    if ( '0' === $gasPriceWei ) {
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_gas_price_wei: 0 === gasPriceWei: " . $block->baseFeePerGas . ', bn=' . ( new phpseclib3\Math\BigInteger( $block->baseFeePerGas ) )->toString() . '; block=' . print_r( $block, true ) );
                                        return CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_default_gas_price_wei();
                                    }
                                
                                }
                            
                            } catch ( Exception $ex ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_gas_price_wei: " . $ex->getMessage() );
                                return CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_default_gas_price_wei();
                            }
                            
                            if ( is_null( $gasPriceWei ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_gas_price_wei: is_null(gasPriceWei)" );
                                return CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_default_gas_price_wei();
                            }
                            
                            $cache_gas_price = array(
                                'tm'            => time(),
                                'gas_price'     => $gasPriceWei,
                                'gas_price_tip' => $gasPriceTipWei,
                            );
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_default_gas_price_wei();
                            }
                            
                            $option_name = 'ethereumicoio_cache_gas_price-wei-' . $chainId;
                            
                            if ( get_option( $option_name ) ) {
                                update_option( $option_name, $cache_gas_price );
                            } else {
                                $deprecated = '';
                                $autoload = 'no';
                                add_option(
                                    $option_name,
                                    $cache_gas_price,
                                    $deprecated,
                                    $autoload
                                );
                            }
                            
                            return $cache_gas_price;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_gas_price_wei()
                        {
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $option_name = 'ethereumicoio_cache_gas_price-wei-' . $chainId;
                            $cache_gas_price_wei = get_option( $option_name, array() );
                            if ( !$cache_gas_price_wei ) {
                                $cache_gas_price_wei = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_gas_price_wei();
                            }
                            $tm_diff = time() - intval( $cache_gas_price_wei['tm'] );
                            // TODO: admin setting
                            $timeout = 10 * 60;
                            // seconds
                            if ( $tm_diff > $timeout ) {
                                $cache_gas_price_wei = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_gas_price_wei();
                            }
                            $gasPriceWei = doubleval( $cache_gas_price_wei['gas_price'] );
                            
                            if ( is_null( $cache_gas_price_wei['gas_price_tip'] ) ) {
                                // only if pre-EIP1559
                                $gasPriceMaxWei = doubleval( CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_default_gas_price_wei()['gas_price'] );
                                if ( $gasPriceMaxWei < $gasPriceWei ) {
                                    $gasPriceWei = $gasPriceMaxWei;
                                }
                            }
                            
                            return intval( $gasPriceWei );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_gas_price_tip_wei()
                        {
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $option_name = 'ethereumicoio_cache_gas_price-wei-' . $chainId;
                            $cache_gas_price_wei = get_option( $option_name, array() );
                            
                            if ( !$cache_gas_price_wei ) {
                                $cache_gas_price_wei = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_gas_price_wei();
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( '!$cache_gas_price_wei: ' . print_r( $cache_gas_price_wei, true ) );
                            }
                            
                            $tm_diff = time() - intval( $cache_gas_price_wei['tm'] );
                            // TODO: admin setting
                            $timeout = 10 * 60;
                            // seconds
                            
                            if ( $tm_diff > $timeout ) {
                                $cache_gas_price_wei = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_query_gas_price_wei();
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( '$tm_diff > $timeout: ' . print_r( $cache_gas_price_wei, true ) );
                            }
                            
                            
                            if ( is_null( $cache_gas_price_wei['gas_price_tip'] ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( 'is_null($cache_gas_price_wei[\'gas_price_tip\']): ' . print_r( $cache_gas_price_wei, true ) );
                                return null;
                            }
                            
                            $gasPriceTipWei = doubleval( $cache_gas_price_wei['gas_price_tip'] );
                            
                            if ( !is_null( CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_default_gas_price_wei()['gas_price'] ) ) {
                                $gasPriceTipMaxWei = doubleval( CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_default_gas_price_wei()['gas_price'] );
                                if ( $gasPriceTipMaxWei < $gasPriceTipWei ) {
                                    $gasPriceTipWei = $gasPriceTipMaxWei;
                                }
                            }
                            
                            return intval( $gasPriceTipWei );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_isEIP1559( $eth = null )
                        {
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $option_name = 'ethereumicoio_cache_is-eip-1559-network-' . $chainId;
                            // delete_option($option_name);
                            $isEIP1559Option = get_option( $option_name, null );
                            $tm_diff = time() - intval( ( !is_null( $isEIP1559Option ) ? $isEIP1559Option['tm'] : time() ) );
                            // TODO: admin setting
                            $timeout = 10 * 60;
                            // seconds
                            // list($error, $block) = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getLatestBlock($eth);
                            $isEIP1559 = ( !is_null( $isEIP1559Option ) ? $isEIP1559Option['isEIP1559'] : null );
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( $option_name . ' : isEIP1559=' . (( is_null( $isEIP1559 ) ? 'null' : (( $isEIP1559 ? 'true' : 'false' )) )) );
                            
                            if ( is_null( $isEIP1559 ) || $tm_diff > $timeout ) {
                                list( $error, $block ) = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getLatestBlock( $eth );
                                
                                if ( !is_null( $error ) ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to get block: " . $error );
                                    return null;
                                }
                                
                                $isEIP1559 = property_exists( $block, 'baseFeePerGas' );
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_isEIP1559: isEIP1559=' . (( is_null( $isEIP1559 ) ? 'null' : (( $isEIP1559 ? 'true' : 'false' )) )) );
                                // isset($block['baseFeePerGas']);
                                
                                if ( get_option( $option_name ) ) {
                                    update_option( $option_name, [
                                        'isEIP1559' => $isEIP1559,
                                        'tm'        => time(),
                                    ] );
                                } else {
                                    $deprecated = '';
                                    $autoload = 'no';
                                    add_option(
                                        $option_name,
                                        [
                                        'isEIP1559' => $isEIP1559,
                                        'tm'        => time(),
                                    ],
                                        $deprecated,
                                        $autoload
                                    );
                                }
                                
                                return $isEIP1559;
                            }
                            
                            return $isEIP1559;
                        }
                        
                        /**
                         * Hide Attributes data panel.
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_hide_attributes_data_panel( $tabs )
                        {
                            $tabs['shipping']['class'][] = 'hide_if_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type';
                            $tabs['shipping']['class'][] = 'hide_if_variable_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type';
                            $tabs['shipping']['class'][] = 'hide_if_auction_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type';
                            return $tabs;
                        }
                        
                        add_filter( 'woocommerce_product_data_tabs', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_hide_attributes_data_panel' );
                        //----------------------------------------------------------------------------//
                        //                     Shipping field for crypto-address                      //
                        //----------------------------------------------------------------------------//
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency_product_in_cart()
                        {
                            if ( !is_null( WC()->cart ) ) {
                                // Find each product in the cart and add it to the $cart_ids array
                                foreach ( WC()->cart->get_cart() as $cart_item_key => $product ) {
                                    if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product['product_id'] ) ) {
                                        return true;
                                    }
                                }
                            }
                            return false;
                        }
                        
                        // The type of crypto product in a cart
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_type_in_cart()
                        {
                            if ( !is_null( WC()->cart ) ) {
                                // Find each product in the cart and add it to the $cart_ids array
                                foreach ( WC()->cart->get_cart() as $cart_item_key => $product ) {
                                    
                                    if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product['product_id'] ) ) {
                                        $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product['product_id'], '_select_cryptocurrency_option', true );
                                        if ( empty($cryptocurrency_option) ) {
                                            $cryptocurrency_option = '';
                                        }
                                        return $cryptocurrency_option;
                                    }
                                
                                }
                            }
                            return '';
                        }
                        
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_id_in_cart()
                        {
                            if ( !is_null( WC()->cart ) ) {
                                // Find each product in the cart and add it to the $cart_ids array
                                foreach ( WC()->cart->get_cart() as $cart_item_key => $product ) {
                                    if ( _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product['product_id'] ) ) {
                                        return $product['product_id'];
                                    }
                                }
                            }
                            return '';
                        }
                        
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_type_in_order( $order_id )
                        {
                            // do the payment
                            $order = wc_get_order( $order_id );
                            if ( !$order ) {
                                return '';
                            }
                            $order_items = $order->get_items();
                            foreach ( $order_items as $product ) {
                                $product_id = $product['product_id'];
                                if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product_id ) ) {
                                    // skip non-cryptocurrency products
                                    continue;
                                }
                                $_product = wc_get_product( $product_id );
                                
                                if ( !$_product ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_type_in_order({$product_id}) not a product" );
                                    continue;
                                }
                                
                                $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, '_select_cryptocurrency_option', true );
                                return $cryptocurrency_option;
                            }
                            return '';
                        }
                        
                        // Hook in
                        add_filter( 'woocommerce_checkout_fields', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_override_checkout_fields' );
                        // Our hooked in function - $fields is passed via the filter!
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_override_checkout_fields( $fields )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency_product_in_cart() ) {
                                return $fields;
                            }
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_type_in_cart();
                            return apply_filters( 'cryptocurrency_product_for_woocommerce_override_checkout_fields', $fields, $cryptocurrency_option );
                        }
                        
                        add_filter(
                            'cryptocurrency_product_for_woocommerce_override_checkout_fields',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_override_checkout_fields',
                            20,
                            2
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_override_checkout_fields( $fields, $cryptocurrency_option )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            if ( "ETHEREUM" !== apply_filters( 'cryptocurrency_product_for_woocommerce_get_base_blockchain', '', $cryptocurrency_option ) ) {
                                return $fields;
                            }
                            $user_id = get_current_user_id();
                            $walletDisabled = ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_field_disable'] ) ? esc_attr( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_field_disable'] ) : '' );
                            $isWalletFieldDisabled = !empty($walletDisabled);
                            $custom_attributes = array();
                            if ( !wp_doing_ajax() && !($user_id > 0 || $isWalletFieldDisabled || !function_exists( 'ETHEREUM_WALLET_create_account' ) || !WC()->checkout()->is_registration_required()) ) {
                                ?>
<script type='text/javascript'>
    jQuery( document ).ready( function() {
        setTimeout(function(){
            jQuery('#billing_cryptocurrency_ethereum_address_field').hide();
        }, 100);
    });
</script>
    <?php 
                            }
                            $value = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_user_wallet();
                            if ( !empty($walletDisabled) ) {
                                $custom_attributes['readonly'] = 'readonly';
                            }
                            
                            if ( !wp_doing_ajax() ) {
                                ?>
<script type='text/javascript'>
    if (typeof jQuery === 'undefined') {
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function(){
                document.getElementById('billing_cryptocurrency_ethereum_address').value = "<?php 
                                echo  $value ;
                                ?>";
            }, 100);
        });
    } else {
        jQuery( document ).ready( function() {
            setTimeout(function(){
                jQuery('#billing_cryptocurrency_ethereum_address').val("<?php 
                                echo  $value ;
                                ?>");
            }, 100);
        });
    }
</script>
    <?php 
                            }
                            
                            $fields['billing']['billing_cryptocurrency_ethereum_address'] = array(
                                'label'             => CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_geEthereumAddressFieldLabel(),
                                'placeholder'       => _x( '0x', 'placeholder', 'cryptocurrency-product-for-woocommerce' ),
                                'required'          => $user_id > 0 || $isWalletFieldDisabled || !function_exists( 'ETHEREUM_WALLET_create_account' ) || !WC()->checkout()->is_registration_required(),
                                'class'             => array( 'form-row-wide' ),
                                'clear'             => true,
                                'value'             => $value,
                                'custom_attributes' => $custom_attributes,
                            );
                            return $fields;
                        }
                        
                        /**
                         * Display field value on the order edit page
                         */
                        //add_action( 'woocommerce_admin_order_data_after_billing_address', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_checkout_field_display_admin_order_meta', 10, 1 );
                        //
                        //function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_checkout_field_display_admin_order_meta($order){
                        //    echo '<p><strong>'.__('Ethereum Address From Checkout Form', 'cryptocurrency-product-for-woocommerce').':</strong> ' . _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $order->get_id(), '_billing_cryptocurrency_ethereum_address', true ) . '</p>';
                        //}
                        /* Display additional billing fields (email, phone) in ADMIN area (i.e. Order display ) */
                        /* Note:  $fields keys (i.e. field names) must be in format:  WITHOUT the "billing_" prefix (it's added by the code) */
                        add_filter( 'woocommerce_admin_billing_fields', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_additional_admin_billing_fields' );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_additional_admin_billing_fields( $fields )
                        {
                            $fields['cryptocurrency_ethereum_address'] = array(
                                'label' => CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_geEthereumAddressFieldLabel(),
                            );
                            return $fields;
                        }
                        
                        /* Display additional billing fields (email, phone) in USER area (i.e. Admin User/Customer display ) */
                        /* Note:  $fields keys (i.e. field names) must be in format: billing_ */
                        add_filter( 'woocommerce_customer_meta_fields', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_additional_customer_meta_fields' );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_additional_customer_meta_fields( $fields )
                        {
                            $fields['billing']['fields']['billing_cryptocurrency_ethereum_address'] = array(
                                'label'       => CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_geEthereumAddressFieldLabel(),
                                'description' => '',
                            );
                            return $fields;
                        }
                        
                        add_filter(
                            'cryptocurrency_product_for_woocommerce_get_order_txhash',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_get_order_txhash',
                            10,
                            4
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_get_order_txhash(
                            $fields,
                            $cryptocurrency_option,
                            $order_id,
                            $product_id
                        )
                        {
                            $txhash = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $order_id, 'ether_txhash_' . $product_id, true );
                            if ( !empty($txhash) ) {
                                $fields['cryptocurrency_ether_txhash_' . $product_id] = array(
                                    'label' => __( 'Crypto Tx Hash', 'cryptocurrency-product-for-woocommerce' ),
                                    'value' => $txhash,
                                );
                            }
                            return $fields;
                        }
                        
                        // @see https://stackoverflow.com/a/41987077/4256005
                        add_filter(
                            'woocommerce_email_customer_details_fields',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_email_customer_details_fields',
                            20,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_email_customer_details_fields( $fields, $sent_to_admin = false, $order = null )
                        {
                            
                            if ( is_null( $order ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( 'woocommerce_email_customer_details_fields order is null' );
                                return $fields;
                            }
                            
                            $order_id = $order->get_id();
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_type_in_order( $order_id );
                            if ( empty($cryptocurrency_option) ) {
                                // CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_email_customer_details_fields empty($cryptocurrency_option) for order id " . $order_id);
                                return $fields;
                            }
                            $_billing_cryptocurrency_address = apply_filters(
                                'cryptocurrency_product_for_woocommerce_get_cryptocurrency_address',
                                '',
                                $cryptocurrency_option,
                                $order_id
                            );
                            $fields['billing_cryptocurrency_address'] = array(
                                'label' => __( 'Crypto Wallet Address', 'cryptocurrency-product-for-woocommerce' ),
                                'value' => $_billing_cryptocurrency_address,
                            );
                            $order_items = $order->get_items();
                            foreach ( $order_items as $item ) {
                                $product_id = $item['product_id'];
                                if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product_id ) ) {
                                    // skip non-cryptocurrency products
                                    continue;
                                }
                                $fields = apply_filters(
                                    'cryptocurrency_product_for_woocommerce_get_order_txhash',
                                    $fields,
                                    $cryptocurrency_option,
                                    $order_id,
                                    $product_id
                                );
                            }
                            return $fields;
                        }
                        
                        /* Add CSS for ADMIN area so that the additional billing fields (email, phone) display on left and right side of edit billing details */
                        //add_action('admin_head', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_custom_admin_css');
                        //function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_custom_admin_css() {
                        //  echo '<style>
                        //    #order_data .order_data_column ._billing_email2_field {
                        //        clear: left;
                        //        float: left;
                        //    }
                        //    #order_data .order_data_column ._billing_phone_field {
                        //        float: right;
                        //    }
                        //  </style>';
                        //}
                        // @see https://stackoverflow.com/a/37780501/4256005
                        // Adding Meta container admin shop_order pages
                        add_action( 'add_meta_boxes', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_add_meta_boxes' );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_add_meta_boxes()
                        {
                            global  $post ;
                            if ( 'shop_order' !== get_post_type( $post ) ) {
                                return;
                            }
                            $order_id = $post->ID;
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_type_in_order( $order_id );
                            if ( empty($cryptocurrency_option) ) {
                                return;
                            }
                            do_action( 'cryptocurrency_product_for_woocommerce_add_meta_boxes', $cryptocurrency_option );
                        }
                        
                        // Adding Meta container admin shop_order pages
                        add_action(
                            'cryptocurrency_product_for_woocommerce_add_meta_boxes',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_add_meta_boxes',
                            20,
                            1
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_add_meta_boxes( $cryptocurrency_option )
                        {
                            if ( "ETHEREUM" !== apply_filters( 'cryptocurrency_product_for_woocommerce_get_base_blockchain', '', $cryptocurrency_option ) ) {
                                return;
                            }
                            add_meta_box(
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_other_fields',
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_geEthereumAddressFieldLabel(),
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_add_other_fields_for_packaging',
                                'shop_order',
                                'side',
                                'core'
                            );
                        }
                        
                        // Adding Meta field in the meta container admin shop_order pages
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_add_other_fields_for_packaging()
                        {
                            global  $post ;
                            if ( 'shop_order' !== get_post_type( $post ) ) {
                                return;
                            }
                            $order_id = $post->ID;
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_type_in_order( $order_id );
                            $_billing_cryptocurrency_ethereum_address = apply_filters(
                                'cryptocurrency_product_for_woocommerce_get_cryptocurrency_address',
                                '',
                                $cryptocurrency_option,
                                $order_id
                            );
                            $meta_field_data = $_billing_cryptocurrency_ethereum_address;
                            echo  '<input type="hidden" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_' . $cryptocurrency_option . '_other_meta_field_nonce" value="' . wp_create_nonce() . '">
    <p style="border-bottom:solid 1px #eee;padding-bottom:13px;">
        <input type="text" style="width:250px;";" name="_billing_cryptocurrency_ethereum_address_input" placeholder="' . $meta_field_data . '" value="' . $meta_field_data . '"></p>' ;
                        }
                        
                        // Save the data of the Meta field
                        add_action(
                            'save_post',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_save_wc_order_other_fields',
                            1000,
                            1
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_save_wc_order_other_fields( $post_id )
                        {
                            // We need to verify this with the proper authorization (security stuff).
                            if ( 'shop_order' !== get_post_type( $post_id ) ) {
                                return;
                            }
                            // Check the user's permissions.
                            if ( !current_user_can( 'edit_shop_order', $post_id ) ) {
                                return $post_id;
                            }
                            $order_id = $post_id;
                            $order = wc_get_order( $order_id );
                            if ( !$order ) {
                                return $post_id;
                            }
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_type_in_order( $order_id );
                            // Check if our nonce is set.
                            if ( !isset( $_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_' . $cryptocurrency_option . '_other_meta_field_nonce'] ) ) {
                                return $post_id;
                            }
                            $nonce = $_REQUEST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_' . $cryptocurrency_option . '_other_meta_field_nonce'];
                            //Verify that the nonce is valid.
                            
                            if ( !wp_verify_nonce( $nonce ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "save_wc_order_other_fields: bad nonce" );
                                return $post_id;
                            }
                            
                            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
                            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                                return $post_id;
                            }
                            // --- Its safe for us to save the data ! --- //
                            do_action( 'cryptocurrency_product_for_woocommerce_save_wc_order_other_fields', $cryptocurrency_option, $post_id );
                        }
                        
                        add_action(
                            'cryptocurrency_product_for_woocommerce_save_wc_order_other_fields',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_save_wc_order_other_fields_hook',
                            10,
                            2
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_save_wc_order_other_fields_hook( $cryptocurrency_option, $post_id )
                        {
                            if ( "ETHEREUM" !== apply_filters( 'cryptocurrency_product_for_woocommerce_get_base_blockchain', '', $cryptocurrency_option ) ) {
                                return;
                            }
                            // Sanitize user input  and update the meta field in the database.
                            $_billing_cryptocurrency_ethereum_address_input = sanitize_text_field( $_POST['_billing_cryptocurrency_ethereum_address_input'] );
                            $order_id = $post_id;
                            $order = wc_get_order( $order_id );
                            if ( !$order ) {
                                return;
                            }
                            
                            if ( empty($_billing_cryptocurrency_ethereum_address_input) ) {
                                // @see https://stackoverflow.com/a/43815280/4256005
                                // Get an instance of the WC_Order object
                                // Get the user ID from WC_Order methods
                                $user_id = $order->get_user_id();
                                // or $order->get_customer_id();
                                if ( $user_id >= 0 ) {
                                    $_billing_cryptocurrency_ethereum_address_input = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                                }
                            }
                            
                            $_billing_cryptocurrency_ethereum_address_input_old = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $post_id, '_billing_cryptocurrency_ethereum_address', true );
                            
                            if ( $_billing_cryptocurrency_ethereum_address_input_old != $_billing_cryptocurrency_ethereum_address_input ) {
                                update_post_meta( $post_id, '_billing_cryptocurrency_ethereum_address', $_billing_cryptocurrency_ethereum_address_input );
                                // Is this a note for the customer?
                                $is_customer_note = 1;
                                $order->add_order_note( sprintf( __( '%1$s set to %2$s', 'cryptocurrency-product-for-woocommerce' ), CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_geEthereumAddressFieldLabel(), $_billing_cryptocurrency_ethereum_address_input ), $is_customer_note );
                            }
                        
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_checkout_update_order_meta( $order_id, $data )
                        {
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency_product_in_cart() ) {
                                return;
                            }
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_type_in_cart();
                            do_action(
                                'cryptocurrency_product_for_woocommerce_checkout_update_order_meta',
                                $cryptocurrency_option,
                                $order_id,
                                $data
                            );
                        }
                        
                        add_action(
                            'woocommerce_checkout_update_order_meta',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_checkout_update_order_meta',
                            20,
                            2
                        );
                        add_action(
                            'cryptocurrency_product_for_woocommerce_checkout_update_order_meta',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_checkout_update_order_meta_hook',
                            10,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_checkout_update_order_meta_hook( $cryptocurrency_option, $order_id, $data )
                        {
                            if ( "ETHEREUM" !== apply_filters( 'cryptocurrency_product_for_woocommerce_get_base_blockchain', '', $cryptocurrency_option ) ) {
                                return;
                            }
                            if ( isset( $data['billing_cryptocurrency_ethereum_address'] ) ) {
                                update_post_meta( $order_id, '_billing_cryptocurrency_ethereum_address', $data['billing_cryptocurrency_ethereum_address'] );
                            }
                        }
                        
                        add_filter(
                            'cryptocurrency_product_for_woocommerce_get_base_blockchain',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_get_base_blockchain',
                            20,
                            2
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_get_base_blockchain( $blockchain, $cryptocurrency_option )
                        {
                            if ( !in_array( $cryptocurrency_option, [ 'Ether' ] ) ) {
                                return $blockchain;
                            }
                            return "ETHEREUM";
                        }
                        
                        //----------------------------------------------------------------------------//
                        //                     Process order status changes                           //
                        //----------------------------------------------------------------------------//
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_processing( $order_id )
                        {
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_order( $order_id );
                        }
                        
                        add_action( 'woocommerce_order_status_processing', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_processing' );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_completed( $order_id )
                        {
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_order( $order_id );
                        }
                        
                        add_action( 'woocommerce_order_status_completed', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_completed' );
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_double_int_multiply( $dval, $ival )
                        {
                            $dval = doubleval( $dval );
                            $ival = intval( $ival );
                            $dv1 = floor( $dval );
                            $ret = new phpseclib3\Math\BigInteger( intval( $dv1 ) );
                            $ret = $ret->multiply( new phpseclib3\Math\BigInteger( $ival ) );
                            if ( $dv1 === $dval ) {
                                return $ret;
                            }
                            $dv2 = $dval - $dv1;
                            $iv1 = intval( $dv2 * $ival );
                            $ret = $ret->add( new phpseclib3\Math\BigInteger( $iv1 ) );
                            return $ret;
                        }
                        
                        add_action(
                            "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_send_ether",
                            "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_send_ether",
                            0,
                            6
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_send_ether(
                            $order_id,
                            $product_id,
                            $marketAddress,
                            $eth_value,
                            $providerUrl,
                            $from_user_id
                        )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $order = wc_get_order( $order_id );
                            
                            if ( !$order ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Order {$order_id} was not found. Skip payment." );
                                return;
                            }
                            
                            
                            if ( !$order->has_status( "processing" ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Order {$order_id} status is not eligible for processing. Skip payment." );
                                return;
                            }
                            
                            
                            if ( 0 == $order->get_total() ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Order {$order_id} has zero total value paid and is not eligible for processing. Skip payment." );
                                return;
                            }
                            
                            if ( !is_null( $order_id ) ) {
                                
                                if ( CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_is_order_payed( $order_id, $product_id ) ) {
                                    // already payed
                                    $txhash = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $order_id, 'ether_txhash_' . $product_id, true );
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Ether payment found for order {$order_id}, product {$product_id}: {$txhash}. Skip payment." );
                                    return;
                                }
                            
                            }
                            // проверить, есть ли прошлая транзакция
                            $thisWalletAddress = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress( $product_id, $from_user_id );
                            
                            if ( is_null( $thisWalletAddress ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum wallet address", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return false;
                            }
                            
                            $lasttxhash = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_last_txhash( $thisWalletAddress );
                            $txhash = null;
                            $nonce = null;
                            $canceled = false;
                            try {
                                
                                if ( $lasttxhash ) {
                                    // если есть, проверить, завершена ли она
                                    $lastnonce = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_last_nonce( $thisWalletAddress );
                                    $tx_confirmed = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_tx_confirmed(
                                        $lasttxhash,
                                        $lastnonce,
                                        $providerUrl,
                                        $product_id,
                                        $from_user_id
                                    );
                                    
                                    if ( $tx_confirmed ) {
                                        //   - да, послать новую транзакцию
                                        list( $txhash, $nonce, $canceled ) = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_send_ether_impl(
                                            $order_id,
                                            $product_id,
                                            $marketAddress,
                                            $eth_value,
                                            $providerUrl,
                                            $from_user_id
                                        );
                                    } else {
                                        
                                        if ( is_null( $tx_confirmed ) ) {
                                            // nonce in last tx is outdated. remove it
                                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_delete_last_txhash( $thisWalletAddress );
                                            //            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_cancel_complete_order_task($order_id, $txhash, $nonce);
                                        }
                                    
                                    }
                                
                                } else {
                                    // нет последней транзакции. послать новую транзакцию
                                    list( $txhash, $nonce, $canceled ) = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_send_ether_impl(
                                        $order_id,
                                        $product_id,
                                        $marketAddress,
                                        $eth_value,
                                        $providerUrl,
                                        $from_user_id
                                    );
                                }
                                
                                
                                if ( $txhash ) {
                                    // успех - запомнить новую последнюю транзакцию
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_set_last_txhash( $thisWalletAddress, $txhash, $nonce );
                                    // поставить задачу отслеживания состояния транзакции
                                    if ( !is_null( $order_id ) ) {
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_complete_order_task( $order_id, $product_id );
                                    }
                                } else {
                                    if ( !$canceled ) {
                                        // Неуспех - поставить себя снова в очередь
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_enqueue_send_ether_task(
                                            $order_id,
                                            $product_id,
                                            $marketAddress,
                                            $eth_value,
                                            $providerUrl,
                                            1 * 60,
                                            $from_user_id
                                        );
                                    }
                                }
                            
                            } catch ( Exception $ex ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_send_ether: " . $ex->getMessage() );
                            }
                            return true;
                        }
                        
                        // Takes a hex (string) address as input
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_checksum_encode( $addr_str )
                        {
                            $out = array();
                            $addr = str_replace( '0x', '', strtolower( $addr_str ) );
                            $addr_array = str_split( $addr );
                            $hash_addr = \kornrunner\Keccak::hash( $addr, 256 );
                            $hash_addr_array = str_split( $hash_addr );
                            for ( $idx = 0 ;  $idx < count( $addr_array ) ;  $idx++ ) {
                                $ch = $addr_array[$idx];
                                
                                if ( (int) hexdec( $hash_addr_array[$idx] ) >= 8 ) {
                                    $out[] = strtoupper( $ch ) . '';
                                } else {
                                    $out[] = $ch . '';
                                }
                            
                            }
                            return '0x' . implode( '', $out );
                        }
                        
                        // create Ethereum wallet on user register
                        // see https://wp-kama.ru/hook/user_register
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_address_from_key( $privateKeyHex )
                        {
                            $privateKeyFactory = new \BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory();
                            $privateKey = $privateKeyFactory->fromHexUncompressed( $privateKeyHex );
                            $pubKeyHex = $privateKey->getPublicKey()->getHex();
                            $hash = \kornrunner\Keccak::hash( substr( hex2bin( $pubKeyHex ), 1 ), 256 );
                            $ethAddress = '0x' . substr( $hash, 24 );
                            $ethAddressChkSum = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_checksum_encode( $ethAddress );
                            return $ethAddressChkSum;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_create_account()
                        {
                            $random = new \BitWasp\Bitcoin\Crypto\Random\Random();
                            $privateKeyBuffer = $random->bytes( 32 );
                            $privateKeyHex = $privateKeyBuffer->getHex();
                            $ethAddressChkSum = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_address_from_key( $privateKeyHex );
                            return [ $ethAddressChkSum, $privateKeyHex ];
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_send_ether_impl(
                            $order_id,
                            $product_id,
                            $marketAddress,
                            $eth_value,
                            $providerUrl,
                            $from_user_id
                        )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $thisWalletAddress = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress( $product_id, $from_user_id );
                            
                            if ( is_null( $thisWalletAddress ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum wallet address", 'cryptocurrency-product-for-woocommerce' ) ) );
                                if ( !is_null( $order_id ) ) {
                                    update_post_meta( $order_id, 'status', __( 'Configuration error', 'cryptocurrency-product-for-woocommerce' ) );
                                }
                                return null;
                            }
                            
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                if ( !is_null( $order_id ) ) {
                                    update_post_meta( $order_id, 'status', __( 'Configuration error', 'cryptocurrency-product-for-woocommerce' ) );
                                }
                                return null;
                            }
                            
                            $eth_value_wei = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_double_int_multiply( $eth_value, pow( 10, 18 ) );
                            $eth_value_wei_str = $eth_value_wei->toString();
                            // 1. check balance
                            $blockchainNetwork = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainNetwork();
                            $requestManager = new \Web3\RequestManagers\HttpRequestManager( $providerUrl, 10 );
                            $web3 = new \Web3\Web3( new \Web3\Providers\HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $eth_balance = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBalanceEth( $thisWalletAddress, $providerUrl, $eth );
                            
                            if ( null === $eth_balance ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "eth_balance is null" );
                                if ( !is_null( $order_id ) ) {
                                    update_post_meta( $order_id, 'status', __( 'Network error', 'cryptocurrency-product-for-woocommerce' ) );
                                }
                                return null;
                            }
                            
                            
                            if ( $eth_balance->compare( $eth_value_wei ) < 0 ) {
                                $eth_balance_str = $eth_balance->toString();
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "eth_balance_wei({$eth_balance_str}) < eth_value_wei({$eth_value_wei_str}) for {$order_id}" );
                                
                                if ( !is_null( $order_id ) ) {
                                    update_post_meta( $order_id, 'status', __( 'Insufficient funds', 'cryptocurrency-product-for-woocommerce' ) );
                                    // Load the order.
                                    $order = wc_get_order( $order_id );
                                    // Place the order to failed.
                                    $res = $order->update_status( 'failed', sprintf(
                                        __( '%3$s balance (%1$s wei) is less then the value requested: %2$s wei.', 'cryptocurrency-product-for-woocommerce' ),
                                        $eth_balance_str,
                                        $eth_value_wei_str,
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainCurrencyTickerName()
                                    ) );
                                    if ( !$res ) {
                                        // failed to complete order
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to fail order: " . $order_id );
                                    }
                                }
                                
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_delete_last_txhash( $thisWalletAddress );
                                return [ null, null, true ];
                            }
                            
                            // 3. make payment if balance is enough
                            $nonce = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_nonce( $thisWalletAddress, $providerUrl, $eth );
                            
                            if ( null === $nonce ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "nonce is null" );
                                if ( !is_null( $order_id ) ) {
                                    update_post_meta( $order_id, 'status', __( 'Network error', 'cryptocurrency-product-for-woocommerce' ) );
                                }
                                return null;
                            }
                            
                            $gasLimit = intval( ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['gas_limit'] ) ? $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['gas_limit'] : '200000' ) );
                            $gasPrice = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_gas_price_wei();
                            $gasPriceTip = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_gas_price_tip_wei();
                            $thisWalletPrivKey = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletPrivateKey( $product_id, $from_user_id );
                            
                            if ( is_null( $thisWalletPrivKey ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum wallet private key", 'cryptocurrency-product-for-woocommerce' ) ) . ". product_id=" . $product_id . "; thisWalletAddress=" . $thisWalletAddress );
                                if ( !is_null( $order_id ) ) {
                                    update_post_meta( $order_id, 'status', __( 'Configuration error', 'cryptocurrency-product-for-woocommerce' ) );
                                }
                                return null;
                            }
                            
                            $to = $marketAddress;
                            $nonceb = \BitWasp\Buffertools\Buffer::int( $nonce );
                            $gasPrice = \BitWasp\Buffertools\Buffer::int( $gasPrice );
                            $gasLimit = \BitWasp\Buffertools\Buffer::int( $gasLimit );
                            $transactionData = [
                                'from'    => $thisWalletAddress,
                                'nonce'   => '0x' . $nonceb->getHex(),
                                'to'      => strtolower( $to ),
                                'gas'     => '0x' . $gasLimit->getHex(),
                                'value'   => '0x' . $eth_value_wei->toHex(),
                                'chainId' => $chainId,
                                'data'    => null,
                            ];
                            list( $error, $gasEstimate ) = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_gas_estimate( $transactionData, $eth );
                            
                            if ( null === $gasEstimate ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "gasEstimate is null: " . $error );
                                return null;
                            }
                            
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "gasEstimate: " . $gasEstimate->toHex() );
                            
                            if ( $gasLimit->getHex() === $gasEstimate->toHex() ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Too low gas limit specified in settings: " . $gasLimit->getHex() );
                                return null;
                            }
                            
                            $transactionData['gas'] = '0x' . $gasEstimate->toHex();
                            unset( $transactionData['from'] );
                            
                            if ( is_null( $gasPriceTip ) ) {
                                // pre-EIP1559
                                $transactionData['gasPrice'] = '0x' . $gasPrice->getHex();
                            } else {
                                $transactionData['accessList'] = [];
                                // EIP1559
                                $transactionData['maxFeePerGas'] = '0x' . $gasPrice->getHex();
                                $gasPriceTip = \BitWasp\Buffertools\Buffer::int( $gasPriceTip );
                                $transactionData['maxPriorityFeePerGas'] = '0x' . $gasPriceTip->getHex();
                            }
                            
                            $transaction = new \Web3p\EthereumTx\Transaction( $transactionData );
                            $signedTransaction = "0x" . $transaction->sign( $thisWalletPrivKey );
                            $txHash = null;
                            $eth->sendRawTransaction( (string) $signedTransaction, function ( $err, $transaction ) use( &$txHash, &$transactionData, $signedTransaction ) {
                                
                                if ( $err !== null ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to sendRawTransaction: " . $err );
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to sendRawTransaction: transactionData=" . print_r( $transactionData, true ) );
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to sendRawTransaction: signedTransaction=" . (string) $signedTransaction );
                                    return;
                                }
                                
                                $txHash = $transaction;
                            } );
                            
                            if ( null === $txHash ) {
                                if ( !is_null( $order_id ) ) {
                                    update_post_meta( $order_id, 'status', __( 'Network error', 'cryptocurrency-product-for-woocommerce' ) );
                                }
                                return null;
                            }
                            
                            
                            if ( !is_null( $order_id ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_set_order_txhash(
                                    $order_id,
                                    $product_id,
                                    $txHash,
                                    $blockchainNetwork
                                );
                                update_post_meta( $order_id, 'txdata_' . $product_id, sanitize_text_field( $signedTransaction ) );
                            }
                            
                            // the remaining balance
                            $eth_balance_remaining = $eth_balance->subtract( $eth_value_wei );
                            list( $eth_balance_remaining, $_ ) = $eth_balance_remaining->divide( new phpseclib3\Math\BigInteger( pow( 10, 9 ) ) );
                            $eth_balance_f = doubleval( $eth_balance_remaining->toString() ) / pow( 10, 9 );
                            
                            if ( !is_null( $product_id ) ) {
                                $product = wc_get_product( $product_id );
                                
                                if ( $product ) {
                                    $minimumValue = apply_filters( 'woocommerce_quantity_input_min', 0, $product );
                                    if ( empty($minimumValue) || 0 == $minimumValue ) {
                                        $minimumValue = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_quantity_input_min( 0.01, $product );
                                    }
                                    $status = 'outofstock';
                                    if ( doubleval( $minimumValue ) < $eth_balance_f ) {
                                        $status = 'instock';
                                    }
                                    wc_update_product_stock_status( $product_id, $status );
                                    //            // adjust stock quantity for fee
                                    //            list($eth_value_diff_wei, $_) = $eth_value_diff_wei->divide(new phpseclib3\Math\BigInteger(pow(10, 9)));
                                    //            $eth_value_f = floatval(doubleval($eth_value_diff_wei->toString()) / pow(10, 9));
                                    //            // the fee amount to decrease stock
                                    //            $eth_value_diff_wei = $eth_value_wei->subtract($eth_value_wei0);
                                    //            // adjust stock quantity for fee
                                    //            list($eth_value_diff_wei, $_) = $eth_value_diff_wei->divide(new phpseclib3\Math\BigInteger(pow(10, 9)));
                                    //            $eth_value_f = floatval(doubleval($eth_value_diff_wei->toString()) / pow(10, 9));
                                    //            wc_update_product_stock( $product, $eth_value_f, 'decrease');
                                }
                            
                            }
                            
                            if ( !is_null( $order_id ) ) {
                                update_post_meta( $order_id, 'status', __( 'Success', 'cryptocurrency-product-for-woocommerce' ) );
                            }
                            return array( $txHash, $nonce, false );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_txhash_path( $txHash, $blockchainNetwork )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $txHashPath = '';
                            switch ( $blockchainNetwork ) {
                                case 'mainnet':
                                    $txHashPath = 'https://etherscan.io/tx/' . $txHash;
                                    break;
                                case 'ropsten':
                                    $txHashPath = 'https://ropsten.etherscan.io/tx/' . $txHash;
                                    break;
                                case 'rinkeby':
                                    $txHashPath = 'https://rinkeby.etherscan.io/tx/' . $txHash;
                                    break;
                                case 'bsc':
                                    $txHashPath = 'https://bscscan.com/tx/' . $txHash;
                                    break;
                                case 'bsctest':
                                    $txHashPath = 'https://testnet.bscscan.com/tx/' . $txHash;
                                    break;
                                case 'polygon':
                                    $txHashPath = 'https://polygonscan.com/tx/' . $txHash;
                                    break;
                                case 'mumbai':
                                    $txHashPath = 'https://mumbai.polygonscan.com/tx/' . $txHash;
                                    break;
                                default:
                                    break;
                            }
                            return $txHashPath;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_address_path( $address, $blockchainNetwork )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $addressPath = '';
                            switch ( $blockchainNetwork ) {
                                case 'mainnet':
                                    $addressPath = 'https://etherscan.io/address/' . $address;
                                    break;
                                case 'ropsten':
                                    $addressPath = 'https://ropsten.etherscan.io/address/' . $address;
                                    break;
                                case 'rinkeby':
                                    $addressPath = 'https://rinkeby.etherscan.io/address/' . $address;
                                    break;
                                case 'bsc':
                                    $txHashPath = 'https://bscscan.com/address/' . $address;
                                    break;
                                case 'bsctest':
                                    $txHashPath = 'https://testnet.bscscan.com/address/' . $address;
                                    break;
                                case 'polygon':
                                    $txHashPath = 'https://polygonscan.com/address/' . $address;
                                    break;
                                case 'mumbai':
                                    $txHashPath = 'https://mumbai.polygonscan.com/address/' . $address;
                                    break;
                                default:
                                    break;
                            }
                            return $addressPath;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_set_order_txhash(
                            $order_id,
                            $product_id,
                            $txHash,
                            $blockchainNetwork
                        )
                        {
                            $txHashPath = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_txhash_path( $txHash, $blockchainNetwork );
                            $order = wc_get_order( $order_id );
                            $order->add_order_note( sprintf( __( 'Sent to blockchain. Transaction hash  <a target="_blank" href="%1$s">%2$s</a>.', 'cryptocurrency-product-for-woocommerce' ), $txHashPath, $txHash ) );
                            update_post_meta( $order_id, 'ether_txhash_' . $product_id, sanitize_text_field( $txHash ) );
                        }
                        
                        add_action(
                            "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_send_tx",
                            "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_send_tx",
                            0,
                            4
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_send_tx(
                            $contractAddress,
                            $data,
                            $gasLimit,
                            $providerUrl,
                            $restartOnError = true
                        )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            // проверить, есть ли прошлая транзакция
                            $thisWalletAddress = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress();
                            
                            if ( is_null( $thisWalletAddress ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum wallet address", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return;
                            }
                            
                            $lasttxhash = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_last_txhash( $thisWalletAddress );
                            $txhash = null;
                            $nonce = null;
                            $canceled = false;
                            try {
                                
                                if ( $lasttxhash ) {
                                    $lastnonce = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_last_nonce( $thisWalletAddress );
                                    // если есть, проверить, завершена ли она
                                    $tx_confirmed = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_tx_confirmed( $lasttxhash, $lastnonce, $providerUrl );
                                    
                                    if ( $tx_confirmed ) {
                                        //   - да, послать новую транзакцию
                                        list( $txhash, $nonce, $canceled ) = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_send_tx_impl(
                                            $contractAddress,
                                            $data,
                                            $gasLimit,
                                            $providerUrl
                                        );
                                    } else {
                                        
                                        if ( is_null( $tx_confirmed ) ) {
                                            // nonce in last tx is outdated. remove it
                                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_delete_last_txhash( $thisWalletAddress );
                                            //            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_cancel_complete_order_task($order_id, $txhash, $nonce);
                                        }
                                    
                                    }
                                
                                } else {
                                    // нет последней транзакции. послать новую транзакцию
                                    list( $txhash, $nonce, $canceled ) = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_send_tx_impl(
                                        $contractAddress,
                                        $data,
                                        $gasLimit,
                                        $providerUrl
                                    );
                                }
                                
                                
                                if ( $txhash ) {
                                    // успех - запомнить новую последнюю транзакцию
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_set_last_txhash( $thisWalletAddress, $txhash, $nonce );
                                } else {
                                    if ( !$canceled ) {
                                        // Неуспех - поставить себя снова в очередь
                                        if ( $restartOnError ) {
                                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_send_tx_task(
                                                $contractAddress,
                                                $data,
                                                $gasLimit,
                                                $providerUrl,
                                                1 * 60
                                            );
                                        }
                                    }
                                }
                            
                            } catch ( Exception $ex ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_send_tx: " . $ex->getMessage() );
                            }
                            return $txhash;
                        }
                        
                        // TODO: wait for a configured number of blocks
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_tx_confirmed(
                            $txhash,
                            $lastnonce,
                            $providerUrl,
                            $product_id = null,
                            $from_user_id = null
                        )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $requestManager = new \Web3\RequestManagers\HttpRequestManager( $providerUrl, 10 );
                            $web3 = new \Web3\Web3( new \Web3\Providers\HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $is_confirmed = false;
                            $thisWalletAddress = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress( $product_id, $from_user_id );
                            
                            if ( is_null( $thisWalletAddress ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum wallet address", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return $is_confirmed;
                            }
                            
                            $eth->getTransactionByHash( $txhash, function ( $err, $transaction ) use(
                                &$is_confirmed,
                                $txhash,
                                $lastnonce,
                                $thisWalletAddress,
                                $providerUrl
                            ) {
                                
                                if ( $err !== null ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to getTransactionByHash: " . $err );
                                    $nonce = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_nonce( $thisWalletAddress, $providerUrl );
                                    
                                    if ( !is_null( $nonce ) && intval( $lastnonce ) < intval( $nonce ) ) {
                                        // tx outdated flag
                                        $is_confirmed = null;
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "tx nonce({$lastnonce}) less then address nonce({$nonce})" );
                                    }
                                    
                                    return;
                                }
                                
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "transaction: " . print_r( $transaction, true ) );
                                
                                if ( is_null( $transaction ) ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_increase_tx_errors_counter( $txhash );
                                    $errors_count = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_tx_errors_counter( $txhash );
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "tx ({$txhash}) is not found in blockchain. errors_count = " . $errors_count );
                                    
                                    if ( $errors_count >= 10 ) {
                                        $is_confirmed = null;
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "tx ({$txhash}) is_confirmed is set to null" );
                                    }
                                
                                } else {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_reset_tx_errors_counter( $txhash );
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "tx ({$txhash}) errors_count reset" );
                                    $is_confirmed = property_exists( $transaction, "blockHash" ) && !empty($transaction->blockHash) && '0x0000000000000000000000000000000000000000000000000000000000000000' != $transaction->blockHash;
                                }
                            
                            } );
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "is_confirmed({$txhash}): " . $is_confirmed );
                            return $is_confirmed;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_tx_succeeded( $txhash, $providerUrl )
                        {
                            $requestManager = new \Web3\RequestManagers\HttpRequestManager( $providerUrl, 10 );
                            $web3 = new \Web3\Web3( new \Web3\Providers\HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $is_confirmed = false;
                            $gas = NULL;
                            $eth->getTransactionByHash( $txhash, function ( $err, $transaction ) use( &$gas, &$is_confirmed ) {
                                
                                if ( $err !== null ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to getTransactionByHash: " . $err );
                                    return;
                                }
                                
                                //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("transaction: " . print_r($transaction, true));
                                $is_confirmed = property_exists( $transaction, "blockHash" ) && !empty($transaction->blockHash) && '0x0000000000000000000000000000000000000000000000000000000000000000' != $transaction->blockHash;
                                $gas = $transaction->gas;
                            } );
                            if ( !$is_confirmed ) {
                                return null;
                            }
                            $gasUsed = NULL;
                            $status = NULL;
                            $transactionReceipt = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getTransactionReceipt( $txhash, $eth );
                            if ( is_null( $transactionReceipt ) ) {
                                return null;
                            }
                            if ( property_exists( $transactionReceipt, "status" ) && !empty($transactionReceipt->status) ) {
                                $status = $transactionReceipt->status;
                            }
                            if ( !is_null( $status ) ) {
                                return boolval( intval( $status, 16 ) );
                            }
                            if ( property_exists( $transactionReceipt, "gasUsed" ) && !empty($transactionReceipt->gasUsed) ) {
                                $gasUsed = $transactionReceipt->gasUsed;
                            }
                            if ( is_null( $gasUsed ) ) {
                                return null;
                            }
                            return intval( $gas, 16 ) != intval( $gasUsed, 16 );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_last_txhash( $thisWalletAddress )
                        {
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $option = $thisWalletAddress . "-cryptocurrency-product-for-woocommerce-queue-txhash-" . $chainId;
                            $txhash = get_option( $option );
                            return $txhash;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_last_nonce( $thisWalletAddress )
                        {
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $option = $thisWalletAddress . "-cryptocurrency-product-for-woocommerce-queue-nonce-" . $chainId;
                            $nonce = get_option( $option );
                            return $nonce;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_tx_errors_counter( $txhash )
                        {
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $option = $txhash . "-cryptocurrency-product-for-woocommerce-errors-counter-" . $chainId;
                            $value = get_option( $option );
                            if ( empty($value) ) {
                                return 0;
                            }
                            return intval( $value );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_increase_tx_errors_counter( $txhash )
                        {
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $option = $txhash . "-cryptocurrency-product-for-woocommerce-errors-counter-" . $chainId;
                            $prev_value = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_tx_errors_counter( $txhash );
                            $new_value = $prev_value + 1;
                            $autoload = true;
                            update_option( $option, $new_value, $autoload );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_reset_tx_errors_counter( $txhash )
                        {
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $option = $txhash . "-cryptocurrency-product-for-woocommerce-errors-counter-" . $chainId;
                            $new_value = 0;
                            $autoload = true;
                            update_option( $option, $new_value, $autoload );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_set_last_txhash( $thisWalletAddress, $txhash, $nonce )
                        {
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $option = $thisWalletAddress . "-cryptocurrency-product-for-woocommerce-queue-txhash-" . $chainId;
                            $new_value = $txhash;
                            $autoload = true;
                            update_option( $option, $new_value, $autoload );
                            $option = $thisWalletAddress . "-cryptocurrency-product-for-woocommerce-queue-nonce-" . $chainId;
                            $new_value = $nonce;
                            $autoload = true;
                            update_option( $option, $new_value, $autoload );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_delete_last_txhash( $thisWalletAddress )
                        {
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $option = $thisWalletAddress . "-cryptocurrency-product-for-woocommerce-queue-txhash-" . $chainId;
                            delete_option( $option );
                            $option = $thisWalletAddress . "-cryptocurrency-product-for-woocommerce-queue-nonce-" . $chainId;
                            delete_option( $option );
                        }
                        
                        add_action(
                            "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_complete_order",
                            "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_complete_order",
                            0,
                            2
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_complete_order( $order_id, $_product_id )
                        {
                            try {
                                // do the payment
                                $order = wc_get_order( $order_id );
                                if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $_product_id ) ) {
                                    // skip non-cryptocurrency products
                                    return;
                                }
                                $order_items = $order->get_items();
                                if ( !$order_items ) {
                                    return;
                                }
                                $is_order_complete = true;
                                foreach ( $order_items as $item_id => $item ) {
                                    $product_id = $item['product_id'];
                                    if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product_id ) ) {
                                        // skip non-cryptocurrency products
                                        continue;
                                    }
                                    $_product = wc_get_product( $product_id );
                                    
                                    if ( !$_product ) {
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "complete_order({$product_id}) not a product" );
                                        continue;
                                    }
                                    
                                    $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, '_select_cryptocurrency_option', true );
                                    $is_order_complete = apply_filters(
                                        'cryptocurrency_product_for_woocommerce_is_order_complete',
                                        $is_order_complete,
                                        $cryptocurrency_option,
                                        $order_id,
                                        $product_id
                                    );
                                }
                                // $tx_succeeded = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_tx_succeeded($txhash, $providerUrl);
                                
                                if ( $is_order_complete ) {
                                    // Load the order.
                                    $order = wc_get_order( $order_id );
                                    // Place the order completed.
                                    $res = $order->update_status( 'completed', __( 'Transaction confirmed.', 'cryptocurrency-product-for-woocommerce' ) );
                                    
                                    if ( !$res ) {
                                        // failed to complete order
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to complete order: " . $order_id );
                                        // Неуспех - поставить себя снова в очередь
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_complete_order_task( $order_id, $product_id, 30 );
                                    }
                                    
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Order " . $order_id . " completed." );
                                } else {
                                    
                                    if ( is_null( $is_order_complete ) ) {
                                        // transaction failed
                                        // CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("transaction $txhash for $order_id has failed");
                                        update_post_meta( $order_id, 'status', __( 'Transaction failed', 'cryptocurrency-product-for-woocommerce' ) );
                                    } else {
                                        // tx is not confirmed yet
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to complete order: " . $order_id . ". one or more txns not confirmed yet. Restart processing." );
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_complete_order_task( $order_id, $product_id, 30 );
                                    }
                                
                                }
                                
                                // backward compatibility
                                do_action(
                                    'cryptocurrency_product_for_woocommerce_complete_order',
                                    $cryptocurrency_option,
                                    $order_id,
                                    $product_id
                                );
                            } catch ( Exception $ex ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_complete_order: " . $ex->getMessage() );
                                // Неуспех - поставить себя снова в очередь +60сек
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_process_order_task( $order_id, 60 );
                            }
                        }
                        
                        add_filter(
                            'cryptocurrency_product_for_woocommerce_is_order_complete',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_is_order_complete_hook',
                            10,
                            4
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_is_order_complete_hook(
                            $is_order_complete,
                            $cryptocurrency_option,
                            $order_id,
                            $product_id
                        )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            if ( 'Ether' !== $cryptocurrency_option ) {
                                return $is_order_complete;
                            }
                            // order is not complete flag
                            if ( false === $is_order_complete ) {
                                return $is_order_complete;
                            }
                            // order is failed flag
                            if ( is_null( $is_order_complete ) ) {
                                return $is_order_complete;
                            }
                            $thisWalletAddress = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress( $product_id );
                            
                            if ( is_null( $thisWalletAddress ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum wallet address", 'cryptocurrency-product-for-woocommerce' ) ) );
                                update_post_meta( $order_id, 'status', __( 'Configuration error', 'cryptocurrency-product-for-woocommerce' ) );
                                return $is_order_complete;
                            }
                            
                            $txhash = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $order_id, 'ether_txhash_' . $product_id, true );
                            if ( empty($txhash) ) {
                                // CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_is_order_complete_hook: no ether_txhash_$product_id in the order complete task for order: " . $order_id);
                                return false;
                            }
                            $providerUrl = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getWeb3Endpoint();
                            try {
                                $tx_succeeded = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_tx_succeeded( $txhash, $providerUrl );
                                
                                if ( $tx_succeeded ) {
                                    // success, do not change flag value
                                    $is_order_complete = $is_order_complete;
                                } else {
                                    
                                    if ( is_null( $tx_succeeded ) ) {
                                        // tx is not confirmed yet
                                        $is_order_complete = false;
                                    } else {
                                        // transaction failed
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "transaction {$txhash} for {$order_id} has failed" );
                                        $is_order_complete = null;
                                        $order = wc_get_order( $order_id );
                                        // Place the order to failed.
                                        $blockchainNetwork = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainNetwork();
                                        $txHashPath = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_txhash_path( $txhash, $blockchainNetwork );
                                        $res = $order->update_status( 'failed', sprintf( __( 'Transaction <a target="_blank" href="%1$s">%2$s</a> has failed.', 'cryptocurrency-product-for-woocommerce' ), $txHashPath, $txhash ) );
                                        
                                        if ( !$res ) {
                                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to fail order: " . $order_id );
                                            // Неуспех - поставить себя снова в очередь
                                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_complete_order_task( $order_id, $product_id, 30 );
                                        }
                                    
                                    }
                                
                                }
                            
                            } catch ( Exception $ex ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_is_order_complete_hook: " . $ex->getMessage() );
                            }
                            return $is_order_complete;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_complete_order_task( $order_id, $product_id, $offset = 0 )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir ;
                            $order = wc_get_order( $order_id );
                            
                            if ( !$order ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to create complete_order task for order: {$order_id}" );
                                return;
                            }
                            
                            $date = $order->get_date_created();
                            // fail order after one week of inactivity
                            $timeout = 3600 * 24 * CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getExpirationPeriod();
                            
                            if ( time() - $date->getTimestamp() > $timeout ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to create complete_order task for order {$order_id}: order timed out." );
                                update_post_meta( $order_id, 'status', __( 'Timed out', 'cryptocurrency-product-for-woocommerce' ) );
                                // Place the order to failed.
                                $res = $order->update_status( 'failed', __( 'Timed out', 'cryptocurrency-product-for-woocommerce' ) );
                                if ( !$res ) {
                                    // failed to complete order
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to fail order: " . $order_id );
                                }
                                return;
                            }
                            
                            $timestamp = time() + $offset;
                            $hook = "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_complete_order";
                            $args = array( $order_id, $product_id );
                            // @see https://github.com/woocommerce/action-scheduler/issues/730
                            
                            if ( !class_exists( 'ActionScheduler', false ) || !ActionScheduler::is_initialized() ) {
                                require_once $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir . '/vendor/woocommerce/action-scheduler/classes/abstracts/ActionScheduler.php';
                                ActionScheduler::init( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir . '/vendor/woocommerce/action-scheduler/action-scheduler.php' );
                            }
                            
                            $task_id = as_schedule_single_action( $timestamp, $hook, $args );
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Task complete_order with id {$task_id} scheduled for order: {$order_id}" );
                        }
                        
                        // function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_cancel_complete_order_task($order_id, $product_id, $txhash, $nonce) {
                        // //    global $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options;
                        //
                        //     $hook = "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_complete_order";
                        //     $args = array($order_id, $product_id, $txhash, $nonce);
                        //     as_unschedule_action( $hook, $args );
                        //     CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("Task complete_order with txhash $txhash unscheduled for order: $order_id");
                        // }
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_send_tx_task(
                            $contractAddress,
                            $data,
                            $gasLimit,
                            $providerUrl,
                            $offset = 0
                        )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options, $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir ;
                            $timestamp = time() + $offset;
                            $hook = "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_send_tx";
                            $args = array(
                                $contractAddress,
                                $data,
                                $gasLimit,
                                $providerUrl
                            );
                            $thisWalletAddress = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress();
                            
                            if ( is_null( $thisWalletAddress ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum wallet address", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return;
                            }
                            
                            $group = $thisWalletAddress . "-cryptocurrency-product-for-woocommerce-queue";
                            // @see https://github.com/woocommerce/action-scheduler/issues/730
                            
                            if ( !class_exists( 'ActionScheduler', false ) || !ActionScheduler::is_initialized() ) {
                                require_once $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir . '/vendor/woocommerce/action-scheduler/classes/abstracts/ActionScheduler.php';
                                ActionScheduler::init( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir . '/vendor/woocommerce/action-scheduler/action-scheduler.php' );
                            }
                            
                            $task_id = as_schedule_single_action(
                                $timestamp,
                                $hook,
                                $args,
                                $group
                            );
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Task send_tx({$contractAddress}) with id {$task_id} scheduled for group: {$group}" );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_enqueue_send_ether_task(
                            $order_id,
                            $product_id,
                            $marketAddress,
                            $product_quantity,
                            $providerUrl,
                            $offset = 0,
                            $from_user_id = null
                        )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options, $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir ;
                            $timestamp = time() + $offset;
                            $hook = "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_send_ether";
                            $args = array(
                                $order_id,
                                $product_id,
                                $marketAddress,
                                $product_quantity,
                                $providerUrl,
                                $from_user_id
                            );
                            $thisWalletAddress = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress( $product_id, $from_user_id );
                            
                            if ( is_null( $thisWalletAddress ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum wallet address", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return;
                            }
                            
                            $group = $thisWalletAddress . "-cryptocurrency-product-for-woocommerce-queue";
                            // @see https://github.com/woocommerce/action-scheduler/issues/730
                            
                            if ( !class_exists( 'ActionScheduler', false ) || !ActionScheduler::is_initialized() ) {
                                require_once $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir . '/vendor/woocommerce/action-scheduler/classes/abstracts/ActionScheduler.php';
                                ActionScheduler::init( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir . '/vendor/woocommerce/action-scheduler/action-scheduler.php' );
                            }
                            
                            $task_id = as_schedule_single_action(
                                $timestamp,
                                $hook,
                                $args,
                                $group
                            );
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Task send_ether with id {$task_id} for order {$order_id} scheduled for group: {$group}" );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_nonce( $accountAddress, $providerUrl, $eth = null )
                        {
                            
                            if ( !$eth ) {
                                $requestManager = new \Web3\RequestManagers\HttpRequestManager( $providerUrl, 10 );
                                $web3 = new \Web3\Web3( new \Web3\Providers\HttpProvider( $requestManager ) );
                                $eth = $web3->eth;
                            }
                            
                            $nonce = 0;
                            $eth->getTransactionCount( $accountAddress, function ( $err, $transactionCount ) use( &$nonce ) {
                                
                                if ( $err !== null ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to getTransactionCount: " . $err );
                                    $nonce = null;
                                    return;
                                }
                                
                                $nonce = intval( $transactionCount->toString() );
                            } );
                            return $nonce;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getTransactionReceipt( $txhash, $eth = null )
                        {
                            
                            if ( !$eth ) {
                                $providerUrl = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getWeb3Endpoint();
                                $requestManager = new \Web3\RequestManagers\HttpRequestManager( $providerUrl, 10 );
                                $web3 = new \Web3\Web3( new \Web3\Providers\HttpProvider( $requestManager ) );
                                $eth = $web3->eth;
                            }
                            
                            $transactionReceiptRes = NULL;
                            $eth->getTransactionReceipt( $txhash, function ( $err, $transactionReceipt ) use( &$transactionReceiptRes ) {
                                
                                if ( $err !== null ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to getTransactionReceipt: " . $err );
                                    return;
                                }
                                
                                $transactionReceiptRes = $transactionReceipt;
                                //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("transactionReceipt: " . print_r($transactionReceipt, true));
                            } );
                            return $transactionReceiptRes;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getLatestBlock( $eth )
                        {
                            static  $_block_saved = null ;
                            $error = null;
                            
                            if ( is_null( $_block_saved ) ) {
                                $block = null;
                                $eth->getBlockByNumber( 'latest', false, function ( $err, $_block ) use( &$block, &$error ) {
                                    
                                    if ( $err !== null ) {
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to getBlockByNumber: " . $err );
                                        $error = $err;
                                        return;
                                    }
                                    
                                    $block = $_block;
                                    // CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getLatestBlock: " . print_r($_block, true));
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getLatestBlock: latest" );
                                } );
                                $_block_saved = $block;
                            }
                            
                            return [ $error, $_block_saved ];
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlock( $blockHashOrBlockNumber, $eth = null )
                        {
                            
                            if ( !$eth ) {
                                $providerUrl = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getWeb3Endpoint();
                                $requestManager = new \Web3\RequestManagers\HttpRequestManager( $providerUrl, 10 );
                                $web3 = new \Web3\Web3( new \Web3\Providers\HttpProvider( $requestManager ) );
                                $eth = $web3->eth;
                            }
                            
                            $blockRes = NULL;
                            $eth->getBlockByHash( $blockHashOrBlockNumber, true, function ( $err, $block ) use( &$blockRes ) {
                                
                                if ( $err !== null ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to getBlock: " . $err );
                                    return;
                                }
                                
                                $blockRes = $block;
                                //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("block: " . print_r($block, true));
                            } );
                            if ( is_null( $blockRes ) ) {
                                $eth->getBlockByNumber( $blockHashOrBlockNumber, true, function ( $err, $block ) use( &$blockRes ) {
                                    
                                    if ( $err !== null ) {
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to getBlock: " . $err );
                                        return;
                                    }
                                    
                                    $blockRes = $block;
                                    //            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("block: " . print_r($block, true));
                                } );
                            }
                            return $blockRes;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getLogs(
                            $address,
                            $topics,
                            $eth = null,
                            $fromBlock = '0x0',
                            $toBlock = 'latest'
                        )
                        {
                            /* getLogs
                              Object - The filter options:
                              fromBlock: QUANTITY|TAG - (optional, default: "latest") Integer block number, or "latest" for the last mined block or "pending", "earliest" for not yet mined transactions.
                              toBlock: QUANTITY|TAG - (optional, default: "latest") Integer block number, or "latest" for the last mined block or "pending", "earliest" for not yet mined transactions.
                              address: DATA|Array, 20 Bytes - (optional) Contract address or a list of addresses from which logs should originate.
                              topics: Array of DATA, - (optional) Array of 32 Bytes DATA topics. Topics are order-dependent. Each topic can also be an array of DATA with “or” options.
                              blockhash: DATA, 32 Bytes - (optional, future) With the addition of EIP-234, blockHash will be a new filter option which restricts the logs returned to the single block with the 32-byte hash blockHash. Using blockHash is equivalent to fromBlock = toBlock = the block number with hash blockHash. If blockHash is present in in the filter criteria, then neither fromBlock nor toBlock are allowed.
                              */
                            
                            if ( !$eth ) {
                                $providerUrl = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getWeb3Endpoint();
                                $requestManager = new \Web3\RequestManagers\HttpRequestManager( $providerUrl, 10 );
                                $web3 = new \Web3\Web3( new \Web3\Providers\HttpProvider( $requestManager ) );
                                $eth = $web3->eth;
                            }
                            
                            // CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getLogs: topics=" . print_r($topics, true));
                            foreach ( $topics as $key => $topic ) {
                                
                                if ( is_int( $topic ) ) {
                                    $topicBn = new phpseclib3\Math\BigInteger( $topic );
                                    $topic = $topicBn->toHex();
                                }
                                
                                
                                if ( 0 === strpos( $topic, '0x' ) ) {
                                    $topic = substr( $topic, 2 );
                                    $topic = sprintf( '0x%064s', $topic );
                                    $topics[$key] = $topic;
                                }
                            
                            }
                            $blockRes = NULL;
                            $args = [
                                'fromBlock' => $fromBlock,
                                'toBlock'   => $toBlock,
                                'address'   => $address,
                                'topics'    => array_values( $topics ),
                            ];
                            // CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getLogs: " . print_r($args, true));
                            $eth->getLogs( $args, function ( $err, $logs ) use( &$logsRes ) {
                                
                                if ( $err !== null ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to getLogs: " . $err );
                                    return;
                                }
                                
                                $logsRes = $logs;
                                //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("logs: " . print_r($logs, true));
                            } );
                            return $logsRes;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_send_tx_impl(
                            $contractAddress,
                            $data,
                            $gasLimit,
                            $providerUrl
                        )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $thisWalletAddress = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress();
                            
                            if ( is_null( $thisWalletAddress ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum wallet address", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $chainId = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId();
                            
                            if ( null === $chainId ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Blockchain", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            // 4. call payToken if allowance is enough
                            $requestManager = new \Web3\RequestManagers\HttpRequestManager( $providerUrl, 10 );
                            $web3 = new \Web3\Web3( new \Web3\Providers\HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $nonce = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_nonce( $thisWalletAddress, $providerUrl, $eth );
                            
                            if ( null === $nonce ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "nonce is null" );
                                return null;
                            }
                            
                            $gasPrice = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_gas_price_wei();
                            $gasPriceTip = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_gas_price_tip_wei();
                            $thisWalletPrivKey = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletPrivateKey();
                            
                            if ( is_null( $thisWalletPrivKey ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum wallet private key", 'cryptocurrency-product-for-woocommerce' ) ) );
                                return null;
                            }
                            
                            $to = $contractAddress;
                            $nonceb = \BitWasp\Buffertools\Buffer::int( $nonce );
                            $gasPrice = \BitWasp\Buffertools\Buffer::int( $gasPrice );
                            $gasLimit = \BitWasp\Buffertools\Buffer::int( $gasLimit );
                            $transactionData = [
                                'from'    => $thisWalletAddress,
                                'nonce'   => '0x' . $nonceb->getHex(),
                                'to'      => strtolower( $to ),
                                'gas'     => '0x' . $gasLimit->getHex(),
                                'value'   => '0x0',
                                'chainId' => $chainId,
                                'data'    => '0x' . $data,
                            ];
                            list( $error, $gasEstimate ) = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_gas_estimate( $transactionData, $eth );
                            
                            if ( null === $gasEstimate ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "gasEstimate is null: " . $error );
                                return null;
                            }
                            
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "gasEstimate: " . $gasEstimate->toHex() );
                            
                            if ( $gasLimit->getHex() === $gasEstimate->toHex() ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Too low gas limit specified in settings: " . $gasLimit->getHex() );
                                return null;
                            }
                            
                            $transactionData['gas'] = '0x' . $gasEstimate->toHex();
                            unset( $transactionData['from'] );
                            
                            if ( is_null( $gasPriceTip ) ) {
                                // pre-EIP1559
                                $transactionData['gasPrice'] = '0x' . $gasPrice->getHex();
                            } else {
                                $transactionData['accessList'] = [];
                                // EIP1559
                                $transactionData['maxFeePerGas'] = '0x' . $gasPrice->getHex();
                                $gasPriceTip = \BitWasp\Buffertools\Buffer::int( $gasPriceTip );
                                $transactionData['maxPriorityFeePerGas'] = '0x' . $gasPriceTip->getHex();
                            }
                            
                            $transaction = new \Web3p\EthereumTx\Transaction( $transactionData );
                            $signedTransaction = "0x" . $transaction->sign( $thisWalletPrivKey );
                            $txHash = null;
                            $eth->sendRawTransaction( (string) $signedTransaction, function ( $err, $transaction ) use( &$txHash, &$transactionData, $signedTransaction ) {
                                
                                if ( $err !== null ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to sendRawTransaction: " . $err );
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to sendRawTransaction: transactionData=" . print_r( $transactionData, true ) );
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to sendRawTransaction: signedTransaction=" . (string) $signedTransaction );
                                    return;
                                }
                                
                                $txHash = $transaction;
                            } );
                            if ( null === $txHash ) {
                                return null;
                            }
                            return array( $txHash, $nonce, false );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_encodeParameters( $types, $params )
                        {
                            $ethABI = new \Web3\Contracts\Ethabi( [
                                'address'      => new Web3\Contracts\Types\Address(),
                                'bool'         => new Web3\Contracts\Types\Boolean(),
                                'bytes'        => new Web3\Contracts\Types\Bytes(),
                                'dynamicBytes' => new Web3\Contracts\Types\DynamicBytes(),
                                'int'          => new Web3\Contracts\Types\Integer(),
                                'string'       => new Web3\Contracts\Types\Str(),
                                'uint'         => new Web3\Contracts\Types\Uinteger(),
                            ] );
                            $_data = $ethABI->encodeParameters( $types, $params );
                            return $_data;
                        }
                        
                        /**
                         * Log information using the WC_Logger class.
                         *
                         * Will do nothing unless debug is enabled.
                         *
                         * @param string $msg   The message to be logged.
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( $msg )
                        {
                            static  $logger = false ;
                            // Create a logger instance if we don't already have one.
                            if ( false === $logger ) {
                                $logger = new WC_Logger();
                            }
                            $logger->add( 'cryptocurrency-product-for-woocommerce', $msg );
                        }
                        
                        add_action(
                            "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_order",
                            "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_order",
                            0,
                            1
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_is_order_payed( $order_id, $product_id )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            // backward compatibility
                            $txhash = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $order_id, 'ether_txhash', true );
                            if ( !empty($txhash) ) {
                                return true;
                            }
                            $txhash = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $order_id, 'ether_txhash_' . $product_id, true );
                            return !empty($txhash);
                        }
                        
                        add_action(
                            'cryptocurrency_product_for_woocommerce_get_cryptocurrency_address',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_get_cryptocurrency_address_hook',
                            10,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_get_cryptocurrency_address_hook( $address, $cryptocurrency_option, $order_id )
                        {
                            if ( "ETHEREUM" !== apply_filters( 'cryptocurrency_product_for_woocommerce_get_base_blockchain', '', $cryptocurrency_option ) ) {
                                return $address;
                            }
                            $_billing_cryptocurrency_ethereum_address = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $order_id, '_billing_cryptocurrency_ethereum_address', true );
                            
                            if ( empty($_billing_cryptocurrency_ethereum_address) ) {
                                $order = wc_get_order( $order_id );
                                if ( !$order ) {
                                    return $address;
                                }
                                $user_id = $order->get_customer_id();
                                if ( $user_id <= 0 ) {
                                    return $address;
                                }
                                $_billing_cryptocurrency_ethereum_address = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                            }
                            
                            if ( empty($_billing_cryptocurrency_ethereum_address) ) {
                                return $address;
                            }
                            return $_billing_cryptocurrency_ethereum_address;
                        }
                        
                        /**
                         * Check if order is not processed yet and process it in this case:
                         * sends Ether or ERC20 tokens to the customer Ethereum address
                         *
                         * @param int $order_id The order id
                         */
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_order( $order_id )
                        {
                            _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_order_impl( $order_id );
                        }
                        
                        ////see woocommerce simple auctions wordpress auctions
                        //function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_auction_order_item($order, $item_id) {
                        //    if ( function_exists( 'wc_get_order_item_meta' ) ) {
                        //        $item_meta = wc_get_order_item_meta( $item_id, '' );
                        //    } else {
                        //        $item_meta = method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
                        //    }
                        //    $product_id   = $item_meta['_product_id'][0];
                        //    $product_data = wc_get_product( $product_id );
                        //    return ( method_exists( $product_data, 'get_type' ) && $product_data->get_type() == 'auction' );
                        //}
                        //// $id = $this->get_main_wpml_product_id();
                        //add_action('woocommerce_simple_auction_won', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_simple_auction_won', 10, 1);
                        //
                        //function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_simple_auction_won($product_id) {
                        //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_simple_auction_won($product_id) call");
                        //    $order_id = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, '_order_id', true );
                        //    if (empty($order_id)) {
                        //        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_simple_auction_won($product_id) empty order_id");
                        //        return;
                        //    }
                        //    _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_order_impl($order_id, false);
                        //}
                        //
                        //// do_action( 'woocommerce_simple_auction_close_buynow', $product_id , $original_product_id);
                        /**
                         * Check if order is not processed yet and process it in this case:
                         * sends Ether or ERC20 tokens to the customer Ethereum address
                         *
                         * @param int $order_id The order id
                         */
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_order_impl( $order_id, $skip_auction = true )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            try {
                                // do the payment
                                $order = wc_get_order( $order_id );
                                $order_items = $order->get_items();
                                if ( !$order_items ) {
                                    return;
                                }
                                foreach ( $order_items as $item_id => $item ) {
                                    $product_id = $item['product_id'];
                                    if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product_id ) ) {
                                        // skip non-cryptocurrency products
                                        continue;
                                    }
                                    //            if ($skip_auction && _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_auction_order_item($order, $item_id)) {
                                    //                // skip auction products
                                    //                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("process_order($product_id) skip auction");
                                    //                continue;
                                    //            }
                                    $_product = wc_get_product( $product_id );
                                    
                                    if ( !$_product ) {
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "process_order({$product_id}) not a product" );
                                        continue;
                                    }
                                    
                                    $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, '_select_cryptocurrency_option', true );
                                    $_billing_cryptocurrency_address = apply_filters(
                                        'cryptocurrency_product_for_woocommerce_get_cryptocurrency_address',
                                        '',
                                        $cryptocurrency_option,
                                        $order_id
                                    );
                                    
                                    if ( empty($_billing_cryptocurrency_address) ) {
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Ethereum address is empty for order {$order_id}. Skip processing." );
                                        continue;
                                    }
                                    
                                    $marketAddress = $_billing_cryptocurrency_address;
                                    $minimumValue = apply_filters( 'woocommerce_quantity_input_min', 0, $_product );
                                    if ( empty($minimumValue) || 0 == $minimumValue ) {
                                        $minimumValue = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_quantity_input_min( 0.01, $_product );
                                    }
                                    $minimumStep = apply_filters( 'woocommerce_quantity_input_step', 0.01, $_product );
                                    if ( empty($minimumStep) || 0 == floatval( $minimumStep ) ) {
                                        $minimumStep = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_quantity_input_step( 0.01, $_product );
                                    }
                                    $product_quantity = null;
                                    
                                    if ( is_null( $product_quantity ) ) {
                                        $product_quantity = $item['qty'];
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_order({$product_id}): total: " . $item['total'] . ', total_tax: ' . $item['total_tax'] . ', product_quantity=' . $product_quantity );
                                    }
                                    
                                    // add 10% of the $minimumStep to workaround price fluctuations
                                    
                                    if ( floatval( $product_quantity ) < floatval( $minimumValue ) ) {
                                        // Place the order to failed.
                                        $res = $order->update_status( 'failed', sprintf( __( 'Product quantity %1$s less then the minimum allowed: %2$s.', 'cryptocurrency-product-for-woocommerce' ), $product_quantity, $minimumValue ) );
                                        if ( !$res ) {
                                            // failed to complete order
                                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to fail order: " . $order_id );
                                        }
                                        continue;
                                    }
                                    
                                    $maximumValue = apply_filters( 'woocommerce_quantity_input_max', -1, $_product );
                                    if ( empty($maximumValue) || $maximumValue ) {
                                        $maximumValue = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_quantity_input_max( -1, $_product );
                                    }
                                    if ( $maximumValue > 0 ) {
                                        //                $product_quantity = $product['qty'];
                                        
                                        if ( floatval( $product_quantity ) > floatval( $maximumValue ) ) {
                                            // Place the order to failed.
                                            $res = $order->update_status( 'failed', sprintf( __( 'Product quantity %1$s greater then the maximum allowed: %2$s.', 'cryptocurrency-product-for-woocommerce' ), $product_quantity, $maximumValue ) );
                                            if ( !$res ) {
                                                // failed to complete order
                                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to fail order: " . $order_id );
                                            }
                                            continue;
                                        }
                                    
                                    }
                                    $variation_id = $item->get_variation_id();
                                    
                                    if ( !empty($variation_id) ) {
                                        // item is variable and we can check its variation
                                        $variation = wc_get_product_object( 'variation', $variation_id );
                                        
                                        if ( !$variation ) {
                                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to get product object for variation_id: ", $variation_id );
                                            continue;
                                        }
                                        
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_send_task(
                                            $cryptocurrency_option,
                                            $order_id,
                                            $variation->get_id(),
                                            $marketAddress,
                                            $product_quantity
                                        );
                                    } else {
                                        // $id = $this->get_main_wpml_product_id();
                                        // do_action('woocommerce_simple_auction_won', $id);
                                        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_send_task(
                                            $cryptocurrency_option,
                                            $order_id,
                                            $product_id,
                                            $marketAddress,
                                            $product_quantity
                                        );
                                    }
                                
                                }
                            } catch ( Exception $ex ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_order: " . $ex->getMessage() );
                                // Неуспех - поставить себя снова в очередь +60сек
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_process_order_task( $order_id, 60 );
                            }
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_send_task(
                            $cryptocurrency_option,
                            $order_id,
                            $product_id,
                            $marketAddress,
                            $product_quantity
                        )
                        {
                            $order = wc_get_order( $order_id );
                            
                            if ( !$order ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Order {$order_id} was not found. Skip payment." );
                                return;
                            }
                            
                            
                            if ( !$order->has_status( "processing" ) ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Order {$order_id} status is not eligible for processing. Skip payment." );
                                return;
                            }
                            
                            
                            if ( 0 == $order->get_total() ) {
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Order {$order_id} has zero total value paid and is not eligible for processing. Skip payment." );
                                return;
                            }
                            
                            do_action(
                                'cryptocurrency_product_for_woocommerce_enqueue_send_task',
                                $cryptocurrency_option,
                                $order_id,
                                $product_id,
                                $marketAddress,
                                $product_quantity
                            );
                        }
                        
                        add_action(
                            'cryptocurrency_product_for_woocommerce_enqueue_send_task',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_enqueue_send_task_hook',
                            10,
                            5
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_enqueue_send_task_hook(
                            $cryptocurrency_option,
                            $order_id,
                            $product_id,
                            $marketAddress,
                            $product_quantity
                        )
                        {
                            if ( 'Ether' !== $cryptocurrency_option ) {
                                return;
                            }
                            // send Ether
                            
                            if ( CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_is_order_payed( $order_id, $product_id ) ) {
                                $txhash = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $order_id, 'ether_txhash_' . $product_id, true );
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Payment found for order {$order_id}, product {$product_id}: {$txhash}. Skip payment." );
                                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_complete_order_task( $order_id, $product_id );
                                return;
                            }
                            
                            $providerUrl = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getWeb3Endpoint();
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_enqueue_send_ether_task(
                                $order_id,
                                $product_id,
                                $marketAddress,
                                $product_quantity,
                                $providerUrl
                            );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_process_order_task( $order_id, $offset = 0 )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir ;
                            $timestamp = time() + $offset;
                            $hook = "CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_process_order";
                            $args = array( $order_id );
                            // @see https://github.com/woocommerce/action-scheduler/issues/730
                            
                            if ( !class_exists( 'ActionScheduler', false ) || !ActionScheduler::is_initialized() ) {
                                require_once $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir . '/vendor/woocommerce/action-scheduler/classes/abstracts/ActionScheduler.php';
                                ActionScheduler::init( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir . '/vendor/woocommerce/action-scheduler/action-scheduler.php' );
                            }
                            
                            $task_id = as_schedule_single_action( $timestamp, $hook, $args );
                            CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Task process_order with id {$task_id} scheduled for order: {$order_id}" );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getWeb3Endpoint()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $infuraApiKey = ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['infuraApiKey'] ) ? esc_attr( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['infuraApiKey'] ) : '' );
                            $blockchainNetwork = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainNetwork();
                            if ( empty($blockchainNetwork) ) {
                                $blockchainNetwork = 'mainnet';
                            }
                            $web3Endpoint = "https://" . esc_attr( $blockchainNetwork ) . ".infura.io/v3/" . esc_attr( $infuraApiKey );
                            return $web3Endpoint;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainCurrencyTicker()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $currency_ticker = "ETH";
                            return $currency_ticker;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_ether_product_type_disabled()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            return false;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_erc20_product_type_disabled()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            return false;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainCurrencyTickerName()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $currency_ticker_name = "Ether";
                            return $currency_ticker_name;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainName()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $blockchain_display_name = "Ethereum";
                            return $blockchain_display_name;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getTokenStandardName()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $token_standard_name = "ERC20";
                            return $token_standard_name;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_geEthereumAddressFieldLabel()
                        {
                            $blockchain_display_name = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainName();
                            return sprintf( __( '%1$s Address', 'cryptocurrency-product-for-woocommerce' ), $blockchain_display_name );
                            return $blockchain_display_name;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getExpirationPeriod()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            return 7;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            static  $_saved_chain_id = null ;
                            if ( is_null( $_saved_chain_id ) ) {
                                $_saved_chain_id = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId_impl();
                            }
                            return $_saved_chain_id;
                        }
                        
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getChainId_impl()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $blockchainNetwork = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainNetwork();
                            if ( empty($blockchainNetwork) ) {
                                $blockchainNetwork = 'mainnet';
                            }
                            $chainId = null;
                            switch ( $blockchainNetwork ) {
                                case 'mainnet':
                                    $chainId = 1;
                                    break;
                                case 'ropsten':
                                    $chainId = 3;
                                    break;
                                case 'rinkeby':
                                    $chainId = 4;
                                    break;
                                case 'bsc':
                                    $chainId = 56;
                                    break;
                                case 'bsctest':
                                    $chainId = 97;
                                    break;
                                case 'polygon':
                                    $chainId = 137;
                                    break;
                                case 'mumbai':
                                    $chainId = 80001;
                                    break;
                                default:
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Bad blockchain_network setting:" . $blockchainNetwork );
                                    break;
                            }
                            return $chainId;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainNetwork()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $blockchainNetwork = 'mainnet';
                            if ( !isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['blockchain_network'] ) ) {
                                return $blockchainNetwork;
                            }
                            if ( empty($CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['blockchain_network']) ) {
                                return $blockchainNetwork;
                            }
                            $blockchainNetwork = esc_attr( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['blockchain_network'] );
                            return $blockchainNetwork;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBalanceEth( $thisWalletAddress, $providerUrl, $eth = null )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            
                            if ( is_null( $eth ) ) {
                                $requestManager = new \Web3\RequestManagers\HttpRequestManager( $providerUrl, 10 );
                                $web3 = new \Web3\Web3( new \Web3\Providers\HttpProvider( $requestManager ) );
                                $eth = $web3->eth;
                            }
                            
                            $ether_balance_wei = null;
                            $eth->getBalance( $thisWalletAddress, function ( $err, $balance ) use( &$ether_balance_wei ) {
                                
                                if ( $err !== null ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to getBalance: " . $err );
                                    return;
                                }
                                
                                $ether_balance_wei = $balance;
                            } );
                            return $ether_balance_wei;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress( $product_id = null, $from_user_id = null )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $wallet = null;
                            
                            if ( !is_null( $from_user_id ) ) {
                                $vendor_id = $from_user_id;
                                
                                if ( user_can( $vendor_id, 'administrator' ) ) {
                                    if ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_address'] ) ) {
                                        $wallet = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_address'];
                                    }
                                } else {
                                    if ( user_can( $vendor_id, 'vendor' ) ) {
                                    }
                                }
                            
                            } else {
                                
                                if ( !is_null( $product_id ) ) {
                                    // background task processing
                                    $vendor_id = get_post_field( 'post_author_override', $product_id );
                                    if ( empty($vendor_id) ) {
                                        $vendor_id = get_post_field( 'post_author', $product_id );
                                    }
                                    
                                    if ( user_can( $vendor_id, 'administrator' ) ) {
                                        if ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_address'] ) ) {
                                            $wallet = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_address'];
                                        }
                                    } else {
                                        if ( user_can( $vendor_id, 'vendor' ) ) {
                                        }
                                    }
                                
                                }
                            
                            }
                            
                            if ( !is_null( $wallet ) && !empty($wallet) ) {
                                return esc_attr( $wallet );
                            }
                            
                            if ( current_user_can( 'administrator' ) ) {
                                if ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_address'] ) ) {
                                    $wallet = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_address'];
                                }
                            } else {
                                
                                if ( current_user_can( 'vendor' ) ) {
                                } else {
                                    $user_id = get_current_user_id();
                                    
                                    if ( $user_id <= 0 ) {
                                        // background task processing
                                        $vendor_id = get_post_field( 'post_author_override', $product_id );
                                        if ( empty($vendor_id) ) {
                                            $vendor_id = get_post_field( 'post_author', $product_id );
                                        }
                                        
                                        if ( user_can( $vendor_id, 'administrator' ) ) {
                                            if ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_address'] ) ) {
                                                $wallet = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_address'];
                                            }
                                        } else {
                                            if ( user_can( $vendor_id, 'vendor' ) ) {
                                            }
                                        }
                                    
                                    }
                                
                                }
                            
                            }
                            
                            if ( is_null( $wallet ) || empty($wallet) ) {
                                if ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_address'] ) ) {
                                    $wallet = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_address'];
                                }
                            }
                            if ( is_null( $wallet ) || empty($wallet) ) {
                                return null;
                            }
                            return esc_attr( $wallet );
                        }
                        
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletPrivateKey( $product_id = null, $from_user_id = null )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $privateKey = null;
                            
                            if ( !is_null( $from_user_id ) ) {
                                $vendor_id = $from_user_id;
                                
                                if ( user_can( $vendor_id, 'administrator' ) ) {
                                    if ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_private_key'] ) ) {
                                        $privateKey = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_private_key'];
                                    }
                                } else {
                                    if ( user_can( $vendor_id, 'vendor' ) ) {
                                    }
                                }
                            
                            } else {
                                
                                if ( !is_null( $product_id ) ) {
                                    // background task processing
                                    $vendor_id = get_post_field( 'post_author_override', $product_id );
                                    if ( empty($vendor_id) ) {
                                        $vendor_id = get_post_field( 'post_author', $product_id );
                                    }
                                    
                                    if ( user_can( $vendor_id, 'administrator' ) ) {
                                        if ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_private_key'] ) ) {
                                            $privateKey = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_private_key'];
                                        }
                                    } else {
                                        if ( user_can( $vendor_id, 'vendor' ) ) {
                                        }
                                    }
                                
                                }
                            
                            }
                            
                            if ( !is_null( $privateKey ) && !empty($privateKey) ) {
                                return esc_attr( $privateKey );
                            }
                            
                            if ( current_user_can( 'administrator' ) ) {
                                if ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_private_key'] ) ) {
                                    $privateKey = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_private_key'];
                                }
                            } else {
                                
                                if ( current_user_can( 'vendor' ) ) {
                                } else {
                                    $user_id = get_current_user_id();
                                    
                                    if ( $user_id <= 0 ) {
                                        // background task processing
                                        $vendor_id = get_post_field( 'post_author_override', $product_id );
                                        if ( empty($vendor_id) ) {
                                            $vendor_id = get_post_field( 'post_author', $product_id );
                                        }
                                        
                                        if ( user_can( $vendor_id, 'administrator' ) ) {
                                            if ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_private_key'] ) ) {
                                                $privateKey = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_private_key'];
                                            }
                                        } else {
                                            if ( user_can( $vendor_id, 'vendor' ) ) {
                                            }
                                        }
                                    
                                    }
                                
                                }
                            
                            }
                            
                            if ( is_null( $privateKey ) || empty($privateKey) ) {
                                if ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_private_key'] ) ) {
                                    $privateKey = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_private_key'];
                                }
                            }
                            if ( is_null( $privateKey ) || empty($privateKey) ) {
                                return null;
                            }
                            return esc_attr( $privateKey );
                        }
                        
                        //----------------------------------------------------------------------------//
                        //                            Enqueue Scripts                                 //
                        //----------------------------------------------------------------------------//
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_user_wallet()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            $value = '';
                            if ( !isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_meta'] ) ) {
                                return $value;
                            }
                            $userWalletMetaKey = esc_attr( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_meta'] );
                            
                            if ( !empty($userWalletMetaKey) ) {
                                // https://stackoverflow.com/a/19722500/4256005
                                $user_id = get_current_user_id();
                                $key = $userWalletMetaKey;
                                $single = true;
                                $value = get_user_meta( $user_id, $key, $single );
                                if ( empty($value) ) {
                                    $value = get_user_meta( $user_id, 'billing_cryptocurrency_ethereum_address', $single );
                                }
                            }
                            
                            return $value;
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_scripts_()
                        {
                            wp_enqueue_script( 'cryptocurrency-product-for-woocommerce' );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_script()
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_url_path ;
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            global  $post ;
                            $options = $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options;
                            $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
                            
                            if ( !wp_script_is( 'web3', 'queue' ) && !wp_script_is( 'web3', 'done' ) ) {
                                wp_dequeue_script( 'web3' );
                                wp_deregister_script( 'web3' );
                                wp_register_script(
                                    'web3',
                                    $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_url_path . "/web3.min.js",
                                    array( 'jquery' ),
                                    '1.3.4'
                                );
                            }
                            
                            
                            if ( !wp_script_is( 'bignumber', 'queue' ) && !wp_script_is( 'bignumber', 'done' ) ) {
                                wp_dequeue_script( 'bignumber' );
                                wp_deregister_script( 'bignumber' );
                                wp_register_script(
                                    'bignumber',
                                    $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_url_path . "/bignumber.min.js",
                                    array( 'jquery' ),
                                    '9.0.1'
                                );
                            }
                            
                            
                            if ( !wp_script_is( 'cryptocurrency-product-for-woocommerce', 'queue' ) && !wp_script_is( 'cryptocurrency-product-for-woocommerce', 'done' ) ) {
                                wp_dequeue_script( 'cryptocurrency-product-for-woocommerce' );
                                wp_deregister_script( 'cryptocurrency-product-for-woocommerce' );
                                wp_register_script(
                                    'cryptocurrency-product-for-woocommerce',
                                    $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_url_path . "/cryptocurrency-product-for-woocommerce{$min}.js",
                                    array( 'jquery', 'web3', 'bignumber' ),
                                    '3.14.0'
                                );
                            }
                            
                            $product_id = null;
                            
                            if ( $post ) {
                                $product = wc_get_product( $post->ID );
                                if ( $product ) {
                                    $product_id = $product->get_id();
                                }
                            }
                            
                            $thisWalletAddress = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getHotWalletAddress( $product_id );
                            if ( is_null( $thisWalletAddress ) ) {
                                $thisWalletAddress = '';
                            }
                            wp_localize_script( 'cryptocurrency-product-for-woocommerce', 'cryptocurrency', apply_filters( 'cryptocurrency_product_for_woocommerce_wp_localize_script', [
                                'web3Endpoint'  => esc_html( CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getWeb3Endpoint() ),
                                'walletAddress' => esc_html( $thisWalletAddress ),
                            ] ) );
                        }
                        
                        add_action( 'admin_enqueue_scripts', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_enqueue_script' );
                        //----------------------------------------------------------------------------//
                        //                               Admin Options                                //
                        //----------------------------------------------------------------------------//
                        if ( is_admin() ) {
                            include_once $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugin_dir . '/cryptocurrency-product-for-woocommerce.admin.php';
                        }
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_add_menu_link()
                        {
                            $page = add_options_page(
                                __( 'Cryptocurrency Product Settings', 'cryptocurrency-product-for-woocommerce' ),
                                __( 'Cryptocurrency Product', 'cryptocurrency-product-for-woocommerce' ),
                                'manage_options',
                                'cryptocurrency-product-for-woocommerce',
                                'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options_page'
                            );
                        }
                        
                        add_filter( 'admin_menu', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_add_menu_link' );
                        // Place in Option List on Settings > Plugins page
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_actlinks( $links, $file )
                        {
                            // Static so we don't call plugin_basename on every plugin row.
                            static  $this_plugin ;
                            if ( !$this_plugin ) {
                                $this_plugin = plugin_basename( __FILE__ );
                            }
                            
                            if ( $file == $this_plugin ) {
                                $settings_link = '<a href="options-general.php?page=cryptocurrency-product-for-woocommerce">' . __( 'Settings' ) . '</a>';
                                array_unshift( $links, $settings_link );
                                // before other links
                            }
                            
                            return $links;
                        }
                        
                        add_filter(
                            'plugin_action_links',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_actlinks',
                            10,
                            2
                        );
                        //----------------------------------------------------------------------------//
                        //                Use decimal in quantity fields in WooCommerce               //
                        //----------------------------------------------------------------------------//
                        // @see: http://codeontrack.com/use-decimal-in-quantity-fields-in-woocommerce-wordpress/
                        add_action( 'plugins_loaded', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugins_loaded' );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_plugins_loaded()
                        {
                            // Removes the WooCommerce filter, that is validating the quantity to be an int
                            remove_filter( 'woocommerce_stock_amount', 'intval' );
                            // Add a filter, that validates the quantity to be a float
                            add_filter( 'woocommerce_stock_amount', 'floatval' );
                        }
                        
                        //// Add unit price fix when showing the unit price on processed orders
                        //add_filter('woocommerce_order_amount_item_total', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_amount_item_total', 10, 5);
                        //function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_amount_item_total($total, $order, $item, $inc_tax = false, $round = true) {
                        //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_order_amount_item_total: price=$total, item=" . print_r($item, true));
                        //    return $total;
                        ////    $product = $item->get_product();
                        ////    $new_total = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_price( $total, $product );
                        ////    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_order_amount_item_total: new_total=$new_total");
                        ////    return $new_total;
                        //}
                        //add_filter('woocommerce_order_amount_item_subtotal', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_amount_item_subtotal', 10, 5);
                        //function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_amount_item_subtotal($subtotal, $order, $item, $inc_tax = false, $round = true) {
                        //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_order_amount_item_subtotal: subtotal=$subtotal, item=" . print_r($item, true));
                        //    $product = $item->get_product();
                        //    $new_subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_price( $subtotal, $product );
                        //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_order_amount_item_subtotal: new_subtotal=$new_subtotal");
                        //    return $new_subtotal;
                        //}
                        //add_filter('woocommerce_order_get_total', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_get_total', 10, 2);
                        //function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_get_total($total, $order) {
                        ////    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_order_get_total: total=$total");
                        ////    return $total;
                        //    $new_total = 0;
                        //    foreach ( $order->get_items() as $item ) {
                        //        $new_total += $item->get_total();
                        //    }
                        ////    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_order_get_total: new_total=$new_total");
                        //    return $new_total;
                        //}
                        //// Add unit price fix when showing the unit price on processed orders
                        //add_filter('woocommerce_order_item_get_total', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_item_get_total', 10, 2);
                        //function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_item_get_total($total, $item) {
                        //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_order_item_get_total: total=$total");
                        //    return $total;
                        ////    $product = $item->get_product();
                        ////    $new_total = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_price( $total, $product );
                        ////    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_order_item_get_total: new_total=$new_total");
                        ////    return $new_total;
                        //}
                        //add_filter('woocommerce_order_item_get_subtotal', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_item_get_subtotal', 10, 2);
                        //function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_order_item_get_subtotal($subtotal, $item) {
                        //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_order_item_get_subtotal: subtotal=$subtotal");
                        //    return $subtotal;
                        ////    $product = $item->get_product();
                        ////    $new_subtotal = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_product_get_price( $subtotal, $product );
                        ////    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_order_item_get_subtotal: new_subtotal=$new_subtotal");
                        ////    return $new_subtotal;
                        //}
                        //add_filter('woocommerce_get_formatted_order_total', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_formatted_order_total', 10, 2);
                        //function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_formatted_order_total($formatted_total, $order/*, $tax_display, $display_refunded*/) {
                        //    $order_total     = $order->get_total();
                        ////    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_get_formatted_order_total:  formatted_total: $formatted_total, tax_display: $tax_display, display_refunded: $display_refunded");
                        //    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("woocommerce_get_formatted_order_total: order_total: $order_total, formatted_total: $formatted_total");
                        //    return $formatted_total;
                        //}
                        add_filter(
                            'cryptocurrency_product_for_woocommerce_woocommerce_quantity_input_min',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_woocommerce_quantity_input_min_hook',
                            20,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_woocommerce_quantity_input_min_hook( $min, $cryptocurrency_option, $product_id )
                        {
                            if ( 'Ether' !== $cryptocurrency_option ) {
                                return $min;
                            }
                            return 0.001;
                        }
                        
                        // define the woocommerce_quantity_input_min callback
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_quantity_input_min( $min, $product )
                        {
                            if ( !$product ) {
                                return $min;
                            }
                            $product_id = $product->get_id();
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product_id ) ) {
                                return $min;
                            }
                            $minimumValue = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, '_text_input_cryptocurrency_minimum_value', true );
                            if ( !empty($minimumValue) ) {
                                return floatval( $minimumValue );
                            }
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, '_select_cryptocurrency_option', true );
                            return apply_filters(
                                'cryptocurrency_product_for_woocommerce_woocommerce_quantity_input_min',
                                $min,
                                $cryptocurrency_option,
                                $product_id
                            );
                        }
                        
                        add_filter(
                            'woocommerce_quantity_input_min',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_quantity_input_min',
                            10,
                            2
                        );
                        // define the woocommerce_quantity_input_max callback
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_quantity_input_max( $max, $product )
                        {
                            if ( !$product ) {
                                return $max;
                            }
                            $product_id = $product->get_id();
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product_id ) ) {
                                return $max;
                            }
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, '_select_cryptocurrency_option', true );
                            return apply_filters(
                                'cryptocurrency_product_for_woocommerce_woocommerce_quantity_input_max',
                                $max,
                                $cryptocurrency_option,
                                $product_id
                            );
                        }
                        
                        add_filter(
                            'woocommerce_quantity_input_max',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_quantity_input_max',
                            10,
                            2
                        );
                        add_filter(
                            'cryptocurrency_product_for_woocommerce_woocommerce_quantity_input_step',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_woocommerce_quantity_input_step_hook',
                            20,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_woocommerce_quantity_input_step_hook( $step, $cryptocurrency_option, $product_id )
                        {
                            if ( 'Ether' !== $cryptocurrency_option ) {
                                return $step;
                            }
                            return 1.0E-5;
                        }
                        
                        // define the woocommerce_quantity_input_step callback
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_quantity_input_step( $step, $product )
                        {
                            if ( !$product ) {
                                return $step;
                            }
                            $product_id = $product->get_id();
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product_id ) ) {
                                return $step;
                            }
                            $step = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, '_text_input_cryptocurrency_step', true );
                            if ( !empty($step) ) {
                                return floatval( $step );
                            }
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_post_meta( $product_id, '_select_cryptocurrency_option', true );
                            return apply_filters(
                                'cryptocurrency_product_for_woocommerce_woocommerce_quantity_input_step',
                                $step,
                                $cryptocurrency_option,
                                $product_id
                            );
                        }
                        
                        add_filter(
                            'woocommerce_quantity_input_step',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_quantity_input_step',
                            10,
                            2
                        );
                        //----------------------------------------------------------------------------//
                        //                                   L10n                                     //
                        //----------------------------------------------------------------------------//
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_load_textdomain()
                        {
                            /**
                             * Localise.
                             */
                            load_plugin_textdomain( 'cryptocurrency-product-for-woocommerce', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
                        }
                        
                        add_action( 'plugins_loaded', 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_load_textdomain' );
                        //----------------------------------------------------------------------------//
                        //                      Ethereum address verification                         //
                        //----------------------------------------------------------------------------//
                        add_action(
                            'woocommerce_after_checkout_validation',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_after_checkout_validation',
                            10,
                            2
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_after_checkout_validation( $data, $errors )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            if ( !_CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency_product_in_cart() ) {
                                return;
                            }
                            $cryptocurrency_option = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_type_in_cart();
                            $product_id = _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_cryptocurrency_product_id_in_cart();
                            do_action(
                                'cryptocurrency_product_for_woocommerce_after_checkout_validation',
                                $cryptocurrency_option,
                                $product_id,
                                $data,
                                $errors
                            );
                        }
                        
                        function _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wei_to_ether( $balance )
                        {
                            $powDecimals = new phpseclib3\Math\BigInteger( pow( 10, 18 ) );
                            list( $q, $r ) = $balance->divide( $powDecimals );
                            $sR = $r->toString();
                            $tokenDecimalChar = '.';
                            $tokenDecimals = 18;
                            $strBalanceDecimals = sprintf( '%018s', $sR );
                            $strBalanceDecimals2 = substr( $strBalanceDecimals, 0, $tokenDecimals );
                            
                            if ( str_pad( "", $tokenDecimals, "0" ) == $strBalanceDecimals2 ) {
                                $strBalance = rtrim( $q->toString() . $tokenDecimalChar . $strBalanceDecimals, '0' );
                            } else {
                                $strBalance = rtrim( $q->toString() . $tokenDecimalChar . $strBalanceDecimals2, '0' );
                            }
                            
                            $strBalance = rtrim( $strBalance, $tokenDecimalChar );
                            return $strBalance;
                        }
                        
                        add_filter(
                            'cryptocurrency_product_for_woocommerce_after_checkout_validation',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_override_checkout_fields',
                            20,
                            4
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ETHER_ALL_after_checkout_validation(
                            $cryptocurrency_option,
                            $product_id,
                            $data,
                            $errors
                        )
                        {
                            global  $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options ;
                            if ( "ETHEREUM" !== apply_filters( 'cryptocurrency_product_for_woocommerce_get_base_blockchain', '', $cryptocurrency_option ) ) {
                                return;
                            }
                            $user_id = get_current_user_id();
                            $walletDisabled = ( isset( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_field_disable'] ) ? esc_attr( $CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options['wallet_field_disable'] ) : '' );
                            $isWalletFieldDisabled = !empty($walletDisabled);
                            if ( !($user_id > 0 || $isWalletFieldDisabled || !function_exists( 'ETHEREUM_WALLET_create_account' ) || !WC()->checkout()->is_registration_required()) ) {
                                // TODO: гость ввёл fd9422a2-eb92-46d7-a490-25ac1211a4e6 вместо адреса и смог оплатить биткойном
                                // Wallet плагин установлен
                                // "Allow customers to place orders without an account" is checked
                                return;
                            }
                            $value = (string) $data['billing_cryptocurrency_ethereum_address'];
                            if ( \Web3\Utils::isAddress( $value ) ) {
                                return;
                            }
                            // Do your data processing here and in case of an
                            // error add it to the errors array like:
                            $blockchain_display_name = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_getBlockchainName();
                            $error = sprintf( __( 'Please input correct %1$s address in the form like 0x476Bb28Bc6D0e9De04dB5E19912C392F9a76535d.', 'cryptocurrency-product-for-woocommerce' ), $blockchain_display_name );
                            $errors->add( 'validation', $error );
                        }
                        
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_gas_estimate( $transactionParamsArray, $eth )
                        {
                            $gasEstimate = null;
                            $error = null;
                            $transactionParamsArrayCopy = $transactionParamsArray;
                            unset( $transactionParamsArrayCopy['nonce'] );
                            unset( $transactionParamsArrayCopy['chainId'] );
                            // CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("ETHEREUM_WALLET_get_gas_estimate: " . print_r($transactionParamsArray, true));
                            // CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log("ETHEREUM_WALLET_get_gas_estimate2: " . print_r($transactionParamsArrayCopy, true));
                            $eth->estimateGas( $transactionParamsArrayCopy, function ( $err, $gas ) use( &$gasEstimate, &$error ) {
                                
                                if ( $err !== null ) {
                                    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( "Failed to estimateGas: " . $err );
                                    $error = $err;
                                    return;
                                }
                                
                                $gasEstimate = $gas;
                            } );
                            return [ $error, $gasEstimate ];
                        }
                        
                        add_action(
                            'woocommerce_order_item_needs_processing',
                            'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_item_needs_processing',
                            10,
                            3
                        );
                        function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_woocommerce_order_item_needs_processing( $needs_procesing, $product, $order_id )
                        {
                            return $needs_procesing || _CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_is_cryptocurrency( $product->get_id() );
                        }
                    
                    }
                    
                    //if ( ! function_exists( 'cryptocurrency_product_for_woocommerce_freemius_init' ) ) {
                }
                
                // WooCommerce activated
            }
        
        }
    
    }

}

// PHP version