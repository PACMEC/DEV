<?php


add_filter( 'ethereum_wallet_settings_tabs', 'PACMEC_WALLET_blockchain_settings_tabs_hook', 30, 1 );
function PACMEC_WALLET_blockchain_settings_tabs_hook( $possible_screens ) {
    $possible_screens['blockchain'] = esc_html(__( 'Blockchain', 'pacmec-wallet' ));
    return $possible_screens;
}

add_filter( 'ethereum_wallet_get_save_options', 'PACMEC_WALLET_blockchain_get_save_options_hook', 30, 2 );
function PACMEC_WALLET_blockchain_get_save_options_hook( $new_options, $current_screen ) {
    if ('blockchain' !== $current_screen) {
        return $new_options;
    }

    $new_options['gas_limit']             = ( ! empty( $_POST['PACMEC_WALLET_gas_limit'] )         && is_numeric( $_POST['PACMEC_WALLET_gas_limit'] ) )             ? intval(sanitize_text_field($_POST['PACMEC_WALLET_gas_limit']))  : 200000;
    $new_options['gas_price']             = ( ! empty( $_POST['PACMEC_WALLET_gas_price'] )             && is_numeric( $_POST['PACMEC_WALLET_gas_price'] ) )                 ? floatval(sanitize_text_field($_POST['PACMEC_WALLET_gas_price']))    : 200;
    $new_options['blockchain_network']    = ( ! empty( $_POST['PACMEC_WALLET_blockchain_network'] )      /*&& is_numeric( $_POST['PACMEC_WALLET_blockchain_network'] )*/ )      ? sanitize_text_field($_POST['PACMEC_WALLET_blockchain_network'])       : 'mainnet';
    $new_options['infuraApiKey']          = ( ! empty( $_POST['PACMEC_WALLET_infuraApiKey'] )     /*&& is_numeric( $_POST['PACMEC_WALLET_infuraApiKey'] )*/ )     ? sanitize_text_field($_POST['PACMEC_WALLET_infuraApiKey'])      : '';

    return $new_options;
}

add_filter( 'ethereum_wallet_print_options', 'PACMEC_WALLET_blockchain_print_options_hook', 30, 2 );
function PACMEC_WALLET_blockchain_print_options_hook( $options, $current_screen ) {
    if ('blockchain' !== $current_screen) {
        return;
    }
?>

<tr valign="top">
<th scope="row"><?php _e("Blockchain", 'pacmec-wallet'); ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_blockchain_network" type="text" maxlength="128" placeholder="mainnet" value="<?php echo ! empty( $options['blockchain_network'] ) ? esc_attr( $options['blockchain_network'] ) : 'mainnet'; ?>">
        <p><?php _e("The blockchain used: mainnet or ropsten. Use mainnet in production, and ropsten in test mode. See plugin documentation for the testing guide.", 'pacmec-wallet') ?></p>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
    <th scope="row"><?php _e("Infura.io API Key", 'pacmec-wallet'); ?><sup>*</sup></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_infuraApiKey" type="text" maxlength="70" placeholder="<?php _e("Put your Infura.io API Key here", 'pacmec-wallet'); ?>" value="<?php echo ! empty( $options['infuraApiKey'] ) ? esc_attr( $options['infuraApiKey'] ) : ''; ?>">
        <p><?php echo sprintf(
            __('The API key for the %1$s. You need to register on this site to obtain it. Follow the %2$sGet infura API Key%3$s guide please.', 'pacmec-wallet')
            , '<a target="_blank" href="https://infura.io/register">https://infura.io/</a>'
            , '<a target="_blank" href="https://ethereumico.io/knowledge-base/infura-api-key-guide/">'
            , '</a>'
        )?></p>
        <p><strong><?php echo sprintf(
            __('Note that this setting is ignored if the "%1$s" setting is set', 'pacmec-wallet')
            , __("Ethereum Node JSON-RPC Endpoint", 'pacmec-wallet')
        )?></strong></p>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e("Gas Limit", 'pacmec-wallet'); ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_gas_limit" type="number" min="0" step="10000" maxlength="8" placeholder="200000" value="<?php echo ! empty( $options['gas_limit'] ) ? esc_attr( $options['gas_limit'] ) : '200000'; ?>">
        <p><?php _e("The default gas limit to to spend on your transactions. 200000 is a reasonable default value.", 'pacmec-wallet') ?></p>
    </label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e("Gas price", 'pacmec-wallet'); ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_gas_price" type="number" min="0" step="1" maxlength="8" placeholder="200" value="<?php echo ! empty( $options['gas_price'] ) ? esc_attr( $options['gas_price'] ) : '200'; ?>">
        <p><?php _e("The gas price in Gwei. Reasonable values are in a 50-250 ratio. The default value is 200 to ensure that your tx would be sent in most of the time.", 'pacmec-wallet') ?></p>
        <p><?php _e("The actual gas price used would be this value or less, depending on the current reasonable gas price in the blockchain.", 'pacmec-wallet') ?></p>
    </label>
</fieldset></td>
</tr>

<?php
}
