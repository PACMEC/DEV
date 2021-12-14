<?php

defined( 'CCHARTS_INDEX' ) or die( '' );

class CCharts_Database {


    static $history_table_name;
    static $history_table_columns;

    static $updates;
    static $db_current_version;

    static public function load()
    {
        global $wpdb;

        self::$history_table_name = $wpdb->prefix . CCharts_Constants::$history_table_suffix;
        self::$history_table_columns = array(
            'time_','pair_','open_','close_','low_','high_','average_'
        );

        self::getUpdates();
        self::getDBVersion();
        self::checkDBVersion();
    }

    static protected function getDBVersion()
    {
        self::$db_current_version = get_option(CCharts_Constants::$db_version_option, null);
    }

    static protected function saveDBVersion()
    {
        update_option(CCharts_Constants::$db_version_option, CCharts_Constants::$db_version);
    }

    static public function checkDBVersion()
    {
        if(self::$db_current_version != CCharts_Constants::$db_version){
            self::createHistoryTable();
            self::resetUpdates();
            self::saveDBVersion();
        }
    }

    static public function insertRowsToTable($table, $cols, $rows)
    {
        global $wpdb;

        $last_row = count($rows) - 1;
        $cols_query = implode(',', $cols);

        $query = "REPLACE INTO $table ($cols_query) VALUES ";

        foreach ($rows as $i => $row){
            $values = implode(',',$row);
            $query .= "($values)";

            if($i != $last_row){
                $query .= ',';
            }
        }

        return $wpdb->query($query);
    }

    static public function createHistoryTable()
    {

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $h_table = self::$history_table_name;

        $wpdb->query("DROP TABLE IF EXISTS $h_table; ");

        $h_sql = "CREATE TABLE $h_table (
          time_ INT(11) NOT NULL,
          pair_ VARCHAR(30) NOT NULL,
          open_ FLOAT NOT NULL,
          close_ FLOAT NOT NULL,
          low_ FLOAT NOT NULL,
          high_ FLOAT NOT NULL,
          average_ FLOAT NOT NULL,
          CONSTRAINT PK_CCHARTS_HISTORICAL PRIMARY KEY (time_,pair_)
        ) $charset_collate;";


        return $wpdb->query($h_sql);
    }

    static public function getRateHistoricalData($pair, $start, $end)
    {

        global $wpdb;
        $h_table = self::$history_table_name;

        $query = $pair == 'USDT_BTC' ?
            "SELECT time_ as time_unix, average_ as avg_value FROM $h_table WHERE pair_ = 'USDT_BTC' AND time_ >= $start AND time_ <= $end ORDER BY time_;" :
            "SELECT t1.time_ as time_unix, t1.average_*t2.average_ as avg_value FROM (SELECT time_,average_ FROM $h_table WHERE pair_='$pair') as t1 INNER JOIN (SELECT time_,average_ FROM $h_table WHERE pair_='USDT_BTC' AND time_ >= $start AND time_ <= $end) as t2 ON t1.time_ = t2.time_ ORDER BY t1.time_";

        return $wpdb->get_results($query,ARRAY_A);
    }

    static public function getLastUpdatedPair()
    {
        global $wpdb;
        $h_table = self::$history_table_name;

        $query = "SELECT pair_,MAX(time_) as last_update FROM $h_table GROUP BY pair_ ORDER BY last_update ASC;";

        return $wpdb->get_row($query,ARRAY_A);
    }

    static public function insertHistoryRows($rows)
    {
        return self::insertRowsToTable(self::$history_table_name,self::$history_table_columns,$rows);
    }

    static public function resetPair($pair){
        global $wpdb;
        $h_table = self::$history_table_name;

        $query = "DELETE FROM $h_table WHERE pair_ = '$pair';";
        $wpdb->query($query);
        self::setUpdate($pair,0);
    }

    static public function resetAllPairs(){
        global $wpdb;
        $h_table = self::$history_table_name;

        $query = "DELETE FROM $h_table WHERE 1;";
        $wpdb->query($query);
        self::resetUpdates();
    }

    static public function resetUpdates()
    {
        self::$updates = array();

        foreach (CCharts_Constants::$pairs as $pair){
            self::$updates[$pair] = 0;
        }
        self::saveUpdates();
    }

    static public function getUpdates($def = null)
    {
        self::$updates = maybe_unserialize(get_option(CCharts_Constants::$updates_option, $def));
    }

    static public function saveUpdates()
    {
        update_option(CCharts_Constants::$updates_option, maybe_serialize(self::$updates));
    }

    static public function setUpdate($pair, $time){

        self::$updates[$pair] = $time;
        self::saveUpdates();
    }
}

CCharts_Database::load();