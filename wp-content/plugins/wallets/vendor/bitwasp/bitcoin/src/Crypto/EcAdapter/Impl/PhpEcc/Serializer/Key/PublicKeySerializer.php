<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Key;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class PublicKeySerializer implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface
{
    /**
     * @var EcAdapter
     */
    private $ecAdapter;
    /**
     * @param EcAdapter $ecAdapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter $ecAdapter)
    {
        $this->ecAdapter = $ecAdapter;
    }
    /**
     * @param PublicKey $publicKey
     * @return string
     */
    public function getPrefix(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey $publicKey) : string
    {
        if (null === $publicKey->getPrefix()) {
            return $publicKey->isCompressed() ? $this->ecAdapter->getMath()->isEven($publicKey->getPoint()->getY()) ? \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey::KEY_COMPRESSED_EVEN : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey::KEY_COMPRESSED_ODD : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey::KEY_UNCOMPRESSED;
        } else {
            return $publicKey->getPrefix();
        }
    }
    /**
     * @param PublicKey $publicKey
     * @return BufferInterface
     */
    private function doSerialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey $publicKey) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $point = $publicKey->getPoint();
        $length = 33;
        $data = $this->getPrefix($publicKey) . \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::int(\gmp_strval($point->getX(), 10), 32)->getBinary();
        if (!$publicKey->isCompressed()) {
            $length = 65;
            $data .= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::int(\gmp_strval($point->getY(), 10), 32)->getBinary();
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($data, $length);
    }
    /**
     * @param PublicKeyInterface $publicKey
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface $publicKey) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        /** @var PublicKey $publicKey */
        return $this->doSerialize($publicKey);
    }
    /**
     * @param BufferInterface $buffer
     * @return PublicKeyInterface
     * @throws \Exception
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface
    {
        if (!\in_array($buffer->getSize(), [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey::LENGTH_COMPRESSED, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey::LENGTH_UNCOMPRESSED], \true)) {
            throw new \Exception('Invalid hex string, must match size of compressed or uncompressed public key');
        }
        /** @var PublicKey $key */
        $key = $this->ecAdapter->publicKeyFromBuffer($buffer);
        return $key;
    }
}
