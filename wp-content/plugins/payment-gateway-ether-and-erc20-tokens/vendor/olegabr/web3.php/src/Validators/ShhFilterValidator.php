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
class ShhFilterValidator
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
        if (isset($value['to']) && \Ethereumico\Epg\Dependencies\Web3\Validators\IdentityValidator::validate($value['to']) === \false) {
            return \false;
        }
        if (!isset($value['topics']) || !\is_array($value['topics'])) {
            return \false;
        }
        foreach ($value['topics'] as $topic) {
            if (\is_array($topic)) {
                foreach ($topic as $subTopic) {
                    if (\Ethereumico\Epg\Dependencies\Web3\Validators\HexValidator::validate($subTopic) === \false) {
                        return \false;
                    }
                }
                continue;
            }
            if (\Ethereumico\Epg\Dependencies\Web3\Validators\HexValidator::validate($topic) === \false) {
                if (!\is_null($topic)) {
                    return \false;
                }
            }
        }
        return \true;
    }
}
