<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Factory;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\Random;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\MultisigHD;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\P2pkhScriptDataFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\Base58ExtendedKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\ExtendedKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class HierarchicalKeyFactory
{
    /**
     * @var EcAdapterInterface
     */
    private $adapter;
    /**
     * @var Base58ExtendedKeySerializer
     */
    private $serializer;
    /**
     * @var PrivateKeyFactory
     */
    private $privFactory;
    /**
     * HierarchicalKeyFactory constructor.
     * @param EcAdapterInterface|null $ecAdapter
     * @param Base58ExtendedKeySerializer|null $serializer
     * @throws \Exception
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\Base58ExtendedKeySerializer $serializer = null)
    {
        $this->adapter = $ecAdapter ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter();
        $this->privFactory = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory($this->adapter);
        $this->serializer = $serializer ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\Base58ExtendedKeySerializer(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\ExtendedKeySerializer($this->adapter));
    }
    /**
     * @param Random $random
     * @param ScriptDataFactory|null $scriptDataFactory
     * @return HierarchicalKey
     * @throws \BitWasp\Bitcoin\Exceptions\RandomBytesFailure
     * @throws \Exception
     */
    public function generateMasterKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\Random $random, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory $scriptDataFactory = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey
    {
        return $this->fromEntropy($random->bytes(64), $scriptDataFactory);
    }
    /**
     * @param BufferInterface $entropy
     * @param ScriptDataFactory|null $scriptFactory
     * @return HierarchicalKey
     * @throws \Exception
     */
    public function fromEntropy(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $entropy, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory $scriptFactory = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey
    {
        $seed = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::hmac('sha512', $entropy, new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer('Bitcoin seed'));
        $privSecret = $seed->slice(0, 32);
        $chainCode = $seed->slice(32, 32);
        $scriptFactory = $scriptFactory ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\P2pkhScriptDataFactory(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface::class, \true, $this->adapter));
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey($this->adapter, $scriptFactory, 0, 0, 0, $chainCode, $this->privFactory->fromBufferCompressed($privSecret));
    }
    /**
     * @param string $extendedKey
     * @param NetworkInterface|null $network
     * @return HierarchicalKey
     * @throws \BitWasp\Bitcoin\Exceptions\Base58ChecksumFailure
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     */
    public function fromExtended(string $extendedKey, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey
    {
        return $this->serializer->parse($network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork(), $extendedKey);
    }
    /**
     * @param ScriptDataFactory $scriptFactory
     * @param HierarchicalKey ...$keys
     * @return MultisigHD
     */
    public function multisig(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory $scriptFactory, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey ...$keys) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\MultisigHD
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\MultisigHD($scriptFactory, ...$keys);
    }
}
