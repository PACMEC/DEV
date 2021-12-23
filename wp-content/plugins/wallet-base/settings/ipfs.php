<?php

add_filter(
    'ethereum_wallet_settings_tabs',
    'PACMEC_WALLET_ipfs_settings_tabs_hook',
    30,
    1
);
function PACMEC_WALLET_ipfs_settings_tabs_hook( $possible_screens )
{
    $possible_screens['ipfs'] = esc_html( __( 'IPFS', 'pacmec-wallet' ) );
    return $possible_screens;
}

add_filter(
    'ethereum_wallet_get_save_options',
    'PACMEC_WALLET_ipfs_get_save_options_hook',
    30,
    2
);
function PACMEC_WALLET_ipfs_get_save_options_hook( $new_options, $current_screen )
{
    if ( 'ipfs' !== $current_screen ) {
        return $new_options;
    }
    return $new_options;
}

add_filter(
    'ethereum_wallet_print_options',
    'PACMEC_WALLET_ipfs_print_options_hook',
    30,
    2
);
function PACMEC_WALLET_ipfs_print_options_hook( $options, $current_screen )
{
    if ( 'ipfs' !== $current_screen ) {
        return;
    }
    ?>

<tr valign="top">
<th scope="row"><?php 
    _e( "IPFS Gateway URL", 'pacmec-wallet' );
    ?></th>
<td><fieldset>
    <label>
        <input class="text" name="PACMEC_WALLET_ipfs_gateway_url" type="text" maxlength="10240" placeholder="https://ipfs.io/ipfs/" value="<?php 
    echo  ( !empty($options['ipfs_gateway_url']) ? esc_attr( $options['ipfs_gateway_url'] ) : 'https://ipfs.io/ipfs/' ) ;
    ?>">
        <p><?php 
    _e( 'The ipfs gateway used to show "ipfs://" URLs. The https://ipfs.io/ipfs/ is used by default.', 'pacmec-wallet' );
    ?></p>
        <p><?php 
    echo  sprintf(
        __( 'Pinning services like %5$s provide %3$shigh quality gateway service%4$s. %1$sPublic gateway services%2$s are also available', 'pacmec-wallet' ),
        '<a href="https://docs.ipfs.io/concepts/ipfs-gateway/#gateway-providers" target="_blank" rel="follow">',
        '</a>',
        '<a href="https://pinata.cloud/documentation#DedicatedGateways" target="_blank" rel="follow">',
        '</a>',
        'pinata.cloud'
    ) ;
    ?></p>
    </label>
</fieldset></td>
</tr>
<?php 
}
