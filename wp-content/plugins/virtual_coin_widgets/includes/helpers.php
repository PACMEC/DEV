<?php defined( 'VCW_INDEX' ) or die( '' );

if(!class_exists('VCW_Helpers')) {

    class VCW_Helpers
    {

        static protected $number_dec_point = '.';
        static protected $number_thousands_sep = ',';


        static public function number_format($number, $precision)
        {
            return number_format($number, $precision, self::$number_dec_point, self::$number_thousands_sep);
        }

        static public function price_format($value)
        {
            if(!is_numeric($value)) return '?';

            $number = floatval($value);
            $exp = log10($number); // get log10 of number for decimal places control

            if($exp >= 4) $price = self::number_format($number, 0);
            elseif ($exp >= 3) $price = self::number_format($number, 1);
            elseif ($exp >= 2) $price = self::number_format($number, 2);
            elseif ($exp >= 1) $price = self::number_format($number, 2);
            elseif ($exp >= 0) $price = self::number_format($number, 3);
            elseif ($exp >= -1) $price = self::number_format($number, 4);
            elseif ($exp >= -2) $price = self::number_format($number, 5);
            elseif ($exp >= -3) $price = self::number_format($number, 6);
            elseif ($exp >= -4) $price = self::number_format($number, 7);
            elseif ($exp >= -5) $price = self::number_format($number, 8);
            elseif ($exp >= -6) $price = self::number_format($number, 8);
            elseif ($exp >= -7) $price = self::number_format($number, 8);
            elseif ($exp >= -8) $price = self::number_format($number, 8);
            else $price = 0;

            return $price;
        }

        static public function big_number($number) {
            return self::number_format($number,0);
        }

        static public function checkArrayValues(&$array, $keys = null, $test = null)
        {
            if(!is_array($array))
                return false;

            if(is_null($keys)) {
                foreach ($array as $key => $value){
                    if($test) {
                        if(!call_user_func($test, $value))
                            return false;
                    }
                    else if(!isset($array[$key])) {
                        return false;
                    }
                }
            }
            else if(is_array($keys)) {
                foreach ($keys as $key){
                    if($test) {
                        if(!array_key_exists($key, $array) || !call_user_func($test, $array[$key]))
                            return false;
                    }
                    else if(!isset($array[$key])) {
                        return false;
                    }
                }
            }
            else {
                return false;
            }

            return true;
        }

        static public function changeString($change, $percent_sign = true)
        {
            if(is_null($change)) {
                return '---';
            }
            else {
                $change_str = self::number_format(abs($change), 2);
                return $percent_sign ? "$change_str %" : $change_str;
            }
        }

        static public function defaultTableWidgetItems($count)
        {
            $f_count = floatval($count);

            $top = VCW_Data::cryptoCurrenciesTop($f_count > 0 ? $f_count : 10);

            if(is_array($top)){
                return array_map(function($c) {
                    return $c['final_code'];
                }, $top);
            }

            return null;
        }

        static public function currencySymbol($code, $default = null)
        {
            if(isset(VCW_Constants::$currency_symbols[$code])) {
                return VCW_Constants::$currency_symbols[$code];
            }

            return $default;
        }

    }

}