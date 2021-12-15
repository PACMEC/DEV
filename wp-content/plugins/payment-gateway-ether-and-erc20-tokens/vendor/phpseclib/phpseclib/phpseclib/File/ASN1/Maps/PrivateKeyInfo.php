<?php

/**
 * PrivateKeyInfo
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
 * PrivateKeyInfo
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PrivateKeyInfo
{
    const MAP = ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_SEQUENCE, 'children' => ['version' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_INTEGER, 'mapping' => ['v1']], 'privateKeyAlgorithm' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\AlgorithmIdentifier::MAP, 'privateKey' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\PrivateKey::MAP, 'attributes' => ['constant' => 0, 'optional' => \true, 'implicit' => \true] + \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\Attributes::MAP]];
}
