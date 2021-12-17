<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
add_filter(
    'ethereum_wallet_settings_tabs',
    'ETHEREUM_WALLET_advanced_blockchain_settings_tabs_hook',
    30,
    1
);
function ETHEREUM_WALLET_advanced_blockchain_settings_tabs_hook( $possible_screens )
{
    $possible_screens['advanced_blockchain'] = esc_html( __( 'Advanced Blockchain', 'wallets-ethereum' ) );
    return $possible_screens;
}

add_filter(
    'ethereum_wallet_get_save_options',
    'ETHEREUM_WALLET_advanced_blockchain_get_save_options_hook',
    30,
    2
);
function ETHEREUM_WALLET_advanced_blockchain_get_save_options_hook( $new_options, $current_screen )
{
    if ( 'advanced_blockchain' !== $current_screen ) {
        return $new_options;
    }
    return $new_options;
}

add_filter(
    'ethereum_wallet_print_options',
    'ETHEREUM_WALLET_advanced_blockchain_print_options_hook',
    30,
    2
);
function ETHEREUM_WALLET_advanced_blockchain_print_options_hook( $options, $current_screen )
{
    if ( 'advanced_blockchain' !== $current_screen ) {
        return;
    }
    ?>
<tr valign="top">
<td scope="row" colspan="2"><blockquote>
<?php 
    _e( "Use these settings only if you want to use Ethereum node other than infura.io, or completely another EVM-compatible blockchain like Quorum, BSC, etc.", 'wallets-ethereum' );
    ?>
</blockquote></td>

<tr valign="top">
<th scope="row"><?php 
    _e( "Ethereum Node JSON-RPC Endpoint", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_web3Endpoint" type="text" maxlength="1024" placeholder="<?php 
    _e( "Put your Ethereum Node JSON-RPC Endpoint here", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['web3Endpoint']) ? esc_attr( $options['web3Endpoint'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum Node JSON-RPC Endpoint URI. This is an advanced setting. Use with care. This setting supercedes the "%1$s" setting.', 'wallets-ethereum' ), __( "Infura.io API Key", 'wallets-ethereum' ) ) ;
    ?></p>
        <?php 
    
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        ?>
        <p><?php 
        echo  '<a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' . __( 'Upgrade to Unlock!', 'wallets-ethereum' ) . '</a>' ;
        ?></p>
        <?php 
    }
    
    ?>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Ethereum Node Websocket Endpoint", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_web3WSSEndpoint" type="text" maxlength="1024" placeholder="<?php 
    _e( "Put your Ethereum Node JSON-RPC Endpoint here", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['web3WSSEndpoint']) ? esc_attr( $options['web3WSSEndpoint'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum Node Websocket Endpoint URI. This is an advanced setting. Use with care. This setting supercedes the "%1$s" setting. MUST be set if the "%2$s" setting is set', 'wallets-ethereum' ), __( "Infura.io API Key", 'wallets-ethereum' ), __( "Ethereum Node JSON-RPC Endpoint", 'wallets-ethereum' ) ) ;
    ?></p>
        <?php 
    
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        ?>
        <p><?php 
        echo  '<a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' . __( 'Upgrade to Unlock!', 'wallets-ethereum' ) . '</a>' ;
        ?></p>
        <?php 
    }
    
    ?>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Base crypto currency symbol", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_currency_ticker" type="text" maxlength="42" placeholder="<?php 
    _e( "ETH for Ethereum, BNB for Binance Smart Chain", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['currency_ticker']) ? esc_attr( $options['currency_ticker'] ) : 'ETH' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The base crypto currency ticker for the blockchain configured, like ETH for Ethereum or BNB for Binance Smart Chain. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'wallets-ethereum' ), __( "Ethereum Node JSON-RPC Endpoint", 'wallets-ethereum' ) ) ;
    ?></p>
        <?php 
    
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        ?>
        <p><?php 
        echo  '<a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' . __( 'Upgrade to Unlock!', 'wallets-ethereum' ) . '</a>' ;
        ?></p>
        <?php 
    }
    
    ?>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Base crypto currency name", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_currency_name" type="text" maxlength="42" placeholder="<?php 
    _e( "Ether or Binance Coin", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['currency_name']) ? esc_attr( $options['currency_name'] ) : 'Ether' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The base crypto currency name for the blockchain configured, like Ether for Ethereum or Binance Coin for Binance Smart Chain. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'wallets-ethereum' ), __( "Ethereum Node JSON-RPC Endpoint", 'wallets-ethereum' ) ) ;
    ?></p>
        <?php 
    
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        ?>
        <p><?php 
        echo  '<a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' . __( 'Upgrade to Unlock!', 'wallets-ethereum' ) . '</a>' ;
        ?></p>
        <?php 
    }
    
    ?>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Transaction explorer URL", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_view_transaction_url" type="text" maxlength="1024" placeholder="<?php 
    _e( "Put your Transaction explorer URL template here", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['view_transaction_url']) ? esc_attr( $options['view_transaction_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum transaction explorer URL template. It should contain %%s pattern to insert tx hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'wallets-ethereum' ), __( "Ethereum Node JSON-RPC Endpoint", 'wallets-ethereum' ) ) ;
    ?></p>
        <?php 
    
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        ?>
        <p><?php 
        echo  '<a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' . __( 'Upgrade to Unlock!', 'wallets-ethereum' ) . '</a>' ;
        ?></p>
        <?php 
    }
    
    ?>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Address explorer URL", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_view_address_url" type="text" maxlength="1024" placeholder="<?php 
    _e( "Put your Address explorer URL template here", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['view_address_url']) ? esc_attr( $options['view_address_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum address explorer URL template. It should contain %%s pattern to insert address hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'wallets-ethereum' ), __( "Ethereum Node JSON-RPC Endpoint", 'wallets-ethereum' ) ) ;
    ?></p>
        <?php 
    
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        ?>
        <p><?php 
        echo  '<a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' . __( 'Upgrade to Unlock!', 'wallets-ethereum' ) . '</a>' ;
        ?></p>
        <?php 
    }
    
    ?>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Transactions List API URL", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_tx_list_api_url" type="text" maxlength="2048" placeholder="<?php 
    _e( "Put your Transactions List API URL template here", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['tx_list_api_url']) ? esc_attr( $options['tx_list_api_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum Transactions List API URL template. It should contain %%s pattern to insert address hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'wallets-ethereum' ), __( "Ethereum Node JSON-RPC Endpoint", 'wallets-ethereum' ) ) ;
    ?></p>
        <p>
            For the etherscan.io like APIs it would look like <pre>https://bscscan.com/api?module=account&action=txlist&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=1234567890</pre>
        </p>
        <?php 
    
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        ?>
        <p><?php 
        echo  '<a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' . __( 'Upgrade to Unlock!', 'wallets-ethereum' ) . '</a>' ;
        ?></p>
        <?php 
    }
    
    ?>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Gas Price API URL", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_gas_price_api_url" type="text" maxlength="2048" placeholder="<?php 
    _e( "Put your Transactions List API URL template here", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['gas_price_api_url']) ? esc_attr( $options['gas_price_api_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum Gas Pice API URL. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'wallets-ethereum' ), __( "Ethereum Node JSON-RPC Endpoint", 'wallets-ethereum' ) ) ;
    ?></p>
        <p>
            For the etherscan.io like APIs it looks like <pre>https://api-ropsten.etherscan.io/api?module=gastracker&action=gasoracle&apikey=1234567890</pre>
        </p>
        <?php 
    
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        ?>
        <p><?php 
        echo  '<a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' . __( 'Upgrade to Unlock!', 'wallets-ethereum' ) . '</a>' ;
        ?></p>
        <?php 
    }
    
    ?>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Internal Transactions List API URL", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_internal_tx_list_api_url" type="text" maxlength="2048" placeholder="<?php 
    _e( "Put your Internal Transactions List API URL template here", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['internal_tx_list_api_url']) ? esc_attr( $options['internal_tx_list_api_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum Internal Transactions List API URL template. It should contain %%s pattern to insert address hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'wallets-ethereum' ), __( "Ethereum Node JSON-RPC Endpoint", 'wallets-ethereum' ) ) ;
    ?></p>
        <p>
            For the etherscan.io like APIs it would look like <pre>https://bscscan.com/api?module=account&action=txlistinternal&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=1234567890</pre>
        </p>
        <?php 
    
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        ?>
        <p><?php 
        echo  '<a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' . __( 'Upgrade to Unlock!', 'wallets-ethereum' ) . '</a>' ;
        ?></p>
        <?php 
    }
    
    ?>
    </label>
</fieldset></td>
</tr>


<tr valign="top">
<th scope="row"><?php 
    _e( "Token Transactions List API URL", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_token_tx_list_api_url" type="text" maxlength="2048" placeholder="<?php 
    _e( "Put your Token Transactions List API URL template here", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['token_tx_list_api_url']) ? esc_attr( $options['token_tx_list_api_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum Token Transactions List API URL template. It should contain %%s pattern to insert address hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'wallets-ethereum' ), __( "Ethereum Node JSON-RPC Endpoint", 'wallets-ethereum' ) ) ;
    ?></p>
        <p>
            For the etherscan.io like APIs it would look like <pre>https://bscscan.com/api?module=account&action=tokentx&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=1234567890</pre>
        </p>
        <?php 
    
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        ?>
        <p><?php 
        echo  '<a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' . __( 'Upgrade to Unlock!', 'wallets-ethereum' ) . '</a>' ;
        ?></p>
        <?php 
    }
    
    ?>
    </label>
</fieldset></td>
</tr>


<tr valign="top">
<th scope="row"><?php 
    _e( "NFT token Transactions List API URL", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_nft_token_tx_list_api_url" type="text" maxlength="2048" placeholder="<?php 
    _e( "Put your NFT token Transactions List API URL template here", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['nft_token_tx_list_api_url']) ? esc_attr( $options['nft_token_tx_list_api_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum NFT Token Transactions List API URL template. It should contain %%s pattern to insert address hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'wallets-ethereum' ), __( "Ethereum Node JSON-RPC Endpoint", 'wallets-ethereum' ) ) ;
    ?></p>
        <p>
            For the etherscan.io like APIs it would look like <pre>https://bscscan.com/api?module=account&action=tokennfttx&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=1234567890</pre>
        </p>
        <?php 
    
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        ?>
        <p><?php 
        echo  '<a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' . __( 'Upgrade to Unlock!', 'wallets-ethereum' ) . '</a>' ;
        ?></p>
        <?php 
    }
    
    ?>
    </label>
</fieldset></td>
</tr>
<?php 
}
