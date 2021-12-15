<?php
/**
 * CryptoWoo add-on version compatibility
 *
 * @package    CryptoWoo
 * @subpackage Admin
 */

/**
 * CryptoWoo add-on version compatibility class
 */
class CW_Versions {


	/**
	 * Compare versions, maybe display admin notice
	 */
	public static function check_addon_version_compatibility() {

		$deactivate = array();

		$hd_wallet_addon_file    = '/cryptowoo-hd-wallet-addon/cryptowoo-hd-wallet-addon.php';
		$hd_wallet_addon_version = self::get_addon_version( 'HDWALLET_VER', $hd_wallet_addon_file );
		if ( $hd_wallet_addon_version && version_compare( $hd_wallet_addon_version, '0.12.0', '<' ) ) {
			add_action( 'admin_notices', 'CW_Versions::cryptowoo_hd_addon_outdated' );
		}

		$dash_addon_file    = '/cryptowoo-dash-addon/cryptowoo-dash-addon.php';
		$dash_addon_version = self::get_addon_version( 'CWDASH_VER', $dash_addon_file );
		if ( $dash_addon_version && version_compare( $dash_addon_version, '0.4.1', '<' ) ) {
			add_action( 'admin_notices', 'CW_Versions::cryptowoo_dash_addon_outdated' );
		}

		$vtc_addon_file    = '/cryptowoo-vertcoin-addon/cryptowoo-vertcoin-addon.php';
		$vtc_addon_version = self::get_addon_version( 'CWVTC_VER', $vtc_addon_file );
		if ( $vtc_addon_version && version_compare( $vtc_addon_version, '1.2.5', '<' ) ) {
			add_action( 'admin_notices', 'CW_Versions::cryptowoo_vtc_addon_outdated' );
		}

		$eth_addon_file    = '/cryptowoo-ethereum-addon/cryptowoo-ethereum-addon.php';
		$eth_addon_version = self::get_addon_version( 'CWETH_VER', $eth_addon_file );
		if ( $eth_addon_version && version_compare( $eth_addon_version, '1.7.3', '<' ) ) {
			add_action( 'admin_notices', 'CW_Versions::cryptowoo_eth_addon_outdated' );
		}

		$xmr_addon_file    = '/cryptowoo-monero-addon/cryptowoo-monero-addon.php';
		$xmr_addon_version = self::get_addon_version( 'CWXMR_VER', $xmr_addon_file );
		if ( $xmr_addon_version && version_compare( $xmr_addon_version, '1.0.10', '<' ) ) {
			add_action( 'admin_notices', 'CW_Versions::cryptowoo_xmr_addon_outdated' );
		}

		$dokan_addon_file    = '/cryptowoo-dokan-addon/cryptowoo-dokan-addon.php';
		$dokan_addon_version = self::get_addon_version( 'CWDA_VER', $dokan_addon_file );
		if ( $dokan_addon_version && version_compare( $dokan_addon_version, '0.1.2', '<' ) ) {
			add_action( 'admin_notices', 'CW_Versions::cryptowoo_dokan_addon_outdated' );
		}

		// Bitcoin Cash has been added to the main CryptoWoo plugin so the add-on should not be active anymore.
		// If Bitcoin Cash Add-on still exists in this site, we will deactivate it and show the user a notice.
		if ( file_exists( WP_PLUGIN_DIR . '/cryptowoo-bitcoin-cash-addon/cryptowoo-bitcoin-cash-addon.php' ) ) {
			$deactivate[] = '/cryptowoo-bitcoin-cash-addon/cryptowoo-bitcoin-cash-addon.php';
			add_action( 'admin_notices', 'CW_Versions::cryptowoo_bitcoin_cash_addon_deprecated' );
		}

		if ( count( $deactivate ) ) {
			deactivate_plugins( $deactivate, true );
		}

	}

