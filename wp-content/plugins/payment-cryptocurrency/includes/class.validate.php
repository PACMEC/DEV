<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

use BitWasp\Bitcoin\Base58;
use BitWasp\Bitcoin\Address\AddressCreator;
use \BitWasp\Bitcoin\Network\NetworkFactory;

/**
 * Validation methods for cryptocurrency addresses and master public keys
 *
 * @category   CryptoPay
 * @package    CryptoPay
 * @subpackage Validate
 * @author     DRDoGE
 */
class CW_Validate {


	/**
	 * Return value specified in $return if variable or array value is empty or not set
	 *
	 * @param  $key
	 * @param  bool|false $options
	 * @param  bool|false $return
	 * @return mixed
	 */
	public static function check_if_unset( $key, $options = false, $return = false ) {

		if ( is_array( $options ) ) {
			return isset( $options[ $key ] ) && ! empty( $options[ $key ] ) ? $options[ $key ] : $return;
		} else {
			return isset( $key ) && ! empty( $key ) ? $key : $return;
		}
	}

	/**
	 * Validates a cryptocurrency address offline
	 * using BitWasp\Bitcoin\Address\AddressCreator
	 *
	 * @param  $address
	 * @param  $currency
	 * @param  bool     $test
	 * @return bool
	 */
	public function bitwasp_offline_validate_address( $address, $currency, $test = false ) {
		$valid = apply_filters( "cw_validate_address_$currency", $address );
		if ( is_bool( $valid ) ) {
			return $valid;
		}

		// Minimum length
		if ( ! $address || strlen( $address ) < 25 ) {
			return false;
		}
		switch ( $currency ) {
			default:
			case 'BTC':
				$network = NetworkFactory::bitcoin();
				break;
			case 'BCH':
				$network = NetworkFactory::bitcoin();
				break;
			case 'LTC':
				$network = NetworkFactory::Litecoin();
				break;
			case 'DOGE':
				$network = NetworkFactory::Dogecoin();
				break;
			case 'BLK':
				$network = NetworkFactory::create( '19', '55', '99' )->setHDPubByte( '0488b21e' )->setHDPrivByte( '02cfbf60' )->setNetMagicBytes( 'd9b4bef9' );

				break;
		}

		$ac       = new AddressCreator();
		$is_valid = true;
		try {
			$address = $ac->fromString( $address, $network );
		} catch ( Exception $e ) {
			$is_valid = false;
		}

		return $is_valid;
	}

