<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bloom\BloomFilter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface BlockInterface extends SerializableInterface
{
    const MAX_BLOCK_SIZE = 1000000;
    /**
     * Get the header of this block.
     *
     * @return BlockHeaderInterface
     */
    public function getHeader() : BlockHeaderInterface;
    /**
     * Calculate the merkle root of the transactions in the block.
     *
     * @return BufferInterface
     */
    public function getMerkleRoot() : BufferInterface;
    /**
     * Return the block's transactions.
     *
     * @return TransactionInterface[]
     */
    public function getTransactions() : array;
    /**
     * @param int $i
     * @return TransactionInterface
     */
    public function getTransaction(int $i) : TransactionInterface;
    /**
     * @param BloomFilter $filter
     * @return FilteredBlock
     */
    public function filter(BloomFilter $filter) : FilteredBlock;
}
