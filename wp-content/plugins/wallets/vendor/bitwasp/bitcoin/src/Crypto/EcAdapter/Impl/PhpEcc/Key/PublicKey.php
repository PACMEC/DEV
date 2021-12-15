<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Key\PublicKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\Key;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\Signer;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface;
class PublicKey extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\Key implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface
{
    /**
     * @var EcAdapter
     */
    private $ecAdapter;
    /**
     * @var PointInterface
     */
    private $point;
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var bool
     */
    private $compressed;
    /**
     * PublicKey constructor.
     * @param EcAdapter $ecAdapter
     * @param PointInterface $point
     * @param bool $compressed
     * @param string $prefix
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter $ecAdapter, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface $point, bool $compressed = \false, string $prefix = null)
    {
        $this->ecAdapter = $ecAdapter;
        $this->point = $point;
        $this->prefix = $prefix;
        $this->compressed = $compressed;
    }
    /**
     * @return GeneratorPoint
     */
    public function getGenerator() : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint
    {
        return $this->ecAdapter->getGenerator();
    }
    /**
     * @return \Mdanter\Ecc\Primitives\CurveFpInterface
     */
    public function getCurve() : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface
    {
        return $this->ecAdapter->getGenerator()->getCurve();
    }
    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
    /**
     * @return PointInterface
     */
    public function getPoint() : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface
    {
        return $this->point;
    }
    /**
     * @param BufferInterface $msg32
     * @param SignatureInterface $signature
     * @return bool
     */
    public function verify(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $msg32, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface $signature) : bool
    {
        $hash = \gmp_init($msg32->getHex(), 16);
        $signer = new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\Signer($this->ecAdapter->getMath());
        return $signer->verify($this, $signature, $hash);
    }
    /**
     * @param \GMP $tweak
     * @return KeyInterface
     */
    public function tweakAdd(\GMP $tweak) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface
    {
        $offset = $this->ecAdapter->getGenerator()->mul($tweak);
        $newPoint = $this->point->add($offset);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey($this->ecAdapter, $newPoint, $this->compressed);
    }
    /**
     * @param \GMP $tweak
     * @return KeyInterface
     */
    public function tweakMul(\GMP $tweak) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface
    {
        $point = $this->point->mul($tweak);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey($this->ecAdapter, $point, $this->compressed);
    }
    /**
     * @param BufferInterface $publicKey
     * @return bool
     */
    public static function isCompressedOrUncompressed(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $publicKey) : bool
    {
        $vchPubKey = $publicKey->getBinary();
        if ($publicKey->getSize() < self::LENGTH_COMPRESSED) {
            return \false;
        }
        if ($vchPubKey[0] === self::KEY_UNCOMPRESSED) {
            if ($publicKey->getSize() !== self::LENGTH_UNCOMPRESSED) {
                // Invalid length for uncompressed key
                return \false;
            }
        } elseif (\in_array($vchPubKey[0], [self::KEY_COMPRESSED_EVEN, self::KEY_COMPRESSED_ODD])) {
            if ($publicKey->getSize() !== self::LENGTH_COMPRESSED) {
                return \false;
            }
        } else {
            return \false;
        }
        return \true;
    }
    /**
     * @return bool
     */
    public function isCompressed() : bool
    {
        return $this->compressed;
    }
    /**
     * @param PublicKey $other
     * @return bool
     */
    private function doEquals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey $other) : bool
    {
        return $this->compressed === $other->compressed && $this->point->equals($other->point) && ($this->prefix === null || $other->prefix === null || $this->prefix === $other->prefix);
    }
    /**
     * @param PublicKeyInterface $other
     * @return bool
     */
    public function equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface $other) : bool
    {
        /** @var self $other */
        return $this->doEquals($other);
    }
    /**
     * @return BufferInterface
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Key\PublicKeySerializer($this->ecAdapter))->serialize($this);
    }
}
