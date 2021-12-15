<?php

/*
Plugin Name: Ethereum Wallet
Plugin URI: https://wordpress.org/plugins/ethereum-wallet/
Description: Wallet for Ether and ERC20 tokens for WordPress
Version: 4.0.8
WC requires at least: 5.5.0
WC tested up to: 5.8.0
Author: ethereumicoio
Text Domain: ethereum-wallet
Domain Path: /languages
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

//namespace \Ethereumico\EthereumWallet;
// 0x2185043691F82Ca1F939F0eCd62Ce96F84F012E9
use  Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger ;
use  Ethereumico\EthereumWallet\Dependencies\kornrunner\Keccak ;
use  Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory ;
use  Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer ;
use  Ethereumico\EthereumWallet\Dependencies\Web3p\EthereumTx\Transaction ;
use  Ethereumico\EthereumWallet\Dependencies\Web3\Web3 ;
use  Ethereumico\EthereumWallet\Dependencies\Web3\Providers\HttpProvider ;
use  Ethereumico\EthereumWallet\Dependencies\Web3\RequestManagers\HttpRequestManager ;
use  Ethereumico\EthereumWallet\Dependencies\Web3\Contract ;
use  Ethereumico\EthereumWallet\CurrencyConvertor ;
// Explicitly globalize to support bootstrapped WordPress
global 
    $ETHEREUM_WALLET_plugin_basename,
    $ETHEREUM_WALLET_options,
    $ETHEREUM_WALLET_plugin_dir,
    $ETHEREUM_WALLET_plugin_url_path,
    $ETHEREUM_WALLET_services,
    $ETHEREUM_WALLET_amp_icons_css
;
if ( !function_exists( 'ETHEREUM_WALLET_deactivate' ) ) {
    function ETHEREUM_WALLET_deactivate()
    {
        if ( !current_user_can( 'activate_plugins' ) ) {
            return;
        }
        deactivate_plugins( plugin_basename( __FILE__ ) );
    }

}

if ( PHP_INT_SIZE != 8 ) {
    add_action( 'admin_init', 'ETHEREUM_WALLET_deactivate' );
    add_action( 'admin_notices', 'ETHEREUM_WALLET_admin_notice' );
    function ETHEREUM_WALLET_admin_notice()
    {
        if ( !current_user_can( 'activate_plugins' ) ) {
            return;
        }
        echo  '<div class="error"><p><strong>WordPress Ethereum Wallet</strong> requires 64 bit architecture server.</p></div>' ;
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }

} else {
    
    if ( version_compare( phpversion(), '7.0', '<' ) ) {
        add_action( 'admin_init', 'ETHEREUM_WALLET_deactivate' );
        add_action( 'admin_notices', 'ETHEREUM_WALLET_admin_notice' );
        function ETHEREUM_WALLET_admin_notice()
        {
            if ( !current_user_can( 'activate_plugins' ) ) {
                return;
            }
            echo  '<div class="error"><p><strong>WordPress Ethereum Wallet</strong> requires PHP version 7.0 or above.</p></div>' ;
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
        }
    
    } else {
        
        if ( !function_exists( 'gmp_init' ) ) {
            add_action( 'admin_init', 'ETHEREUM_WALLET_deactivate' );
            add_action( 'admin_notices', 'ETHEREUM_WALLET_admin_notice_gmp' );
            function ETHEREUM_WALLET_admin_notice_gmp()
            {
                if ( !current_user_can( 'activate_plugins' ) ) {
                    return;
                }
                echo  '<div class="error"><p><strong>WordPress Ethereum Wallet</strong> requires <a href="http://php.net/manual/en/book.gmp.php">GMP</a> module to be installed.</p></div>' ;
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
        
        } else {
            
            if ( !function_exists( 'mb_strtolower' ) ) {
                add_action( 'admin_init', 'ETHEREUM_WALLET_deactivate' );
                add_action( 'admin_notices', 'ETHEREUM_WALLET_admin_notice_mbstring' );
                function ETHEREUM_WALLET_admin_notice_mbstring()
                {
                    if ( !current_user_can( 'activate_plugins' ) ) {
                        return;
                    }
                    echo  '<div class="error"><p><strong>WordPress Ethereum Wallet</strong> requires <a href="http://php.net/manual/en/book.mbstring.php">Multibyte String (mbstring)</a> module to be installed.</p></div>' ;
                    if ( isset( $_GET['activate'] ) ) {
                        unset( $_GET['activate'] );
                    }
                }
            
            } else {
                // @see https://github.com/woocommerce/action-scheduler/issues/730
                require_once dirname( __FILE__ ) . '/vendor/woocommerce/action-scheduler/action-scheduler.php';
                
                if ( function_exists( 'ethereum_wallet_freemius_init' ) ) {
                    ethereum_wallet_freemius_init()->set_basename( false, __FILE__ );
                } else {
                    if ( !function_exists( 'ethereum_wallet_freemius_init' ) ) {
                        
                        if ( !function_exists( 'ethereum_wallet_freemius_init' ) ) {
                            // Create a helper function for easy SDK access.
                            function ethereum_wallet_freemius_init()
                            {
                                global  $ethereum_wallet_freemius_init ;
                                
                                if ( !isset( $ethereum_wallet_freemius_init ) ) {
                                    // Include Freemius SDK.
                                    require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
                                    $ethereum_wallet_freemius_init = fs_dynamic_init( array(
                                        'id'              => '4542',
                                        'slug'            => 'ethereum-wallet',
                                        'type'            => 'plugin',
                                        'public_key'      => 'pk_912bef5e630149f746bec10d0a6d2',
                                        'is_premium'      => false,
                                        'premium_suffix'  => 'Professional',
                                        'has_addons'      => false,
                                        'has_paid_plans'  => true,
                                        'trial'           => array(
                                        'days'               => 7,
                                        'is_require_payment' => true,
                                    ),
                                        'has_affiliation' => 'all',
                                        'menu'            => array(
                                        'slug'   => 'ethereum-wallet',
                                        'parent' => array(
                                        'slug' => 'options-general.php',
                                    ),
                                    ),
                                        'is_live'         => true,
                                    ) );
                                }
                                
                                return $ethereum_wallet_freemius_init;
                            }
                            
                            // Init Freemius.
                            ethereum_wallet_freemius_init();
                            // Signal that SDK was initiated.
                            do_action( 'ethereum_wallet_freemius_init_loaded' );
                        }
                    
                    }
                    // ... Your plugin's main file logic ...
                    $ETHEREUM_WALLET_plugin_basename = plugin_basename( dirname( __FILE__ ) );
                    $ETHEREUM_WALLET_plugin_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
                    $ETHEREUM_WALLET_plugin_url_path = untrailingslashit( plugin_dir_url( __FILE__ ) );
                    // HTTPS?
                    $ETHEREUM_WALLET_plugin_url_path = ( is_ssl() ? str_replace( 'http:', 'https:', $ETHEREUM_WALLET_plugin_url_path ) : $ETHEREUM_WALLET_plugin_url_path );
                    // Set plugin options
                    $ETHEREUM_WALLET_options = get_option( 'ethereum-wallet_options', array() );
                    require $ETHEREUM_WALLET_plugin_dir . '/vendor/autoload.php';
                    require $ETHEREUM_WALLET_plugin_dir . '/currencyconvertor.php';
                    function ETHEREUM_WALLET_init()
                    {
                        global  $ETHEREUM_WALLET_plugin_dir, $ETHEREUM_WALLET_plugin_basename, $ETHEREUM_WALLET_options ;
                        // Load the textdomain for translations
                        load_plugin_textdomain( 'ethereum-wallet', false, $ETHEREUM_WALLET_plugin_basename . '/languages/' );
                    }
                    
                    add_filter( 'init', 'ETHEREUM_WALLET_init' );
                    // Takes a hex (string) address as input
                    function ETHEREUM_WALLET_checksum_encode( $addr_str )
                    {
                        $out = array();
                        $addr = str_replace( '0x', '', strtolower( $addr_str ) );
                        $addr_array = str_split( $addr );
                        $hash_addr = Keccak::hash( $addr, 256 );
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
                    function ETHEREUM_WALLET_address_from_key( $privateKeyHex )
                    {
                        $privateKeyFactory = new PrivateKeyFactory();
                        $privateKey = $privateKeyFactory->fromHexUncompressed( $privateKeyHex );
                        $pubKeyHex = $privateKey->getPublicKey()->getHex();
                        $hash = Keccak::hash( substr( hex2bin( $pubKeyHex ), 1 ), 256 );
                        $ethAddress = '0x' . substr( $hash, 24 );
                        $ethAddressChkSum = ETHEREUM_WALLET_checksum_encode( $ethAddress );
                        return $ethAddressChkSum;
                    }
                    
                    function ETHEREUM_WALLET_create_account()
                    {
                        $random = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\Random();
                        $privateKeyBuffer = $random->bytes( 32 );
                        $privateKeyHex = $privateKeyBuffer->getHex();
                        $ethAddressChkSum = ETHEREUM_WALLET_address_from_key( $privateKeyHex );
                        return [ $ethAddressChkSum, $privateKeyHex ];
                    }
                    
                    // REST API
                    require_once $ETHEREUM_WALLET_plugin_dir . '/rest/api.php';
                    add_action( 'rest_api_init', function () {
                        do_action( 'ethereum_wallet_rest_api_endpoint' );
                    } );
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    function ETHEREUM_WALLET_get_user_id_by_wallet( $account )
                    {
                        $user_ids = get_users( array(
                            'fields'      => 'ids',
                            'number'      => 1,
                            'count_total' => false,
                            'meta_key'    => 'user_ethereum_wallet_address',
                            'meta_value'  => ETHEREUM_WALLET_checksum_encode( $account ),
                        ) );
                        //    ETHEREUM_WALLET_log("ETHEREUM_WALLET_get_user_by_wallet($account): " . print_r($user_ids, true));
                        if ( empty($user_ids) ) {
                            return null;
                        }
                        return reset( $user_ids );
                    }
                    
                    // $fromAddressPath = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_address_path($from, $blockchainNetwork);
                    function ETHEREUM_WALLET_get_user_page_url( $ownerURL, $user_id )
                    {
                        // ETHEREUM_WALLET_log("ETHEREUM_WALLET_get_token_owner_page_url( $user_id )");
                        if ( is_null( $user_id ) ) {
                            return $ownerURL;
                        }
                        // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                        return $ownerURL;
                    }
                    
                    function ETHEREUM_WALLET_get_user_name( $from_user_id )
                    {
                        $fromUser = get_user_by( 'id', $from_user_id );
                        $fromVendorName = ( $fromUser ? $fromUser->display_name : '' );
                        if ( empty($fromVendorName) ) {
                            $fromVendorName = ( $fromUser ? $fromUser->user_login : '' );
                        }
                        return $fromVendorName;
                    }
                    
                    function ETHEREUM_WALLET_get_avatar_url( $from_user_id )
                    {
                        $fromAvatarUrl = null;
                        // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                        if ( is_null( $fromAvatarUrl ) ) {
                            $fromAvatarUrl = get_avatar_url( $from_user_id );
                        }
                        return $fromAvatarUrl;
                    }
                    
                    // create Ethereum wallet on user register
                    // see https://wp-kama.ru/hook/user_register
                    add_action( 'user_register', 'ETHEREUM_WALLET_user_registration' );
                    function ETHEREUM_WALLET_user_registration( $user_id )
                    {
                        $accountsJSON = get_user_meta( $user_id, 'user_ethereum_wallet_accounts', true );
                        
                        if ( !empty($accountsJSON) ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_user_registration: account already exists" );
                            return;
                        }
                        
                        list( $ethAddressChkSum, $privateKeyHex ) = ETHEREUM_WALLET_create_account();
                        $accounts = [ [
                            "name"    => __( 'Default account', 'ethereum-wallet' ),
                            "address" => $ethAddressChkSum,
                            "key"     => $privateKeyHex,
                        ] ];
                        // @see https://stackoverflow.com/a/44263857/4256005
                        update_user_meta( $user_id, 'user_ethereum_wallet_accounts', json_encode( $accounts, JSON_UNESCAPED_UNICODE ) );
                        // set default account
                        update_user_meta( $user_id, 'user_ethereum_wallet_address', $ethAddressChkSum );
                        update_user_meta( $user_id, 'user_ethereum_wallet_key', $privateKeyHex );
                    }
                    
                    function ETHEREUM_WALLET_calc_display_value( $value )
                    {
                        if ( $value < 1 ) {
                            return [ 0.01 * floor( 100 * $value ), __( ETHEREUM_WALLET_getCurrencyTicker(), 'ethereum-wallet' ) ];
                        }
                        if ( $value < 1000 ) {
                            return [ 0.1 * floor( 10 * $value ), __( ETHEREUM_WALLET_getCurrencyTicker(), 'ethereum-wallet' ) ];
                        }
                        if ( $value < 1000000 ) {
                            return [ 0.1 * floor( 10 * 0.001 * $value ), __( 'K', 'ethereum-wallet' ) ];
                        }
                        return [ 0.1 * floor( 10 * 1.0E-6 * $value ), __( 'M', 'ethereum-wallet' ) ];
                    }
                    
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    // function ETHEREUM_WALLET_etherscan_query_gas_price_gwei() {
                    //     $apiEndpoint = ETHEREUM_WALLET_get_gas_price_api_url();
                    //     $response = wp_remote_get( $apiEndpoint, array('sslverify' => false) );
                    //     if( is_wp_error( $response ) ) {
                    //         ETHEREUM_WALLET_log("Error in gasPriceOracle response: ", $response);
                    //         return ETHEREUM_WALLET_get_default_gas_price_gwei();
                    //     }
                    //
                    //     $http_code = wp_remote_retrieve_response_code( $response );
                    //     if (200 != $http_code) {
                    //         ETHEREUM_WALLET_log("Bad response code in gasPriceOracle response: ", $http_code);
                    //         return ETHEREUM_WALLET_get_default_gas_price_gwei();
                    //     }
                    //
                    //     $body = wp_remote_retrieve_body( $response );
                    //     if (!$body) {
                    //         ETHEREUM_WALLET_log("empty body in gasPriceOracle response");
                    //         return ETHEREUM_WALLET_get_default_gas_price_gwei();
                    //     }
                    //
                    //     $j = json_decode($body, true);
                    //     if (!isset($j["result"])) {
                    //         ETHEREUM_WALLET_log("No result field. body: " . $body);
                    //         return ETHEREUM_WALLET_get_default_gas_price_gwei();
                    //     }
                    //
                    //     $j = $j["result"];
                    //     if (!isset($j["ProposeGasPrice"])) {
                    //         ETHEREUM_WALLET_log("no ProposeGasPrice field in gasPriceOracle response");
                    //         return ETHEREUM_WALLET_get_default_gas_price_gwei();
                    //     }
                    //
                    //     $gasPriceGwei = $j["ProposeGasPrice"];
                    //     $cache_gas_price = array('tm' => time(), 'gas_price' => $gasPriceGwei, 'gas_prices' =>[
                    //         'slow' => $j["SafeGasPrice"],
                    //         'safe' => $j["ProposeGasPrice"],
                    //         'fast' => $j["FastGasPrice"],
                    //     ]);
                    //
                    //     if ( get_option('ethereumicoio_cache_gas_price') ) {
                    //         update_option('ethereumicoio_cache_gas_price', $cache_gas_price);
                    //     } else {
                    //         $deprecated='';
                    //         $autoload='no';
                    //         add_option('ethereumicoio_cache_gas_price', $cache_gas_price, $deprecated, $autoload);
                    //     }
                    //     return $cache_gas_price;
                    // }
                    class ETHEREUM_WALLET_Logger
                    {
                        /**
                         * Add a log entry.
                         *
                         * This is not the preferred method for adding log messages. Please use log() or any one of
                         * the level methods (debug(), info(), etc.). This method may be deprecated in the future.
                         *
                         * @param string $handle
                         * @param string $message
                         * @param string $level
                         *
                         * @see https://docs.woocommerce.com/wc-apidocs/source-class-WC_Logger.html#105
                         *
                         * @return bool
                         */
                        public function add( $handle, $message, $level = 'unused' )
                        {
                            error_log( $handle . ': ' . $message );
                            return true;
                        }
                    
                    }
                    function ETHEREUM_WALLET_log( $error )
                    {
                        static  $logger = false ;
                        // Create a logger instance if we don't already have one.
                        if ( false === $logger ) {
                            /**
                             * Check if WooCommerce is active
                             * https://wordpress.stackexchange.com/a/193908/137915
                             **/
                            
                            if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && class_exists( "WC_Logger", false ) ) {
                                $logger = new WC_Logger();
                            } else {
                                $logger = new ETHEREUM_WALLET_Logger();
                            }
                        
                        }
                        $logger->add( 'ethereum-wallet', $error );
                    }
                    
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    function ETHEREUM_WALLET_getBalanceEth( $providerUrl, $accountAddress, $web3 = null )
                    {
                        $ether_quantity_wei = null;
                        $error = null;
                        try {
                            
                            if ( is_null( $web3 ) ) {
                                $requestManager = new HttpRequestManager( $providerUrl, 10 );
                                $web3 = new Web3( new HttpProvider( $requestManager ) );
                            }
                            
                            $eth = $web3->eth;
                            $eth->getBalance( $accountAddress, function ( $err, $balance ) use( &$ether_quantity_wei, &$error ) {
                                
                                if ( $err !== null ) {
                                    ETHEREUM_WALLET_log( "Failed to getBalance: " . $err );
                                    $error = $err;
                                    return;
                                }
                                
                                $ether_quantity_wei = $balance;
                            } );
                            return [ $error, $ether_quantity_wei ];
                        } catch ( \Exception $ex ) {
                            ETHEREUM_WALLET_log( $ex->getMessage() );
                            if ( is_null( $error ) ) {
                                $error = $ex->getMessage();
                            }
                            return [ $error, null ];
                        }
                    }
                    
                    function ETHEREUM_WALLET_getLatestBlock( $eth )
                    {
                        static  $_block_saved = null ;
                        $error = null;
                        
                        if ( is_null( $_block_saved ) ) {
                            $block = null;
                            $eth->getBlockByNumber( 'latest', false, function ( $err, $_block ) use( &$block, &$error ) {
                                
                                if ( $err !== null ) {
                                    ETHEREUM_WALLET_log( "Failed to getBlockByNumber: " . $err );
                                    $error = $err;
                                    return;
                                }
                                
                                $block = $_block;
                                // ETHEREUM_WALLET_log("ETHEREUM_WALLET_getLatestBlock: " . print_r($_block, true));
                                // ETHEREUM_WALLET_log("ETHEREUM_WALLET_getLatestBlock: latest");
                            } );
                            $_block_saved = $block;
                        }
                        
                        return [ $error, $_block_saved ];
                    }
                    
                    function ETHEREUM_WALLET_getWeb3Endpoint()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $infuraApiKey = '';
                        if ( isset( $ETHEREUM_WALLET_options['infuraApiKey'] ) ) {
                            $infuraApiKey = esc_attr( $ETHEREUM_WALLET_options['infuraApiKey'] );
                        }
                        $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
                        if ( empty($blockchainNetwork) ) {
                            $blockchainNetwork = 'mainnet';
                        }
                        $web3Endpoint = "https://" . esc_attr( $blockchainNetwork ) . ".infura.io/v3/" . esc_attr( $infuraApiKey );
                        return $web3Endpoint;
                    }
                    
                    function ETHEREUM_WALLET_getWeb3WSSEndpoint()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $infuraApiKey = '';
                        if ( isset( $ETHEREUM_WALLET_options['infuraApiKey'] ) ) {
                            $infuraApiKey = esc_attr( $ETHEREUM_WALLET_options['infuraApiKey'] );
                        }
                        $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
                        if ( empty($blockchainNetwork) ) {
                            $blockchainNetwork = 'mainnet';
                        }
                        $web3WSSEndpoint = "wss://" . esc_attr( $blockchainNetwork ) . ".infura.io/ws/v3/" . esc_attr( $infuraApiKey );
                        return $web3WSSEndpoint;
                    }
                    
                    function ETHEREUM_WALLET_getBlockchainNetwork()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $blockchainNetwork = 'mainnet';
                        if ( !isset( $ETHEREUM_WALLET_options['blockchain_network'] ) ) {
                            return $blockchainNetwork;
                        }
                        if ( empty($ETHEREUM_WALLET_options['blockchain_network']) ) {
                            return $blockchainNetwork;
                        }
                        $blockchainNetwork = esc_attr( $ETHEREUM_WALLET_options['blockchain_network'] );
                        return $blockchainNetwork;
                    }
                    
                    function ETHEREUM_WALLET_getCurrencyTicker()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $currency_ticker = 'ETH';
                        return $currency_ticker;
                    }
                    
                    function ETHEREUM_WALLET_getCurrencyName()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $currency_name = __( 'Ether', 'ethereum-wallet' );
                        return $currency_name;
                    }
                    
                    function _ETHEREUM_WALLET_balance_shortcode_data( $attributes )
                    {
                        global  $ETHEREUM_WALLET_options ;
                        global  $ETHEREUM_WALLET_plugin_dir, $ETHEREUM_WALLET_plugin_url_path ;
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return null;
                        }
                        $accountAddress = ETHEREUM_WALLET_get_wallet_address();
                        $attributes = shortcode_atts( array(
                            'paper'            => '',
                            'tokensymbol'      => '',
                            'tokenname'        => '',
                            'tokenaddress'     => '',
                            'tokendecimals'    => '2',
                            'tokendecimalchar' => '.',
                            'tokenwooproduct'  => '',
                            'tokeniconpath'    => '',
                            'tokeniconheight'  => '54px',
                            'displayfiat'      => '0',
                            'updatetimeout'    => '60',
                        ), $attributes, 'ethereum-wallet-balance' );
                        $strPaper = ( !empty($attributes['paper']) ? esc_attr( $attributes['paper'] ) : '' );
                        $tokenName = ( !empty($attributes['tokenname']) ? esc_attr( $attributes['tokenname'] ) : '' );
                        $tokenSymbol = ( !empty($attributes['tokensymbol']) ? esc_attr( $attributes['tokensymbol'] ) : $tokenName );
                        $tokenAddress = ( !empty($attributes['tokenaddress']) ? esc_attr( $attributes['tokenaddress'] ) : '' );
                        $tokenDecimals = intval( ( isset( $attributes['tokendecimals'] ) ? esc_attr( $attributes['tokendecimals'] ) : (( empty($tokenAddress) ? '5' : '2' )) ) );
                        $tokenDecimalCharDefault = '.';
                        if ( function_exists( "wc_get_price_decimal_separator" ) ) {
                            $tokenDecimalCharDefault = wc_get_price_decimal_separator();
                        }
                        $tokenDecimalChar = ( !empty($attributes['tokendecimalchar']) ? esc_attr( $attributes['tokendecimalchar'] ) : $tokenDecimalCharDefault );
                        $product_id = ( !empty($attributes['tokenwooproduct']) ? intval( esc_attr( $attributes['tokenwooproduct'] ) ) : '' );
                        $tokenIconPath = ( !empty($attributes['tokeniconpath']) ? esc_attr( $attributes['tokeniconpath'] ) : '' );
                        $tokenIconHeight = floatval( ( !empty($attributes['tokeniconheight']) ? esc_attr( $attributes['tokeniconheight'] ) : '54' ) ) . 'px';
                        $displayFiat = ( !empty($attributes['displayfiat']) ? boolval( esc_attr( $attributes['displayfiat'] ) ) : false );
                        $updatetimeout = intval( ( isset( $attributes['updatetimeout'] ) ? esc_attr( $attributes['updatetimeout'] ) : '60' ) );
                        $providerUrl = ETHEREUM_WALLET_getWeb3Endpoint();
                        
                        if ( !empty($tokenAddress) && empty($tokenSymbol) && empty($tokenName) ) {
                            $tokenSymbol = ETHEREUM_WALLET_get_token_symbol( $tokenAddress, $providerUrl );
                            $tokenName = $tokenSymbol;
                        }
                        
                        /**
                         * @param BigInteger $balance The Ether or Token balance in wei.
                         */
                        $balance = new BigInteger( 0 );
                        $strBalance = '0';
                        $strBalanceNum = '0';
                        $strCurrencyName = ETHEREUM_WALLET_getCurrencyName();
                        $strCurrencySymbol = ETHEREUM_WALLET_getCurrencyTicker();
                        if ( !empty($accountAddress) ) {
                            
                            if ( empty($tokenAddress) ) {
                                // ETH
                                list( $error, $balance ) = ETHEREUM_WALLET_getBalanceEth( $providerUrl, $accountAddress );
                                
                                if ( !is_null( $balance ) ) {
                                    $powDecimals = new BigInteger( pow( 10, 18 ) );
                                    list( $q, $r ) = $balance->divide( $powDecimals );
                                    
                                    if ( 0 == $tokenDecimals ) {
                                        
                                        if ( $r->multiply( new BigInteger( 2 ) )->compare( $powDecimals ) < 0 ) {
                                            $strBalance = $q->toString();
                                            $strBalanceNum = $q->toString();
                                        } else {
                                            $strBalance = $q->add( new BigInteger( 1 ) )->toString();
                                            $strBalanceNum = $q->add( new BigInteger( 1 ) )->toString();
                                        }
                                    
                                    } else {
                                        $sR = $r->toString();
                                        $strBalanceDecimals = sprintf( '%018s', $sR );
                                        $strBalanceDecimals2 = substr( $strBalanceDecimals, 0, $tokenDecimals );
                                        
                                        if ( str_pad( "", $tokenDecimals, "0" ) == $strBalanceDecimals2 ) {
                                            $strBalance = rtrim( $q->toString() . $tokenDecimalChar . $strBalanceDecimals, '0' );
                                            $strBalanceNum = rtrim( $q->toString() . '.' . $strBalanceDecimals, '0' );
                                        } else {
                                            $strBalance = rtrim( $q->toString() . $tokenDecimalChar . $strBalanceDecimals2, '0' );
                                            $strBalanceNum = rtrim( $q->toString() . '.' . $strBalanceDecimals2, '0' );
                                        }
                                        
                                        $strBalance = rtrim( $strBalance, $tokenDecimalChar );
                                        $strBalanceNum = rtrim( $strBalanceNum, '.' );
                                    }
                                
                                } else {
                                    $strBalance = __( 'Failed to retrieve Ether balance', 'ethereum-wallet' );
                                }
                            
                            } else {
                                // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                            }
                        
                        }
                        $fiatBalance = null;
                        $exchangeRate = null;
                        $exchangeRateWC = null;
                        
                        if ( $product_id ) {
                            // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                        } else {
                            if ( ETHEREUM_WALLET_getCurrencyTicker() == $strCurrencySymbol && $displayFiat ) {
                                
                                if ( function_exists( "get_woocommerce_currency" ) ) {
                                    $currency = get_woocommerce_currency();
                                    
                                    if ( $currency != 'MYC' && !(function_exists( 'mycred_point_type_exists' ) && mycred_point_type_exists( $currency )) ) {
                                        $cryptocompareApiKey = '';
                                        if ( isset( $ETHEREUM_WALLET_options['cryptocompare_api_key'] ) ) {
                                            $cryptocompareApiKey = esc_attr( $ETHEREUM_WALLET_options['cryptocompare_api_key'] );
                                        }
                                        $convertor = new CurrencyConvertor( ETHEREUM_WALLET_getCurrencyTicker(), $currency, $cryptocompareApiKey );
                                        $exchangeRate = $convertor->get_exchange_rate();
                                        $fiatBalance = $convertor->convert( floatval( $strBalanceNum ) );
                                        $fiatBalance = 0.01 * floor( 100 * $fiatBalance );
                                        
                                        if ( function_exists( "wc_price" ) ) {
                                            $fiatBalance = wc_price( $fiatBalance );
                                            $exchangeRateWC = wc_price( $exchangeRate );
                                        }
                                    
                                    }
                                
                                }
                            
                            }
                        }
                        
                        $exchangeRateDisplay = __( 'N/A', 'ethereum-wallet' );
                        if ( !is_null( $exchangeRate ) ) {
                            $exchangeRateDisplay = sprintf( __( '%1$s per %2$s', 'ethereum-wallet' ), $exchangeRateWC, $strCurrencySymbol );
                        }
                        $fiatCurrencySymbol = __( 'N/A', 'ethereum-wallet' );
                        $fiatIconURL = $ETHEREUM_WALLET_plugin_url_path . '/images/cryptocurrency-icons/svg/color/generic.svg';
                        
                        if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
                            $fiatCurrencySymbol = get_woocommerce_currency_symbol();
                            $fiatCurrency = get_woocommerce_currency();
                            $fiatIconPath = $ETHEREUM_WALLET_plugin_dir . '/images/cryptocurrency-icons/svg/color/' . strtolower( $fiatCurrency ) . '.svg';
                            if ( file_exists( $fiatIconPath ) ) {
                                $fiatIconURL = $ETHEREUM_WALLET_plugin_url_path . '/images/cryptocurrency-icons/svg/color/' . strtolower( $fiatCurrency ) . '.svg';
                            }
                        }
                        
                        return [
                            'paper'               => $strPaper,
                            'symbol'              => $strCurrencySymbol,
                            'name'                => $strCurrencyName,
                            'address'             => $tokenAddress,
                            'decimals'            => $tokenDecimals,
                            'updatetimeout'       => $updatetimeout,
                            'decimalchar'         => $tokenDecimalChar,
                            'wooproduct'          => $product_id,
                            'iconpath'            => $tokenIconPath,
                            'fiaticonpath'        => $fiatIconURL,
                            'iconheight'          => $tokenIconHeight,
                            'balance'             => $strBalance,
                            'fiatbalance'         => ( is_null( $fiatBalance ) ? __( 'N/A', 'ethereum-wallet' ) : $fiatBalance ),
                            'exchangerate'        => ( is_null( $exchangeRate ) ? __( 'N/A', 'ethereum-wallet' ) : $exchangeRate ),
                            'exchangeratedisplay' => $exchangeRateDisplay,
                            'displayfiat'         => $displayFiat,
                            'fiatCurrencySymbol'  => $fiatCurrencySymbol,
                        ];
                    }
                    
                    function ETHEREUM_WALLET_balance_shortcode( $attributes )
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        // $attributes = shortcode_atts( array(
                        //     'paper' => '',
                        //     'tokensymbol' => '',
                        //     'tokenname' => '',
                        //     'tokenaddress' => '',
                        //     'tokendecimals' => '2',
                        //     'tokendecimalchar' => '.',
                        //     'tokenwooproduct' => '',
                        //     'tokeniconpath' => '',
                        //     'tokeniconheight' => '54px',
                        //     'displayfiat' => '0',
                        //     'updatetimeout' => '60', // seconds
                        // ), $attributes, 'ethereum-wallet-balance' );
                        $data = _ETHEREUM_WALLET_balance_shortcode_data( $attributes );
                        if ( is_null( $data ) ) {
                            return;
                        }
                        $js = '';
                        $ret = '<div class="ethereum-wallet-balance-shortcode" ' . 'data-paper="' . esc_attr( $data['paper'] ) . '" ' . 'data-symbol="' . esc_attr( $data['symbol'] ) . '" ' . 'data-name="' . esc_attr( $data['name'] ) . '" ' . 'data-address="' . esc_attr( $data['address'] ) . '" ' . 'data-decimals="' . esc_attr( $data['decimals'] ) . '" ' . 'data-decimalchar="' . esc_attr( $data['decimalchar'] ) . '" ' . 'data-wooproduct="' . esc_attr( $data['wooproduct'] ) . '" ' . 'data-iconpath="' . esc_attr( $data['iconpath'] ) . '" ' . 'data-iconheight="' . esc_attr( $data['iconheight'] ) . '" ' . 'data-balance="' . esc_attr( $data['balance'] ) . '" ' . 'data-fiatbalance="' . esc_attr( $data['fiatbalance'] ) . '" ' . 'data-exchangerate="' . esc_attr( $data['exchangerate'] ) . '" ' . 'data-exchangeratedisplay="' . esc_attr( $data['exchangeratedisplay'] ) . '" ' . 'data-displayfiat="' . esc_attr( $data['displayfiat'] ) . '"></div>';
                        ETHEREUM_WALLET_enqueue_scripts_();
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'ethereum-wallet-balance', 'ETHEREUM_WALLET_balance_shortcode' );
                    function ETHEREUM_WALLET_account_shortcode( $attributes )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $accountAddress = ETHEREUM_WALLET_get_wallet_address();
                        $attributes = shortcode_atts( array(
                            'label'   => '',
                            'nolabel' => '',
                        ), $attributes, 'ethereum-wallet-account' );
                        $label = ( !empty($attributes['label']) ? esc_attr( $attributes['label'] ) : __( 'Account', 'ethereum-wallet' ) );
                        $nolabel = ( !empty($attributes['nolabel']) ? esc_attr( $attributes['nolabel'] ) : '' );
                        $js = '';
                        $ret = '<div class="ethereum-wallet-account-shortcode" ' . 'data-label="' . $label . '" ' . 'data-nolabel="' . $nolabel . '" ' . 'data-account="' . $accountAddress . '"></div>';
                        ETHEREUM_WALLET_enqueue_scripts_();
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'ethereum-wallet-account', 'ETHEREUM_WALLET_account_shortcode' );
                    function ETHEREUM_WALLET_account_management_export_shortcode( $attributes )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $privateKey = get_user_meta( $user_id, 'user_ethereum_wallet_key', true );
                        
                        if ( empty($privateKey) ) {
                            ETHEREUM_WALLET_user_registration( $user_id );
                            $privateKey = get_user_meta( $user_id, 'user_ethereum_wallet_key', true );
                        }
                        
                        $attributes = shortcode_atts( array(
                            'label'   => '',
                            'nolabel' => '',
                        ), $attributes, 'ethereum-wallet-account-management-export' );
                        $label = ( !empty($attributes['label']) ? esc_attr( $attributes['label'] ) : __( 'Private key', 'ethereum-wallet' ) );
                        $nolabel = ( !empty($attributes['nolabel']) ? esc_attr( $attributes['nolabel'] ) : '' );
                        $js = '';
                        $ret = '<div class="ethereum-wallet-account-management-export-shortcode" ' . 'data-label="' . $label . '" ' . 'data-nolabel="' . $nolabel . '" ' . 'data-account="' . $privateKey . '"></div>';
                        ETHEREUM_WALLET_enqueue_scripts_();
                        //    wp_enqueue_script( 'jquery.qrcode' );
                        //    wp_enqueue_script( 'clipboard' );
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'ethereum-wallet-account-management-export', 'ETHEREUM_WALLET_account_management_export_shortcode' );
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    add_filter(
                        'woocommerce_product_data_store_cpt_get_products_query',
                        'ETHEREUM_WALLET_handling_custom_meta_query_keys',
                        10,
                        3
                    );
                    function ETHEREUM_WALLET_handling_custom_meta_query_keys( $wp_query_args, $query_vars, $data_store_cpt )
                    {
                        $meta_keys = [
                            '_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type',
                            // '_text_input_cryptocurrency_data',
                            '_select_cryptocurrency_option',
                        ];
                        // The custom meta_key
                        if ( $meta_keys ) {
                            foreach ( $meta_keys as $meta_key ) {
                                if ( !empty($query_vars[$meta_key]) ) {
                                    $wp_query_args['meta_query'][] = array(
                                        'key'   => $meta_key,
                                        'value' => esc_attr( $query_vars[$meta_key] ),
                                    );
                                }
                            }
                        }
                        // ETHEREUM_WALLET_log('$wp_query_args=' . print_r($wp_query_args, true));
                        return $wp_query_args;
                    }
                    
                    function ETHEREUM_WALLET_get_token2wcproduct()
                    {
                        $token2wcproduct = [];
                        if ( !function_exists( 'wc_get_products' ) ) {
                            return $token2wcproduct;
                        }
                        $products = wc_get_products( array(
                            '_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' => 'yes',
                            '_select_cryptocurrency_option'                                       => 'ERC20',
                        ) );
                        // ETHEREUM_WALLET_log('$productsERC20=' . print_r($products, true));
                        if ( $products ) {
                            foreach ( $products as $product ) {
                                $product_id = $product->get_id();
                                $tokenAddress = get_post_meta( $product_id, '_text_input_cryptocurrency_data', true );
                                
                                if ( !empty($tokenAddress) ) {
                                    $token2wcproduct[strtolower( $tokenAddress )] = $product;
                                    // } else {
                                    //     //_select_cryptocurrency_option
                                    //     $_select_cryptocurrency_option = get_post_meta( $product_id, '_select_cryptocurrency_option', true );
                                    //     if (!empty($_select_cryptocurrency_option) && 'Ether' == $_select_cryptocurrency_option) {
                                    //         $token2wcproduct['0x0000000000000000000000000000000000000001'] = $product;
                                    //     }
                                }
                            
                            }
                        }
                        $products = wc_get_products( array(
                            '_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' => 'yes',
                            '_select_cryptocurrency_option'                                       => 'Ether',
                        ) );
                        // ETHEREUM_WALLET_log('$productsEther=' . print_r($products, true));
                        if ( $products ) {
                            foreach ( $products as $product ) {
                                // $product_id = $product->get_id();
                                // $tokenAddress = get_post_meta( $product_id, '_text_input_cryptocurrency_data', true );
                                // if (!empty($tokenAddress)) {
                                //     $token2wcproduct[strtolower($tokenAddress)] = $product;
                                // } else {
                                //     //_select_cryptocurrency_option
                                //     $_select_cryptocurrency_option = get_post_meta( $product_id, '_select_cryptocurrency_option', true );
                                //     if (!empty($_select_cryptocurrency_option) && 'Ether' == $_select_cryptocurrency_option) {
                                $token2wcproduct['0x0000000000000000000000000000000000000001'] = $product;
                                //     }
                                // }
                            }
                        }
                        return $token2wcproduct;
                    }
                    
                    function _ETHEREUM_WALLET_get_tokens_data( $onlytokens, $tokenslist, $attributes )
                    {
                        global  $ETHEREUM_WALLET_plugin_dir, $ETHEREUM_WALLET_plugin_url_path ;
                        // $ops = '';
                        $tokensData = [
                            '0x0000000000000000000000000000000000000001' => [
                            "tokenAddress" => '',
                            "tokenName"    => ETHEREUM_WALLET_getCurrencyName(),
                            "tokenSymbol"  => ETHEREUM_WALLET_getCurrencyTicker(),
                            "tokenDecimal" => 18,
                        ],
                        ];
                        // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                        $token2wcproduct = ETHEREUM_WALLET_get_token2wcproduct();
                        // ETHEREUM_WALLET_log('token2wcproduct=' . print_r($token2wcproduct, true));
                        $tokensDataFinal = [];
                        if ( $tokensData ) {
                            foreach ( $tokensData as $key => $token ) {
                                $tokenAddress = $token['tokenAddress'];
                                $tokenSymbol = $token['tokenSymbol'];
                                $tokenName = $token['tokenName'];
                                $product = ( isset( $token2wcproduct[strtolower( $tokenAddress )] ) ? $token2wcproduct[strtolower( $tokenAddress )] : null );
                                $product_id = ( !is_null( $product ) ? $product->get_id() : '' );
                                $iconPath = $ETHEREUM_WALLET_plugin_dir . '/images/cryptocurrency-icons/svg/color/' . strtolower( $tokenSymbol ) . '.svg';
                                $iconURL = $ETHEREUM_WALLET_plugin_url_path . '/images/cryptocurrency-icons/svg/color/generic.svg';
                                
                                if ( file_exists( $iconPath ) ) {
                                    $iconURL = $ETHEREUM_WALLET_plugin_url_path . '/images/cryptocurrency-icons/svg/color/' . strtolower( $tokenSymbol ) . '.svg';
                                } else {
                                    
                                    if ( !is_null( $product ) ) {
                                        // @see https://stackoverflow.com/a/56997005/4256005
                                        $iconPathProduct = wp_get_attachment_url( $product->get_image_id() );
                                        if ( !empty($iconPathProduct) ) {
                                            $iconURL = $iconPathProduct;
                                        }
                                    }
                                
                                }
                                
                                $tokenAttrs = array(
                                    'paper'            => '',
                                    'tokensymbol'      => $tokenSymbol,
                                    'tokenname'        => ( !empty($tokenName) ? $tokenName : $tokenSymbol ),
                                    'tokenaddress'     => $tokenAddress,
                                    'tokendecimals'    => ( isset( $attributes['tokendecimals'] ) ? $attributes['tokendecimals'] : '2' ),
                                    'tokendecimalchar' => ( isset( $attributes['tokendecimalchar'] ) ? $attributes['tokendecimalchar'] : '.' ),
                                    'tokenwooproduct'  => $product_id,
                                    'tokeniconpath'    => $iconURL,
                                    'tokeniconheight'  => ( isset( $attributes['tokeniconheight'] ) ? $attributes['tokeniconheight'] : '54px' ),
                                    'displayfiat'      => '1',
                                );
                                // ETHEREUM_WALLET_log('tokenAttrs=' . print_r($tokenAttrs, true));
                                $tokenData = _ETHEREUM_WALLET_balance_shortcode_data( $tokenAttrs );
                                if ( is_null( $tokenData ) ) {
                                    continue;
                                }
                                $tokensDataFinal[] = $tokenData;
                            }
                        }
                        return $tokensDataFinal;
                    }
                    
                    function ETHEREUM_WALLET_sendform_shortcode( $attributes )
                    {
                        global  $ETHEREUM_WALLET_plugin_dir, $ETHEREUM_WALLET_plugin_url_path ;
                        //    global $ETHEREUM_WALLET_options;
                        //	$options = stripslashes_deep( $ETHEREUM_WALLET_options );
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $attributes = shortcode_atts( array(
                            'updatetimeout' => '60',
                        ), $attributes, 'ethereum-wallet-sendform' );
                        $onlytokens = $attributes['onlytokens'];
                        $tokenslist = $attributes['tokenslist'];
                        $updatetimeout = intval( ( isset( $attributes['updatetimeout'] ) ? esc_attr( $attributes['updatetimeout'] ) : '60' ) );
                        $tokensDataFinal = _ETHEREUM_WALLET_get_tokens_data( $onlytokens, $tokenslist, $attributes );
                        // ETHEREUM_WALLET_log('$tokensDataFinal=' . print_r($tokensDataFinal, true));
                        // $gas_price_api_url = ETHEREUM_WALLET_get_gas_price_api_url();
                        $js = '';
                        $ret = '<span class="ethereum-wallet-sendform-shortcode" ' . 'data-nonce="' . wp_create_nonce( 'ethereum-wallet-send_form' ) . '" ' . 'data-tokens="' . esc_attr( json_encode( $tokensDataFinal ) ) . '" ' . 'data-onlytokens="' . esc_attr( $onlytokens ) . '" ' . 'data-updatetimeout="' . esc_attr( $updatetimeout ) . '" ' . '></span>';
                        ETHEREUM_WALLET_enqueue_scripts_();
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'ethereum-wallet-sendform', 'ETHEREUM_WALLET_sendform_shortcode' );
                    function ETHEREUM_WALLET_sendform_action()
                    {
                        global  $wp ;
                        global  $ETHEREUM_WALLET_erc1404ContractABI ;
                        global  $ETHEREUM_WALLET_options ;
                        $error = null;
                        if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
                            return;
                        }
                        if ( empty($_POST['action']) || 'ethereum_wallet_send' !== $_POST['action'] ) {
                            return;
                        }
                        
                        if ( function_exists( 'wc_nocache_headers' ) ) {
                            wc_nocache_headers();
                        } else {
                            nocache_headers();
                        }
                        
                        $nonce_value = '';
                        
                        if ( isset( $_REQUEST['ethereum-wallet-send-form-nonce'] ) ) {
                            $nonce_value = $_REQUEST['ethereum-wallet-send-form-nonce'];
                        } else {
                            if ( isset( $_REQUEST['_wpnonce'] ) ) {
                                $nonce_value = $_REQUEST['_wpnonce'];
                            }
                        }
                        
                        
                        if ( !wp_verify_nonce( $nonce_value, 'ethereum-wallet-send_form' ) ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_sendform_action: bad nonce detected: " . $nonce_value );
                            return;
                        }
                        
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $lastTxHash = get_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', true );
                        
                        if ( !empty($lastTxHash) && 0 == ETHEREUM_WALLET_is_tx_confirmed( $lastTxHash ) ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_sendform_action: tx already in progress: " . $lastTxHash );
                            return;
                        }
                        
                        $lastTxTime = get_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', true );
                        delete_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', $lastTxHash );
                        delete_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', $lastTxTime );
                        $accountAddress = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                        $privateKey = get_user_meta( $user_id, 'user_ethereum_wallet_key', true );
                        // To address
                        
                        if ( !isset( $_REQUEST['ethereum-wallet-sendform-to'] ) ) {
                            ETHEREUM_WALLET_log( "ethereum-wallet-sendform-to not set" );
                            return;
                        }
                        
                        $to = sanitize_text_field( $_REQUEST['ethereum-wallet-sendform-to'] );
                        
                        if ( empty($to) ) {
                            ETHEREUM_WALLET_log( "empty ethereum-wallet-sendform-to" );
                            return;
                        }
                        
                        
                        if ( 42 != strlen( $to ) ) {
                            ETHEREUM_WALLET_log( "strlen ethereum-wallet-sendform-to != 42: " . $to );
                            return;
                        }
                        
                        
                        if ( '0x' != substr( $to, 0, 2 ) ) {
                            ETHEREUM_WALLET_log( "startsWith ethereum-wallet-sendform-to != 0x: " . $to );
                            return;
                        }
                        
                        // Amount
                        
                        if ( !isset( $_REQUEST['ethereum-wallet-sendform-amount'] ) ) {
                            ETHEREUM_WALLET_log( "ethereum-wallet-sendform-amount not set" );
                            return;
                        }
                        
                        $amount = sanitize_text_field( $_REQUEST['ethereum-wallet-sendform-amount'] );
                        
                        if ( empty($amount) ) {
                            ETHEREUM_WALLET_log( "empty ethereum-wallet-sendform-amount" );
                            return;
                        }
                        
                        
                        if ( !is_numeric( $amount ) ) {
                            ETHEREUM_WALLET_log( "non-numeric ethereum-wallet-sendform-amount: " . $amount );
                            return;
                        }
                        
                        // Currency address
                        
                        if ( !isset( $_REQUEST['ethereum-wallet-sendform-currency'] ) ) {
                            ETHEREUM_WALLET_log( "ethereum-wallet-sendform-currency not set" );
                            return;
                        }
                        
                        $currency = sanitize_text_field( $_REQUEST['ethereum-wallet-sendform-currency'] );
                        
                        if ( empty($currency) ) {
                            ETHEREUM_WALLET_log( "empty ethereum-wallet-sendform-currency" );
                            return;
                        }
                        
                        
                        if ( 42 != strlen( $currency ) ) {
                            ETHEREUM_WALLET_log( "strlen ethereum-wallet-sendform-currency != 42: " . $to );
                            return;
                        }
                        
                        
                        if ( '0x' != substr( $currency, 0, 2 ) ) {
                            ETHEREUM_WALLET_log( "startsWith ethereum-wallet-sendform-currency != 0x: " . $to );
                            return;
                        }
                        
                        $error = null;
                        $txhash = false;
                        
                        if ( "0x0000000000000000000000000000000000000001" === $currency ) {
                            // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                            if ( is_null( $error ) && false === $txhash ) {
                                // check for not processed with premium code
                                list( $error, $txhash ) = ETHEREUM_WALLET_send_ether(
                                    $accountAddress,
                                    $to,
                                    $amount,
                                    $privateKey
                                );
                            }
                        } else {
                            // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                        }
                        
                        
                        if ( !is_null( $error ) ) {
                            ?>
    <script>
        var ethereumWalletErrorMessageTimeoutId = null;
        function ethereumWalletErrorMessageTimeoutFunc() {
            clearTimeout(ethereumWalletErrorMessageTimeoutId);
            if ('undefined' === typeof window.ethereumWallet) {
                ethereumWalletErrorMessageTimeoutId = setTimeout(ethereumWalletErrorMessageTimeoutFunc, 100);
                return;
            }
            window.ethereumWallet.error_message = "<?php 
                            echo  ( $error instanceof \Exception ? $error->getMessage() : $error ) ;
                            ?>";
            window.ethereumWallet.ETHEREUM_WALLET_update_error_message();
        }
        ethereumWalletErrorMessageTimeoutId = setTimeout(ethereumWalletErrorMessageTimeoutFunc, 100);
    </script>
<?php 
                            return;
                        }
                        
                        
                        if ( false !== $txhash ) {
                            update_user_meta( $user_id, 'user_ethereum_wallet_last_tx_currency', $currency );
                            update_user_meta( $user_id, 'user_ethereum_wallet_last_tx_to', $to );
                            update_user_meta( $user_id, 'user_ethereum_wallet_last_tx_value', $amount );
                            update_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', $txhash );
                            update_user_meta( $user_id, 'user_ethereum_wallet_last_tx_hash', $txhash );
                            update_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', time() );
                            ?>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
        var ethereumWalletErrorMessageTimeoutId2 = null;
        function ethereumWalletErrorMessageTimeoutFunc2() {
            clearTimeout(ethereumWalletErrorMessageTimeoutId2);
            if ('undefined' === typeof window.ethereumWallet) {
                ethereumWalletErrorMessageTimeoutId2 = setTimeout(ethereumWalletErrorMessageTimeoutFunc2, 100);
                return;
            }
            window.ethereumWallet.error_message = "";
            window.ethereumWallet.ETHEREUM_WALLET_update_error_message();
        }
        ethereumWalletErrorMessageTimeoutId2 = setTimeout(ethereumWalletErrorMessageTimeoutFunc2, 100);
    </script>
<?php 
                        }
                    
                    }
                    
                    add_action( 'wp_loaded', "ETHEREUM_WALLET_sendform_action", 20 );
                    function ETHEREUM_WALLET_account_management_create_shortcode( $attributes )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $attributes = shortcode_atts( array(
                            'label'       => '',
                            'nolabel'     => '',
                            'placeholder' => '',
                            'buttonlabel' => '',
                        ), $attributes, 'ethereum-wallet-account-management-create' );
                        $label = ( !empty($attributes['label']) ? esc_attr( $attributes['label'] ) : __( 'Account name', 'ethereum-wallet' ) );
                        $placeholder = ( !empty($attributes['placeholder']) ? esc_attr( $attributes['placeholder'] ) : __( 'Input new account name', 'ethereum-wallet' ) );
                        $buttonlabel = ( !empty($attributes['buttonlabel']) ? esc_attr( $attributes['buttonlabel'] ) : __( 'Add', 'ethereum-wallet' ) );
                        $nolabel = ( !empty($attributes['nolabel']) ? esc_attr( $attributes['nolabel'] ) : '' );
                        $nonce = wp_create_nonce( 'ethereum-wallet-account-management-create-send_form' );
                        $js = '';
                        $ret = '<div class="ethereum-wallet-account-management-create-shortcode" ' . 'data-nonce="' . $nonce . '" ' . 'data-label="' . $label . '" ' . 'data-nolabel="' . $nolabel . '" ' . 'data-buttonlabel="' . $buttonlabel . '" ' . 'data-placeholder="' . $placeholder . '"></div>';
                        ETHEREUM_WALLET_enqueue_scripts_();
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'ethereum-wallet-account-management-create', 'ETHEREUM_WALLET_account_management_create_shortcode' );
                    function ETHEREUM_WALLET_account_management_create_action()
                    {
                        global  $wp ;
                        if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
                            return;
                        }
                        if ( empty($_POST['action']) || 'ethereum_wallet_account_management_create_send' !== $_POST['action'] ) {
                            return;
                        }
                        
                        if ( function_exists( 'wc_nocache_headers' ) ) {
                            wc_nocache_headers();
                        } else {
                            nocache_headers();
                        }
                        
                        $nonce_value = '';
                        
                        if ( isset( $_REQUEST['ethereum-wallet-account-management-create-send-form-nonce'] ) ) {
                            $nonce_value = $_REQUEST['ethereum-wallet-account-management-create-send-form-nonce'];
                        } else {
                            if ( isset( $_REQUEST['_wpnonce'] ) ) {
                                $nonce_value = $_REQUEST['_wpnonce'];
                            }
                        }
                        
                        
                        if ( !wp_verify_nonce( $nonce_value, 'ethereum-wallet-account-management-create-send_form' ) ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_create_action: bad nonce detected: " . $nonce_value );
                            return;
                        }
                        
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        
                        if ( isset( $_REQUEST['ethereum-wallet-account-management-create-name'] ) ) {
                            $name = sanitize_text_field( $_REQUEST['ethereum-wallet-account-management-create-name'] );
                            
                            if ( __( 'Default account', 'ethereum-wallet' ) == $name ) {
                                ETHEREUM_WALLET_log( "ethereum-wallet-account-management-create-name an attempt to replace the default account is blocked: " . $name );
                            } else {
                                
                                if ( !empty($name) ) {
                                    // create new account request
                                    ETHEREUM_WALLET_log( "ethereum-wallet-account-management-create-name is set: " . $name );
                                    $accountsJSON = get_user_meta( $user_id, 'user_ethereum_wallet_accounts', true );
                                    if ( empty($accountsJSON) ) {
                                        $accountsJSON = '[]';
                                    }
                                    $accounts = json_decode( $accountsJSON, true );
                                    list( $ethAddressChkSum, $privateKeyHex ) = ETHEREUM_WALLET_create_account();
                                    $accounts[] = [
                                        "name"    => $name,
                                        "address" => $ethAddressChkSum,
                                        "key"     => $privateKeyHex,
                                    ];
                                    // @see https://stackoverflow.com/a/44263857/4256005
                                    update_user_meta( $user_id, 'user_ethereum_wallet_accounts', json_encode( $accounts, JSON_UNESCAPED_UNICODE ) );
                                    // set new account as default
                                    update_user_meta( $user_id, 'user_ethereum_wallet_address', $ethAddressChkSum );
                                    update_user_meta( $user_id, 'user_ethereum_wallet_key', $privateKeyHex );
                                }
                            
                            }
                        
                        }
                        
                        ?>
    <!-- <script>
        // if ( window.history.replaceState ) {
        //     window.history.replaceState( null, null, window.location.href );
        // }
    </script> -->
