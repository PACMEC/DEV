<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\Der;

use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ASNObject;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point\PointSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKey;
class Parser
{
    /**
     * @var GmpMathInterface
     */
    private $adapter;
    /**
     * @var UncompressedPointSerializer
     */
    private $pointSerializer;
    /**
     * Parser constructor.
     * @param GmpMathInterface $adapter
     * @param PointSerializerInterface|null $pointSerializer
     */
    public function __construct(GmpMathInterface $adapter, PointSerializerInterface $pointSerializer = null)
    {
        $this->adapter = $adapter;
        $this->pointSerializer = $pointSerializer ?: new UncompressedPointSerializer();
    }
    /**
     * @param string $binaryData
     * @return PublicKeyInterface
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse(string $binaryData) : PublicKeyInterface
    {
        $asnObject = ASNObject::fromBinary($binaryData);
        if ($asnObject->getType() !== Identifier::SEQUENCE) {
            throw new \RuntimeException('Invalid data.');
        }
        /** @var Sequence $asnObject */
        if ($asnObject->getNumberofChildren() != 2) {
            throw new \RuntimeException('Invalid data.');
        }
        $children = $asnObject->getChildren();
        if (\count($children) != 2) {
            throw new \RuntimeException('Invalid data.');
        }
        if (\count($children) != 2) {
            throw new \RuntimeException('Invalid data.');
        }
        if ($children[0]->getType() !== Identifier::SEQUENCE) {
            throw new \RuntimeException('Invalid data.');
        }
        if (\count($children[0]->getChildren()) != 2) {
            throw new \RuntimeException('Invalid data.');
        }
        if ($children[0]->getChildren()[0]->getType() !== Identifier::OBJECT_IDENTIFIER) {
            throw new \RuntimeException('Invalid data.');
        }
        if ($children[0]->getChildren()[1]->getType() !== Identifier::OBJECT_IDENTIFIER) {
            throw new \RuntimeException('Invalid data.');
        }
        if ($children[1]->getType() !== Identifier::BITSTRING) {
            throw new \RuntimeException('Invalid data.');
        }
        $oid = $children[0]->getChildren()[0];
        $curveOid = $children[0]->getChildren()[1];
        $encodedKey = $children[1];
        if ($oid->getContent() !== DerPublicKeySerializer::X509_ECDSA_OID) {
            throw new \RuntimeException('Invalid data: non X509 data.');
        }
        $generator = CurveOidMapper::getGeneratorFromOid($curveOid);
        return $this->parseKey($generator, $encodedKey->getContent());
    }
    /**
     * @param GeneratorPoint $generator
     * @param string $data
     * @return PublicKeyInterface
     */
    public function parseKey(GeneratorPoint $generator, string $data) : PublicKeyInterface
    {
        $point = $this->pointSerializer->unserialize($generator->getCurve(), $data);
        return new PublicKey($this->adapter, $generator, $point);
    }
}
