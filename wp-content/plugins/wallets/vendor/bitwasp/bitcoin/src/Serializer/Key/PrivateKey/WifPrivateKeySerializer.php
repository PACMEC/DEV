<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\PrivateKey;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Base58;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PrivateKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\Base58ChecksumFailure;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\InvalidPrivateKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
class WifPrivateKeySerializer
{
    /**
     * @var PrivateKeySerializerInterface
     */
    private $keySerializer;
    /**
     * @param PrivateKeySerializerInterface $serializer
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PrivateKeySerializerInterface $serializer)
    {
        $this->keySerializer = $serializer;
    }
    /**
     * @param NetworkInterface $network
     * @param PrivateKeyInterface $privateKey
     * @return string
     * @throws \Exception
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface $privateKey) : string
    {
        $prefix = \pack("H*", $network->getPrivByte());
        if ($privateKey->isCompressed()) {
            $ending = "\1";
        } else {
            $ending = "";
        }
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Base58::encodeCheck(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer("{$prefix}{$this->keySerializer->serialize($privateKey)->getBinary()}{$ending}"));
    }
    /**
     * @param string $wif
     * @param NetworkInterface|null $network
     * @return PrivateKeyInterface
     * @throws Base58ChecksumFailure
     * @throws InvalidPrivateKey
     * @throws \Exception
     */
    public function parse(string $wif, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
    {
        $network = $network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork();
        $data = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Base58::decodeCheck($wif);
        if ($data->slice(0, 1)->getHex() !== $network->getPrivByte()) {
            throw new \RuntimeException('WIF prefix does not match networks');
        }
        $payload = $data->slice(1);
        $size = $payload->getSize();
        if (33 === $size) {
            $compressed = \true;
            $payload = $payload->slice(0, 32);
        } else {
            if (32 === $size) {
                $compressed = \false;
            } else {
                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\InvalidPrivateKey("Private key should be always be 32 or 33 bytes (depending on if it's compressed)");
            }
        }
        return $this->keySerializer->parse($payload, $compressed);
    }
}
