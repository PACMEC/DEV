<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\FilteredBlock;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class FilteredBlockSerializer
{
    /**
     * @var BlockHeaderSerializer
     */
    private $headerSerializer;
    /**
     * @var PartialMerkleTreeSerializer
     */
    private $treeSerializer;
    /**
     * @param BlockHeaderSerializer $header
     * @param PartialMerkleTreeSerializer $tree
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\BlockHeaderSerializer $header, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\PartialMerkleTreeSerializer $tree)
    {
        $this->headerSerializer = $header;
        $this->treeSerializer = $tree;
    }
    /**
     * @param Parser $parser
     * @return FilteredBlock
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\FilteredBlock
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\FilteredBlock($this->headerSerializer->fromParser($parser), $this->treeSerializer->fromParser($parser));
    }
    /**
     * @param BufferInterface $data
     * @return FilteredBlock
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\FilteredBlock
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($data));
    }
    /**
     * @param FilteredBlock $merkleBlock
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\FilteredBlock $merkleBlock) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::concat($this->headerSerializer->serialize($merkleBlock->getHeader()), $this->treeSerializer->serialize($merkleBlock->getPartialTree()));
    }
}