	/**
	 * Render "plugin outdated" admin notice
	 *
	 * @param string $plugin_name The plugin name.
	 */
	public static function render_plugin_outdated_admin_notice( $plugin_name ) {
		$plugin_slug = str_replace( array( ' ', 'add-on' ), array( '-', 'addon' ), strtolower( $plugin_name ) );
		$target_base = 'update.php?action=upgrade-plugin&plugin=';
		$plugin_file = "$plugin_slug/$plugin_slug.php";

		$notice = CW_Admin_Notice::generate( CW_Admin_Notice::NOTICE_ERROR )
			/* translators: %1$s: cryptowoo add-on name */
			->add_message( sprintf( esc_html__( '%1$s is outdated! Please install the latest version now.', 'cryptowoo' ), $plugin_name ) )
			->add_message( sprintf( esc_html__( 'If you do not update now, functionality such as exchange rate updates and order processing may not work, and you may lose the settings for this add-on if you save the CryptoWoo plugin settings page.', 'cryptowoo' ), $plugin_name, '<br>' ) )
			->make_dismissible( str_replace( '-', '_', $plugin_slug ) . '_inactive' );
		if ( current_user_can( 'update_plugins' ) && isset( get_plugin_updates()[ $plugin_file ]->update ) ) {
			$notice->add_button( esc_html__( 'Update To Latest Release', 'cryptowoo' ), '', $target_base, $plugin_file, 'upgrade-plugin_' . $plugin_file );
		} else {
			$notice->add_button_with_full_path_url( esc_html__( 'Download Latest Release', 'cryptowoo' ), '', 'https://www.cryptowoo.com/my-account/api-downloads/' );
		}

		$notice->print_notice();
	}

	/**
	 * Outdated HD Wallet Add-on
	 */
	public static function cryptowoo_hd_addon_outdated() {
		self::render_plugin_outdated_admin_notice( 'CryptoWoo HD Wallet Add-on' );
	}

	/**
	 * Outdated Dash Add-on
	 */
	public static function cryptowoo_dash_addon_outdated() {
		self::render_plugin_outdated_admin_notice( 'CryptoWoo Dash Add-on' );
	}

	/**
	 * Outdated Bitcoin Cash Add-on
	 */
	public static function cryptowoo_bch_addon_outdated() {
		self::render_plugin_outdated_admin_notice( 'CryptoWoo Bitcoin Cash Add-on' );
	}

	/**
	 * Outdated Vertcoin Add-on
	 */
	public static function cryptowoo_vtc_addon_outdated() {
		self::render_plugin_outdated_admin_notice( 'CryptoWoo Vertcoin Add-on' );
	}

	/**
	 * Outdated Ethereum Add-on
	 */
	public static function cryptowoo_eth_addon_outdated() {
		self::render_plugin_outdated_admin_notice( 'CryptoWoo Ethereum Add-on' );
	}

	/**
	 * Outdated Monero Add-on
	 */
	public static function cryptowoo_xmr_addon_outdated() {
		self::render_plugin_outdated_admin_notice( 'CryptoWoo Monero Add-on' );
	}

	/**
	 * Outdated Dokan Add-on
	 */
	public static function cryptowoo_dokan_addon_outdated() {
		self::render_plugin_outdated_admin_notice( 'CryptoWoo Dokan Add-on' );
	}


	/**
	 * Deprecated Bitcoin Cash Add-on
	 */
	public static function cryptowoo_bitcoin_cash_addon_deprecated() {
		$target_base = 'plugins.php?action=delete-selected&checked%5B0%5D=';
		$plugin_file = 'cryptowoo-bitcoin-cash-addon/cryptowoo-bitcoin-cash-addon.php';

		$notice = CW_Admin_Notice::generate( CW_Admin_Notice::NOTICE_ERROR )
			->add_message( esc_html__( 'Bitcoin Cash is now included in the main CryptoWoo plugin, so the CryptoWoo Bitcoin Cash Add-on is disabled.', 'cryptowoo' ) )
			->add_message( esc_html__( 'We recommend you delete the Bitcoin Cash Add-on now.', 'cryptowoo' ) )
			->make_dismissible( 'bch_addon_inactive' );
		if ( current_user_can( 'delete_plugins' ) ) {
			$notice->add_button( esc_html__( 'Delete Bitcoin Cash Add-on', 'cryptowoo' ), '', $target_base, $plugin_file, 'bulk-plugins' );
		}

		$notice->print_notice();
	}

	/**
	 *
	 * Get the version of a CryptoWoo add-on
	 *
	 * @param string $version_constant The version constant name in the add-on.
	 * @param string $file             The file path to the main add-on file.
	 *
	 * @return string
	 */
	private static function get_addon_version( string $version_constant, string $file ) : string {
		if ( defined( $version_constant ) ) {
			return constant( $version_constant );
		}

		$file_path = WP_PLUGIN_DIR . $file;

		if ( ! file_exists( $file_path ) ) {
			return '';
		}

		$plugin_data = get_file_data( $file_path, array( 'Version' => 'Version' ), false );

		return $plugin_data['Version'];
	}

}
