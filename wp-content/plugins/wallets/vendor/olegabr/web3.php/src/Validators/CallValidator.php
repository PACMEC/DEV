<?php

/**
 * This file is part of web3.php package.
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\EthereumWallet\Dependencies\Web3\Validators;

use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\IValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\QuantityValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\TagValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\HexValidator;
class CallValidator
{
    /**
     * validate
     *
     * @param array $value
     * @return bool
     */
    public static function validate($value)
    {
        if (!\is_array($value)) {
            return \false;
        }
        if (isset($value['from']) && \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\AddressValidator::validate($value['from']) === \false) {
            return \false;
        }
        if (!isset($value['to'])) {
            return \false;
        }
        if (\Ethereumico\EthereumWallet\Dependencies\Web3\Validators\AddressValidator::validate($value['to']) === \false) {
            return \false;
        }
        if (isset($value['gas']) && \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\QuantityValidator::validate($value['gas']) === \false) {
            return \false;
        }
        if (isset($value['gasPrice']) && \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\QuantityValidator::validate($value['gasPrice']) === \false) {
            return \false;
        }
        // EIP-1559
        if (isset($value['maxPriorityFeePerGas']) && \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\QuantityValidator::validate($value['maxPriorityFeePerGas']) === \false) {
            return \false;
        }
        if (isset($value['maxFeePerGas']) && \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\QuantityValidator::validate($value['maxFeePerGas']) === \false) {
            return \false;
        }
        if (isset($value['value']) && \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\QuantityValidator::validate($value['value']) === \false) {
            return \false;
        }
        if (isset($value['data']) && \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\HexValidator::validate($value['data']) === \false) {
            return \false;
        }
        if (isset($value['nonce']) && \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\QuantityValidator::validate($value['nonce']) === \false) {
            return \false;
        }
        return \true;
    }
}