<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Key;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Adapter\EcAdapter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Serializer\Key\PrivateKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\CompactSignature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\Signature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\Key;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\RbgInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\InvalidPrivateKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\PrivateKey\WifPrivateKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class PrivateKey extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\Key implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
{
    /**
     * @var \GMP
     */
    private $secret;
    /**
     * @var string
     */
    private $secretBin;
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
     * @param EcAdapter $adapter
     * @param \GMP $secret
     * @param bool|false $compressed
     * @throws \Exception
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Adapter\EcAdapter $adapter, \GMP $secret, bool $compressed = \false)
    {
        $buffer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::int(\gmp_strval($secret, 10), 32);
        if (!$adapter->validatePrivateKey($buffer)) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\InvalidPrivateKey('Invalid private key');
        }
        if (\false === \is_bool($compressed)) {
            throw new \InvalidArgumentException('PrivateKey: Compressed argument must be a boolean');
        }
        $this->ecAdapter = $adapter;
        $this->secret = $secret;
        $this->secretBin = $buffer->getBinary();
        $this->compressed = $compressed;
    }
    /**
     * @param BufferInterface $msg32
     * @param RbgInterface|null $rbgInterface
     * @return Signature
     */
    public function sign(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $msg32, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\RbgInterface $rbgInterface = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\Signature
    {
        $context = $this->ecAdapter->getContext();
        $sig_t = null;
        if (1 !== secp256k1_ecdsa_sign($context, $sig_t, $msg32->getBinary(), $this->secretBin)) {
            throw new \RuntimeException('Secp256k1: failed to sign');
        }
        /** @var resource $sig_t */
        $derSig = '';
        secp256k1_ecdsa_signature_serialize_der($context, $derSig, $sig_t);
        $rL = \ord($derSig[3]);
        $r = (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\substr($derSig, 4, $rL), $rL))->getGmp();
        $sL = \ord($derSig[4 + $rL + 1]);
        $s = (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\substr($derSig, 4 + $rL + 2, $sL), $sL))->getGmp();
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\Signature($this->ecAdapter, $r, $s, $sig_t);
    }
    /**
     * @param BufferInterface $msg32
     * @param RbgInterface|null $rbfInterface
     * @return CompactSignature
     */
    public function signCompact(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $msg32, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\RbgInterface $rbfInterface = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface
    {
        $context = $this->ecAdapter->getContext();
        $sig_t = null;
        if (1 !== secp256k1_ecdsa_sign_recoverable($context, $sig_t, $msg32->getBinary(), $this->secretBin)) {
            throw new \RuntimeException('Secp256k1: failed to sign');
        }
        /** @var resource $sig_t
         */
        $recid = 0;
        $ser = '';
        if (!secp256k1_ecdsa_recoverable_signature_serialize_compact($context, $ser, $recid, $sig_t)) {
            throw new \RuntimeException('Failed to obtain recid');
        }
        /** @var resource $sig_t */
        /** @var int $recid */
        unset($ser);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\CompactSignature($this->ecAdapter, $sig_t, $recid, $this->isCompressed());
    }
    /**
     * @return bool
     */
    public function isCompressed() : bool
    {
        return $this->compressed;
    }
    /**
     * @return \GMP
     */
    public function getSecret()
    {
        return $this->secret;
    }
    /**
     * @return string
     */
    public function getSecretBinary() : string
    {
        return $this->secretBin;
    }
    /**
     * @return PublicKey
     */
    public function getPublicKey()
    {
        if (null === $this->publicKey) {
            $context = $this->ecAdapter->getContext();
            $publicKey_t = null;
            if (1 !== secp256k1_ec_pubkey_create($context, $publicKey_t, $this->getBinary())) {
                throw new \RuntimeException('Failed to create public key');
            }
            /** @var resource $publicKey_t */
            $this->publicKey = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Key\PublicKey($this->ecAdapter, $publicKey_t, $this->compressed);
        }
        return $this->publicKey;
    }
    /**
     * @param \GMP $tweak
     * @return KeyInterface
     */
    public function tweakAdd(\GMP $tweak) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface
    {
        $adapter = $this->ecAdapter;
        $math = $adapter->getMath();
        $context = $adapter->getContext();
        $privateKey = $this->getBinary();
        // mod by reference
        $tweak = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::int($math->toString($tweak), 32)->getBinary();
        $ret = \Ethereumico\EthereumWallet\Dependencies\secp256k1_ec_privkey_tweak_add($context, $privateKey, $tweak);
        if ($ret !== 1) {
            throw new \RuntimeException('Secp256k1 privkey tweak add: failed');
        }
        $secret = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($privateKey);
        return $adapter->getPrivateKey($secret->getGmp(), $this->compressed);
    }
    /**
     * @param \GMP $tweak
     * @return KeyInterface
     */
    public function tweakMul(\GMP $tweak) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface
    {
        $privateKey = $this->getBinary();
        $math = $this->ecAdapter->getMath();
        $tweak = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::int($math->toString($tweak), 32)->getBinary();
        $ret = \Ethereumico\EthereumWallet\Dependencies\secp256k1_ec_privkey_tweak_mul($this->ecAdapter->getContext(), $privateKey, $tweak);
        if ($ret !== 1) {
            throw new \RuntimeException('Secp256k1 privkey tweak mul: failed');
        }
        $secret = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($privateKey);
        return $this->ecAdapter->getPrivateKey($secret->getGmp(), $this->compressed);
    }
    /**
     * @param NetworkInterface $network
     * @return string
     */
    public function toWif(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : string
    {
        $network = $network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork();
        $wifSerializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\PrivateKey\WifPrivateKeySerializer(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Serializer\Key\PrivateKeySerializer($this->ecAdapter));
        return $wifSerializer->serialize($network, $this);
    }
    /**
     * @return BufferInterface
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Serializer\Key\PrivateKeySerializer($this->ecAdapter))->serialize($this);
    }
}
