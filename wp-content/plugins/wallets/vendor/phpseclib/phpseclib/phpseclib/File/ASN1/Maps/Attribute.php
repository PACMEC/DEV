<?php

/**
 * Attribute
 *
 * PHP version 5
 *
 * @category  File
 * @package   ASN1
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */
namespace Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps;

use Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1;
/**
 * Attribute
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class Attribute
{
    const MAP = ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_SEQUENCE, 'children' => ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\AttributeType::MAP, 'value' => ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_SET, 'min' => 1, 'max' => -1, 'children' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\AttributeValue::MAP]]];
}
