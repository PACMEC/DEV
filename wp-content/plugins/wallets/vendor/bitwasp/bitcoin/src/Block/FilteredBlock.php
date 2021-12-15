<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\BlockHeaderSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\FilteredBlockSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\PartialMerkleTreeSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class FilteredBlock extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable
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
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeaderInterface $header, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\PartialMerkleTree $merkleTree)
    {
        $this->header = $header;
        $this->partialTree = $merkleTree;
    }
    /**
     * @return BlockHeaderInterface
     */
    public function getHeader() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeaderInterface
    {
        return $this->header;
    }
    /**
     * @return PartialMerkleTree
     */
    public function getPartialTree() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\PartialMerkleTree
    {
        return $this->partialTree;
    }
    /**
     * @return BufferInterface
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\FilteredBlockSerializer(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\BlockHeaderSerializer(), new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\PartialMerkleTreeSerializer()))->serialize($this);
    }
}
