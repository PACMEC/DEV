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
use Ethereumico\Epg\Dependencies\Web3\Validators\HexValidator;
use Ethereumico\Epg\Dependencies\Web3\Validators\IdentityValidator;
class PostValidator
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
        if (isset($value['from']) && \Ethereumico\Epg\Dependencies\Web3\Validators\IdentityValidator::validate($value['from']) === \false) {
            return \false;
        }
        if (isset($value['to']) && \Ethereumico\Epg\Dependencies\Web3\Validators\IdentityValidator::validate($value['to']) === \false) {
            return \false;
        }
        if (!isset($value['topics']) || !\is_array($value['topics'])) {
            return \false;
        }
        foreach ($value['topics'] as $topic) {
            if (\Ethereumico\Epg\Dependencies\Web3\Validators\IdentityValidator::validate($topic) === \false) {
                return \false;
            }
        }
        if (!isset($value['payload'])) {
            return \false;
        }
        if (\Ethereumico\Epg\Dependencies\Web3\Validators\HexValidator::validate($value['payload']) === \false) {
            return \false;
        }
        if (!isset($value['priority'])) {
            return \false;
        }
        if (\Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::validate($value['priority']) === \false) {
            return \false;
        }
        if (!isset($value['ttl'])) {
            return \false;
        }
        if (isset($value['ttl']) && \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::validate($value['ttl']) === \false) {
            return \false;
        }
        return \true;
    }
}
