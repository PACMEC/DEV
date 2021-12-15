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

use InvalidArgumentException;
use Ethereumico\Epg\Dependencies\Web3\Contracts\SolidityType;
use Ethereumico\Epg\Dependencies\Web3\Contracts\Types\IType;
use Ethereumico\Epg\Dependencies\Web3\Utils;
use Ethereumico\Epg\Dependencies\Web3\Formatters\IntegerFormatter;
class Address extends \Ethereumico\Epg\Dependencies\Web3\Contracts\SolidityType implements \Ethereumico\Epg\Dependencies\Web3\Contracts\Types\IType
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
        return \preg_match('/^address(\\[([0-9]*)\\])*$/', $name) === 1;
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
     * to do: iban
     * 
     * @param mixed $value
     * @param string $name
     * @return string
     */
    public function inputFormat($value, $name)
    {
        $value = (string) $value;
        if (\Ethereumico\Epg\Dependencies\Web3\Utils::isAddress($value)) {
            $value = \mb_strtolower($value);
            if (\Ethereumico\Epg\Dependencies\Web3\Utils::isZeroPrefixed($value)) {
                $value = \Ethereumico\Epg\Dependencies\Web3\Utils::stripZero($value);
            }
        }
        $value = \Ethereumico\Epg\Dependencies\Web3\Formatters\IntegerFormatter::format($value);
        return $value;
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
        return '0x' . \mb_substr($value, 24, 40);
    }
}
