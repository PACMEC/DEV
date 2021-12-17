<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Signature\Der;

use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ASNObject;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Integer;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\Signature;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception\SignatureDecodeException;
class Parser
{
    /**
     * @param string $binary
     * @return SignatureInterface
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse(string $binary) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\SignatureInterface
    {
        $offsetIndex = 0;
        $asnObject = \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ASNObject::fromBinary($binary, $offsetIndex);
        if ($offsetIndex != \strlen($binary)) {
            throw new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception\SignatureDecodeException('Invalid data.');
        }
        // Set inherits from Sequence, so use getType!
        if ($asnObject->getType() !== \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier::SEQUENCE) {
            throw new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception\SignatureDecodeException('Invalid tag for sequence.');
        }
        if ($asnObject->getNumberofChildren() !== 2) {
            throw new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception\SignatureDecodeException('Invalid data.');
        }
        if (!($asnObject[0] instanceof \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Integer && $asnObject[1] instanceof \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Integer)) {
            throw new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception\SignatureDecodeException('Invalid data.');
        }
        return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\Signature(\gmp_init($asnObject[0]->getContent(), 10), \gmp_init($asnObject[1]->getContent(), 10));
    }
}
