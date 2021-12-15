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
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\HexFormatter;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\QuantityFormatter;
class TransactionFormatter implements \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\IFormatter
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
            $value['gas'] = \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\QuantityFormatter::format($value['gas']);
        }
        if (isset($value['gasPrice'])) {
            $value['gasPrice'] = \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\QuantityFormatter::format($value['gasPrice']);
        }
        // EIP-1559
        if (isset($value['maxPriorityFeePerGas'])) {
            $value['maxPriorityFeePerGas'] = \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\QuantityFormatter::format($value['maxPriorityFeePerGas']);
        }
        if (isset($value['maxFeePerGas'])) {
            $value['maxFeePerGas'] = \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\QuantityFormatter::format($value['maxFeePerGas']);
        }
        if (isset($value['value'])) {
            $value['value'] = \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\QuantityFormatter::format($value['value']);
        }
        if (isset($value['data'])) {
            $value['data'] = \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\HexFormatter::format($value['data']);
        }
        if (isset($value['nonce'])) {
            $value['nonce'] = \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\QuantityFormatter::format($value['nonce']);
        }
        return $value;
    }
}
