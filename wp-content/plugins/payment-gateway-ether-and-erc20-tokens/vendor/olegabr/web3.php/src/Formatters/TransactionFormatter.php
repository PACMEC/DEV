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
use Ethereumico\Epg\Dependencies\Web3\Formatters\HexFormatter;
use Ethereumico\Epg\Dependencies\Web3\Formatters\QuantityFormatter;
class TransactionFormatter implements \Ethereumico\Epg\Dependencies\Web3\Formatters\IFormatter
{
    /**
     * format
     *
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        if (isset($value['gas'])) {
            $value['gas'] = \Ethereumico\Epg\Dependencies\Web3\Formatters\QuantityFormatter::format($value['gas']);
        }
        if (isset($value['gasPrice'])) {
            $value['gasPrice'] = \Ethereumico\Epg\Dependencies\Web3\Formatters\QuantityFormatter::format($value['gasPrice']);
        }
        // EIP-1559
        if (isset($value['maxPriorityFeePerGas'])) {
            $value['maxPriorityFeePerGas'] = \Ethereumico\Epg\Dependencies\Web3\Formatters\QuantityFormatter::format($value['maxPriorityFeePerGas']);
        }
        if (isset($value['maxFeePerGas'])) {
            $value['maxFeePerGas'] = \Ethereumico\Epg\Dependencies\Web3\Formatters\QuantityFormatter::format($value['maxFeePerGas']);
        }
        if (isset($value['value'])) {
            $value['value'] = \Ethereumico\Epg\Dependencies\Web3\Formatters\QuantityFormatter::format($value['value']);
        }
        if (isset($value['data'])) {
            $value['data'] = \Ethereumico\Epg\Dependencies\Web3\Formatters\HexFormatter::format($value['data']);
        }
        if (isset($value['nonce'])) {
            $value['nonce'] = \Ethereumico\Epg\Dependencies\Web3\Formatters\QuantityFormatter::format($value['nonce']);
        }
        return $value;
    }
}
