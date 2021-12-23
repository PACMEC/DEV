<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\BlockHeaderSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\FilteredBlockSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\PartialMerkleTreeSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class FilteredBlock extends Serializable
{
    /**
     * @var BlockHeaderInterface
     */
    private $header;
    /**
     * @var PartialMerkleTree
     */
    private $partialTree;
    /**
     * @param BlockHeaderInterface $header
     * @param PartialMerkleTree $merkleTree
     */
    public function __construct(BlockHeaderInterface $header, PartialMerkleTree $merkleTree)
    {
        $this->header = $header;
        $this->partialTree = $merkleTree;
    }
    /**
     * @return BlockHeaderInterface
     */
    public function getHeader() : BlockHeaderInterface
    {
        return $this->header;
    }
    /**
     * @return PartialMerkleTree
     */
    public function getPartialTree() : PartialMerkleTree
    {
        return $this->partialTree;
    }
    /**
     * @return BufferInterface
     */
    public function getBuffer() : BufferInterface
    {
        return (new FilteredBlockSerializer(new BlockHeaderSerializer(), new PartialMerkleTreeSerializer()))->serialize($this);
    }
}
