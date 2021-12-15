<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\PartialMerkleTree;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Template;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory;
class PartialMerkleTreeSerializer
{
    /**
     * @var \BitWasp\Buffertools\Template
     */
    private $template;
    /**
     * PartialMerkleTreeSerializer constructor.
     */
    public function __construct()
    {
        $this->template = $this->getTemplate();
    }
    /**
     * @return Template
     */
    public function getTemplate() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Template
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory())->uint32le()->vector(function (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) {
            return $parser->readBytes(32);
        })->vector(function (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) {
            return $parser->readBytes(1);
        })->getTemplate();
    }
    /**
     * @param int $last
     * @param BufferInterface[] $vBytes
     * @return array
     */
    private function buffersToBitArray($last, array $vBytes) : array
    {
        $size = \count($vBytes) * 8;
        $vBits = [];
        for ($p = 0; $p < $size; $p++) {
            $byteIndex = (int) \floor($p / 8);
            $byte = \ord($vBytes[$byteIndex]->getBinary());
            $vBits[$p] = (int) (($byte & 1 << $p % 8) !== 0);
        }
        return \array_slice($vBits, 0, $last);
    }
    /**
     * @param Parser $parser
     * @return PartialMerkleTree
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\PartialMerkleTree
    {
        list($txCount, $vHash, $vBits) = $this->template->parse($parser);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\PartialMerkleTree((int) $txCount, $vHash, $this->buffersToBitArray($txCount, $vBits));
    }
    /**
     * @param BufferInterface $buffer
     * @return PartialMerkleTree
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\PartialMerkleTree
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($buffer));
    }
    /**
     * @param array $bits
     * @return array
     */
    private function bitsToBuffers(array $bits) : array
    {
        $vBuffers = \str_split(\str_pad('', (int) ((\count($bits) + 7) / 8), '0', \STR_PAD_LEFT));
        $nBits = \count($bits);
        for ($p = 0; $p < $nBits; $p++) {
            $index = (int) \floor($p / 8);
            $vBuffers[$index] |= $bits[$p] << $p % 8;
        }
        foreach ($vBuffers as &$value) {
            $value = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::int($value);
        }
        unset($value);
        return $vBuffers;
    }
    /**
     * @param PartialMerkleTree $tree
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\PartialMerkleTree $tree) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return $this->template->write([$tree->getTxCount(), $tree->getHashes(), $this->bitsToBuffers($tree->getFlagBits())]);
    }
}
