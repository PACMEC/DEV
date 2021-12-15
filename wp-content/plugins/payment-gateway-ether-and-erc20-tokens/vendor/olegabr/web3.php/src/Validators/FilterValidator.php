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
use Ethereumico\Epg\Dependencies\Web3\Validators\AddressValidator;
class FilterValidator
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
        if (isset($value['fromBlock']) && \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::validate($value['fromBlock']) === \false && \Ethereumico\Epg\Dependencies\Web3\Validators\TagValidator::validate($value['fromBlock']) === \false) {
            return \false;
        }
        if (isset($value['toBlock']) && \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::validate($value['toBlock']) === \false && \Ethereumico\Epg\Dependencies\Web3\Validators\TagValidator::validate($value['toBlock']) === \false) {
            return \false;
        }
        if (isset($value['address'])) {
            if (\is_array($value['address'])) {
                foreach ($value['address'] as $address) {
                    if (\Ethereumico\Epg\Dependencies\Web3\Validators\AddressValidator::validate($address) === \false) {
                        return \false;
                    }
                }
            } elseif (\Ethereumico\Epg\Dependencies\Web3\Validators\AddressValidator::validate($value['address']) === \false) {
                return \false;
            }
        }
        if (isset($value['topics']) && \is_array($value['topics'])) {
            foreach ($value['topics'] as $topic) {
                if (\is_array($topic)) {
                    foreach ($topic as $v) {
                        if (isset($v) && \Ethereumico\Epg\Dependencies\Web3\Validators\HexValidator::validate($v) === \false) {
                            return \false;
                        }
                    }
                } else {
                    if (isset($topic) && \Ethereumico\Epg\Dependencies\Web3\Validators\HexValidator::validate($topic) === \false) {
                        return \false;
                    }
                }
            }
        }
        return \true;
    }
}
