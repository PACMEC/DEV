<?php

/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ethereumico\EthereumWallet\Dependencies\FG\X509\CSR;

use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ASNObject;
use Ethereumico\EthereumWallet\Dependencies\FG\X509\CertificateExtensions;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\OID;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Parsable;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Construct;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Set;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\ObjectIdentifier;
class Attributes extends \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Construct implements \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Parsable
{
    public function getType()
    {
        return 0xa0;
    }
    public function addAttribute($objectIdentifier, \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Set $attribute)
    {
        if (\is_string($objectIdentifier)) {
            $objectIdentifier = new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\ObjectIdentifier($objectIdentifier);
        }
        $attributeSequence = new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence($objectIdentifier, $attribute);
        $attributeSequence->getNumberOfLengthOctets();
        // length and number of length octets is calculated
        $this->addChild($attributeSequence);
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], 0xa0, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        $octetsToRead = $contentLength;
        $parsedObject = new self();
        while ($octetsToRead > 0) {
            $initialOffset = $offsetIndex;
            // used to calculate how much bits have been read
            self::parseIdentifier($binaryData[$offsetIndex], \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier::SEQUENCE, $offsetIndex++);
            self::parseContentLength($binaryData, $offsetIndex);
            $objectIdentifier = \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\ObjectIdentifier::fromBinary($binaryData, $offsetIndex);
            $oidString = $objectIdentifier->getContent();
            if ($oidString == \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\OID::PKCS9_EXTENSION_REQUEST) {
                $attribute = \Ethereumico\EthereumWallet\Dependencies\FG\X509\CertificateExtensions::fromBinary($binaryData, $offsetIndex);
            } else {
                $attribute = \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ASNObject::fromBinary($binaryData, $offsetIndex);
            }
            $parsedObject->addAttribute($objectIdentifier, $attribute);
            $octetsToRead -= $offsetIndex - $initialOffset;
        }
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }
}
