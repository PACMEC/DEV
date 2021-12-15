<?php

/**
 * This file is part of web3.php package.
 *
 * EIP-1559
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
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\QuantityFormatter;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\IntegerFormatter;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\FloatArrayFormatter;
class FeeHistoryFormatter implements \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\IFormatter
{
    /**
     * format
     *
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        if (isset($value['oldestBlock'])) {
            $value['oldestBlock'] = \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\QuantityFormatter::format($value['oldestBlock']);
        }
        if (isset($value['baseFeePerGas'])) {
            $value['baseFeePerGas'] = \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\IntegerFormatter::format($value['baseFeePerGas']);
        }
        if (isset($value['gasUsedRatio'])) {
            $value['gasUsedRatio'] = \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\FloatArrayFormatter::format($value['gasUsedRatio']);
        }
        if (isset($value['reward'])) {
            $value['reward'] = \array_map(function ($v) {
                return \array_map(function ($v2) {
                    return \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\IntegerFormatter::format($v2);
                }, $v);
            }, $value['reward']);
        }
        return $value;
    }
}
