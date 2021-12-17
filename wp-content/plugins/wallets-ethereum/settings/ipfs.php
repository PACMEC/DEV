<?php

add_filter(
    'ethereum_wallet_settings_tabs',
    'ETHEREUM_WALLET_ipfs_settings_tabs_hook',
    30,
    1
);
function ETHEREUM_WALLET_ipfs_settings_tabs_hook( $possible_screens )
{
    $possible_screens['ipfs'] = esc_html( __( 'IPFS', 'wallets-ethereum' ) );
    return $possible_screens;
}

add_filter(
    'ethereum_wallet_get_save_options',
    'ETHEREUM_WALLET_ipfs_get_save_options_hook',
    30,
    2
);
function ETHEREUM_WALLET_ipfs_get_save_options_hook( $new_options, $current_screen )
{
    if ( 'ipfs' !== $current_screen ) {
        return $new_options;
    }
    return $new_options;
}

add_filter(
    'ethereum_wallet_print_options',
    'ETHEREUM_WALLET_ipfs_print_options_hook',
    30,
    2
);
function ETHEREUM_WALLET_ipfs_print_options_hook( $options, $current_screen )
{
    if ( 'ipfs' !== $current_screen ) {
        return;
    }
    ?>

<tr valign="top">
<th scope="row"><?php 
    _e( "IPFS Gateway URL", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_ipfs_gateway_url" type="text" maxlength="10240" placeholder="https://ipfs.io/ipfs/" value="<?php 
    echo  ( !empty($options['ipfs_gateway_url']) ? esc_attr( $options['ipfs_gateway_url'] ) : 'https://ipfs.io/ipfs/' ) ;
    ?>">
        <p><?php 
    _e( 'The ipfs gateway used to show "ipfs://" URLs. The https://ipfs.io/ipfs/ is used by default.', 'wallets-ethereum' );
    ?></p>
        <p><?php 
    echo  sprintf(
        __( 'Pinning services like %5$s provide %3$shigh quality gateway service%4$s. %1$sPublic gateway services%2$s are also available', 'wallets-ethereum' ),
        '<a href="https://docs.ipfs.io/concepts/ipfs-gateway/#gateway-providers" target="_blank" rel="follow">',
        '</a>',
        '<a href="https://pinata.cloud/documentation#DedicatedGateways" target="_blank" rel="follow">',
        '</a>',
        'pinata.cloud'
    ) ;
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

<?php 
}
