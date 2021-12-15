<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HdPrefix\GlobalPrefixConfig;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\P2pkhScriptDataFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PrivateKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class ExtendedKeySerializer
{
    /**
     * @var EcAdapterInterface
     */
    private $ecAdapter;
    /**
     * @var RawExtendedKeySerializer
     */
    private $rawSerializer;
    /**
     * @var P2pkhScriptDataFactory
     */
    private $defaultScriptFactory;
    /**
     * @var GlobalPrefixConfig
     */
    private $prefixConfig;
    /**
     * @var PrivateKeySerializerInterface
     */
    private $privateKeySerializer;
    /**
     * @var PublicKeySerializerInterface
     */
    private $publicKeySerializer;
    /**
     * @param EcAdapterInterface $ecAdapter
     * @param GlobalPrefixConfig|null $config
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HdPrefix\GlobalPrefixConfig $config = null)
    {
        $this->privateKeySerializer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PrivateKeySerializerInterface::class, \true, $ecAdapter);
        $this->publicKeySerializer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface::class, \true, $ecAdapter);
        $this->ecAdapter = $ecAdapter;
        $this->rawSerializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\RawExtendedKeySerializer($ecAdapter);
        $this->defaultScriptFactory = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\P2pkhScriptDataFactory();
        $this->prefixConfig = $config;
    }
    /**
     * @param NetworkInterface $network
     * @param HierarchicalKey $key
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey $key) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if (null === $this->prefixConfig) {
            if ($key->getScriptDataFactory()->getScriptType() !== $this->defaultScriptFactory->getScriptType()) {
                throw new \InvalidArgumentException("Cannot serialize non-P2PKH HierarchicalKeys without a GlobalPrefixConfig");
            }
            $privatePrefix = $network->getHDPrivByte();
            $publicPrefix = $network->getHDPubByte();
        } else {
            $scriptConfig = $this->prefixConfig->getNetworkConfig($network)->getConfigForScriptType($key->getScriptDataFactory()->getScriptType());
            $privatePrefix = $scriptConfig->getPrivatePrefix();
            $publicPrefix = $scriptConfig->getPublicPrefix();
        }
        if ($key->isPrivate()) {
            $prefix = $privatePrefix;
            $keyData = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer("\0{$key->getPrivateKey()->getBinary()}");
        } else {
            $prefix = $publicPrefix;
            $keyData = $key->getPublicKey()->getBuffer();
        }
        return $this->rawSerializer->serialize(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\RawKeyParams($prefix, $key->getDepth(), $key->getFingerprint(), $key->getSequence(), $key->getChainCode(), $keyData));
    }
    /**
     * @param NetworkInterface $network
     * @param Parser $parser
     * @return HierarchicalKey
     * @throws ParserOutOfRange
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey
    {
        $params = $this->rawSerializer->fromParser($parser);
        if (null === $this->prefixConfig) {
            if (!($params->getPrefix() === $network->getHDPubByte() || $params->getPrefix() === $network->getHDPrivByte())) {
                throw new \InvalidArgumentException('HD key magic bytes do not match network magic bytes');
            }
            $privatePrefix = $network->getHDPrivByte();
            $scriptFactory = $this->defaultScriptFactory;
        } else {
            $scriptConfig = $this->prefixConfig->getNetworkConfig($network)->getConfigForPrefix($params->getPrefix());
            $privatePrefix = $scriptConfig->getPrivatePrefix();
            $scriptFactory = $scriptConfig->getScriptDataFactory();
        }
        if ($params->getPrefix() === $privatePrefix) {
            $key = $this->privateKeySerializer->parse($params->getKeyData()->slice(1), \true);
        } else {
            $key = $this->publicKeySerializer->parse($params->getKeyData());
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey($this->ecAdapter, $scriptFactory, $params->getDepth(), $params->getParentFingerprint(), $params->getSequence(), $params->getChainCode(), $key);
    }
    /**
     * @param NetworkInterface $network
     * @param BufferInterface $buffer
     * @return HierarchicalKey
     * @throws ParserOutOfRange
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey
    {
        return $this->fromParser($network, new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($buffer));
    }
}
