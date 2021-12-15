<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function ETHEREUM_WALLET_options_page() {

	// Require admin privs
	if ( ! current_user_can( 'manage_options' ) )
		return false;

	$new_options = array();

	// Which tab is selected?
    $possible_screens = apply_filters('ethereum_wallet_settings_tabs', []);
//    asort($possible_screens);

	$current_screen = ( isset( $_GET['tab'] ) && isset( $possible_screens[$_GET['tab']] ) ) ? $_GET['tab'] : 'blockchain';

	if ( isset( $_POST['Submit'] ) ) {

		// Nonce verification
		check_admin_referer( 'ethereum-wallet-update-options' );

        $new_options = apply_filters('ethereum_wallet_get_save_options'
            , $new_options, $current_screen);

		// Get all existing Ethereum Wallet options
		$existing_options = get_option( 'ethereum-wallet_options', array() );

		// Merge $new_options into $existing_options to retain Ethereum Wallet options from all other screens/tabs
		if ( $existing_options ) {
			$new_options = array_merge( $existing_options, $new_options );
		}

        if ( get_option('ethereum-wallet_options') ) {
            update_option('ethereum-wallet_options', $new_options);
        } else {
            $deprecated='';
            $autoload='no';
            add_option('ethereum-wallet_options', $new_options, $deprecated, $autoload);
        }

		?>
		<div class="updated"><p><?php _e( 'Settings saved.' ); ?></p></div>
		<?php

	} else if ( isset( $_POST['Reset'] ) ) {
		// Nonce verification
		check_admin_referer( 'ethereum-wallet-update-options' );

		delete_option( 'ethereum-wallet_options' );
	}

	$options = stripslashes_deep( get_option( 'ethereum-wallet_options', array() ) );

	?>

	<div class="wrap">

	<h1><?php _e( 'Ethereum Wallet Settings', 'ethereum-wallet' ); ?></h1>


    <?php settings_errors(); ?>

    <section>
        <h1><?php _e('Install and Configure Guide', 'ethereum-wallet') ?></h1>
        <p><?php echo sprintf(__('Use the official %1$sInstall and Configure%2$s step by step guide to configure this plugin.', 'ethereum-wallet')
            , '<a href="https://ethereumico.io/knowledge-base/install-ethereum-wallet-wordpress-plugin/" target="_blank">'
            , '</a>') ?></p>
    </section>

    <?php
        if ( ethereum_wallet_freemius_init()->is_not_paying() ) {
            echo '<section><h1>' . esc_html__('Awesome Premium Features', 'ethereum-wallet') . '</h1>';
            echo esc_html__('ERC20 tokens support and more.', 'ethereum-wallet');
            echo ' <a href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">' .
                esc_html__('Upgrade Now!', 'ethereum-wallet') .
                '</a>';
            echo '</section>';
        }
    ?>

	<h2 class="nav-tab-wrapper">
        <?php
            if ($possible_screens) foreach($possible_screens as $s => $sTitle) {
        ?>
		<a href="<?php echo admin_url( 'options-general.php?page=ethereum-wallet&tab=' . esc_attr($s) ); ?>" class="nav-tab<?php if ( $s == $current_screen ) echo ' nav-tab-active'; ?>"><?php echo $sTitle; ?></a>
        <?php
            }
        ?>
	</h2>

	<form id="ethereum-wallet_admin_form" method="post" action="">

	<?php wp_nonce_field('ethereum-wallet-update-options'); ?>

		<table class="form-table">

		<?php
            do_action('ethereum_wallet_print_options', $options, $current_screen);
        ?>

        <?php
            if ( !ethereum_wallet_freemius_init()->is_paying() ) {
        ?>
			<tr valign="top">
                <th scope="row"><h2><?php _e("ERC20 tokens support", 'ethereum-wallet'); ?></h2></th>
			<td>
                <p><?php echo sprintf(
                    __('Install the %1$sPRO plugin version%2$s!', 'ethereum-wallet')
                    , '<a target="_blank" href="' . ethereum_wallet_freemius_init()->get_upgrade_url() . '">'
                    , '</a>'
                )?></p>
			</td>
			</tr>

        <?php
            }
        ?>
        </table>

        <h2><?php _e("Want to sell ERC20 token for fiat and/or Bitcoin?", 'ethereum-wallet'); ?></h2>
        <p><?php echo sprintf(
            __('Install the %1$sCryptocurrency Product for WooCommerce plugin%2$s!', 'ethereum-wallet')
            , '<a target="_blank" href="https://ethereumico.io/product/cryptocurrency-wordpress-plugin/">'
            , '</a>'
        )?></p>

        <h2><?php _e("Want to accept Ether or any ERC20/ERC223 token in your WooCommerce store?", 'ethereum-wallet'); ?></h2>
        <p><?php echo sprintf(
            __('Install the %1$sEther and ERC20 tokens WooCommerce Payment Gateway%2$s plugin!', 'ethereum-wallet')
            , '<a target="_blank" href="https://wordpress.org/plugins/ether-and-erc20-tokens-woocommerce-payment-gateway/">'
            , '</a>'
        )?></p>

        <h2><?php _e("Want to sell your ERC20/ERC223 ICO token from your ICO site?", 'ethereum-wallet'); ?></h2>
        <p><?php echo sprintf(
            __('Install the %1$sThe EthereumICO Wordpress plugin%2$s!', 'ethereum-wallet')
            , '<a target="_blank" href="https://ethereumico.io/product/ethereum-ico-wordpress-plugin/">'
            , '</a>'
        )?></p>

        <h2><?php _e("Need help to configure this plugin?", 'ethereum-wallet'); ?></h2>
        <p><?php echo sprintf(
            __('Feel free to %1$shire me!%2$s', 'ethereum-wallet')
            , '<a target="_blank" href="https://ethereumico.io/product/configure-wordpress-plugins/">'
            , '</a>'
        )?></p>

        <h2><?php _e("Need help to develop a ERC20 token for your ICO?", 'ethereum-wallet'); ?></h2>
        <p><?php echo sprintf(
            __('Feel free to %1$shire me!%2$s', 'ethereum-wallet')
            , '<a target="_blank" href="https://ethereumico.io/product/crowdsale-contract-development/">'
            , '</a>'
        )?></p>

		<p class="submit">
			<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Changes', 'ethereum-wallet' ) ?>" />
			<input id="ETHEREUM_WALLET_reset_options" type="submit" name="Reset" onclick="return confirm('<?php _e('Are you sure you want to delete all Ethereum Wallet options?', 'ethereum-wallet' ) ?>')" value="<?php _e('Reset', 'ethereum-wallet' ) ?>" />
		</p>

	</form>

    <p class="alignleft"><?php echo sprintf(
        __('If you like <strong>Ethereum Wallet</strong> plugin please leave us a %1$s rating. A huge thanks in advance!', 'ethereum-wallet')
        , '<a href="https://wordpress.org/support/plugin/ethereum-wallet/reviews?rate=5#new-post" target="_blank">★★★★★</a>'
    )?></p>

    </div>

<?php

}