<?php 
                    }
                    
                    add_action( 'wp_loaded', "ETHEREUM_WALLET_account_management_create_action", 20 );
                    function ETHEREUM_WALLET_account_management_import_shortcode( $attributes )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $attributes = shortcode_atts( array(
                            'label'          => '',
                            'nolabel'        => '',
                            'placeholder'    => '',
                            'labelkey'       => '',
                            'nolabelkey'     => '',
                            'placeholderkey' => '',
                            'buttonlabel'    => '',
                        ), $attributes, 'ethereum-wallet-account-management-import' );
                        $label = ( !empty($attributes['label']) ? esc_attr( $attributes['label'] ) : __( 'Account name', 'ethereum-wallet' ) );
                        $nolabel = ( !empty($attributes['nolabel']) ? esc_attr( $attributes['nolabel'] ) : '' );
                        $placeholder = ( !empty($attributes['placeholder']) ? esc_attr( $attributes['placeholder'] ) : __( 'Input new account name', 'ethereum-wallet' ) );
                        $labelkey = ( !empty($attributes['labelkey']) ? esc_attr( $attributes['labelkey'] ) : __( 'Private key', 'ethereum-wallet' ) );
                        $nolabelkey = ( !empty($attributes['nolabelkey']) ? esc_attr( $attributes['nolabelkey'] ) : '' );
                        $placeholderkey = ( !empty($attributes['placeholderkey']) ? esc_attr( $attributes['placeholderkey'] ) : __( 'Input your private key here', 'ethereum-wallet' ) );
                        $buttonlabel = ( !empty($attributes['buttonlabel']) ? esc_attr( $attributes['buttonlabel'] ) : __( 'Import', 'ethereum-wallet' ) );
                        $nonce = wp_create_nonce( 'ethereum-wallet-account-management-import-send_form' );
                        $js = '';
                        $ret = '<div class="ethereum-wallet-account-management-import-shortcode" ' . 'data-nonce="' . $nonce . '" ' . 'data-label="' . $label . '" ' . 'data-nolabel="' . $nolabel . '" ' . 'data-labelkey="' . $labelkey . '" ' . 'data-nolabelkey="' . $nolabelkey . '" ' . 'data-buttonlabel="' . $buttonlabel . '" ' . 'data-placeholderkey="' . $placeholderkey . '" ' . 'data-placeholder="' . $placeholder . '"></div>';
                        ETHEREUM_WALLET_enqueue_scripts_();
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'ethereum-wallet-account-management-import', 'ETHEREUM_WALLET_account_management_import_shortcode' );
                    function ETHEREUM_WALLET_account_management_import_action()
                    {
                        global  $wp ;
                        if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
                            return;
                        }
                        if ( empty($_POST['action']) || 'ethereum_wallet_account_management_import_send' !== $_POST['action'] ) {
                            return;
                        }
                        
                        if ( function_exists( 'wc_nocache_headers' ) ) {
                            wc_nocache_headers();
                        } else {
                            nocache_headers();
                        }
                        
                        $nonce_value = '';
                        
                        if ( isset( $_REQUEST['ethereum-wallet-account-management-import-send-form-nonce'] ) ) {
                            $nonce_value = $_REQUEST['ethereum-wallet-account-management-import-send-form-nonce'];
                        } else {
                            if ( isset( $_REQUEST['_wpnonce'] ) ) {
                                $nonce_value = $_REQUEST['_wpnonce'];
                            }
                        }
                        
                        
                        if ( !wp_verify_nonce( $nonce_value, 'ethereum-wallet-account-management-import-send_form' ) ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_import_action: bad nonce detected: " . $nonce_value );
                            return;
                        }
                        
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        
                        if ( isset( $_REQUEST['ethereum-wallet-account-management-import-name'] ) && isset( $_REQUEST['ethereum-wallet-account-management-import-key'] ) ) {
                            $name = sanitize_text_field( $_REQUEST['ethereum-wallet-account-management-import-name'] );
                            $privateKeyHex = sanitize_text_field( $_REQUEST['ethereum-wallet-account-management-import-key'] );
                            
                            if ( __( 'Default account', 'ethereum-wallet' ) == $name ) {
                                ETHEREUM_WALLET_log( "ethereum-wallet-account-management-import-name an attempt to replace the default account is blocked: " . $name );
                            } else {
                                
                                if ( !empty($name) && !empty($privateKeyHex) ) {
                                    // import new account request
                                    ETHEREUM_WALLET_log( "ethereum-wallet-account-management-import-name is set: " . $name );
                                    $blnIsValid = false;
                                    try {
                                        $ecAdapter = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter();
                                        $privateKeyBuffer = Buffer::hex( $privateKeyHex );
                                        $blnIsValid = $ecAdapter->validatePrivateKey( $privateKeyBuffer );
                                    } catch ( \Exception $ex ) {
                                        ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_import_action: " . $ex->getMessage() );
                                        ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_import_action: " . $ex->getTraceAsString() );
                                    }
                                    
                                    if ( !$blnIsValid ) {
                                        ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_import_action: invalid private key" );
                                    } else {
                                        $accountsJSON = get_user_meta( $user_id, 'user_ethereum_wallet_accounts', true );
                                        if ( empty($accountsJSON) ) {
                                            $accountsJSON = '[]';
                                        }
                                        $accounts = json_decode( $accountsJSON, true );
                                        $ethAddressChkSum = null;
                                        try {
                                            $ethAddressChkSum = ETHEREUM_WALLET_address_from_key( $privateKeyHex );
                                        } catch ( \Exception $ex ) {
                                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_import_action: " . $ex->getMessage() );
                                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_import_action: " . $ex->getTraceAsString() );
                                        }
                                        
                                        if ( !is_null( $ethAddressChkSum ) ) {
                                            $blnFound = false;
                                            if ( $accounts ) {
                                                foreach ( $accounts as $account ) {
                                                    
                                                    if ( $ethAddressChkSum == $account["address"] ) {
                                                        $blnFound = true;
                                                        break;
                                                    }
                                                
                                                }
                                            }
                                            
                                            if ( !$blnFound ) {
                                                $accounts[] = [
                                                    "name"     => $name,
                                                    "address"  => $ethAddressChkSum,
                                                    "key"      => $privateKeyHex,
                                                    "imported" => true,
                                                ];
                                                // @see https://stackoverflow.com/a/44263857/4256005
                                                update_user_meta( $user_id, 'user_ethereum_wallet_accounts', json_encode( $accounts, JSON_UNESCAPED_UNICODE ) );
                                                // set new account as default
                                                update_user_meta( $user_id, 'user_ethereum_wallet_address', $ethAddressChkSum );
                                                update_user_meta( $user_id, 'user_ethereum_wallet_key', $privateKeyHex );
                                            }
                                        
                                        }
                                    
                                    }
                                
                                }
                            
                            }
                        
                        }
                        
                        ?>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
