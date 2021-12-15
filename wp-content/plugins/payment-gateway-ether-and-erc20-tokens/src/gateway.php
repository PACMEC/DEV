<?php

namespace Ethereumico\Epg;

require $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path . '/vendor/autoload.php';
require_once $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path . '/vendor/woocommerce/action-scheduler/action-scheduler.php';
use  Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger ;
use  Ethereumico\Epg\Dependencies\Web3\Web3 ;
use  Ethereumico\Epg\Dependencies\Web3\Providers\HttpProvider ;
use  Ethereumico\Epg\Dependencies\Web3\RequestManagers\HttpRequestManager ;
use  Ethereumico\Epg\Dependencies\Web3\Contract ;
//use \Ethereumico\Epg\Dependencies\Web3\Utils;
//use \Ethereumico\Epg\Dependencies\WC_Logger;
//use \Ethereumico\Epg\Dependencies\WC_Order;
use  WC_Payment_Gateway ;
use  WC_Admin_Settings ;
require_once $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path . '/src/coinbaseratesource.php';
require_once $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path . '/src/coinmarketcapratesource.php';
require_once $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path . '/src/cryptocompareratesource.php';
require_once $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path . '/src/kangaratesource.php';
require_once $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path . '/src/livecoinratesource.php';
require_once $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path . '/src/uniswapratesource.php';
/**
 * WooCommerce gateway class implementation.
 */
