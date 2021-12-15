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
use Ethereumico\EthereumWallet\Dependencies\Web3\Utils;
class TagValidator implements \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\IValidator
{
    /**
     * validate
     *
     * @param string $value
     * @return bool
     */
    public static function validate($value)
    {
        $value = \Ethereumico\EthereumWallet\Dependencies\Web3\Utils::toString($value);
        $tags = ['latest', 'earliest', 'pending'];
        return \in_array($value, $tags);
    }
}
