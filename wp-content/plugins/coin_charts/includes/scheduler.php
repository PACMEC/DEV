<?php

defined( 'CCHARTS_INDEX' ) or die( '' );

class CCharts_Scheduler {

    static public function load()
    {
        add_filter('cron_schedules', array(get_class(),'addScheduleOption'));
        register_activation_hook(CCHARTS_INDEX, array(get_class(),'activation'));
        register_deactivation_hook(CCHARTS_INDEX, array(get_class(),'deactivation'));
        add_action(CCharts_Constants::$schedule_event, array(get_class(),'ticker'));
    }

    static public function addScheduleOption($schedules)
    {
        if(!isset($schedules["1min"])){
            $schedules["1min"] = array(
                'interval' => 60,
                'display' => __('Once every minute'));
        }
        return $schedules;
    }

    static public function deactivation()
    {
        wp_clear_scheduled_hook(CCharts_Constants::$schedule_event);
    }

    static public function activation()
    {
        if (! wp_next_scheduled ( CCharts_Constants::$schedule_event )) {
            wp_schedule_event(time(), '1min', CCharts_Constants::$schedule_event);
        }
    }

    static public function ticker()
    {
        asort(CCharts_Database::$updates);

        $count = 0;

        foreach (CCharts_Database::$updates as $pair => $last_update){

            if($count++ == CCharts_Constants::$max_historical_calls){
                break;
            }

            $data = CCharts_Requests::pairChartData($pair,$last_update+1);

            if(is_array($data)){

                if(isset($data['error']) || (isset($data[0]) && $data[0]['date'] == 0)){
                    continue;
                }

                $rows = array();

                foreach ($data as $entry){
                    $rows[] = array(
                        $entry['date'],
                        "'".$pair."'",
                        $entry['open'],
                        $entry['close'],
                        $entry['low'],
                        $entry['high'],
                        $entry['weightedAverage']
                    );
                }
                $last_row = $rows[count($rows)-1];
                CCharts_Database::insertHistoryRows($rows);
                CCharts_Database::setUpdate($pair,$last_row[0]);
            }
        }

    }

}

CCharts_Scheduler::load();