	/**
	 * Validates a cryptocurrency address offline
	 * Try using BitWasp\Bitcoin\Address\AddressCreator,
	 * then try with BitWasp\Bitcoin\Base58
	 *
	 * #Address prefixes
	 *
	 * block chain-based currencies use addresses, which are a Base58Check encoding of some hash, typically that of a public key.
	 * The encoding includes a version byte, which affects the first character in the address.
	 *
	 * The following is a list of some prefixes which are in use.
	 *
	 * Decimal    Hex    Example use                        Leading symbol(s)    Example
	 * 0        00    Bitcoin pubkey hash (P2PKH address)            1            17VZNX1SN5NtKa8UQFxwQbFeFc3iqRYhem
	 * 5        05    Bitcoin script hash (P2SH address)                3            3EktnHQD7RiAE6uzMj2ZifT9YgRrkSgzQX
	 *
	 * 111        6F    Testnet pubkey hash                            m or n        mipcBbFg9gMiCh81Kj8tqqdgoZub1ZJRfn
	 * 196        C4    Testnet script hash                            2            2MzQwSSnBHWHqSAqtTVQ6v47XtaisrJa1Vc
	 *
	 * 22       16  Dogecoin script hash                            9 or A      9wZKSx1MT5NNi7ybdkQd8Y7ifHKbVL1253
	 * 30       1E  Dogecoin pubkey hash                            D           DA1796XbaYxBwSc41yTDiirr1uuNkS446P
	 * 113      71  Dogecoin testnet pubkey hash                    m or n
	 *
	 * 48       30  Litecoin pubkey hash                            L           LhK2kQwiaAvhjWY799cZvMyYwnQAcxkarr
	 *
	 * 52       34  Namecoin pubkey hash                            M or N      NATX6zEUNfxfvgVwz8qVnnw3hLhhYXhgQn
	 *
	 * 239        EF    Testnet Private key (WIF, uncompressed pubkey)    9            92Pg46rUhgTT7romnV7iGW6W1gbGdeezqdbJCzShkCsYNzyyNcc
	 * 239        EF    Testnet Private key (WIF, compressed pubkey)    c            cNJFgo1driFnPcBdBX8BrJrpxchBWXwXCvNH5SoSkdcF6JXXwHMm
	 * 4        53 135 207    043587CF    Testnet BIP32 pubkey        tpub        tpubD6NzVbkrYhZ4WLczPJWReQycCJdd6YVWXubbVUFnJ5KgU5MDQrD998ZJLNGbhd2pq7ZtDiPYTfJ7iBenLVQpYgSQqPjUsQeJXH8VQ8xA67D
	 * 4        53 131 148    04358394    Testnet BIP32 private key    tprv        tprv8ZgxMBicQKsPcsbCVeqqF1KVdH7gwDJbxbzpCxDUsoXHdb6SnTPYxdwSAKDC6KKJzv7khnNWRAJQsRA8BBQyiSfYnRt6zuu4vZQGKjeW4YF
	 * 128        80    Private key (WIF, uncompressed pubkey)            5            5Hwgr3u458GLafKBgxtssHSPqJnYoGrSzgQsPwLFhLNYskDPyyA
	 * 128        80    Private key (WIF, compressed pubkey)            K or L        L1aW4aubDFB7yfras2S1mN3bqg9nwySY8nkoLmJebSLD5BWv3ENZ
	 * 4        136 178 30    0488B21E    BIP32 pubkey                xpub        xpub661MyMwAqRbcEYS8w7XLSVeEsBXy79zSzH1J8vCdxAZningWLdN3zgtU6LBpB85b3D2yc8sfvZU521AAwdZafEz7mnzBBsz4wKY5e4cp9LB
	 * 4        136 173 228    0488ADE4    BIP32 private key        xprv        xprv9s21ZrQH143K24Mfq5zL5MhWK9hUhhGbd45hLXo2Pq2oqzMMo63oStZzF93Y5wvzdUayhgkkFoicQZcP3y52uPPxFnfoLZB21Teqt1VvEHx
	 *
	 * Source: Bitcoin Wiki https://en.bitcoin.it/w/index.php?title=List_of_address_prefixes
	 *
	 * @param  $address
	 * @param  $currency
	 * @param  bool     $test
	 * @return bool
	 */
	public function offline_validate_address( $address, $currency, $test = false ) {
		$valid = apply_filters( "cw_validate_address_$currency", $address );
		if ( is_bool( $valid ) ) {
			return $valid;
		}

		// Minimum length
		if ( ! $address || strlen( $address ) < 25 ) {
			return false;
		}

		switch ( $currency ) {
			default:
			case 'BTC':
				$network = NetworkFactory::bitcoin();
				break;
			case 'BCH':
				$network = NetworkFactory::bitcoin();
				break;
			case 'LTC':
				$network = NetworkFactory::Litecoin();
				break;
			case 'DOGE':
				$network = NetworkFactory::Dogecoin();
				break;
			case 'BLK':
				$network = BitWasp\Bitcoin\Network\CW_NetworkFactory::blackcoin();
				break;
		}

		$ac       = new AddressCreator();
		$is_valid = true;
		try {
			$parsed_address = $ac->fromString( $address, $network );
		} catch ( Exception $e ) {
			$is_valid = false;
		}
		if ( $is_valid ) {
			return true;
		}

		// Legacy validation
		try {
			$base_58_decoded = Base58::decode( $address )->getHex();
		} catch ( Exception $e ) {
			CW_AdminMain::cryptowoo_log_data( 0, __FUNCTION__, $address . ' ' . $e->getMessage(), 'error' );
			return false;
		}

		$validation['base58_decode'] = $base_58_decoded;
		$validation['base58_prefix'] = substr( $validation['base58_decode'], 0, 2 );

		// Address prefixes @todo refactor
		$prefix = array(
			'BTC'               => '00',
			'BTC_MULTISIG'      => '05',
			'BTCTEST'           => '6f',
			'BTCTEST_MULTISIG'  => 'c4',
			'BCH'               => '00',
			'BCH_MULTISIG'      => '05',
			'DOGE'              => '1e',
			'DOGE_MULTISIG'     => '16',
			'DOGETEST'          => '71',
			'DOGETEST_MULTISIG' => 'c4',
			'LTC'               => '30',
			'LTC_MULTISIG'      => '05',
			'BLK'               => '19',
			'BLK_MULTISIG'      => '55',
		);

		$prefix = apply_filters( 'address_prefixes', $prefix );

		// $detect_coin                 = array_search($validation['base58_prefix'], $prefix);
		// $validation['detected_coin'] = false !== $detect_coin ? str_replace('_MULTISIG', '', $detect_coin) : false;

		// Prepare multisig prefix array key
		$currency_ms = sprintf( '%s_MULTISIG', $currency );

		// Pay to pubkey hash
		$is_p2pkh = isset( $prefix[ $currency ] ) && $prefix[ $currency ] === $validation['base58_prefix'];

		// Pay to script hash
		$is_p2sh = isset( $prefix[ $currency_ms ] ) && $prefix[ $currency_ms ] === $validation['base58_prefix'];

		// Detect coin
		$validation['detected_coin'] = $is_p2pkh || $is_p2sh ? $currency : false;

		// Matching address prefix?
		if ( ! $validation['detected_coin'] ) {
			return false;
		}

		$address               = hex2bin( $validation['base58_decode'] );
		$validation['address'] = bin2hex( $address );
		if ( strlen( $address ) != 25 ) {
			return false;
		}
		$checksum   = substr( $address, 21, 4 );
		$rawAddress = substr( $address, 0, 21 );

		$validation['rawAddress'] = bin2hex( $rawAddress );
		$validation['sha256']     = hash( 'sha256', $rawAddress );

		$validation['checksum'] = bin2hex( $checksum );
		$validation['sha256_2'] = hash( 'sha256', hex2bin( $validation['sha256'] ) );
		$validation['is_valid'] = substr( hex2bin( $validation['sha256_2'] ), 0, 4 ) == $checksum;

		if ( $test ) {
			return $validation;
		} else {
			return (bool) $validation['is_valid'];
		}

	}

