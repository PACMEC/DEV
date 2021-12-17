<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Key\PrivateKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\Signature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\Key;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\RbgInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\Rfc6979;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\InvalidPrivateKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\PrivateKey\WifPrivateKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\Signer;
class PrivateKey extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\Key implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
{
    /**
     * @var \GMP
     */
    private $secretMultiplier;
    /**
     * @var bool
     */
    private $compressed;
    /**
     * @var PublicKey
     */
    private $publicKey;
    /**
     * @var EcAdapter
     */
    private $ecAdapter;
    /**
     * @param EcAdapter $ecAdapter
     * @param \GMP $int
     * @param bool $compressed
     * @throws InvalidPrivateKey
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter $ecAdapter, \GMP $int, bool $compressed = \false)
    {
        if (\false === $ecAdapter->validatePrivateKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::int(\gmp_strval($int, 10), 32))) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\InvalidPrivateKey('Invalid private key - must be less than curve order.');
        }
        $this->ecAdapter = $ecAdapter;
        $this->secretMultiplier = $int;
        $this->compressed = $compressed;
    }
    /**
     * @return \GMP
     */
    public function getSecret() : \GMP
    {
        return $this->secretMultiplier;
    }
    /**
     * @param BufferInterface $msg32
     * @param RbgInterface|null $rbg
     * @return Signature
     */
    public function sign(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $msg32, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\RbgInterface $rbg = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface
    {
        $rbg = $rbg ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\Rfc6979($this->ecAdapter, $this, $msg32);
        $randomK = \gmp_init($rbg->bytes(32)->getHex(), 16);
        $hash = \gmp_init($msg32->getHex(), 16);
        $math = $this->ecAdapter->getMath();
        $signer = new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\Signer($math);
        $signature = $signer->sign($this->ecAdapter->getGenerator()->getPrivateKeyFrom($this->secretMultiplier), $hash, $randomK);
        $s = $signature->getS();
        // if s is less than half the curve order, invert s
        if (!$this->ecAdapter->validateSignatureElement($s, \true)) {
            $s = $math->sub($this->ecAdapter->getOrder(), $s);
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\Signature($this->ecAdapter, $signature->getR(), $s);
    }
    /**
     * @param BufferInterface $msg32
     * @param RbgInterface|null $rbg
     * @return CompactSignatureInterface
     * @throws \Exception
     */
    public function signCompact(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $msg32, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\RbgInterface $rbg = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface
    {
        $sign = $this->sign($msg32, $rbg);
        // calculate the recovery param
        // there should be a way to get this when signing too, but idk how ...
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature($this->ecAdapter, $sign->getR(), $sign->getS(), $this->ecAdapter->calcPubKeyRecoveryParam($sign->getR(), $sign->getS(), $msg32, $this->getPublicKey()), $this->isCompressed());
    }
    /**
     * @param \GMP $tweak
     * @return KeyInterface
     */
    public function tweakAdd(\GMP $tweak) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface
    {
        $adapter = $this->ecAdapter;
        $modMath = $adapter->getMath()->getModularArithmetic($adapter->getGenerator()->getOrder());
        return $adapter->getPrivateKey($modMath->add($tweak, $this->getSecret()), $this->compressed);
    }
    /**
     * @param \GMP $tweak
     * @return KeyInterface
     */
    public function tweakMul(\GMP $tweak) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface
    {
        $adapter = $this->ecAdapter;
        $modMath = $adapter->getMath()->getModularArithmetic($adapter->getGenerator()->getOrder());
        return $adapter->getPrivateKey($modMath->mul($tweak, $this->getSecret()), $this->compressed);
    }
    /**
     * {@inheritDoc}
     */
    public function isCompressed() : bool
    {
        return $this->compressed;
    }
    /**
     * Return the public key
     *
     * @return PublicKey
     */
    public function getPublicKey() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface
    {
        if (null === $this->publicKey) {
            $point = $this->ecAdapter->getGenerator()->mul($this->secretMultiplier);
            $this->publicKey = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey($this->ecAdapter, $point, $this->compressed);
        }
        return $this->publicKey;
    }
    /**
     * @param NetworkInterface $network
     * @return string
     */
    public function toWif(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : string
    {
        $network = $network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork();
        $serializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\PrivateKey\WifPrivateKeySerializer(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Key\PrivateKeySerializer($this->ecAdapter));
        return $serializer->serialize($network, $this);
    }
    /**
     * @return BufferInterface
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Key\PrivateKeySerializer($this->ecAdapter))->serialize($this);
    }
}
