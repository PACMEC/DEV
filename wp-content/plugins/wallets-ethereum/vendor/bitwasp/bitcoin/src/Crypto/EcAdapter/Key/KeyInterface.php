<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface KeyInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface
{
    /**
     * Check if the key should be be using compressed format
     *
     * @return bool
     */
    public function isCompressed() : bool;
    /**
     * Return a boolean indicating whether the key is private.
     *
     * @return bool
     */
    public function isPrivate() : bool;
    /**
     * Return the hash of the public key.
     *
     * @param PublicKeySerializerInterface|null $serializer
     * @return BufferInterface
     */
    public function getPubKeyHash(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface $serializer = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * @param \GMP $offset
     * @return KeyInterface
     */
    public function tweakAdd(\GMP $offset) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
    /**
     * @param \GMP $offset
     * @return KeyInterface
     */
    public function tweakMul(\GMP $offset) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
    /**
     * @return BufferInterface
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
}
