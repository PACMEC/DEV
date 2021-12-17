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
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\FloatValidator;
class FloatArrayValidator
{
    /**
     * validate
     *
     * @param array $value
     * @return bool
     */
    public static function validate($value)
    {
        return \is_array($value) && \array_reduce($value, function ($ret, $v) {
            return $ret && \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\FloatValidator::validate($v);
        }, \true);
    }
}
