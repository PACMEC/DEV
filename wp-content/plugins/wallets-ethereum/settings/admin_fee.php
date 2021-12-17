<?php

add_filter(
    'ethereum_wallet_settings_tabs',
    'ETHEREUM_WALLET_admin_fee_settings_tabs_hook',
    30,
    1
);
function ETHEREUM_WALLET_admin_fee_settings_tabs_hook( $possible_screens )
{
    $possible_screens['admin_fee'] = esc_html( __( 'Admin Fee', 'wallets-ethereum' ) );
    return $possible_screens;
}

add_filter(
    'ethereum_wallet_get_save_options',
    'ETHEREUM_WALLET_admin_fee_get_save_options_hook',
    30,
    2
);
function ETHEREUM_WALLET_admin_fee_get_save_options_hook( $new_options, $current_screen )
{
    if ( 'admin_fee' !== $current_screen ) {
        return $new_options;
    }
    return $new_options;
}

add_filter(
    'ethereum_wallet_print_options',
    'ETHEREUM_WALLET_admin_fee_print_options_hook',
    30,
    2
);
function ETHEREUM_WALLET_admin_fee_print_options_hook( $options, $current_screen )
{
    if ( 'admin_fee' !== $current_screen ) {
        return;
    }
    ?>

<tr valign="top">
<th scope="row"><?php 
    _e( "Admin Fee Markup, %", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_admin_fee_percent" type="number" min="0" step="0.0001" max="100" maxlength="8" placeholder="0" value="<?php 
    echo  ( !empty($options['admin_fee_percent']) ? esc_attr( $options['admin_fee_percent'] ) : '0' ) ;
    ?>">
        <p><?php 
    _e( "The fee percent taken from users with every Ether transfer from accounts controlled by this plugin.", 'wallets-ethereum' );
    ?></p>
        <p><?php 
    _e( "If it is set to zero, all other settings in this section are ignored.", 'wallets-ethereum' );
    ?></p>
        <p><?php 
    _e( "For a fixed fee set this value to a non-zero value like 1 and the same desired value in the min/max settings below.", 'wallets-ethereum' );
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
    _e( "Min Admin Fee Markup, ETH", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_admin_min_fee_eth" type="number" min="0" step="0.00001" maxlength="10" placeholder="0" value="<?php 
    echo  ( !empty($options['admin_min_fee_eth']) ? esc_attr( $options['admin_min_fee_eth'] ) : '0' ) ;
    ?>">
        <p><?php 
    _e( "The minimum admin fee in ETH taken from users with every Ether transfer from accounts controlled by this plugin. Zero means no minimum fee applied.", 'wallets-ethereum' );
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
    _e( "Max Admin Fee Markup, ETH", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_admin_max_fee_eth" type="number" min="0" step="0.00001" maxlength="10" placeholder="0" value="<?php 
    echo  ( !empty($options['admin_max_fee_eth']) ? esc_attr( $options['admin_max_fee_eth'] ) : '0' ) ;
    ?>">
        <p><?php 
    _e( "The maximum admin fee in ETH taken from users with every Ether transfer from accounts controlled by this plugin. Zero means no maximum fee applied.", 'wallets-ethereum' );
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
    _e( "Admin Fee Account", 'wallets-ethereum' );
    ?></th>
<td><fieldset>
    <label>
        <input <?php 
    if ( !ethereum_wallet_freemius_init()->is__premium_only() || !ethereum_wallet_freemius_init()->is_plan( 'pro', true ) ) {
        echo  'disabled' ;
    }
    ?> class="text" name="ETHEREUM_WALLET_admin_fee_account" id="ETHEREUM_WALLET_admin_fee_account" type="text" maxlength="42" placeholder="<?php 
    _e( "0x0000000000000000000000000000000000000000", 'wallets-ethereum' );
    ?>" value="<?php 
    echo  ( !empty($options['admin_fee_account']) ? esc_attr( $options['admin_fee_account'] ) : '' ) ;
    ?>">
        <p><?php 
    echo  __( 'The ethereum address admin fee payment should be sent to.', 'wallets-ethereum' ) ;
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
