<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function PACMEC_WALLET_options_page() {

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
		check_admin_referer( 'pacmec-wallet-update-options' );

        $new_options = apply_filters('ethereum_wallet_get_save_options'
            , $new_options, $current_screen);

		// Get all existing Ethereum Wallet options
		$existing_options = get_option( 'pacmec-wallet_options', array() );

		// Merge $new_options into $existing_options to retain Ethereum Wallet options from all other screens/tabs
		if ( $existing_options ) {
			$new_options = array_merge( $existing_options, $new_options );
		}

        if ( get_option('pacmec-wallet_options') ) {
            update_option('pacmec-wallet_options', $new_options);
        } else {
            $deprecated='';
            $autoload='no';
            add_option('pacmec-wallet_options', $new_options, $deprecated, $autoload);
        }

		?>
		<div class="updated"><p><?php _e( 'Settings saved.' ); ?></p></div>
		<?php

	} else if ( isset( $_POST['Reset'] ) ) {
		// Nonce verification
		check_admin_referer( 'pacmec-wallet-update-options' );

		delete_option( 'pacmec-wallet_options' );
	}

	$options = stripslashes_deep( get_option( 'pacmec-wallet_options', array() ) );

	?>

	<div class="wrap">

	<h1><?php _e( 'Ethereum Wallet Settings', 'pacmec-wallet' ); ?></h1>


    <?php settings_errors(); ?>

    <section>
        <h1><?php _e('Install and Configure Guide', 'pacmec-wallet') ?></h1>
        <p><?php echo sprintf(__('Use the official %1$sInstall and Configure%2$s step by step guide to configure this plugin.', 'pacmec-wallet')
            , '<a href="https://ethereumico.io/knowledge-base/install-pacmec-wallet-wordpress-plugin/" target="_blank">'
            , '</a>') ?></p>
    </section>


	<h2 class="nav-tab-wrapper">
        <?php
            if ($possible_screens) foreach($possible_screens as $s => $sTitle) {
        ?>
		<a href="<?php echo admin_url( 'options-general.php?page=pacmec-wallet&tab=' . esc_attr($s) ); ?>" class="nav-tab<?php if ( $s == $current_screen ) echo ' nav-tab-active'; ?>"><?php echo $sTitle; ?></a>
        <?php
            }
        ?>
	</h2>

	<form id="pacmec-wallet_admin_form" method="post" action="">

	<?php wp_nonce_field('pacmec-wallet-update-options'); ?>

		<table class="form-table">

		<?php
            do_action('ethereum_wallet_print_options', $options, $current_screen);
        ?>
        </table>


		<p class="submit">
			<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Changes', 'pacmec-wallet' ) ?>" />
			<input id="PACMEC_WALLET_reset_options" type="submit" name="Reset" onclick="return confirm('<?php _e('Are you sure you want to delete all Ethereum Wallet options?', 'pacmec-wallet' ) ?>')" value="<?php _e('Reset', 'pacmec-wallet' ) ?>" />
		</p>

	</form>
    </div>

<?php

}
