<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class BitcoindBlockSerializer
{
    /**
     * @var NetworkInterface
     */
    private $network;
    /**
     * @var BlockSerializer
     */
    private $blockSerializer;
    /**
     * @var \BitWasp\Buffertools\Types\ByteString
     */
    private $magic;
    /**
     * @var \BitWasp\Buffertools\Types\Uint32
     */
    private $size;
    /**
     * @param NetworkInterface $network
     * @param BlockSerializer $blockSerializer
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\BlockSerializer $blockSerializer)
    {
        $this->blockSerializer = $blockSerializer;
        $this->magic = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::bytestringle(4);
        $this->size = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::uint32le();
        $this->network = $network;
    }
    /**
     * @param BlockInterface $block
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockInterface $block) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $buffer = $this->blockSerializer->serialize($block);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\sprintf("%s%s%s", \strrev(\pack("H*", $this->network->getNetMagicBytes())), \pack("V", $buffer->getSize()), $buffer->getBinary()));
    }
    /**
     * @param Parser $parser
     * @return BlockInterface
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     * @throws \Exception
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser)
    {
        /**
         * @var Buffer $bytes
         * @var int $blockSize
         */
        list($bytes, $blockSize) = [$this->magic->read($parser), (int) $this->size->read($parser)];
        if ($bytes->getHex() !== $this->network->getNetMagicBytes()) {
            throw new \RuntimeException('Block version bytes did not match network');
        }
        return $this->blockSerializer->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($parser->readBytes($blockSize)));
    }
    /**
     * @param BufferInterface $data
     * @return BlockInterface
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockInterface
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($data));
    }
}
