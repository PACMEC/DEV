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
class Uinteger extends \Ethereumico\Epg\Dependencies\Web3\Contracts\SolidityType implements \Ethereumico\Epg\Dependencies\Web3\Contracts\Types\IType
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
        return \preg_match('/^uint([0-9]{1,})?(\\[([0-9]*)\\])*$/', $name) === 1;
    }
    /**
     * isDynamicType
     * 
     * @return bool
     */
    public function isDynamicType()
    {
        return \false;
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
        return \Ethereumico\Epg\Dependencies\Web3\Formatters\IntegerFormatter::format($value);
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
        $match = [];
        if (\preg_match('/^[0]+([a-f0-9]+)$/', $value, $match) === 1) {
            // due to value without 0x prefix, we will parse as decimal
            $value = '0x' . $match[1];
        }
        return \Ethereumico\Epg\Dependencies\Web3\Formatters\BigNumberFormatter::format($value);
    }
}