<?php 
                    }
                    
                    add_action( 'wp_loaded', "ETHEREUM_WALLET_account_management_import_action", 20 );
                    function ETHEREUM_WALLET_account_management_select_shortcode( $attributes )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $attributes = shortcode_atts( array(
                            'label'             => '',
                            'nolabel'           => '',
                            'buttonremovelabel' => '',
                            'buttonselectlabel' => '',
                        ), $attributes, 'ethereum-wallet-account-management-select' );
                        $label = ( !empty($attributes['label']) ? esc_attr( $attributes['label'] ) : __( 'Default account', 'ethereum-wallet' ) );
                        $nolabel = ( !empty($attributes['nolabel']) ? esc_attr( $attributes['nolabel'] ) : '' );
                        $buttonremovelabel = ( !empty($attributes['buttonremovelabel']) ? esc_attr( $attributes['buttonremovelabel'] ) : __( 'Remove', 'ethereum-wallet' ) );
                        $buttonselectlabel = ( !empty($attributes['buttonselectlabel']) ? esc_attr( $attributes['buttonselectlabel'] ) : __( 'Select', 'ethereum-wallet' ) );
                        // $ops = '';
                        $accountsJSON = get_user_meta( $user_id, 'user_ethereum_wallet_accounts', true );
                        if ( empty($accountsJSON) ) {
                            $accountsJSON = '[]';
                        }
                        $accounts = json_decode( $accountsJSON, true );
                        $defaultAddress = ETHEREUM_WALLET_get_wallet_address();
                        $defaultAddressName = __( 'Default account', 'ethereum-wallet' );
                        
                        if ( !$accounts ) {
                            $name = $defaultAddressName;
                            $address = $defaultAddress;
                            $privateKey = get_user_meta( $user_id, 'user_ethereum_wallet_key', true );
                            $accounts = [ [
                                "name"    => $name,
                                "address" => $address,
                                "key"     => $privateKey,
                            ] ];
                            // @see https://stackoverflow.com/a/44263857/4256005
                            update_user_meta( $user_id, 'user_ethereum_wallet_accounts', json_encode( $accounts, JSON_UNESCAPED_UNICODE ) );
                        }
                        
                        // if ($accounts) foreach ($accounts as $account) {
                        //     $selected = '';
                        //     if ($defaultAddress == $account["address"]) {
                        //         $selected = ' selected';
                        //     }
                        //     $op = '<option value="' . $account["address"] . '"' . $selected . '>' . $account["name"] . ' - ' . $account["address"] . '</option>';
                        //     $ops .= $op;
                        // }
                        // 	$js = '';
                        // 	$ret =
                        // '<form method="post" action="" onsubmit="return window.ethereumWallet.validate_account_management_select_form()"><div class="twbs"><div class="container-fluid ethereum-wallet-account-management-select-shortcode">
                        //     <div class="row ethereum-wallet-account-management-select-content">
                        //         <div class="col-12">
                        //             <div class="form-group">
                        //                 <label class="control-label" for="ethereum-wallet-account-management-select-default">'. __('Default account', 'ethereum-wallet') . '</label>
                        //                 <div class="input-group" style="margin-top: 8px">
                        //                     <select
                        //                         class="custom-select form-control"
                        //                         id="ethereum-wallet-account-management-select-default"
                        //                         name="ethereum-wallet-account-management-select-default" >
                        //                         '.$ops.'
                        //                     </select>
                        //                 </div>
                        //             </div>
                        //         </div>
                        //     </div>
                        //     <div class="row ethereum-wallet-account-management-select-content">
                        //         <div class="col-12">
                        //             <div class="form-group">
                        //                 '.wp_nonce_field( 'ethereum-wallet-account-management-select-send_form', 'ethereum-wallet-account-management-select-send-form-nonce', true, false ).'
                        //                 <input type="hidden" name="action" value="ethereum_wallet_account_management_select_send" />
                        //                 <button
                        //                     id="ethereum-wallet-account-management-select-send-button"
                        //                     name="ethereum-wallet-account-management-select-send-button"
                        //                     type="submit"
                        //                     value="'. __('Select', 'ethereum-wallet') . '"
                        //                     class="button btn btn-default float-right col-12 col-md-4">'. __('Select', 'ethereum-wallet') . '</button>
                        //                 <button
                        //                     id="ethereum-wallet-account-management-delete-send-button"
                        //                     name="ethereum-wallet-account-management-delete-send-button"
                        //                     type="submit"
                        //                     value="'. __('Remove', 'ethereum-wallet') . '"
                        //                     class="button btn btn-default float-right col-12 col-md-4">'. __('Remove', 'ethereum-wallet') . '</button>
                        //             </div>
                        //         </div>
                        //     </div>
                        // </div></div></form>';
                        $nonce = wp_create_nonce( 'ethereum-wallet-account-management-select-send_form' );
                        $js = '';
                        $ret = '<div class="ethereum-wallet-account-management-select-shortcode" ' . 'data-accounts="' . esc_attr( json_encode( array_map( function ( $a ) use( $defaultAddress ) {
                            return [
                                "name"     => $a['name'],
                                "address"  => $a['address'],
                                "selected" => strtolower( $defaultAddress ) === strtolower( $a['address'] ),
                                "imported" => isset( $a['imported'] ),
                            ];
                        }, $accounts ) ) ) . '" ' . 'data-label="' . $label . '" ' . 'data-nonce="' . $nonce . '" ' . 'data-nolabel="' . $nolabel . '" ' . 'data-buttonremovelabel="' . $buttonremovelabel . '" ' . 'data-buttonselectlabel="' . $buttonselectlabel . '"></div>';
                        ETHEREUM_WALLET_enqueue_scripts_();
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'ethereum-wallet-account-management-select', 'ETHEREUM_WALLET_account_management_select_shortcode' );
                    function ETHEREUM_WALLET_account_management_select_action()
                    {
                        global  $wp ;
                        if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
                            return;
                        }
                        if ( empty($_POST['action']) || 'ethereum_wallet_account_management_select_send' !== $_POST['action'] ) {
                            return;
                        }
                        
                        if ( function_exists( 'wc_nocache_headers' ) ) {
                            wc_nocache_headers();
                        } else {
                            nocache_headers();
                        }
                        
                        $nonce_value = '';
                        
                        if ( isset( $_REQUEST['ethereum-wallet-account-management-select-send-form-nonce'] ) ) {
                            $nonce_value = $_REQUEST['ethereum-wallet-account-management-select-send-form-nonce'];
                        } else {
                            if ( isset( $_REQUEST['_wpnonce'] ) ) {
                                $nonce_value = $_REQUEST['_wpnonce'];
                            }
                        }
                        
                        
                        if ( !wp_verify_nonce( $nonce_value, 'ethereum-wallet-account-management-select-send_form' ) ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_select_action: bad nonce detected: " . $nonce_value );
                            return;
                        }
                        
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        
                        if ( isset( $_REQUEST['ethereum-wallet-account-management-select-send-button'] ) ) {
                            do {
                                if ( !isset( $_REQUEST['ethereum-wallet-account-management-select-default'] ) ) {
                                    break;
                                }
                                $newDefaultAddress = sanitize_text_field( $_REQUEST['ethereum-wallet-account-management-select-default'] );
                                
                                if ( empty($newDefaultAddress) ) {
                                    ETHEREUM_WALLET_log( "empty ethereum-wallet-account-management-select-default" );
                                    break;
                                }
                                
                                
                                if ( 42 != strlen( $newDefaultAddress ) ) {
                                    ETHEREUM_WALLET_log( "strlen ethereum-wallet-account-management-select-default != 42: " . $to );
                                    break;
                                }
                                
                                
                                if ( '0x' != substr( $newDefaultAddress, 0, 2 ) ) {
                                    ETHEREUM_WALLET_log( "startsWith ethereum-wallet-account-management-select-default != 0x: " . $to );
                                    break;
                                }
                                
                                $defaultAddress = ETHEREUM_WALLET_get_wallet_address();
                                
                                if ( $newDefaultAddress == $defaultAddress ) {
                                    ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_select_action: no difference with current default set: " . $defaultAddress );
                                    break;
                                }
                                
                                $accountsJSON = get_user_meta( $user_id, 'user_ethereum_wallet_accounts', true );
                                if ( empty($accountsJSON) ) {
                                    $accountsJSON = '[]';
                                }
                                $accounts = json_decode( $accountsJSON, true );
                                if ( !$accounts ) {
                                    break;
                                }
                                if ( $accounts ) {
                                    foreach ( $accounts as $account ) {
                                        
                                        if ( $account["address"] == $newDefaultAddress ) {
                                            // set default account
                                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_select_action: new default address set: " . $newDefaultAddress );
                                            update_user_meta( $user_id, 'user_ethereum_wallet_address', $newDefaultAddress );
                                            update_user_meta( $user_id, 'user_ethereum_wallet_key', $account["key"] );
                                        }
                                    
                                    }
                                }
                            } while (false);
                        } else {
                            if ( isset( $_REQUEST['ethereum-wallet-account-management-delete-send-button'] ) ) {
                                do {
                                    if ( !isset( $_REQUEST['ethereum-wallet-account-management-select-default'] ) ) {
                                        break;
                                    }
                                    $deleteAddress = sanitize_text_field( $_REQUEST['ethereum-wallet-account-management-select-default'] );
                                    ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_select_action: try to delete: " . $deleteAddress );
                                    
                                    if ( empty($deleteAddress) ) {
                                        ETHEREUM_WALLET_log( "empty ethereum-wallet-account-management-select-default" );
                                        break;
                                    }
                                    
                                    
                                    if ( 42 != strlen( $deleteAddress ) ) {
                                        ETHEREUM_WALLET_log( "strlen ethereum-wallet-account-management-select-default != 42: " . $to );
                                        break;
                                    }
                                    
                                    
                                    if ( '0x' != substr( $deleteAddress, 0, 2 ) ) {
                                        ETHEREUM_WALLET_log( "startsWith ethereum-wallet-account-management-select-default != 0x: " . $to );
                                        break;
                                    }
                                    
                                    $defaultAddress = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                                    
                                    if ( $deleteAddress == $defaultAddress ) {
                                        ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_select_action: can not delete current default address: " . $defaultAddress );
                                        break;
                                    }
                                    
                                    $accountsJSON = get_user_meta( $user_id, 'user_ethereum_wallet_accounts', true );
                                    if ( empty($accountsJSON) ) {
                                        $accountsJSON = '[]';
                                    }
                                    $accounts = json_decode( $accountsJSON, true );
                                    if ( !$accounts ) {
                                        break;
                                    }
                                    $newAccounts = [];
                                    if ( $accounts ) {
                                        foreach ( $accounts as $account ) {
                                            
                                            if ( $account["address"] == $deleteAddress && isset( $account["imported"] ) ) {
                                                ETHEREUM_WALLET_log( "ETHEREUM_WALLET_account_management_select_action: deleted address: " . $deleteAddress );
                                                continue;
                                            }
                                            
                                            $newAccounts[] = $account;
                                        }
                                    }
                                    // @see https://stackoverflow.com/a/44263857/4256005
                                    update_user_meta( $user_id, 'user_ethereum_wallet_accounts', json_encode( $newAccounts, JSON_UNESCAPED_UNICODE ) );
                                } while (false);
                            }
                        }
                        
                        ?>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
