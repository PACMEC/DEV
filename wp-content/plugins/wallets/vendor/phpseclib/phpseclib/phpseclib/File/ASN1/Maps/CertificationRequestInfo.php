<?php

/**
 * CertificationRequestInfo
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
 * CertificationRequestInfo
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class CertificationRequestInfo
{
    const MAP = ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_SEQUENCE, 'children' => ['version' => ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER, 'mapping' => ['v1']], 'subject' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\Name::MAP, 'subjectPKInfo' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\SubjectPublicKeyInfo::MAP, 'attributes' => ['constant' => 0, 'optional' => \true, 'implicit' => \true] + \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1\Maps\Attributes::MAP]];
}
