<?php

defined( 'CCHARTS_INDEX' ) or die( '' );

class CCharts_Requests {

    static protected function requestGET($url){
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response,true);
    }

    static public function pairChartData($pair, $start = 0, $end = 9999999999)
    {
        $period = CCharts_Constants::$candle_period;
        $url = "https://poloniex.com/public?command=returnChartData&currencyPair=$pair&start=$start&end=$end&period=$period";
        return self::requestGET($url);
    }

}