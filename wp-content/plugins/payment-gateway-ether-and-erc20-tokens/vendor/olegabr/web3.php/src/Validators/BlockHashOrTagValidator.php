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
use Ethereumico\Epg\Dependencies\Web3\Validators\TagValidator;
class BlockHashOrTagValidator
{
    /**
     * validate
     *
     * @param string $value
     * @return bool
     */
    public static function validate($value)
    {
        if (!\is_string($value)) {
            return \false;
        }
        return \Ethereumico\Epg\Dependencies\Web3\Validators\TagValidator::validate($value) || \preg_match('/^0x[a-fA-F0-9]{64}$/', $value) >= 1;
    }
}
