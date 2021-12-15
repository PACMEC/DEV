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
use Ethereumico\Epg\Dependencies\Web3\Utils;
class TagValidator implements \Ethereumico\Epg\Dependencies\Web3\Validators\IValidator
{
    /**
     * validate
     *
     * @param string $value
     * @return bool
     */
    public static function validate($value)
    {
        $value = \Ethereumico\Epg\Dependencies\Web3\Utils::toString($value);
        $tags = ['latest', 'earliest', 'pending'];
        return \in_array($value, $tags);
    }
}
