<?php

/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Composite;

use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\PrintableString;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\IA5String;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\UTF8String;
class RDNString extends \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Composite\RelativeDistinguishedName
{
    /**
     * @param string|\FG\ASN1\Universal\ObjectIdentifier $objectIdentifierString
     * @param string|\FG\ASN1\ASNObject $value
     */
    public function __construct($objectIdentifierString, $value)
    {
        if (\Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\PrintableString::isValid($value)) {
            $value = new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\PrintableString($value);
        } else {
            if (\Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\IA5String::isValid($value)) {
                $value = new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\IA5String($value);
            } else {
                $value = new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\UTF8String($value);
            }
        }
        parent::__construct($objectIdentifierString, $value);
    }
}
