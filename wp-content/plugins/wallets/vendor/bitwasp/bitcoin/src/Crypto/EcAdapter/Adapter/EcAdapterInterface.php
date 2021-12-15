<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface EcAdapterInterface
{
    /**
     * @return Math
     */
    public function getMath() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math;
    /**
     * @return \Mdanter\Ecc\Primitives\GeneratorPoint
     */
    public function getGenerator();
    /**
     * @return \GMP
     */
    public function getOrder() : \GMP;
    /**
     * @param BufferInterface $buffer
     * @return bool
     */
    public function validatePrivateKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer) : bool;
    /**
     * @param \GMP $element
     * @param bool|false $halfOrder
     * @return bool
     */
    public function validateSignatureElement(\GMP $element, bool $halfOrder = \false) : bool;
    /**
     * @param \GMP $scalar
     * @param bool|false $compressed
     * @return PrivateKeyInterface
     */
    public function getPrivateKey(\GMP $scalar, bool $compressed = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
    /**
     * @param BufferInterface $messageHash
     * @param CompactSignatureInterface $compactSignature
     * @return PublicKeyInterface
     */
    public function recover(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $messageHash, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface $compactSignature) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
}
