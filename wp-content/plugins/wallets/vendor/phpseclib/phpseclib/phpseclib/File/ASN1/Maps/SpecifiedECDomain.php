<?php

/**
 * SpecifiedECDomain
 *
 * From: http://www.secg.org/sec1-v2.pdf#page=109
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
 * SpecifiedECDomain
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class SpecifiedECDomain
{
    const MAP = ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_SEQUENCE, 'children' => ['version' => ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER, 'mapping' => [1 => 'ecdpVer1', 'ecdpVer2', 'ecdpVer3']], 'fieldID' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\FieldID::MAP, 'curve' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\Curve::MAP, 'base' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\ECPoint::MAP, 'order' => ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER], 'cofactor' => ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER, 'optional' => \true], 'hash' => ['optional' => \true] + \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\HashAlgorithm::MAP]];
}