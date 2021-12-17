<?php


add_filter( 'ethereum_wallet_settings_tabs', 'ETHEREUM_WALLET_api_keys_settings_tabs_hook', 30, 1 );
function ETHEREUM_WALLET_api_keys_settings_tabs_hook( $possible_screens ) {
    $possible_screens['api_keys'] = esc_html(__( 'API Keys', 'wallets-ethereum' ));
    return $possible_screens;
}

add_filter( 'ethereum_wallet_get_save_options', 'ETHEREUM_WALLET_api_keys_get_save_options_hook', 30, 2 );
function ETHEREUM_WALLET_api_keys_get_save_options_hook( $new_options, $current_screen ) {
    if ('api_keys' !== $current_screen) {
        return $new_options;
    }

    $new_options['etherscanApiKey']       = ( ! empty( $_POST['ETHEREUM_WALLET_etherscanApiKey'] )  /*&& is_numeric( $_POST['ETHEREUM_WALLET_etherscanApiKey'] )*/ )  ? sanitize_text_field($_POST['ETHEREUM_WALLET_etherscanApiKey'])   : '';
    $new_options['cryptocompare_api_key'] = ( ! empty( $_POST['ETHEREUM_WALLET_cryptocompare_api_key'] )     /*&& is_numeric( $_POST['ETHEREUM_WALLET_cryptocompare_api_key'] )*/ )     ? sanitize_text_field($_POST['ETHEREUM_WALLET_cryptocompare_api_key'])      : '';

    return $new_options;
}

add_filter( 'ethereum_wallet_print_options', 'ETHEREUM_WALLET_api_keys_print_options_hook', 30, 2 );
function ETHEREUM_WALLET_api_keys_print_options_hook( $options, $current_screen ) {
    if ('api_keys' !== $current_screen) {
        return;
    }
?>

<tr valign="top">
<th scope="row"><?php _e("Etherscan Api Key", 'wallets-ethereum'); ?></th>
<td><fieldset>
    <label>
        <input class="text" name="ETHEREUM_WALLET_etherscanApiKey" id="ETHEREUM_WALLET_etherscanApiKey" type="text" maxlength="35" placeholder="<?php _e("Put your Etherscan Api Key here", 'wallets-ethereum'); ?>" value="<?php echo ! empty( $options['etherscanApiKey'] ) ? esc_attr( $options['etherscanApiKey'] ) : ''; ?>">
        <p><?php echo sprintf(__('The API key for the %1$s. You need to %2$sregister%3$s on this site to obtain it.', 'wallets-ethereum')
            , '<a target="_blank" href="https://etherscan.io/myapikey">https://etherscan.io</a>'
            , '<a target="_blank" href="https://etherscan.io/register">'
            , '</a>') ?></p>
        <p><?php _e("Required for transactions history display and any ERC20 tokens related functionality.", 'wallets-ethereum') ?></p>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e("Cryptocompare API Key", 'wallets-ethereum'); ?></th>
<td><fieldset>
    <label>
        <input class="text" name="ETHEREUM_WALLET_cryptocompare_api_key" type="text" maxlength="70" placeholder="<?php _e("Put your Cryptocompare API Key here", 'wallets-ethereum'); ?>" value="<?php echo ! empty( $options['cryptocompare_api_key'] ) ? esc_attr( $options['cryptocompare_api_key'] ) : ''; ?>">
        <p><?php echo sprintf(__('The API key for the <a target="_blank" href="%s">%s</a>. You need to register on this site to obtain it.', 'wallets-ethereum')
            , 'https://min-api.cryptocompare.com'
            , 'https://min-api.cryptocompare.com'
            )?></p>
        <p><?php _e("Enter it if you plan to display fiat Ether rate.", 'wallets-ethereum') ?></p>
    </label>
</fieldset></td>
</tr>

<?php
}
