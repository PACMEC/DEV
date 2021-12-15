<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class RawExtendedKeySerializer
{
    /**
     * @var EcAdapterInterface
     */
    private $ecAdapter;
    /**
     * @var \BitWasp\Buffertools\Types\ByteString
     */
    private $bytestring4;
    /**
     * @var \BitWasp\Buffertools\Types\Uint8
     */
    private $uint8;
    /**
     * @var \BitWasp\Buffertools\Types\Uint32
     */
    private $uint32;
    /**
     * @var \BitWasp\Buffertools\Types\ByteString
     */
    private $bytestring32;
    /**
     * @var \BitWasp\Buffertools\Types\ByteString
     */
    private $bytestring33;
    /**
     * RawExtendedKeySerializer constructor.
     * @param EcAdapterInterface $ecAdapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter)
    {
        $this->ecAdapter = $ecAdapter;
        $this->bytestring4 = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::bytestring(4);
        $this->uint8 = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::uint8();
        $this->uint32 = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::uint32();
        $this->bytestring32 = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::bytestring(32);
        $this->bytestring33 = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::bytestring(33);
    }
    /**
     * @param RawKeyParams $keyParams
     * @return BufferInterface
     * @throws \Exception
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\RawKeyParams $keyParams) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\pack("H*", $keyParams->getPrefix()) . $this->uint8->write($keyParams->getDepth()) . $this->uint32->write($keyParams->getParentFingerprint()) . $this->uint32->write($keyParams->getSequence()) . $this->bytestring32->write($keyParams->getChainCode()) . $this->bytestring33->write($keyParams->getKeyData()));
    }
    /**
     * @param Parser $parser
     * @return RawKeyParams
     * @throws ParserOutOfRange
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\RawKeyParams
    {
        try {
            return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\RawKeyParams($this->bytestring4->read($parser)->getHex(), (int) $this->uint8->read($parser), (int) $this->uint32->read($parser), (int) $this->uint32->read($parser), $this->bytestring32->read($parser), $this->bytestring33->read($parser));
        } catch (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange $e) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange('Failed to extract HierarchicalKey from parser');
        }
    }
}
