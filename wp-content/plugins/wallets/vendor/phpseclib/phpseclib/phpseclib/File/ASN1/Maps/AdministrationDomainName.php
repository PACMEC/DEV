<?php

/**
 * AdministrationDomainName
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
 * AdministrationDomainName
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class AdministrationDomainName
{
    const MAP = [
        'type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_CHOICE,
        // if class isn't present it's assumed to be \phpseclib3\File\ASN1::CLASS_UNIVERSAL or
        // (if constant is present) \phpseclib3\File\ASN1::CLASS_CONTEXT_SPECIFIC
        'class' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::CLASS_APPLICATION,
        'cast' => 2,
        'children' => ['numeric' => ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_NUMERIC_STRING], 'printable' => ['type' => \Ethereumico\EthereumWallet\Dependencies\phpseclib3\File\ASN1::TYPE_PRINTABLE_STRING]],
    ];
}
