<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\Epg\Dependencies\Web3\Formatters;

use InvalidArgumentException;
use Ethereumico\Epg\Dependencies\Web3\Utils;
use Ethereumico\Epg\Dependencies\Web3\Formatters\IFormatter;
use Ethereumico\Epg\Dependencies\Web3\Formatters\IntegerFormatter;
class AddressFormatter implements \Ethereumico\Epg\Dependencies\Web3\Formatters\IFormatter
{
    /**
     * format
     * to do: iban
     * 
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        $value = (string) $value;
        if (\Ethereumico\Epg\Dependencies\Web3\Utils::isAddress($value)) {
            $value = \mb_strtolower($value);
            if (\Ethereumico\Epg\Dependencies\Web3\Utils::isZeroPrefixed($value)) {
                return $value;
            }
            return '0x' . $value;
        }
        $value = \Ethereumico\Epg\Dependencies\Web3\Formatters\IntegerFormatter::format($value, 40);
        return '0x' . $value;
    }
}
