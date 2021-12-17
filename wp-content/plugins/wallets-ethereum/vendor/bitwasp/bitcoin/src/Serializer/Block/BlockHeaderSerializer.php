<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeader;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeaderInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class BlockHeaderSerializer
{
    /**
     * @var \BitWasp\Buffertools\Types\Int32
     */
    private $int32le;
    /**
     * @var \BitWasp\Buffertools\Types\ByteString
     */
    private $hash;
    /**
     * @var \BitWasp\Buffertools\Types\Uint32
     */
    private $uint32le;
    public function __construct()
    {
        $this->hash = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::bytestringle(32);
        $this->uint32le = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::uint32le();
        $this->int32le = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::int32le();
    }
    /**
     * @param BufferInterface $buffer
     * @return BlockHeaderInterface
     * @throws ParserOutOfRange
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeaderInterface
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($buffer));
    }
    /**
     * @param Parser $parser
     * @return BlockHeaderInterface
     * @throws ParserOutOfRange
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeaderInterface
    {
        try {
            return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeader((int) $this->int32le->read($parser), $this->hash->read($parser), $this->hash->read($parser), (int) $this->uint32le->read($parser), (int) $this->uint32le->read($parser), (int) $this->uint32le->read($parser));
        } catch (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange $e) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange('Failed to extract full block header from parser');
        }
    }
    /**
     * @param BlockHeaderInterface $header
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeaderInterface $header) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($this->int32le->write($header->getVersion()) . $this->hash->write($header->getPrevBlock()) . $this->hash->write($header->getMerkleRoot()) . $this->uint32le->write($header->getTimestamp()) . $this->uint32le->write($header->getBits()) . $this->uint32le->write($header->getNonce()));
    }
}
