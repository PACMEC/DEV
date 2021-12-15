<?php

/**
 * RSAPrivateKey
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
namespace Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps;

use Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1;
/**
 * RSAPrivateKey
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class RSAPrivateKey
{
    // version must be multi if otherPrimeInfos present
    const MAP = ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_SEQUENCE, 'children' => [
        'version' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER, 'mapping' => ['two-prime', 'multi']],
        'modulus' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER],
        // n
        'publicExponent' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER],
        // e
        'privateExponent' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER],
        // d
        'prime1' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER],
        // p
        'prime2' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER],
        // q
        'exponent1' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER],
        // d mod (p-1)
        'exponent2' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER],
        // d mod (q-1)
        'coefficient' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER],
        // (inverse of q) mod p
        'otherPrimeInfos' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\OtherPrimeInfos::MAP + ['optional' => \true],
    ]];
}
