<?php

/*
Plugin Name: PACMEC Wallet
Plugin URI: https://wordpress.org/plugins/pacmec-wallet/
Description: Wallet for Ether and ERC20 tokens for PACMEC
Version: 3.3.0
WC requires at least: 5.5.0
WC tested up to: 5.8.0
Author: ethereumicoio
Text Domain: pacmec-wallet
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
    $PACMEC_WALLET_plugin_basename,
    $PACMEC_WALLET_options,
    $PACMEC_WALLET_plugin_dir,
    $PACMEC_WALLET_plugin_url_path,
    $PACMEC_WALLET_services,
    $PACMEC_WALLET_amp_icons_css
;
if ( !function_exists( 'PACMEC_WALLET_deactivate' ) ) {
    function PACMEC_WALLET_deactivate()
    {
        if ( !current_user_can( 'activate_plugins' ) ) {
            return;
        }
        deactivate_plugins( plugin_basename( __FILE__ ) );
    }

}

if ( PHP_INT_SIZE != 8 ) {
    add_action( 'admin_init', 'PACMEC_WALLET_deactivate' );
    add_action( 'admin_notices', 'PACMEC_WALLET_admin_notice' );
    function PACMEC_WALLET_admin_notice()
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
        add_action( 'admin_init', 'PACMEC_WALLET_deactivate' );
        add_action( 'admin_notices', 'PACMEC_WALLET_admin_notice' );
        function PACMEC_WALLET_admin_notice()
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
            add_action( 'admin_init', 'PACMEC_WALLET_deactivate' );
            add_action( 'admin_notices', 'PACMEC_WALLET_admin_notice_gmp' );
            function PACMEC_WALLET_admin_notice_gmp()
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
                add_action( 'admin_init', 'PACMEC_WALLET_deactivate' );
                add_action( 'admin_notices', 'PACMEC_WALLET_admin_notice_mbstring' );
                function PACMEC_WALLET_admin_notice_mbstring()
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
                                        'id'              => '9545',
                                        'slug'            => 'pacmec-wallet',
                                        'type'            => 'plugin',
                                        'public_key'      => 'pk_814aa04a7db97545fce07109950e5',
                                        'is_premium'      => false,
                                        // 'premium_suffix'  => 'Professional',
                                        'has_addons'      => false,
                                        'has_paid_plans'  => false,
                                        // 'trial'           => array(
																						// 'days'               => 7,
																						// 'is_require_payment' => true,
																				// ),
                                        // 'has_affiliation' => 'all',
                                        'menu'            => array(
																					'slug'   => 'pacmec-wallet',
																					'parent' => array(
																						'slug' => 'options-general.php',
																					),
																				),
                                        // 'is_live'         => true,
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
                    $PACMEC_WALLET_plugin_basename = plugin_basename( dirname( __FILE__ ) );
                    $PACMEC_WALLET_plugin_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
                    $PACMEC_WALLET_plugin_url_path = untrailingslashit( plugin_dir_url( __FILE__ ) );
                    // HTTPS?
                    $PACMEC_WALLET_plugin_url_path = ( is_ssl() ? str_replace( 'http:', 'https:', $PACMEC_WALLET_plugin_url_path ) : $PACMEC_WALLET_plugin_url_path );
                    // Set plugin options
                    $PACMEC_WALLET_options = get_option( 'pacmec-wallet_options', array() );
                    /**
                     * The ERC721 smart contract ABI
                     *
                     * @var string The ERC721 smart contract ABI
                     * @see https://ropsten.etherscan.io/address/0x5c007a1d8051dfda60b3692008b9e10731b67fde#code
                     */
                    $PACMEC_WALLET_erc721ContractABI = '[{"constant":true,"inputs":[{"name":"_interfaceID","type":"bytes4"}],"name":"supportsInterface","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"_name","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_tokenId","type":"uint256"}],"name":"getApproved","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_approved","type":"address"},{"name":"_tokenId","type":"uint256"}],"name":"approve","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_to","type":"address"},{"name":"_tokenId","type":"uint256"}],"name":"transferFrom","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_to","type":"address"},{"name":"_tokenId","type":"uint256"}],"name":"safeTransferFrom","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_tokenId","type":"uint256"}],"name":"burn","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"_tokenId","type":"uint256"}],"name":"ownerOf","outputs":[{"name":"_owner","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_owner","type":"address"}],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"name":"_symbol","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_operator","type":"address"},{"name":"_approved","type":"bool"}],"name":"setApprovalForAll","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_to","type":"address"},{"name":"_tokenId","type":"uint256"},{"name":"_data","type":"bytes"}],"name":"safeTransferFrom","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"_tokenId","type":"uint256"}],"name":"tokenURI","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_to","type":"address"},{"name":"_tokenId","type":"uint256"},{"name":"_uri","type":"string"}],"name":"mint","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"_owner","type":"address"},{"name":"_operator","type":"address"}],"name":"isApprovedForAll","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"inputs":[{"name":"_name","type":"string"},{"name":"_symbol","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"name":"_from","type":"address"},{"indexed":true,"name":"_to","type":"address"},{"indexed":true,"name":"_tokenId","type":"uint256"}],"name":"Transfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"_owner","type":"address"},{"indexed":true,"name":"_approved","type":"address"},{"indexed":true,"name":"_tokenId","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"_owner","type":"address"},{"indexed":true,"name":"_operator","type":"address"},{"indexed":false,"name":"_approved","type":"bool"}],"name":"ApprovalForAll","type":"event"}]';
                    require $PACMEC_WALLET_plugin_dir . '/vendor/autoload.php';
                    require $PACMEC_WALLET_plugin_dir . '/currencyconvertor.php';
                    function PACMEC_WALLET_init()
                    {
                        global  $PACMEC_WALLET_plugin_dir, $PACMEC_WALLET_plugin_basename, $PACMEC_WALLET_options ;
                        // Load the textdomain for translations
                        load_plugin_textdomain( 'pacmec-wallet', false, $PACMEC_WALLET_plugin_basename . '/languages/' );
                    }
                    
                    add_filter( 'init', 'PACMEC_WALLET_init' );
                    // Takes a hex (string) address as input
                    function PACMEC_WALLET_checksum_encode( $addr_str )
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
                    function PACMEC_WALLET_address_from_key( $privateKeyHex )
                    {
                        $privateKeyFactory = new PrivateKeyFactory();
                        $privateKey = $privateKeyFactory->fromHexUncompressed( $privateKeyHex );
                        $pubKeyHex = $privateKey->getPublicKey()->getHex();
                        $hash = Keccak::hash( substr( hex2bin( $pubKeyHex ), 1 ), 256 );
                        $ethAddress = '0x' . substr( $hash, 24 );
                        $ethAddressChkSum = PACMEC_WALLET_checksum_encode( $ethAddress );
                        return $ethAddressChkSum;
                    }
                    
                    function PACMEC_WALLET_create_account()
                    {
                        $random = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\Random();
                        $privateKeyBuffer = $random->bytes( 32 );
                        $privateKeyHex = $privateKeyBuffer->getHex();
                        $ethAddressChkSum = PACMEC_WALLET_address_from_key( $privateKeyHex );
                        return [ $ethAddressChkSum, $privateKeyHex ];
                    }
                    
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    // create Ethereum wallet on user register
                    // see https://wp-kama.ru/hook/user_register
                    add_action( 'user_register', 'PACMEC_WALLET_user_registration' );
                    function PACMEC_WALLET_user_registration( $user_id )
                    {
                        $accountsJSON = get_user_meta( $user_id, 'user_ethereum_wallet_accounts', true );
                        
                        if ( !empty($accountsJSON) ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_user_registration: account already exists" );
                            return;
                        }
                        
                        list( $ethAddressChkSum, $privateKeyHex ) = PACMEC_WALLET_create_account();
                        $accounts = [ [
                            "name"    => __( 'Default account', 'pacmec-wallet' ),
                            "address" => $ethAddressChkSum,
                            "key"     => $privateKeyHex,
                        ] ];
                        // @see https://stackoverflow.com/a/44263857/4256005
                        update_user_meta( $user_id, 'user_ethereum_wallet_accounts', json_encode( $accounts, JSON_UNESCAPED_UNICODE ) );
                        // set default account
                        update_user_meta( $user_id, 'user_ethereum_wallet_address', $ethAddressChkSum );
                        update_user_meta( $user_id, 'user_ethereum_wallet_key', $privateKeyHex );
                    }
                    
                    function PACMEC_WALLET_calc_display_value( $value )
                    {
                        if ( $value < 1 ) {
                            return [ 0.01 * floor( 100 * $value ), __( 'CTN', 'pacmec-wallet' ) ];
                        }
                        if ( $value < 1000 ) {
                            return [ 0.1 * floor( 10 * $value ), __( 'CTN', 'pacmec-wallet' ) ];
                        }
                        if ( $value < 1000000 ) {
                            return [ 0.1 * floor( 10 * 0.001 * $value ), __( 'K', 'pacmec-wallet' ) ];
                        }
                        return [ 0.1 * floor( 10 * 1.0E-6 * $value ), __( 'M', 'pacmec-wallet' ) ];
                    }
                    
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    class PACMEC_WALLET_Logger
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
                    function PACMEC_WALLET_log( $error )
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
                                $logger = new PACMEC_WALLET_Logger();
                            }
                        
                        }
                        $logger->add( 'pacmec-wallet', $error );
                    }
                    
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    function PACMEC_WALLET_getBalanceEth( $providerUrl, $accountAddress, $web3 = null )
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
															PACMEC_WALLET_log( "Failed to getBalance: " . $err );
															$error = $err;
															return;
														}
														$ether_quantity_wei = $balance;
												} );
												return [ $error, $ether_quantity_wei ];
											} catch ( \Exception $ex ) {
												PACMEC_WALLET_log( $ex->getMessage() );
												if ( is_null( $error ) ) {
														$error = $ex->getMessage();
												}
												return [ $error, null ];
											}
                    }
										
                    function PACMEC_WALLET_getBalanceToken( $providerUrl, $accountAddress, $contractAddress, $web3 = null )
                    {
											$PACMEC_WALLET_ContractABI      = '[{"inputs":[{"internalType":"uint256","name":"initialSupply","type":"uint256"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"bytes32","name":"role","type":"bytes32"},{"indexed":true,"internalType":"bytes32","name":"previousAdminRole","type":"bytes32"},{"indexed":true,"internalType":"bytes32","name":"newAdminRole","type":"bytes32"}],"name":"RoleAdminChanged","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"bytes32","name":"role","type":"bytes32"},{"indexed":true,"internalType":"address","name":"account","type":"address"},{"indexed":true,"internalType":"address","name":"sender","type":"address"}],"name":"RoleGranted","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"bytes32","name":"role","type":"bytes32"},{"indexed":true,"internalType":"address","name":"account","type":"address"},{"indexed":true,"internalType":"address","name":"sender","type":"address"}],"name":"RoleRevoked","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[],"name":"BURNER_ROLE","outputs":[{"internalType":"bytes32","name":"","type":"bytes32"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"DEFAULT_ADMIN_ROLE","outputs":[{"internalType":"bytes32","name":"","type":"bytes32"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"EXCHAN_ROLE","outputs":[{"internalType":"bytes32","name":"","type":"bytes32"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"MINTER_ROLE","outputs":[{"internalType":"bytes32","name":"","type":"bytes32"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"from","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"burn","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"buy","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"buyActived","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"buyOff","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"buyOn","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"buyPrice","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"to","type":"address"}],"name":"buyTo","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"bytes32","name":"role","type":"bytes32"}],"name":"getRoleAdmin","outputs":[{"internalType":"bytes32","name":"","type":"bytes32"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"bytes32","name":"role","type":"bytes32"},{"internalType":"address","name":"account","type":"address"}],"name":"grantRole","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"bytes32","name":"role","type":"bytes32"},{"internalType":"address","name":"account","type":"address"}],"name":"hasRole","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"mint","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"mintActived","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"mintOff","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"mintOn","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"renounceOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"bytes32","name":"role","type":"bytes32"},{"internalType":"address","name":"account","type":"address"}],"name":"renounceRole","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"bytes32","name":"role","type":"bytes32"},{"internalType":"address","name":"account","type":"address"}],"name":"revokeRole","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"sell","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"sellActived","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"sellOff","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"sellOn","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"sellPrice","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"newSellPrice","type":"uint256"},{"internalType":"uint256","name":"newBuyPrice","type":"uint256"}],"name":"setPrices","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"bytes4","name":"interfaceId","type":"bytes4"}],"name":"supportsInterface","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"version","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"}]';
											$PACMEC_WALLET_ContractBYTECODE = '60806040526040518060400160405280600381526020017f312e300000000000000000000000000000000000000000000000000000000000815250600790805190602001906200005192919062000649565b50629b739b60095562b90fe1600a553480156200006d57600080fd5b5060405162003f2e38038062003f2e833981810160405281019062000093919062000710565b6040518060400160405280600481526020017f44434f50000000000000000000000000000000000000000000000000000000008152506040518060400160405280600481526020017f44434f500000000000000000000000000000000000000000000000000000000081525081600390805190602001906200011792919062000649565b5080600490805190602001906200013092919062000649565b50505062000153620001476200028560201b60201c565b6200028d60201b60201c565b6001600860006101000a81548160ff0219169083151502179055506001600860026101000a81548160ff0219169083151502179055506001600860016101000a81548160ff021916908315150217905550620001d67f9f2df0fed2c77648de5860a4cc508cd0818c85b8b8a1ab4ceeef8d981c8956a6336200035360201b60201c565b620002087f3c11d16cbaffd01df69ce1c404f6340ee057498f5f00246190ea54220576a848336200035360201b60201c565b6200023a7fb424e57ec8efae719991fd85f6cd7e102224f476e98ec83aa343016637171090336200035360201b60201c565b6200024f6000801b336200035360201b60201c565b6200026130826200036960201b60201c565b6200027e336a084595161401484a0000006200036960201b60201c565b506200090d565b600033905090565b6000600660009054906101000a900473ffffffffffffffffffffffffffffffffffffffff16905081600660006101000a81548173ffffffffffffffffffffffffffffffffffffffff021916908373ffffffffffffffffffffffffffffffffffffffff1602179055508173ffffffffffffffffffffffffffffffffffffffff168173ffffffffffffffffffffffffffffffffffffffff167f8be0079c531659141344cd1fd0a4f28419497f9722a3daafe3b4186f6b6457e060405160405180910390a35050565b620003658282620004e260201b60201c565b5050565b600073ffffffffffffffffffffffffffffffffffffffff168273ffffffffffffffffffffffffffffffffffffffff161415620003dc576040517f08c379a0000000000000000000000000000000000000000000000000000000008152600401620003d3906200077a565b60405180910390fd5b620003f060008383620005d460201b60201c565b8060026000828254620004049190620007ca565b92505081905550806000808473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060008282546200045b9190620007ca565b925050819055508173ffffffffffffffffffffffffffffffffffffffff16600073ffffffffffffffffffffffffffffffffffffffff167fddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef83604051620004c291906200079c565b60405180910390a3620004de60008383620005d960201b60201c565b5050565b620004f48282620005de60201b60201c565b620005d05760016005600084815260200190815260200160002060000160008373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060006101000a81548160ff021916908315150217905550620005756200028560201b60201c565b73ffffffffffffffffffffffffffffffffffffffff168173ffffffffffffffffffffffffffffffffffffffff16837f2f8788117e7eff1d82e926ec794901d17c78024a50270940304540a733656f0d60405160405180910390a45b5050565b505050565b505050565b60006005600084815260200190815260200160002060000160008373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060009054906101000a900460ff16905092915050565b828054620006579062000831565b90600052602060002090601f0160209004810192826200067b5760008555620006c7565b82601f106200069657805160ff1916838001178555620006c7565b82800160010185558215620006c7579182015b82811115620006c6578251825591602001919060010190620006a9565b5b509050620006d69190620006da565b5090565b5b80821115620006f5576000816000905550600101620006db565b5090565b6000815190506200070a81620008f3565b92915050565b600060208284031215620007295762000728620008c5565b5b60006200073984828501620006f9565b91505092915050565b600062000751601f83620007b9565b91506200075e82620008ca565b602082019050919050565b620007748162000827565b82525050565b60006020820190508181036000830152620007958162000742565b9050919050565b6000602082019050620007b3600083018462000769565b92915050565b600082825260208201905092915050565b6000620007d78262000827565b9150620007e48362000827565b9250827fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff038211156200081c576200081b62000867565b5b828201905092915050565b6000819050919050565b600060028204905060018216806200084a57607f821691505b6020821081141562000861576200086062000896565b5b50919050565b7f4e487b7100000000000000000000000000000000000000000000000000000000600052601160045260246000fd5b7f4e487b7100000000000000000000000000000000000000000000000000000000600052602260045260246000fd5b600080fd5b7f45524332303a206d696e7420746f20746865207a65726f206164647265737300600082015250565b620008fe8162000827565b81146200090a57600080fd5b50565b613611806200091d6000396000f3fe6080604052600436106102515760003560e01c80636876a84611610139578063a457c2d7116100b6578063d547741f1161007a578063d547741f14610861578063dcf72c101461088a578063dd62ed3e146108a6578063e4849b32146108e3578063efa659b21461090c578063f2fde38b1461092357610251565b8063a457c2d714610787578063a64c2ade146107c4578063a6f2ae3a146107ef578063a9059cbb146107f9578063d53913931461083657610251565b80638da5cb5b116100fd5780638da5cb5b146106a057806391d14854146106cb57806395d89b41146107085780639dc29fac14610733578063a217fddf1461075c57610251565b80636876a846146105df57806370a08231146105f6578063715018a6146106335780637b4ad19e1461064a5780638620410b1461067557610251565b80632f4da27c116101d25780633eed17c7116101965780633eed17c71461050757806340c10f191461051e578063485a345a146105475780634b7503341461055e57806354fd4d5014610589578063660db5a5146105b457610251565b80632f4da27c14610448578063313ce5671461045f57806336568abe1461048a57806339509351146104b35780633b936ebb146104f057610251565b806323b872dd1161021957806323b872dd1461034f578063248a9ca31461038c578063280d52b3146103c9578063282c51f3146103f45780632f2ff15d1461041f57610251565b806301ffc9a71461025657806305fefda71461029357806306fdde03146102bc578063095ea7b3146102e757806318160ddd14610324575b600080fd5b34801561026257600080fd5b5061027d600480360381019061027891906127a7565b61094c565b60405161028a9190612ba8565b60405180910390f35b34801561029f57600080fd5b506102ba60048036038101906102b5919061282e565b6109c6565b005b3480156102c857600080fd5b506102d1610a0b565b6040516102de9190612bde565b60405180910390f35b3480156102f357600080fd5b5061030e600480360381019061030991906126fa565b610a9d565b60405161031b9190612ba8565b60405180910390f35b34801561033057600080fd5b50610339610abb565b6040516103469190612dc0565b60405180910390f35b34801561035b57600080fd5b50610376600480360381019061037191906126a7565b610ac5565b6040516103839190612ba8565b60405180910390f35b34801561039857600080fd5b506103b360048036038101906103ae919061273a565b610bbd565b6040516103c09190612bc3565b60405180910390f35b3480156103d557600080fd5b506103de610bdd565b6040516103eb9190612ba8565b60405180910390f35b34801561040057600080fd5b50610409610bf0565b6040516104169190612bc3565b60405180910390f35b34801561042b57600080fd5b5061044660048036038101906104419190612767565b610c14565b005b34801561045457600080fd5b5061045d610c3d565b005b34801561046b57600080fd5b50610474610c8d565b6040516104819190612ddb565b60405180910390f35b34801561049657600080fd5b506104b160048036038101906104ac9190612767565b610c96565b005b3480156104bf57600080fd5b506104da60048036038101906104d591906126fa565b610d19565b6040516104e79190612ba8565b60405180910390f35b3480156104fc57600080fd5b50610505610dc5565b005b34801561051357600080fd5b5061051c610e15565b005b34801561052a57600080fd5b50610545600480360381019061054091906126fa565b610e65565b005b34801561055357600080fd5b5061055c610ebf565b005b34801561056a57600080fd5b50610573610f0f565b6040516105809190612dc0565b60405180910390f35b34801561059557600080fd5b5061059e610f15565b6040516105ab9190612bde565b60405180910390f35b3480156105c057600080fd5b506105c9610fa3565b6040516105d69190612ba8565b60405180910390f35b3480156105eb57600080fd5b506105f4610fb6565b005b34801561060257600080fd5b5061061d6004803603810190610618919061263a565b611006565b60405161062a9190612dc0565b60405180910390f35b34801561063f57600080fd5b5061064861104e565b005b34801561065657600080fd5b5061065f6110d6565b60405161066c9190612bc3565b60405180910390f35b34801561068157600080fd5b5061068a6110fa565b6040516106979190612dc0565b60405180910390f35b3480156106ac57600080fd5b506106b5611100565b6040516106c29190612b8d565b60405180910390f35b3480156106d757600080fd5b506106f260048036038101906106ed9190612767565b61112a565b6040516106ff9190612ba8565b60405180910390f35b34801561071457600080fd5b5061071d611195565b60405161072a9190612bde565b60405180910390f35b34801561073f57600080fd5b5061075a600480360381019061075591906126fa565b611227565b005b34801561076857600080fd5b50610771611268565b60405161077e9190612bc3565b60405180910390f35b34801561079357600080fd5b506107ae60048036038101906107a991906126fa565b61126f565b6040516107bb9190612ba8565b60405180910390f35b3480156107d057600080fd5b506107d961135a565b6040516107e69190612ba8565b60405180910390f35b6107f761136d565b005b34801561080557600080fd5b50610820600480360381019061081b91906126fa565b611478565b60405161082d9190612ba8565b60405180910390f35b34801561084257600080fd5b5061084b611496565b6040516108589190612bc3565b60405180910390f35b34801561086d57600080fd5b5061088860048036038101906108839190612767565b6114ba565b005b6108a4600480360381019061089f919061263a565b6114e3565b005b3480156108b257600080fd5b506108cd60048036038101906108c89190612667565b6115ef565b6040516108da9190612dc0565b60405180910390f35b3480156108ef57600080fd5b5061090a600480360381019061090591906127d4565b611676565b005b34801561091857600080fd5b50610921611728565b005b34801561092f57600080fd5b5061094a6004803603810190610945919061263a565b611778565b005b60007f7965db0b000000000000000000000000000000000000000000000000000000007bffffffffffffffffffffffffffffffffffffffffffffffffffffffff1916827bffffffffffffffffffffffffffffffffffffffffffffffffffffffff191614806109bf57506109be82611870565b5b9050919050565b7fb424e57ec8efae719991fd85f6cd7e102224f476e98ec83aa3430166371710906109f8816109f36118da565b6118e2565b8260098190555081600a81905550505050565b606060038054610a1a9061301a565b80601f0160208091040260200160405190810160405280929190818152602001828054610a469061301a565b8015610a935780601f10610a6857610100808354040283529160200191610a93565b820191906000526020600020905b815481529060010190602001808311610a7657829003601f168201915b5050505050905090565b6000610ab1610aaa6118da565b848461197f565b6001905092915050565b6000600254905090565b6000610ad2848484611b4a565b6000600160008673ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000206000610b1d6118da565b73ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002054905082811015610b9d576040517f08c379a0000000000000000000000000000000000000000000000000000000008152600401610b9490612cc0565b60405180910390fd5b610bb185610ba96118da565b85840361197f565b60019150509392505050565b600060056000838152602001908152602001600020600101549050919050565b600860009054906101000a900460ff1681565b7f3c11d16cbaffd01df69ce1c404f6340ee057498f5f00246190ea54220576a84881565b610c1d82610bbd565b610c2e81610c296118da565b6118e2565b610c388383611dcb565b505050565b7f9f2df0fed2c77648de5860a4cc508cd0818c85b8b8a1ab4ceeef8d981c8956a6610c6f81610c6a6118da565b6118e2565b6001600860006101000a81548160ff02191690831515021790555050565b60006012905090565b610c9e6118da565b73ffffffffffffffffffffffffffffffffffffffff168173ffffffffffffffffffffffffffffffffffffffff1614610d0b576040517f08c379a0000000000000000000000000000000000000000000000000000000008152600401610d0290612d80565b60405180910390fd5b610d158282611eac565b5050565b6000610dbb610d266118da565b848460016000610d346118da565b73ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060008873ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002054610db69190612e1d565b61197f565b6001905092915050565b7fb424e57ec8efae719991fd85f6cd7e102224f476e98ec83aa343016637171090610df781610df26118da565b6118e2565b6001600860016101000a81548160ff02191690831515021790555050565b7fb424e57ec8efae719991fd85f6cd7e102224f476e98ec83aa343016637171090610e4781610e426118da565b6118e2565b6000600860026101000a81548160ff02191690831515021790555050565b7f9f2df0fed2c77648de5860a4cc508cd0818c85b8b8a1ab4ceeef8d981c8956a6610e9781610e926118da565b6118e2565b600860009054906101000a900460ff16610eb057600080fd5b610eba8383611f8e565b505050565b7fb424e57ec8efae719991fd85f6cd7e102224f476e98ec83aa343016637171090610ef181610eec6118da565b6118e2565b6001600860026101000a81548160ff02191690831515021790555050565b60095481565b60078054610f229061301a565b80601f0160208091040260200160405190810160405280929190818152602001828054610f4e9061301a565b8015610f9b5780601f10610f7057610100808354040283529160200191610f9b565b820191906000526020600020905b815481529060010190602001808311610f7e57829003601f168201915b505050505081565b600860019054906101000a900460ff1681565b7f9f2df0fed2c77648de5860a4cc508cd0818c85b8b8a1ab4ceeef8d981c8956a6610fe881610fe36118da565b6118e2565b6000600860006101000a81548160ff02191690831515021790555050565b60008060008373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020549050919050565b6110566118da565b73ffffffffffffffffffffffffffffffffffffffff16611074611100565b73ffffffffffffffffffffffffffffffffffffffff16146110ca576040517f08c379a00000000000000000000000000000000000000000000000000000000081526004016110c190612ce0565b60405180910390fd5b6110d460006120ee565b565b7fb424e57ec8efae719991fd85f6cd7e102224f476e98ec83aa34301663717109081565b600a5481565b6000600660009054906101000a900473ffffffffffffffffffffffffffffffffffffffff16905090565b60006005600084815260200190815260200160002060000160008373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060009054906101000a900460ff16905092915050565b6060600480546111a49061301a565b80601f01602080910402602001604051908101604052809291908181526020018280546111d09061301a565b801561121d5780601f106111f25761010080835404028352916020019161121d565b820191906000526020600020905b81548152906001019060200180831161120057829003601f168201915b5050505050905090565b7f3c11d16cbaffd01df69ce1c404f6340ee057498f5f00246190ea54220576a848611259816112546118da565b6118e2565b61126383836121b4565b505050565b6000801b81565b6000806001600061127e6118da565b73ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000205490508281101561133b576040517f08c379a000000000000000000000000000000000000000000000000000000000815260040161133290612d60565b60405180910390fd5b61134f6113466118da565b8585840361197f565b600191505092915050565b600860029054906101000a900460ff1681565b600860019054906101000a900460ff1661138657600080fd5b6000600a54346113969190612e73565b905080600a543073ffffffffffffffffffffffffffffffffffffffff166370a08231306040518263ffffffff1660e01b81526004016113d59190612b8d565b60206040518083038186803b1580156113ed57600080fd5b505afa158015611401573d6000803e3d6000fd5b505050506040513d601f19601f820116820180604052508101906114259190612801565b61142f9190612e73565b106114445761143f303383611b4a565b611475565b600860009054906101000a900460ff1661145d57600080fd5b61147433600a543461146f9190612e73565b611f8e565b5b50565b600061148c6114856118da565b8484611b4a565b6001905092915050565b7f9f2df0fed2c77648de5860a4cc508cd0818c85b8b8a1ab4ceeef8d981c8956a681565b6114c382610bbd565b6114d4816114cf6118da565b6118e2565b6114de8383611eac565b505050565b600860019054906101000a900460ff166114fc57600080fd5b6000600a543461150c9190612e73565b905080600a543073ffffffffffffffffffffffffffffffffffffffff166370a08231306040518263ffffffff1660e01b815260040161154b9190612b8d565b60206040518083038186803b15801561156357600080fd5b505afa158015611577573d6000803e3d6000fd5b505050506040513d601f19601f8201168201806040525081019061159b9190612801565b6115a59190612e73565b106115ba576115b5308383611b4a565b6115eb565b600860009054906101000a900460ff166115d357600080fd5b6115ea82600a54346115e59190612e73565b611f8e565b5b5050565b6000600160008473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060008373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002054905092915050565b600860029054906101000a900460ff1661168f57600080fd5b6000309050600954826116a29190612ea4565b8173ffffffffffffffffffffffffffffffffffffffff163110156116c557600080fd5b6116d0338284611b4a565b3373ffffffffffffffffffffffffffffffffffffffff166108fc600954846116f89190612ea4565b9081150290604051600060405180830381858888f19350505050158015611723573d6000803e3d6000fd5b505050565b7fb424e57ec8efae719991fd85f6cd7e102224f476e98ec83aa34301663717109061175a816117556118da565b6118e2565b6000600860016101000a81548160ff02191690831515021790555050565b6117806118da565b73ffffffffffffffffffffffffffffffffffffffff1661179e611100565b73ffffffffffffffffffffffffffffffffffffffff16146117f4576040517f08c379a00000000000000000000000000000000000000000000000000000000081526004016117eb90612ce0565b60405180910390fd5b600073ffffffffffffffffffffffffffffffffffffffff168173ffffffffffffffffffffffffffffffffffffffff161415611864576040517f08c379a000000000000000000000000000000000000000000000000000000000815260040161185b90612c60565b60405180910390fd5b61186d816120ee565b50565b60007f01ffc9a7000000000000000000000000000000000000000000000000000000007bffffffffffffffffffffffffffffffffffffffffffffffffffffffff1916827bffffffffffffffffffffffffffffffffffffffffffffffffffffffff1916149050919050565b600033905090565b6118ec828261112a565b61197b576119118173ffffffffffffffffffffffffffffffffffffffff16601461238b565b61191f8360001c602061238b565b604051602001611930929190612b53565b6040516020818303038152906040526040517f08c379a00000000000000000000000000000000000000000000000000000000081526004016119729190612bde565b60405180910390fd5b5050565b600073ffffffffffffffffffffffffffffffffffffffff168373ffffffffffffffffffffffffffffffffffffffff1614156119ef576040517f08c379a00000000000000000000000000000000000000000000000000000000081526004016119e690612d40565b60405180910390fd5b600073ffffffffffffffffffffffffffffffffffffffff168273ffffffffffffffffffffffffffffffffffffffff161415611a5f576040517f08c379a0000000000000000000000000000000000000000000000000000000008152600401611a5690612c80565b60405180910390fd5b80600160008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060008473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020819055508173ffffffffffffffffffffffffffffffffffffffff168373ffffffffffffffffffffffffffffffffffffffff167f8c5be1e5ebec7d5bd14f71427d1e84f3dd0314c0f7b2291e5b200ac8c7c3b92583604051611b3d9190612dc0565b60405180910390a3505050565b600073ffffffffffffffffffffffffffffffffffffffff168373ffffffffffffffffffffffffffffffffffffffff161415611bba576040517f08c379a0000000000000000000000000000000000000000000000000000000008152600401611bb190612d20565b60405180910390fd5b600073ffffffffffffffffffffffffffffffffffffffff168273ffffffffffffffffffffffffffffffffffffffff161415611c2a576040517f08c379a0000000000000000000000000000000000000000000000000000000008152600401611c2190612c20565b60405180910390fd5b611c358383836125c7565b60008060008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002054905081811015611cbb576040517f08c379a0000000000000000000000000000000000000000000000000000000008152600401611cb290612ca0565b60405180910390fd5b8181036000808673ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002081905550816000808573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000206000828254611d4e9190612e1d565b925050819055508273ffffffffffffffffffffffffffffffffffffffff168473ffffffffffffffffffffffffffffffffffffffff167fddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef84604051611db29190612dc0565b60405180910390a3611dc58484846125cc565b50505050565b611dd5828261112a565b611ea85760016005600084815260200190815260200160002060000160008373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060006101000a81548160ff021916908315150217905550611e4d6118da565b73ffffffffffffffffffffffffffffffffffffffff168173ffffffffffffffffffffffffffffffffffffffff16837f2f8788117e7eff1d82e926ec794901d17c78024a50270940304540a733656f0d60405160405180910390a45b5050565b611eb6828261112a565b15611f8a5760006005600084815260200190815260200160002060000160008373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060006101000a81548160ff021916908315150217905550611f2f6118da565b73ffffffffffffffffffffffffffffffffffffffff168173ffffffffffffffffffffffffffffffffffffffff16837ff6391f5c32d9c69d2a47ea670b442974b53935d1edc7fd64eb21e047a839171b60405160405180910390a45b5050565b600073ffffffffffffffffffffffffffffffffffffffff168273ffffffffffffffffffffffffffffffffffffffff161415611ffe576040517f08c379a0000000000000000000000000000000000000000000000000000000008152600401611ff590612da0565b60405180910390fd5b61200a600083836125c7565b806002600082825461201c9190612e1d565b92505081905550806000808473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060008282546120719190612e1d565b925050819055508173ffffffffffffffffffffffffffffffffffffffff16600073ffffffffffffffffffffffffffffffffffffffff167fddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef836040516120d69190612dc0565b60405180910390a36120ea600083836125cc565b5050565b6000600660009054906101000a900473ffffffffffffffffffffffffffffffffffffffff16905081600660006101000a81548173ffffffffffffffffffffffffffffffffffffffff021916908373ffffffffffffffffffffffffffffffffffffffff1602179055508173ffffffffffffffffffffffffffffffffffffffff168173ffffffffffffffffffffffffffffffffffffffff167f8be0079c531659141344cd1fd0a4f28419497f9722a3daafe3b4186f6b6457e060405160405180910390a35050565b600073ffffffffffffffffffffffffffffffffffffffff168273ffffffffffffffffffffffffffffffffffffffff161415612224576040517f08c379a000000000000000000000000000000000000000000000000000000000815260040161221b90612d00565b60405180910390fd5b612230826000836125c7565b60008060008473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020549050818110156122b6576040517f08c379a00000000000000000000000000000000000000000000000000000000081526004016122ad90612c40565b60405180910390fd5b8181036000808573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002081905550816002600082825461230d9190612efe565b92505081905550600073ffffffffffffffffffffffffffffffffffffffff168373ffffffffffffffffffffffffffffffffffffffff167fddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef846040516123729190612dc0565b60405180910390a3612386836000846125cc565b505050565b60606000600283600261239e9190612ea4565b6123a89190612e1d565b67ffffffffffffffff8111156123c1576123c0613108565b5b6040519080825280601f01601f1916602001820160405280156123f35781602001600182028036833780820191505090505b5090507f30000000000000000000000000000000000000000000000000000000000000008160008151811061242b5761242a6130d9565b5b60200101907effffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff1916908160001a9053507f78000000000000000000000000000000000000000000000000000000000000008160018151811061248f5761248e6130d9565b5b60200101907effffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff1916908160001a905350600060018460026124cf9190612ea4565b6124d99190612e1d565b90505b6001811115612579577f3031323334353637383961626364656600000000000000000000000000000000600f86166010811061251b5761251a6130d9565b5b1a60f81b828281518110612532576125316130d9565b5b60200101907effffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff1916908160001a905350600485901c94508061257290612ff0565b90506124dc565b50600084146125bd576040517f08c379a00000000000000000000000000000000000000000000000000000000081526004016125b490612c00565b60405180910390fd5b8091505092915050565b505050565b505050565b6000813590506125e08161357f565b92915050565b6000813590506125f581613596565b92915050565b60008135905061260a816135ad565b92915050565b60008135905061261f816135c4565b92915050565b600081519050612634816135c4565b92915050565b6000602082840312156126505761264f613137565b5b600061265e848285016125d1565b91505092915050565b6000806040838503121561267e5761267d613137565b5b600061268c858286016125d1565b925050602061269d858286016125d1565b9150509250929050565b6000806000606084860312156126c0576126bf613137565b5b60006126ce868287016125d1565b93505060206126df868287016125d1565b92505060406126f086828701612610565b9150509250925092565b6000806040838503121561271157612710613137565b5b600061271f858286016125d1565b925050602061273085828601612610565b9150509250929050565b6000602082840312156127505761274f613137565b5b600061275e848285016125e6565b91505092915050565b6000806040838503121561277e5761277d613137565b5b600061278c858286016125e6565b925050602061279d858286016125d1565b9150509250929050565b6000602082840312156127bd576127bc613137565b5b60006127cb848285016125fb565b91505092915050565b6000602082840312156127ea576127e9613137565b5b60006127f884828501612610565b91505092915050565b60006020828403121561281757612816613137565b5b600061282584828501612625565b91505092915050565b6000806040838503121561284557612844613137565b5b600061285385828601612610565b925050602061286485828601612610565b9150509250929050565b61287781612f32565b82525050565b61288681612f44565b82525050565b61289581612f50565b82525050565b60006128a682612df6565b6128b08185612e01565b93506128c0818560208601612fbd565b6128c98161313c565b840191505092915050565b60006128df82612df6565b6128e98185612e12565b93506128f9818560208601612fbd565b80840191505092915050565b6000612912602083612e01565b915061291d8261314d565b602082019050919050565b6000612935602383612e01565b915061294082613176565b604082019050919050565b6000612958602283612e01565b9150612963826131c5565b604082019050919050565b600061297b602683612e01565b915061298682613214565b604082019050919050565b600061299e602283612e01565b91506129a982613263565b604082019050919050565b60006129c1602683612e01565b91506129cc826132b2565b604082019050919050565b60006129e4602883612e01565b91506129ef82613301565b604082019050919050565b6000612a07602083612e01565b9150612a1282613350565b602082019050919050565b6000612a2a602183612e01565b9150612a3582613379565b604082019050919050565b6000612a4d602583612e01565b9150612a58826133c8565b604082019050919050565b6000612a70602483612e01565b9150612a7b82613417565b604082019050919050565b6000612a93601783612e12565b9150612a9e82613466565b601782019050919050565b6000612ab6602583612e01565b9150612ac18261348f565b604082019050919050565b6000612ad9601183612e12565b9150612ae4826134de565b601182019050919050565b6000612afc602f83612e01565b9150612b0782613507565b604082019050919050565b6000612b1f601f83612e01565b9150612b2a82613556565b602082019050919050565b612b3e81612fa6565b82525050565b612b4d81612fb0565b82525050565b6000612b5e82612a86565b9150612b6a82856128d4565b9150612b7582612acc565b9150612b8182846128d4565b91508190509392505050565b6000602082019050612ba2600083018461286e565b92915050565b6000602082019050612bbd600083018461287d565b92915050565b6000602082019050612bd8600083018461288c565b92915050565b60006020820190508181036000830152612bf8818461289b565b905092915050565b60006020820190508181036000830152612c1981612905565b9050919050565b60006020820190508181036000830152612c3981612928565b9050919050565b60006020820190508181036000830152612c598161294b565b9050919050565b60006020820190508181036000830152612c798161296e565b9050919050565b60006020820190508181036000830152612c9981612991565b9050919050565b60006020820190508181036000830152612cb9816129b4565b9050919050565b60006020820190508181036000830152612cd9816129d7565b9050919050565b60006020820190508181036000830152612cf9816129fa565b9050919050565b60006020820190508181036000830152612d1981612a1d565b9050919050565b60006020820190508181036000830152612d3981612a40565b9050919050565b60006020820190508181036000830152612d5981612a63565b9050919050565b60006020820190508181036000830152612d7981612aa9565b9050919050565b60006020820190508181036000830152612d9981612aef565b9050919050565b60006020820190508181036000830152612db981612b12565b9050919050565b6000602082019050612dd56000830184612b35565b92915050565b6000602082019050612df06000830184612b44565b92915050565b600081519050919050565b600082825260208201905092915050565b600081905092915050565b6000612e2882612fa6565b9150612e3383612fa6565b9250827fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff03821115612e6857612e6761304c565b5b828201905092915050565b6000612e7e82612fa6565b9150612e8983612fa6565b925082612e9957612e9861307b565b5b828204905092915050565b6000612eaf82612fa6565b9150612eba83612fa6565b9250817fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff0483118215151615612ef357612ef261304c565b5b828202905092915050565b6000612f0982612fa6565b9150612f1483612fa6565b925082821015612f2757612f2661304c565b5b828203905092915050565b6000612f3d82612f86565b9050919050565b60008115159050919050565b6000819050919050565b60007fffffffff0000000000000000000000000000000000000000000000000000000082169050919050565b600073ffffffffffffffffffffffffffffffffffffffff82169050919050565b6000819050919050565b600060ff82169050919050565b60005b83811015612fdb578082015181840152602081019050612fc0565b83811115612fea576000848401525b50505050565b6000612ffb82612fa6565b9150600082141561300f5761300e61304c565b5b600182039050919050565b6000600282049050600182168061303257607f821691505b60208210811415613046576130456130aa565b5b50919050565b7f4e487b7100000000000000000000000000000000000000000000000000000000600052601160045260246000fd5b7f4e487b7100000000000000000000000000000000000000000000000000000000600052601260045260246000fd5b7f4e487b7100000000000000000000000000000000000000000000000000000000600052602260045260246000fd5b7f4e487b7100000000000000000000000000000000000000000000000000000000600052603260045260246000fd5b7f4e487b7100000000000000000000000000000000000000000000000000000000600052604160045260246000fd5b600080fd5b6000601f19601f8301169050919050565b7f537472696e67733a20686578206c656e67746820696e73756666696369656e74600082015250565b7f45524332303a207472616e7366657220746f20746865207a65726f206164647260008201527f6573730000000000000000000000000000000000000000000000000000000000602082015250565b7f45524332303a206275726e20616d6f756e7420657863656564732062616c616e60008201527f6365000000000000000000000000000000000000000000000000000000000000602082015250565b7f4f776e61626c653a206e6577206f776e657220697320746865207a65726f206160008201527f6464726573730000000000000000000000000000000000000000000000000000602082015250565b7f45524332303a20617070726f766520746f20746865207a65726f20616464726560008201527f7373000000000000000000000000000000000000000000000000000000000000602082015250565b7f45524332303a207472616e7366657220616d6f756e742065786365656473206260008201527f616c616e63650000000000000000000000000000000000000000000000000000602082015250565b7f45524332303a207472616e7366657220616d6f756e742065786365656473206160008201527f6c6c6f77616e6365000000000000000000000000000000000000000000000000602082015250565b7f4f776e61626c653a2063616c6c6572206973206e6f7420746865206f776e6572600082015250565b7f45524332303a206275726e2066726f6d20746865207a65726f2061646472657360008201527f7300000000000000000000000000000000000000000000000000000000000000602082015250565b7f45524332303a207472616e736665722066726f6d20746865207a65726f20616460008201527f6472657373000000000000000000000000000000000000000000000000000000602082015250565b7f45524332303a20617070726f76652066726f6d20746865207a65726f2061646460008201527f7265737300000000000000000000000000000000000000000000000000000000602082015250565b7f416363657373436f6e74726f6c3a206163636f756e7420000000000000000000600082015250565b7f45524332303a2064656372656173656420616c6c6f77616e63652062656c6f7760008201527f207a65726f000000000000000000000000000000000000000000000000000000602082015250565b7f206973206d697373696e6720726f6c6520000000000000000000000000000000600082015250565b7f416363657373436f6e74726f6c3a2063616e206f6e6c792072656e6f756e636560008201527f20726f6c657320666f722073656c660000000000000000000000000000000000602082015250565b7f45524332303a206d696e7420746f20746865207a65726f206164647265737300600082015250565b61358881612f32565b811461359357600080fd5b50565b61359f81612f50565b81146135aa57600080fd5b50565b6135b681612f5a565b81146135c157600080fd5b50565b6135cd81612fa6565b81146135d857600080fd5b5056fea26469706673582212206635d8fa82f663781aab7b1521cae057dda7eef18df290fd864d1e6354e01a8f64736f6c63430008070033000000000000000000000000000000000000314dc6448d9338c15b0a00000000';
											
											$ether_quantity_wei = null;
											$error = null;
											
											try {
												if ( is_null( $web3 ) ) {
													$requestManager = new HttpRequestManager( $providerUrl, 10 );
													$web3 = new Web3( new HttpProvider( $requestManager ) );
												}
												
												$functionName = "balanceOf";
												$contract_i = new Contract($providerUrl, $PACMEC_WALLET_ContractABI);
												$contract = $contract_i->at($contractAddress);
												
												$res  = $contract->call('balanceOf', $accountAddress, function ($err, $balance) use(&$error, &$ether_quantity_wei) {
													if ( $err !== null ) {
														PACMEC_WALLET_log( "Failed to getBalance: " . $err );
														$error = $err;
														return;
													}
													$ether_quantity_wei = $balance[0];
													// $ether_quantity_wei = $balance[0]->toString();
													// $ether_quantity_wei = new BigInteger($balance[0]);
												});
												
												return [ $error, $ether_quantity_wei ];
											} catch ( \Exception $ex ) {
												PACMEC_WALLET_log( $ex->getMessage() );
												if ( is_null( $error ) ) {
													$error = $ex->getMessage();
												}
												return [ $error, null ];
											}
                    }
                    
                    function PACMEC_WALLET_getLatestBlock( $eth )
                    {
                        static  $_block_saved = null ;
                        $error = null;
                        
                        if ( is_null( $_block_saved ) ) {
                            $block = null;
                            $eth->getBlockByNumber( 'latest', false, function ( $err, $_block ) use( &$block, &$error ) {
                                
                                if ( $err !== null ) {
                                    PACMEC_WALLET_log( "Failed to getBlockByNumber: " . $err );
                                    $error = $err;
                                    return;
                                }
                                
                                $block = $_block;
                                // PACMEC_WALLET_log("PACMEC_WALLET_getLatestBlock: " . print_r($_block, true));
                                PACMEC_WALLET_log( "PACMEC_WALLET_getLatestBlock: latest" );
                            } );
                            $_block_saved = $block;
                        }
                        
                        return [ $error, $_block_saved ];
                    }
                    
                    function PACMEC_WALLET_getWeb3Endpoint()
                    {
                        global  $PACMEC_WALLET_options ;
                        $infuraApiKey = '';
                        if ( isset( $PACMEC_WALLET_options['infuraApiKey'] ) ) {
                            $infuraApiKey = esc_attr( $PACMEC_WALLET_options['infuraApiKey'] );
                        }
                        $blockchainNetwork = PACMEC_WALLET_getBlockchainNetwork();
                        if ( empty($blockchainNetwork) ) {
                            $blockchainNetwork = 'mainnet';
                        }
                        $web3Endpoint = "https://" . esc_attr( $blockchainNetwork ) . ".infura.io/v3/" . esc_attr( $infuraApiKey );
                        return $web3Endpoint;
                    }
                    
                    function PACMEC_WALLET_getWeb3WSSEndpoint()
                    {
                        global  $PACMEC_WALLET_options ;
                        $infuraApiKey = '';
                        if ( isset( $PACMEC_WALLET_options['infuraApiKey'] ) ) {
                            $infuraApiKey = esc_attr( $PACMEC_WALLET_options['infuraApiKey'] );
                        }
                        $blockchainNetwork = PACMEC_WALLET_getBlockchainNetwork();
                        if ( empty($blockchainNetwork) ) {
                            $blockchainNetwork = 'mainnet';
                        }
                        $web3WSSEndpoint = "wss://" . esc_attr( $blockchainNetwork ) . ".infura.io/ws/v3/" . esc_attr( $infuraApiKey );
                        return $web3WSSEndpoint;
                    }
                    
                    function PACMEC_WALLET_getBlockchainNetwork()
                    {
                        global  $PACMEC_WALLET_options ;
                        $blockchainNetwork = 'mainnet';
                        if ( !isset( $PACMEC_WALLET_options['blockchain_network'] ) ) {
                            return $blockchainNetwork;
                        }
                        if ( empty($PACMEC_WALLET_options['blockchain_network']) ) {
                            return $blockchainNetwork;
                        }
                        $blockchainNetwork = esc_attr( $PACMEC_WALLET_options['blockchain_network'] );
                        return $blockchainNetwork;
                    }
                    
                    function PACMEC_WALLET_balance_shortcode( $attrs )
                    {
                        global  $PACMEC_WALLET_options ;
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $accountAddress = PACMEC_WALLET_get_wallet_address();
                        $attributes = shortcode_atts( array(
													'tokensymbol'       => '',
													'tokenname'         => '',
													'tokenaddress'      => '',
													'tokendecimals'     => '18',
													'tokendecimalchar'  => '.',
													'tokenwooproduct'   => '',
													'tokeniconpath'     => '',
													'displayfiat'       => '0',
													'displayfiatsymbol' => '0',
                        ), $attrs, 'pacmec-wallet-balance' );
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
                        $displayFiat = ( !empty($attributes['displayfiat']) ? boolval( esc_attr( $attributes['displayfiat'] ) ) : false );
                        $displayfiatsymbol = ( !empty($attributes['displayfiatsymbol']) ? esc_attr( $attributes['displayfiatsymbol'] ) : "COP" );
                        $providerUrl = PACMEC_WALLET_getWeb3Endpoint();
                        
                        
                        /**
                         * @param BigInteger $balance The Ether or Token balance in wei.
                         */
                        $balance = new BigInteger( 0 );
                        $strBalance = '0';
                        $strBalanceNum = '0';
                        $strCurrencyName = __( 'Citrino', 'pacmec-wallet' );
                        $strCurrencySymbol = "CTN";
												
                        if ( !empty($tokenAddress)) {
													if(!empty($tokenName)) $strCurrencyName = $tokenName;
													if(!empty($tokenSymbol)) $strCurrencySymbol = $tokenSymbol;
                        }
												
												
												
                        if ( !empty($accountAddress) ) {
													if ( empty($tokenAddress) ) {
														// CTN
														list( $error, $balance ) = PACMEC_WALLET_getBalanceEth( $providerUrl, $accountAddress );
														
													} else {
														list( $error, $balance ) = PACMEC_WALLET_getBalanceToken( $providerUrl, $accountAddress, $tokenAddress );
														// if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
													}
													
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
														$strBalance = __( 'Failed to retrieve balance', 'pacmec-wallet' );
													}
                        }
                        $js = '';
                        $fiatBalance = null;
                        $exchangeRate = null;
                        
                        if ( $product_id ) {
                            // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                        } else {
                            if ( "CTN" == $strCurrencySymbol && $displayFiat ) {
                                if ( function_exists( "get_woocommerce_currency" ) ) {
                                    $currency = get_woocommerce_currency();
                                    if ( $currency != 'MYC' && !(function_exists( 'mycred_point_type_exists' ) && mycred_point_type_exists( $currency )) ) {
                                        $cryptocompareApiKey = '';
                                        if ( isset( $PACMEC_WALLET_options['cryptocompare_api_key'] ) ) {
                                            $cryptocompareApiKey = esc_attr( $PACMEC_WALLET_options['cryptocompare_api_key'] );
                                        }
                                        $convertor = new CurrencyConvertor( 'COP', $currency, $cryptocompareApiKey );
                                        $exchangeRate = $convertor->get_exchange_rate();
                                        $fiatBalance = $convertor->convert( floatval( $strBalanceNum ) );
                                        $fiatBalance = 0.01 * floor( 1 * $fiatBalance );
                                        
                                        if ( function_exists( "wc_price" ) ) {
                                            $fiatBalance = wc_price( $fiatBalance );
                                            $exchangeRate = wc_price( $exchangeRate );
                                        }
                                    }
                                }
                            } else {
                                if ( function_exists( "get_woocommerce_currency" ) ) {
                                    $currency = get_woocommerce_currency();
                                    if ( $currency != 'MYC' && !(function_exists( 'mycred_point_type_exists' ) && mycred_point_type_exists( $currency )) ) {
                                        $cryptocompareApiKey = '';
                                        if ( isset( $PACMEC_WALLET_options['cryptocompare_api_key'] ) ) {
                                            $cryptocompareApiKey = esc_attr( $PACMEC_WALLET_options['cryptocompare_api_key'] );
                                        }
																				
                                        $convertor = new CurrencyConvertor( $displayfiatsymbol, $currency, $cryptocompareApiKey );
                                        $exchangeRate = $convertor->get_exchange_rate();
                                        $fiatBalance = $convertor->convert( floatval( $strBalanceNum ) );
                                        $fiatBalance = 0.01 * floor( 100 * $fiatBalance );
                                        
                                        if ( function_exists( "wc_price" ) ) {
                                            $fiatBalance = wc_price( $fiatBalance );
                                            $exchangeRate = wc_price( $exchangeRate );
                                        }
                                    }
                                }
														}
                        }
												
                        if ( is_null( $fiatBalance ) ) {
                            $ret = '<div class="twbs"><div class="container-fluid pacmec-wallet-balance-shortcode">
    <div class="row pacmec-wallet-balance-content">
        <div class="col-md-6 col-6 text-right pacmec-wallet-balance-value-wrapper">
            <div class="pacmec-wallet-balance-value">' . $strBalance . '</div>
        </div>
        <div class="col-md-6 col-6 pacmec-wallet-balance-token-name-wrapper">
            <div style="display: none" class="hidden pacmec-wallet-balance-token-address">' . $tokenAddress . '</div>
            <div style="display: none" class="hidden pacmec-wallet-balance-token-decimals">' . $tokenDecimals . '</div>
            <div style="display: none" class="hidden pacmec-wallet-balance-token-decimal-char">' . $tokenDecimalChar . '</div>
            <div class="pacmec-wallet-balance-token-name">' . $strCurrencySymbol . '</div>
        </div>
    </div>
</div></div>';
                        } else {
                            
                            if ( empty($tokenIconPath) ) {
                                $ret = '<div class="twbs"><div class="container-fluid pacmec-wallet-balance-shortcode">
    <div class="row pacmec-wallet-balance-content">
        <div class="col-md-4 col-4 text-right pacmec-wallet-balance-value-wrapper">
            <div class="pacmec-wallet-balance-value">' . $strBalance . '</div>
        </div>
        <div class="col-md-4 col-4 pacmec-wallet-balance-token-name-wrapper">
            <div style="display: none" class="hidden pacmec-wallet-balance-token-address">' . $tokenAddress . '</div>
            <div style="display: none" class="hidden pacmec-wallet-balance-token-decimals">' . $tokenDecimals . '</div>
            <div style="display: none" class="hidden pacmec-wallet-balance-token-decimal-char">' . $tokenDecimalChar . '</div>
            <div class="pacmec-wallet-balance-token-name">' . $strCurrencySymbol . '</div>
        </div>
        <div class="col-md-4 col-4 pacmec-wallet-balance-fiat-value-wrapper">
            <div class="pacmec-wallet-balance-fiat-value">' . $fiatBalance . '</div>
        </div>
    </div>
</div></div>';
                            } else {
                                // @see https://bootsnipp.com/snippets/org5r
                                $ret = '<div class="twbs"><div class="container-fluid pacmec-wallet-balance-shortcode"><ul class="list-group">
    <li class="list-group-item pacmec-wallet-balance-content">
        <div class="col-7 col-xs-7 col-sm-7 pacmec-wallet-balance-wrapper-left">
            <div class="pacmec-wallet-balance-token-image-wrapper">
                <img src="' . $tokenIconPath . '" class="pacmec-wallet-balance-token-image">
            </div>
            <span class="pacmec-wallet-balance-token-long-name">' . $strCurrencyName . '</span><br>
            <span class="small pacmec-wallet-balance-exchange-rate">' . sprintf( __( '%1$s per %2$s', 'pacmec-wallet' ), $exchangeRate, $strCurrencySymbol ) . '</span>
        </div>
        <div style="display: none" class="hidden pacmec-wallet-balance-token-address">' . $tokenAddress . '</div>
        <div style="display: none" class="hidden pacmec-wallet-balance-token-symbol">' . $strCurrencySymbol . '</div>
        <div style="display: none" class="hidden pacmec-wallet-balance-token-decimals">' . $tokenDecimals . '</div>
        <div style="display: none" class="hidden pacmec-wallet-balance-token-decimal-char">' . $tokenDecimalChar . '</div>
        <div class="col-5 col-xs-5 col-sm-5 text-right pacmec-wallet-balance-wrapper-right">
            <span class="pacmec-wallet-balance-fiat-value">' . $fiatBalance . '</span><br>
            <span class="pacmec-wallet-balance-value">' . sprintf( __( '%1$s %2$s', 'pacmec-wallet' ), $strBalance, $strCurrencySymbol ) . '</span>
        </div>
        <div class="clearfix"></div>
    </li>
</ul></div></div>';
                            }
                        
                        }
                        
                        PACMEC_WALLET_enqueue_scripts_();
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'pacmec-wallet-balance', 'PACMEC_WALLET_balance_shortcode' );
                    function PACMEC_WALLET_dividends( $attrs )
                    {
                        global  $PACMEC_WALLET_options ;
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                        return '';
                    }
                    
                    add_shortcode( 'pacmec-wallet-dividends', 'PACMEC_WALLET_balance_dividends' );
                    function PACMEC_WALLET_account_shortcode( $attrs )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        static  $counter = 0 ;
                        ++$counter;
                        $accountAddress = PACMEC_WALLET_get_wallet_address();
                        $attributes = shortcode_atts( array(
                            'label'   => '',
                            'nolabel' => '',
                        ), $attrs, 'pacmec-wallet-account' );
                        $label = ( !empty($attributes['label']) ? esc_attr( $attributes['label'] ) : __( 'Account', 'pacmec-wallet' ) );
                        $nolabel = ( !empty($attributes['nolabel']) ? esc_attr( $attributes['nolabel'] ) : '' );
                        //    $labelTag = $label;
                        $labelTag = '<label class="control-label" for="pacmec-wallet-account">' . $label . '</label>';
                        if ( 'yes' == $nolabel ) {
                            $labelTag = '';
                        }
                        $js = '';
                        //    $ret = '<div class="pacmec-wallet-account-shortcode" data-label="'.$labelTag.'" data-account="'.$accountAddress.'">';
                        $ret = '<div class="twbs"><div class="container-fluid pacmec-wallet-account-shortcode">
    <div class="row pacmec-wallet-account-account-wrapper pacmec-wallet-account-wrapper">
        <div class="col-12">
            <div class="form-group">
                ' . $labelTag . '
                <div class="input-group" style="margin-top: 8px">
                    <input type="text"
                           value="' . $accountAddress . '"
                           readonly
                           id="pacmec-wallet-account' . $counter . '"
                           class="form-control">
                    <span class="input-group-append">
                        <div class="btn-group" role="group">
                            <button class="button btn btn-default btn-left d-md-inline pacmec-wallet-qr-button"
                                    type="button"
                                    data-toggle="collapse"
                                    href="#pacmec-wallet-account-qr' . $counter . '"
                                    role="button"
                                    aria-expanded="false"
                                    aria-controls="pacmec-wallet-account-qr' . $counter . '"
                                    title="' . __( 'QR', 'pacmec-wallet' ) . '">
                                <i class="fa fa-qrcode" aria-hidden="true"></i>
                            </button>
                            <button class="button btn btn-default btn-right pacmec-wallet-copy-button" type="button"
                                    data-clipboard-target="#pacmec-wallet-account' . $counter . '"
                                    data-clipboard-action="copy"
                                    id="pacmec-wallet-account-copy-qr-button' . $counter . '"
                                    title="' . __( 'Copy', 'pacmec-wallet' ) . '">
                                <i class="fa fa-clipboard" aria-hidden="true"></i>
                            </button>
                        </div>
                    </span>
                </div>
                <div class="collapse pacmec-wallet-account-qr"
                        id="pacmec-wallet-account-qr' . $counter . '">
                    <div class="d-md-block mx-auto col-md-4 float-none">
                        <div class="pacmec-wallet-account-canvas-qr' . $counter . '"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div></div>';
                        PACMEC_WALLET_enqueue_scripts_();
                        //    wp_enqueue_script( 'jquery.qrcode' );
                        //    wp_enqueue_script( 'clipboard' );
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'pacmec-wallet-account', 'PACMEC_WALLET_account_shortcode' );
                    function PACMEC_WALLET_account_management_export_shortcode( $attrs )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        static  $counter = 0 ;
                        ++$counter;
                        $privateKey = get_user_meta( $user_id, 'user_ethereum_wallet_key', true );
                        
                        if ( empty($privateKey) ) {
                            PACMEC_WALLET_user_registration( $user_id );
                            $privateKey = get_user_meta( $user_id, 'user_ethereum_wallet_key', true );
                        }
                        
                        $attributes = shortcode_atts( array(
                            'label'   => '',
                            'nolabel' => '',
                        ), $attrs, 'pacmec-wallet-account-management-export' );
                        $label = ( !empty($attributes['label']) ? esc_attr( $attributes['label'] ) : __( 'Private key', 'pacmec-wallet' ) );
                        $nolabel = ( !empty($attributes['nolabel']) ? esc_attr( $attributes['nolabel'] ) : '' );
                        $labelTag = '<label class="control-label" for="pacmec-wallet-account-management-export">' . $label . '</label>';
                        if ( 'yes' == $nolabel ) {
                            $labelTag = '';
                        }
                        $js = '';
                        $ret = '<div class="twbs"><div class="container-fluid pacmec-wallet-account-management-export-shortcode">
    <div class="row pacmec-wallet-account-management-export-wrapper">
        <div class="col-12">
            <div class="form-group">
                ' . $labelTag . '
                <div class="input-group" style="margin-top: 8px">
                    <input type="text"
                           value="' . $privateKey . '"
                           readonly
                           id="pacmec-wallet-account-management-export' . $counter . '"
                           class="form-control">
                    <span class="input-group-append">
                        <div class="btn-group" role="group">
                            <button class="button btn btn-default btn-left d-md-inline pacmec-wallet-qr-button" type="button"
                                    data-toggle="collapse"
                                    href="#pacmec-wallet-account-management-export-qr' . $counter . '"
                                    role="button" aria-expanded="false"
                                    aria-controls="pacmec-wallet-account-management-export-qr' . $counter . '"
                                    title="' . __( 'QR', 'pacmec-wallet' ) . '">
                                <i class="fa fa-qrcode" aria-hidden="true"></i>
                            </button>
                            <button class="button btn btn-default btn-right pacmec-wallet-copy-button" type="button"
                                    data-clipboard-target="#pacmec-wallet-account-management-export' . $counter . '"
                                    data-clipboard-action="copy"
                                    title="' . __( 'Copy', 'pacmec-wallet' ) . '"
                                    id="pacmec-wallet-account-management-export-copy-qr-button' . $counter . '">
                                <i class="fa fa-clipboard" aria-hidden="true"></i>
                            </button>
                        </div>
                    </span>
                </div>
                <div class="collapse pacmec-wallet-account-management-export-qr"
                        id="pacmec-wallet-account-management-export-qr' . $counter . '">
                    <div class="d-md-block mx-auto col-md-4 float-none">
                        <div class="pacmec-wallet-account-management-export-canvas-qr' . $counter . '"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div></div>';
                        PACMEC_WALLET_enqueue_scripts_();
                        //    wp_enqueue_script( 'jquery.qrcode' );
                        //    wp_enqueue_script( 'clipboard' );
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'pacmec-wallet-account-management-export', 'PACMEC_WALLET_account_management_export_shortcode' );
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    function PACMEC_WALLET_sendform_shortcode( $attributes )
                    {
                        //    global $PACMEC_WALLET_options;
                        //	$options = stripslashes_deep( $PACMEC_WALLET_options );
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $ops = '';
                        // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                        $lastTxHash = get_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', true );
                        $lastTxTime = get_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', true );
                        
                        if ( !empty($lastTxHash) && 0 != PACMEC_WALLET_is_tx_confirmed( $lastTxHash ) ) {
                            delete_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', $lastTxHash );
                            delete_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', $lastTxTime );
                            $lastTxHash = '';
                            $lastTxTime = '';
                        }
                        
                        
                        if ( !empty($lastTxHash) ) {
                            $lastTxMsg = sprintf(
                                __( 'Last transaction %1$s%2$s%3$s%4$s%5$s is still in progress. Wait please.', 'pacmec-wallet' ),
                                '<a target="_blank" href="',
                                PACMEC_WALLET_get_txhash_path( $lastTxHash ),
                                '">',
                                substr( $lastTxHash, 0, 8 ),
                                '</a>'
                            );
                        } else {
                            $lastTxMsg = sprintf(
                                __( 'Last transaction %1$s%2$s%3$s%4$s%5$s is still in progress. Wait please.', 'pacmec-wallet' ),
                                '',
                                '',
                                '',
                                '',
                                ''
                            );
                        }
                        
                        $js = '';
                        $ret = '<form method="post" action="" name="pacmec-wallet-send-form"><div class="twbs"><div class="container-fluid pacmec-wallet-sendform-shortcode">
    <div class="row pacmec-wallet-sendform-content">
        <div class="col-12">
            <div id="pacmec-wallet-error-box" class="form-group hidden" hidden>
                <div class="alert alert-error" role="alert">
                    <!--http://jsfiddle.net/0vzmmn0v/1/-->
                    <div class="fa fa-exclamation-triangle" aria-hidden="true"></div>
                    <div class="pacmec-wallet-error-message"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row pacmec-wallet-sendform-content">
        <div class="col-12">
            <div id="pacmec-wallet-tx-in-progress-alert" class="form-group hidden" hidden>
                <div class="alert alert-warning" role="alert">
                    <!--http://jsfiddle.net/0vzmmn0v/1/-->
                    <div class="fa fa-exclamation-triangle" aria-hidden="true"></div>
                    <div>' . $lastTxMsg . '</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row pacmec-wallet-sendform-content">
        <div class="col-12">
            <div class="form-group">
                <label class="control-label" for="pacmec-wallet-sendform-to">' . __( 'To', 'pacmec-wallet' ) . '</label>
                <div class="input-group" style="margin-top: 8px">
                    <input type="text"
                           value=""
                           placeholder="' . __( 'Input the recipient ethereum address', 'pacmec-wallet' ) . '"
                           id="pacmec-wallet-sendform-to"
                           name="pacmec-wallet-sendform-to"
                           class="form-control">
                    <span class="input-group-append">
                        <div class="btn-group" role="group">
                            <button class="button btn btn-default btn-left d-md-inline pacmec-wallet-qr-scan-button" type="button"
                                    data-toggle="collapse"
                                    href="#pacmec-wallet-to-qr1"
                                    role="button" aria-expanded="false"
                                    aria-controls="pacmec-wallet-to-qr1"
                                    title="' . __( 'QR', 'pacmec-wallet' ) . '">
                                <i class="fa fa-qrcode" aria-hidden="true"></i>
                            </button>
                        </div>
                    </span>
                </div>
                <div class="collapse" id="pacmec-wallet-to-qr1">
                    <div class="d-md-block mx-auto col-md-4 float-none">
                        <div class="d-none" id="pacmec-wallet-to-canvas-qr1-loading-message">🎥' . __( 'Unable to access video stream (please make sure you have a webcam enabled)', 'pacmec-wallet' ) . '</div>
                        <canvas id="pacmec-wallet-to-canvas-qr1" width="256px" height="256px" hidden></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row pacmec-wallet-sendform-content">
        <div class="col-12">
            <div class="form-group">
                <label class="control-label" for="pacmec-wallet-sendform-amount">' . __( 'Amount', 'pacmec-wallet' ) . '</label>
                <div class="input-group" style="margin-top: 8px">
                    <input style="cursor: text;"
                        type="number"
                        value=""
                        min="0"
                        step="0.000000000000000001"
                        id="pacmec-wallet-sendform-amount"
                        name="pacmec-wallet-sendform-amount"
                        class="form-control"
						lang="en-US">
                    <select style="max-width: 80px;"
                        class="custom-select form-control"
                        id="pacmec-wallet-sendform-currency"
                        name="pacmec-wallet-sendform-currency" >
                        <option value="0x0000000000000000000000000000000000000001" selected="">' . __( 'CTN', 'pacmec-wallet' ) . '</option>
                        ' . $ops . '
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row pacmec-wallet-sendform-content">
        <div class="col-12">
            <div class="form-group">
                ' . wp_nonce_field(
                            'pacmec-wallet-send_form',
                            'pacmec-wallet-send-form-nonce',
                            true,
                            false
                        ) . '
                <input type="hidden" name="action" value="ethereum_wallet_send" />
                <button
                    id="pacmec-wallet-send-button"
                    name="pacmec-wallet-send-button"
                    type="submit"
                    value="' . __( 'Send', 'pacmec-wallet' ) . '"
                    class="button btn btn-default float-right col-12 col-md-4">' . __( 'Send', 'pacmec-wallet' ) . '</button>
                <div id="pacmec-wallet-tx-in-progress-spinner" class="spinner float-right"></div>
            </div>
        </div>
    </div>