	/**
	 * Validate integrity of API keys and master public keys via hash in uploadsdir
	 *
	 * @return array
	 */
	function cryptowoo_api_check() {

		$options = cw_get_options();

		$keys = array( NONCE_SALT );

		$foo = array( // @todo refactor
			'cryptowoo_btc_api',
			'cryptowoo_doge_api',
			'cryptowoo_ltc_api',
			'cryptowoo_btctest_api',
			'cryptowoo_dogetest_api',
			'cryptowoo_btc_mpk',
			'cryptowoo_doge_mpk',
			'cryptowoo_doge_mpk_xpub',
			'cryptowoo_ltc_mpk',
			'cryptowoo_ltc_mpk_xpub',
			'cryptowoo_btctest_mpk',
			'cryptowoo_dogetest_mpk',
			'cryptowoo_blk_mpk',
			'cryptowoo_blk_mpk_xpub',
			'safe_btc_address',
			'safe_ltc_address',
			'safe_doge_address',
		);

		for ( $i = 0; $i < count( $foo ); $i++ ) {
			$keys[] = cw_get_option( $foo[ $i ] ) ?: '0';
		}

		$result          = array();
		$result['valid'] = false;
		$filename        = cw_get_option( 'cw_filename' );

		if ( ! empty( $keys ) && $filename ) {
			$result['new_hash'] = hash_hmac( 'sha256', print_r( $keys, true ), AUTH_SALT );

			$result['old_hash'] = file_get_contents( trailingslashit( wp_upload_dir()['basedir'] ) . sanitize_file_name( $filename ), null, null, 0, 64 );

			// Returns < 0 if str1 is less than str2; > 0 if str1 is greater than str2, and 0 if they are equal.
			$result['valid'] = hash_equals( $result['old_hash'] ?: '', $result['new_hash'] ?: '' ) ? true : false;

		} else {
			$result['valid'] = true;
		}
		return $result;
	}


	/**
	 * Validate Master Public Key
	 *
	 * Use base58 to validate mpk:
	 * xpubs are base58 encoded with the standard 32 bit checksum at the end.
	 * So if you have a validation routine for bitcoin addresses you already have almost a validation routine for xpubs.
	 * They differ in the length (78 instead of 21 bytes excluding checksum) and in the prefix (0x0488B21E instead of 0x00 for mainnet).
	 *
	 * @param  $mpk
	 * @param  $mand_mpk_prefix
	 * @param  $mand_base58_prefix
	 * @return bool
	 */
	public function validate_mpk( $mpk, $mand_mpk_prefix, $mand_base58_prefix ) {

		$validation['mpk_prefix'] = $mpk_prefix = substr( $mpk, 0, 4 );
		if ( strcasecmp( $mpk_prefix, $mand_mpk_prefix ) !== 0 ) {
			return false; // $validation;
		}

		$validation['base58_decode'] = Base58::decode( $mpk )->getHex();
		$validation['base58_prefix'] = substr( $validation['base58_decode'], 0, 8 );

		if ( strcasecmp( $validation['base58_prefix'], $mand_base58_prefix ) !== 0 ) {
			return false; // $validation;
		}

		$validation['mpk']        = $mpk = hex2bin( $validation['base58_decode'] );
		$validation['strlen_mpk'] = $strlen_mpk = strlen( $mpk );
		if ( (int) $strlen_mpk !== 82 ) {
			return false; // $validation;
		}
		$validation['checksum'] = $checksum = substr( $mpk, 78, 4 );
		$validation['awMPK']    = $rawMPK = substr( $mpk, 0, 78 );
		$validation['sha256']   = $sha256 = hash( 'sha256', $rawMPK );
		$validation['sha256_2'] = $sha256 = hash( 'sha256', hex2bin( $sha256 ) );

		return substr( hex2bin( $sha256 ), 0, 4 ) == $checksum;
	}

}
