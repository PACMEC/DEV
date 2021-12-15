<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Chain;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Chain\BlockLocator;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class BlockLocatorSerializer
{
    /**
     * @var \BitWasp\Buffertools\Types\VarInt
     */
    private $varint;
    /**
     * @var \BitWasp\Buffertools\Types\ByteString
     */
    private $bytestring32le;
    public function __construct()
    {
        $this->varint = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::varint();
        $this->bytestring32le = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::bytestringle(32);
    }
    /**
     * @param Parser $parser
     * @return BlockLocator
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Chain\BlockLocator
    {
        $numHashes = $this->varint->read($parser);
        $hashes = [];
        for ($i = 0; $i < $numHashes; $i++) {
            $hashes[] = $this->bytestring32le->read($parser);
        }
        $hashStop = $this->bytestring32le->read($parser);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Chain\BlockLocator($hashes, $hashStop);
    }
    /**
     * @param BufferInterface $data
     * @return BlockLocator
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Chain\BlockLocator
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($data));
    }
    /**
     * @param BlockLocator $blockLocator
     * @return BufferInterface
     * @throws \Exception
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Chain\BlockLocator $blockLocator) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $binary = $this->varint->write(\count($blockLocator->getHashes()));
        foreach ($blockLocator->getHashes() as $hash) {
            $binary .= $this->bytestring32le->write($hash);
        }
        $binary .= $this->bytestring32le->write($blockLocator->getHashStop());
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($binary);
    }
}
