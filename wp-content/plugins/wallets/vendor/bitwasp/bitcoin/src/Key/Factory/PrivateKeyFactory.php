<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Factory;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PrivateKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\Random;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\PrivateKey\WifPrivateKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class PrivateKeyFactory
{
    /**
     * @var PrivateKeySerializerInterface
     */
    private $privSerializer;
    /**
     * @var WifPrivateKeySerializer
     */
    private $wifSerializer;
    /**
     * PrivateKeyFactory constructor.
     * @param EcAdapterInterface $ecAdapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null)
    {
        $ecAdapter = $ecAdapter ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter();
        $this->privSerializer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PrivateKeySerializerInterface::class, \true, $ecAdapter);
        $this->wifSerializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\PrivateKey\WifPrivateKeySerializer($this->privSerializer);
    }
    /**
     * @param Random $random
     * @return PrivateKeyInterface
     * @throws \BitWasp\Bitcoin\Exceptions\RandomBytesFailure
     */
    public function generateCompressed(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\Random $random) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
    {
        return $this->privSerializer->parse($random->bytes(32), \true);
    }
    /**
     * @param Random $random
     * @return PrivateKeyInterface
     * @throws \BitWasp\Bitcoin\Exceptions\RandomBytesFailure
     */
    public function generateUncompressed(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\Random $random) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
    {
        return $this->privSerializer->parse($random->bytes(32), \false);
    }
    /**
     * @param BufferInterface $raw
     * @return PrivateKeyInterface
     * @throws \Exception
     */
    public function fromBufferCompressed(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $raw) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
    {
        return $this->privSerializer->parse($raw, \true);
    }
    /**
     * @param BufferInterface $raw
     * @return PrivateKeyInterface
     * @throws \Exception
     */
    public function fromBufferUncompressed(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $raw) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
    {
        return $this->privSerializer->parse($raw, \false);
    }
    /**
     * @param string $hex
     * @return PrivateKeyInterface
     * @throws \Exception
     */
    public function fromHexCompressed(string $hex) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
    {
        return $this->fromBufferCompressed(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex($hex));
    }
    /**
     * @param string $hex
     * @return PrivateKeyInterface
     * @throws \Exception
     */
    public function fromHexUncompressed(string $hex) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
    {
        return $this->fromBufferUncompressed(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex($hex));
    }
    /**
     * @param string $wif
     * @param NetworkInterface $network
     * @return PrivateKeyInterface
     * @throws \BitWasp\Bitcoin\Exceptions\Base58ChecksumFailure
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidPrivateKey
     * @throws \Exception
     */
    public function fromWif(string $wif, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
    {
        return $this->wifSerializer->parse($wif, $network);
    }
}
