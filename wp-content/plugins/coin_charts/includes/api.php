<?php

defined( 'CCHARTS_INDEX' ) or die( '' );

class CCharts_API {

    static protected function ajaxAction($action, $method, $visitor = true, $admin = true)
    {
        if($admin) add_action( "wp_ajax_$action", array(get_class(), $method) );
        if($visitor) add_action( "wp_ajax_nopriv_$action", array(get_class(), $method) );
    }

    static public function load()
    {
        if ( is_admin() ) {
            self::ajaxAction('cc_chart_data','chartData');
            self::ajaxAction('cc_reset_data','resetData',false);
            self::ajaxAction('cc_update_table','updateTable',false);
        }

    }

    static public function timeRange($interval){
        $t = time();

        switch ($interval){
            case '1d':
                return array($t-60*60*24, $t);
            case '7d':
                return array($t-60*60*24*7, $t);
            case '1m':
                return array($t-60*60*24*30, $t);
            case '3m':
                return array($t-60*60*24*90, $t);
            case '6m':
                return array($t-60*60*24*180, $t);
            case '1y':
                return array($t-60*60*24*365, $t);
            case 'all':
            default:
                return array(0,$t);
        }
    }

    static protected function chartDataPoints($pair,$start,$end){
        $points = array();
        $pair_rows = CCharts_Database::getRateHistoricalData($pair,$start,$end);

        if(is_array($pair_rows)){

            $count_rows = count($pair_rows);
            $div = ceil($count_rows/300);

            for ($i=0;$i<$count_rows;$i+=$div){
                $average = 0;

                for($j=0;$j<$div;$j++){
                    if(!isset($pair_rows[$i+$j]))
                        break;
                    $average += $pair_rows[$i+$j]['avg_value'];
                }
                $average /= $j;

                $last = $i+$j-1;

                $last_row = $last >= $count_rows ? $count_rows-1 : $last;

                $points[] = array($pair_rows[$last_row]['time_unix'],$average);
            }
        }
        return $points;
    }

    static public function chartData()
    {

        $points = null;
        $name = null;

        if(isset($_POST['chart_options']) && is_array($_POST['chart_options'])){
            $chart_options = $_POST['chart_options'];

            if(isset($chart_options['symbol']) &&
                isset(CCharts_Constants::$currencies[$chart_options['symbol']]) &&
                isset($chart_options['interval'])){

                $currency = CCharts_Constants::$currencies[$chart_options['symbol']];
                $pair = $currency['pair'];
                $name = $currency['name'];
                $interval = $chart_options['interval'];

                $time_range = self::timeRange($interval);
                $points = self::chartDataPoints($pair,$time_range[0],$time_range[1]);
            }
        }

        wp_send_json(array(
            'points' => $points,
            'name' => $name
        ));
    }

    static public function updateTable(){
        $table = array();

        foreach (CCharts_Constants::$currencies as $symbol => $info){
            $last_update = CCharts_Database::$updates[$info['pair']];

            $table[] = array(
                'symbol' => $symbol,
                'name' => $info['name'],
                'last_update' => $last_update == 0 ? 'Waiting' : date('Y-m-d H:i',$last_update)
            );
        }


        wp_send_json(array(
            'table' => $table
        ));
    }

    static public function resetData(){

        $reset_target = null;

        if (isset($_POST['reset_target']) && current_user_can( 'manage_options' ) ) {
            $reset_target = $_POST['reset_target'];

            if($reset_target === 'all_pairs'){
                CCharts_Database::resetAllPairs();
            }
            else if(isset(CCharts_Constants::$currencies[$reset_target])){

                $pair = CCharts_Constants::$currencies[$reset_target]['pair'];

                CCharts_Database::resetPair($pair);
            }
        }

        wp_send_json(array(
            'target' => $reset_target
        ));
    }
}

CCharts_API::load();