</div></div></form>';
                        PACMEC_WALLET_enqueue_scripts_();
                        //    wp_enqueue_script( 'jsQR' );
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'pacmec-wallet-sendform', 'PACMEC_WALLET_sendform_shortcode' );
                    function PACMEC_WALLET_sendform_action()
                    {
                        global  $wp ;
                        global  $PACMEC_WALLET_erc1404ContractABI ;
                        global  $PACMEC_WALLET_options ;
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
                        
                        if ( isset( $_REQUEST['pacmec-wallet-send-form-nonce'] ) ) {
                            $nonce_value = $_REQUEST['pacmec-wallet-send-form-nonce'];
                        } else {
                            if ( isset( $_REQUEST['_wpnonce'] ) ) {
                                $nonce_value = $_REQUEST['_wpnonce'];
                            }
                        }
                        
                        
                        if ( !wp_verify_nonce( $nonce_value, 'pacmec-wallet-send_form' ) ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_sendform_action: bad nonce detected: " . $nonce_value );
                            return;
                        }
                        
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $lastTxHash = get_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', true );
                        
                        if ( !empty($lastTxHash) && 0 == PACMEC_WALLET_is_tx_confirmed( $lastTxHash ) ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_sendform_action: tx already in progress: " . $lastTxHash );
                            return;
                        }
                        
                        $lastTxTime = get_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', true );
                        delete_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', $lastTxHash );
                        delete_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', $lastTxTime );
                        $accountAddress = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                        $privateKey = get_user_meta( $user_id, 'user_ethereum_wallet_key', true );
                        // To address
                        
                        if ( !isset( $_REQUEST['pacmec-wallet-sendform-to'] ) ) {
                            PACMEC_WALLET_log( "pacmec-wallet-sendform-to not set" );
                            return;
                        }
                        
                        $to = sanitize_text_field( $_REQUEST['pacmec-wallet-sendform-to'] );
                        
                        if ( empty($to) ) {
                            PACMEC_WALLET_log( "empty pacmec-wallet-sendform-to" );
                            return;
                        }
                        
                        
                        if ( 42 != strlen( $to ) ) {
                            PACMEC_WALLET_log( "strlen pacmec-wallet-sendform-to != 42: " . $to );
                            return;
                        }
                        
                        
                        if ( '0x' != substr( $to, 0, 2 ) ) {
                            PACMEC_WALLET_log( "startsWith pacmec-wallet-sendform-to != 0x: " . $to );
                            return;
                        }
                        
                        // Amount
                        
                        if ( !isset( $_REQUEST['pacmec-wallet-sendform-amount'] ) ) {
                            PACMEC_WALLET_log( "pacmec-wallet-sendform-amount not set" );
                            return;
                        }
                        
                        $amount = sanitize_text_field( $_REQUEST['pacmec-wallet-sendform-amount'] );
                        
                        if ( empty($amount) ) {
                            PACMEC_WALLET_log( "empty pacmec-wallet-sendform-amount" );
                            return;
                        }
                        
                        
                        if ( !is_numeric( $amount ) ) {
                            PACMEC_WALLET_log( "non-numeric pacmec-wallet-sendform-amount: " . $amount );
                            return;
                        }
                        
                        // Currency address
                        
                        if ( !isset( $_REQUEST['pacmec-wallet-sendform-currency'] ) ) {
                            PACMEC_WALLET_log( "pacmec-wallet-sendform-currency not set" );
                            return;
                        }
                        
                        $currency = sanitize_text_field( $_REQUEST['pacmec-wallet-sendform-currency'] );
                        
                        if ( empty($currency) ) {
                            PACMEC_WALLET_log( "empty pacmec-wallet-sendform-currency" );
                            return;
                        }
                        
                        
                        if ( 42 != strlen( $currency ) ) {
                            PACMEC_WALLET_log( "strlen pacmec-wallet-sendform-currency != 42: " . $to );
                            return;
                        }
                        
                        
                        if ( '0x' != substr( $currency, 0, 2 ) ) {
                            PACMEC_WALLET_log( "startsWith pacmec-wallet-sendform-currency != 0x: " . $to );
                            return;
                        }
                        
                        $error = null;
                        $txhash = false;
                        
                        if ( "0x0000000000000000000000000000000000000001" === $currency ) {
                            // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                            if ( is_null( $error ) && false === $txhash ) {
                                // check for not processed with premium code
                                list( $error, $txhash ) = PACMEC_WALLET_send_ether(
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
            window.ethereumWallet.PACMEC_WALLET_update_error_message();
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
            window.ethereumWallet.PACMEC_WALLET_update_error_message();
        }
        ethereumWalletErrorMessageTimeoutId2 = setTimeout(ethereumWalletErrorMessageTimeoutFunc2, 100);
    </script>
<?php 
                        }
                    
                    }
                    
                    add_action( 'wp_loaded', "PACMEC_WALLET_sendform_action", 20 );
                    function PACMEC_WALLET_account_management_create_shortcode( $attributes )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $js = '';
                        $ret = '<form method="post" action="" onsubmit="return window.ethereumWallet.validate_account_management_create_form()"><div class="twbs"><div class="container-fluid pacmec-wallet-account-management-create-shortcode">
    <div class="row pacmec-wallet-account-management-create-content">
        <div class="col-12">
            <div class="form-group">
                <label class="control-label" for="pacmec-wallet-account-management-create-name">' . __( 'Account name', 'pacmec-wallet' ) . '</label>
                <div class="input-group" style="margin-top: 8px">
                    <input type="text"
                           value=""
                           placeholder="' . __( 'Input new account name', 'pacmec-wallet' ) . '"
                           id="pacmec-wallet-account-management-create-name"
                           name="pacmec-wallet-account-management-create-name"
                           class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="row pacmec-wallet-account-management-create-content">
        <div class="col-12">
            <div class="form-group">
                ' . wp_nonce_field(
                            'pacmec-wallet-account-management-create-send_form',
                            'pacmec-wallet-account-management-create-send-form-nonce',
                            true,
                            false
                        ) . '
                <input type="hidden" name="action" value="ethereum_wallet_account_management_create_send" />
                <button
                    id="pacmec-wallet-account-management-create-send-button"
                    name="pacmec-wallet-account-management-create-send-button"
                    type="submit"
                    value="' . __( 'Create', 'pacmec-wallet' ) . '"
                    class="button btn btn-default float-right col-12 col-md-4">' . __( 'Create', 'pacmec-wallet' ) . '</button>
            </div>
        </div>
    </div>
