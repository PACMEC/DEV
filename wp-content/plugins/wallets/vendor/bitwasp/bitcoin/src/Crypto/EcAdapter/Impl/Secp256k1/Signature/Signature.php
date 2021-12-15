<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Adapter\EcAdapter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Serializer\Signature\DerSignatureSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class Signature extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface
{
    /**
     * @var \GMP
     */
    private $r;
    /**
     * @var  \GMP
     */
    private $s;
    /**
     * @var EcAdapter
     */
    private $ecAdapter;
    /**
     * @var resource
     */
    private $secp256k1_sig;
    /**
     * @param EcAdapter $adapter
     * @param \GMP $r
     * @param \GMP $s
     * @param resource $secp256k1_ecdsa_signature_t
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Adapter\EcAdapter $adapter, \GMP $r, \GMP $s, $secp256k1_ecdsa_signature_t)
    {
        if (!\is_resource($secp256k1_ecdsa_signature_t) || !\get_resource_type($secp256k1_ecdsa_signature_t) === SECP256K1_TYPE_SIG) {
            throw new \InvalidArgumentException('Secp256k1\\Signature\\Signature expects ' . SECP256K1_TYPE_SIG . ' resource');
        }
        $this->secp256k1_sig = $secp256k1_ecdsa_signature_t;
        $this->ecAdapter = $adapter;
        $this->r = $r;
        $this->s = $s;
    }
    /**
     * @return \GMP
     */
    public function getR()
    {
        return $this->r;
    }
    /**
     * @return \GMP
     */
    public function getS()
    {
        return $this->s;
    }
    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->secp256k1_sig;
    }
    /**
     * @param Signature $other
     * @return bool
     */
    private function doEquals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\Signature $other) : bool
    {
        $a = '';
        $b = '';
        secp256k1_ecdsa_signature_serialize_der($this->ecAdapter->getContext(), $a, $this->getResource());
        secp256k1_ecdsa_signature_serialize_der($this->ecAdapter->getContext(), $b, $other->getResource());
        return \hash_equals($a, $b);
    }
    /**
     * @param SignatureInterface $signature
     * @return bool
     */
    public function equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface $signature) : bool
    {
        /** @var Signature $signature */
        return $this->doEquals($signature);
    }
    /**
     * @return BufferInterface
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Serializer\Signature\DerSignatureSerializer($this->ecAdapter))->serialize($this);
    }
}