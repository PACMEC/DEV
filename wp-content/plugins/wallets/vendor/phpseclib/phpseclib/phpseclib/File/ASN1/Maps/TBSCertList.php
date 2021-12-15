<?php

/**
 * TBSCertList
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
 * TBSCertList
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class TBSCertList
{
    const MAP = ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_SEQUENCE, 'children' => ['version' => ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER, 'mapping' => ['v1', 'v2', 'v3'], 'optional' => \true, 'default' => 'v2'], 'signature' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\AlgorithmIdentifier::MAP, 'issuer' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\Name::MAP, 'thisUpdate' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\Time::MAP, 'nextUpdate' => ['optional' => \true] + \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\Time::MAP, 'revokedCertificates' => ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_SEQUENCE, 'optional' => \true, 'min' => 0, 'max' => -1, 'children' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\RevokedCertificate::MAP], 'crlExtensions' => ['constant' => 0, 'optional' => \true, 'explicit' => \true] + \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\Extensions::MAP]];
}
