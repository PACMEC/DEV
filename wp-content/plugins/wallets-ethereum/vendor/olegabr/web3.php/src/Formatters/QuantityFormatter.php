<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\EthereumWallet\Dependencies\Web3\Formatters;

use InvalidArgumentException;
use Ethereumico\EthereumWallet\Dependencies\Web3\Utils;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\IFormatter;
class QuantityFormatter implements \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\IFormatter
{
    /**
     * format
     * 
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        $value = \Ethereumico\EthereumWallet\Dependencies\Web3\Utils::toString($value);
        if (\Ethereumico\EthereumWallet\Dependencies\Web3\Utils::isZeroPrefixed($value)) {
            // test hex with zero ahead, hardcode 0x0
            if ($value === '0x0' || \strpos($value, '0x0') !== 0) {
                return $value;
            }
            $hex = \preg_replace('/^0x0+(?!$)/', '', $value);
        } else {
            $bn = \Ethereumico\EthereumWallet\Dependencies\Web3\Utils::toBn($value);
            $hex = $bn->toHex(\true);
        }
        if (empty($hex)) {
            $hex = '0';
        } else {
            $hex = \preg_replace('/^0+(?!$)/', '', $hex);
        }
        return '0x' . $hex;
    }
}
