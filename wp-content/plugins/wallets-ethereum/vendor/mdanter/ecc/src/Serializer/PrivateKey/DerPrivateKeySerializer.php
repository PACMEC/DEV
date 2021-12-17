<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PrivateKey;

use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ASNObject;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Integer;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\BitString;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\OctetString;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ExplicitlyTaggedObject;
/**
 * PEM Private key formatter
 *
 * @link https://tools.ietf.org/html/rfc5915
 */
class DerPrivateKeySerializer implements \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PrivateKey\PrivateKeySerializerInterface
{
    const VERSION = 1;
    /**
     * @var GmpMathInterface|null
     */
    private $adapter;
    /**
     * @var DerPublicKeySerializer
     */
    private $pubKeySerializer;
    /**
     * @param GmpMathInterface       $adapter
     * @param DerPublicKeySerializer $pubKeySerializer
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter = null, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer $pubKeySerializer = null)
    {
        $this->adapter = $adapter ?: \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory::getAdapter();
        $this->pubKeySerializer = $pubKeySerializer ?: new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer($this->adapter);
    }
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Serializer\PrivateKey\PrivateKeySerializerInterface::serialize()
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface $key) : string
    {
        $privateKeyInfo = new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence(new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Integer(self::VERSION), new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\OctetString($this->formatKey($key)), new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ExplicitlyTaggedObject(0, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Util\CurveOidMapper::getCurveOid($key->getPoint()->getCurve())), new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ExplicitlyTaggedObject(1, $this->encodePubKey($key)));
        return $privateKeyInfo->getBinary();
    }
    /**
     * @param PrivateKeyInterface $key
     * @return BitString
     */
    private function encodePubKey(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface $key) : \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\BitString
    {
        return new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\BitString($this->pubKeySerializer->getUncompressedKey($key->getPublicKey()));
    }
    /**
     * @param PrivateKeyInterface $key
     * @return string
     */
    private function formatKey(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface $key) : string
    {
        return \gmp_strval($key->getSecret(), 16);
    }
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Serializer\PrivateKey\PrivateKeySerializerInterface::parse()
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse(string $data) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface
    {
        $asnObject = \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\ASNObject::fromBinary($data);
        if (!$asnObject instanceof \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence || $asnObject->getNumberofChildren() !== 4) {
            throw new \RuntimeException('Invalid data.');
        }
        $children = $asnObject->getChildren();
        $version = $children[0];
        if ($version->getContent() != 1) {
            throw new \RuntimeException('Invalid data: only version 1 (RFC5915) keys are supported.');
        }
        $key = \gmp_init($children[1]->getContent(), 16);
        $oid = $children[2]->getContent()[0];
        $generator = \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Util\CurveOidMapper::getGeneratorFromOid($oid);
        return $generator->getPrivateKeyFrom($key);
    }
}
