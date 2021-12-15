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
class HexFormatter implements \Ethereumico\Epg\Dependencies\Web3\Formatters\IFormatter
{
    /**
     * format
     * 
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        $value = \Ethereumico\Epg\Dependencies\Web3\Utils::toString($value);
        $value = \mb_strtolower($value);
        if (\Ethereumico\Epg\Dependencies\Web3\Utils::isZeroPrefixed($value)) {
            return $value;
        } else {
            $value = \Ethereumico\Epg\Dependencies\Web3\Utils::toHex($value, \true);
        }
        return $value;
    }
}
