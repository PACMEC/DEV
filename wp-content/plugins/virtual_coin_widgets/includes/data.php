<?php defined( 'VCW_INDEX' ) or die( '' );

if(!class_exists('VCW_Data'))
{
    class VCW_Data
    {
        static protected $data;

        static public function init()
        {

        }

        static protected function request($url)
        {
            $request = wp_remote_get($url);

            if(is_wp_error($request)) {
                return false;
            }

            return json_decode(wp_remote_retrieve_body($request), true);
        }

        static public function coin($id)
        {
            $transient  = "vcw_coingecko_$id";
            $coin       = get_transient($transient);

            if($coin === false) {
                $info = self::request("https://api.coingecko.com/api/v3/coins/$id");

                if(is_array($info)) {
                    $change_1h = array_key_exists('usd', $info['market_data']['price_change_percentage_1h_in_currency']) ?
                        $info['market_data']['price_change_percentage_1h_in_currency']['usd'] : null;

                    $coin = array(
                        'symbol'            => strtoupper($info['symbol']),
                        'name'              => $info['name'],
                        'id'                => $info['id'],
                        'image_thumb'       => $info['image']['thumb'],
                        'image_small'       => $info['image']['small'],
                        'image_large'       => $info['image']['large'],
                        'price'             => $info['market_data']['current_price'],
                        'change_1h'         => $change_1h,
                        'change_24h'        => floatval($info['market_data']['price_change_percentage_24h']),
                        'change_7d'         => floatval($info['market_data']['price_change_percentage_7d']),
                    );

                    set_transient($transient, maybe_serialize($coin), 60);
                    return $coin;
                }

                return null;
            }

            return maybe_unserialize($coin);
        }

        static public function rates()
        {
            $transient  = 'vcw_coingecko_rates';
            $rates      = get_transient($transient);

            if($rates === false) {
                $rates = self::request('https://api.coingecko.com/api/v3/exchange_rates');

                if(isset($rates['rates'])) {
                    $rates          = $rates['rates'];
                    $new_rates      = array();
                    $btcusd_rate    = $rates['usd']['value'];

                    foreach ($rates as $code => $rate) {
                        $code = strtoupper($code);

                        if($code === 'BTC') $rate['value'] = 1;

                        $rate['value'] = $rate['value']/$btcusd_rate;

                        $new_rates[$code] = $rate;
                    }

                    set_transient($transient, maybe_serialize($new_rates), 60);
                    return $new_rates;
                }

                return null;
            }

            return maybe_unserialize($rates);
        }

        static protected function fetchTopCoins()
        {
            $transient  = 'vcw_coingecko_top';
            $top        = get_transient($transient);

            if($top === false) {
                $top = self::request('https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&per_page=100&order=market_cap_desc');

                if(is_array($top)) {
                    set_transient($transient, maybe_serialize($top), 5*60);
                    return $top;
                }

                return array();
            }

            return maybe_unserialize($top);
        }

        static public function coinList()
        {
            $transient  = 'vcw_coingecko_list';
            $list       = get_transient($transient);

            if($list === false) {
                $list = self::request('https://api.coingecko.com/api/v3/coins/list');

                if(is_array($list)) {

                    usort($list, function ($a,$b) {
                        return strcasecmp($a['name'], $b['name']);
                    });

                    set_transient($transient, maybe_serialize($list), 5*60);
                    return $list;
                }

                return array();
            }

            return maybe_unserialize($list);
        }

        static public function topCoinsIds($count)
        {
            $top = self::fetchTopCoins();

            return array_map(function ($coin) {
                return $coin['id'];
            }, array_slice($top,0, $count));
        }

        static public function rate($code)
        {
            $rates = self::rates();

            return empty($rates[$code]) ?
                null : $rates[$code];
        }

        static public function fx($from, $to, $value)
        {
            $rates = self::rates();

            if(empty($rates[$from]) || empty($rates[$to]) || !is_numeric($value))
                return '?';

            $rate = $from === 'USD' ?
                $rates[$to]['value'] : // direct rate
                $rates[$to]['value'] / $rates[$from]['value']; // indirect rate

            return $rate * $value;
        }

        static public function requestAllRates()
        {
            $coin_list  = self::coinList();
            $rates      = self::rates();

            if(empty($coin_list))
                return array();

            $pages      = ceil(count($coin_list)/250);
            $all_rates  = array();

            foreach ($rates as $code => $rate) {
                $all_rates[$code] = array(
                    'value' => $rate['value'],
                    'name'  => $rate['name']
                );
            }

            for($p=1; $p<=$pages; $p++) {
                $coins = self::request("https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&per_page=250&page=$p");

                if(is_array($coins)) {
                    foreach ($coins as $coin) {
                        $code       = strtoupper($coin['symbol']);
                        $rate_val   = floatval($coin['current_price']);

                        if(empty($rates[$code]) && $rate_val) {
                            $all_rates[$code] = array(
                                'value' => 1/$rate_val,
                                'name'  => $coin['name']
                            );
                        }
                    }
                }
                else break;
            }

            return $all_rates;
        }

        static public function allCurrenciesRates()
        {
            $transient  = 'vcw_all_currencies';
            $all_rates  = get_transient($transient);

            if($all_rates === false) {
                $all_rates = self::requestAllRates();

                if(empty($all_rates))
                    return array();

                set_transient($transient, maybe_serialize($all_rates), 5*60);
                return $all_rates;
            }

            return maybe_unserialize($all_rates);
        }
    }

    VCW_Data::init();
}


