<?php

/**
 * CertificateList
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
 * CertificateList
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class CertificateList
{
    const MAP = ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_SEQUENCE, 'children' => ['tbsCertList' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\TBSCertList::MAP, 'signatureAlgorithm' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\AlgorithmIdentifier::MAP, 'signature' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_BIT_STRING]]];
}