class Gateway extends WC_Payment_Gateway
{
    /**
     * Constructor, set variables etc. and add hooks/filters
     */
    function __construct( $id = null )
    {
        $this->id = ( is_null( $id ) ? 'ether-and-erc20-tokens-woocommerce-payment-gateway' : $id );
        if ( is_null( $id ) ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway1'] = $this;
        }
        $this->has_fields = true;
        $this->supports = array( 'products' );
        // Load the settings.
        $this->init_settings();
        $base_currency_ticker_name = $this->get_setting_( 'currency_ticker_name', 'Ether' );
        $token_standard_name = $this->get_setting_( 'token_standard_name', 'ERC20' );
        $blockchain_display_name = $this->get_setting_( 'blockchain_display_name', 'Ethereum' );
        $this->method_title = sprintf( __( 'Pay with %1$s or %2$s token', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name, $token_standard_name );
        $this->method_description = sprintf(
            __( 'Pay with %1$s or %2$s token provides customers a way to pay with MetaMask or any other %3$s wallet.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            $base_currency_ticker_name,
            $token_standard_name,
            $blockchain_display_name
        );
        // Load the form fields.
        $this->init_form_fields();
        // Set the public facing title according to the user's setting.
        $base_currency_ticker = $this->get_setting_( 'currency_ticker', 'ETH' );
        $this->title = $this->get_setting_( 'title', sprintf( __( 'Pay with %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ) );
        //        $this->description = $this->settings['short_description'];
        $this->description = $this->method_description;
        $this->view_transaction_url = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->get_txhash_path_template( $this );
        // Save options from admin forms.
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'verify_api_connection' ) );
        // Show gateway icon.
        //        add_filter('woocommerce_gateway_icon', array($this, 'show_icons'), 10, 2);
        add_filter(
            'epg_rate_source_create',
            array( $this, 'rate_source_create_filter' ),
            10,
            6
        );
        add_filter(
            'epg_rate_sources_list',
            array( $this, 'rate_sources_list_filter' ),
            10,
            2
        );
        //        add_action('woocommerce_checkout_order_processed', array($this, 'checkout_order_processed_handler'), 30, 3);
        // Show payment instructions on thank you page.
        add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thank_you_page' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
        add_action( 'woocommerce_order_status_on-hold', array( $this, 'order_on_hold_handler' ) );
        add_action( 'woocommerce_order_status_cancelled', array( $this, 'order_cancelled_handler' ) );
        add_action( 'before_delete_post', array( $this, 'before_delete_post_handler' ) );
        //        add_action( 'ether_and_erc20_tokens_woocommerce_payment_gateway_complete_order', array( $this, 'complete_order' ), 0, 1 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_filter(
            'woocommerce_product_data_store_cpt_get_products_query',
            array( $this, 'handling_custom_meta_query_keys' ),
            10,
            3
        );
    }
    
    public function rate_source_create_filter(
        $rateSource,
        $id,
        $rateSourceId,
        $sourceAddress,
        $sourceSymbol,
        $destination
    )
    {
        if ( !is_null( $rateSource ) ) {
            return $rateSource;
        }
        if ( $id !== $this->id ) {
            return $rateSource;
        }
        switch ( $rateSourceId ) {
            case 'Coinmarketcap.com':
                $apiKey = esc_attr( $this->get_setting_( 'coinmarketcap_api_key' ) );
                $rateSource = new CoinmarketcapRateSource(
                    $sourceAddress,
                    $sourceSymbol,
                    $destination,
                    $apiKey
                );
                break;
            case 'Cryptocompare.com':
                $apiKey = esc_attr( $this->get_setting_( 'cryptocompare_api_key' ) );
                $rateSource = new CryptocompareRateSource(
                    $sourceAddress,
                    $sourceSymbol,
                    $destination,
                    $apiKey
                );
                break;
            case 'Livecoin.net':
                $rateSource = new LivecoinRateSource( $sourceAddress, $sourceSymbol, $destination );
                break;
            case 'Coinbase.com':
                $rateSource = new CoinbaseRateSource( $sourceAddress, $sourceSymbol, $destination );
                break;
            case 'Kanga.exchange':
                $rateSource = new KangaRateSource( $sourceAddress, $sourceSymbol, $destination );
                break;
            case 'UniswapV2':
                $providerUrl = $this->getWeb3Endpoint();
                $rateSource = new UniswapV2RateSource(
                    $sourceAddress,
                    $sourceSymbol,
                    $destination,
                    $providerUrl
                );
                break;
            default:
                break;
        }
        return $rateSource;
    }
    
    public function rate_sources_list_filter( $rate_sources, $id )
    {
        if ( is_null( $rate_sources ) ) {
            $rate_sources = [];
        }
        if ( $id !== $this->id ) {
            return $rate_sources;
        }
        $rate_sources[] = 'Coinmarketcap.com';
        $rate_sources[] = 'Cryptocompare.com';
        $rate_sources[] = 'Livecoin.net';
        $rate_sources[] = 'Coinbase.com';
        $rate_sources[] = 'UniswapV2';
        $rate_sources[] = 'Kanga.exchange';
        return $rate_sources;
    }
    
    protected function get_rate_sources_()
    {
        $rate_sources = apply_filters( 'epg_rate_sources_list', [], $this->id );
        return array_merge( [ __( 'Fixed', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) ], array_unique( $rate_sources ) );
    }
    
    public function admin_enqueue_scripts( $hook )
    {
        if ( 'woocommerce_page_wc-settings' != $hook ) {
            return;
        }
        $query = [
            'section' => '',
        ];
        if ( isset( $_SERVER['QUERY_STRING'] ) ) {
            parse_str( $_SERVER['QUERY_STRING'], $query );
        }
        if ( !isset( $query['section'] ) || $this->id != $query['section'] ) {
            return;
        }
        $base_url = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_url;
        $base_path = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path;
        $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        
        if ( !wp_script_is( 'bootstrap', 'queue' ) && !wp_script_is( 'bootstrap', 'done' ) ) {
            wp_dequeue_script( 'bootstrap' );
            wp_deregister_script( 'bootstrap' );
            wp_register_script(
                'bootstrap',
                $base_url . "/js/bootstrap{$min}.js",
                array( 'jquery' ),
                '4.0.0'
            );
        }
        
        
        if ( !wp_script_is( 'jeditable', 'queue' ) && !wp_script_is( 'jeditable', 'done' ) ) {
            wp_dequeue_script( 'jeditable' );
            wp_deregister_script( 'jeditable' );
            wp_register_script(
                'jeditable',
                $base_url . "/js/jquery.jeditable.min.js",
                array( 'jquery' ),
                '2.0.7'
            );
        }
        
        wp_enqueue_script(
            'data-tables',
            $base_url . "/js/jquery.dataTables{$min}.js",
            array( 'jquery', 'jeditable' ),
            '1.10.18'
        );
        // 1. runtime~main
        // 2. vendors
        // 3. main
        $runtimeMain = null;
        $vendors = null;
        $main = null;
        $files = scandir( $base_path . "/js/admin/" );
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
        
        if ( !wp_script_is( 'wooetherc20paymentgateway-admin-runtime-main', 'queue' ) && !wp_script_is( 'wooetherc20paymentgateway-admin-runtime-main', 'done' ) ) {
            wp_dequeue_script( 'wooetherc20paymentgateway-admin-runtime-main' );
            wp_deregister_script( 'wooetherc20paymentgateway-admin-runtime-main' );
            wp_register_script(
                'wooetherc20paymentgateway-admin-runtime-main',
                $base_url . "/js/admin/" . $runtimeMain[0],
                array(),
                $runtimeMain[1]
            );
        }
        
        
        if ( !wp_script_is( 'wooetherc20paymentgateway-admin-vendors', 'queue' ) && !wp_script_is( 'wooetherc20paymentgateway-admin-vendors', 'done' ) ) {
            wp_dequeue_script( 'wooetherc20paymentgateway-admin-vendors' );
            wp_deregister_script( 'wooetherc20paymentgateway-admin-vendors' );
            wp_register_script(
                'wooetherc20paymentgateway-admin-vendors',
                $base_url . "/js/admin/" . $vendors[0],
                array( 'wooetherc20paymentgateway-admin-runtime-main' ),
                $vendors[1]
            );
        }
        
        
        if ( !wp_script_is( 'wooetherc20paymentgateway-admin-main', 'queue' ) && !wp_script_is( 'wooetherc20paymentgateway-admin-main', 'done' ) ) {
            wp_dequeue_script( 'wooetherc20paymentgateway-admin-main' );
            wp_deregister_script( 'wooetherc20paymentgateway-admin-main' );
            wp_register_script(
                'wooetherc20paymentgateway-admin-main',
                $base_url . "/js/admin/" . $main[0],
                array(
                'jquery',
                'bootstrap',
                'wooetherc20paymentgateway-admin-vendors',
                'wp-i18n'
            ),
                $main[1]
            );
        }
        
        wp_enqueue_script( 'wooetherc20paymentgateway-admin-main' );
        if ( function_exists( 'wp_set_script_translations' ) ) {
            wp_set_script_translations( 'wooetherc20paymentgateway-admin-main', 'ether-and-erc20-tokens-woocommerce-payment-gateway', $base_path . 'languages' );
        }
        $rate_sources = $this->get_rate_sources_();
        $web3Endpoint = $this->getWeb3Endpoint();
        $web3WSSEndpoint = $this->getWeb3WSSEndpoint();
        $addressSite = $this->getAddressSite16();
        $base_currency_ticker = $this->get_setting_( 'currency_ticker', 'ETH' );
        $base_currency_ticker_name = $this->get_setting_( 'currency_ticker_name', 'Ether' );
        $token_standard_name = $this->get_setting_( 'token_standard_name', 'ERC20' );
        $blockchain_display_name = $this->get_setting_( 'blockchain_display_name', 'Ethereum' );
        $this->log( "tokens_supported(" . $this->id . ") -> " . $this->get_setting_( 'tokens_supported' ) );
        wp_localize_script( 'wooetherc20paymentgateway-admin-main', 'epg', [
            'base_currency'                                       => esc_html( get_woocommerce_currency() ),
            'tokens_supported'                                    => esc_html( $this->get_setting_( 'tokens_supported' ) ),
            'decimals_ether'                                      => esc_html( $this->get_setting_( 'decimals_ether', '5' ) ),
            'disable_ether'                                       => esc_html( $this->get_setting_( 'disable_ether', 'no' ) ),
            'rate_sources'                                        => esc_html( implode( ',', $rate_sources ) ),
            'localePath'                                          => esc_html( $base_url . "/i18n/" . get_locale() . ".json" ),
            'gas_limit'                                           => esc_html( $this->get_setting_( 'gas_limit', '200000' ) ),
            'gas_price'                                           => esc_html( floatval( $this->get_setting_( 'gas_price', '5' ) ) ),
            'payment_address'                                     => esc_html( $this->get_setting_( 'payment_address' ) ),
            'addressSite'                                         => $addressSite,
            'gateway_address'                                     => $this->getGatewayContractAddress(),
            'gateway_abi'                                         => $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->gatewayContractABI,
            'erc20_abi'                                           => $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->erc20ContractABI,
            'erc223_abi'                                          => $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->erc223ContractABI,
            'erc777_abi'                                          => $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->erc777ContractABI,
            'order_txhash'                                        => '',
            'web3HTTPSEndpoint'                                   => esc_html( $web3Endpoint ),
            'web3Endpoint'                                        => esc_html( $web3WSSEndpoint ),
            'blockchain_network'                                  => esc_html( $this->getBlockchainNetwork() ),
            'base_currency_ticker'                                => esc_html( $base_currency_ticker ),
            'user_wallet_address'                                 => esc_html( ( function_exists( 'ETHEREUM_WALLET_web3_signTransaction_endpoint' ) ? ETHEREUM_WALLET_get_wallet_address() : "" ) ),
            'baseURL'                                             => $base_url,
            'confirmations_number'                                => $this->get_setting_( 'confirmations_number', '12' ),
            'view_transaction_url'                                => $this->get_setting_( 'view_transaction_url', '' ),
            'wp_rest_nonce'                                       => wp_create_nonce( 'wp_rest' ),
            'wp_rest_url'                                         => esc_attr( get_rest_url() ),
            'str_withdraw'                                        => __( 'Withdraw', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_failed'                                  => __( 'Payment failed. Try to adjust gas setting', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_trust_wallet_title'                              => __( 'Trust Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_trust_wallet_desc'                               => __( 'Trust Wallet is a Secure Multi Coin Wallet for Android and iOS. It is the official cryptocurrency wallet of Binance.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_metamask_title'                                  => __( 'MetaMask', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_metamask_desc'                                   => sprintf( __( 'MetaMask is a bridge that allows you to visit the distributed web of tomorrow in your browser today. It allows you to run %1$s dApps right in your browser without running a full %1$s node.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_ethereum_wallet_title'                           => sprintf( __( '%1$s Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_ethereum_wallet_desc'                            => sprintf( __( 'Use %1$s Wallet generated for you on this site upon registration', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_inconsistent_wallet'                             => sprintf( __( 'The wallet connected should be the same as the "%1$s" setting value.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), sprintf( __( 'Your %1$s address', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), strtolower( $blockchain_display_name ) ) ),
            'str_tokens_table_column_symbol_title'                => __( 'Symbol', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tokens_table_column_address_title'               => __( 'Address', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tokens_table_column_rate_source_title'           => __( 'Rate Source', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tokens_table_column_rate_title'                  => __( 'Rate', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tokens_table_column_currency_title'              => __( 'Currency', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tokens_table_column_markup_title'                => __( 'Markup, %', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tokens_table_column_decimals_title'              => __( 'Decimals', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tokens_table_column_remove_title'                => __( 'Remove', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tokens_table_button_remove_title'                => __( 'Remove', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tokens_table_button_add_token_title'             => __( 'Add Token', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tokens_table_remove_confirm_msg'                 => __( 'Do you really want to remove this token?', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tokens_table_rate_source_fixed_label'            => __( 'Fixed', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_gateway_title_text'                              => $this->get_setting_( 'title', sprintf( __( 'Pay with %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ) ),
            'str_purchase_tokens_button_text'                     => __( 'Purchase %1$s tokens', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_purchase_ether_button_text'                      => sprintf( __( 'Purchase %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_make_deposit_button_text'                        => __( 'Deposit with MetaMask', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_make_deposit_with_ethereum_wallet_button_text'   => sprintf( __( 'Deposit with %1$s Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_pay_button_text'                                 => __( 'Pay with MetaMask', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_download_metamask'                               => __( 'Download MetaMask', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_download_metamask_mobile'                        => __( 'Open / Install MetaMask Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_download_trust'                                  => __( 'Open / Install Trust Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_unlock_metamask_account'                         => __( 'Unlock your account please.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_pay_with_ethereum_wallet'                        => sprintf( __( 'Pay with %1$s Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_pay_eth_failure'                                 => sprintf( __( 'Failed to pay %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_pay_eth_success'                                 => sprintf( __( 'Pay %1$s succeeded', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_deposit_eth_failure'                             => sprintf( __( 'Failed to deposit %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_deposit_eth_success'                             => sprintf( __( 'Deposit %1$s succeeded', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_pay_token_failure'                               => sprintf( __( 'Failed to pay %1$s token', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_pay_token_failure_insufficient_balance'          => sprintf( __( 'Failed to pay %1$s token: insufficient balance', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_pay_token_success'                               => sprintf( __( 'Pay %1$s token succeeded', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_pay_eth_rejected'                                => sprintf( __( 'Failed to pay %1$s - rejected', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_pay_token_rejected'                              => sprintf( __( 'Failed to pay %1$s token - rejected', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_deposit_token_failure'                           => sprintf( __( 'Failed to deposit %1$s token', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_deposit_token_failure_insufficient_balance'      => sprintf( __( 'Failed to deposit %1$s token: insufficient balance', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_deposit_token_success'                           => sprintf( __( 'Deposit %1$s token succeeded', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_deposit_eth_rejected'                            => sprintf( __( 'Failed to deposit %1$s - rejected', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_deposit_token_rejected'                          => sprintf( __( 'Failed to deposit %1$s token - rejected', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_copied_msg'                                      => __( 'Copied to clipboard', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_mm_site_connect_failed'                          => sprintf( __( 'You have rejected the store to %1$s Wallet connect request', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_payment_complete'                                => __( 'Payment succeeded!', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_complete_no_metamask'                    => __( 'Payment succeeded! Reload page if it was not auto reloaded.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_incomplete'                              => __( 'Payment status: not complete.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_metamask_network_mismatch'                       => __( 'Network mismatch. Choose another network or ask site administrator.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_metamask_network_mismatch_detailed'              => __( 'Network mismatch. Chosen network is %1$s, but this site is configured for %2$s. Choose another network or ask site administrator', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_dlg_content'                             => sprintf( __( 'To use the QR-code feature input your %1$s account address please', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_account_dlg_address_field_label'                 => __( 'Address', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_dlg_qrcode_button_label'                 => __( 'QR-code', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_dlg_title'                               => __( 'Enter account address', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_dlg_ok_button_label'                     => __( 'OK', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_dlg_cancel_button_label'                 => __( 'Cancel', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_label'                                   => __( 'Account', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_pay_button_label'                                => __( 'Pay', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_deposit_button_label'                            => __( 'Deposit', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_alert_dlg_title'                                 => __( 'Error', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_alert_dlg_title_default'                         => __( 'Alert', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_alert_dlg_ok_button_label'                       => __( 'OK', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_step_unknown'                            => __( 'N/A', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_dlg_content'                             => sprintf( __( '%1$s Payment', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name ),
            'str_payment_step_token_deposit'                      => __( 'Token Deposit', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_step_token_payment'                      => __( 'Token Payment', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_copied_to_clipboard'                             => __( 'Copied to clipboard', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_title'                            => __( 'Advanced', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_desc'                             => __( 'Open to see advanced controls', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_step_label'                              => __( 'Payment Step', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_address_label'                    => __( 'Address', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_copy_to_clipboard'                               => __( 'Copy to clipboard', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_qrcode_button_label'              => __( 'QR-code', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_value_label'                      => __( 'Value', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_data_label'                       => __( 'Data', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_qrcode_title'                                    => __( 'QR-code', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_qrcode_desc'                                     => __( 'QR-code is a read-only mode. You can copy values or scan QR-codes for the To address, Value and Data fields to make a payment with your favorite mobile wallet.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_success_label'                           => __( 'Payment succeeded! Tx %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_currency_selector_balance_label'         => __( 'Balance', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_currency_selector_balance_value_default' => __( 'N/A', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_currency_selector_currency_label'        => __( 'Currency', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_currency_selector_currency_helper_text'  => __( 'Choose currency to pay with', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_currency_selector_amount_label'          => __( 'Amount', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_method_card_account_label'               => __( 'Account', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_method_card_connect_button_label'        => __( 'Connect', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_method_card_disconnect_button_label'     => __( 'Disconnect', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_qrcode_dlg_title'                                => __( 'Scan QR-code', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tx_progress_dlg_title'                           => __( 'Confirmations', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tx_progress_dlg_content'                         => __( 'Tx confirmations %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
        ] );
        
        if ( !wp_style_is( 'font-awesome', 'queue' ) && !wp_style_is( 'font-awesome', 'done' ) ) {
            wp_dequeue_style( 'font-awesome' );
            wp_deregister_style( 'font-awesome' );
            wp_register_style(
                'font-awesome',
                $base_url . "/css/font-awesome{$min}.css",
                array(),
                '4.7.0'
            );
        }
        
        wp_enqueue_style(
            'bootstrap-ether-and-erc20-tokens-woocommerce-payment-gateway',
            $base_url . "/css/bootstrap-ns{$min}.css",
            array( 'font-awesome' ),
            '4.0.0'
        );
        wp_enqueue_style(
            'data-tables',
            $base_url . "/css/jquery.dataTables{$min}.css",
            array(),
            '1.10.18'
        );
    }
    
    public function handling_custom_meta_query_keys( $wp_query_args, $query_vars, $data_store_cpt )
    {
        $meta_key = '_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type';
        // The custom meta_key
        if ( !empty($query_vars[$meta_key]) ) {
            $wp_query_args['meta_query'][] = array(
                'key'   => $meta_key,
                'value' => esc_attr( $query_vars[$meta_key] ),
            );
        }
        return $wp_query_args;
    }
    
    public function get_setting_( $setting, $default = '' )
    {
        return ( isset( $this->settings[$setting] ) && !empty($this->settings[$setting]) ? $this->settings[$setting] : $default );
    }
    
    public function order_on_hold_handler( $order_id )
    {
        if ( !$this->check_gateway_class( $order_id ) ) {
            return;
        }
        $this->enqueue_complete_order_task( $order_id );
    }
    
    public function order_cancelled_handler( $order_id )
    {
        if ( !$this->check_gateway_class( $order_id ) ) {
            return;
        }
        $this->cancel_complete_order_task( $order_id );
    }
    
    public function before_delete_post_handler( $order_id )
    {
        global  $post_type ;
        if ( $post_type !== 'shop_order' ) {
            return;
        }
        if ( !$this->check_gateway_class( $order_id ) ) {
            return;
        }
        $this->cancel_complete_order_task( $order_id );
    }
    
    public function enqueue_complete_order_task( $order_id, $offset = 0 )
    {
        $this->cancel_complete_order_task( $order_id );
        $timestamp = time() + $offset;
        $hook = 'ether_and_erc20_tokens_woocommerce_payment_gateway_complete_order';
        $args = array( $order_id );
        $base_path = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path;
        // @see https://github.com/woocommerce/action-scheduler/issues/730
        
        if ( !class_exists( 'ActionScheduler', false ) || !\ActionScheduler::is_initialized() ) {
            require_once $base_path . '/vendor/woocommerce/action-scheduler/classes/abstracts/ActionScheduler.php';
            \ActionScheduler::init( $base_path . '/vendor/woocommerce/action-scheduler/action-scheduler.php' );
        }
        
        $task_id = as_schedule_single_action( $timestamp, $hook, $args );
        $this->log( "Task complete_order with id {$task_id} scheduled for order: {$order_id}" );
    }
    
    public function cancel_complete_order_task( $order_id )
    {
        $hook = "ether_and_erc20_tokens_woocommerce_payment_gateway_complete_order";
        $args = array( $order_id );
        $base_path = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path;
        // @see https://github.com/woocommerce/action-scheduler/issues/730
        
        if ( !class_exists( 'ActionScheduler', false ) || !\ActionScheduler::is_initialized() ) {
            require_once $base_path . '/vendor/woocommerce/action-scheduler/classes/abstracts/ActionScheduler.php';
            \ActionScheduler::init( $base_path . '/vendor/woocommerce/action-scheduler/action-scheduler.php' );
        }
        
        as_unschedule_action( $hook, $args );
        $this->log( "Task complete_order unscheduled for order: {$order_id}" );
    }
    
    public function complete_order( $order_id )
    {
        $this->log( "complete_order(order_id: {$order_id})" );
        $tokens_supported = esc_attr( $this->get_setting_( 'tokens_supported' ) );
        $providerUrl = $this->getWeb3Endpoint();
        $marketAddress = $this->getMarketAddress();
        try {
            $paymentSuccess = $this->check_tx_status(
                $order_id,
                $tokens_supported,
                $this->getOrderExpiredTimeout(),
                $providerUrl,
                $marketAddress,
                true
            );
        } catch ( \Exception $ex ) {
            $this->log( "complete_order: " . $ex->getMessage() );
        }
        if ( !$paymentSuccess ) {
            // re-enqueue itself to check later
            $this->enqueue_complete_order_task( $order_id, 1 * 60 );
        }
        return $paymentSuccess;
    }
    
    public function complete_order_internal( $order_id )
    {
        $tokens_supported = esc_attr( $this->get_setting_( 'tokens_supported' ) );
        $providerUrl = $this->getWeb3Endpoint();
        $marketAddress = $this->getMarketAddress();
        $paymentSuccess = false;
        try {
            $paymentSuccess = $this->check_tx_status(
                $order_id,
                $tokens_supported,
                $this->getOrderExpiredTimeout(),
                $providerUrl,
                $marketAddress,
                true
            );
        } catch ( \Exception $ex ) {
            $this->log( "complete_order: " . $ex->getMessage() );
        }
        if ( $paymentSuccess ) {
            $this->cancel_complete_order_task( $order_id );
        }
        return $paymentSuccess;
    }
    
    //    /**
    //     * Output the logo.
    //     *
    //     * @param  string $icon    The default WC-generated icon.
    //     * @param  string $gateway The gateway the icons are for.
    //     *
    //     * @return string          The HTML for the selected iconsm or empty string if none
    //     */
    //    public function show_icons($icon, $gateway) {
    //        if ($this->id !== $gateway) {
    //            return $icon;
    //        }
    //        $img_url = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_url . '/img/ethereum-icon.png';
    //        return '<img src="' . esc_attr($img_url) . '" width="25" height="25">';
    //    }
    protected function get_token2wcproduct()
    {
        $token2wcproduct = [];
        $products = wc_get_products( array(
            '_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' => 'yes',
        ) );
        foreach ( $products as $product ) {
            $product_id = $product->get_id();
            $tokenAddress = get_post_meta( $product_id, '_text_input_cryptocurrency_data', true );
            
            if ( !empty($tokenAddress) ) {
                $token2wcproduct[strtolower( $tokenAddress )] = $product;
            } else {
                //_select_cryptocurrency_option
                $_select_cryptocurrency_option = get_post_meta( $product_id, '_select_cryptocurrency_option', true );
                if ( !empty($_select_cryptocurrency_option) && 'Ether' == $_select_cryptocurrency_option ) {
                    $token2wcproduct['0x0000000000000000000000000000000000000001'] = $product;
                }
            }
        
        }
        return $token2wcproduct;
    }
    
    protected function get_ether_icon()
    {
        $img_url = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_url . '/img/ethereum-icon.png';
        $icon_html = '<img src="' . esc_attr( $img_url ) . '" width="25" height="25">';
        return $icon_html;
    }
    
    protected function get_token_icon( $tokenAddress, $token2wcproduct )
    {
        $icon_html = '';
        
        if ( isset( $token2wcproduct[$tokenAddress] ) ) {
            $product = $token2wcproduct[$tokenAddress];
            $image_id = $product->get_image_id();
            
            if ( $image_id ) {
                $image_ops = wp_get_attachment_image_src( $image_id, 'thumbnail', false );
                $img_url = $image_ops[0];
                $icon_html = '<img src="' . esc_attr( $img_url ) . '" width="25" height="25">';
            }
        
        }
        
        return $icon_html;
    }
    
    /**
     * Get gateway icon.
     * @return string
     */
    public function get_icon()
    {
        $icon_html = '';
        $tokens_supported = esc_attr( $this->get_setting_( 'tokens_supported' ) );
        
        if ( empty(trim( $tokens_supported )) ) {
            $icon_html .= $this->get_ether_icon();
        } else {
            $disable_ether = $this->get_setting_( 'disable_ether', 'no' );
            if ( 'yes' != $disable_ether ) {
                $icon_html .= $this->get_ether_icon();
            }
            $token2wcproduct = $this->get_token2wcproduct();
            $tokensArr = explode( ",", $tokens_supported );
            if ( $tokensArr ) {
                foreach ( $tokensArr as $token ) {
                    $tokenPartsArr = explode( ":", $token );
                    
                    if ( count( $tokenPartsArr ) >= 3 ) {
                        $tokenAddress = strtolower( $tokenPartsArr[1] );
                        $icon_html .= $this->get_token_icon( $tokenAddress, $token2wcproduct );
                    }
                
                }
            }
        }
        
        return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
    }
    
    /**
     * Tell the user how much their order will cost if they pay by ETH.
     */
    public function payment_fields()
    {
        echo  '<p class="epg-eth-pricing-note"><strong>' ;
        $tokens_supported = esc_attr( $this->get_setting_( 'tokens_supported' ) );
        $base_currency_ticker = $this->get_setting_( 'currency_ticker', 'ETH' );
        $token_standard_name = $this->get_setting_( 'token_standard_name', 'ERC20' );
        
        if ( empty(trim( $tokens_supported )) ) {
            echo  sprintf( __( 'Payment in %1$s will be due.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ) ;
        } else {
            $tokensCount = 1;
            $tokensArr = explode( ",", $tokens_supported );
            if ( $tokensArr ) {
                $tokensCount = count( $tokensArr );
            }
            $disable_ether = $this->get_setting_( 'disable_ether', 'no' );
            
            if ( 'yes' != $disable_ether ) {
                
                if ( $tokensCount > 1 ) {
                    echo  sprintf( __( 'Payment in %1$s or an equivalent in supported %2$s tokens will be due.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker, $token_standard_name ) ;
                } else {
                    $tokenPartsArr = explode( ":", $tokensArr[0] );
                    
                    if ( count( $tokenPartsArr ) >= 3 ) {
                        $symbol = $tokenPartsArr[0];
                        echo  sprintf( __( 'Payment in %1$s or an equivalent in %2$s tokens will be due.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker, $symbol ) ;
                    } else {
                        echo  sprintf( __( 'Payment in %1$s or an equivalent in supported %2$s tokens will be due.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker, $token_standard_name ) ;
                    }
                
                }
            
            } else {
                
                if ( $tokensCount > 1 ) {
                    echo  sprintf( __( 'Payment in a supported %1$s tokens will be due.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ) ;
                } else {
                    $tokenPartsArr = explode( ":", $tokensArr[0] );
                    
                    if ( count( $tokenPartsArr ) >= 3 ) {
                        $symbol = $tokenPartsArr[0];
                        printf( __( 'Payment in %s tokens will be due.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $symbol );
                    } else {
                        echo  sprintf( __( 'Payment in a supported %1$s tokens will be due.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ) ;
                    }
                
                }
            
            }
        
        }
        
        echo  '</strong></p>' ;
    }
    
    /**
     * Checks that not too much time has passed since we quoted them a price.
     */
    public function validate_fields()
    {
        //        $price_info = WC()->session->get('epg_calculated_value');
        //        // Prices quoted at checkout must be re-calculated if more than 15
        //        // minutes have passed.
        //        $validity_period = apply_filters('epg_checkout_validity_time', 900);
        //        if (!is_array($price_info) || ($price_info['timestamp'] + $validity_period < time())) {
        //            wc_add_notice(__('ETH price quote has been updated, please check and confirm before proceeding.', 'ether-and-erc20-tokens-woocommerce-payment-gateway'), 'error');
        //            return false;
        //        }
        return true;
    }
    
    /**
     * Mark up a price by the configured amount.
     *
     * @param  float $price  The price to be marked up.
     *
     * @return float         The marked up price.
     */
    protected function apply_markup( $price )
    {
        $markup_percent = doubleval( $this->get_setting_( 'markup_percent', '0' ) );
        $multiplier = $markup_percent / 100 + 1;
        return round( $price * $multiplier, 15, PHP_ROUND_HALF_UP );
    }
    
    /**
     * Mark up a price by the configured amount.
     *
     * @param  float $price  The price to be marked up.
     *
     * @return float         The marked up price.
     */
    protected function apply_markup_token( $price )
    {
        $markup_percent_token = doubleval( $this->get_setting_( 'markup_percent_token', '0' ) );
        $multiplier = $markup_percent_token / 100 + 1;
        $markup_percent = doubleval( $this->get_setting_( 'markup_percent', '0' ) );
        $divider = $markup_percent / 100 + 1;
        return round( doubleval( $price ) * $multiplier / $divider, 15, PHP_ROUND_HALF_UP );
    }
    
    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields()
    {
        //        $base_currency_ticker = $this->get_setting_('currency_ticker', 'ETH');
        $base_currency_ticker_name = $this->get_setting_( 'currency_ticker_name', 'Ether' );
        $token_standard_name = $this->get_setting_( 'token_standard_name', 'ERC20' );
        $blockchain_display_name = $this->get_setting_( 'blockchain_display_name', 'Ethereum' );
        $this->form_fields = array(
            'enabled' => array(
            'title'       => __( 'Enable / disable', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'label'       => sprintf( __( 'Enable payment with %1$s or %2$s tokens', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name, $token_standard_name ),
            'type'        => 'checkbox',
            'description' => '',
            'default'     => 'no',
        ),
        );
        $tokens_supported = esc_attr( $this->get_setting_( 'tokens_supported' ) );
        
        if ( empty(trim( $tokens_supported )) ) {
            $title = sprintf( __( 'Pay with %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name );
        } else {
            $title = sprintf( __( 'Pay with %1$s or %2$s token', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name, $token_standard_name );
        }
        
        $this->form_fields += array(
            'basic_settings' => array(
            'title'       => __( 'Basic settings', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'title',
            'description' => '',
        ),
            'debug'          => array(
            'title'       => __( 'Enable debug mode', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'label'       => __( 'Enable only if you are diagnosing problems.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'checkbox',
            'description' => sprintf( __( 'Log interactions inside <code>%s</code>', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), wc_get_log_file_path( $this->id ) ),
            'default'     => 'no',
        ),
            'title'          => array(
            'title'       => __( 'Title', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'text',
            'description' => __( 'This controls the name of the payment option that the user sees during checkout.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'default'     => $title,
        ),
        );
        $this->form_fields += array(
            'payment_details'      => array(
            'title'       => __( 'Payment details', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'title',
            'description' => '',
        ),
            'payment_address'      => array(
            'title'       => sprintf( __( 'Your %1$s address', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), strtolower( $blockchain_display_name ) ),
            'type'        => 'text',
            'description' => sprintf( __( 'The %2$s address payment should be sent to. Make sure to use one address per one online store. Do not use one address for two or more stores! Also, make sure to use a %1$s tokens compatible wallet if you are planning to accept it!', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name, strtolower( $blockchain_display_name ) ),
            'default'     => '',
        ),
            'tokens_supported'     => array(
            'title'       => sprintf( __( 'Supported %1$s tokens list', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'type'        => 'textarea',
            'description' => __( 'Provide a list of tokens you want to support.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'default'     => '',
        ),
            'disable_ether'        => array(
            'title'       => sprintf( __( 'Disable %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name ),
            'label'       => sprintf( __( 'Disallow customer to pay with %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name ),
            'type'        => 'checkbox',
            'description' => __( 'This option is useful to accept only some token. It is an advanced option. Use with care.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'default'     => 'no',
        ),
            'decimals_ether'       => array(
            'title'             => sprintf( __( '%1$s decimals', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name ),
            'description'       => sprintf( __( 'Number of digits after a decimal point for %1$s display. 5 is used by default.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name ),
            'default'           => '5',
            'type'              => 'number',
            'css'               => 'width:100px;',
            'custom_attributes' => array(
            'min'  => 0,
            'max'  => 18,
            'step' => 1,
        ),
        ),
            'markup_percent'       => array(
            'title'             => sprintf( __( 'Mark %1$s price up by %%', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name ),
            'description'       => __( 'To help cover currency fluctuations the plugin can automatically mark up converted rates for you. These are applied as percentage markup, so a 1ETH value with a 1.00% markup will be presented to the customer as 1.01ETH.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'default'           => '0.0',
            'type'              => 'number',
            'css'               => 'width:100px;',
            'custom_attributes' => array(
            'min'  => -100,
            'max'  => 100,
            'step' => 0.1,
        ),
        ),
            'markup_percent_token' => array(
            'title'             => sprintf( __( 'Mark %1$s token price up by %%', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'description'       => sprintf( __( 'To help cover currency fluctuations the plugin can automatically mark up converted rates for you. These are applied as percentage markup, so a 1 %1$s Token value with a 1.00%% markup will be presented to the customer as 1.01 Token.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'default'           => '0.0',
            'type'              => 'number',
            'css'               => 'width:100px;',
            'custom_attributes' => array(
            'min'  => -100,
            'max'  => 100,
            'step' => 0.1,
        ),
        ),
        );
        $this->form_fields += array(
            'blockchain_settings'  => array(
            'title'       => __( 'Blockchain settings', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'title',
            'description' => '',
        ),
            'blockchain_network'   => array(
            'title'       => __( 'Blockchain', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'text',
            'description' => __( '<p>The blockchain used: mainnet, ropsten, rinkeby, bsctest, bsc. Use mainnet, polygon or bsc in production, and ropsten, rinkeby, mumbai or bsctest in test mode.</p>', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'default'     => 'mainnet',
        ),
            'gas_limit'            => array(
            'title'       => __( 'Gas limit', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'number',
            'description' => __( 'The gas limit for transaction.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'default'     => '200000',
        ),
            'gas_price'            => array(
            'title'       => __( 'Minimum gas price, Gwei', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'number',
            'description' => __( 'The minimum gas price to set for transaction. Can help for BSC and other similar chains if underpriced transaction error occur. Mostly affects the Ethereum Wallet plugin payments.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'default'     => '5',
        ),
            'confirmations_number' => array(
            'title'       => __( 'Confirmations number', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'number',
            'description' => sprintf( __( 'Number of blocks to wait before confirm order as payed successfully. The recommended safe value is 12. There are no reason to set it to a higher value for %1$s blockchain. You can set it to a lower value like 2 to increase usability.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'default'     => '2',
        ),
        );
        $strBIZAd = '';
        if ( !ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init()->is_plan( 'biz', true ) ) {
            $strBIZAd = '<p>' . sprintf( __( '%1$sUpgrade to the BIZ plan%2$s to support your private or any other EVM-compatible blockchain!', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), '<a href="' . ether_and_erc20_tokens_woocommerce_payment_gateway_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) . '</p>';
        }
        $this->form_fields += array(
            'advanced_blockchain_settings' => array(
            'title'       => __( 'Advanced Blockchain settings', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'title',
            'description' => '<p>' . __( 'These settings should be considered as advanced. Use with care!', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) . '</p>' . $strBIZAd,
        ),
            'web3Endpoint'                 => array(
            'title'       => sprintf( __( '%1$s Node JSON-RPC Endpoint', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'type'        => 'text',
            'title'       => sprintf( __( '%1$s Node JSON-RPC Endpoint', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'description' => '<p>' . sprintf( __( 'The %2$s Node JSON-RPC Endpoint URI. This is an advanced setting. Use with care. This setting supercedes the "%1$s" setting.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), __( "Infura.io API Key", 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ) . '</p>' . $strBIZAd,
            'default'     => '',
        ),
            'web3WSSEndpoint'              => array(
            'title'       => sprintf( __( '%1$s Node Websocket Endpoint', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'type'        => 'text',
            'description' => '<p>' . sprintf(
            __( 'The %3$s Node Websocket Endpoint URI. This is an advanced setting. Use with care. This setting supercedes the "%1$s" setting. MUST be set if the "%2$s" setting is set', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            __( "Infura.io API Key", 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            sprintf( __( '%1$s Node JSON-RPC Endpoint', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            $blockchain_display_name
        ) . '</p>' . $strBIZAd,
            'default'     => '',
        ),
            'view_transaction_url'         => array(
            'title'       => __( 'Transaction explorer URL', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'text',
            'description' => '<p>' . sprintf( __( 'The %2$s transaction explorer URL template. It should contain %%s pattern to insert tx hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), sprintf( __( '%1$s Node JSON-RPC Endpoint', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ), $blockchain_display_name ) . '</p>' . $strBIZAd,
            'default'     => '',
        ),
            'currency_ticker'              => array(
            'title'       => __( 'Base crypto currency', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'text',
            'description' => '<p>' . sprintf( __( 'The base crypto currency ticker for the blockchain configured, like ETH for Ethereum or BNB for Binance Smart Chain. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), sprintf( __( '%1$s Node JSON-RPC Endpoint', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ) ) . '</p>' . $strBIZAd,
            'default'     => 'ETH',
        ),
            'currency_ticker_name'         => array(
            'title'       => __( 'Base crypto currency name', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'text',
            'description' => '<p>' . sprintf( __( 'The base crypto currency ticker name for the blockchain configured, like Ether for Ethereum or BNB for Binance Smart Chain. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), sprintf( __( '%1$s Node JSON-RPC Endpoint', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ) ) . '</p>' . $strBIZAd,
            'default'     => 'Ether',
        ),
            'token_standard_name'          => array(
            'title'       => __( 'Token standard name', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'text',
            'description' => '<p>' . sprintf( __( 'The crypto currency token standard name for the blockchain configured, like ERC20 for Ethereum or BEP20 for Binance Smart Chain. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), sprintf( __( '%1$s Node JSON-RPC Endpoint', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ) ) . '</p>' . $strBIZAd,
            'default'     => 'ERC20',
        ),
            'blockchain_display_name'      => array(
            'title'       => __( 'Blockchain display name', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'text',
            'description' => '<p>' . sprintf( __( 'The display name for the blockchain configured, like Ethereum or Binance Smart Chain. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), sprintf( __( '%1$s Node JSON-RPC Endpoint', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ) ) . '</p>' . $strBIZAd,
            'default'     => 'Ethereum',
        ),
        );
        $this->form_fields += array(
            'api_credentials'       => array(
            'title'       => __( 'API credentials', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'title',
            'description' => '',
        ),
            'cryptocompare_api_key' => array(
            'title'       => __( 'Cryptocompare API Key', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'text',
            'description' => sprintf( __( '<p>The API key for the %1$s. You need to register on this site to obtain it.</p>', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), sprintf( '<a target="_blank" href="%1$s">%2$s</a>', 'https://min-api.cryptocompare.com', 'cryptocompare.com' ) ),
            'default'     => '',
        ),
            'infura_api_key'        => array(
            'title'       => __( 'Infura.io API Key', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'text',
            'description' => '<p>' . sprintf( __( 'The API key for the %1$s. You need to register on this site to obtain it. Use this guide please: %2$s.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), '<a target="_blank" href="https://infura.io/register">https://infura.io/</a>', '<a target="_blank" href="https://ethereumico.io/knowledge-base/infura-api-key-guide/">Get infura API Key</a>' ) . '</p><p>' . sprintf( __( 'Note that this setting is ignored if the "%1$s" setting is set', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), sprintf( __( '%1$s Node JSON-RPC Endpoint', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ) ) . '</p>',
            'default'     => '',
        ),
            'coinmarketcap_api_key' => array(
            'title'       => __( 'Coinmarketcap.com API Key', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'text',
            'description' => sprintf( __( '<p>The API key for the %1$s. You need to register on this site to obtain it.</p>', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), sprintf( '<a target="_blank" href="%1$s">%2$s</a>', 'https://pro.coinmarketcap.com/account', 'coinmarketcap.com' ) ),
            'default'     => '',
        ),
        );
        $this->form_fields += array(
            'advanced_settings'             => array(
            'title'       => __( 'Advanced settings', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'title',
            'description' => '',
        ),
            'custom_currency'               => array(
            'title'       => __( 'Yes / No', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'label'       => __( 'Custom WooCommerce currency', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'checkbox',
            'description' => __( 'Check if custom WooCommerce currency is used in this store', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'default'     => 'no',
        ),
            'payment_complete_order_status' => array(
            'title'       => __( 'Order Status', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'label'       => __( 'Payment Complete Order Status', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'select',
            'description' => __( 'The status to apply for order after payment is complete', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'options'     => [
            '' => '',
        ] + wc_get_order_statuses(),
            'default'     => 'no',
        ),
            'order_expire_timeout'          => array(
            'title'       => __( 'Order expire timeout, seconds', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'number',
            'description' => __( 'The number of seconds to wait for a payment confirmation before expiring the order. 24 hours is the default.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'default'     => '' . 24 * 3600,
        ),
        );
        //        $this->form_fields += array(
        //            'earnings' => array(
        //                'title' => __('Earnings', 'ether-and-erc20-tokens-woocommerce-payment-gateway'),
        //                'type' => 'title',
        //                'description' => '',
        //            ),
        //            'seller_earnings' => array(
        //                'title' => '',
        //                'type' => 'text',
        //                'default' => '',
        //                'css' => 'display:none',
        //                'description' => '<div id="woocommerce_' . $this->id . '_seller_earnings_container"></div>',
        //            ),
        //        );
        //
        $this->form_fields += array(
            'ads1' => array(
            'title'       => __( 'Need help to configure this plugin?', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'type'        => 'title',
            'description' => sprintf( __( 'Feel free to %1$shire me!%2$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), '<a target="_blank" href="https://www.upwork.com/freelancers/~0134e80b874bd1fa5f">', '</a>' ),
        ),
        );
        $this->form_fields += array(
            'ads2' => array(
            'title'       => sprintf( __( 'Need help to develop a %1$s token for your ICO?', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'type'        => 'title',
            'description' => sprintf( __( 'Feel free to %1$shire me!%2$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), '<a target="_blank" href="https://ethereumico.io/product/crowdsale-contract-development/">', '</a>' ),
        ),
        );
        $this->form_fields += array(
            'ads3' => array(
            'title'       => sprintf( __( 'Want to sell your %1$s ICO token from your ICO site?', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'type'        => 'title',
            'description' => sprintf( __( 'Install the %1$sThe EthereumICO Wordpress plugin%2$s!', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), '<a target="_blank" href="https://ethereumico.io/product/ethereum-ico-wordpress-plugin/">', '</a>' ),
        ),
        );
        $this->form_fields += array(
            'ads4' => array(
            'title'       => sprintf( __( 'Want to sell %1$s token for fiat and/or Bitcoin?', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'type'        => 'title',
            'description' => sprintf( __( 'Install the %1$sCryptocurrency Product for WooCommerce plugin%2$s!', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), '<a target="_blank" href="https://ethereumico.io/product/cryptocurrency-wordpress-plugin/">', '</a>' ),
        ),
        );
        $this->form_fields += array(
            'ads5' => array(
            'title'       => sprintf( __( 'Want to create %1$s wallets on your Wordpress site?', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'type'        => 'title',
            'description' => sprintf( __( 'Install the %1$sWordPress Ethereum Wallet plugin%2$s!', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), '<a target="_blank" href="https://ethereumico.io/product/wordpress-ethereum-wallet-plugin/">', '</a>' ),
        ),
        );
    }
    
    /**
     * Do not allow enabling of the gateway without providing a payment address.
     */
    public function validate_enabled_field( $key, $value )
    {
        $post_data = $this->get_post_data();
        if ( $value ) {
            
            if ( empty($post_data['woocommerce_' . $this->id . '_payment_address']) ) {
                $blockchain_display_name = $this->get_setting_( 'blockchain_display_name', 'Ethereum' );
                WC_Admin_Settings::add_error( sprintf( __( 'You must provide an %1$s address before enabling the gateway' ), $blockchain_display_name ) );
                return 'no';
            } else {
                return 'yes';
            }
        
        }
        return 'no';
    }
    
    /**
     * Output the gateway settings page.
     */
    public function admin_options()
    {
        $base_currency_ticker_name = $this->get_setting_( 'currency_ticker_name', 'Ether' );
        $token_standard_name = $this->get_setting_( 'token_standard_name', 'ERC20' );
        $tokens_supported = esc_attr( $this->get_setting_( 'tokens_supported' ) );
        
        if ( empty(trim( $tokens_supported )) ) {
            $title0 = sprintf( __( 'Pay with %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name );
        } else {
            $title0 = sprintf( __( 'Pay with %1$s or %2$s token', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name, $token_standard_name );
        }
        
        $title = $this->get_setting_( 'title', $title0 );
        ?>
        <h3><?php 
        echo  $title ;
        ?></h3>
        <p><?php 
        echo  sprintf( __( 'Your customers will be given instructions about where, and how much to pay. Your orders will be marked as on-hold when they are placed. And as complete when payment is recieved. You can update your orders on the <a href="%s">WooCommerce Orders</a> page.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), admin_url( 'edit.php?post_type=shop_order' ) ) ;
        ?></p>
        <table class="form-table">
        <?php 
        $this->generate_settings_html();
        ?>
        </table><!--/.form-table-->
        <?php 
    }
    
    /**
     * See if the site can be connected to the auto-verification service.
     */
    public function verify_api_connection()
    {
        $tokens_supported = esc_attr( $this->get_setting_( 'tokens_supported' ) );
        $tokensArr = explode( ",", $tokens_supported );
        
        if ( $tokensArr ) {
            $base_currency_ticker = $this->get_setting_( 'currency_ticker', 'ETH' );
            $destination = $base_currency_ticker;
            foreach ( $tokensArr as $tokenStr ) {
                $tokenPartsArr = explode( ":", $tokenStr );
                if ( count( $tokenPartsArr ) < 3 ) {
                    continue;
                }
                $tokenSymbol = $tokenPartsArr[0];
                $tokenAddress = $tokenPartsArr[1];
                $source = $tokenAddress;
                $transient_key = 'epg_token_exchange_rate_' . $source . '_' . $destination;
                delete_transient( $transient_key );
                $source = $tokenSymbol;
                $transient_key = 'epg_token_exchange_rate_' . $source . '_' . $destination;
                delete_transient( $transient_key );
            }
        }
    
    }
    
    public function checkout_order_processed_handler( $order_id, $posted_data, $order )
    {
        $total = $order->get_total();
        $this->log( "checkout_order_processed_handler(order_id={$order_id}): total=" . $total );
        $currency = get_woocommerce_currency();
        $eth_rate = null;
        $eth_value = null;
        $custom_currency = $this->get_setting_( 'custom_currency', 'no' );
        
        if ( 'yes' != $custom_currency && $currency != 'MYC' && !(function_exists( 'mycred_point_type_exists' ) && mycred_point_type_exists( $currency )) ) {
            $cryptocompareApiKey = esc_attr( $this->get_setting_( 'cryptocompare_api_key' ) );
            $base_currency_ticker = $this->get_setting_( 'currency_ticker', 'ETH' );
            $convertor = new CurrencyConvertor( $currency, $base_currency_ticker, $cryptocompareApiKey );
            $eth_value = $convertor->convert( $total );
            $eth_value = $this->apply_markup( $eth_value );
            $eth_rate = $convertor->get_exchange_rate();
        }
        
        $stored_info = array(
            'eth_value' => $eth_value,
            'eth_rate'  => $eth_rate,
            'timestamp' => time(),
        );
        // $this->log('epg_calculated_value: ' . print_r($stored_info, true));
        // Set the value in the session so we can log it against the order.
        WC()->session->set( 'epg_calculated_value', $stored_info );
    }
    
    //    /**
    //     * Clear cache
    //     */
    //    public function process_admin_options() {
    //        $tokens_supported = esc_attr($this->get_setting_('tokens_supported'));
    //        $tokensArr = explode(",", $tokens_supported);
    //        if ($tokensArr) {
    //            $destination = 'ETH';
    //            foreach ($tokensArr as $tokenStr) {
    //                $tokenPartsArr = explode(":", $tokenStr);
    //                if (count($tokenPartsArr) < 3) {
    //                    continue;
    //                }
    //                $tokenAddress = $tokenPartsArr[1];
    //                $source = $tokenAddress;
    //
    //                $transient_key = 'epg_token_exchange_rate_' . $source . '_' . $destination;
    //                delete_transient( $transient_key );
    //            }
    //        }
    //    }
    /**
     * Process the payment.
     *
     * @param int $order_id  The order ID to update.
     */
    public function process_payment( $order_id )
    {
        // Load the order.
        $order = wc_get_order( $order_id );
        $this->checkout_order_processed_handler( $order_id, null, $order );
        // Retrieve the ETH value.
        $stored_info = WC()->session->get( 'epg_calculated_value' );
        $base_currency_ticker = $this->get_setting_( 'currency_ticker', 'ETH' );
        // Add order note.
        $order->add_order_note( sprintf( __( 'Order submitted, and payment with %s requested.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ) );
        
        if ( is_array( $stored_info ) ) {
            // Store the ETH amount required against the order.
            $eth_value = $stored_info['eth_value'];
            
            if ( !empty($eth_value) && doubleval( $eth_value ) > 0 ) {
                update_post_meta( $order_id, '_epg_eth_value', $eth_value );
            } else {
                $eth_value = get_post_meta( $order_id, '_epg_eth_value', true );
            }
        
        } else {
            $eth_value = get_post_meta( $order_id, '_epg_eth_value', true );
        }
        
        
        if ( is_array( $stored_info ) ) {
            // Store the ETH amount required against the order.
            $eth_rate = $stored_info['eth_rate'];
            
            if ( !empty($eth_rate) && doubleval( $eth_rate ) > 0 ) {
                update_post_meta( $order_id, 'epg_eth_rate', $eth_rate );
            } else {
                $eth_rate = get_post_meta( $order_id, 'epg_eth_rate', true );
            }
        
        } else {
            $eth_rate = get_post_meta( $order_id, 'epg_eth_rate', true );
        }
        
        $currency = get_woocommerce_currency();
        $custom_currency = $this->get_setting_( 'custom_currency', 'no' );
        
        if ( 'yes' == $custom_currency ) {
            $curr = $currency;
            $order->add_order_note( sprintf( __( 'Order value calculated as %f %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $order->get_total(), $curr ) );
        } else {
            
            if ( $currency != 'MYC' && !(function_exists( 'mycred_point_type_exists' ) && mycred_point_type_exists( $currency )) ) {
                $order->add_order_note( sprintf( __( 'Order value calculated as %f %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $eth_value, $base_currency_ticker ) );
            } else {
                // myCRED Point Based Store
                $curr = mycred_get_point_type_name( MYCRED_DEFAULT_TYPE_KEY, false );
                $order->add_order_note( sprintf( __( 'Order value calculated as %f %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $order->get_total(), $curr ) );
            }
        
        }
        
        // Place the order on hold.
        $order->update_status( 'on-hold', __( 'Awaiting payment.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) );
        // Reduce stock levels.
        
        if ( is_callable( 'wc_reduce_stock_levels' ) ) {
            wc_reduce_stock_levels( $order->get_id() );
        } else {
            $order->reduce_order_stock();
        }
        
        // Remove cart.
        WC()->cart->empty_cart();
        // Redirect the user to the confirmation page.
        
        if ( method_exists( $order, 'get_checkout_order_received_url' ) ) {
            $redirect = $order->get_checkout_order_received_url();
        } else {
            
            if ( is_callable( array( $order, 'get_id' ) ) && is_callable( array( $order, 'get_order_key' ) ) ) {
                $redirect = add_query_arg( 'key', $order->get_order_key(), add_query_arg( 'order', $order->get_id(), get_permalink( get_option( 'woocommerce_thanks_page_id' ) ) ) );
            } else {
                $redirect = add_query_arg( 'key', $order->order_key, add_query_arg( 'order', $order->id, get_permalink( get_option( 'woocommerce_thanks_page_id' ) ) ) );
            }
        
        }
        
        // Return thank you page redirect.
        return array(
            'result'   => 'success',
            'redirect' => $redirect,
        );
    }
    
    public function check_tx_status(
        $order_id,
        $tokens_supported,
        $orderExpiredTimeout,
        $providerUrl,
        $marketAddress,
        $standalone = false
    )
    {
        
        if ( 'Jobster' == wp_get_theme()->name || 'Jobster' == wp_get_theme()->parent_theme ) {
            $eth_value = get_option( 'wpjobster_ether_and_erc20_tokens_payment_gateway_eth_value_' . $order_id, null );
            $eth_rate = get_option( 'wpjobster_ether_and_erc20_tokens_payment_gateway_eth_rate_' . $order_id, null );
            $this->log( "check_tx_status(order_id: {$order_id}, eth_value: {$eth_value}, eth_rate: {$eth_rate})" );
            
            if ( !is_null( $eth_value ) ) {
                // wpjobster transaction
                $payment = wpj_get_payment( array(
                    'payment_type_id' => $order_id,
                    'payment_type'    => 'job_purchase',
                ) );
                
                if ( !$payment ) {
                    if ( $standalone ) {
                        $this->log( sprintf( 'Failed to find order: %s', $order_id ) );
                    }
                    return false;
                }
                
                $this->log( "check_tx_status payment: " . print_r( $payment, true ) );
                
                if ( $payment->payment_status == 'completed' ) {
                    if ( $standalone ) {
                        $this->log( sprintf( __( 'Order do not need payment, tx check stopped: %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $order_id ) );
                    }
                    return true;
                }
                
                $created = $payment->datemade;
                $diff = time() - $created;
                
                if ( $diff > $orderExpiredTimeout ) {
                    
                    if ( $standalone ) {
                        $payment_details = sprintf( __( 'Order expired.  Order updated to failed: %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $order_id );
                        $this->log( $payment_details );
                        $payment_response = '';
                        do_action(
                            "wpjobster_" . $payment->payment_type . "_payment_failed",
                            $order_id,
                            'ether_and_erc20_tokens_payment_gateway',
                            $payment_details,
                            $payment_response
                        );
                    }
                    
                    // stop cron job re-enqueue
                    return true;
                }
                
                $order_txhash = get_option( 'wpjobster_ether_and_erc20_tokens_payment_gateway_txhash_' . $order_id );
                if ( $standalone ) {
                    $this->log( sprintf( 'order_txhash: %s', $order_txhash ) );
                }
                
                if ( !empty($order_txhash) ) {
                    $confirmations_number = $this->get_tx_confirmations_number( $order_txhash, $providerUrl );
                    $confirmations_number_max = intval( $this->get_setting_( 'confirmations_number', '12' ) );
                    
                    if ( $confirmations_number < $confirmations_number_max ) {
                        // no payment found
                        if ( $standalone ) {
                            $this->log( 'Not enough confirmations ' . $confirmations_number . '/' . $confirmations_number_max . ' found for order ' . $order_id . ' with txhash ' . $order_txhash );
                        }
                        return false;
                    }
                
                }
                
                $paymentInfo = $this->getPaymentInfo( $order_id, $providerUrl, $marketAddress );
                
                if ( !$paymentInfo ) {
                    // no payment yet
                    $this->log( sprintf( __( 'No payment found for order: %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $order_id ) );
                    return false;
                }
                
                
                if ( empty($order_txhash) ) {
                    // QR-codes payment was made
                    $block_number = get_option( 'wpjobster_ether_and_erc20_tokens_payment_gateway_ethereum_block_number' . $order_id, '' );
                    
                    if ( empty($block_number) ) {
                        $block_number = $this->get_block_number( $providerUrl );
                        update_option( 'wpjobster_ether_and_erc20_tokens_payment_gateway_ethereum_block_number' . $order_id, sanitize_text_field( $block_number ) );
                        $this->log( 'Payment found at block ' . $block_number );
                    } else {
                        $new_block_number = $this->get_block_number( $providerUrl );
                        $confirmations_number = $new_block_number - intval( $block_number );
                        $confirmations_number_max = intval( $this->get_setting_( 'confirmations_number', '12' ) );
                        
                        if ( $confirmations_number < $confirmations_number_max ) {
                            // no payment found
                            if ( $standalone ) {
                                $this->log( 'Not enough confirmations ' . $confirmations_number . '/' . $confirmations_number_max . ' found for order ' . $order_id . ' with txhash ' . $order_txhash );
                            }
                            return false;
                        }
                    
                    }
                
                }
                
                $paymentSuccess = false;
                $custom_currency = $this->get_setting_( 'custom_currency', 'no' );
                $decimals_eth = 1000000000000000000;
                $eth_value_wei = self::double_int_multiply( $eth_value, $decimals_eth );
                // payment recieved
                foreach ( $paymentInfo as $currencyPayment => $valuePayment ) {
                    $this->log( sprintf(
                        __( 'PaymentInfo recieved for order_id=%s. %s: %s. eth_value=%s, eth_value_wei=%s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
                        $order_id,
                        $currencyPayment,
                        $valuePayment,
                        $eth_value,
                        $eth_value_wei->toString()
                    ) );
                    // ETH is encoded as address 0x0000000000000000000000000000000000000001
                    
                    if ( '0x0000000000000000000000000000000000000001' == $currencyPayment ) {
                        $paymentSuccess = $valuePayment->compare( $eth_value_wei ) >= 0;
                    } else {
                        // $valuePayment is in some ERC20 token
                        $decimals_token = intval( $this->get_token_decimals( $currencyPayment, $providerUrl )->toString() );
                        $rate = $this->getTokenRate( $tokens_supported, $currencyPayment, $eth_rate );
                        // check if token is supported
                        
                        if ( null !== $rate ) {
                            $token_value = $this->apply_markup_token( $eth_value / doubleval( $rate ) );
                            $decimals_token10 = intval( pow( 10, $decimals_token ) );
                            $token_value_wei = self::double_int_multiply( $token_value, $decimals_token10 );
                            // $paymentSuccess = ($value >= $value_eth_token);
                            $paymentSuccess = $valuePayment->compare( $token_value_wei ) >= 0;
                        }
                        
                        if ( !$paymentSuccess ) {
                            $this->log( sprintf(
                                'Payment failure for order_id=%s. tokens: %s. token decimals: %s. rate=%s. token_value=%s. token_value_wei=%s',
                                $order_id,
                                $tokens_supported,
                                $decimals_token,
                                $rate,
                                $token_value,
                                $token_value_wei->toString()
                            ) );
                        }
                    }
                    
                    break;
                }
                
                if ( $paymentSuccess ) {
                    $payment_details = sprintf( __( 'Successful payment notification received. Order updated to pending.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) );
                    $this->log( $payment_details );
                    $notify_page = get_bloginfo( 'url' ) . '/?payment_response=ether_and_erc20_tokens_payment_gateway&oid=' . $order_id . '&wpj_payment_id=' . $payment->id;
                    //                    $notify_page  = 'http://127.0.0.1' . '/?payment_response=ether_and_erc20_tokens_payment_gateway&oid=' . $order_id . '&wpj_payment_id=' . $payment->id;
                    // @see https://wordpress.stackexchange.com/a/329010
                    $url = $notify_page;
                    $body = array(
                        'order_txhash'                                                  => $order_txhash,
                        'payment_details'                                               => $payment_details,
                        'order_id'                                                      => $order_id,
                        'payment_status'                                                => 'success',
                        'ether_and_erc20_tokens_payment_gateway_complete_order_wpnonce' => wp_create_nonce( 'ether_and_erc20_tokens_payment_gateway_complete_order_' . $order_id ),
                    );
                    // @see https://wordpress.stackexchange.com/a/89000
                    $cookies = array();
                    foreach ( $_COOKIE as $name => $value ) {
                        $cookies[] = new \WP_Http_Cookie( array(
                            'name'  => $name,
                            'value' => $value,
                        ) );
                    }
                    $args = array(
                        'method'    => 'POST',
                        'timeout'   => 45,
                        'sslverify' => false,
                        'headers'   => array(
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ),
                        'cookies'   => $cookies,
                        'body'      => http_build_query( $body ),
                    );
                    $request = wp_remote_post( $url, $args );
                    if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
                        $this->log( "failed to send request: " . print_r( $request, true ) );
                    }
                    $response = wp_remote_retrieve_body( $request );
                    //                    $this->log("response: " . print_r( $response, true ));
                } else {
                    $payment_details = sprintf( __( 'Non-successful payment notification received. Order updated to failed: %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $order_id );
                    $this->log( $payment_details );
                    $notify_page = get_bloginfo( 'url' ) . '/?payment_response=ether_and_erc20_tokens_payment_gateway&oid=' . $order_id . '&wpj_payment_id=' . $payment->id;
                    //                    $notify_page  = 'http://127.0.0.1' . '/?payment_response=ether_and_erc20_tokens_payment_gateway&oid=' . $order_id . '&wpj_payment_id=' . $payment->id;
                    // @see https://wordpress.stackexchange.com/a/329010
                    $url = $notify_page;
                    $body = array(
                        'order_txhash'                                                  => $order_txhash,
                        'payment_details'                                               => $payment_details,
                        'order_id'                                                      => $order_id,
                        'payment_status'                                                => 'fail',
                        'ether_and_erc20_tokens_payment_gateway_complete_order_wpnonce' => wp_create_nonce( 'ether_and_erc20_tokens_payment_gateway_complete_order_' . $order_id ),
                    );
                    // @see https://wordpress.stackexchange.com/a/89000
                    $cookies = array();
                    foreach ( $_COOKIE as $name => $value ) {
                        $cookies[] = new \WP_Http_Cookie( array(
                            'name'  => $name,
                            'value' => $value,
                        ) );
                    }
                    $args = array(
                        'method'    => 'POST',
                        'timeout'   => 45,
                        'sslverify' => false,
                        'headers'   => array(
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ),
                        'cookies'   => $cookies,
                        'body'      => http_build_query( $body ),
                    );
                    $request = wp_remote_post( $url, $args );
                    if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
                        $this->log( "failed to send request2: " . print_r( $request, true ) );
                    }
                    $response = wp_remote_retrieve_body( $request );
                    //                    $this->log("response2: " . print_r( $response, true ));
                }
                
                return $paymentSuccess;
            }
            
            return false;
        }
        
        if ( !function_exists( 'wc_get_order' ) ) {
            return false;
        }
        $order = wc_get_order( $order_id );
        
        if ( !$order->needs_payment() ) {
            if ( $standalone ) {
                $this->log( sprintf( __( 'Order do not need payment, tx check stopped: %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $order_id ) );
            }
            return true;
        }
        
        // WC_DateTime|NULL object if the date is set or null if there is no date.
        $created = $order->get_date_created();
        
        if ( $created ) {
            $diff = time() - $created->getTimestamp();
            
            if ( $diff > $orderExpiredTimeout ) {
                
                if ( $standalone ) {
                    $this->log( sprintf( __( 'Order expired.  Order updated to failed: %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $order_id ) );
                    $order->add_order_note( __( 'Order was expired.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) );
                    $order->update_status( 'failed', __( 'Order was expired.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) );
                }
                
                // stop cron job re-enqueue
                return true;
            }
        
        }
        
        $order_txhash = get_post_meta( $order_id, 'ethereum_txhash', true );
        
        if ( !empty($order_txhash) ) {
            $confirmations_number = $this->get_tx_confirmations_number( $order_txhash, $providerUrl );
            $confirmations_number_max = intval( $this->get_setting_( 'confirmations_number', '12' ) );
            
            if ( $confirmations_number < $confirmations_number_max ) {
                // no payment found
                if ( $standalone ) {
                    $this->log( 'Not enough confirmations ' . $confirmations_number . '/' . $confirmations_number_max . ' found for order ' . $order_id . ' with txhash ' . $order_txhash );
                }
                return false;
            }
        
        }
        
        //            // no payment found
        //            if ($standalone) {
        //                $this->log(
        //                    'No payment txhash found for order ' . $order_id
        //                );
        //            }
        //            return false;
        $paymentInfo = $this->getPaymentInfo( $order_id, $providerUrl, $marketAddress );
        
        if ( !$paymentInfo ) {
            // no payment yet
            $this->log( sprintf( __( 'No payment found for order: %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $order_id ) );
            return false;
        }
        
        
        if ( empty($order_txhash) ) {
            // QR-codes payment was made
            $block_number = get_post_meta( $order_id, '_ethereum_block_number', true );
            
            if ( empty($block_number) ) {
                $block_number = $this->get_block_number( $providerUrl );
                update_post_meta( $order_id, '_ethereum_block_number', sanitize_text_field( $block_number ) );
                $this->log( 'Payment found at block ' . $block_number );
            } else {
                $new_block_number = $this->get_block_number( $providerUrl );
                $confirmations_number = $new_block_number - intval( $block_number );
                $confirmations_number_max = intval( $this->get_setting_( 'confirmations_number', '12' ) );
                
                if ( $confirmations_number < $confirmations_number_max ) {
                    // no payment found
                    if ( $standalone ) {
                        $this->log( 'Not enough confirmations ' . $confirmations_number . '/' . $confirmations_number_max . ' found for order ' . $order_id . ' with txhash ' . $order_txhash );
                    }
                    return false;
                }
            
            }
        
        }
        
        $paymentSuccess = false;
        $custom_currency = $this->get_setting_( 'custom_currency', 'no' );
        $eth_value = $this->getEthValueByOrderId( $order_id, $custom_currency );
        $eth_rate = $this->getEthRateByOrderId( $order_id, $custom_currency );
        $decimals_eth = 1000000000000000000;
        $eth_value_wei = self::double_int_multiply( $eth_value, $decimals_eth );
        // payment recieved
        foreach ( $paymentInfo as $currencyPayment => $valuePayment ) {
            $this->log( sprintf(
                __( 'PaymentInfo recieved for order_id=%s. %s: %s. eth_value=%s, eth_value_wei=%s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
                $order_id,
                $currencyPayment,
                $valuePayment,
                $eth_value,
                $eth_value_wei->toString()
            ) );
            // ETH is encoded as address 0x0000000000000000000000000000000000000001
            
            if ( '0x0000000000000000000000000000000000000001' == $currencyPayment ) {
                $paymentSuccess = $valuePayment->compare( $eth_value_wei ) >= 0;
            } else {
                // $valuePayment is in some ERC20 token
                $decimals_token = intval( $this->get_token_decimals( $currencyPayment, $providerUrl )->toString() );
                $rate = $this->getTokenRate( $tokens_supported, $currencyPayment, $eth_rate );
                // check if token is supported
                
                if ( null !== $rate ) {
                    $token_value = $this->apply_markup_token( $eth_value / doubleval( $rate ) );
                    $decimals_token10 = intval( pow( 10, $decimals_token ) );
                    $token_value_wei = self::double_int_multiply( $token_value, $decimals_token10 );
                    // $paymentSuccess = ($value >= $value_eth_token);
                    $paymentSuccess = $valuePayment->compare( $token_value_wei ) >= 0;
                }
                
                if ( !$paymentSuccess ) {
                    $this->log( sprintf(
                        'Payment failure for order_id=%s. tokens: %s. token decimals: %s. rate=%s. token_value=%s. token_value_wei=%s',
                        $order_id,
                        $tokens_supported,
                        $decimals_token,
                        $rate,
                        $token_value,
                        $token_value_wei->toString()
                    ) );
                }
            }
            
            break;
        }
        
        if ( $paymentSuccess ) {
            // Trigger the emails to be registered and hooked.
            WC()->mailer()->init_transactional_emails();
            $this->log( __( 'Successful payment notification received. Order updated to pending.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) );
            $order->add_order_note( __( 'Successful payment notification received. Order updated to pending.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) );
            $order->payment_complete( $order_txhash );
        } else {
            $this->log( sprintf( __( 'Non-successful payment notification received. Order updated to failed: %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $order_id ) );
            $order->add_order_note( __( 'Non-successful payment notification received. Order updated to failed.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) );
            $order->update_status( 'failed', __( 'Non-successful payment notification.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) );
        }
        
        return $paymentSuccess;
    }
    
    protected function get_tx_confirmations_number( $txhash, $providerUrl )
    {
        $requestManager = new HttpRequestManager( $providerUrl, 10 );
        $web3 = new Web3( new HttpProvider( $requestManager ) );
        $eth = $web3->eth;
        $txBlockNumber = null;
        $eth->getTransactionByHash( $txhash, function ( $err, $transaction ) use( &$txBlockNumber, $txhash ) {
            
            if ( $err !== null ) {
                $this->log( "Failed to getTransactionByHash: " . $err );
                return;
            }
            
            $this->log( "transaction: " . print_r( $transaction, true ) );
            
            if ( is_null( $transaction ) ) {
                $this->log( "tx ({$txhash}) is not found in blockchain" );
                return;
            }
            
            
            if ( !property_exists( $transaction, "blockHash" ) || empty($transaction->blockHash) || '0x0000000000000000000000000000000000000000000000000000000000000000' == $transaction->blockHash ) {
                $this->log( "tx ({$txhash}) is not confirmed yet" );
                return;
            }
            
            
            if ( !property_exists( $transaction, "blockNumber" ) || empty($transaction->blockNumber) ) {
                $this->log( "tx ({$txhash}) is not confirmed yet" );
                return;
            }
            
            $txBlockNumber = hexdec( $transaction->blockNumber );
        } );
        if ( is_null( $txBlockNumber ) ) {
            return 0;
        }
        // TODO: cache $txBlockNumber for this $txhash
        $blockNumber = null;
        $eth->blockNumber( function ( $err, $res ) use( &$blockNumber ) {
            
            if ( $err !== null ) {
                $this->log( "Failed to get blockNumber: " . $err );
                return;
            }
            
            $this->log( "blockNumber: " . print_r( $res, true ) );
            
            if ( is_null( $res ) || empty($res) ) {
                $this->log( "null or empty result for blockNumber" );
                return;
            }
            
            $blockNumber = hexdec( $res );
        } );
        if ( is_null( $blockNumber ) ) {
            return 0;
        }
        return $blockNumber - $txBlockNumber;
    }
    
    protected function get_block_number( $providerUrl )
    {
        $requestManager = new HttpRequestManager( $providerUrl, 10 );
        $web3 = new Web3( new HttpProvider( $requestManager ) );
        $eth = $web3->eth;
        $blockNumber = null;
        $eth->blockNumber( function ( $err, $res ) use( &$blockNumber ) {
            
            if ( $err !== null ) {
                $this->log( "Failed to get blockNumber: " . $err );
                return;
            }
            
            $this->log( "blockNumber: " . print_r( $res, true ) );
            
            if ( is_null( $res ) || empty($res) ) {
                $this->log( "null or empty result for blockNumber" );
                return;
            }
            
            $blockNumber = hexdec( $res );
        } );
        if ( is_null( $blockNumber ) ) {
            return 0;
        }
        return $blockNumber;
    }
    
    protected function getAddressSite16()
    {
        return $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->getAddressSite16();
    }
    
    /**
     * Output the payment information onto the thank you page.
     *
     * @param  int $order_id  The order ID.
     */
    public function thank_you_page( $order_id )
    {
        $order = wc_get_order( $order_id );
        
        if ( !$order->needs_payment() ) {
            $this->log( 'Order does not need payment' );
            return;
        }
        
        // set task to check tx state and complete order if needed
        //        $tokens_supported = esc_attr($this->get_setting_('tokens_supported'));
        // try to complete order if payment succeeded
        $paymentSuccess = $this->complete_order_internal( $order_id );
        
        if ( $paymentSuccess ) {
            $this->log( 'Order is already payed' );
            return;
        }
        
        $custom_currency = $this->get_setting_( 'custom_currency', 'no' );
        $eth_value = $this->getEthValueByOrderId( $order_id, $custom_currency );
        $eth_value_wei = self::double_int_multiply( $eth_value, pow( 10, 18 ) );
        //        list($eth_value_with_dust, $eth_value_with_dust_str) = $this->getEthValueWithDustByOrderId($eth_value, $order_id);
        $token2wcproduct = [];
        $products = wc_get_products( array(
            '_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' => 'yes',
        ) );
        foreach ( $products as $product ) {
            $product_id = $product->get_id();
            $tokenAddress = get_post_meta( $product_id, '_text_input_cryptocurrency_data', true );
            
            if ( !empty($tokenAddress) ) {
                $url = get_permalink( $product_id );
                $token2wcproduct[strtolower( $tokenAddress )] = $url;
            } else {
                //_select_cryptocurrency_option
                $_select_cryptocurrency_option = get_post_meta( $product_id, '_select_cryptocurrency_option', true );
                
                if ( !empty($_select_cryptocurrency_option) && 'Ether' == $_select_cryptocurrency_option ) {
                    $url = get_permalink( $product_id );
                    $token2wcproduct['0x0000000000000000000000000000000000000001'] = $url;
                }
            
            }
        
        }
        // Output everything.
        ?>
    <div id="ether-and-erc20-tokens-woocommerce-payment-gateway-widget"></div>
<?php 
        $base_url = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_url;
        $base_path = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path;
        $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        // 1. runtime~main
        // 2. vendors
        // 3. main
        $runtimeMain = null;
        $vendors = null;
        $main = null;
        $files = scandir( $base_path . "/js/" );
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
        
        if ( !wp_script_is( 'wooetherc20paymentgateway-runtime-main', 'queue' ) && !wp_script_is( 'wooetherc20paymentgateway-runtime-main', 'done' ) ) {
            wp_dequeue_script( 'wooetherc20paymentgateway-runtime-main' );
            wp_deregister_script( 'wooetherc20paymentgateway-runtime-main' );
            wp_register_script(
                'wooetherc20paymentgateway-runtime-main',
                $base_url . "/js/" . $runtimeMain[0],
                array(),
                $runtimeMain[1]
            );
        }
        
        
        if ( !wp_script_is( 'wooetherc20paymentgateway-vendors', 'queue' ) && !wp_script_is( 'wooetherc20paymentgateway-vendors', 'done' ) ) {
            wp_dequeue_script( 'wooetherc20paymentgateway-vendors' );
            wp_deregister_script( 'wooetherc20paymentgateway-vendors' );
            wp_register_script(
                'wooetherc20paymentgateway-vendors',
                $base_url . "/js/" . $vendors[0],
                array( 'wooetherc20paymentgateway-runtime-main' ),
                $vendors[1]
            );
        }
        
        
        if ( !wp_script_is( 'wooetherc20paymentgateway-main', 'queue' ) && !wp_script_is( 'wooetherc20paymentgateway-main', 'done' ) ) {
            wp_dequeue_script( 'wooetherc20paymentgateway-main' );
            wp_deregister_script( 'wooetherc20paymentgateway-main' );
            wp_register_script(
                'wooetherc20paymentgateway-main',
                $base_url . "/js/" . $main[0],
                array( 'wooetherc20paymentgateway-vendors', 'wp-i18n' ),
                $main[1]
            );
        }
        
        wp_enqueue_script( 'wooetherc20paymentgateway-main' );
        if ( function_exists( 'wp_set_script_translations' ) ) {
            wp_set_script_translations( 'wooetherc20paymentgateway-main', 'ether-and-erc20-tokens-woocommerce-payment-gateway', $base_path . 'languages' );
        }
        $web3Endpoint = $this->getWeb3Endpoint();
        $web3WSSEndpoint = $this->getWeb3WSSEndpoint();
        
        if ( function_exists( 'ETHEREUM_WALLET_send_transaction' ) && get_current_user_id() > 0 ) {
            list( $lastTxHash, $lastTxTime ) = ETHEREUM_WALLET_get_last_tx_hash_time();
        } else {
            list( $lastTxHash, $lastTxTime ) = [ "", "" ];
        }
        
        $order_txhash = get_post_meta( $order_id, 'ethereum_txhash', true );
        $addressSite = $this->getAddressSite16();
        $base_currency_ticker = $this->get_setting_( 'currency_ticker', 'ETH' );
        $base_currency_ticker_name = $this->get_setting_( 'currency_ticker_name', 'Ether' );
        $token_standard_name = $this->get_setting_( 'token_standard_name', 'ERC20' );
        $blockchain_display_name = $this->get_setting_( 'blockchain_display_name', 'Ethereum' );
        wp_localize_script( 'wooetherc20paymentgateway-main', 'epg', [
            'gas_limit'                                           => esc_html( $this->get_setting_( 'gas_limit', '200000' ) ),
            'gas_price'                                           => esc_html( floatval( $this->get_setting_( 'gas_price', '5' ) ) ),
            'payment_address'                                     => esc_html( $this->get_setting_( 'payment_address' ) ),
            'addressSite'                                         => $addressSite,
            'masterVendor'                                        => '0x0000000000000000000000000000000000000000',
            'masterVendorFee'                                     => '0',
            'tokens_supported'                                    => esc_html( $this->get_tokens_supported(
            $this->get_setting_( 'tokens_supported' ),
            $order_id,
            $custom_currency,
            $eth_value
        ) ),
            'decimals_ether'                                      => esc_html( $this->get_setting_( 'decimals_ether', '5' ) ),
            'markup_percent'                                      => esc_html( $this->get_setting_( 'markup_percent', '0' ) ),
            'markup_percent_token'                                => esc_html( $this->get_setting_( 'markup_percent_token', '0' ) ),
            'gateway_address'                                     => $this->getGatewayContractAddress(),
            'gateway_abi'                                         => $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->gatewayContractABI,
            'erc20_abi'                                           => $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->erc20ContractABI,
            'erc223_abi'                                          => $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->erc223ContractABI,
            'erc777_abi'                                          => $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->erc777ContractABI,
            'eth_value'                                           => esc_html( $eth_value ),
            'eth_value_wei'                                       => esc_html( $eth_value_wei->toString() ),
            'order_id'                                            => $order_id,
            'order_txhash'                                        => $order_txhash,
            'web3Endpoint'                                        => esc_html( $web3WSSEndpoint ),
            'web3HTTPSEndpoint'                                   => esc_html( $web3Endpoint ),
            'blockchain_network'                                  => esc_html( $this->getBlockchainNetwork() ),
            'base_currency_ticker'                                => esc_html( $base_currency_ticker ),
            'user_wallet_address'                                 => esc_html( ( function_exists( 'ETHEREUM_WALLET_web3_signTransaction_endpoint' ) ? ETHEREUM_WALLET_get_wallet_address() : "" ) ),
            'user_wallet_last_txhash'                             => esc_html( $lastTxHash ),
            'user_wallet_last_txtime'                             => esc_html( $lastTxTime ),
            'token2wcproduct'                                     => esc_html( json_encode( $token2wcproduct ) ),
            'baseURL'                                             => $base_url,
            'disable_ether'                                       => $this->get_setting_( 'disable_ether', 'no' ),
            'confirmations_number'                                => $this->get_setting_( 'confirmations_number', '12' ),
            'view_transaction_url'                                => $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->get_txhash_path_template( $this ),
            'wp_rest_nonce'                                       => wp_create_nonce( 'wp_rest' ),
            'wp_rest_url'                                         => esc_attr( get_rest_url() ),
            'save_order_txhash_nonce'                             => wp_create_nonce( 'save_order_txhash' ),
            'str_gateway_title_text'                              => $this->get_setting_( 'title', sprintf( __( 'Pay with %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ) ),
            'str_purchase_tokens_button_text'                     => __( 'Purchase %1$s tokens', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_purchase_ether_button_text'                      => sprintf( __( 'Purchase %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_make_deposit_button_text'                        => __( 'Deposit with MetaMask', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_make_deposit_with_ethereum_wallet_button_text'   => sprintf( __( 'Deposit with %1$s Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_pay_button_text'                                 => __( 'Pay with MetaMask', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_download_metamask'                               => __( 'Download MetaMask', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_download_metamask_mobile'                        => __( 'Open / Install MetaMask Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_download_trust'                                  => __( 'Open / Install Trust Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_unlock_metamask_account'                         => __( 'Unlock your account please.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_pay_with_ethereum_wallet'                        => sprintf( __( 'Pay with %1$s Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_pay_eth_failure'                                 => sprintf( __( 'Failed to pay %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_pay_eth_success'                                 => sprintf( __( 'Pay %1$s succeeded', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_deposit_eth_failure'                             => sprintf( __( 'Failed to deposit %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_deposit_eth_success'                             => sprintf( __( 'Deposit %1$s succeeded', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_pay_token_failure'                               => sprintf( __( 'Failed to pay %1$s token', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_pay_token_failure_insufficient_balance'          => sprintf( __( 'Failed to pay %1$s token: insufficient balance', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_pay_token_success'                               => sprintf( __( 'Pay %1$s token succeeded', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_pay_eth_rejected'                                => sprintf( __( 'Failed to pay %1$s - rejected', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_pay_token_rejected'                              => sprintf( __( 'Failed to pay %1$s token - rejected', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_deposit_token_failure'                           => sprintf( __( 'Failed to deposit %1$s token', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_deposit_token_failure_insufficient_balance'      => sprintf( __( 'Failed to deposit %1$s token: insufficient balance', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_deposit_token_success'                           => sprintf( __( 'Deposit %1$s token succeeded', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_deposit_eth_rejected'                            => sprintf( __( 'Failed to deposit %1$s - rejected', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker ),
            'str_deposit_token_rejected'                          => sprintf( __( 'Failed to deposit %1$s token - rejected', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $token_standard_name ),
            'str_copied_msg'                                      => __( 'Copied to clipboard', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_mm_site_connect_failed'                          => sprintf( __( 'You have rejected the store to %1$s Wallet connect request', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_payment_complete'                                => __( 'Payment succeeded!', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_complete_no_metamask'                    => __( 'Payment succeeded! Reload page if it was not auto reloaded.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_incomplete'                              => __( 'Payment status: not complete.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_metamask_network_mismatch'                       => __( 'Network mismatch. Choose another network or ask site administrator.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_metamask_network_mismatch_detailed'              => __( 'Network mismatch. Chosen network is %1$s, but this site is configured for %2$s. Choose another network or ask site administrator', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_dlg_content'                             => sprintf( __( 'To use the QR-code feature input your %1$s account address please', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_account_dlg_address_field_label'                 => __( 'Address', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_dlg_qrcode_button_label'                 => __( 'QR-code', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_dlg_title'                               => __( 'Enter account address', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_dlg_ok_button_label'                     => __( 'OK', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_dlg_cancel_button_label'                 => __( 'Cancel', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_account_label'                                   => __( 'Account', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_failed'                                  => __( 'Payment failed. Try to adjust gas setting', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_pay_button_label'                                => __( 'Pay', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_deposit_button_label'                            => __( 'Deposit', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_alert_dlg_title'                                 => __( 'Error', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_alert_dlg_title_default'                         => __( 'Alert', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_alert_dlg_ok_button_label'                       => __( 'OK', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_step_unknown'                            => __( 'N/A', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_step_ether_payment'                      => sprintf( __( '%1$s Payment', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name ),
            'str_payment_step_token_deposit'                      => __( 'Token Deposit', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_step_token_payment'                      => __( 'Token Payment', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_copied_to_clipboard'                             => __( 'Copied to clipboard', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_title'                            => __( 'Advanced', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_desc'                             => __( 'Open to see advanced controls', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_step_label'                              => __( 'Payment Step', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_address_label'                    => __( 'Address', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_copy_to_clipboard'                               => __( 'Copy to clipboard', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_qrcode_button_label'              => __( 'QR-code', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_value_label'                      => __( 'Value', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_advanced_panel_data_label'                       => __( 'Data', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_trust_wallet_title'                              => __( 'Trust Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_trust_wallet_desc'                               => __( 'Trust Wallet is a Secure Multi Coin Wallet for Android and iOS. It is the official cryptocurrency wallet of Binance.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_metamask_title'                                  => __( 'MetaMask', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_metamask_desc'                                   => sprintf( __( 'MetaMask is a bridge that allows you to visit the distributed web of tomorrow in your browser today. It allows you to run %1$s dApps right in your browser without running a full %1$s node.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_ethereum_wallet_title'                           => sprintf( __( '%1$s Wallet', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_ethereum_wallet_desc'                            => sprintf( __( 'Use %1$s Wallet generated for you on this site upon registration', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $blockchain_display_name ),
            'str_qrcode_title'                                    => __( 'QR-code', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_qrcode_desc'                                     => __( 'QR-code is a read-only mode. You can copy values or scan QR-codes for the To address, Value and Data fields to make a payment with your favorite mobile wallet.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_walletconnect_title'                             => __( 'Wallet Connect', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_walletconnect_desc'                              => __( 'WalletConnect is the web3 standard to connect blockchain wallets to dapps.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_success_label'                           => __( 'Payment succeeded! Tx %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_currency_selector_balance_label'         => __( 'Balance', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_currency_selector_balance_value_default' => __( 'N/A', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_currency_selector_currency_label'        => __( 'Currency', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_currency_selector_currency_helper_text'  => __( 'Choose currency to pay with', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_currency_selector_amount_label'          => __( 'Amount', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_method_card_account_label'               => __( 'Account', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_method_card_connect_button_label'        => __( 'Connect', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_payment_method_card_disconnect_button_label'     => __( 'Disconnect', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_qrcode_dlg_title'                                => __( 'Scan QR-code', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tx_progress_dlg_title'                           => __( 'Confirmations', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            'str_tx_progress_dlg_content'                         => __( 'Tx confirmations %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
        ] );
    }
    
    public function register_plugin_styles()
    {
        //        $base_url = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_url;
        //        $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        //            wp_dequeue_script('web3');
        //            wp_deregister_script('web3');
        //            wp_register_script(
        //                'web3', $base_url . "/js/web3{$min}.js", array('jquery'), '0.20.6'
        //            );
        /*
                if( ( ! wp_script_is( 'bootstrap', 'queue' ) ) && ( ! wp_script_is( 'bootstrap', 'done' ) ) ) {
                    wp_dequeue_script('bootstrap');
                    wp_deregister_script('bootstrap');
                    wp_register_script(
                        'bootstrap',
                        $base_url . "/js/bootstrap{$min}.js", array('jquery'), '4.0.0'
                    );
                }
        */
        //            if( ( ! wp_script_is( 'sprintf', 'queue' ) ) && ( ! wp_script_is( 'sprintf', 'done' ) ) ) {
        //                wp_dequeue_script('sprintf');
        //                wp_deregister_script('sprintf');
        //                wp_register_script(
        //                    'sprintf',
        //                    $base_url . "/js/sprintf.min.js", array(), '1.1.2'
        //                );
        //            }
        /*
                if( ( ! wp_script_is( 'qrcode', 'queue' ) ) && ( ! wp_script_is( 'qrcode', 'done' ) ) ) {
                    wp_dequeue_script('qrcode');
                    wp_deregister_script('qrcode');
                    wp_register_script(
                        'qrcode',
                        $base_url . "/js/qrcode{$min}.js", array(), '2009'
                    );
                }
        
                if( ( ! wp_script_is( 'jquery.qrcode', 'queue' ) ) && ( ! wp_script_is( 'jquery.qrcode', 'done' ) ) ) {
                    wp_dequeue_script('jquery.qrcode');
                    wp_deregister_script('jquery.qrcode');
                    wp_register_script(
                        'jquery.qrcode',
                        $base_url . "/js/jquery.qrcode{$min}.js", array('jquery', 'qrcode'), '1.0'
                    );
                }
        
                if( ( ! wp_script_is( 'bootstrap.wizard', 'queue' ) ) && ( ! wp_script_is( 'bootstrap.wizard', 'done' ) ) ) {
                    wp_dequeue_script('bootstrap.wizard');
                    wp_deregister_script('bootstrap.wizard');
                    wp_register_script(
                        'bootstrap.wizard',
                        $base_url . "/js/jquery.bootstrap.wizard{$min}.js", array('jquery', 'bootstrap'), '1.4.2'
                    );
                }
        
                if( ( ! wp_script_is( 'clipboard', 'queue' ) ) && ( ! wp_script_is( 'clipboard', 'done' ) ) ) {
                    wp_dequeue_script('clipboard');
                    wp_deregister_script('clipboard');
                    wp_register_script(
                        'clipboard',
                        $base_url . "/js/clipboard{$min}.js", array(), '2.0.4'
        //                    $base_url . "/js/clipboard.min.js", array(), '2.0.4'
                    );
                }
        
                if( ( ! wp_style_is( 'font-awesome', 'queue' ) ) && ( ! wp_style_is( 'font-awesome', 'done' ) ) ) {
                    wp_dequeue_style('font-awesome');
                    wp_deregister_style('font-awesome');
                    wp_register_style(
                        'font-awesome',
                        $base_url . "/css/font-awesome{$min}.css", array(), '4.7.0'
                    );
                }
        
                if( ( ! wp_style_is( 'bootstrap-ether-and-erc20-tokens-woocommerce-payment-gateway', 'queue' ) ) && ( ! wp_style_is( 'bootstrap-ether-and-erc20-tokens-woocommerce-payment-gateway', 'done' ) ) ) {
                    wp_dequeue_style('bootstrap-ether-and-erc20-tokens-woocommerce-payment-gateway');
                    wp_deregister_style('bootstrap-ether-and-erc20-tokens-woocommerce-payment-gateway');
                    wp_register_style(
                        'bootstrap-ether-and-erc20-tokens-woocommerce-payment-gateway',
                        $base_url . "/css/bootstrap-ns{$min}.css", array('font-awesome'), '4.0.0'
                    );
            }
        */
    }
    
    public function getMarketAddress()
    {
        return esc_attr( $this->get_setting_( 'payment_address' ) );
    }
    
    public function getOrderExpiredTimeout()
    {
        return intval( esc_attr( $this->get_setting_( 'order_expire_timeout', 1 * 24 * 3600 ) ) );
        // TODO: use order_expire_timeout
        //        return 1 * 24 * 3600; // one day
        //        return $this->settings['order_expire_timeout'];
    }
    
    protected function getWeb3Endpoint()
    {
        $web3Endpoint = esc_attr( $this->get_setting_( 'web3Endpoint' ) );
        if ( !empty($web3Endpoint) ) {
            return $web3Endpoint;
        }
        $infuraApiKey = esc_attr( $this->get_setting_( 'infura_api_key' ) );
        $blockchainNetwork = $this->getBlockchainNetwork();
        $web3Endpoint = "https://" . esc_attr( $blockchainNetwork ) . ".infura.io/v3/" . esc_attr( $infuraApiKey );
        return $web3Endpoint;
    }
    
    protected function getWeb3WSSEndpoint()
    {
        $web3Endpoint = esc_attr( $this->get_setting_( 'web3WSSEndpoint' ) );
        if ( !empty($web3Endpoint) ) {
            return $web3Endpoint;
        }
        $web3Endpoint = esc_attr( $this->get_setting_( 'web3Endpoint' ) );
        if ( !empty($web3Endpoint) ) {
            return $web3Endpoint;
        }
        $infuraApiKey = esc_attr( $this->get_setting_( 'infura_api_key' ) );
        $blockchainNetwork = $this->getBlockchainNetwork();
        $web3Endpoint = "wss://" . esc_attr( $blockchainNetwork ) . ".infura.io/ws/v3/" . esc_attr( $infuraApiKey );
        return $web3Endpoint;
    }
    
    protected function getBlockchainNetwork()
    {
        return $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->getBlockchainNetwork( $this );
    }
    
    protected static function double_int_multiply( $dval, $ival )
    {
        $dval = doubleval( $dval );
        $ival = intval( $ival );
        $ival_BI = new BigInteger( intval( $ival ) );
        $sval = strval( $dval );
        $parts = explode( '.', $sval );
        if ( count( $parts ) === 1 ) {
            $parts = explode( ',', $sval );
        }
        
        if ( count( $parts ) === 1 ) {
            $ret = new BigInteger( intval( $dval ) );
            $ret = $ret->multiply( $ival_BI );
            return $ret;
        }
        
        $parts1 = $parts[0];
        $ret = new BigInteger( intval( $parts1 ) );
        $ret = $ret->multiply( $ival_BI );
        if ( doubleval( $parts1 ) === $dval ) {
            return $ret;
        }
        $parts2 = $parts[1];
        $parts2_BI = new BigInteger( intval( $parts2 ) );
        $parts2_decimals = intval( pow( 10, strlen( $parts2 ) ) );
        $parts2_decimals_BI = new BigInteger( intval( $parts2_decimals ) );
        list( $parts2_res_BI, $_ ) = $parts2_BI->multiply( $ival_BI )->divide( $parts2_decimals_BI );
        $ret = $ret->add( $parts2_res_BI );
        return $ret;
    }
    
    protected function getTokenRate_from_API( $rateSourceId, $tokenAddress, $tokenSymbol )
    {
        return $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->getTokenRate_from_API(
            $rateSourceId,
            $tokenAddress,
            $tokenSymbol,
            null,
            $this
        );
    }
    
    protected function getGatewayContractAddress()
    {
        return $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->getGatewayContractAddress( $this );
    }
    
    protected function getEthValueByOrderId( $order_id, $custom_currency )
    {
        $order = wc_get_order( $order_id );
        $currency = get_woocommerce_currency();
        
        if ( 'yes' != $custom_currency && $currency != 'MYC' && !(function_exists( 'mycred_point_type_exists' ) && mycred_point_type_exists( $currency )) ) {
            if ( is_callable( array( $order, 'get_meta' ) ) ) {
                return doubleval( $order->get_meta( '_epg_eth_value' ) );
            }
            return doubleval( get_post_meta( $order_id, '_epg_eth_value', true ) );
        }
        
        return $order->get_total();
    }
    
    protected function getEthRateByOrderId( $order_id, $custom_currency )
    {
        $currency = get_woocommerce_currency();
        
        if ( 'yes' != $custom_currency && $currency != 'MYC' && !(function_exists( 'mycred_point_type_exists' ) && mycred_point_type_exists( $currency )) ) {
            $order = wc_get_order( $order_id );
            if ( is_callable( array( $order, 'get_meta' ) ) ) {
                return doubleval( $order->get_meta( 'epg_eth_rate' ) );
            }
            return doubleval( get_post_meta( $order_id, 'epg_eth_rate', true ) );
        }
        
        return 1;
    }
    
    //    protected function getEthValueWithDustByOrderId($eth_value, $order_id) {
    //        $eth_value_wei0 = static::double_int_multiply($eth_value, pow(10, 18));
    //        $a10000000000 = new BigInteger(10000000000);
    //        list($eth_value_wei1, $_) = $eth_value_wei0->divide($a10000000000);
    //        $eth_value_wei = $eth_value_wei1->multiply($a10000000000);
    //        $diff = $eth_value_wei0->subtract($eth_value_wei);
    //        $order_id_wei = new BigInteger(intval($order_id));
    //        if ($order_id_wei->compare($diff) < 0) {
    //            // compensate replacement of these weis with order_id
    //            $eth_value_wei = $eth_value_wei->add($a10000000000);
    //        }
    //        // add order_id as a dust
    //        $eth_value_wei = $eth_value_wei->add($order_id_wei);
    //        list($eth_value_wei_1, $eth_value_wei_2) = $eth_value_wei->divide(new BigInteger(pow(10, 18)));
    //        return array($eth_value_wei->toString(), $eth_value_wei_1->toString() . '.' . sprintf("%'.018d", intval($eth_value_wei_2->toString())));
    //    }
    /**
     *
     * @param type $tokens_supported
     * @param type $tokenAddress
     * @param number $eth_rate 1 USD in ETH
     * @return number Token rate in ETH
     */
    protected function getTokenRate( $tokens_supported, $tokenAddress, $eth_rate )
    {
        $tokensArr = explode( ",", $tokens_supported );
        if ( !$tokensArr ) {
            return null;
        }
        $currency = get_woocommerce_currency();
        // USD,EUR, ...
        foreach ( $tokensArr as $tokenStr ) {
            $tokenPartsArr = explode( ":", $tokenStr );
            if ( count( $tokenPartsArr ) < 3 ) {
                continue;
            }
            $tokenSymbol = $tokenPartsArr[0];
            $address = $tokenPartsArr[1];
            if ( strtolower( $tokenAddress ) != strtolower( $address ) ) {
                continue;
            }
            $rate = null;
            
            if ( count( $tokenPartsArr ) >= 5 && !empty($tokenPartsArr[4]) && __( 'Fixed', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) !== $tokenPartsArr[4] ) {
                // Take rate from API endpoint
                $rateSourceId = $tokenPartsArr[4];
                $rate = $this->getTokenRate_from_API( $rateSourceId, $tokenAddress, $tokenSymbol );
            }
            
            
            if ( is_null( $rate ) ) {
                $rate = $tokenPartsArr[2];
                $pos = strpos( $rate, $currency );
                
                if ( $pos !== FALSE ) {
                    $rate = substr( $rate, 0, strlen( $rate ) - strlen( $currency ) );
                    // e.g. rate=0.1USD => eth_rate=0.01ETH
                    $rate = $rate * $eth_rate;
                }
            
            }
            
            return $rate;
        }
        return null;
    }
    
    // adjust token rates given in the base currency, not in Ether
    protected function get_tokens_supported(
        $tokens_supported,
        $order_id,
        $custom_currency,
        $eth_value
    )
    {
        $eth_rate = $this->getEthRateByOrderId( $order_id, $custom_currency );
        $currency = get_woocommerce_currency();
        // USD,EUR, ...
        $res = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->get_tokens_supported(
            $tokens_supported,
            $eth_rate,
            $eth_value,
            $currency,
            $this
        );
        $this->log( "get_tokens_supported(" . $this->id . ")({$tokens_supported}, {$order_id}, {$custom_currency}) -> " . $res );
        return $res;
    }
    
    protected function call_gateway_method(
        $method,
        $order_id,
        $providerUrl,
        $marketAddress
    )
    {
        $abi = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->gatewayContractABI;
        $contract = new Contract( new HttpProvider( new HttpRequestManager( $providerUrl, 10 ) ), $abi );
        $contractAddress = $this->getGatewayContractAddress();
        $ret = null;
        $callback = function ( $error, $result ) use( &$ret ) {
            
            if ( $error !== null ) {
                $this->log( $error );
                return;
            }
            
            $this->log( "RESULT: " . print_r( $result, true ) );
            foreach ( $result as $key => $res ) {
                $ret = $res;
                $this->log( "key: " . $key . "; ret: " . $ret );
                break;
            }
        };
        // call contract function
        $this->log( sprintf(
            __( 'call contract %s method %s for market %s for order %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
            $contractAddress,
            $method,
            $marketAddress,
            $order_id
        ) );
        $addressSite = $this->getAddressSite16();
        $contract->at( $contractAddress )->call(
            $method,
            $marketAddress,
            $addressSite,
            $order_id,
            $callback
        );
        $this->log( "ret2: " . $ret );
        return $ret;
    }
    
    protected function get_token_decimals( $tokenAddress, $providerUrl )
    {
        $abi = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->erc20ContractABI;
        $contract = new Contract( new HttpProvider( new HttpRequestManager( $providerUrl, 10 ) ), $abi );
        $ret = null;
        $callback = function ( $error, $result ) use( &$ret ) {
            
            if ( $error !== null ) {
                $this->log( $error );
                return;
            }
            
            $this->log( "RESULT: " . print_r( $result, true ) );
            foreach ( $result as $key => $res ) {
                $ret = $res;
                $this->log( "key: " . $key . "; ret: " . $ret );
                break;
            }
        };
        // call contract function
        $this->log( sprintf( 'call contract %s method decimals', $tokenAddress ) );
        $contract->at( $tokenAddress )->call( "decimals", $callback );
        $this->log( "ret2: " . $ret );
        return $ret;
    }
    
    protected function getPaymentInfo( $order_id, $providerUrl, $marketAddress )
    {
        $currencyPayment = $this->call_gateway_method(
            "getCurrencyPayment",
            $order_id,
            $providerUrl,
            $marketAddress
        );
        if ( null === $currencyPayment ) {
            return null;
        }
        
        if ( !($currencyPayment === "0x0000000000000000000000000000000000000000" || $currencyPayment === "0x") ) {
            $valuePayment = $this->call_gateway_method(
                "getValuePayment",
                $order_id,
                $providerUrl,
                $marketAddress
            );
            if ( null !== $valuePayment ) {
                // $valuePayment_f = doubleval($valuePayment->toString());
                return [
                    $currencyPayment => $valuePayment,
                ];
            }
        }
        
        return null;
    }
    
    protected function check_gateway_class( $order_id )
    {
        $payment_gateway = wc_get_payment_gateway_by_order( $order_id );
        if ( !$payment_gateway ) {
            return false;
        }
        return $payment_gateway instanceof \Ethereumico\Epg\Gateway && $payment_gateway->id == $this->id;
    }
    
    protected function log( $msg )
    {
        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log( $msg, $this );
    }

}
/**
 * WooCommerce gateway class implementation.
 */
class Gateway2 extends Gateway
{
    /**
     * Constructor, set variables etc. and add hooks/filters
     */
    function __construct()
    {
        parent::__construct( 'ether-and-erc20-tokens-woocommerce-payment-gateway2' );
        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway2'] = $this;
        $base_currency_ticker_name = $this->get_setting_( 'currency_ticker_name', 'Ether' );
        $token_standard_name = $this->get_setting_( 'token_standard_name', 'ERC20' );
        $tokens_supported = esc_attr( $this->get_setting_( 'tokens_supported' ) );
        
        if ( empty(trim( $tokens_supported )) ) {
            $title0 = sprintf( __( 'Pay with %1$s', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name );
        } else {
            $title0 = sprintf( __( 'Pay with %1$s or %2$s token', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), $base_currency_ticker_name, $token_standard_name );
        }
        
        $title = $this->get_setting_( 'title', $title0 );
        $this->method_title = $title . ' 2';
    }

}