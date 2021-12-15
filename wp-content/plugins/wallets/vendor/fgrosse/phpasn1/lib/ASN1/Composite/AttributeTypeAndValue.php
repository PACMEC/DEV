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

use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ASNObject;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\ObjectIdentifier;
class AttributeTypeAndValue extends \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence
{
    /**
     * @param ObjectIdentifier|string $objIdentifier
     * @param \FG\ASN1\ASNObject $value
     */
    public function __construct($objIdentifier, \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ASNObject $value)
    {
        if ($objIdentifier instanceof \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\ObjectIdentifier == \false) {
            $objIdentifier = new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\ObjectIdentifier($objIdentifier);
        }
        parent::__construct($objIdentifier, $value);
    }
    public function __toString()
    {
        return $this->children[0] . ': ' . $this->children[1];
    }
}
