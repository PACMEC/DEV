<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
add_filter(
    'ethereum_wallet_settings_tabs',
    'PACMEC_WALLET_advanced_blockchain_settings_tabs_hook',
    30,
    1
);
function PACMEC_WALLET_advanced_blockchain_settings_tabs_hook( $possible_screens )
{
    $possible_screens['advanced_blockchain'] = esc_html( __( 'Advanced Blockchain', 'pacmec-wallet' ) );
    return $possible_screens;
}

add_filter(
    'ethereum_wallet_get_save_options',
    'PACMEC_WALLET_advanced_blockchain_get_save_options_hook',
    30,
    2
);
function PACMEC_WALLET_advanced_blockchain_get_save_options_hook( $new_options, $current_screen )
{
    if ( 'advanced_blockchain' !== $current_screen ) {
        return $new_options;
    }
    return $new_options;
}

add_filter(
    'ethereum_wallet_print_options',
    'PACMEC_WALLET_advanced_blockchain_print_options_hook',
    30,
    2
);
function PACMEC_WALLET_advanced_blockchain_print_options_hook( $options, $current_screen )
{
    if ( 'advanced_blockchain' !== $current_screen ) {
        return;
    }
    ?>
<tr valign="top">
<td scope="row" colspan="2"><blockquote>
<?php 
    _e( "Use these settings only if you want to use Ethereum node other than infura.io, or completely another EVM-compatible blockchain like Quorum, BSC, etc.", 'pacmec-wallet' );
    ?>
</blockquote></td>

<tr valign="top">
<th scope="row"><?php 
    _e( "Ethereum Node JSON-RPC Endpoint", 'pacmec-wallet' );
    ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_web3Endpoint" type="text" maxlength="1024" placeholder="<?php 
    _e( "Put your Ethereum Node JSON-RPC Endpoint here", 'pacmec-wallet' );
    ?>" value="<?php 
    echo  ( !empty($options['web3Endpoint']) ? esc_attr( $options['web3Endpoint'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum Node JSON-RPC Endpoint URI. This is an advanced setting. Use with care. This setting supercedes the "%1$s" setting.', 'pacmec-wallet' ), __( "Infura.io API Key", 'pacmec-wallet' ) ) ;
    ?></p>
        
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Ethereum Node Websocket Endpoint", 'pacmec-wallet' );
    ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_web3WSSEndpoint" type="text" maxlength="1024" placeholder="<?php 
    _e( "Put your Ethereum Node JSON-RPC Endpoint here", 'pacmec-wallet' );
    ?>" value="<?php 
    echo  ( !empty($options['web3WSSEndpoint']) ? esc_attr( $options['web3WSSEndpoint'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum Node Websocket Endpoint URI. This is an advanced setting. Use with care. This setting supercedes the "%1$s" setting. MUST be set if the "%2$s" setting is set', 'pacmec-wallet' ), __( "Infura.io API Key", 'pacmec-wallet' ), __( "Ethereum Node JSON-RPC Endpoint", 'pacmec-wallet' ) ) ;
    ?></p>
        
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Transaction explorer URL", 'pacmec-wallet' );
    ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_view_transaction_url" type="text" maxlength="1024" placeholder="<?php 
    _e( "Put your Transaction explorer URL template here", 'pacmec-wallet' );
    ?>" value="<?php 
    echo  ( !empty($options['view_transaction_url']) ? esc_attr( $options['view_transaction_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum transaction explorer URL template. It should contain %%s pattern to insert tx hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'pacmec-wallet' ), __( "Ethereum Node JSON-RPC Endpoint", 'pacmec-wallet' ) ) ;
    ?></p>
        
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Address explorer URL", 'pacmec-wallet' );
    ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_view_address_url" type="text" maxlength="1024" placeholder="<?php 
    _e( "Put your Address explorer URL template here", 'pacmec-wallet' );
    ?>" value="<?php 
    echo  ( !empty($options['view_address_url']) ? esc_attr( $options['view_address_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum address explorer URL template. It should contain %%s pattern to insert address hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'pacmec-wallet' ), __( "Ethereum Node JSON-RPC Endpoint", 'pacmec-wallet' ) ) ;
    ?></p>
        
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Transactions List API URL", 'pacmec-wallet' );
    ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_tx_list_api_url" type="text" maxlength="2048" placeholder="<?php 
    _e( "Put your Transactions List API URL template here", 'pacmec-wallet' );
    ?>" value="<?php 
    echo  ( !empty($options['tx_list_api_url']) ? esc_attr( $options['tx_list_api_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum Transactions List API URL template. It should contain %%s pattern to insert address hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'pacmec-wallet' ), __( "Ethereum Node JSON-RPC Endpoint", 'pacmec-wallet' ) ) ;
    ?></p>
        <p>
            For the etherscan.io like APIs it would look like <pre>https://bscscan.com/api?module=account&action=txlist&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=1234567890</pre>
        </p>
        
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php 
    _e( "Internal Transactions List API URL", 'pacmec-wallet' );
    ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_internal_tx_list_api_url" type="text" maxlength="2048" placeholder="<?php 
    _e( "Put your Internal Transactions List API URL template here", 'pacmec-wallet' );
    ?>" value="<?php 
    echo  ( !empty($options['internal_tx_list_api_url']) ? esc_attr( $options['internal_tx_list_api_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum Internal Transactions List API URL template. It should contain %%s pattern to insert address hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'pacmec-wallet' ), __( "Ethereum Node JSON-RPC Endpoint", 'pacmec-wallet' ) ) ;
    ?></p>
        <p>
            For the etherscan.io like APIs it would look like <pre>https://bscscan.com/api?module=account&action=txlistinternal&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=1234567890</pre>
        </p>
        
    </label>
</fieldset></td>
</tr>


<tr valign="top">
<th scope="row"><?php 
    _e( "Token Transactions List API URL", 'pacmec-wallet' );
    ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_token_tx_list_api_url" type="text" maxlength="2048" placeholder="<?php 
    _e( "Put your Token Transactions List API URL template here", 'pacmec-wallet' );
    ?>" value="<?php 
    echo  ( !empty($options['token_tx_list_api_url']) ? esc_attr( $options['token_tx_list_api_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum Token Transactions List API URL template. It should contain %%s pattern to insert address hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'pacmec-wallet' ), __( "Ethereum Node JSON-RPC Endpoint", 'pacmec-wallet' ) ) ;
    ?></p>
        <p>
            For the etherscan.io like APIs it would look like <pre>https://bscscan.com/api?module=account&action=tokentx&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=1234567890</pre>
        </p>
        
    </label>
</fieldset></td>
</tr>


<tr valign="top">
<th scope="row"><?php 
    _e( "NFT token Transactions List API URL", 'pacmec-wallet' );
    ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_nft_token_tx_list_api_url" type="text" maxlength="2048" placeholder="<?php 
    _e( "Put your NFT token Transactions List API URL template here", 'pacmec-wallet' );
    ?>" value="<?php 
    echo  ( !empty($options['nft_token_tx_list_api_url']) ? esc_attr( $options['nft_token_tx_list_api_url'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  sprintf( __( 'The Ethereum NFT Token Transactions List API URL template. It should contain %%s pattern to insert address hash to. This is an advanced setting most commonly used with the "%1$s" setting. Use with care.', 'pacmec-wallet' ), __( "Ethereum Node JSON-RPC Endpoint", 'pacmec-wallet' ) ) ;
    ?></p>
        <p>
            For the etherscan.io like APIs it would look like <pre>https://bscscan.com/api?module=account&action=tokennfttx&address=%s&startblock=0&endblock=99999999&sort=desc&apikey=1234567890</pre>
        </p>
        
    </label>
</fieldset></td>
</tr>
<?php 
}
