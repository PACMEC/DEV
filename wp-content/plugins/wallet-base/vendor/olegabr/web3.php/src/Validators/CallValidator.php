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
        if (isset($value['from']) && AddressValidator::validate($value['from']) === \false) {
            return \false;
        }
        if (!isset($value['to'])) {
            return \false;
        }
        if (AddressValidator::validate($value['to']) === \false) {
            return \false;
        }
        if (isset($value['gas']) && QuantityValidator::validate($value['gas']) === \false) {
            return \false;
        }
        if (isset($value['gasPrice']) && QuantityValidator::validate($value['gasPrice']) === \false) {
            return \false;
        }
        // EIP-1559
        if (isset($value['maxPriorityFeePerGas']) && QuantityValidator::validate($value['maxPriorityFeePerGas']) === \false) {
            return \false;
        }
        if (isset($value['maxFeePerGas']) && QuantityValidator::validate($value['maxFeePerGas']) === \false) {
            return \false;
        }
        if (isset($value['value']) && QuantityValidator::validate($value['value']) === \false) {
            return \false;
        }
        if (isset($value['data']) && HexValidator::validate($value['data']) === \false) {
            return \false;
        }
        if (isset($value['nonce']) && QuantityValidator::validate($value['nonce']) === \false) {
            return \false;
        }
        return \true;
    }
}
