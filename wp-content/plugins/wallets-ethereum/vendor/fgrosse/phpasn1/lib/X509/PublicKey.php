<?php

/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ethereumico\EthereumWallet\Dependencies\FG\X509;

use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\OID;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\NullObject;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\BitString;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\ObjectIdentifier;
class PublicKey extends \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence
{
    /**
     * @param string $hexKey
     * @param \FG\ASN1\ASNObject|string $algorithmIdentifierString
     */
    public function __construct($hexKey, $algorithmIdentifierString = \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\OID::RSA_ENCRYPTION)
    {
        parent::__construct(new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence(new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\ObjectIdentifier($algorithmIdentifierString), new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\NullObject()), new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\BitString($hexKey));
    }
}
