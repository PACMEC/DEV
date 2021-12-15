<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\Epg\Dependencies\Web3\Contracts\Types;

use Ethereumico\Epg\Dependencies\Web3\Utils;
use Ethereumico\Epg\Dependencies\Web3\Contracts\SolidityType;
use Ethereumico\Epg\Dependencies\Web3\Contracts\Types\IType;
use Ethereumico\Epg\Dependencies\Web3\Formatters\IntegerFormatter;
use Ethereumico\Epg\Dependencies\Web3\Formatters\BigNumberFormatter;
class Str extends \Ethereumico\Epg\Dependencies\Web3\Contracts\SolidityType implements \Ethereumico\Epg\Dependencies\Web3\Contracts\Types\IType
{
    /**
     * construct
     * 
     * @return void
     */
    public function __construct()
    {
        //
    }
    /**
     * isType
     * 
     * @param string $name
     * @return bool
     */
    public function isType($name)
    {
        return \preg_match('/^string(\\[([0-9]*)\\])*$/', $name) === 1;
    }
    /**
     * isDynamicType
     * 
     * @return bool
     */
    public function isDynamicType()
    {
        return \true;
    }
    /**
     * inputFormat
     * 
     * @param mixed $value
     * @param string $name
     * @return string
     */
    public function inputFormat($value, $name)
    {
        $value = \Ethereumico\Epg\Dependencies\Web3\Utils::toHex($value);
        $prefix = \Ethereumico\Epg\Dependencies\Web3\Formatters\IntegerFormatter::format(\mb_strlen($value) / 2);
        $l = \floor((\mb_strlen($value) + 63) / 64);
        $padding = $l * 64 - \mb_strlen($value) + 1 >= 0 ? $l * 64 - \mb_strlen($value) : 0;
        return $prefix . $value . \implode('', \array_fill(0, $padding, '0'));
    }
    /**
     * outputFormat
     * 
     * @param mixed $value
     * @param string $name
     * @return string
     */
    public function outputFormat($value, $name)
    {
        $strLen = \mb_substr($value, 0, 64);
        $strValue = \mb_substr($value, 64);
        $match = [];
        if (\preg_match('/^[0]+([a-f0-9]+)$/', $strLen, $match) === 1) {
            $strLen = \Ethereumico\Epg\Dependencies\Web3\Formatters\BigNumberFormatter::format('0x' . $match[1])->toString();
        }
        $strValue = \mb_substr($strValue, 0, (int) $strLen * 2);
        return \Ethereumico\Epg\Dependencies\Web3\Utils::hexToBin($strValue);
    }
}
