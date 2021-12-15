<?php

/**
 * DirectoryString
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
 * DirectoryString
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class DirectoryString
{
    const MAP = ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_CHOICE, 'children' => ['teletexString' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_TELETEX_STRING], 'printableString' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_PRINTABLE_STRING], 'universalString' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_UNIVERSAL_STRING], 'utf8String' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_UTF8_STRING], 'bmpString' => ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_BMP_STRING]]];
}
