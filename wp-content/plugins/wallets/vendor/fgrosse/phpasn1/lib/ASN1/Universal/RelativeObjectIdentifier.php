<?php

/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal;

use Exception;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Parsable;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Exception\ParserException;
class RelativeObjectIdentifier extends \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\ObjectIdentifier implements \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Parsable
{
    public function __construct($subIdentifiers)
    {
        $this->value = $subIdentifiers;
        $this->subIdentifiers = \explode('.', $subIdentifiers);
        $nrOfSubIdentifiers = \count($this->subIdentifiers);
        for ($i = 0; $i < $nrOfSubIdentifiers; $i++) {
            if (\is_numeric($this->subIdentifiers[$i])) {
                // enforce the integer type
                $this->subIdentifiers[$i] = \intval($this->subIdentifiers[$i]);
            } else {
                throw new \Exception("[{$subIdentifiers}] is no valid object identifier (sub identifier " . ($i + 1) . ' is not numeric)!');
            }
        }
    }
    public function getType()
    {
        return \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier::RELATIVE_OID;
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier::RELATIVE_OID, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 1);
        try {
            $oidString = self::parseOid($binaryData, $offsetIndex, $contentLength);
        } catch (\Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Exception\ParserException $e) {
            throw new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Exception\ParserException('Malformed ASN.1 Relative Object Identifier', $e->getOffset());
        }
        $parsedObject = new self($oidString);
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }
}
