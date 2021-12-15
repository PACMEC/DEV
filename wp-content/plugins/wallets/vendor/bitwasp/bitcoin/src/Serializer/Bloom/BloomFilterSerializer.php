<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Bloom;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bloom\BloomFilter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class BloomFilterSerializer
{
    /**
     * @var \BitWasp\Buffertools\Types\Uint32
     */
    private $uint32le;
    /**
     * @var \BitWasp\Buffertools\Types\Uint8
     */
    private $uint8le;
    /**
     * @var \BitWasp\Buffertools\Types\VarInt
     */
    private $varint;
    public function __construct()
    {
        $this->uint32le = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::uint32le();
        $this->uint8le = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::uint8le();
        $this->varint = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::varint();
    }
    /**
     * @param BloomFilter $filter
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bloom\BloomFilter $filter) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $parser = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser();
        $parser->appendBinary($this->varint->write(\count($filter->getData())));
        foreach ($filter->getData() as $i) {
            $parser->appendBinary(\pack('c', $i));
        }
        $parser->appendBinary($this->uint32le->write($filter->getNumHashFuncs()));
        $parser->appendBinary($this->uint32le->write($filter->getTweak()));
        $parser->appendBinary($this->uint8le->write($filter->getFlags()));
        return $parser->getBuffer();
    }
    /**
     * @param Parser $parser
     * @return BloomFilter
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bloom\BloomFilter
    {
        $varint = (int) $this->varint->read($parser);
        $vData = [];
        for ($i = 0; $i < $varint; $i++) {
            $vData[] = (int) $this->uint8le->read($parser);
        }
        $nHashFuncs = (int) $this->uint32le->read($parser);
        $nTweak = (int) $this->uint32le->read($parser);
        $flags = (int) $this->uint8le->read($parser);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bloom\BloomFilter(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getMath(), $vData, $nHashFuncs, $nTweak, $flags);
    }
    /**
     * @param BufferInterface $data
     * @return BloomFilter
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bloom\BloomFilter
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($data));
    }
}
