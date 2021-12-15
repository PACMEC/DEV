<?php

/**
 * GeneralSubtree
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
 * GeneralSubtree
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class GeneralSubtree
{
    const MAP = ['type' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::TYPE_SEQUENCE, 'children' => ['base' => \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\GeneralName::MAP, 'minimum' => ['constant' => 0, 'optional' => \true, 'implicit' => \true, 'default' => '0'] + \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\BaseDistance::MAP, 'maximum' => ['constant' => 1, 'optional' => \true, 'implicit' => \true] + \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\BaseDistance::MAP]];
}
