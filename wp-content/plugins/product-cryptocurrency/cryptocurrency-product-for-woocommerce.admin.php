<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_options_page()
{
    // Require admin privs
    if ( !current_user_can( 'manage_options' ) ) {
        return false;
    }
    $new_options = array();
    // Which tab is selected?
    $possible_screens = array(
        'default' => esc_html( __( 'Standard', 'cryptocurrency-product-for-woocommerce' ) ),
    );
    $possible_screens = apply_filters( 'cryptocurrency_product_for_woocommerce_settings_tabs', $possible_screens );
    asort( $possible_screens );
    $current_screen = ( isset( $_GET['tab'] ) && isset( $possible_screens[$_GET['tab']] ) ? $_GET['tab'] : 'default' );
    
    if ( isset( $_POST['Submit'] ) ) {
        // Nonce verification
        check_admin_referer( 'cryptocurrency-product-for-woocommerce-update-options' );
        // Standard options screen
        
        if ( 'default' == $current_screen ) {
            //            $new_options['wallet_address']        = ( ! empty( $_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_address'] )       /*&& is_numeric( $_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_address'] )*/ )       ? sanitize_text_field($_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_address'])        : '';
            $new_options['gas_limit'] = ( !empty($_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_gas_limit']) && is_numeric( $_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_gas_limit'] ) ? intval( sanitize_text_field( $_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_gas_limit'] ) ) : 400000 );
            $new_options['gas_price'] = ( !empty($_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_gas_price']) && is_numeric( $_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_gas_price'] ) ? floatval( sanitize_text_field( $_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_gas_price'] ) ) : 0 );
            if ( !empty($_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_private_key']) ) {
                $new_options['wallet_private_key'] = sanitize_text_field( $_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_private_key'] );
            }
            $new_options['blockchain_network'] = ( !empty($_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_blockchain_network']) ? sanitize_text_field( $_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_blockchain_network'] ) : 'mainnet' );
            $new_options['infuraApiKey'] = ( !empty($_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_infuraApiKey']) ? sanitize_text_field( $_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_infuraApiKey'] ) : '' );
            $new_options['wallet_meta'] = ( !empty($_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_meta']) ? sanitize_text_field( $_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_meta'] ) : '' );
            //            if (empty($new_options['wallet_meta']) && function_exists('ETHEREUM_WALLET_get_wallet_address')) {
            //                $new_options['wallet_meta'] = 'user_ethereum_wallet_address';
            //            }
            $new_options['wallet_field_disable'] = ( !empty($_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_field_disable']) ? 'on' : '' );
            $new_options['ether_product_type_disable'] = ( !empty($_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ether_product_type_disable']) ? 'on' : '' );
            $new_options['erc20_product_type_disable'] = ( !empty($_POST['CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_erc20_product_type_disable']) ? 'on' : '' );
        }
        
        $new_options = apply_filters( 'cryptocurrency_product_for_woocommerce_get_save_options', $new_options, $current_screen );
        // Get all existing Cryptocurrency Product options
        $existing_options = get_option( 'cryptocurrency-product-for-woocommerce_options', array() );
        
        if ( (!isset( $new_options['wallet_private_key'] ) || empty($new_options['wallet_private_key'])) && (!isset( $existing_options['wallet_private_key'] ) || empty($existing_options['wallet_private_key'])) ) {
            // neither old nor new pkey value is set
            list( $ethAddressChkSum, $privateKeyHex ) = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_create_account();
            $new_options['wallet_address'] = $ethAddressChkSum;
            $new_options['wallet_private_key'] = $privateKeyHex;
        }
        
        
        if ( isset( $existing_options['wallet_private_key'] ) && !empty($existing_options['wallet_private_key']) && isset( $new_options['wallet_private_key'] ) && !empty($new_options['wallet_private_key']) && $existing_options['wallet_private_key'] != $new_options['wallet_private_key'] ) {
            // new pkey value is set. let's backup the old value
            $backup = "";
            if ( isset( $existing_options['_backup_wallet_private_keys'] ) ) {
                $backup = $existing_options['_backup_wallet_private_keys'];
            }
            if ( FALSE === strpos( $backup, $existing_options['wallet_private_key'] ) ) {
                $new_options['_backup_wallet_private_keys'] = $backup . ":" . $existing_options['wallet_private_key'];
            }
            // and calculate the new address from a pkey
            try {
                $ethAddressChkSum = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_address_from_key( $new_options['wallet_private_key'] );
                $new_options['wallet_address'] = $ethAddressChkSum;
            } catch ( \InvalidArgumentException $ex ) {
                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( $ex->getMessage() );
                CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_log( $ex->getTraceAsString() );
                add_settings_error(
                    'wallet_private_key',
                    esc_attr( 'bad_private_key_value' ),
                    sprintf( 'The "%1$s" value entered is not correct.', __( "Ethereum wallet private key", 'cryptocurrency-product-for-woocommerce' ) ),
                    'error'
                );
                unset( $new_options['wallet_private_key'] );
            }
        }
        
        // Merge $new_options into $existing_options to retain Cryptocurrency Product options from all other screens/tabs
        if ( $existing_options ) {
            $new_options = array_merge( $existing_options, $new_options );
        }
        
        if ( false !== get_option( 'cryptocurrency-product-for-woocommerce_options' ) ) {
            update_option( 'cryptocurrency-product-for-woocommerce_options', $new_options );
        } else {
            $deprecated = '';
            $autoload = 'no';
            add_option(
                'cryptocurrency-product-for-woocommerce_options',
                $new_options,
                $deprecated,
                $autoload
            );
        }
        
        ?>
		<div class="updated"><p><?php 
        _e( 'Settings saved.' );
        ?></p></div>
		<?php 
    } else {
        
        if ( isset( $_POST['Reset'] ) ) {
            // Nonce verification
            check_admin_referer( 'cryptocurrency-product-for-woocommerce-update-options' );
            delete_option( 'cryptocurrency-product-for-woocommerce_options' );
        }
    
    }
    
    $existing_options = get_option( 'cryptocurrency-product-for-woocommerce_options', array() );
    
    if ( !isset( $existing_options['wallet_private_key'] ) || empty($existing_options['wallet_private_key']) ) {
        // no pkey is set yet. Let's generate one
        list( $ethAddressChkSum, $privateKeyHex ) = CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_create_account();
        $existing_options['wallet_address'] = $ethAddressChkSum;
        $existing_options['wallet_private_key'] = $privateKeyHex;
        
        if ( false !== get_option( 'cryptocurrency-product-for-woocommerce_options' ) ) {
            update_option( 'cryptocurrency-product-for-woocommerce_options', $existing_options );
        } else {
            $deprecated = '';
            $autoload = 'no';
            add_option(
                'cryptocurrency-product-for-woocommerce_options',
                $existing_options,
                $deprecated,
                $autoload
            );
        }
    
    }
    
    $options = stripslashes_deep( get_option( 'cryptocurrency-product-for-woocommerce_options', array() ) );
    ?>

	<div class="wrap">

	<h1><?php 
    _e( 'Cryptocurrency Product Settings', 'cryptocurrency-product-for-woocommerce' );
    ?></h1>

    <?php 
    settings_errors();
    ?>

    <section>
        <h1><?php 
    _e( 'Install and Configure Guide', 'cryptocurrency-product-for-woocommerce' );
    ?></h1>
        <p><?php 
    echo  sprintf( __( 'Use the official %1$sInstall and Configure%2$s step by step guide to configure this plugin.', 'cryptocurrency-product-for-woocommerce' ), '<a href="https://ethereumico.io/knowledge-base/cryptocurrency-product-for-woocommerce-plugin-install-and-configure/" target="_blank">', '</a>' ) ;
    ?></p>
    </section>

    <?php 
    
    if ( cryptocurrency_product_for_woocommerce_freemius_init()->is_not_paying() ) {
        echo  '<section><h1>' . esc_html__( 'Awesome Premium Features', 'cryptocurrency-product-for-woocommerce' ) . '</h1>' ;
        echo  esc_html__( 'ERC20 tokens support and more.', 'cryptocurrency-product-for-woocommerce' ) ;
        echo  ' <a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '">' . esc_html__( 'Upgrade Now!', 'cryptocurrency-product-for-woocommerce' ) . '</a>' ;
        echo  '</section>' ;
    }
    
    ?>

	<h2 class="nav-tab-wrapper">
        <?php 
    if ( $possible_screens ) {
        foreach ( $possible_screens as $s => $sTitle ) {
            ?>
		<a href="<?php 
            echo  admin_url( 'options-general.php?page=cryptocurrency-product-for-woocommerce&tab=' . esc_attr( $s ) ) ;
            ?>" class="nav-tab<?php 
            if ( $s == $current_screen ) {
                echo  ' nav-tab-active' ;
            }
            ?>"><?php 
            echo  $sTitle ;
            ?></a>
        <?php 
        }
    }
    ?>
	</h2>

	<form id="cryptocurrency-product-for-woocommerce_admin_form" method="post" action="">

	<?php 
    wp_nonce_field( 'cryptocurrency-product-for-woocommerce-update-options' );
    ?>

		<table class="form-table">

		<?php 
    
    if ( 'default' == $current_screen ) {
        ?>
			<tr valign="top">
			<th scope="row"><?php 
        _e( "Ethereum wallet address", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input disabled class="text" autocomplete="off" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_address" type="text" maxlength="42" placeholder="0x0000000000000000000000000000000000000000" value="<?php 
        echo  ( !empty($options['wallet_address']) ? esc_attr( $options['wallet_address'] ) : '' ) ;
        ?>">
                    <p><?php 
        _e( "The Ethereum address of your wallet from which you would sell Ether or ERC20 tokens.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                    <p><?php 
        echo  sprintf( __( "This Ethereum address is auto generated first time you install this plugin. You can change it by changing the \"%s\" setting.", 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum wallet private key", 'cryptocurrency-product-for-woocommerce' ) ) ;
        ?></p>
                    <p><?php 
        _e( "Send your Ether and/or ERC20/ERC721 tokens to this address to be able to sell it.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Ethereum wallet private key", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input class="text" autocomplete="off" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_private_key" type="password" maxlength="128" value="">
                    <p><?php 
        _e( "The private key of your Ethereum wallet from which you will sell Ether or ERC20 tokens. It is kept in a secret and <strong>never</strong> sent to the client side. See plugin documentation for additional security considerations.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                    <p><?php 
        _e( "This private key is auto generated first time you install this plugin. You can change it to your own if needed here.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Infura.io API Key", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_infuraApiKey" type="text" maxlength="35" placeholder="<?php 
        _e( "Put your Infura.io API Key here", 'cryptocurrency-product-for-woocommerce' );
        ?>" value="<?php 
        echo  ( !empty($options['infuraApiKey']) ? esc_attr( $options['infuraApiKey'] ) : '' ) ;
        ?>">
                    <p><?php 
        echo  sprintf( __( 'The API key for the %1$s. You need to register on this site to obtain it. Use this guide please: %2$s.', 'cryptocurrency-product-for-woocommerce' ), '<a target="_blank" href="https://infura.io/register">https://infura.io/</a>', '<a target="_blank" href="https://ethereumico.io/knowledge-base/infura-api-key-guide/">Get infura API Key</a>' ) ;
        ?></p>
                    <p><strong><?php 
        echo  sprintf( __( 'Note that this setting is ignored if the "%1$s" setting is set', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum Node JSON-RPC Endpoint", 'cryptocurrency-product-for-woocommerce' ) ) ;
        ?></strong></p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Blockchain", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_blockchain_network" type="text" maxlength="128" placeholder="mainnet" value="<?php 
        echo  ( !empty($options['blockchain_network']) ? esc_attr( $options['blockchain_network'] ) : 'mainnet' ) ;
        ?>">
                    <p><?php 
        _e( "The blockchain used: mainnet or ropsten. Use mainnet in production, and ropsten in test mode. See plugin documentation for the testing guide.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Ethereum Node JSON-RPC Endpoint", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input <?php 
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        }
        ?> class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_web3Endpoint" type="text" maxlength="1024" placeholder="<?php 
        _e( "Put your Ethereum Node JSON-RPC Endpoint here", 'cryptocurrency-product-for-woocommerce' );
        ?>" value="<?php 
        echo  ( !empty($options['web3Endpoint']) ? esc_attr( $options['web3Endpoint'] ) : '' ) ;
        ?>">
                    <p><?php 
        echo  sprintf( __( 'The Ethereum Node JSON-RPC Endpoint URI. This is an advanced setting. Use with care. This setting supercedes the "%1$s" setting.', 'cryptocurrency-product-for-woocommerce' ), __( "Infura.io API Key", 'cryptocurrency-product-for-woocommerce' ) ) ;
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        ?>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Base crypto currency", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input <?php 
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        }
        ?> class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_currency_ticker" type="text" maxlength="1024" placeholder="<?php 
        _e( "Put blockchain name here", 'cryptocurrency-product-for-woocommerce' );
        ?>" value="<?php 
        echo  ( !empty($options['currency_ticker']) ? esc_attr( $options['currency_ticker'] ) : 'ETH' ) ;
        ?>">
                    <p><?php 
        echo  sprintf( __( 'The base crypto currency ticker for the blockchain configured, like ETH for Ethereum or BNB for Binance Smart Chain. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum Node JSON-RPC Endpoint", 'cryptocurrency-product-for-woocommerce' ) ) ;
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        ?>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Base crypto currency name", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input <?php 
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        }
        ?> class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_currency_ticker_name" type="text" maxlength="1024" placeholder="<?php 
        _e( "Put blockchain name here", 'cryptocurrency-product-for-woocommerce' );
        ?>" value="<?php 
        echo  ( !empty($options['currency_ticker_name']) ? esc_attr( $options['currency_ticker_name'] ) : 'Ether' ) ;
        ?>">
                    <p><?php 
        echo  sprintf( __( 'The base crypto currency ticker name for the blockchain configured, like Ether for Ethereum or BNB for Binance Smart Chain. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum Node JSON-RPC Endpoint", 'cryptocurrency-product-for-woocommerce' ) ) ;
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        ?>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Token standard name", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input <?php 
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        }
        ?> class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_token_standard_name" type="text" maxlength="1024" placeholder="<?php 
        _e( "Put token standard name here", 'cryptocurrency-product-for-woocommerce' );
        ?>" value="<?php 
        echo  ( !empty($options['token_standard_name']) ? esc_attr( $options['token_standard_name'] ) : 'ERC20' ) ;
        ?>">
                    <p><?php 
        echo  sprintf( __( 'The crypto currency token standard name for the blockchain configured, like ERC20 for Ethereum or BEP20 for Binance Smart Chain. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum Node JSON-RPC Endpoint", 'cryptocurrency-product-for-woocommerce' ) ) ;
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        ?>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Blockchain display name", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input <?php 
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        }
        ?> class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_blockchain_display_name" type="text" maxlength="1024" placeholder="<?php 
        _e( "Put blockchain name here", 'cryptocurrency-product-for-woocommerce' );
        ?>" value="<?php 
        echo  ( !empty($options['blockchain_display_name']) ? esc_attr( $options['blockchain_display_name'] ) : 'Ethereum' ) ;
        ?>">
                    <p><?php 
        echo  sprintf( __( 'The display name for the blockchain configured, like Ethereum or Binance Smart Chain. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum Node JSON-RPC Endpoint", 'cryptocurrency-product-for-woocommerce' ) ) ;
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        ?>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Transaction explorer URL", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input <?php 
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        }
        ?> class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_view_transaction_url" type="text" maxlength="1024" placeholder="<?php 
        _e( "Put your Transaction explorer URL template here", 'cryptocurrency-product-for-woocommerce' );
        ?>" value="<?php 
        echo  ( !empty($options['view_transaction_url']) ? esc_attr( $options['view_transaction_url'] ) : '' ) ;
        ?>">
                    <p><?php 
        echo  sprintf( __( 'The Ethereum transaction explorer URL template. It should contain %%s pattern to insert tx hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum Node JSON-RPC Endpoint", 'cryptocurrency-product-for-woocommerce' ) ) ;
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        ?>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Address explorer URL", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input <?php 
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        }
        ?> class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_view_address_url" type="text" maxlength="1024" placeholder="<?php 
        _e( "Put your Transaction explorer URL template here", 'cryptocurrency-product-for-woocommerce' );
        ?>" value="<?php 
        echo  ( !empty($options['view_address_url']) ? esc_attr( $options['view_address_url'] ) : '' ) ;
        ?>">
                    <p><?php 
        echo  sprintf( __( 'The Ethereum address explorer URL template. It should contain %%s pattern to insert address hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'cryptocurrency-product-for-woocommerce' ), __( "Ethereum Node JSON-RPC Endpoint", 'cryptocurrency-product-for-woocommerce' ) ) ;
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        ?>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Gas Limit", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_gas_limit" type="number" min="0" step="10000" maxlength="8" placeholder="400000" value="<?php 
        echo  ( !empty($options['gas_limit']) ? esc_attr( $options['gas_limit'] ) : '400000' ) ;
        ?>">
                    <p><?php 
        _e( "The maximum amount of gas to spend on your transactions. The actual value would be lower and estimated with standard Ethereum API. 400000 is a reasonable default value.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Gas price", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_gas_price" type="number" min="0" step="1" maxlength="8" value="<?php 
        echo  ( !empty($options['gas_price']) || '0' == $options['gas_price'] ? esc_attr( $options['gas_price'] ) : '200' ) ;
        ?>">
                    <p><?php 
        _e( "The maximum gas price allowed in Gwei. Reasonable values are in a 50-250 ratio. The default value is 200 to ensure that your tx would be sent in most of the time.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                    <p><?php 
        _e( "The actual gas price used would be this value or less, depending on the current reasonable gas price in the blockchain.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Ethereum Wallet meta key", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_meta" type="text" value="<?php 
        
        if ( !empty($options['wallet_meta']) ) {
            echo  esc_attr( $options['wallet_meta'] ) ;
        } else {
            if ( function_exists( 'ETHEREUM_WALLET_get_wallet_address' ) ) {
                ?>user_ethereum_wallet_address<?php 
            }
        }
        
        ?>">
                    <p><?php 
        _e( "The meta key used in plugin like Ultimate Member for an Ethereum wallet address field in user registration form. It can be used here to pre-fill the Ethereum wallet field on the Checkout page.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                    <p><?php 
        echo  sprintf(
            __( 'The <strong>user_ethereum_wallet_address</strong> can be used here to pre-fill this field from the %1$s%2$s%3$s on the Checkout page.', 'cryptocurrency-product-for-woocommerce' ),
            '<a href="https://ethereumico.io/product/wordpress-ethereum-wallet-plugin/" target="_blank" rel="nofollow">',
            'Wordpress Ethereum Wallet plugin',
            '</a>'
        ) ;
        ?></p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Disable Ethereum Wallet field?", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input class="checkbox" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_wallet_field_disable" type="checkbox" <?php 
        echo  ( !empty($options['wallet_field_disable']) ? 'checked' : '' ) ;
        ?> >
                    <p><?php 
        _e( "If the Ethereum Wallet meta key value is used, you can disable the Ethereum wallet field on the Checkout page. It prevents user to buy tokens to any other address except the registered one.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Require enough Ether on a Checkout page?", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input <?php 
        
        if ( cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() && !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        } else {
            if ( !in_array( 'ethereum-wallet-premium/ethereum-wallet.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                echo  'disabled' ;
            }
        }
        
        ?>  class="checkbox" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_require_enough_ether" type="checkbox" <?php 
        echo  ( !empty($options['require_enough_ether']) ? 'checked' : '' ) ;
        ?> >
                    <p><?php 
        _e( "If this setting is set, user would not be able to place an order if the Ether balance on her Ethereum Wallet plugin's generated account is not enough to pay for the order.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
					<p><?php 
        echo  sprintf( __( 'Make sure to configure "%1$s" or "%2$s" settings to use this feature.', 'cryptocurrency-product-for-woocommerce' ), __( "Coinmarketcap.com API Key", 'cryptocurrency-product-for-woocommerce' ), __( "Cryptocompare.com API Key", 'cryptocurrency-product-for-woocommerce' ) ) ;
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        
        if ( !in_array( 'ethereum-wallet-premium/ethereum-wallet.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( 'The %1$sEthereum Wallet PRO%2$s plugin is required for this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="https://checkout.freemius.com/mode/dialog/plugin/4542/plan/7314/" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        ?>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Vendor Fee", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                <input <?php 
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        }
        ?> class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_vendor_fee" type="number" min="0" step="1" maxlength="8" placeholder="0" value="<?php 
        echo  ( !empty($options['vendor_fee']) ? esc_attr( $options['vendor_fee'] ) : '' ) ;
        ?>">
                    <p><?php 
        echo  sprintf( __( 'The fee in %1$s vendor should pay to publish cryptocurrency product. This fee would be taken from a vendor\'s Ethereum Wallet account in Ether.', 'cryptocurrency-product-for-woocommerce' ), esc_attr( get_woocommerce_currency_symbol() ) ) ;
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        
        if ( in_array( 'wc-vendors-pro/wcvendors-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && (!function_exists( 'cryptocurrency_product_for_woocommerce_wcv_freemius_init' ) || !cryptocurrency_product_for_woocommerce_wcv_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_wcv_freemius_init()->is_plan( 'pro', true )) ) {
            ?>
                    <p><?php 
            
            if ( function_exists( 'cryptocurrency_product_for_woocommerce_wcv_freemius_init' ) ) {
                echo  sprintf( __( 'Consider the %1$sCryptocurrency Product for WooCommerce WC Vendors Marketplace Addon%2$s for frontend multi-vendor features support.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_wcv_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            } else {
                echo  sprintf( __( 'Consider the %1$sCryptocurrency Product for WooCommerce WC Vendors Marketplace Addon%2$s for frontend multi-vendor features support.', 'cryptocurrency-product-for-woocommerce' ), '<a href="https://checkout.freemius.com/mode/dialog/plugin/4888/plan/7859/" target="_blank">', '</a>' ) ;
            }
            
            ?></p>
                    <?php 
        }
        
        ?>
                    <?php 
        if ( !in_array( 'wc-vendors-pro/wcvendors-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && !in_array( 'wc-vendors/class-wc-vendors.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            echo  '<p>' . sprintf(
                __( 'Install the free %1$sWC Vendors Marketplace%2$s plugin for simple multi-vendor features support, or the %3$sWC Vendors Marketplace PRO%4$s plugin for advanced frontend multi-vendor features support.', 'cryptocurrency-product-for-woocommerce' ),
                '<a href="https://wordpress.org/plugins/wc-vendors/" target="_blank" rel="noreferrer noopener nofollow">',
                '</a>',
                '<a href="https://www.wcvendors.com/product/wc-vendors-pro/partner/olegabr/?campaign=wcvendorspro" target="_blank" rel="noreferrer noopener sponsored nofollow">',
                '</a>'
            ) . '</p>' ;
        }
        ?>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Expiration period", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                <input <?php 
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        }
        ?> class="text" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_expiration_period" type="number" min="0" step="1" maxlength="8" placeholder="7" value="<?php 
        echo  ( !empty($options['expiration_period']) ? esc_attr( $options['expiration_period'] ) : '7' ) ;
        ?>">
                    <p><?php 
        echo  _e( 'Number of days to wait till mark an order as expired if no payment or blockchain transaction confirmation is detected.', 'cryptocurrency-product-for-woocommerce' ) ;
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        ?>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Disable Ether product type?", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input <?php 
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        }
        ?> class="checkbox" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_ether_product_type_disable" type="checkbox" <?php 
        echo  ( !empty($options['ether_product_type_disable']) ? 'checked' : '' ) ;
        ?> >
                    <p><?php 
        _e( "If this setting is checked, the Ether product type would not be shown on the product edit page.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        ?>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php 
        _e( "Disable ERC20 token product type?", 'cryptocurrency-product-for-woocommerce' );
        ?></th>
			<td><fieldset>
				<label>
                    <input <?php 
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            echo  'disabled' ;
        }
        ?> class="checkbox" name="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_erc20_product_type_disable" type="checkbox" <?php 
        echo  ( !empty($options['erc20_product_type_disable']) ? 'checked' : '' ) ;
        ?> >
                    <p><?php 
        _e( "If this setting is checked, the ERC20 token product type would not be shown on the product edit page.", 'cryptocurrency-product-for-woocommerce' );
        ?></p>
                    <?php 
        
        if ( !cryptocurrency_product_for_woocommerce_freemius_init()->is__premium_only() || !cryptocurrency_product_for_woocommerce_freemius_init()->is_plan( 'pro', true ) ) {
            ?>
                    <p><?php 
            echo  sprintf( __( '%1$sUpgrade Now!%2$s to enable this feature.', 'cryptocurrency-product-for-woocommerce' ), '<a href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '" target="_blank">', '</a>' ) ;
            ?></p>
                    <?php 
        }
        
        ?>
                </label>
			</fieldset></td>
			</tr>

		<?php 
    }
    
    ?>
		<?php 
    do_action( 'cryptocurrency_product_for_woocommerce_print_options', $options, $current_screen );
    ?>

		</table>

        <h2><?php 
    _e( "Need help to configure this plugin?", 'cryptocurrency-product-for-woocommerce' );
    ?></h2>
        <p><?php 
    echo  sprintf( __( 'Feel free to %1$shire me!%2$s', 'cryptocurrency-product-for-woocommerce' ), '<a target="_blank" href="https://ethereumico.io/product/configure-wordpress-plugins/" rel="noreferrer noopener sponsored nofollow">', '</a>' ) ;
    ?></p>

        <h2><?php 
    _e( "Need help to develop a ERC20 or ERC721 token?", 'cryptocurrency-product-for-woocommerce' );
    ?></h2>
        <p><?php 
    echo  sprintf( __( 'Feel free to %1$shire me!%2$s', 'cryptocurrency-product-for-woocommerce' ), '<a target="_blank" href="https://ethereumico.io/product/smart-contract-development-services/" rel="noreferrer noopener sponsored nofollow">', '</a>' ) ;
    ?></p>

        <h2><?php 
    _e( "Want to perform an ICO Crowdsale from your Wordpress site?", 'cryptocurrency-product-for-woocommerce' );
    ?></h2>
        <p><?php 
    echo  sprintf( __( 'Install the %1$sEthereum ICO WordPress plugin%2$s!', 'cryptocurrency-product-for-woocommerce' ), '<a target="_blank" href="https://ethereumico.io/product/ethereum-ico-wordpress-plugin/" rel="noreferrer noopener sponsored nofollow">', '</a>' ) ;
    ?></p>

        <h2><?php 
    _e( "Want to create Ethereum wallets on your Wordpress site?", 'cryptocurrency-product-for-woocommerce' );
    ?></h2>
        <p><?php 
    echo  sprintf( __( 'Install the %1$sWordPress Ethereum Wallet plugin%2$s!', 'cryptocurrency-product-for-woocommerce' ), '<a target="_blank" href="https://ethereumico.io/product/wordpress-ethereum-wallet-plugin/" rel="noreferrer noopener sponsored nofollow">', '</a>' ) ;
    ?></p>

        <?php 
    
    if ( cryptocurrency_product_for_woocommerce_freemius_init()->is_not_paying() ) {
        ?>
        <h2><?php 
        _e( "Want to sell ERC20 token for fiat and/or Bitcoin?", 'cryptocurrency-product-for-woocommerce' );
        ?></h2>
        <p><?php 
        echo  sprintf( __( 'Install the %1$sPRO plugin version%2$s!', 'cryptocurrency-product-for-woocommerce' ), '<a target="_blank" href="' . cryptocurrency_product_for_woocommerce_freemius_init()->get_upgrade_url() . '">', '</a>' ) ;
        ?></p>

        <?php 
    }
    
    ?>

		<p class="submit">
			<input class="button-primary" type="submit" name="Submit" value="<?php 
    _e( 'Save Changes', 'cryptocurrency-product-for-woocommerce' );
    ?>" />
			<input id="CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_reset_options" type="submit" name="Reset" onclick="return confirm('<?php 
    _e( 'Are you sure you want to delete all Cryptocurrency Product options?', 'cryptocurrency-product-for-woocommerce' );
    ?>')" value="<?php 
    _e( 'Reset', 'cryptocurrency-product-for-woocommerce' );
    ?>" />
		</p>

	</form>

    <p class="alignleft"><?php 
    echo  sprintf( __( 'If you like <strong>Cryptocurrency Product for WooCommerce</strong> please leave us a %1$s rating. A huge thanks in advance!', 'cryptocurrency-product-for-woocommerce' ), '<a href="https://wordpress.org/support/plugin/cryptocurrency-product-for-woocommerce/reviews?rate=5#new-post" target="_blank">★★★★★</a>' ) ;
    ?></p>


    </div>

<?php 
}
