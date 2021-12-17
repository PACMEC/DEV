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
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\IntegerFormatter;
class AddressFormatter implements \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\IFormatter
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
        if (\Ethereumico\EthereumWallet\Dependencies\Web3\Utils::isAddress($value)) {
            $value = \mb_strtolower($value);
            if (\Ethereumico\EthereumWallet\Dependencies\Web3\Utils::isZeroPrefixed($value)) {
                return $value;
            }
            return '0x' . $value;
        }
        $value = \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\IntegerFormatter::format($value, 40);
        return '0x' . $value;
    }
}
