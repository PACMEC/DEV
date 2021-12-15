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
use Ethereumico\Epg\Dependencies\Web3\Formatters\FloatFormatter;
class FloatArrayFormatter implements \Ethereumico\Epg\Dependencies\Web3\Formatters\IFormatter
{
    /**
     * format
     *
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        return \array_map(function ($v) {
            return \Ethereumico\Epg\Dependencies\Web3\Formatters\FloatFormatter::format($v);
        }, $value);
    }
}
