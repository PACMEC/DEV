<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\Der;

use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\ObjectIdentifier;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\BitString;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NamedCurveFp;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point\PointSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
class Formatter
{
    /**
     * @var UncompressedPointSerializer
     */
    private $pointSerializer;
    /**
     * Formatter constructor.
     * @param PointSerializerInterface|null $pointSerializer
     */
    public function __construct(PointSerializerInterface $pointSerializer = null)
    {
        $this->pointSerializer = $pointSerializer ?: new UncompressedPointSerializer();
    }
    /**
     * @param PublicKeyInterface $key
     * @return string
     */
    public function format(PublicKeyInterface $key) : string
    {
        if (!$key->getCurve() instanceof NamedCurveFp) {
            throw new \RuntimeException('Not implemented for unnamed curves');
        }
        $sequence = new Sequence(new Sequence(new ObjectIdentifier(DerPublicKeySerializer::X509_ECDSA_OID), CurveOidMapper::getCurveOid($key->getCurve())), new BitString($this->encodePoint($key->getPoint())));
        return $sequence->getBinary();
    }
    /**
     * @param PointInterface $point
     * @return string
     */
    public function encodePoint(PointInterface $point) : string
    {
        return $this->pointSerializer->serialize($point);
    }
}
