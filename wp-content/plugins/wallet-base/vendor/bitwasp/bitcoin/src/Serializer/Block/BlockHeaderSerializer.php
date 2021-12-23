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
        $this->hash = Types::bytestringle(32);
        $this->uint32le = Types::uint32le();
        $this->int32le = Types::int32le();
    }
    /**
     * @param BufferInterface $buffer
     * @return BlockHeaderInterface
     * @throws ParserOutOfRange
     */
    public function parse(BufferInterface $buffer) : BlockHeaderInterface
    {
        return $this->fromParser(new Parser($buffer));
    }
    /**
     * @param Parser $parser
     * @return BlockHeaderInterface
     * @throws ParserOutOfRange
     */
    public function fromParser(Parser $parser) : BlockHeaderInterface
    {
        try {
            return new BlockHeader((int) $this->int32le->read($parser), $this->hash->read($parser), $this->hash->read($parser), (int) $this->uint32le->read($parser), (int) $this->uint32le->read($parser), (int) $this->uint32le->read($parser));
        } catch (ParserOutOfRange $e) {
            throw new ParserOutOfRange('Failed to extract full block header from parser');
        }
    }
    /**
     * @param BlockHeaderInterface $header
     * @return BufferInterface
     */
    public function serialize(BlockHeaderInterface $header) : BufferInterface
    {
        return new Buffer($this->int32le->write($header->getVersion()) . $this->hash->write($header->getPrevBlock()) . $this->hash->write($header->getMerkleRoot()) . $this->uint32le->write($header->getTimestamp()) . $this->uint32le->write($header->getBits()) . $this->uint32le->write($header->getNonce()));
    }
}
