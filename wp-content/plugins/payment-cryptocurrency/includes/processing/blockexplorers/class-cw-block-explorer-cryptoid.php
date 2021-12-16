<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * CryptoID Block Explorer API Class
 *
 * @category   CryptoPay
 * @package    OrderProcessing
 * @subpackage BlockExplorerAPI
 * @author     CryptoPay AS
 */
class CW_Block_Explorer_CryptoID extends CW_Block_Explorer_API_CryptoID {


	/**
	 *
	 * Get the block explorer API URL with format
	 *
	 * @return string
	 */
	protected function get_base_url() : string {
		return 'https://chainz.cryptoid.info/';
	}

	/**
	 *
	 * Get the block explorer supported currencies
	 *
	 * @return string[]
	 */
	protected function get_supported_currencies() : array {
		return array(
			'BTC',
			'1337',
			'2GIVE',
			'42',
			'ADC',
			'AGM',
			'AIAS',
			'ALN',
			'ARCO',
			'AREPA',
			'ARG',
			'ARI',
			'AXE',
			'B3',
			'BAY',
			'BBK',
			'BBP',
			'BBTC',
			'BCC',
			'BCCX',
			'BCZ',
			'BEAN',
			'BLC',
			'BLITZ',
			'BLK',
			'BLOCK',
			'BLU',
			'BNODE',
			'BOLI',
			'BRO',
			'BSD',
			'BTDX',
			'BTX',
			'BUK2',
			'BXT',
			'BYC',
			'BYND',
			'BYTZ',
			'CACHE',
			'CANN',
			'CAPS',
			'CBX',
			'CHBT',
			'CIV',
			'CLOAK',
			'CMM',
			'CNO',
			'COLX',
			'CORG',
			'CPS',
			'CRAVE',
			'CRT',
			'CRYPT',
			'CRW',
			'CURE',
			'DAL',
			'DASH',
			'DEM',
			'DGB',
			'DGC',
			'DIME',
			'DIVI',
			'DMD',
			'D',
			'DMB',
			'DONU',
			'DONU-OLD',
			'DOPE',
			'DVC',
			'EAC',
			'EC',
			'ECC',
			'EFL',
			'EGC',
			'ELT',
			'EMC2',
			'EMD',
			'ENRG',
			'ENT',
			'ENY',
			'ERC',
			'EST',
			'EXCL',
			'FTC',
			'FUNK',
			'GALI',
			'GAP',
			'GCR',
			'GENX',
			'GLC',
			'GLT',
			'GP',
			'GPL2',
			'GRN',
			'GRS',
			'GRS-TEST',
			'GRWI',
			'GUN',
			'GXX',
			'HBN',
			'HTH',
			'HTML',
			'HTML5',
			'I0C',
			'IC',
			'ICN',
			'IFC',
			'IFLT',
			'IMG',
			'INFX',
			'INSN',
			'IOC',
			'ION',
			'IXC',
			'J',
			'KED',
			'KFX',
			'KLKS',
			'KOBO',
			'KORE',
			'LANA',
			'LCC',
			'LINX',
			'LIT',
			'LTC',
			'LTNCG',
			'LTV',
			'LUX',
			'LYNX',
			'LYRA',
			'MANNA',
			'MARKS',
			'MEC',
			'MNC',
			'MOON',
			'MRC',
			'MUE',
			'N8V',
			'NACHO',
			'NAV',
			'NETKO',
			'NEVA',
			'NIX',
			'NOBL',
			'NOR',
			'NORT',
			'NOTE',
			'NPC',
			'NTRN',
			'NYC',
			'ODIN',
			'OK',
			'ONION',
			'ORB',
			'OZC',
			'PAK',
			'PART',
			'PCN',
			'PHO',
			'PHR',
			'PIGGY',
			'PINK',
			'PIVX',
			'PND',
			'PNY',
			'POT',
			'PPC',
			'PPC-TEST',
			'PTC',
			'PURA',
			'PUT',
			'PUT-OLD',
			'PWRB',
			'QAC',
			'QBT',
			'QRK',
			'RADS',
			'RIC',
			'ROGER',
			'SCIFI',
			'SCOL',
			'SCRIBE',
			'SLG',
			'SLM',
			'SLR',
			'SLS',
			'SMLY',
			'SPK',
			'SPRTS',
			'STK',
			'STRAT',
			'STRAT-TEST',
			'SUPER',
			'SWAMP',
			'SWING',
			'SXC',
			'SYS',
			'SYS-OLD',
			'TAJ',
			'TALK-OLD',
			'TAO',
			'TES',
			'TOA',
			'TREX',
			'TRC',
			'TROLL',
			'TRUMP',
			'TRUST',
			'TZC',
			'UFO',
			'UMO',
			'UNO',
			'UTIP',
			'VEIL',
			'VLS',
			'VGS',
			'VESTX',
			'VIA',
			'VRC',
			'VRM',
			'VTA',
			'VTC',
			'WC',
			'WDC',
			'WEX',
			'WGR',
			'X0Z',
			'XBC',
			'XC',
			'XJO',
			'XMG',
			'XMY',
			'XP',
			'XSPEC',
			'XST',
			'XVP',
			'XZC',
			'ZEIT',
			'ZET',
			'ZLN',
			'ZNZ',
		);
	}
}
