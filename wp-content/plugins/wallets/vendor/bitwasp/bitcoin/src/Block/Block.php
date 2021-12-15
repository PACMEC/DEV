<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bloom\BloomFilter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\BlockHeaderSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\BlockSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class Block extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockInterface
{
    /**
     * @var Math
     */
    private $math;
    /**
     * @var BlockHeaderInterface
     */
    private $header;
    /**
     * @var TransactionInterface[]
     */
    private $transactions;
    /**
     * @var MerkleRoot
     */
    private $merkleRoot;
    /**
     * Block constructor.
     * @param Math $math
     * @param BlockHeaderInterface $header
     * @param TransactionInterface[] ...$transactions
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math $math, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeaderInterface $header, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface ...$transactions)
    {
        $this->math = $math;
        $this->header = $header;
        $this->transactions = $transactions;
    }
    /**
     * {@inheritdoc}
     * @see \BitWasp\Bitcoin\Block\BlockInterface::getHeader()
     */
    public function getHeader() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeaderInterface
    {
        return $this->header;
    }
    /**
     * {@inheritdoc}
     * @see \BitWasp\Bitcoin\Block\BlockInterface::getMerkleRoot()
     * @throws \BitWasp\Bitcoin\Exceptions\MerkleTreeEmpty
     */
    public function getMerkleRoot() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if (null === $this->merkleRoot) {
            $this->merkleRoot = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\MerkleRoot($this->math, $this->getTransactions());
        }
        return $this->merkleRoot->calculateHash();
    }
    /**
     * @see \BitWasp\Bitcoin\Block\BlockInterface::getTransactions()
     * @return TransactionInterface[]
     */
    public function getTransactions() : array
    {
        return $this->transactions;
    }
    /**
     * @see \BitWasp\Bitcoin\Block\BlockInterface::getTransaction()
     * @param int $i
     * @return TransactionInterface
     */
    public function getTransaction(int $i) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface
    {
        if (!\array_key_exists($i, $this->transactions)) {
            throw new \InvalidArgumentException("No transaction in the block with this index");
        }
        return $this->transactions[$i];
    }
    /**
     * @param BloomFilter $filter
     * @return FilteredBlock
     */
    public function filter(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bloom\BloomFilter $filter) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\FilteredBlock
    {
        $vMatch = [];
        $vHashes = [];
        foreach ($this->getTransactions() as $tx) {
            $vMatch[] = $filter->isRelevantAndUpdate($tx);
            $vHashes[] = $tx->getTxHash();
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\FilteredBlock($this->getHeader(), \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\PartialMerkleTree::create(\count($this->getTransactions()), $vHashes, $vMatch));
    }
    /**
     * {@inheritdoc}
     * @see \BitWasp\Buffertools\SerializableInterface::getBuffer()
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\BlockSerializer($this->math, new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\BlockHeaderSerializer(), new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionSerializer()))->serialize($this);
    }
}
