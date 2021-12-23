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
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\TagValidator;
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
        return TagValidator::validate($value) || \preg_match('/^0x[a-fA-F0-9]{64}$/', $value) >= 1;
    }
}
