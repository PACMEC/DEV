<?php

/**
 * This file is part of web3.php package.
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\Epg\Dependencies\Web3\Validators;

use Ethereumico\Epg\Dependencies\Web3\Validators\IValidator;
use Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator;
use Ethereumico\Epg\Dependencies\Web3\Validators\TagValidator;
use Ethereumico\Epg\Dependencies\Web3\Validators\HexValidator;
class TransactionValidator
{
    /**
     * validate
     * To do: check is data optional?
     * Data is not optional on spec, see https://github.com/ethereum/wiki/wiki/JSON-RPC#eth_sendtransaction
     *
     * @param array $value
     * @return bool
     */
    public static function validate($value)
    {
        if (!\is_array($value)) {
            return \false;
        }
        if (!isset($value['from'])) {
            return \false;
        }
        if (\Ethereumico\Epg\Dependencies\Web3\Validators\AddressValidator::validate($value['from']) === \false) {
            return \false;
        }
        if (isset($value['to']) && \Ethereumico\Epg\Dependencies\Web3\Validators\AddressValidator::validate($value['to']) === \false && $value['to'] !== '') {
            return \false;
        }
        if (isset($value['gas']) && \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::validate($value['gas']) === \false) {
            return \false;
        }
        if (isset($value['gasPrice']) && \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::validate($value['gasPrice']) === \false) {
            return \false;
        }
        // EIP-1559
        if (isset($value['maxPriorityFeePerGas']) && \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::validate($value['maxPriorityFeePerGas']) === \false) {
            return \false;
        }
        if (isset($value['maxFeePerGas']) && \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::validate($value['maxFeePerGas']) === \false) {
            return \false;
        }
        if (isset($value['value']) && \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::validate($value['value']) === \false) {
            return \false;
        }
        // if (!isset($value['data'])) {
        //     return false;
        // }
        // if (HexValidator::validate($value['data']) === false) {
        //     return false;
        // }
        if (isset($value['data']) && \Ethereumico\Epg\Dependencies\Web3\Validators\HexValidator::validate($value['data']) === \false) {
            return \false;
        }
        if (isset($value['nonce']) && \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::validate($value['nonce']) === \false) {
            return \false;
        }
        return \true;
    }
}
