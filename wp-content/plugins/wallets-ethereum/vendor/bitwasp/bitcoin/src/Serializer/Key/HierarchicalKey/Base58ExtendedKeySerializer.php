<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Base58;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
class Base58ExtendedKeySerializer
{
    /**
     * @var ExtendedKeySerializer
     */
    private $serializer;
    /**
     * @param ExtendedKeySerializer $hdSerializer
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\ExtendedKeySerializer $hdSerializer)
    {
        $this->serializer = $hdSerializer;
    }
    /**
     * @param NetworkInterface $network
     * @param HierarchicalKey $key
     * @return string
     * @throws \Exception
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey $key) : string
    {
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Base58::encodeCheck($this->serializer->serialize($network, $key));
    }
    /**
     * @param NetworkInterface $network
     * @param string $base58
     * @return HierarchicalKey
     * @throws \BitWasp\Bitcoin\Exceptions\Base58ChecksumFailure
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network, string $base58) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey
    {
        return $this->serializer->parse($network, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Base58::decodeCheck($base58));
    }
}