</div></div></form>';
                        PACMEC_WALLET_enqueue_scripts_();
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'pacmec-wallet-account-management-create', 'PACMEC_WALLET_account_management_create_shortcode' );
                    function PACMEC_WALLET_account_management_create_action()
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
                        
                        if ( isset( $_REQUEST['pacmec-wallet-account-management-create-send-form-nonce'] ) ) {
                            $nonce_value = $_REQUEST['pacmec-wallet-account-management-create-send-form-nonce'];
                        } else {
                            if ( isset( $_REQUEST['_wpnonce'] ) ) {
                                $nonce_value = $_REQUEST['_wpnonce'];
                            }
                        }
                        
                        
                        if ( !wp_verify_nonce( $nonce_value, 'pacmec-wallet-account-management-create-send_form' ) ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_account_management_create_action: bad nonce detected: " . $nonce_value );
                            return;
                        }
                        
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        
                        if ( isset( $_REQUEST['pacmec-wallet-account-management-create-name'] ) ) {
                            $name = sanitize_text_field( $_REQUEST['pacmec-wallet-account-management-create-name'] );
                            
                            if ( __( 'Default account', 'pacmec-wallet' ) == $name ) {
                                PACMEC_WALLET_log( "pacmec-wallet-account-management-create-name an attempt to replace the default account is blocked: " . $name );
                            } else {
                                
                                if ( !empty($name) ) {
                                    // create new account request
                                    PACMEC_WALLET_log( "pacmec-wallet-account-management-create-name is set: " . $name );
                                    $accountsJSON = get_user_meta( $user_id, 'user_ethereum_wallet_accounts', true );
                                    if ( empty($accountsJSON) ) {
                                        $accountsJSON = '[]';
                                    }
                                    $accounts = json_decode( $accountsJSON, true );
                                    list( $ethAddressChkSum, $privateKeyHex ) = PACMEC_WALLET_create_account();
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
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
<?php 
                    }
                    
                    add_action( 'wp_loaded', "PACMEC_WALLET_account_management_create_action", 20 );
                    function PACMEC_WALLET_account_management_import_shortcode( $attributes )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $js = '';
                        $ret = '<form method="post" action="" onsubmit="return window.ethereumWallet.validate_account_management_import_form()"><div class="twbs"><div class="container-fluid pacmec-wallet-account-management-import-shortcode">
    <div class="row pacmec-wallet-account-management-import-content">
        <div class="col-12">
            <div class="form-group">
                <label class="control-label" for="pacmec-wallet-account-management-import-name">' . __( 'Account name', 'pacmec-wallet' ) . '</label>
                <div class="input-group" style="margin-top: 8px">
                    <input type="text"
                        value=""
                        placeholder="' . __( 'Input new account name', 'pacmec-wallet' ) . '"
                        id="pacmec-wallet-account-management-import-name"
                        name="pacmec-wallet-account-management-import-name"
                        class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="pacmec-wallet-account-management-import-key">' . __( 'Private key', 'pacmec-wallet' ) . '</label>
                <div class="input-group" style="margin-top: 8px">
                    <input type="text"
                        autocomplete="off"
                        value=""
                        placeholder="' . __( 'Input your private key here', 'pacmec-wallet' ) . '"
                        id="pacmec-wallet-account-management-import-key"
                        name="pacmec-wallet-account-management-import-key"
                        class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="row pacmec-wallet-account-management-import-content">
        <div class="col-12">
            <div class="form-group">
                ' . wp_nonce_field(
                            'pacmec-wallet-account-management-import-send_form',
                            'pacmec-wallet-account-management-import-send-form-nonce',
                            true,
                            false
                        ) . '
                <input type="hidden" name="action" value="ethereum_wallet_account_management_import_send" />
                <button
                    id="pacmec-wallet-account-management-import-send-button"
                    name="pacmec-wallet-account-management-import-send-button"
                    type="submit"
                    value="' . __( 'Import', 'pacmec-wallet' ) . '"
                    class="button btn btn-default float-right col-12 col-md-4">' . __( 'Import', 'pacmec-wallet' ) . '</button>
            </div>
        </div>
    </div>
</div></div></form>';
                        PACMEC_WALLET_enqueue_scripts_();
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'pacmec-wallet-account-management-import', 'PACMEC_WALLET_account_management_import_shortcode' );
                    function PACMEC_WALLET_account_management_import_action()
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
                        
                        if ( isset( $_REQUEST['pacmec-wallet-account-management-import-send-form-nonce'] ) ) {
                            $nonce_value = $_REQUEST['pacmec-wallet-account-management-import-send-form-nonce'];
                        } else {
                            if ( isset( $_REQUEST['_wpnonce'] ) ) {
                                $nonce_value = $_REQUEST['_wpnonce'];
                            }
                        }
                        
                        
                        if ( !wp_verify_nonce( $nonce_value, 'pacmec-wallet-account-management-import-send_form' ) ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_account_management_import_action: bad nonce detected: " . $nonce_value );
                            return;
                        }
                        
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        
                        if ( isset( $_REQUEST['pacmec-wallet-account-management-import-name'] ) && isset( $_REQUEST['pacmec-wallet-account-management-import-key'] ) ) {
                            $name = sanitize_text_field( $_REQUEST['pacmec-wallet-account-management-import-name'] );
                            $privateKeyHex = sanitize_text_field( $_REQUEST['pacmec-wallet-account-management-import-key'] );
                            
                            if ( __( 'Default account', 'pacmec-wallet' ) == $name ) {
                                PACMEC_WALLET_log( "pacmec-wallet-account-management-import-name an attempt to replace the default account is blocked: " . $name );
                            } else {
                                
                                if ( !empty($name) && !empty($privateKeyHex) ) {
                                    // import new account request
                                    PACMEC_WALLET_log( "pacmec-wallet-account-management-import-name is set: " . $name );
                                    $blnIsValid = false;
                                    try {
                                        $ecAdapter = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter();
                                        $privateKeyBuffer = Buffer::hex( $privateKeyHex );
                                        $blnIsValid = $ecAdapter->validatePrivateKey( $privateKeyBuffer );
                                    } catch ( \Exception $ex ) {
                                        PACMEC_WALLET_log( "PACMEC_WALLET_account_management_import_action: " . $ex->getMessage() );
                                        PACMEC_WALLET_log( "PACMEC_WALLET_account_management_import_action: " . $ex->getTraceAsString() );
                                    }
                                    
                                    if ( !$blnIsValid ) {
                                        PACMEC_WALLET_log( "PACMEC_WALLET_account_management_import_action: invalid private key" );
                                    } else {
                                        $accountsJSON = get_user_meta( $user_id, 'user_ethereum_wallet_accounts', true );
                                        if ( empty($accountsJSON) ) {
                                            $accountsJSON = '[]';
                                        }
                                        $accounts = json_decode( $accountsJSON, true );
                                        $ethAddressChkSum = null;
                                        try {
                                            $ethAddressChkSum = PACMEC_WALLET_address_from_key( $privateKeyHex );
                                        } catch ( \Exception $ex ) {
                                            PACMEC_WALLET_log( "PACMEC_WALLET_account_management_import_action: " . $ex->getMessage() );
                                            PACMEC_WALLET_log( "PACMEC_WALLET_account_management_import_action: " . $ex->getTraceAsString() );
                                        }
                                        
                                        if ( !is_null( $ethAddressChkSum ) ) {
                                            $blnFound = false;
                                            foreach ( $accounts as $account ) {
                                                
                                                if ( $ethAddressChkSum == $account["address"] ) {
                                                    $blnFound = true;
                                                    break;
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
                    
                    add_action( 'wp_loaded', "PACMEC_WALLET_account_management_import_action", 20 );
                    function PACMEC_WALLET_account_management_select_shortcode( $attributes )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $ops = '';
                        $accountsJSON = get_user_meta( $user_id, 'user_ethereum_wallet_accounts', true );
                        if ( empty($accountsJSON) ) {
                            $accountsJSON = '[]';
                        }
                        $accounts = json_decode( $accountsJSON, true );
                        
                        if ( !$accounts ) {
                            $name = __( 'Default account', 'pacmec-wallet' );
                            $address = PACMEC_WALLET_get_wallet_address();
                            $privateKey = get_user_meta( $user_id, 'user_ethereum_wallet_key', true );
                            $accounts = [ [
                                "name"    => $name,
                                "address" => $address,
                                "key"     => $privateKey,
                            ] ];
                            // @see https://stackoverflow.com/a/44263857/4256005
                            update_user_meta( $user_id, 'user_ethereum_wallet_accounts', json_encode( $accounts, JSON_UNESCAPED_UNICODE ) );
                        }
                        
                        $defaultAddress = PACMEC_WALLET_get_wallet_address();
                        foreach ( $accounts as $account ) {
                            $selected = '';
                            if ( $defaultAddress == $account["address"] ) {
                                $selected = ' selected';
                            }
                            $op = '<option value="' . $account["address"] . '"' . $selected . '>' . $account["name"] . ' - ' . $account["address"] . '</option>';
                            $ops .= $op;
                        }
                        $js = '';
                        $ret = '<form method="post" action="" onsubmit="return window.ethereumWallet.validate_account_management_select_form()"><div class="twbs"><div class="container-fluid pacmec-wallet-account-management-select-shortcode">
    <div class="row pacmec-wallet-account-management-select-content">
        <div class="col-12">
            <div class="form-group">
                <label class="control-label" for="pacmec-wallet-account-management-select-default">' . __( 'Default account', 'pacmec-wallet' ) . '</label>
                <div class="input-group" style="margin-top: 8px">
                    <select
                        class="custom-select form-control"
                        id="pacmec-wallet-account-management-select-default"
                        name="pacmec-wallet-account-management-select-default" >
                        ' . $ops . '
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row pacmec-wallet-account-management-select-content">
        <div class="col-12">
            <div class="form-group">
                ' . wp_nonce_field(
                            'pacmec-wallet-account-management-select-send_form',
                            'pacmec-wallet-account-management-select-send-form-nonce',
                            true,
                            false
                        ) . '
                <input type="hidden" name="action" value="ethereum_wallet_account_management_select_send" />
                <button
                    id="pacmec-wallet-account-management-select-send-button"
                    name="pacmec-wallet-account-management-select-send-button"
                    type="submit"
                    value="' . __( 'Select', 'pacmec-wallet' ) . '"
                    class="button btn btn-default float-right col-12 col-md-4">' . __( 'Select', 'pacmec-wallet' ) . '</button>
                <button
                    id="pacmec-wallet-account-management-delete-send-button"
                    name="pacmec-wallet-account-management-delete-send-button"
                    type="submit"
                    value="' . __( 'Remove', 'pacmec-wallet' ) . '"
                    class="button btn btn-default float-right col-12 col-md-4">' . __( 'Remove', 'pacmec-wallet' ) . '</button>
            </div>
        </div>
    </div>
</div></div></form>';
                        PACMEC_WALLET_enqueue_scripts_();
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'pacmec-wallet-account-management-select', 'PACMEC_WALLET_account_management_select_shortcode' );
                    function PACMEC_WALLET_account_management_select_action()
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
                        
                        if ( isset( $_REQUEST['pacmec-wallet-account-management-select-send-form-nonce'] ) ) {
                            $nonce_value = $_REQUEST['pacmec-wallet-account-management-select-send-form-nonce'];
                        } else {
                            if ( isset( $_REQUEST['_wpnonce'] ) ) {
                                $nonce_value = $_REQUEST['_wpnonce'];
                            }
                        }
                        
                        
                        if ( !wp_verify_nonce( $nonce_value, 'pacmec-wallet-account-management-select-send_form' ) ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_account_management_select_action: bad nonce detected: " . $nonce_value );
                            return;
                        }
                        
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        
                        if ( isset( $_REQUEST['pacmec-wallet-account-management-select-send-button'] ) ) {
                            do {
                                if ( !isset( $_REQUEST['pacmec-wallet-account-management-select-default'] ) ) {
                                    break;
                                }
                                $newDefaultAddress = sanitize_text_field( $_REQUEST['pacmec-wallet-account-management-select-default'] );
                                
                                if ( empty($newDefaultAddress) ) {
                                    PACMEC_WALLET_log( "empty pacmec-wallet-account-management-select-default" );
                                    break;
                                }
                                
                                
                                if ( 42 != strlen( $newDefaultAddress ) ) {
                                    PACMEC_WALLET_log( "strlen pacmec-wallet-account-management-select-default != 42: " . $to );
                                    break;
                                }
                                
                                
                                if ( '0x' != substr( $newDefaultAddress, 0, 2 ) ) {
                                    PACMEC_WALLET_log( "startsWith pacmec-wallet-account-management-select-default != 0x: " . $to );
                                    break;
                                }
                                
                                $defaultAddress = PACMEC_WALLET_get_wallet_address();
                                
                                if ( $newDefaultAddress == $defaultAddress ) {
                                    PACMEC_WALLET_log( "PACMEC_WALLET_account_management_select_action: no difference with current default set: " . $defaultAddress );
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
                                foreach ( $accounts as $account ) {
                                    
                                    if ( $account["address"] == $newDefaultAddress ) {
                                        // set default account
                                        PACMEC_WALLET_log( "PACMEC_WALLET_account_management_select_action: new default address set: " . $newDefaultAddress );
                                        update_user_meta( $user_id, 'user_ethereum_wallet_address', $newDefaultAddress );
                                        update_user_meta( $user_id, 'user_ethereum_wallet_key', $account["key"] );
                                    }
                                
                                }
                            } while (false);
                        } else {
                            if ( isset( $_REQUEST['pacmec-wallet-account-management-delete-send-button'] ) ) {
                                do {
                                    if ( !isset( $_REQUEST['pacmec-wallet-account-management-select-default'] ) ) {
                                        break;
                                    }
                                    $deleteAddress = sanitize_text_field( $_REQUEST['pacmec-wallet-account-management-select-default'] );
                                    PACMEC_WALLET_log( "PACMEC_WALLET_account_management_select_action: try to delete: " . $deleteAddress );
                                    
                                    if ( empty($deleteAddress) ) {
                                        PACMEC_WALLET_log( "empty pacmec-wallet-account-management-select-default" );
                                        break;
                                    }
                                    
                                    
                                    if ( 42 != strlen( $deleteAddress ) ) {
                                        PACMEC_WALLET_log( "strlen pacmec-wallet-account-management-select-default != 42: " . $to );
                                        break;
                                    }
                                    
                                    
                                    if ( '0x' != substr( $deleteAddress, 0, 2 ) ) {
                                        PACMEC_WALLET_log( "startsWith pacmec-wallet-account-management-select-default != 0x: " . $to );
                                        break;
                                    }
                                    
                                    $defaultAddress = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                                    
                                    if ( $deleteAddress == $defaultAddress ) {
                                        PACMEC_WALLET_log( "PACMEC_WALLET_account_management_select_action: can not delete current default address: " . $defaultAddress );
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
                                    foreach ( $accounts as $account ) {
                                        
                                        if ( $account["address"] == $deleteAddress && isset( $account["imported"] ) ) {
                                            PACMEC_WALLET_log( "PACMEC_WALLET_account_management_select_action: deleted address: " . $deleteAddress );
                                            continue;
                                        }
                                        
                                        $newAccounts[] = $account;
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
                    
                    add_action( 'wp_loaded', "PACMEC_WALLET_account_management_select_action", 20 );
                    // TODO: wait for a configured number of blocks
                    /**
                     *
                     * @param type $txhash
                     * @return Integer confirmed: 1, unconfirmed: 0, failed: -1
                     */
                    function PACMEC_WALLET_is_tx_confirmed( $txhash )
                    {
                        $is_confirmed = false;
                        $is_failed = false;
                        $is_mined = false;
                        $txBlockNumber = '';
                        $savedTxInfo = null;
                        try {
                            $providerUrl = PACMEC_WALLET_getWeb3Endpoint();
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
                                    PACMEC_WALLET_log( "Failed to getTransactionByHash: " . $err );
                                    $is_failed = true;
                                    return;
                                }
                                
                                
                                if ( is_null( $transaction ) ) {
                                    PACMEC_WALLET_log( "Failed to getTransactionByHash: transaction returned is null" );
                                    $is_failed = true;
                                    return;
                                }
                                
                                
                                if ( !is_object( $transaction ) ) {
                                    PACMEC_WALLET_log( "Failed to getTransactionByHash: transaction returned is not an object: " . $transaction );
                                    $is_failed = true;
                                    return;
                                }
                                
                                PACMEC_WALLET_log( "transaction: " . print_r( $transaction, true ) );
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
                                        PACMEC_WALLET_log( "Failed to getTransactionReceipt: " . $err );
                                        return;
                                    }
                                    
                                    
                                    if ( is_null( $receipt ) ) {
                                        PACMEC_WALLET_log( "Failed to getTransactionReceipt: receipt returned is null" );
                                        return;
                                    }
                                    
                                    
                                    if ( !is_object( $receipt ) ) {
                                        PACMEC_WALLET_log( "Failed to getTransactionReceipt: receipt returned is not an object: " . $receipt );
                                        return;
                                    }
                                    
                                    PACMEC_WALLET_log( "transaction receipt: " . print_r( $receipt, true ) );
                                    
                                    if ( isset( $receipt->status ) ) {
                                        
                                        if ( $receipt->status === "0x1" ) {
                                            $is_confirmed = true;
                                        } else {
                                            
                                            if ( $receipt->status === "0x0" ) {
                                                
                                                if ( $receipt->gasUsed > $savedTxInfo->gas ) {
                                                    $is_failed = true;
                                                    PACMEC_WALLET_log( "getTransactionReceipt({$txhash}): we ran out of gas, not confirmed!" );
                                                } else {
                                                    $is_failed = true;
                                                    PACMEC_WALLET_log( "getTransactionReceipt({$txhash}): bad tx status, not confirmed!" );
                                                }
                                            
                                            } else {
                                                // unknown status. pre-Byzantium
                                                
                                                if ( $receipt->gasUsed >= $savedTxInfo->gas ) {
                                                    $is_failed = true;
                                                    PACMEC_WALLET_log( "getTransactionReceipt({$txhash}): we ran out of gas, not confirmed!" );
                                                } else {
                                                    $is_confirmed = true;
                                                }
                                            
                                            }
                                        
                                        }
                                    
                                    } else {
                                        // unknown status. pre-Byzantium
                                        
                                        if ( $receipt->gasUsed >= $savedTxInfo->gas ) {
                                            $is_failed = true;
                                            PACMEC_WALLET_log( "getTransactionReceipt({$txhash}): we ran out of gas, not confirmed!" );
                                        } else {
                                            $is_confirmed = true;
                                        }
                                    
                                    }
                                
                                } );
                                
                                if ( $is_confirmed ) {
                                    $blockNumber = null;
                                    $eth->blockNumber( function ( $err, $lastBlockNumber ) use( &$blockNumber ) {
                                        
                                        if ( $err !== null ) {
                                            PACMEC_WALLET_log( "Failed to get blockNumber: " . $err );
                                            return;
                                        }
                                        
                                        PACMEC_WALLET_log( "lastBlockNumber: " . $lastBlockNumber->toString() );
                                        $blockNumber = intval( $lastBlockNumber->toString() );
                                    } );
                                    
                                    if ( null !== $blockNumber && '' !== $txBlockNumber ) {
                                        // https://www.reddit.com/r/ethereum/comments/4eplsv/how_many_confirms_is_considered_safe_in_ethereum/d229xie/
                                        //                $safeBlockCount = 12; // TODO: add admin setting
                                        $safeBlockCount = 1;
                                        // TODO: add admin setting
                                        $is_confirmed = $is_confirmed && $blockNumber - $txBlockNumber >= $safeBlockCount;
                                    }
                                    
                                    PACMEC_WALLET_log( "is_confirmed({$txhash} in block {$txBlockNumber}): " . $is_confirmed );
                                }
                            
                            }
                        
                        } catch ( \Exception $ex ) {
                            PACMEC_WALLET_log( $ex->getMessage() );
                        }
                        return ( $is_confirmed ? 1 : (( $is_failed ? -1 : 0 )) );
                    }
                    
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    function PACMEC_WALLET_history_shortcode( $attributes )
                    {
                        $user_id = get_current_user_id();
                        if ( $user_id <= 0 ) {
                            return;
                        }
                        $attributes = shortcode_atts( array(
                            'direction' => '',
                        ), $attributes, 'pacmec-wallet' );
                        // The displayed tx direction: in/out/inout
                        $direction = ( !empty($attributes['direction']) ? $attributes['direction'] : 'inout' );
                        if ( !in_array( $direction, array( 'in', 'out', 'inout' ) ) ) {
                            $direction = 'inout';
                        }
                        $js = '';
                        $ret = '<div class="twbs"><div class="container-fluid pacmec-wallet-history-shortcode">
    <div class="row pacmec-wallet-history-table-wrapper">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped table-condensed pacmec-wallet-history-table pacmec-wallet-history-table-direction-' . $direction . '">
                    <thead>
                        <tr>
                            <th>' . __( '#', 'pacmec-wallet' ) . '</th>
                            <th>' . __( 'Amount', 'pacmec-wallet' ) . '</th>
                            <th>' . __( 'Date', 'pacmec-wallet' ) . '</th>
                            <th>' . __( 'Tx', 'pacmec-wallet' ) . '</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div></div>';
                        PACMEC_WALLET_enqueue_scripts_();
                        wp_enqueue_script( 'data-tables' );
                        return $js . str_replace( "\n", " ", str_replace( "\r", " ", str_replace( "\t", " ", $js . $ret ) ) );
                    }
                    
                    add_shortcode( 'pacmec-wallet-history', 'PACMEC_WALLET_history_shortcode' );
                    function PACMEC_WALLET_enqueue_scripts_()
                    {
                        wp_enqueue_style( 'pacmec-wallet' );
                        wp_enqueue_script( 'pacmec-wallet-main' );
                        //    if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                        //        if ( ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
                        //
                        //            wp_enqueue_script( 'pacmec-wallet-premium' );
                        //
                        //        }
                        //    }
                    }
                    
                    function PACMEC_WALLET_stylesheet()
                    {
                        global  $PACMEC_WALLET_plugin_url_path ;
                        $deps = array(
                            'font-awesome',
                            'bootstrap-pacmec-wallet',
                            'bootstrap-affix-pacmec-wallet',
                            'data-tables'
                        );
                        $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
                        
                        if ( !wp_style_is( 'font-awesome', 'queue' ) && !wp_style_is( 'font-awesome', 'done' ) ) {
                            wp_dequeue_style( 'font-awesome' );
                            wp_deregister_style( 'font-awesome' );
                            wp_register_style(
                                'font-awesome',
                                $PACMEC_WALLET_plugin_url_path . "/css/font-awesome{$min}.css",
                                array(),
                                '4.7.0'
                            );
                        }
                        
                        
                        if ( !wp_style_is( 'bootstrap-pacmec-wallet', 'queue' ) && !wp_style_is( 'bootstrap-pacmec-wallet', 'done' ) ) {
                            wp_dequeue_style( 'bootstrap-pacmec-wallet' );
                            wp_deregister_style( 'bootstrap-pacmec-wallet' );
                            wp_register_style(
                                'bootstrap-pacmec-wallet',
                                $PACMEC_WALLET_plugin_url_path . "/css/bootstrap-ns{$min}.css",
                                array(),
                                '4.0.0'
                            );
                        }
                        
                        
                        if ( !wp_style_is( 'bootstrap-affix-pacmec-wallet', 'queue' ) && !wp_style_is( 'bootstrap-affix-pacmec-wallet', 'done' ) ) {
                            wp_dequeue_style( 'bootstrap-affix-pacmec-wallet' );
                            wp_deregister_style( 'bootstrap-affix-pacmec-wallet' );
                            wp_register_style(
                                'bootstrap-affix-pacmec-wallet',
                                $PACMEC_WALLET_plugin_url_path . "/css/affix.css",
                                array(),
                                '3.3.7'
                            );
                        }
                        
                        
                        if ( !wp_style_is( 'data-tables', 'queue' ) && !wp_style_is( 'data-tables', 'done' ) ) {
                            wp_dequeue_style( 'data-tables' );
                            wp_deregister_style( 'data-tables' );
                            wp_register_style(
                                'data-tables',
                                $PACMEC_WALLET_plugin_url_path . "/css/jquery.dataTables{$min}.css",
                                array(),
                                '1.10.19'
                            );
                        }
                        
                        
                        if ( !wp_style_is( 'pacmec-wallet', 'queue' ) && !wp_style_is( 'pacmec-wallet', 'done' ) ) {
                            wp_dequeue_style( 'pacmec-wallet' );
                            wp_deregister_style( 'pacmec-wallet' );
                            wp_register_style(
                                'pacmec-wallet',
                                $PACMEC_WALLET_plugin_url_path . '/pacmec-wallet.css',
                                $deps,
                                '3.3.0'
                            );
                        }
                    
                    }
                    
                    add_action( 'wp_enqueue_scripts', 'PACMEC_WALLET_stylesheet', 20 );
                    function PACMEC_WALLET_get_wallet_private_key( $user_id = null )
                    {
                        $privateKey = '';
                        return $privateKey;
                    }
                    
                    function PACMEC_WALLET_get_wallet_address( $user_id = null )
                    {
                        $accountAddress = '';
                        if ( is_null( $user_id ) ) {
                            $user_id = get_current_user_id();
                        }
                        
                        if ( $user_id > 0 ) {
                            $accountAddress = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                            
                            if ( empty($accountAddress) ) {
                                PACMEC_WALLET_user_registration( $user_id );
                                $accountAddress = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                            }
                        
                        }
                        
                        return $accountAddress;
                    }
                    
                    function PACMEC_WALLET_get_wallet_name()
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
                            foreach ( $accounts as $account ) {
                                if ( strtolower( $account["address"] ) != strtolower( $accountAddress ) ) {
                                    continue;
                                }
                                $accountName = $account['name'];
                            }
                        }
                        if ( empty($accountName) ) {
                            $accountName = __( 'Default account', 'pacmec-wallet' );
                        }
                        return $accountName;
                    }
                    
                    function PACMEC_WALLET_get_last_tx_hash_time()
                    {
                        $lastTxHash = '';
                        $lastTxTime = '';
                        $user_id = get_current_user_id();
                        
                        if ( $user_id > 0 ) {
                            $lastTxHash = get_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', true );
                            $lastTxTime = get_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', true );
                            
                            if ( !empty($lastTxHash) && 0 != PACMEC_WALLET_is_tx_confirmed( $lastTxHash ) ) {
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
                    
                    function PACMEC_WALLET_get_token_uri_filter( $tokenURI )
                    {
                        global  $PACMEC_WALLET_options ;
                        $options = stripslashes_deep( $PACMEC_WALLET_options );
                        $ipfs_gateway_url = ( !empty($options['ipfs_gateway_url']) ? esc_attr( $options['ipfs_gateway_url'] ) : "https://ipfs.io/ipfs/" );
                        return str_replace( 'ipfs://', $ipfs_gateway_url, $tokenURI );
                    }
                    
                    add_filter(
                        'cryptocurrency_product_for_woocommerce_erc721_get_ipfs_uri',
                        'PACMEC_WALLET_get_token_uri_filter',
                        10,
                        1
                    );
                    function PACMEC_WALLET_enqueue_script()
                    {
                        global  $PACMEC_WALLET_plugin_url_path, $PACMEC_WALLET_plugin_dir, $PACMEC_WALLET_options ;
                        global  $PACMEC_WALLET_erc20ContractABI ;
                        global  $PACMEC_WALLET_erc721ContractABI ;
                        $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
                        
                        if ( !wp_script_is( 'data-tables', 'queue' ) && !wp_script_is( 'data-tables', 'done' ) ) {
                            wp_dequeue_script( 'data-tables' );
                            wp_deregister_script( 'data-tables' );
                            wp_register_script(
                                'data-tables',
                                //$PACMEC_WALLET_plugin_url_path . "/js/jquery.dataTables{$min}.js", array( 'jquery' ), '1.10.19'
                                $PACMEC_WALLET_plugin_url_path . "/js/jquery.dataTables.min.js",
                                array( 'jquery' ),
                                '1.10.19'
                            );
                        }
                        
                        
                        if ( !wp_script_is( 'popper', 'queue' ) && !wp_script_is( 'popper', 'done' ) ) {
                            wp_dequeue_script( 'popper' );
                            wp_deregister_script( 'popper' );
                            wp_register_script(
                                'popper',
                                //            $PACMEC_WALLET_plugin_url_path . "/js/popper{$min}.js", array('jquery'), '1.14.6'
                                $PACMEC_WALLET_plugin_url_path . "/js/popper.min.js",
                                array( 'jquery' ),
                                '1.14.6'
                            );
                        }
                        
                        
                        if ( !wp_script_is( 'pacmec-wallet-bootstrap', 'queue' ) && !wp_script_is( 'pacmec-wallet-bootstrap', 'done' ) ) {
                            wp_dequeue_script( 'pacmec-wallet-bootstrap' );
                            wp_deregister_script( 'pacmec-wallet-bootstrap' );
                            wp_register_script(
                                'pacmec-wallet-bootstrap',
                                //            $PACMEC_WALLET_plugin_url_path . "/js/bootstrap{$min}.js", array('jquery', 'popper'), '4.0.0'
                                $PACMEC_WALLET_plugin_url_path . "/js/bootstrap.min.js",
                                array( 'jquery', 'popper' ),
                                '4.0.0'
                            );
                        }
                        
                        
                        if ( !wp_script_is( 'pacmec-wallet-bootstrap-affix', 'queue' ) && !wp_script_is( 'pacmec-wallet-bootstrap-affix', 'done' ) ) {
                            wp_dequeue_script( 'pacmec-wallet-bootstrap-affix' );
                            wp_deregister_script( 'pacmec-wallet-bootstrap-affix' );
                            wp_register_script(
                                'pacmec-wallet-bootstrap-affix',
                                $PACMEC_WALLET_plugin_url_path . "/js/affix.js",
                                array( 'pacmec-wallet-bootstrap' ),
                                '3.3.7'
                            );
                        }
                        
                        $js_dir = '/js/';
                        // 1. runtime~main
                        // 2. vendors
                        // 3. main
                        $runtimeMain = null;
                        $vendors = null;
                        $main = null;
                        $files = scandir( $PACMEC_WALLET_plugin_dir . $js_dir );
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
                        
                        if ( !wp_script_is( 'pacmec-wallet-runtime-main', 'queue' ) && !wp_script_is( 'pacmec-wallet-runtime-main', 'done' ) ) {
                            wp_dequeue_script( 'pacmec-wallet-runtime-main' );
                            wp_deregister_script( 'pacmec-wallet-runtime-main' );
                            wp_register_script(
                                'pacmec-wallet-runtime-main',
                                $PACMEC_WALLET_plugin_url_path . $js_dir . $runtimeMain[0],
                                array(),
                                $runtimeMain[1]
                            );
                        }
                        
                        
                        if ( !wp_script_is( 'pacmec-wallet-vendors', 'queue' ) && !wp_script_is( 'pacmec-wallet-vendors', 'done' ) ) {
                            wp_dequeue_script( 'pacmec-wallet-vendors' );
                            wp_deregister_script( 'pacmec-wallet-vendors' );
                            wp_register_script(
                                'pacmec-wallet-vendors',
                                $PACMEC_WALLET_plugin_url_path . $js_dir . $vendors[0],
                                array( 'pacmec-wallet-runtime-main' ),
                                $vendors[1]
                            );
                        }
                        
                        
                        if ( !wp_script_is( 'pacmec-wallet-main', 'queue' ) && !wp_script_is( 'pacmec-wallet-main', 'done' ) ) {
                            wp_dequeue_script( 'pacmec-wallet-main' );
                            wp_deregister_script( 'pacmec-wallet-main' );
                            wp_register_script(
                                'pacmec-wallet-main',
                                $PACMEC_WALLET_plugin_url_path . $js_dir . $main[0],
                                array(
                                'pacmec-wallet-vendors',
                                'wp-i18n',
                                'jquery',
                                'pacmec-wallet-bootstrap-affix'
                            ),
                                $main[1]
                            );
                        }
                        
                        //    wp_enqueue_script('pacmec-wallet-main');
                        if ( function_exists( 'wp_set_script_translations' ) ) {
                            wp_set_script_translations( 'pacmec-wallet-main', 'pacmec-wallet', $PACMEC_WALLET_plugin_dir . 'languages' );
                        }
                        $options = stripslashes_deep( $PACMEC_WALLET_options );
                        $etherscanApiKey = ( !empty($options['etherscanApiKey']) ? esc_attr( $options['etherscanApiKey'] ) : '' );
                        $blockchain_network = PACMEC_WALLET_getBlockchainNetwork();
                        $gaslimit = ( !empty($options['gaslimit']) ? esc_attr( $options['gaslimit'] ) : "200000" );
                        $ipfs_gateway_url = ( !empty($options['ipfs_gateway_url']) ? esc_attr( $options['ipfs_gateway_url'] ) : "https://ipfs.io/ipfs/" );
                        $gasprice = PACMEC_WALLET_get_gas_price_wei();
                        $gasPriceTip = PACMEC_WALLET_get_gas_price_tip_wei();
                        $accountAddress = PACMEC_WALLET_get_wallet_address();
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
                            
                            if ( !empty($lastTxHash) && 0 != PACMEC_WALLET_is_tx_confirmed( $lastTxHash ) ) {
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
                            $canListProducts = class_exists( "WCV_Vendors" ) && WCV_Vendors::is_vendor( $user_id ) || user_can( $user_id, 'vendor' ) || user_can( $user_id, 'manage_woocommerce' );
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
                        
                        wp_localize_script( 'pacmec-wallet-main', 'ethereumWallet', [
                            'erc20_abi'                                 => $PACMEC_WALLET_erc20ContractABI,
                            'erc721_abi'                                => $PACMEC_WALLET_erc721ContractABI,
                            'user_wallet_address'                       => esc_html( $accountAddress ),
                            'user_wallet_last_txhash'                   => esc_html( $lastTxHash ),
                            'user_wallet_last_txtime'                   => esc_html( $lastTxTime ),
                            'user_wallet_last_tx_to'                    => esc_html( $lastTxTo ),
                            'user_wallet_last_tx_value'                 => esc_html( $lastTxValue ),
                            'user_wallet_last_tx_currency'              => esc_html( $lastTxCurrency ),
                            'tokens'                                    => esc_html( $tokens_json ),
                            'site_url'                                  => esc_html( site_url() ),
                            'web3Endpoint'                              => PACMEC_WALLET_getWeb3Endpoint(),
                            'web3WSSEndpoint'                           => PACMEC_WALLET_getWeb3WSSEndpoint(),
                            'etherscanApiKey'                           => $etherscanApiKey,
                            'blockchain_network'                        => esc_html( $blockchain_network ),
                            'gasLimit'                                  => esc_html( $gaslimit ),
                            'gasPrice'                                  => esc_html( $gasprice ),
                            'gasPriceTip'                               => esc_html( $gasPriceTip ),
                            'ipfs_gateway_url'                          => esc_html( $ipfs_gateway_url ),
                            'tokenTxListAPIURLTemplate'                 => esc_html( PACMEC_WALLET_get_token_tx_list_api_url_template() ),
                            'tokenNFTTxListAPIURLTemplate'              => esc_html( PACMEC_WALLET_get_nft_token_tx_list_api_url_template() ),
                            'internalTxListAPIURLTemplate'              => esc_html( PACMEC_WALLET_get_internal_tx_list_api_url_template() ),
                            'txListAPIURLTemplate'                      => esc_html( PACMEC_WALLET_get_tx_list_api_url_template() ),
                            'txHashPathTemplate'                        => esc_html( PACMEC_WALLET_get_txhash_path_template() ),
                            'addressPathTemplate'                       => esc_html( PACMEC_WALLET_get_address_path_template() ),
                            'localePath'                                => esc_html( $PACMEC_WALLET_plugin_url_path . "/i18n/" . get_locale() . ".json" ),
                            'confirmations_number'                      => '2',
                            'wp_rest_nonce'                             => wp_create_nonce( 'wp_rest' ),
                            'isNFTPluginActive'                         => esc_html( $isNFTPluginActive ),
                            'canListProducts'                           => esc_html( $canListProducts ),
                            'vendorDashboardPageUrl'                    => esc_html( $vendorDashboardPageUrl ),
                            'str_copied_msg'                            => __( 'Copied to clipboard', 'pacmec-wallet' ),
                            'str_insufficient_eth_balance_msg'          => __( 'Insufficient Ether balance for tx fee payment.', 'pacmec-wallet' ),
                            'str_unknown_token_symbol_msg'              => __( 'Unknown', 'pacmec-wallet' ),
                            'str_unknown_nft_token_symbol_msg'          => __( 'Unknown NFT', 'pacmec-wallet' ),
                            'str_tx_pending_msg'                        => __( 'Pending', 'pacmec-wallet' ),
                            'str_prev_tx_pending_msg'                   => __( 'Previous transaction is still not confirmed or failed', 'pacmec-wallet' ),
                            'str_date_recently_msg'                     => __( 'recently', 'pacmec-wallet' ),
                            'str_date_days_fmt_msg'                     => __( '%1$s days', 'pacmec-wallet' ),
                            'str_date_hours_fmt_msg'                    => __( '%1$s hours', 'pacmec-wallet' ),
                            'str_date_minutes_fmt_msg'                  => __( '%1$s minutes', 'pacmec-wallet' ),
                            'str_copied_to_clipboard'                   => __( 'Copied to clipboard', 'pacmec-wallet' ),
                            'str_copy_to_clipboard'                     => __( 'Copy to clipboard', 'pacmec-wallet' ),
                            'str_qrcode_button_label'                   => __( 'QR-code', 'pacmec-wallet' ),
                            'str_alert_dlg_title'                       => __( 'Error', 'pacmec-wallet' ),
                            'str_alert_dlg_title_default'               => __( 'Alert', 'pacmec-wallet' ),
                            'str_alert_dlg_ok_button_label'             => __( 'OK', 'pacmec-wallet' ),
                            'str_qrcode_dlg_title'                      => __( 'Scan QR-code', 'pacmec-wallet' ),
                            'str_contract_address_template'             => __( 'Contract Address: ', 'pacmec-wallet' ),
                            'str_token_id_template'                     => __( 'Token ID: ', 'pacmec-wallet' ),
                            'str_account_dlg_content'                   => __( 'Enter the recipient\'s Ethereum account address please.', 'pacmec-wallet' ),
                            'str_account_dlg_address_field_label'       => __( 'Recipient', 'pacmec-wallet' ),
                            'str_account_dlg_qrcode_button_label'       => __( 'QR-code', 'pacmec-wallet' ),
                            'str_account_dlg_title'                     => __( 'Enter recipient address', 'pacmec-wallet' ),
                            'str_account_dlg_incorrect_address_msg'     => __( 'Incorrect address', 'pacmec-wallet' ),
                            'str_account_dlg_ok_button_label'           => __( 'Send', 'pacmec-wallet' ),
                            'str_account_dlg_cancel_button_label'       => __( 'Cancel', 'pacmec-wallet' ),
                            'str_confirm_dlg_title'                     => __( 'Confirm', 'pacmec-wallet' ),
                            'str_confirm_dlg_title_default'             => __( 'Confirm', 'pacmec-wallet' ),
                            'str_confirm_dlg_ok_button_label'           => __( 'OK', 'pacmec-wallet' ),
                            'str_confirm_dlg_cancel_button_label'       => __( 'Cancel', 'pacmec-wallet' ),
                            'str_nft_token_send_confirm_msg'            => __( 'You are about to send the %1$s NFT token to the %2$s account address. This action is irreversible. Are you sure?', 'pacmec-wallet' ),
                            'str_tx_progress_dlg_title'                 => __( 'Confirmations', 'pacmec-wallet' ),
                            'str_tx_progress_dlg_content'               => __( 'Tx confirmations %s', 'pacmec-wallet' ),
                            'str_token_send_failed_msg'                 => __( 'Failed to send token', 'pacmec-wallet' ),
                            'str_empty_nft_wallet_msg'                  => __( 'No NFT found. Yet?', 'pacmec-wallet' ),
                            'str_nft_token_sell_not_capable_msg'        => __( 'You need to be a vendor to sell tokens on this store. Open vendor registration page?', 'pacmec-wallet' ),
                            'str_nft_token_resell_redirect_waiting_msg' => __( 'Redirecting to the product edit page. Wait please...', 'pacmec-wallet' ),
                            'str_nft_token_resell_redirect_error_msg'   => __( 'Failed to resell token', 'pacmec-wallet' ),
                        ] );
                    }
                    
                    add_action( 'wp_enqueue_scripts', 'PACMEC_WALLET_enqueue_script' );
                    /**
                     * Admin Options
                     */
                    
                    if ( is_admin() ) {
                        include_once $PACMEC_WALLET_plugin_dir . '/settings/blockchain.php';
                        include_once $PACMEC_WALLET_plugin_dir . '/settings/api_keys.php';
                        include_once $PACMEC_WALLET_plugin_dir . '/settings/admin_fee.php';
                        include_once $PACMEC_WALLET_plugin_dir . '/settings/advanced_blockchain.php';
                        include_once $PACMEC_WALLET_plugin_dir . '/settings/ipfs.php';
                        include_once $PACMEC_WALLET_plugin_dir . '/pacmec-wallet.admin.php';
                    }
                    
                    function PACMEC_WALLET_add_menu_link()
                    {
                        $page = add_options_page(
                            __( 'Ethereum Wallet Settings', 'pacmec-wallet' ),
                            __( 'Ethereum Wallet', 'pacmec-wallet' ),
                            'manage_options',
                            'pacmec-wallet',
                            'PACMEC_WALLET_options_page'
                        );
                    }
                    
                    add_filter( 'admin_menu', 'PACMEC_WALLET_add_menu_link' );
                    // Place in Option List on Settings > Plugins page
                    function PACMEC_WALLET_actlinks( $links, $file )
                    {
                        // Static so we don't call plugin_basename on every plugin row.
                        static  $this_plugin ;
                        if ( !$this_plugin ) {
                            $this_plugin = plugin_basename( __FILE__ );
                        }
                        
                        if ( $file == $this_plugin ) {
                            $settings_link = '<a href="options-general.php?page=pacmec-wallet">' . __( 'Settings' ) . '</a>';
                            array_unshift( $links, $settings_link );
                            // before other links
                        }
                        
                        return $links;
                    }
                    
                    add_filter(
                        'plugin_action_links',
                        'PACMEC_WALLET_actlinks',
                        10,
                        2
                    );
                    function PACMEC_WALLET_get_default_gas_price_wei()
                    {
                        global  $PACMEC_WALLET_options ;
                        $gasPriceMaxGwei = doubleval( ( isset( $PACMEC_WALLET_options['gas_price'] ) ? $PACMEC_WALLET_options['gas_price'] : '21' ) );
                        return array(
                            'tm'            => time(),
                            'gas_price'     => intval( floatval( $gasPriceMaxGwei ) * 1000000000 ),
                            'gas_price_tip' => null,
                        );
                    }
                    
                    function PACMEC_WALLET_query_web3_gas_price_wei()
                    {
                        $providerUrl = PACMEC_WALLET_getWeb3Endpoint();
                        try {
                            $requestManager = new HttpRequestManager( $providerUrl, 10 );
                            $web3 = new Web3( new HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $ret = null;
                            $eth->gasPrice( function ( $err, $gasPrice ) use( &$ret ) {
                                
                                if ( $err !== null ) {
                                    PACMEC_WALLET_log( "Failed to get gasPrice: ", $err );
                                    return;
                                }
                                
                                $ret = $gasPrice;
                            } );
                            if ( is_null( $ret ) ) {
                                return null;
                            }
                            return $ret->toString();
                        } catch ( Exception $ex ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_query_web3_gas_price_wei: " . $ex->getMessage() );
                        }
                        return 0;
                    }
                    
                    function PACMEC_WALLET_query_gas_price_wei()
                    {
											$gasPriceWei = null;
											$gasPriceTipWei = null;
											$providerUrl = PACMEC_WALLET_getWeb3Endpoint();
											try {
												$requestManager = new HttpRequestManager( $providerUrl, 10 );
												$web3 = new Web3( new HttpProvider( $requestManager ) );
												$eth = $web3->eth;
												$isEIP1559 = PACMEC_WALLET_isEIP1559( $eth );
												
												if ( !$isEIP1559 ) {
													PACMEC_WALLET_log( "PACMEC_WALLET_query_gas_price_wei: !isEIP1559" );
													$gasPriceWei = PACMEC_WALLET_query_web3_gas_price_wei();
												} else {
													PACMEC_WALLET_log( "PACMEC_WALLET_query_gas_price_wei: isEIP1559" );
													list( $error, $block ) = PACMEC_WALLET_getLatestBlock( $eth );
													
													if ( !is_null( $error ) ) {
															PACMEC_WALLET_log( "PACMEC_WALLET_query_gas_price_wei: Failed to get block: " . $error );
															return PACMEC_WALLET_get_default_gas_price_wei();
													}
													
													$gasPriceTipWei = PACMEC_WALLET_query_web3_gas_price_wei();
													$gasPriceWei = ( new BigInteger( $block->baseFeePerGas, 16 ) )->multiply( new BigInteger( 2 ) )->add( new BigInteger( $gasPriceTipWei ) );
													$gasPriceWei = $gasPriceWei->toString();
													
													if ( '0' === $gasPriceWei ) {
															PACMEC_WALLET_log( "PACMEC_WALLET_query_gas_price_wei: 0 === gasPriceWei: " . $block->baseFeePerGas . ', bn=' . ( new BigInteger( $block->baseFeePerGas ) )->toString() . '; block=' . print_r( $block, true ) );
															return PACMEC_WALLET_get_default_gas_price_wei();
													}
												}
											} catch ( Exception $ex ) {
												PACMEC_WALLET_log( "PACMEC_WALLET_query_gas_price_wei: " . $ex->getMessage() );
												return PACMEC_WALLET_get_default_gas_price_wei();
											}
											if ( is_null( $gasPriceWei ) ) {
												PACMEC_WALLET_log( "PACMEC_WALLET_query_gas_price_wei: is_null(gasPriceWei)" );
												return PACMEC_WALLET_get_default_gas_price_wei();
											}
											$cache_gas_price = array(
													'tm'            => time(),
													'gas_price'     => $gasPriceWei,
													'gas_price_tip' => $gasPriceTipWei,
											);
											$chainId = PACMEC_WALLET_getChainId();
											if ( null === $chainId ) {
												PACMEC_WALLET_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'pacmec-wallet' ), __( "Blockchain", 'pacmec-wallet' ) ) );
												return PACMEC_WALLET_get_default_gas_price_wei();
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
                    
                    function PACMEC_WALLET_get_gas_price_wei()
                    {
                        $chainId = PACMEC_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            PACMEC_WALLET_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'pacmec-wallet' ), __( "Blockchain", 'pacmec-wallet' ) ) );
                            return null;
                        }
                        
                        $option_name = 'ethereumicoio_cache_gas_price-wei-' . $chainId;
                        $cache_gas_price_wei = get_option( $option_name, array() );
                        if ( !$cache_gas_price_wei ) {
                            $cache_gas_price_wei = PACMEC_WALLET_query_gas_price_wei();
                        }
                        $tm_diff = time() - intval( $cache_gas_price_wei['tm'] );
                        // TODO: admin setting
                        $timeout = 10 * 60;
                        // seconds
                        if ( $tm_diff > $timeout ) {
                            $cache_gas_price_wei = PACMEC_WALLET_query_gas_price_wei();
                        }
                        $gasPriceWei = doubleval( $cache_gas_price_wei['gas_price'] );
                        
                        if ( is_null( $cache_gas_price_wei['gas_price_tip'] ) ) {
                            // only if pre-EIP1559
                            $gasPriceMaxWei = doubleval( PACMEC_WALLET_get_default_gas_price_wei()['gas_price'] );
                            if ( $gasPriceMaxWei < $gasPriceWei ) {
                                $gasPriceWei = $gasPriceMaxWei;
                            }
                        }
                        
                        return intval( $gasPriceWei );
                    }
                    
                    function PACMEC_WALLET_get_gas_price_tip_wei()
                    {
                        $chainId = PACMEC_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            PACMEC_WALLET_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'pacmec-wallet' ), __( "Blockchain", 'pacmec-wallet' ) ) );
                            return null;
                        }
                        
                        $option_name = 'ethereumicoio_cache_gas_price-wei-' . $chainId;
                        $cache_gas_price_wei = get_option( $option_name, array() );
                        
                        if ( !$cache_gas_price_wei ) {
                            $cache_gas_price_wei = PACMEC_WALLET_query_gas_price_wei();
                            PACMEC_WALLET_log( '!$cache_gas_price_wei: ' . print_r( $cache_gas_price_wei, true ) );
                        }
                        
                        $tm_diff = time() - intval( $cache_gas_price_wei['tm'] );
                        // TODO: admin setting
                        $timeout = 10 * 60;
                        // seconds
                        
                        if ( $tm_diff > $timeout ) {
                            $cache_gas_price_wei = PACMEC_WALLET_query_gas_price_wei();
                            PACMEC_WALLET_log( '$tm_diff > $timeout: ' . print_r( $cache_gas_price_wei, true ) );
                        }
                        
                        
                        if ( is_null( $cache_gas_price_wei['gas_price_tip'] ) ) {
                            PACMEC_WALLET_log( 'is_null($cache_gas_price_wei[\'gas_price_tip\']): ' . print_r( $cache_gas_price_wei, true ) );
                            return null;
                        }
                        
                        $gasPriceTipWei = doubleval( $cache_gas_price_wei['gas_price_tip'] );
                        
                        if ( !is_null( PACMEC_WALLET_get_default_gas_price_wei()['gas_price'] ) ) {
                            $gasPriceTipMaxWei = doubleval( PACMEC_WALLET_get_default_gas_price_wei()['gas_price'] );
                            if ( $gasPriceTipMaxWei < $gasPriceTipWei ) {
                                $gasPriceTipWei = $gasPriceTipMaxWei;
                            }
                        }
                        
                        return intval( $gasPriceTipWei );
                    }
                    
                    function PACMEC_WALLET_isEIP1559( $eth = null )
                    {
                        $chainId = PACMEC_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            PACMEC_WALLET_log( sprintf( __( 'Configuration error! The "%s" setting is not set.', 'pacmec-wallet' ), __( "Blockchain", 'pacmec-wallet' ) ) );
                            return null;
                        }
                        
                        $option_name = 'ethereumicoio_cache_is-eip-1559-network-' . $chainId;
                        // delete_option($option_name);
                        $isEIP1559Option = get_option( $option_name, null );
                        $tm_diff = time() - intval( ( !is_null( $isEIP1559Option ) ? $isEIP1559Option['tm'] : time() ) );
                        // TODO: admin setting
                        $timeout = 10 * 60;
                        // seconds
                        // list($error, $block) = PACMEC_WALLET_getLatestBlock($eth);
                        $isEIP1559 = ( !is_null( $isEIP1559Option ) ? $isEIP1559Option['isEIP1559'] : null );
                        PACMEC_WALLET_log( $option_name . ' : isEIP1559=' . (( is_null( $isEIP1559 ) ? 'null' : (( $isEIP1559 ? 'true' : 'false' )) )) );
                        
                        if ( is_null( $isEIP1559 ) || $tm_diff > $timeout ) {
                            list( $error, $block ) = PACMEC_WALLET_getLatestBlock( $eth );
                            
                            if ( !is_null( $error ) ) {
                                PACMEC_WALLET_log( "Failed to get block: " . $error );
                                return null;
                            }
                            
                            $isEIP1559 = property_exists( $block, 'baseFeePerGas' );
                            PACMEC_WALLET_log( 'PACMEC_WALLET_isEIP1559: isEIP1559=' . (( is_null( $isEIP1559 ) ? 'null' : (( $isEIP1559 ? 'true' : 'false' )) )) );
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
                    
                    function PACMEC_WALLET_send_transaction( $to, $eth_value_wei, $data )
                    {
                        global  $PACMEC_WALLET_options ;
                        $chainId = PACMEC_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            PACMEC_WALLET_log( "chainId is null" );
                            return false;
                        }
                        
                        $user_id = get_current_user_id();
                        
                        if ( $user_id <= 0 ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_send_transaction: no user" );
                            return false;
                        }
                        
                        $lastTxHash = get_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', true );
                        
                        if ( !empty($lastTxHash) && 0 == PACMEC_WALLET_is_tx_confirmed( $lastTxHash ) ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_send_transaction: tx already in progress: " . $lastTxHash );
                            return false;
                        }
                        
                        $lastTxTime = get_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', true );
                        delete_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', $lastTxHash );
                        delete_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', $lastTxTime );
                        $from = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                        
                        if ( empty($from) ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_send_transaction: empty from address" );
                            return false;
                        }
                        
                        $txHash = null;
                        try {
                            $providerUrl = PACMEC_WALLET_getWeb3Endpoint();
                            $requestManager = new HttpRequestManager( $providerUrl, 10 );
                            $web3 = new Web3( new HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $nonce = 0;
                            $eth->getTransactionCount( $from, function ( $err, $transactionCount ) use( &$nonce ) {
                                
                                if ( $err !== null ) {
                                    PACMEC_WALLET_log( "Failed to getTransactionCount: " . $err );
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
                                $gasLimit = intval( ( isset( $PACMEC_WALLET_options['gas_limit'] ) ? $PACMEC_WALLET_options['gas_limit'] : '200000' ) );
                            }
                            $gasPrice = PACMEC_WALLET_get_gas_price_wei();
                            $gasPriceTip = PACMEC_WALLET_get_gas_price_tip_wei();
                            $eth_value_wei = new BigInteger( $eth_value_wei );
                            $nonce = Buffer::int( $nonce );
                            $gasPrice = Buffer::int( $gasPrice );
                            $gasLimit = Buffer::int( $gasLimit );
                            $value = $eth_value_wei->toHex();
                            //PACMEC_WALLET_log("value: " . $value);
                            $transactionParamsArray = [
                                'from'    => $from,
                                'nonce'   => '0x' . $nonce->getHex(),
                                'to'      => strtolower( $to ),
                                'gas'     => '0x' . $gasLimit->getHex(),
                                'value'   => '0x' . $value,
                                'chainId' => $chainId,
                                'data'    => $data,
                            ];
                            list( $error, $gasEstimate ) = PACMEC_WALLET_get_gas_estimate( $transactionParamsArray, $eth );
                            
                            if ( null === $gasEstimate ) {
                                PACMEC_WALLET_log( "gasEstimate is null: " . $error );
                                return false;
                            }
                            
                            PACMEC_WALLET_log( "gasEstimate: " . $gasEstimate->toString() );
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
                            list( $error, $eth_balance ) = PACMEC_WALLET_getBalanceEth( $providerUrl, $from );
                            
                            if ( null === $eth_balance ) {
                                PACMEC_WALLET_log( "eth_balance is null: " . $error );
                                return false;
                            }
                            
                            
                            if ( $eth_balance->compare( $eth_value_with_fee_wei ) < 0 ) {
                                $eth_balance_str = $eth_balance->toString();
                                $eth_value_with_fee_wei_str = $eth_value_with_fee_wei->toString();
                                PACMEC_WALLET_log( "Insufficient funds: eth_balance_wei({$eth_balance_str}) < eth_value_with_fee_wei({$eth_value_with_fee_wei_str})" );
                                return false;
                            }
                            
                            $transaction = new Transaction( $transactionParamsArray );
                            $privateKey = get_user_meta( $user_id, 'user_ethereum_wallet_key', true );
                            
                            if ( empty($privateKey) ) {
                                PACMEC_WALLET_log( "PACMEC_WALLET_send_transaction: empty key" );
                                return false;
                            }
                            
                            $signedTransaction = "0x" . $transaction->sign( $privateKey );
                            //PACMEC_WALLET_log("transaction: " . print_r($transactionParamsArray, true));
                            //PACMEC_WALLET_log("signedTransaction: " . $signedTransaction);
                            $eth->sendRawTransaction( (string) $signedTransaction, function ( $err, $transaction ) use( &$txHash, $transactionParamsArray, $signedTransaction ) {
                                
                                if ( $err !== null ) {
                                    PACMEC_WALLET_log( "Failed to sendRawTransaction: " . $err );
                                    PACMEC_WALLET_log( "Failed to sendRawTransaction: transactionData=" . print_r( $transactionParamsArray, true ) );
                                    PACMEC_WALLET_log( "Failed to sendRawTransaction: signedTransaction=" . (string) $signedTransaction );
                                    return;
                                }
                                
                                $txHash = $transaction;
                            } );
                            
                            if ( null === $txHash ) {
                                PACMEC_WALLET_log( "Failed to sendRawTransaction: txHash == null" );
                                return false;
                            }
                            
                            PACMEC_WALLET_log( "txHash: " . $txHash );
                            
                            if ( false !== $txHash ) {
                                update_user_meta( $user_id, 'user_ethereum_wallet_last_txhash', $txHash );
                                update_user_meta( $user_id, 'user_ethereum_wallet_last_tx_hash', $txHash );
                                update_user_meta( $user_id, 'user_ethereum_wallet_last_txtime', time() );
                                update_user_meta( $user_id, 'user_ethereum_wallet_last_tx_to', $transactionParamsArray['to'] );
                                update_user_meta( $user_id, 'user_ethereum_wallet_last_tx_value', $transactionParamsArray['value'] );
                            }
                        
                        } catch ( \Exception $ex ) {
                            PACMEC_WALLET_log( $ex->getMessage() );
                            return false;
                        }
                        return $txHash;
                    }
                    
                    function PACMEC_WALLET_sign_transaction(
                        $to,
                        $eth_value_wei,
                        $data,
                        $gasLimit,
                        $gasPrice,
                        $nonce,
                        $params
                    )
                    {
                        global  $PACMEC_WALLET_options ;
                        $chainId = PACMEC_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            PACMEC_WALLET_log( "chainId is null" );
                            return false;
                        }
                        
                        $user_id = get_current_user_id();
                        
                        if ( $user_id <= 0 ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_send_transaction: no user" );
                            return false;
                        }
                        
                        $from = get_user_meta( $user_id, 'user_ethereum_wallet_address', true );
                        
                        if ( empty($from) ) {
                            PACMEC_WALLET_log( "PACMEC_WALLET_send_transaction: empty from address" );
                            return false;
                        }
                        
                        $gasPrice = ( isset( $params['maxFeePerGas'] ) ? $params['maxFeePerGas'] : $gasPrice );
                        if ( is_null( $gasPrice ) ) {
                            $gasPrice = PACMEC_WALLET_get_gas_price_wei();
                        }
                        $eth_value_wei = new BigInteger( $eth_value_wei, 16 );
                        $eth_value_with_fee_wei = $eth_value_wei->add( ( new BigInteger( str_replace( '0x', '', $gasLimit ), 16 ) )->multiply( new BigInteger( str_replace( '0x', '', $gasPrice ), 16 ) ) );
                        $signedTransaction = null;
                        try {
                            // 1. check deposit
                            $providerUrl = PACMEC_WALLET_getWeb3Endpoint();
                            list( $error, $eth_balance ) = PACMEC_WALLET_getBalanceEth( $providerUrl, $from );
                            
                            if ( null === $eth_balance ) {
                                PACMEC_WALLET_log( "eth_balance is null" );
                                return false;
                            }
                            
                            
                            if ( $eth_balance->compare( $eth_value_with_fee_wei ) < 0 ) {
                                $eth_balance_str = $eth_balance->toString();
                                $eth_value_with_fee_wei_str = $eth_value_with_fee_wei->toString();
                                PACMEC_WALLET_log( "Insufficient funds: eth_balance_wei({$eth_balance_str}) < eth_value_with_fee_wei({$eth_value_with_fee_wei_str})" );
                                return false;
                            }
                            
                            $requestManager = new HttpRequestManager( $providerUrl, 10 );
                            $web3 = new Web3( new HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $value = $eth_value_wei->toHex();
                            //PACMEC_WALLET_log("value: " . $value);
                            $gasPriceTip = ( isset( $params['maxPriorityFeePerGas'] ) ? $params['maxPriorityFeePerGas'] : PACMEC_WALLET_get_gas_price_tip_wei() );
                            $transactionParamsArray = [
                                'nonce'   => $nonce,
                                'to'      => strtolower( $to ),
                                'gas'     => $gasLimit,
                                'value'   => '0x' . $value,
                                'chainId' => $chainId,
                                'data'    => $data,
                            ];
                            PACMEC_WALLET_log( "PACMEC_WALLET_sign_transaction: gasPrice=" . $gasPrice );
                            
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
                                PACMEC_WALLET_log( "PACMEC_WALLET_send_transaction: empty key" );
                                return false;
                            }
                            
                            $signedTransaction = "0x" . $transaction->sign( $privateKey );
                            PACMEC_WALLET_log( "transaction: " . print_r( $transactionParamsArray, true ) );
                            //PACMEC_WALLET_log("signedTransaction: " . $signedTransaction);
                        } catch ( \Exception $ex ) {
                            PACMEC_WALLET_log( $ex->getMessage() );
                            return false;
                        }
                        return $signedTransaction;
                    }
                    
                    function PACMEC_WALLET_get_gas_estimate( $transactionParamsArray, $eth )
                    {
                        $gasEstimate = null;
                        $error = null;
                        $transactionParamsArrayCopy = $transactionParamsArray;
                        unset( $transactionParamsArrayCopy['nonce'] );
                        unset( $transactionParamsArrayCopy['chainId'] );
                        //    PACMEC_WALLET_log("PACMEC_WALLET_get_gas_estimate: " . print_r($transactionParamsArray, true));
                        //    PACMEC_WALLET_log("PACMEC_WALLET_get_gas_estimate2: " . print_r($transactionParamsArrayCopy, true));
                        $eth->estimateGas( $transactionParamsArrayCopy, function ( $err, $gas ) use( &$gasEstimate, &$error ) {
                            
                            if ( $err !== null ) {
                                PACMEC_WALLET_log( "Failed to estimateGas: " . $err );
                                $error = $err;
                                return;
                            }
                            
                            $gasEstimate = $gas;
                        } );
                        return [ $error, $gasEstimate ];
                    }
                    
                    function PACMEC_WALLET_send_ether(
                        $from,
                        $to,
                        $eth_value,
                        $privateKey
                    )
                    {
                        global  $PACMEC_WALLET_options ;
                        $chainId = PACMEC_WALLET_getChainId();
                        
                        if ( null === $chainId ) {
                            PACMEC_WALLET_log( "chainId is null" );
                            return [ sprintf( __( 'Bad "%s" setting', 'pacmec-wallet' ), __( 'Blockchain', 'pacmec-wallet' ) ), false ];
                        }
                        
                        $error = null;
                        try {
                            $providerUrl = PACMEC_WALLET_getWeb3Endpoint();
                            $requestManager = new HttpRequestManager( $providerUrl, 10 );
                            $web3 = new Web3( new HttpProvider( $requestManager ) );
                            $eth = $web3->eth;
                            $nonce = 0;
                            $eth->getTransactionCount( $from, function ( $err, $transactionCount ) use( &$nonce, &$error ) {
                                
                                if ( $err !== null ) {
                                    PACMEC_WALLET_log( "Failed to getTransactionCount: " . $err );
                                    $nonce = null;
                                    $error = $err;
                                    return;
                                }
                                
                                $nonce = intval( $transactionCount->toString() );
                            } );
                            if ( null === $nonce ) {
                                return [ $error, false ];
                            }
                            $gasLimit = intval( ( isset( $PACMEC_WALLET_options['gas_limit'] ) ? $PACMEC_WALLET_options['gas_limit'] : '200000' ) );
                            $gasPrice = PACMEC_WALLET_get_gas_price_wei();
                            $gasPriceTip = PACMEC_WALLET_get_gas_price_tip_wei();
                            $eth_value_wei = _PACMEC_WALLET_double_int_multiply( $eth_value, pow( 10, 18 ) );
                            //    $data = '';
                            $nonce = Buffer::int( $nonce );
                            $gasPrice = Buffer::int( $gasPrice );
                            $gasLimit = Buffer::int( $gasLimit );
                            $value = $eth_value_wei->toHex();
                            //PACMEC_WALLET_log("value: " . $value);
                            $transactionParamsArray = [
                                'from'    => $from,
                                'nonce'   => '0x' . $nonce->getHex(),
                                'to'      => strtolower( $to ),
                                'gas'     => '0x' . $gasLimit->getHex(),
                                'value'   => '0x' . $value,
                                'chainId' => $chainId,
                            ];
                            list( $error, $gasEstimate ) = PACMEC_WALLET_get_gas_estimate( $transactionParamsArray, $eth );
                            
                            if ( null === $gasEstimate ) {
                                PACMEC_WALLET_log( "gasEstimate is null: " . $error );
                                return [ ( !is_null( $error ) ? $error : __( 'Failed to estimate gas', 'pacmec-wallet' ) ), false ];
                            }
                            
                            PACMEC_WALLET_log( "gasEstimate: " . $gasEstimate->toString() );
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
                            list( $error, $eth_balance ) = PACMEC_WALLET_getBalanceEth( $providerUrl, $from );
                            
                            if ( null === $eth_balance ) {
                                PACMEC_WALLET_log( "eth_balance is null" );
                                return [ ( !is_null( $error ) ? $error : __( 'Failed to obtain account balance', 'pacmec-wallet' ) ), false ];
                            }
                            
                            
                            if ( $eth_balance->compare( $eth_value_with_fee_wei ) < 0 ) {
                                $eth_value_str = $eth_value_wei->toString();
                                $eth_balance_str = $eth_balance->toString();
                                $eth_value_with_fee_wei_str = $eth_value_with_fee_wei->toString();
                                PACMEC_WALLET_log( "Insufficient funds: eth_balance_wei({$eth_balance_str}) < eth_value_with_fee_wei({$eth_value_with_fee_wei_str}); eth_value = {$eth_value_str}" );
                                return [ __( 'Insufficient Ether balance for tx fee payment.', 'pacmec-wallet' ), false ];
                            }
                            
                            $transaction = new Transaction( $transactionParamsArray );
                            $signedTransaction = "0x" . $transaction->sign( $privateKey );
                            //PACMEC_WALLET_log("transaction: " . print_r($transactionParamsArray, true));
                            PACMEC_WALLET_log( "signedTransaction: " . $signedTransaction );
                            $txHash = null;
                            $error = null;
                            $eth->sendRawTransaction( (string) $signedTransaction, function ( $err, $transaction ) use(
                                &$txHash,
                                &$error,
                                $transactionParamsArray,
                                $signedTransaction
                            ) {
                                
                                if ( $err !== null ) {
                                    PACMEC_WALLET_log( "Failed to sendRawTransaction: " . $err );
                                    PACMEC_WALLET_log( "Failed to sendRawTransaction: transactionData=" . print_r( $transactionParamsArray, true ) );
                                    PACMEC_WALLET_log( "Failed to sendRawTransaction: signedTransaction=" . (string) $signedTransaction );
                                    $error = $err;
                                    return;
                                }
                                
                                $txHash = $transaction;
                            } );
                            PACMEC_WALLET_log( "txHash: " . $txHash );
                            if ( null === $txHash ) {
                                //        PACMEC_WALLET_log("Failed to sendRawTransaction");
                                return [ $error, false ];
                            }
                            return [ null, $txHash ];
                        } catch ( \Exception $ex ) {
                            PACMEC_WALLET_log( $ex->getMessage() );
                            if ( is_null( $error ) ) {
                                $error = $ex->getMessage();
                            }
                            return [ $error, false ];
                        }
                    }
                    
                    // if ( ethereum_wallet_freemius_init()->is__premium_only() ) {
                    function PACMEC_WALLET_getChainId()
                    {
                        global  $PACMEC_WALLET_options ;
                        static  $_saved_chain_id = null ;
                        if ( is_null( $_saved_chain_id ) ) {
                            $_saved_chain_id = _PACMEC_WALLET_getChainId_impl();
                        }
                        return $_saved_chain_id;
                    }
                    
                    function _PACMEC_WALLET_getChainId_impl()
                    {
                        global  $PACMEC_WALLET_options ;
                        $blockchainNetwork = PACMEC_WALLET_getBlockchainNetwork();
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
												if ( $blockchainNetwork === 'mintme' ) {
                            return 24734;
                        }
												if ( $blockchainNetwork === 'pacmec' ) {
                            return 57001;
                        }
												if ( $blockchainNetwork === 'pacmectest' ) {
                            return 57003;
                        }
                        PACMEC_WALLET_log( "Bad blockchain_network setting:" . $blockchainNetwork );
                        return null;
                    }
                    
                    function PACMEC_WALLET_get_txhash_path( $txHash )
                    {
                        return sprintf( PACMEC_WALLET_get_txhash_path_template(), $txHash );
                    }
                    
                    function PACMEC_WALLET_get_txhash_path_template()
                    {
                        global  $PACMEC_WALLET_options ;
                        $blockchainNetwork = PACMEC_WALLET_getBlockchainNetwork();
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
                            case 'mintme':
                                $txHashPath = 'https://www.mintme.com/explorer/tx/%s';
                                break;
                            default:
                                break;
                        }
                        return $txHashPath;
                    }
                    
                    function PACMEC_WALLET_get_address_path( $address )
                    {
                        return sprintf( PACMEC_WALLET_get_address_path_template(), $address );
                    }
                    
                    function PACMEC_WALLET_get_address_path_template()
                    {
                        global  $PACMEC_WALLET_options ;
                        $blockchainNetwork = PACMEC_WALLET_getBlockchainNetwork();
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
                            case 'mintme':
                                $txHashPath = 'https://www.mintme.com/explorer/address/%s';
                                break;
                            default:
                                break;
                        }
                        return $addressPath;
                    }
                    
                    function PACMEC_WALLET_get_tx_list_api_url_template()
                    {
                        global  $PACMEC_WALLET_options ;
                        $blockchainNetwork = PACMEC_WALLET_getBlockchainNetwork();
                        $options = stripslashes_deep( $PACMEC_WALLET_options );
                        $etherscanApiKey = ( !empty($options['etherscanApiKey']) ? esc_attr( $options['etherscanApiKey'] ) : '' );
                        $blockchain_network = '';
                        if ( 'mainnet' !== $blockchainNetwork ) {
                            $blockchain_network = '-' . $blockchainNetwork;
                        }
                        $tx_list_api_url = 'https://api' . $blockchain_network . '.etherscan.io/api?module=account&action=txlist&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=' . $etherscanApiKey;
                        return $tx_list_api_url;
                    }
                    
                    function PACMEC_WALLET_get_internal_tx_list_api_url_template()
                    {
                        global  $PACMEC_WALLET_options ;
                        $blockchainNetwork = PACMEC_WALLET_getBlockchainNetwork();
                        $options = stripslashes_deep( $PACMEC_WALLET_options );
                        $etherscanApiKey = ( !empty($options['etherscanApiKey']) ? esc_attr( $options['etherscanApiKey'] ) : '' );
                        $blockchain_network = '';
                        if ( 'mainnet' !== $blockchainNetwork ) {
                            $blockchain_network = '-' . $blockchainNetwork;
                        }
                        $internal_tx_list_api_url = 'https://api' . $blockchain_network . '.etherscan.io/api?module=account&action=txlistinternal&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=' . $etherscanApiKey;
                        return $internal_tx_list_api_url;
                    }
                    
                    function PACMEC_WALLET_get_token_tx_list_api_url_template()
                    {
                        global  $PACMEC_WALLET_options ;
                        $blockchainNetwork = PACMEC_WALLET_getBlockchainNetwork();
                        $options = stripslashes_deep( $PACMEC_WALLET_options );
                        $etherscanApiKey = ( !empty($options['etherscanApiKey']) ? esc_attr( $options['etherscanApiKey'] ) : '' );
                        $blockchain_network = '';
                        if ( 'mainnet' !== $blockchainNetwork ) {
                            $blockchain_network = '-' . $blockchainNetwork;
                        }
                        $token_tx_list_api_url = 'https://api' . $blockchain_network . '.etherscan.io/api?module=account&action=tokentx&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=' . $etherscanApiKey;
                        return $token_tx_list_api_url;
                    }
                    
                    function PACMEC_WALLET_get_nft_token_tx_list_api_url_template()
                    {
                        global  $PACMEC_WALLET_options ;
                        $blockchainNetwork = PACMEC_WALLET_getBlockchainNetwork();
                        $options = stripslashes_deep( $PACMEC_WALLET_options );
                        $etherscanApiKey = ( !empty($options['etherscanApiKey']) ? esc_attr( $options['etherscanApiKey'] ) : '' );
                        $blockchain_network = '';
                        if ( 'mainnet' !== $blockchainNetwork ) {
                            $blockchain_network = '-' . $blockchainNetwork;
                        }
                        $nft_token_tx_list_api_url = 'https://api' . $blockchain_network . '.etherscan.io/api?module=account&action=tokennfttx&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=' . $etherscanApiKey;
                        return $nft_token_tx_list_api_url;
                    }
                    
                    function _PACMEC_WALLET_double_int_multiply( $dval, $ival )
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
                    add_action( 'manage_users_columns', 'PACMEC_WALLET_modify_user_columns' );
                    function PACMEC_WALLET_modify_user_columns( $column_headers )
                    {
                        $column_headers['ethereum_wallet'] = __( 'Ethereum wallet', 'pacmec-wallet' );
                        return $column_headers;
                    }
                    
                    add_action( 'admin_head', 'PACMEC_WALLET_custom_admin_css' );
                    function PACMEC_WALLET_custom_admin_css()
                    {
                        echo  '<style>
  .column-ethereum_wallet {width: 22%}
  </style>' ;
                    }
                    
                    add_action(
                        'manage_users_custom_column',
                        'PACMEC_WALLET_user_posts_count_column_content',
                        10,
                        3
                    );
                    function PACMEC_WALLET_user_posts_count_column_content( $value, $column_name, $user_id )
                    {
                        
                        if ( 'ethereum_wallet' == $column_name ) {
                            $address = PACMEC_WALLET_get_wallet_address( $user_id );
                            $addressPath = PACMEC_WALLET_get_address_path( $address );
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