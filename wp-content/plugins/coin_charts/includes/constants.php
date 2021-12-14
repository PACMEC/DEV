<?php

defined( 'CCHARTS_INDEX' ) or die( '' );

class CCharts_Constants {

    static $text_domain = 'ccharts-text';

    // Database
    static $history_table_suffix = 'ccharts_history';
    static $rates_table_suffix = 'ccharts_rates';
    static $updates_option = 'ccharts_updates';
    static $db_version_option = 'ccharts_db_version';
    static $db_version = '1.2';

    // Requests
    static $candle_period = 7200;

    // Scheduler
    static $schedule_event = 'cchart_ticker';
    static $rate_first_time = 1388534400;
    static $max_historical_calls = 3;
    static $historical_call_sleep = 0;

    // Other
    static $pairs;
    static $currencies;

    static public function load()
    {
        self::$pairs = array(
            'USDT_BTC',
            'BTC_ETH',
            'BTC_XRP',
            'BTC_ETC',
            'BTC_GNT',
            'BTC_DOGE',
            'BTC_STR',
            'BTC_XEM',
            'BTC_LTC',
            'BTC_XMR',
            'BTC_DGB',
            'BTC_SC',
            'BTC_BTS',
            'BTC_ZEC',
            'BTC_DASH',
            'BTC_BCN',
            'BTC_FCT',
            'BTC_BTM',
            'BTC_STRAT',
            'BTC_STEEM',
            'BTC_REP',
            'BTC_LSK',
            'BTC_NXT',
            'BTC_SYS',
            'BTC_MAID',
            'BTC_ARDR',
            'BTC_GAME',
            'BTC_DCR',
            'BTC_GNO',
            'BTC_AMP',
            'BTC_LBC',
            'BTC_CLAM',
            'BTC_VTC',
            'BTC_BURST',
            'BTC_RIC',
            'BTC_NAV',
            'BTC_PINK',
            'BTC_PPC',
            'BTC_EXP',
            'BTC_XCP',
            'BTC_BTCD',
            'BTC_EMC2',
            'BTC_VIA',
            'BTC_NXC',
            'BTC_NEOS',
            'BTC_FLO',
            'BTC_PASC',
            'BTC_RADS',
            'BTC_POT',
            'BTC_BLK',
            'BTC_BELA',
            'BTC_BCY',
            'BTC_FLDC',
            'BTC_XPM',
            'BTC_NMC',
            'BTC_GRC',
            'BTC_XVC',
            'BTC_XBC',
            'BTC_HUC',
            'BTC_VRC',
            'BTC_OMNI',
            'BTC_SBD',
            'BTC_BCH',
            'BTC_ZRX',
            'BTC_CVC',
            'BTC_OMG',
            'BTC_STORJ',
            'BTC_GAS'
        );

        self::$currencies = array(
            'BTC' => array(
                'name' => 'Bitcoin',
                'pair' => 'USDT_BTC'
            ),
            'ETH' => array(
                'name' => 'Ethereum',
                'pair' => 'BTC_ETH'
            ),
            'XRP' => array(
                'name' => 'Ripple',
                'pair' => 'BTC_XRP'
            ),
            'ETC' => array(
                'name' => 'Ethereum Classic',
                'pair' => 'BTC_ETC'
            ),
            'GNT' => array(
                'name' => 'Golem',
                'pair' => 'BTC_GNT'
            ),
            'DOGE' => array(
                'name' => 'Dogecoin',
                'pair' => 'BTC_DOGE'
            ),
            'STR' => array(
                'name' => 'Stellar',
                'pair' => 'BTC_STR'
            ),
            'XEM' => array(
                'name' => 'Nem',
                'pair' => 'BTC_XEM'
            ),
            'LTC' => array(
                'name' => 'Litecoin',
                'pair' => 'BTC_LTC'
            ),
            'XMR' => array(
                'name' => 'Monero',
                'pair' => 'BTC_XMR'
            ),
            'DGB' => array(
                'name' => 'DigiByte',
                'pair' => 'BTC_DGB'
            ),
            'SC' => array(
                'name' => 'Siacoin',
                'pair' => 'BTC_SC'
            ),
            'BTS' => array(
                'name' => 'BitShares',
                'pair' => 'BTC_BTS'
            ),
            'ZEC' => array(
                'name' => 'Zcash',
                'pair' => 'BTC_ZEC'
            ),
            'DASH' => array(
                'name' => 'Dash',
                'pair' => 'BTC_DASH'
            ),
            'BCN' => array(
                'name' => 'Bytecoin',
                'pair' => 'BTC_BCN'
            ),
            'FCT' => array(
                'name' => 'Factom',
                'pair' => 'BTC_FCT'
            ),
            'BTM' => array(
                'name' => 'Bitmark',
                'pair' => 'BTC_BTM'
            ),
            'STRAT' => array(
                'name' => 'Stratis',
                'pair' => 'BTC_STRAT'
            ),
            'STEEM' => array(
                'name' => 'Steem',
                'pair' => 'BTC_STEEM'
            ),
            'REP' => array(
                'name' => 'Augur',
                'pair' => 'BTC_REP'
            ),
            'LSK' => array(
                'name' => 'Lisk',
                'pair' => 'BTC_LSK'
            ),
            'NXT' => array(
                'name' => 'Nxt',
                'pair' => 'BTC_NXT'
            ),
            'SYS' => array(
                'name' => 'SysCoin',
                'pair' => 'BTC_SYS'
            ),
            'MAID' => array(
                'name' => 'MaidSafeCoin',
                'pair' => 'BTC_MAID'
            ),
            'ARDR' => array(
                'name' => 'Ardor',
                'pair' => 'BTC_ARDR'
            ),
            'GAME' => array(
                'name' => 'GameCredits',
                'pair' => 'BTC_GAME'
            ),
            'DCR' => array(
                'name' => 'Decred',
                'pair' => 'BTC_DCR'
            ),
            'GNO' => array(
                'name' => 'Gnosis',
                'pair' => 'BTC_GNO'
            ),
            'AMP' => array(
                'name' => 'Synereo',
                'pair' => 'BTC_AMP'
            ),
            'LBC' => array(
                'name' => 'LBRY Credits',
                'pair' => 'BTC_LBC'
            ),
            'CLAM' => array(
                'name' => 'Clams',
                'pair' => 'BTC_CLAM'
            ),
            'VTC' => array(
                'name' => 'Vertcoin',
                'pair' => 'BTC_VTC'
            ),
            'BURST' => array(
                'name' => 'Burst',
                'pair' => 'BTC_BURST'
            ),
            'RIC' => array(
                'name' => 'Riecoin',
                'pair' => 'BTC_RIC'
            ),
            'NAV' => array(
                'name' => 'NAV Coin',
                'pair' => 'BTC_NAV'
            ),
            'PINK' => array(
                'name' => 'PinkCoin',
                'pair' => 'BTC_PINK'
            ),
            'PPC' => array(
                'name' => 'Peercoin',
                'pair' => 'BTC_PPC'
            ),
            'EXP' => array(
                'name' => 'Expanse',
                'pair' => 'BTC_EXP'
            ),
            'XCP' => array(
                'name' => 'Counterparty',
                'pair' => 'BTC_XCP'
            ),
            'BTCD' => array(
                'name' => 'BitcoinDark',
                'pair' => 'BTC_BTCD'
            ),
            'EMC2' => array(
                'name' => 'Einsteinium',
                'pair' => 'BTC_EMC2'
            ),
            'VIA' => array(
                'name' => 'Viacoin',
                'pair' => 'BTC_VIA'
            ),
            'NXC' => array(
                'name' => 'Nexium',
                'pair' => 'BTC_NXC'
            ),
            'NEOS' => array(
                'name' => 'NeosCoin',
                'pair' => 'BTC_NEOS'
            ),
            'FLO' => array(
                'name' => 'FlorinCoin',
                'pair' => 'BTC_FLO'
            ),
            'PASC' => array(
                'name' => 'Pascal Coin',
                'pair' => 'BTC_PASC'
            ),
            'RADS' => array(
                'name' => 'Radium',
                'pair' => 'BTC_RADS'
            ),
            'POT' => array(
                'name' => 'PotCoin',
                'pair' => 'BTC_POT'
            ),
            'BLK' => array(
                'name' => 'BlackCoin',
                'pair' => 'BTC_BLK'
            ),
            'BELA' => array(
                'name' => 'BelaCoin',
                'pair' => 'BTC_BELA'
            ),
            'BCY' => array(
                'name' => 'Bitcrystals',
                'pair' => 'BTC_BCY'
            ),
            'FLDC' => array(
                'name' => 'FoldingCoin',
                'pair' => 'BTC_FLDC'
            ),
            'XPM' => array(
                'name' => 'Primecoin',
                'pair' => 'BTC_XPM'
            ),
            'NMC' => array(
                'name' => 'Namecoin',
                'pair' => 'BTC_NMC'
            ),
            'GRC' => array(
                'name' => 'GridCoin',
                'pair' => 'BTC_GRC'
            ),
            'XVC' => array(
                'name' => 'Vcash',
                'pair' => 'BTC_XVC'
            ),
            'XBC' => array(
                'name' => 'Bitcoin Plus',
                'pair' => 'BTC_XBC'
            ),
            'HUC' => array(
                'name' => 'HunterCoin',
                'pair' => 'BTC_HUC'
            ),
            'VRC' => array(
                'name' => 'VeriCoin',
                'pair' => 'BTC_VRC'
            ),
            'OMNI' => array(
                'name' => 'Omni',
                'pair' => 'BTC_OMNI'
            ),
            'SBD' => array(
                'name' => 'Steem Dollars',
                'pair' => 'BTC_SBD'
            ),
            'BCH' => array(
                'name' => 'Bitcoin Cash',
                'pair' => 'BTC_BCH'
            ),
            'ZRX' => array(
                'name' => '0x',
                'pair' => 'BTC_ZRX'
            ),
            'CVC' => array(
                'name' => 'Civic',
                'pair' => 'BTC_CVC'
            ),
            'OMG' => array(
                'name' => 'OmiseGO',
                'pair' => 'BTC_OMG'
            ),
            'STORJ' => array(
                'name' => 'Storj',
                'pair' => 'BTC_STORJ'
            ),
            'GAS' => array(
                'name' => 'Gas',
                'pair' => 'BTC_GAS'
            )
        );
    }
}

CCharts_Constants::load();