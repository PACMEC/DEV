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

use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ASNObject;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Parsable;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Exception\ParserException;
class NullObject extends \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ASNObject implements \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Parsable
{
    public function getType()
    {
        return \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier::NULL;
    }
    protected function calculateContentLength()
    {
        return 0;
    }
    protected function getEncodedValue()
    {
        return null;
    }
    public function getContent()
    {
        return 'NULL';
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier::NULL, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        if ($contentLength != 0) {
            throw new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Exception\ParserException("An ASN.1 Null should not have a length other than zero. Extracted length was {$contentLength}", $offsetIndex);
        }
        $parsedObject = new self();
        $parsedObject->setContentLength(0);
        return $parsedObject;
    }
}