<?php 
                    }
                    
                    add_action( 'wp_loaded', "ETHEREUM_WALLET_account_management_select_action", 20 );
                    // TODO: wait for a configured number of blocks
                    /**
                     *
                     * @param type $txhash
                     * @return Integer confirmed: 1, unconfirmed: 0, failed: -1
                     */
                    function ETHEREUM_WALLET_is_tx_confirmed( $txhash )
                    {
                        $is_confirmed = false;
                        $is_failed = false;
                        $is_mined = false;
                        $txBlockNumber = '';
                        $savedTxInfo = null;
                        try {
                            $providerUrl = ETHEREUM_WALLET_getWeb3Endpoint();
                            $requestManager = new HttpRequestManager( $providerUrl, 10 );
                            $web3 = new Web3( new HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $eth->getTransactionByHash( $txhash, function ( $err, $transaction ) use(
                                &$is_mined,
                                &$is_failed,
                                &$txBlockNumber,
                                &$savedTxInfo
                            ) {
                                
                                if ( $err !== null ) {
                                    ETHEREUM_WALLET_log( "Failed to getTransactionByHash: " . $err );
                                    $is_failed = true;
                                    return;
                                }
                                
                                
                                if ( is_null( $transaction ) ) {
                                    ETHEREUM_WALLET_log( "Failed to getTransactionByHash: transaction returned is null" );
                                    $is_failed = true;
                                    return;
                                }
                                
                                
                                if ( !is_object( $transaction ) ) {
                                    ETHEREUM_WALLET_log( "Failed to getTransactionByHash: transaction returned is not an object: " . $transaction );
                                    $is_failed = true;
                                    return;
                                }
                                
                                ETHEREUM_WALLET_log( "transaction: " . print_r( $transaction, true ) );
                                $is_mined = property_exists( $transaction, "blockHash" ) && !empty($transaction->blockHash) && '0x0000000000000000000000000000000000000000000000000000000000000000' != $transaction->blockHash;
                                if ( $is_mined ) {
                                    $txBlockNumber = hexdec( $transaction->blockNumber );
                                }
                                $savedTxInfo = $transaction;
                            } );
                            
                            if ( $is_mined ) {
                                $eth->getTransactionReceipt( $txhash, function ( $err, $receipt ) use(
                                    &$is_confirmed,
                                    &$is_failed,
                                    &$savedTxInfo,
                                    &$txhash
                                ) {
                                    
                                    if ( $err !== null ) {
                                        ETHEREUM_WALLET_log( "Failed to getTransactionReceipt: " . $err );
                                        return;
                                    }
                                    
                                    
                                    if ( is_null( $receipt ) ) {
                                        ETHEREUM_WALLET_log( "Failed to getTransactionReceipt: receipt returned is null" );
                                        return;
                                    }
                                    
                                    
                                    if ( !is_object( $receipt ) ) {
                                        ETHEREUM_WALLET_log( "Failed to getTransactionReceipt: receipt returned is not an object: " . $receipt );
                                        return;
                                    }
                                    
                                    ETHEREUM_WALLET_log( "transaction receipt: " . print_r( $receipt, true ) );
                                    
                                    if ( isset( $receipt->status ) ) {
                                        
                                        if ( $receipt->status === "0x1" ) {
                                            $is_confirmed = true;
                                        } else {
                                            
                                            if ( $receipt->status === "0x0" ) {
                                                
                                                if ( $receipt->gasUsed > $savedTxInfo->gas ) {
                                                    $is_failed = true;
                                                    ETHEREUM_WALLET_log( "getTransactionReceipt({$txhash}): we ran out of gas, not confirmed!" );
                                                } else {
                                                    $is_failed = true;
                                                    ETHEREUM_WALLET_log( "getTransactionReceipt({$txhash}): bad tx status, not confirmed!" );
                                                }
                                            
                                            } else {
                                                // unknown status. pre-Byzantium
                                                
                                                if ( $receipt->gasUsed >= $savedTxInfo->gas ) {
                                                    $is_failed = true;
                                                    ETHEREUM_WALLET_log( "getTransactionReceipt({$txhash}): we ran out of gas, not confirmed!" );
                                                } else {
                                                    $is_confirmed = true;
                                                }
                                            
                                            }
                                        
                                        }
                                    
                                    } else {
                                        // unknown status. pre-Byzantium
                                        
                                        if ( $receipt->gasUsed >= $savedTxInfo->gas ) {
                                            $is_failed = true;
                                            ETHEREUM_WALLET_log( "getTransactionReceipt({$txhash}): we ran out of gas, not confirmed!" );
                                        } else {
                                            $is_confirmed = true;
                                        }
                                    
                                    }
                                
                                } );
                                
                                if ( $is_confirmed ) {
                                    $blockNumber = null;
                                    $eth->blockNumber( function ( $err, $lastBlockNumber ) use( &$blockNumber ) {
                                        
                                        if ( $err !== null ) {
                                            ETHEREUM_WALLET_log( "Failed to get blockNumber: " . $err );
                                            return;
                                        }
                                        
                                        ETHEREUM_WALLET_log( "lastBlockNumber: " . $lastBlockNumber->toString() );
                                        $blockNumber = intval( $lastBlockNumber->toString() );
                                    } );
                                    
                                    if ( null !== $blockNumber && '' !== $txBlockNumber ) {
                                        // https://www.reddit.com/r/ethereum/comments/4eplsv/how_many_confirms_is_considered_safe_in_ethereum/d229xie/
                                        //                $safeBlockCount = 12; // TODO: add admin setting
                                        $safeBlockCount = 1;
                                        // TODO: add admin setting
                                        $is_confirmed = $is_confirmed && $blockNumber - $txBlockNumber >= $safeBlockCount;
                                    }
                                    
                                    ETHEREUM_WALLET_log( "is_confirmed({$txhash} in block {$txBlockNumber}): " . $is_confirmed );
                                }
                            
                            }
                        
                        } catch ( \Exception $ex ) {
                            ETHEREUM_WALLET_log( $ex->getMessage() );
                        }
                        return ( $is_confirmed ? 1 : (( $is_failed ? -1 : 0 )) );
                    }
                    
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    function ETHEREUM_WALLET_history_shortcode( $attributes )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $attributes = shortcode_atts( array(
                            'direction'     => '',
                            'rows'          => '10',
                            'iconheight'    => '54px',
                            'updatetimeout' => '60',
                        ), $attributes, 'ethereum-wallet-history' );
                        // The displayed tx direction: in/out/inout
                        $direction = ( !empty($attributes['direction']) ? $attributes['direction'] : 'inout' );
                        if ( !in_array( $direction, array( 'in', 'out', 'inout' ) ) ) {
                            $direction = 'inout';
                        }
                        // $tokens = ! empty( $attributes['tokens'] ) ? esc_attr($attributes['tokens']) : '';
                        $rows = ( !empty($attributes['rows']) ? esc_attr( $attributes['rows'] ) : '10' );
                        $iconheight = floatval( ( !empty($attributes['iconheight']) ? esc_attr( $attributes['iconheight'] ) : '54' ) ) . 'px';
                        $updatetimeout = intval( ( isset( $attributes['updatetimeout'] ) ? esc_attr( $attributes['updatetimeout'] ) : '60' ) );
                        $onlytokens = 'yes';
                        $tokenslist = '';
                        $tokensDataFinal = [];
                        $tokensDataFinal = _ETHEREUM_WALLET_get_tokens_data( $onlytokens, $tokenslist, $attributes );
                        $js = '';
                        $ret = '<div class="ethereum-wallet-history-shortcode" ' . 'data-tokens="' . esc_attr( json_encode( $tokensDataFinal ) ) . '" ' . 'data-direction="' . esc_attr( $direction ) . '" ' . 'data-rows="' . esc_attr( $rows ) . '" ' . 'data-iconheight="' . esc_attr( $iconheight ) . '" ' . 'data-updatetimeout="' . esc_attr( $updatetimeout ) . '" ' . '></div>';
                        ETHEREUM_WALLET_enqueue_scripts_();
                        // wp_enqueue_script( 'data-tables' );
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'ethereum-wallet-history', 'ETHEREUM_WALLET_history_shortcode' );
                    function ETHEREUM_WALLET_enqueue_scripts_()
                    {
                        wp_enqueue_style( 'ethereum-wallet' );
                        wp_enqueue_script( 'ethereum-wallet-main' );
                        //    if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                        //        if ( ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
                        //
                        //            wp_enqueue_script( 'ethereum-wallet-premium' );
                        //
                        //        }
                        //    }
                    }
                    
                    function ETHEREUM_WALLET_stylesheet()
                    {
                        // global $ETHEREUM_WALLET_plugin_url_path;
                        // $deps = array('font-awesome', 'bootstrap-ethereum-wallet', 'bootstrap-affix-ethereum-wallet', 'data-tables');
                        // if( ( ! wp_style_is( 'ethereum-wallet', 'queue' ) ) && ( ! wp_style_is( 'ethereum-wallet', 'done' ) ) ) {
                        //     wp_dequeue_style('ethereum-wallet');
                        //     wp_deregister_style('ethereum-wallet');
                        //     wp_register_style(
                        //         'ethereum-wallet',
                        //         $ETHEREUM_WALLET_plugin_url_path . '/ethereum-wallet.css',
                        //         $deps,
                        //         '4.0.8'
                        //     );
                        // }
                    }
                    
                    add_action( 'wp_enqueue_scripts', 'ETHEREUM_WALLET_stylesheet', 20 );
                    function ETHEREUM_WALLET_get_wallet_private_key( $user_id = null )
                    {
                        $privateKey = '';
                        return $privateKey;
                    }
                    
                    function ETHEREUM_WALLET_get_wallet_address( $user_id = null )
                    {
                        $accountAddress = '';
                        if ( is_null( $user_id ) ) {
                            $user_id = get_current_user_id();
                        }
                        
                        if ( $user_id > 0 ) {
                            $accountAddress = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                            
                            if ( empty($accountAddress) ) {
                                ETHEREUM_WALLET_user_registration( $user_id );
                                $accountAddress = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                            }
                        
                        }
                        
                        return $accountAddress;
                    }
                    
                    function ETHEREUM_WALLET_get_wallet_name()
                    {
                        $accountName = '';
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return $accountName;
                        }
                        $accountAddress = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                        $accountsJSON = get_user_meta( $user_id, 'user_ethereum_wallet_accounts', true );
                        if ( empty($accountsJSON) ) {
                            $accountsJSON = '[]';
                        }
                        $accounts = json_decode( $accountsJSON, true );
                        if ( $accounts ) {
                            if ( $accounts ) {
                                foreach ( $accounts as $account ) {
                                    if ( strtolower( $account["address"] ) != strtolower( $accountAddress ) ) {
                                        continue;
                                    }
                                    $accountName = $account['name'];
                                }
                            }
                        }
                        if ( empty($accountName) ) {
                            $accountName = __( 'Default account', 'ethereum-wallet' );
                        }
                        return $accountName;
                    }
                    
                    function ETHEREUM_WALLET_get_last_tx_hash_time()
                    {
                        $lastTxHash = '';
                        $lastTxTime = '';
                        $user_id = get_current_user_id();
                        
                        if ( $user_id > 0 ) {
                            $lastTxHash = get_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', true );
                            $lastTxTime = get_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', true );
                            
                            if ( !empty($lastTxHash) && 0 != ETHEREUM_WALLET_is_tx_confirmed( $lastTxHash ) ) {
                                delete_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', $lastTxHash );
                                delete_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', $lastTxTime );
                                $lastTxHash = '';
                                $lastTxTime = '';
                            }
                            
                            
                            if ( empty($lastTxTime) ) {
                                $lastTxTime = '';
                            } else {
                                $lastTxTime = time() - intval( $lastTxTime );
                            }
                        
                        }
                        
                        return [ $lastTxHash, $lastTxTime ];
                    }
                    
                    function ETHEREUM_WALLET_get_token_uri_filter( $tokenURI )
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $options = stripslashes_deep( $ETHEREUM_WALLET_options );
                        $ipfs_gateway_url = ( !empty($options['ipfs_gateway_url']) ? esc_attr( $options['ipfs_gateway_url'] ) : "https://ipfs.io/ipfs/" );
                        return str_replace( 'ipfs://', $ipfs_gateway_url, $tokenURI );
                    }
                    
                    add_filter(
                        'cryptocurrency_product_for_woocommerce_erc721_get_ipfs_uri',
                        'ETHEREUM_WALLET_get_token_uri_filter',
                        10,
                        1
                    );
                    function ETHEREUM_WALLET_enqueue_script()
                    {
                        global  $ETHEREUM_WALLET_plugin_url_path, $ETHEREUM_WALLET_plugin_dir, $ETHEREUM_WALLET_options ;
                        global  $ETHEREUM_WALLET_erc20ContractABI ;
                        global  $ETHEREUM_WALLET_erc721ContractABI ;
                        global  $ETHEREUM_WALLET_erc1404ContractABI ;
                        $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
                        $js_dir = '/js/';
                        // 1. runtime~main
                        // 2. vendors
                        // 3. main
                        $runtimeMain = null;
                        $vendors = null;
                        $main = null;
                        $files = scandir( $ETHEREUM_WALLET_plugin_dir . $js_dir );
                        if ( $files ) {
                            foreach ( $files as $filename ) {
                                // @see https://stackoverflow.com/a/173876/4256005
                                $ext = pathinfo( $filename, PATHINFO_EXTENSION );
                                if ( 'js' !== $ext ) {
                                    continue;
                                }
                                $parts = explode( '.', $filename );
                                if ( count( $parts ) < 3 ) {
                                    continue;
                                }
                                list( $name, $versionHash, $ext ) = $parts;
                                switch ( $name ) {
                                    case 'runtime~main':
                                        $runtimeMain = [ $filename, $versionHash ];
                                        break;
                                    case 'vendors':
                                        $vendors = [ $filename, $versionHash ];
                                        break;
                                    case 'main':
                                        $main = [ $filename, $versionHash ];
                                        break;
                                    default:
                                        break;
                                }
                            }
                        }
                        
                        if ( !wp_script_is( 'ethereum-wallet-runtime-main', 'queue' ) && !wp_script_is( 'ethereum-wallet-runtime-main', 'done' ) ) {
                            wp_dequeue_script( 'ethereum-wallet-runtime-main' );
                            wp_deregister_script( 'ethereum-wallet-runtime-main' );
                            wp_register_script(
                                'ethereum-wallet-runtime-main',
                                $ETHEREUM_WALLET_plugin_url_path . $js_dir . $runtimeMain[0],
                                array(),
                                $runtimeMain[1]
                            );
                        }
                        
                        
                        if ( !wp_script_is( 'ethereum-wallet-vendors', 'queue' ) && !wp_script_is( 'ethereum-wallet-vendors', 'done' ) ) {
                            wp_dequeue_script( 'ethereum-wallet-vendors' );
                            wp_deregister_script( 'ethereum-wallet-vendors' );
                            wp_register_script(
                                'ethereum-wallet-vendors',
                                $ETHEREUM_WALLET_plugin_url_path . $js_dir . $vendors[0],
                                array( 'ethereum-wallet-runtime-main' ),
                                $vendors[1]
                            );
                        }
                        
                        
                        if ( !wp_script_is( 'ethereum-wallet-main', 'queue' ) && !wp_script_is( 'ethereum-wallet-main', 'done' ) ) {
                            wp_dequeue_script( 'ethereum-wallet-main' );
                            wp_deregister_script( 'ethereum-wallet-main' );
                            wp_register_script(
                                'ethereum-wallet-main',
                                $ETHEREUM_WALLET_plugin_url_path . $js_dir . $main[0],
                                array( 'ethereum-wallet-vendors', 'wp-i18n' ),
                                $main[1]
                            );
                        }
                        
                        //    wp_enqueue_script('ethereum-wallet-main');
                        if ( function_exists( 'wp_set_script_translations' ) ) {
                            wp_set_script_translations( 'ethereum-wallet-main', 'ethereum-wallet', $ETHEREUM_WALLET_plugin_dir . 'languages' );
                        }
                        $options = stripslashes_deep( $ETHEREUM_WALLET_options );
                        $etherscanApiKey = ( !empty($options['etherscanApiKey']) ? esc_attr( $options['etherscanApiKey'] ) : '' );
                        $blockchain_network = ETHEREUM_WALLET_getBlockchainNetwork();
                        $gaslimit = ( !empty($options['gaslimit']) ? esc_attr( $options['gaslimit'] ) : "200000" );
                        $ipfs_gateway_url = ( !empty($options['ipfs_gateway_url']) ? esc_attr( $options['ipfs_gateway_url'] ) : "https://ipfs.io/ipfs/" );
                        $gasprice = ETHEREUM_WALLET_get_gas_price_wei();
                        $gasPriceTip = ETHEREUM_WALLET_get_gas_price_tip_wei();
                        $gas_price_api_url = ETHEREUM_WALLET_get_gas_price_api_url();
                        $accountAddress = ETHEREUM_WALLET_get_wallet_address();
                        $lastTxHash = '';
                        $lastTxTime = '';
                        $lastTxTo = '';
                        $lastTxValue = '';
                        $lastTxCurrency = '';
                        $tokens_json = '[]';
                        $canListProducts = false;
                        $user_id = get_current_user_id();
                        
                        if ( $user_id > 0 ) {
                            // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                            $lastTxHash = get_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', true );
                            $lastTxTime = get_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', true );
                            $lastTxTo = get_user_meta( $user_id, 'user_ethereum_wallet_last_tx_to', true );
                            $lastTxValue = get_user_meta( $user_id, 'user_ethereum_wallet_last_tx_value', true );
                            $lastTxCurrency = get_user_meta( $user_id, 'user_ethereum_wallet_last_tx_currency', true );
                            
                            if ( !empty($lastTxHash) && 0 != ETHEREUM_WALLET_is_tx_confirmed( $lastTxHash ) ) {
                                delete_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', $lastTxHash );
                                delete_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', $lastTxTime );
                                $lastTxHash = '';
                                $lastTxTime = '';
                            }
                            
                            
                            if ( empty($lastTxTime) ) {
                                $lastTxTime = '';
                            } else {
                                $lastTxTime = time() - intval( $lastTxTime );
                            }
                            
                            // @see https://docs.woocommerce.com/document/roles-capabilities/
                            $canListProducts = class_exists( "WCV_Vendors" ) && WCV_Vendors::is_vendor( $user_id ) || user_can( $user_id, 'vendor' );
                            // ||
                            // user_can($user_id, 'manage_woocommerce');
                        }
                        
                        /**
                         * Check if NFT Wordpress plugin is active
                         * https://wordpress.stackexchange.com/a/193908/137915
                         **/
                        $isNFTPluginActive = in_array( 'cryptocurrency-product-for-woocommerce-erc721-addon-premium/cryptocurrency-product-for-woocommerce-erc721-addon.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && function_exists( 'CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ERC721_put_token_on_sale_endpoint' );
                        $vendorDashboardPageUrl = null;
                        
                        if ( class_exists( 'WCVendors_Pro_Dashboard' ) ) {
                            $vendorDashboardPageUrl = WCVendors_Pro_Dashboard::get_dashboard_page_url();
                        } else {
                            $vendor_dashboard_page = get_option( 'wcvendors_vendor_dashboard_page_id' );
                            $vendorDashboardPageUrl = get_permalink( $vendor_dashboard_page );
                        }
                        
                        $strCurrencyName = strtolower( ETHEREUM_WALLET_getCurrencyName() );
                        $strCurrencyTicker = ETHEREUM_WALLET_getCurrencyTicker();
                        $price_decimals = 2;
                        if ( function_exists( 'wc_get_price_decimals' ) ) {
                            $price_decimals = wc_get_price_decimals();
                        }
                        $exchangeRate = null;
                        $currency_symbol = null;
                        
                        if ( function_exists( "get_woocommerce_currency" ) ) {
                            $currency = get_woocommerce_currency();
                            $currency_symbol = get_woocommerce_currency_symbol( $currency );
                            
                            if ( $currency != 'MYC' && !(function_exists( 'mycred_point_type_exists' ) && mycred_point_type_exists( $currency )) ) {
                                $cryptocompareApiKey = '';
                                if ( isset( $ETHEREUM_WALLET_options['cryptocompare_api_key'] ) ) {
                                    $cryptocompareApiKey = esc_attr( $ETHEREUM_WALLET_options['cryptocompare_api_key'] );
                                }
                                $convertor = new CurrencyConvertor( ETHEREUM_WALLET_getCurrencyTicker(), $currency, $cryptocompareApiKey );
                                $exchangeRate = $convertor->get_exchange_rate();
                            }
                        
                        }
                        
                        wp_localize_script( 'ethereum-wallet-main', 'ethereumWallet', [
                            'erc20_abi'                                                       => $ETHEREUM_WALLET_erc20ContractABI,
                            'erc721_abi'                                                      => $ETHEREUM_WALLET_erc721ContractABI,
                            'erc1404_abi'                                                     => $ETHEREUM_WALLET_erc1404ContractABI,
                            'user_wallet_address'                                             => esc_html( $accountAddress ),
                            'user_wallet_last_txhash'                                         => esc_html( $lastTxHash ),
                            'user_wallet_last_txtime'                                         => esc_html( $lastTxTime ),
                            'user_wallet_last_tx_to'                                          => esc_html( $lastTxTo ),
                            'user_wallet_last_tx_value'                                       => esc_html( $lastTxValue ),
                            'user_wallet_last_tx_currency'                                    => esc_html( $lastTxCurrency ),
                            'tokens'                                                          => esc_html( $tokens_json ),
                            'price_decimals'                                                  => esc_attr( $price_decimals ),
                            'site_url'                                                        => esc_html( site_url() ),
                            'web3Endpoint'                                                    => ETHEREUM_WALLET_getWeb3Endpoint(),
                            'web3WSSEndpoint'                                                 => ETHEREUM_WALLET_getWeb3WSSEndpoint(),
                            'etherscanApiKey'                                                 => $etherscanApiKey,
                            'blockchain_network'                                              => esc_html( $blockchain_network ),
                            'currency_ticker'                                                 => esc_html( $strCurrencyTicker ),
                            'exchangerate'                                                    => ( is_null( $exchangeRate ) ? null : esc_html( $exchangeRate ) ),
                            'store_currency_symbol'                                           => ( is_null( $currency_symbol ) ? '' : esc_html( $currency_symbol ) ),
                            'gasLimit'                                                        => esc_html( $gaslimit ),
                            'gasPrice'                                                        => esc_html( $gasprice ),
                            'gas_price_api_url'                                               => esc_html( $gas_price_api_url ),
                            'ipfs_gateway_url'                                                => esc_html( $ipfs_gateway_url ),
                            'tokenTxListAPIURLTemplate'                                       => esc_html( ETHEREUM_WALLET_get_token_tx_list_api_url_template() ),
                            'tokenNFTTxListAPIURLTemplate'                                    => esc_html( ETHEREUM_WALLET_get_nft_token_tx_list_api_url_template() ),
                            'internalTxListAPIURLTemplate'                                    => esc_html( ETHEREUM_WALLET_get_internal_tx_list_api_url_template() ),
                            'txListAPIURLTemplate'                                            => esc_html( ETHEREUM_WALLET_get_tx_list_api_url_template() ),
                            'txHashPathTemplate'                                              => esc_html( ETHEREUM_WALLET_get_txhash_path_template() ),
                            'addressPathTemplate'                                             => esc_html( ETHEREUM_WALLET_get_address_path_template() ),
                            'localePath'                                                      => esc_html( $ETHEREUM_WALLET_plugin_url_path . "/i18n/" . get_locale() . ".json" ),
                            'confirmations_number'                                            => '1',
                            'wp_rest_nonce'                                                   => wp_create_nonce( 'wp_rest' ),
                            'wp_rest_url'                                                     => esc_attr( get_rest_url() ),
                            'isNFTPluginActive'                                               => esc_html( $isNFTPluginActive ),
                            'canListProducts'                                                 => esc_html( $canListProducts ),
                            'vendorDashboardPageUrl'                                          => esc_html( $vendorDashboardPageUrl ),
                            'str_copied_msg'                                                  => __( 'Copied to clipboard', 'ethereum-wallet' ),
                            'str_insufficient_eth_balance_msg'                                => __( 'Insufficient Ether balance for tx fee payment.', 'ethereum-wallet' ),
                            'str_unknown_token_symbol_msg'                                    => __( 'Unknown', 'ethereum-wallet' ),
                            'str_unknown_nft_token_symbol_msg'                                => __( 'Unknown NFT', 'ethereum-wallet' ),
                            'str_tx_pending_msg'                                              => __( 'Pending', 'ethereum-wallet' ),
                            'str_prev_tx_pending_msg'                                         => __( 'Previous transaction is still not confirmed or failed', 'ethereum-wallet' ),
                            'str_date_recently_msg'                                           => __( 'recently', 'ethereum-wallet' ),
                            'str_date_days_fmt_msg'                                           => __( '%1$s days', 'ethereum-wallet' ),
                            'str_date_hours_fmt_msg'                                          => __( '%1$s hours', 'ethereum-wallet' ),
                            'str_date_minutes_fmt_msg'                                        => __( '%1$s minutes', 'ethereum-wallet' ),
                            'str_copied_to_clipboard'                                         => __( 'Copied to clipboard', 'ethereum-wallet' ),
                            'str_copy_to_clipboard'                                           => __( 'Copy to clipboard', 'ethereum-wallet' ),
                            'str_qrcode_button_label'                                         => __( 'QR-code', 'ethereum-wallet' ),
                            'str_alert_dlg_title'                                             => __( 'Error', 'ethereum-wallet' ),
                            'str_alert_dlg_title_default'                                     => __( 'Alert', 'ethereum-wallet' ),
                            'str_alert_dlg_ok_button_label'                                   => __( 'OK', 'ethereum-wallet' ),
                            'str_qrcode_dlg_title'                                            => __( 'Scan QR-code', 'ethereum-wallet' ),
                            'str_contract_address_template'                                   => __( 'Contract Address', 'ethereum-wallet' ),
                            'str_token_id_template'                                           => __( 'Token ID', 'ethereum-wallet' ),
                            'str_account_dlg_content'                                         => __( 'Enter the recipient\'s account address please.', 'ethereum-wallet' ),
                            'str_account_dlg_address_field_label'                             => __( 'Recipient', 'ethereum-wallet' ),
                            'str_account_dlg_qrcode_button_label'                             => __( 'QR-code', 'ethereum-wallet' ),
                            'str_account_dlg_title'                                           => __( 'Enter recipient address', 'ethereum-wallet' ),
                            'str_account_dlg_incorrect_address_msg'                           => __( 'Incorrect address', 'ethereum-wallet' ),
                            'str_account_dlg_ok_button_label'                                 => __( 'Send', 'ethereum-wallet' ),
                            'str_account_dlg_cancel_button_label'                             => __( 'Cancel', 'ethereum-wallet' ),
                            'str_confirm_dlg_title'                                           => __( 'Confirm', 'ethereum-wallet' ),
                            'str_confirm_dlg_title_default'                                   => __( 'Confirm', 'ethereum-wallet' ),
                            'str_confirm_dlg_ok_button_label'                                 => __( 'OK', 'ethereum-wallet' ),
                            'str_confirm_dlg_cancel_button_label'                             => __( 'Cancel', 'ethereum-wallet' ),
                            'str_nft_token_send_confirm_msg'                                  => __( 'You are about to send the %1$s NFT token to the %2$s account address. This action is irreversible. Are you sure?', 'ethereum-wallet' ),
                            'str_tx_progress_dlg_title'                                       => __( 'Confirmations', 'ethereum-wallet' ),
                            'str_tx_progress_dlg_content'                                     => __( 'Tx confirmations %s', 'ethereum-wallet' ),
                            'str_token_send_failed_msg'                                       => __( 'Failed to send token', 'ethereum-wallet' ),
                            'str_empty_nft_wallet_msg'                                        => __( 'No NFT found. Yet?', 'ethereum-wallet' ),
                            'str_sendform_to_field_placeholder'                               => sprintf( __( 'Input the recipient %s address', 'ethereum-wallet' ), $strCurrencyName ),
                            'str_sendform_to_field_label'                                     => __( 'Send To', 'ethereum-wallet' ),
                            'str_sendform_input_dialog_title'                                 => sprintf( __( 'Input the recipient %s address', 'ethereum-wallet' ), $strCurrencyName ),
                            'str_sendform_input_amount_dialog_title'                          => __( 'Input amount to send', 'ethereum-wallet' ),
                            'str_token_selector_label'                                        => __( 'Select token', 'ethereum-wallet' ),
                            'str_max_value_button_label'                                      => __( 'Max', 'ethereum-wallet' ),
                            'str_amount_field_label'                                          => __( 'Amount', 'ethereum-wallet' ),
                            'str_sendform_amount_field_placeholder'                           => __( 'Amount', 'ethereum-wallet' ),
                            'str_na_label'                                                    => __( 'N/A', 'ethereum-wallet' ),
                            'str_gas_price_slow_label'                                        => __( 'Cheap', 'ethereum-wallet' ),
                            'str_gas_price_safe_label'                                        => __( 'Safe', 'ethereum-wallet' ),
                            'str_gas_price_fast_label'                                        => __( 'Fast', 'ethereum-wallet' ),
                            'str_gas_price_chain_label'                                       => __( 'Chain', 'ethereum-wallet' ),
                            'str_gas_price_label'                                             => __( 'Transaction Fee', 'ethereum-wallet' ),
                            'str_nonce_field_label'                                           => __( 'Nonce', 'ethereum-wallet' ),
                            'str_gas_limit_field_label'                                       => __( 'Gas Limit', 'ethereum-wallet' ),
                            'str_gas_gwei_label'                                              => __( 'Gwei', 'ethereum-wallet' ),
                            'str_data_field_label'                                            => __( 'Data', 'ethereum-wallet' ),
                            'str_undo_button_label'                                           => __( 'Revert', 'ethereum-wallet' ),
                            'str_advanced_panel_title'                                        => __( 'Advanced', 'ethereum-wallet' ),
                            'str_advanced_panel_desc'                                         => __( 'Danger area! Use with care!', 'ethereum-wallet' ),
                            'str_send_tx_button_label'                                        => __( 'Send %s', 'ethereum-wallet' ),
                            'str_token_send_confirm_msg'                                      => __( 'You are about to send %1$s %2$s to %3$s. This action is irreversible. Are you sure?', 'ethereum-wallet' ),
                            'str_tx_sent_label'                                               => __( 'Sent', 'ethereum-wallet' ),
                            'str_tx_received_label'                                           => __( 'Received', 'ethereum-wallet' ),
                            'str_tx_self_label'                                               => __( 'Self', 'ethereum-wallet' ),
                            'str_tx_mint_label'                                               => __( 'Mint', 'ethereum-wallet' ),
                            'str_tx_call_label'                                               => __( 'Call', 'ethereum-wallet' ),
                            'str_tx_unknown_label'                                            => __( 'Unknown', 'ethereum-wallet' ),
                            'str_nft_token_sell_not_capable_msg'                              => __( 'You need to be a vendor to sell tokens on this store. Open vendor registration page?', 'ethereum-wallet' ),
                            'str_rank_column_label'                                           => __( 'Rank', 'ethereum-wallet' ),
                            'str_avatar_column_label'                                         => __( 'Avatar', 'ethereum-wallet' ),
                            'str_username_column_label'                                       => __( 'Username', 'ethereum-wallet' ),
                            'str_wallet_column_label'                                         => __( 'Wallet', 'ethereum-wallet' ),
                            'str_balance_column_label'                                        => __( 'Balance, %s', 'ethereum-wallet' ),
                            'str_tx_count_column_label'                                       => __( 'Tx Count', 'ethereum-wallet' ),
                            'str_table_search_field_label'                                    => __( 'Search…', 'ethereum-wallet' ),
                            'str_table_clear_search_button_tooltip'                           => __( 'Clear', 'ethereum-wallet' ),
                            'str_empty_table_label'                                           => __( 'No records', 'ethereum-wallet' ),
                            'str_empty_table_search_result_label'                             => __( 'No matching records found', 'ethereum-wallet' ),
                            'str_account_management_default_account_name'                     => __( 'Default account', 'ethereum-wallet' ),
                            'str_account_management_empty_account_error_msg'                  => __( 'Can not create unnamed account', 'ethereum-wallet' ),
                            'str_account_management_empty_key_error_msg'                      => __( 'Empty private key specified', 'ethereum-wallet' ),
                            'str_account_management_default_account_replacement_error_msg'    => __( 'Can not replace the Default account', 'ethereum-wallet' ),
                            'str_account_management_account_create_error_msg'                 => __( 'Can not create account', 'ethereum-wallet' ),
                            'str_account_management_account_select_error_msg'                 => __( 'Can not select account', 'ethereum-wallet' ),
                            'str_account_management_account_select_selected_error_msg'        => __( 'Can not select already selected account', 'ethereum-wallet' ),
                            'str_account_management_account_delete_selected_error_msg'        => __( 'Can not delete selected account', 'ethereum-wallet' ),
                            'str_account_management_account_select_delete_internal_error_msg' => __( 'Can not delete non-imported account', 'ethereum-wallet' ),
                            'str_modal_wait_dlg_title'                                        => __( 'Processing…', 'ethereum-wallet' ),
                            'str_input_not_a_number_error_msg'                                => __( 'Incorrect number', 'ethereum-wallet' ),
                            'str_nft_token_resell_redirect_waiting_msg'                       => __( 'Redirecting to the product edit page. Wait please...', 'ethereum-wallet' ),
                            'str_nft_token_resell_redirect_error_msg'                         => __( 'Failed to resell token', 'ethereum-wallet' ),
                        ] );
                    }
                    
                    add_action( 'wp_enqueue_scripts', 'ETHEREUM_WALLET_enqueue_script' );
                    /**
                     * Admin Options
                     */
                    
                    if ( is_admin() ) {
                        include_once $ETHEREUM_WALLET_plugin_dir . '/settings/blockchain.php';
                        include_once $ETHEREUM_WALLET_plugin_dir . '/settings/api_keys.php';
                        include_once $ETHEREUM_WALLET_plugin_dir . '/settings/admin_fee.php';
                        include_once $ETHEREUM_WALLET_plugin_dir . '/settings/advanced_blockchain.php';
                        include_once $ETHEREUM_WALLET_plugin_dir . '/settings/ipfs.php';
                        include_once $ETHEREUM_WALLET_plugin_dir . '/ethereum-wallet.admin.php';
                    }
                    
                    function ETHEREUM_WALLET_add_menu_link()
                    {
                        $page = add_options_page(
                            __( 'Ethereum Wallet Settings', 'ethereum-wallet' ),
                            __( 'Ethereum Wallet', 'ethereum-wallet' ),
                            'manage_options',
                            'ethereum-wallet',
                            'ETHEREUM_WALLET_options_page'
                        );
                    }
                    
                    add_filter( 'admin_menu', 'ETHEREUM_WALLET_add_menu_link' );
                    // Place in Option List on Settings > Plugins page
                    function ETHEREUM_WALLET_actlinks( $links, $file )
                    {
                        // Static so we don't call plugin_basename on every plugin row.
                        static  $this_plugin ;
                        if ( !$this_plugin ) {
                            $this_plugin = plugin_basename( __FILE__ );
                        }
                        
                        if ( $file == $this_plugin ) {
                            $settings_link = '<a href="options-general.php?page=ethereum-wallet">' . __( 'Settings' ) . '</a>';
                            array_unshift( $links, $settings_link );
                            // before other links
                        }
                        
                        return $links;
                    }
                    
                    add_filter(
                        'plugin_action_links',
                        'ETHEREUM_WALLET_actlinks',
                        10,
                        2
                    );
                    function ETHEREUM_WALLET_get_default_gas_price_wei()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $gasPriceMaxGwei = doubleval( ( isset( $ETHEREUM_WALLET_options['gas_price'] ) ? $ETHEREUM_WALLET_options['gas_price'] : '21' ) );
                        $gasPriceMaxWei = intval( floatval( $gasPriceMaxGwei ) * 1000000000 );
                        return array(
                            'tm'            => time(),
                            'gas_price'     => $gasPriceMaxWei,
                            'gas_prices'    => [
                            'slow' => $gasPriceMaxWei,
                            'safe' => $gasPriceMaxWei,
                            'fast' => $gasPriceMaxWei,
                        ],
                            'gas_price_tip' => null,
                        );
                    }
                    
                    function ETHEREUM_WALLET_query_web3_gas_price_wei()
                    {
                        $providerUrl = ETHEREUM_WALLET_getWeb3Endpoint();
                        try {
                            $requestManager = new HttpRequestManager( $providerUrl, 10 );
                            $web3 = new Web3( new HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $ret = null;
                            $eth->gasPrice( function ( $err, $gasPrice ) use( &$ret ) {
                                
                                if ( $err !== null ) {
                                    ETHEREUM_WALLET_log( "Failed to get gasPrice: ", $err );
                                    return;
                                }
                                
                                $ret = $gasPrice;
                            } );
                            if ( is_null( $ret ) ) {
                                return null;
                            }
                            return $ret->toString();
                        } catch ( Exception $ex ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_query_web3_gas_price_wei: " . $ex->getMessage() );
                        }
                        return 0;
                    }
                    
                    function ETHEREUM_WALLET_query_gas_price_wei()
                    {
                        $gasPriceWei = null;
                        $gasPriceTipWei = null;
                        $providerUrl = ETHEREUM_WALLET_getWeb3Endpoint();
                        $default_gas_price_wei = ETHEREUM_WALLET_get_default_gas_price_wei();
                        try {
                            $requestManager = new HttpRequestManager( $providerUrl, 10 );
                            $web3 = new Web3( new HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $isEIP1559 = ETHEREUM_WALLET_isEIP1559( $eth );
                            
                            if ( !$isEIP1559 ) {
                                // ETHEREUM_WALLET_log("ETHEREUM_WALLET_query_gas_price_wei: !isEIP1559");
                                $gasPriceWei = ETHEREUM_WALLET_query_web3_gas_price_wei();
                            } else {
                                // ETHEREUM_WALLET_log("ETHEREUM_WALLET_query_gas_price_wei: isEIP1559");
                                list( $error, $block ) = ETHEREUM_WALLET_getLatestBlock( $eth );
                                
                                if ( !is_null( $error ) ) {
                                    ETHEREUM_WALLET_log( "ETHEREUM_WALLET_query_gas_price_wei: Failed to get block: " . $error );
                                    return $default_gas_price_wei;
                                }
                                
                                $gasPriceAndTipWei = ETHEREUM_WALLET_query_web3_gas_price_wei();
                                ETHEREUM_WALLET_log( "ETHEREUM_WALLET_query_gas_price_wei: gasPriceAndTipWei: " . $gasPriceAndTipWei );
                                $gasPriceTipWeiBI = ( new BigInteger( $gasPriceAndTipWei ) )->subtract( new BigInteger( $block->baseFeePerGas, 16 ) );
                                ETHEREUM_WALLET_log( "ETHEREUM_WALLET_query_gas_price_wei: gasPriceTipWeiBI: " . $gasPriceTipWeiBI->toString() . '; baseFeePerGas = ' . ( new BigInteger( $block->baseFeePerGas, 16 ) )->toString() );
                                
                                if ( $gasPriceTipWeiBI->compare( new BigInteger( 0 ) ) < 0 ) {
                                    $gasPriceTipWeiBI = new BigInteger( 1000000000 );
                                    // 1 Gwei
                                    ETHEREUM_WALLET_log( "ETHEREUM_WALLET_query_gas_price_wei1: gasPriceTipWeiBI: " . $gasPriceTipWeiBI->toString() );
                                }
                                
                                $default_gas_price_wei_BI = new BigInteger( $default_gas_price_wei['gas_price'] );
                                
                                if ( $default_gas_price_wei_BI->compare( $gasPriceTipWeiBI ) < 0 ) {
                                    $gasPriceTipWeiBI = $default_gas_price_wei_BI;
                                    ETHEREUM_WALLET_log( "ETHEREUM_WALLET_query_gas_price_wei2: gasPriceTipWeiBI: " . $gasPriceTipWeiBI->toString() );
                                }
                                
                                $gasPriceTipWei = $gasPriceTipWeiBI->toString();
                                $gasPriceWei = ( new BigInteger( $block->baseFeePerGas, 16 ) )->multiply( new BigInteger( 2 ) )->add( $gasPriceTipWeiBI );
                                $gasPriceWei = $gasPriceWei->toString();
                                
                                if ( '0' === $gasPriceWei ) {
                                    ETHEREUM_WALLET_log( "ETHEREUM_WALLET_query_gas_price_wei: 0 === gasPriceWei: " . $block->baseFeePerGas . ', bn=' . ( new BigInteger( $block->baseFeePerGas ) )->toString() . '; block=' . print_r( $block, true ) );
                                    return $default_gas_price_wei;
                                }
                            
                            }
                        
                        } catch ( Exception $ex ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_query_gas_price_wei: " . $ex->getMessage() );
                            return ETHEREUM_WALLET_get_default_gas_price_wei();
                        }
                        
                        if ( is_null( $gasPriceWei ) ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_query_gas_price_wei: is_null(gasPriceWei)" );
                            return ETHEREUM_WALLET_get_default_gas_price_wei();
                        }
                        
                        $cache_gas_price = array(
                            'tm'            => time(),
                            'gas_price'     => $gasPriceWei,
                            'gas_price_tip' => $gasPriceTipWei,
                        );
                        $chainId = ETHEREUM_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            ETHEREUM_WALLET_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'ethereum-wallet' ), __( "Blockchain", 'ethereum-wallet' ) ) );
                            return ETHEREUM_WALLET_get_default_gas_price_wei();
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
                    
                    function ETHEREUM_WALLET_get_gas_price_wei()
                    {
                        $chainId = ETHEREUM_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            ETHEREUM_WALLET_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'ethereum-wallet' ), __( "Blockchain", 'ethereum-wallet' ) ) );
                            return null;
                        }
                        
                        $option_name = 'ethereumicoio_cache_gas_price-wei-' . $chainId;
                        $cache_gas_price_wei = get_option( $option_name, array() );
                        if ( !$cache_gas_price_wei ) {
                            $cache_gas_price_wei = ETHEREUM_WALLET_query_gas_price_wei();
                        }
                        $tm_diff = time() - intval( $cache_gas_price_wei['tm'] );
                        // TODO: admin setting
                        $timeout = 10 * 60;
                        // seconds
                        if ( $tm_diff > $timeout ) {
                            $cache_gas_price_wei = ETHEREUM_WALLET_query_gas_price_wei();
                        }
                        $gasPriceWei = doubleval( $cache_gas_price_wei['gas_price'] );
                        
                        if ( is_null( $cache_gas_price_wei['gas_price_tip'] ) ) {
                            // only if pre-EIP1559
                            $gasPriceMaxWei = doubleval( ETHEREUM_WALLET_get_default_gas_price_wei()['gas_price'] );
                            if ( $gasPriceMaxWei < $gasPriceWei ) {
                                $gasPriceWei = $gasPriceMaxWei;
                            }
                        }
                        
                        return intval( $gasPriceWei );
                    }
                    
                    function ETHEREUM_WALLET_get_gas_price_tip_wei()
                    {
                        $chainId = ETHEREUM_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            ETHEREUM_WALLET_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'ethereum-wallet' ), __( "Blockchain", 'ethereum-wallet' ) ) );
                            return null;
                        }
                        
                        $option_name = 'ethereumicoio_cache_gas_price-wei-' . $chainId;
                        $cache_gas_price_wei = get_option( $option_name, array() );
                        
                        if ( !$cache_gas_price_wei ) {
                            $cache_gas_price_wei = ETHEREUM_WALLET_query_gas_price_wei();
                            ETHEREUM_WALLET_log( '!$cache_gas_price_wei: ' . print_r( $cache_gas_price_wei, true ) );
                        }
                        
                        $tm_diff = time() - intval( $cache_gas_price_wei['tm'] );
                        // TODO: admin setting
                        $timeout = 10 * 60;
                        // seconds
                        
                        if ( $tm_diff > $timeout ) {
                            $cache_gas_price_wei = ETHEREUM_WALLET_query_gas_price_wei();
                            ETHEREUM_WALLET_log( '$tm_diff > $timeout: ' . print_r( $cache_gas_price_wei, true ) );
                        }
                        
                        
                        if ( is_null( $cache_gas_price_wei['gas_price_tip'] ) ) {
                            ETHEREUM_WALLET_log( 'is_null($cache_gas_price_wei[\'gas_price_tip\']): ' . print_r( $cache_gas_price_wei, true ) );
                            return null;
                        }
                        
                        $gasPriceTipWei = doubleval( $cache_gas_price_wei['gas_price_tip'] );
                        
                        if ( !is_null( ETHEREUM_WALLET_get_default_gas_price_wei()['gas_price'] ) ) {
                            $gasPriceTipMaxWei = doubleval( ETHEREUM_WALLET_get_default_gas_price_wei()['gas_price'] );
                            if ( $gasPriceTipMaxWei < $gasPriceTipWei ) {
                                $gasPriceTipWei = $gasPriceTipMaxWei;
                            }
                        }
                        
                        return intval( $gasPriceTipWei );
                    }
                    
                    function ETHEREUM_WALLET_isEIP1559( $eth = null )
                    {
                        $chainId = ETHEREUM_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            ETHEREUM_WALLET_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'ethereum-wallet' ), __( "Blockchain", 'ethereum-wallet' ) ) );
                            return null;
                        }
                        
                        $option_name = 'ethereumicoio_cache_is-eip-1559-network-' . $chainId;
                        // delete_option($option_name);
                        $isEIP1559Option = get_option( $option_name, null );
                        $tm_diff = time() - intval( ( !is_null( $isEIP1559Option ) ? $isEIP1559Option['tm'] : time() ) );
                        // TODO: admin setting
                        $timeout = 10 * 60;
                        // seconds
                        // list($error, $block) = ETHEREUM_WALLET_getLatestBlock($eth);
                        $isEIP1559 = ( !is_null( $isEIP1559Option ) ? $isEIP1559Option['isEIP1559'] : null );
                        // ETHEREUM_WALLET_log($option_name . ' : isEIP1559=' . (is_null($isEIP1559) ? 'null' : ($isEIP1559 ? 'true' : 'false')));
                        
                        if ( is_null( $isEIP1559 ) || $tm_diff > $timeout ) {
                            list( $error, $block ) = ETHEREUM_WALLET_getLatestBlock( $eth );
                            
                            if ( !is_null( $error ) ) {
                                ETHEREUM_WALLET_log( "Failed to get block: " . $error );
                                return null;
                            }
                            
                            $isEIP1559 = property_exists( $block, 'baseFeePerGas' );
                            // ETHEREUM_WALLET_log('ETHEREUM_WALLET_isEIP1559: isEIP1559=' . (is_null($isEIP1559) ? 'null' : ($isEIP1559 ? 'true' : 'false')));
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
                    
                    function ETHEREUM_WALLET_send_transaction( $to, $eth_value_wei, $data )
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $chainId = ETHEREUM_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            ETHEREUM_WALLET_log( "chainId is null" );
                            return false;
                        }
                        
                        $user_id = get_current_user_id();
                        
                        if ( $user_id <= 0 ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_send_transaction: no user" );
                            return false;
                        }
                        
                        $lastTxHash = get_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', true );
                        
                        if ( !empty($lastTxHash) && 0 == ETHEREUM_WALLET_is_tx_confirmed( $lastTxHash ) ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_send_transaction: tx already in progress: " . $lastTxHash );
                            return false;
                        }
                        
                        $lastTxTime = get_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', true );
                        delete_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', $lastTxHash );
                        delete_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', $lastTxTime );
                        $from = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                        
                        if ( empty($from) ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_send_transaction: empty from address" );
                            return false;
                        }
                        
                        $txHash = null;
                        try {
                            $providerUrl = ETHEREUM_WALLET_getWeb3Endpoint();
                            $requestManager = new HttpRequestManager( $providerUrl, 10 );
                            $web3 = new Web3( new HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $nonce = 0;
                            $eth->getTransactionCount( $from, function ( $err, $transactionCount ) use( &$nonce ) {
                                
                                if ( $err !== null ) {
                                    ETHEREUM_WALLET_log( "Failed to getTransactionCount: " . $err );
                                    $nonce = null;
                                    return;
                                }
                                
                                $nonce = intval( $transactionCount->toString() );
                            } );
                            if ( null === $nonce ) {
                                return false;
                            }
                            $gasLimit = 21000;
                            if ( !empty($data) ) {
                                $gasLimit = intval( ( isset( $ETHEREUM_WALLET_options['gas_limit'] ) ? $ETHEREUM_WALLET_options['gas_limit'] : '200000' ) );
                            }
                            $gasPrice = ETHEREUM_WALLET_get_gas_price_wei();
                            $gasPriceTip = ETHEREUM_WALLET_get_gas_price_tip_wei();
                            $eth_value_wei = new BigInteger( $eth_value_wei );
                            $nonce = Buffer::int( $nonce );
                            $gasPrice = Buffer::int( $gasPrice );
                            $gasLimit = Buffer::int( $gasLimit );
                            $value = $eth_value_wei->toHex();
                            //ETHEREUM_WALLET_log("value: " . $value);
                            $transactionParamsArray = [
                                'from'    => $from,
                                'nonce'   => '0x' . $nonce->getHex(),
                                'to'      => strtolower( $to ),
                                'gas'     => '0x' . $gasLimit->getHex(),
                                'value'   => '0x' . $value,
                                'chainId' => $chainId,
                                'data'    => $data,
                            ];
                            list( $error, $gasEstimate ) = ETHEREUM_WALLET_get_gas_estimate( $transactionParamsArray, $eth );
                            
                            if ( null === $gasEstimate ) {
                                ETHEREUM_WALLET_log( "gasEstimate is null: " . $error );
                                return false;
                            }
                            
                            ETHEREUM_WALLET_log( "gasEstimate: " . $gasEstimate->toString() );
                            $transactionParamsArray['gas'] = '0x' . $gasEstimate->toHex();
                            unset( $transactionParamsArray['from'] );
                            
                            if ( is_null( $gasPriceTip ) ) {
                                // pre-EIP1559
                                $transactionParamsArray['gasPrice'] = '0x' . $gasPrice->getHex();
                            } else {
                                $transactionParamsArray['accessList'] = [];
                                // EIP1559
                                $transactionParamsArray['maxFeePerGas'] = '0x' . $gasPrice->getHex();
                                $gasPriceTip = Buffer::int( $gasPriceTip );
                                $transactionParamsArray['maxPriorityFeePerGas'] = '0x' . $gasPriceTip->getHex();
                            }
                            
                            $eth_value_with_fee_wei = $eth_value_wei->add( ( new BigInteger( $transactionParamsArray['gas'] ) )->multiply( new BigInteger( '0x' . $gasPrice->getHex() ) ) );
                            // 1. check deposit
                            list( $error, $eth_balance ) = ETHEREUM_WALLET_getBalanceEth( $providerUrl, $from );
                            
                            if ( null === $eth_balance ) {
                                ETHEREUM_WALLET_log( "eth_balance is null: " . $error );
                                return false;
                            }
                            
                            
                            if ( $eth_balance->compare( $eth_value_with_fee_wei ) < 0 ) {
                                $eth_balance_str = $eth_balance->toString();
                                $eth_value_with_fee_wei_str = $eth_value_with_fee_wei->toString();
                                ETHEREUM_WALLET_log( "Insufficient funds: eth_balance_wei({$eth_balance_str}) < eth_value_with_fee_wei({$eth_value_with_fee_wei_str})" );
                                return false;
                            }
                            
                            $transaction = new Transaction( $transactionParamsArray );
                            $privateKey = get_user_meta( $user_id, 'user_ethereum_wallet_key', true );
                            
                            if ( empty($privateKey) ) {
                                ETHEREUM_WALLET_log( "ETHEREUM_WALLET_send_transaction: empty key" );
                                return false;
                            }
                            
                            $signedTransaction = "0x" . $transaction->sign( $privateKey );
                            //ETHEREUM_WALLET_log("transaction: " . print_r($transactionParamsArray, true));
                            //ETHEREUM_WALLET_log("signedTransaction: " . $signedTransaction);
                            $eth->sendRawTransaction( (string) $signedTransaction, function ( $err, $transaction ) use( &$txHash, $transactionParamsArray, $signedTransaction ) {
                                
                                if ( $err !== null ) {
                                    ETHEREUM_WALLET_log( "Failed to sendRawTransaction: " . $err );
                                    ETHEREUM_WALLET_log( "Failed to sendRawTransaction: transactionData=" . print_r( $transactionParamsArray, true ) );
                                    ETHEREUM_WALLET_log( "Failed to sendRawTransaction: signedTransaction=" . (string) $signedTransaction );
                                    return;
                                }
                                
                                $txHash = $transaction;
                            } );
                            
                            if ( null === $txHash ) {
                                ETHEREUM_WALLET_log( "Failed to sendRawTransaction: txHash == null" );
                                return false;
                            }
                            
                            ETHEREUM_WALLET_log( "txHash: " . $txHash );
                            
                            if ( false !== $txHash ) {
                                update_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', $txHash );
                                update_user_meta( $user_id, 'user_ethereum_wallet_last_tx_hash', $txHash );
                                update_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', time() );
                                update_user_meta( $user_id, 'user_ethereum_wallet_last_tx_to', $transactionParamsArray['to'] );
                                update_user_meta( $user_id, 'user_ethereum_wallet_last_tx_value', $transactionParamsArray['value'] );
                            }
                        
                        } catch ( \Exception $ex ) {
                            ETHEREUM_WALLET_log( $ex->getMessage() );
                            return false;
                        }
                        return $txHash;
                    }
                    
                    function ETHEREUM_WALLET_sign_transaction(
                        $to,
                        $eth_value_wei,
                        $data,
                        $gasLimit,
                        $gasPrice,
                        $nonce,
                        $params
                    )
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $chainId = ETHEREUM_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            ETHEREUM_WALLET_log( "chainId is null" );
                            return false;
                        }
                        
                        $user_id = get_current_user_id();
                        
                        if ( $user_id <= 0 ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_send_transaction: no user" );
                            return false;
                        }
                        
                        $from = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                        
                        if ( empty($from) ) {
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_send_transaction: empty from address" );
                            return false;
                        }
                        
                        $gasPrice = ( isset( $params['maxFeePerGas'] ) ? $params['maxFeePerGas'] : $gasPrice );
                        if ( is_null( $gasPrice ) ) {
                            $gasPrice = ETHEREUM_WALLET_get_gas_price_wei();
                        }
                        $eth_value_wei = new BigInteger( $eth_value_wei, 16 );
                        $eth_value_with_fee_wei = $eth_value_wei->add( ( new BigInteger( str_replace( '0x', '', $gasLimit ), 16 ) )->multiply( new BigInteger( str_replace( '0x', '', $gasPrice ), 16 ) ) );
                        $signedTransaction = null;
                        try {
                            // 1. check deposit
                            $providerUrl = ETHEREUM_WALLET_getWeb3Endpoint();
                            list( $error, $eth_balance ) = ETHEREUM_WALLET_getBalanceEth( $providerUrl, $from );
                            
                            if ( null === $eth_balance ) {
                                ETHEREUM_WALLET_log( "eth_balance is null" );
                                return false;
                            }
                            
                            
                            if ( $eth_balance->compare( $eth_value_with_fee_wei ) < 0 ) {
                                $eth_balance_str = $eth_balance->toString();
                                $eth_value_with_fee_wei_str = $eth_value_with_fee_wei->toString();
                                ETHEREUM_WALLET_log( "Insufficient funds: eth_balance_wei({$eth_balance_str}) < eth_value_with_fee_wei({$eth_value_with_fee_wei_str})" );
                                return false;
                            }
                            
                            $requestManager = new HttpRequestManager( $providerUrl, 10 );
                            $web3 = new Web3( new HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $value = $eth_value_wei->toHex();
                            //ETHEREUM_WALLET_log("value: " . $value);
                            $gasPriceTip = ( isset( $params['maxPriorityFeePerGas'] ) ? $params['maxPriorityFeePerGas'] : ETHEREUM_WALLET_get_gas_price_tip_wei() );
                            $transactionParamsArray = [
                                'nonce'   => $nonce,
                                'to'      => strtolower( $to ),
                                'gas'     => $gasLimit,
                                'value'   => '0x' . $value,
                                'chainId' => $chainId,
                                'data'    => $data,
                            ];
                            ETHEREUM_WALLET_log( "ETHEREUM_WALLET_sign_transaction: gasPrice=" . $gasPrice );
                            
                            if ( 0 === strpos( $gasPrice, '0x' ) ) {
                                $gasPrice = Buffer::hex( substr( $gasPrice, 2 ) );
                            } else {
                                $gasPrice = Buffer::int( $gasPrice );
                            }
                            
                            
                            if ( is_null( $gasPriceTip ) ) {
                                // pre-EIP1559
                                $transactionParamsArray['gasPrice'] = '0x' . $gasPrice->getHex();
                            } else {
                                $transactionParamsArray['accessList'] = [];
                                // EIP1559
                                $transactionParamsArray['maxFeePerGas'] = '0x' . $gasPrice->getHex();
                                
                                if ( 0 === strpos( $gasPriceTip, '0x' ) ) {
                                    $gasPriceTip = Buffer::hex( substr( $gasPriceTip, 2 ) );
                                } else {
                                    $gasPriceTip = Buffer::int( $gasPriceTip );
                                }
                                
                                $transactionParamsArray['maxPriorityFeePerGas'] = '0x' . $gasPriceTip->getHex();
                            }
                            
                            $transaction = new Transaction( $transactionParamsArray );
                            $privateKey = get_user_meta( $user_id, 'user_ethereum_wallet_key', true );
                            
                            if ( empty($privateKey) ) {
                                ETHEREUM_WALLET_log( "ETHEREUM_WALLET_send_transaction: empty key" );
                                return false;
                            }
                            
                            $signedTransaction = "0x" . $transaction->sign( $privateKey );
                            ETHEREUM_WALLET_log( "transaction: " . print_r( $transactionParamsArray, true ) );
                            //ETHEREUM_WALLET_log("signedTransaction: " . $signedTransaction);
                        } catch ( \Exception $ex ) {
                            ETHEREUM_WALLET_log( $ex->getMessage() );
                            return false;
                        }
                        return $signedTransaction;
                    }
                    
                    function ETHEREUM_WALLET_get_gas_estimate( $transactionParamsArray, $eth )
                    {
                        $gasEstimate = null;
                        $error = null;
                        $transactionParamsArrayCopy = $transactionParamsArray;
                        unset( $transactionParamsArrayCopy['nonce'] );
                        unset( $transactionParamsArrayCopy['chainId'] );
                        //    ETHEREUM_WALLET_log("ETHEREUM_WALLET_get_gas_estimate: " . print_r($transactionParamsArray, true));
                        //    ETHEREUM_WALLET_log("ETHEREUM_WALLET_get_gas_estimate2: " . print_r($transactionParamsArrayCopy, true));
                        $eth->estimateGas( $transactionParamsArrayCopy, function ( $err, $gas ) use( &$gasEstimate, &$error ) {
                            
                            if ( $err !== null ) {
                                ETHEREUM_WALLET_log( "Failed to estimateGas: " . $err );
                                $error = $err;
                                return;
                            }
                            
                            $gasEstimate = $gas;
                        } );
                        return [ $error, $gasEstimate ];
                    }
                    
                    function ETHEREUM_WALLET_send_ether(
                        $from,
                        $to,
                        $eth_value,
                        $privateKey
                    )
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $chainId = ETHEREUM_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            ETHEREUM_WALLET_log( "chainId is null" );
                            return [ sprintf( __( 'Bad "%s" setting', 'ethereum-wallet' ), __( 'Blockchain', 'ethereum-wallet' ) ), false ];
                        }
                        
                        $error = null;
                        try {
                            $providerUrl = ETHEREUM_WALLET_getWeb3Endpoint();
                            $requestManager = new HttpRequestManager( $providerUrl, 10 );
                            $web3 = new Web3( new HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $nonce = 0;
                            $eth->getTransactionCount( $from, function ( $err, $transactionCount ) use( &$nonce, &$error ) {
                                
                                if ( $err !== null ) {
                                    ETHEREUM_WALLET_log( "Failed to getTransactionCount: " . $err );
                                    $nonce = null;
                                    $error = $err;
                                    return;
                                }
                                
                                $nonce = intval( $transactionCount->toString() );
                            } );
                            if ( null === $nonce ) {
                                return [ $error, false ];
                            }
                            $gasLimit = intval( ( isset( $ETHEREUM_WALLET_options['gas_limit'] ) ? $ETHEREUM_WALLET_options['gas_limit'] : '200000' ) );
                            $gasPrice = ETHEREUM_WALLET_get_gas_price_wei();
                            $gasPriceTip = ETHEREUM_WALLET_get_gas_price_tip_wei();
                            $eth_value_wei = _ETHEREUM_WALLET_double_int_multiply( $eth_value, pow( 10, 18 ) );
                            //    $data = '';
                            $nonce = Buffer::int( $nonce );
                            $gasPrice = Buffer::int( $gasPrice );
                            $gasLimit = Buffer::int( $gasLimit );
                            $value = $eth_value_wei->toHex();
                            //ETHEREUM_WALLET_log("value: " . $value);
                            $transactionParamsArray = [
                                'from'    => $from,
                                'nonce'   => '0x' . $nonce->getHex(),
                                'to'      => strtolower( $to ),
                                'gas'     => '0x' . $gasLimit->getHex(),
                                'value'   => '0x' . $value,
                                'chainId' => $chainId,
                            ];
                            list( $error, $gasEstimate ) = ETHEREUM_WALLET_get_gas_estimate( $transactionParamsArray, $eth );
                            
                            if ( null === $gasEstimate ) {
                                ETHEREUM_WALLET_log( "gasEstimate is null: " . $error );
                                return [ ( !is_null( $error ) ? $error : __( 'Failed to estimate gas', 'ethereum-wallet' ) ), false ];
                            }
                            
                            ETHEREUM_WALLET_log( "gasEstimate: " . $gasEstimate->toString() );
                            $transactionParamsArray['gas'] = '0x' . $gasEstimate->toHex();
                            unset( $transactionParamsArray['from'] );
                            
                            if ( is_null( $gasPriceTip ) ) {
                                // pre-EIP1559
                                $transactionParamsArray['gasPrice'] = '0x' . $gasPrice->getHex();
                            } else {
                                $transactionParamsArray['accessList'] = [];
                                // EIP1559
                                $transactionParamsArray['maxFeePerGas'] = '0x' . $gasPrice->getHex();
                                $gasPriceTip = Buffer::int( $gasPriceTip );
                                $transactionParamsArray['maxPriorityFeePerGas'] = '0x' . $gasPriceTip->getHex();
                            }
                            
                            $eth_value_with_fee_wei = $eth_value_wei->add( ( new BigInteger( $transactionParamsArray['gas'] ) )->multiply( new BigInteger( '0x' . $gasPrice->getHex() ) ) );
                            // 1. check deposit
                            list( $error, $eth_balance ) = ETHEREUM_WALLET_getBalanceEth( $providerUrl, $from );
                            
                            if ( null === $eth_balance ) {
                                ETHEREUM_WALLET_log( "eth_balance is null" );
                                return [ ( !is_null( $error ) ? $error : __( 'Failed to obtain account balance', 'ethereum-wallet' ) ), false ];
                            }
                            
                            
                            if ( $eth_balance->compare( $eth_value_with_fee_wei ) < 0 ) {
                                $eth_value_str = $eth_value_wei->toString();
                                $eth_balance_str = $eth_balance->toString();
                                $eth_value_with_fee_wei_str = $eth_value_with_fee_wei->toString();
                                ETHEREUM_WALLET_log( "Insufficient funds: eth_balance_wei({$eth_balance_str}) < eth_value_with_fee_wei({$eth_value_with_fee_wei_str}); eth_value = {$eth_value_str}" );
                                return [ __( 'Insufficient Ether balance for tx fee payment.', 'ethereum-wallet' ), false ];
                            }
                            
                            $transaction = new Transaction( $transactionParamsArray );
                            $signedTransaction = "0x" . $transaction->sign( $privateKey );
                            //ETHEREUM_WALLET_log("transaction: " . print_r($transactionParamsArray, true));
                            ETHEREUM_WALLET_log( "signedTransaction: " . $signedTransaction );
                            $txHash = null;
                            $error = null;
                            $eth->sendRawTransaction( (string) $signedTransaction, function ( $err, $transaction ) use(
                                &$txHash,
                                &$error,
                                $transactionParamsArray,
                                $signedTransaction
                            ) {
                                
                                if ( $err !== null ) {
                                    ETHEREUM_WALLET_log( "Failed to sendRawTransaction: " . $err );
                                    ETHEREUM_WALLET_log( "Failed to sendRawTransaction: transactionData=" . print_r( $transactionParamsArray, true ) );
                                    ETHEREUM_WALLET_log( "Failed to sendRawTransaction: signedTransaction=" . (string) $signedTransaction );
                                    $error = $err;
                                    return;
                                }
                                
                                $txHash = $transaction;
                            } );
                            ETHEREUM_WALLET_log( "txHash: " . $txHash );
                            if ( null === $txHash ) {
                                //        ETHEREUM_WALLET_log("Failed to sendRawTransaction");
                                return [ $error, false ];
                            }
                            return [ null, $txHash ];
                        } catch ( \Exception $ex ) {
                            ETHEREUM_WALLET_log( $ex->getMessage() );
                            if ( is_null( $error ) ) {
                                $error = $ex->getMessage();
                            }
                            return [ $error, false ];
                        }
                    }
                    
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    function ETHEREUM_WALLET_getChainId()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        static  $_saved_chain_id = null ;
                        if ( is_null( $_saved_chain_id ) ) {
                            $_saved_chain_id = _ETHEREUM_WALLET_getChainId_impl();
                        }
                        return $_saved_chain_id;
                    }
                    
                    function _ETHEREUM_WALLET_getChainId_impl()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
                        if ( empty($blockchainNetwork) ) {
                            $blockchainNetwork = 'mainnet';
                        }
                        if ( $blockchainNetwork === 'mainnet' ) {
                            return 1;
                        }
                        if ( $blockchainNetwork === 'ropsten' ) {
                            return 3;
                        }
                        if ( $blockchainNetwork === 'rinkeby' ) {
                            return 4;
                        }
                        if ( $blockchainNetwork === 'goerli' ) {
                            return 5;
                        }
                        if ( $blockchainNetwork === 'kovan' ) {
                            return 42;
                        }
                        if ( $blockchainNetwork === 'bsc' ) {
                            return 56;
                        }
                        if ( $blockchainNetwork === 'bsctest' ) {
                            return 97;
                        }
                        if ( $blockchainNetwork === 'polygon' ) {
                            return 137;
                        }
                        if ( $blockchainNetwork === 'mumbai' ) {
                            return 80001;
                        }
                        ETHEREUM_WALLET_log( "Bad blockchain_network setting:" . $blockchainNetwork );
                        return null;
                    }
                    
                    function ETHEREUM_WALLET_get_txhash_path( $txHash )
                    {
                        return sprintf( ETHEREUM_WALLET_get_txhash_path_template(), $txHash );
                    }
                    
                    function ETHEREUM_WALLET_get_txhash_path_template()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
                        $txHashPath = '%s';
                        switch ( $blockchainNetwork ) {
                            case 'mainnet':
                                $txHashPath = 'https://etherscan.io/tx/%s';
                                break;
                            case 'ropsten':
                                $txHashPath = 'https://ropsten.etherscan.io/tx/%s';
                                break;
                            case 'rinkeby':
                                $txHashPath = 'https://rinkeby.etherscan.io/tx/%s';
                                break;
                            case 'goerli':
                                $txHashPath = 'https://goerli.etherscan.io/tx/%s';
                            case 'kovan':
                                $txHashPath = 'https://kovan.etherscan.io/tx/%s';
                                break;
                            case 'bsc':
                                $txHashPath = 'https://bscscan.com/tx/%s';
                                break;
                            case 'bsctest':
                                $txHashPath = 'https://testnet.bscscan.com/tx/%s';
                                break;
                            case 'polygon':
                                $txHashPath = 'https://polygonscan.com/tx/%s';
                                break;
                            case 'mumbai':
                                $txHashPath = 'https://mumbai.polygonscan.com/tx/%s';
                                break;
                            default:
                                break;
                        }
                        return $txHashPath;
                    }
                    
                    function ETHEREUM_WALLET_get_address_path( $address )
                    {
                        return sprintf( ETHEREUM_WALLET_get_address_path_template(), $address );
                    }
                    
                    function ETHEREUM_WALLET_get_address_path_template()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
                        $addressPath = '%s';
                        switch ( $blockchainNetwork ) {
                            case 'mainnet':
                                $addressPath = 'https://etherscan.io/address/%s';
                                break;
                            case 'ropsten':
                                $addressPath = 'https://ropsten.etherscan.io/address/%s';
                                break;
                            case 'rinkeby':
                                $addressPath = 'https://rinkeby.etherscan.io/address/%s';
                                break;
                            case 'goerli':
                                $addressPath = 'https://goerli.etherscan.io/address/%s';
                            case 'kovan':
                                $addressPath = 'https://kovan.etherscan.io/address/%s';
                                break;
                            case 'bsc':
                                $txHashPath = 'https://bscscan.com/address/%s';
                                break;
                            case 'bsctest':
                                $txHashPath = 'https://testnet.bscscan.com/address/%s';
                                break;
                            case 'polygon':
                                $txHashPath = 'https://polygonscan.com/address/%s';
                                break;
                            case 'mumbai':
                                $txHashPath = 'https://mumbai.polygonscan.com/address/%s';
                                break;
                            default:
                                break;
                        }
                        return $addressPath;
                    }
                    
                    function ETHEREUM_WALLET_get_gas_price_api_url()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
                        $options = stripslashes_deep( $ETHEREUM_WALLET_options );
                        $etherscanApiKey = ( !empty($options['etherscanApiKey']) ? esc_attr( $options['etherscanApiKey'] ) : '' );
                        $blockchain_network = '';
                        if ( 'mainnet' !== $blockchainNetwork ) {
                            $blockchain_network = '-' . $blockchainNetwork;
                        }
                        $gas_price_api_url = 'https://api' . $blockchain_network . '.etherscan.io/api?module=gastracker&action=gasoracle&apikey=' . $etherscanApiKey;
                        return $gas_price_api_url;
                    }
                    
                    function ETHEREUM_WALLET_get_tx_list_api_url_template()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
                        $options = stripslashes_deep( $ETHEREUM_WALLET_options );
                        $etherscanApiKey = ( !empty($options['etherscanApiKey']) ? esc_attr( $options['etherscanApiKey'] ) : '' );
                        $blockchain_network = '';
                        if ( 'mainnet' !== $blockchainNetwork ) {
                            $blockchain_network = '-' . $blockchainNetwork;
                        }
                        $tx_list_api_url = 'https://api' . $blockchain_network . '.etherscan.io/api?module=account&action=txlist&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=' . $etherscanApiKey;
                        return $tx_list_api_url;
                    }
                    
                    function ETHEREUM_WALLET_get_internal_tx_list_api_url_template()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
                        $options = stripslashes_deep( $ETHEREUM_WALLET_options );
                        $etherscanApiKey = ( !empty($options['etherscanApiKey']) ? esc_attr( $options['etherscanApiKey'] ) : '' );
                        $blockchain_network = '';
                        if ( 'mainnet' !== $blockchainNetwork ) {
                            $blockchain_network = '-' . $blockchainNetwork;
                        }
                        $internal_tx_list_api_url = 'https://api' . $blockchain_network . '.etherscan.io/api?module=account&action=txlistinternal&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=' . $etherscanApiKey;
                        return $internal_tx_list_api_url;
                    }
                    
                    function ETHEREUM_WALLET_get_token_tx_list_api_url_template()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
                        $options = stripslashes_deep( $ETHEREUM_WALLET_options );
                        $etherscanApiKey = ( !empty($options['etherscanApiKey']) ? esc_attr( $options['etherscanApiKey'] ) : '' );
                        $blockchain_network = '';
                        if ( 'mainnet' !== $blockchainNetwork ) {
                            $blockchain_network = '-' . $blockchainNetwork;
                        }
                        $token_tx_list_api_url = 'https://api' . $blockchain_network . '.etherscan.io/api?module=account&action=tokentx&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=' . $etherscanApiKey;
                        return $token_tx_list_api_url;
                    }
                    
                    function ETHEREUM_WALLET_get_nft_token_tx_list_api_url_template()
                    {
                        global  $ETHEREUM_WALLET_options ;
                        $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
                        $options = stripslashes_deep( $ETHEREUM_WALLET_options );
                        $etherscanApiKey = ( !empty($options['etherscanApiKey']) ? esc_attr( $options['etherscanApiKey'] ) : '' );
                        $blockchain_network = '';
                        if ( 'mainnet' !== $blockchainNetwork ) {
                            $blockchain_network = '-' . $blockchainNetwork;
                        }
                        $nft_token_tx_list_api_url = 'https://api' . $blockchain_network . '.etherscan.io/api?module=account&action=tokennfttx&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=' . $etherscanApiKey;
                        return $nft_token_tx_list_api_url;
                    }
                    
                    function _ETHEREUM_WALLET_double_int_multiply( $dval, $ival )
                    {
                        $dval = doubleval( $dval );
                        $ival = intval( $ival );
                        $dv1 = floor( $dval );
                        $ret = new BigInteger( intval( $dv1 ) );
                        $ret = $ret->multiply( new BigInteger( $ival ) );
                        if ( $dv1 === $dval ) {
                            return $ret;
                        }
                        $dv2 = $dval - $dv1;
                        $iv1 = intval( $dv2 * $ival );
                        $ret = $ret->add( new BigInteger( $iv1 ) );
                        return $ret;
                    }
                    
                    // @see https://www.tipsandtricks-hq.com/adding-a-custom-column-to-the-users-table-in-wordpress-7378
                    add_action( 'manage_users_columns', 'ETHEREUM_WALLET_modify_user_columns' );
                    function ETHEREUM_WALLET_modify_user_columns( $column_headers )
                    {
                        $column_headers['ethereum_wallet'] = __( 'Ethereum wallet', 'ethereum-wallet' );
                        return $column_headers;
                    }
                    
                    add_action( 'admin_head', 'ETHEREUM_WALLET_custom_admin_css' );
                    function ETHEREUM_WALLET_custom_admin_css()
                    {
                        echo  '<style>
  .column-ethereum_wallet {width: 22%}
  </style>' ;
                    }
                    
                    add_action(
                        'manage_users_custom_column',
                        'ETHEREUM_WALLET_user_posts_count_column_content',
                        10,
                        3
                    );
                    function ETHEREUM_WALLET_user_posts_count_column_content( $value, $column_name, $user_id )
                    {
                        
                        if ( 'ethereum_wallet' == $column_name ) {
                            $address = ETHEREUM_WALLET_get_wallet_address( $user_id );
                            $addressPath = ETHEREUM_WALLET_get_address_path( $address );
                            $value = sprintf( '<a href="%1$s" target="_blank" rel="nofollow">%2$s</a>', $addressPath, $address );
                        }
                        
                        return $value;
                    }
                
                }
                
                // ethereum_wallet_freemius_init
            }
        
        }
    
    }

}

// PHP version