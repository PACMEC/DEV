<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\MerkleTreeEmpty;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\Pleo\Merkle\FixedSizeTree;
class MerkleRoot
{
    /**
     * @var TransactionInterface[]
     */
    private $transactions;
    /**
     * @var Math
     */
    private $math;
    /**
     * @var BufferInterface
     */
    private $lastHash;
    /**
     * Instantiate the class when given a block
     *
     * @param Math $math
     * @param TransactionInterface[] $txCollection
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math $math, array $txCollection)
    {
        $this->math = $math;
        $this->transactions = $txCollection;
    }
    /**
     * @param callable|null $hashFunction
     * @return BufferInterface
     * @throws MerkleTreeEmpty
     */
    public function calculateHash(callable $hashFunction = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if ($this->lastHash instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface) {
            return $this->lastHash;
        }
        $hashFxn = $hashFunction ?: function ($value) {
            return \hash('sha256', \hash('sha256', $value, \true), \true);
        };
        $txCount = \count($this->transactions);
        if ($txCount === 0) {
            // TODO: Probably necessary. Should always have a coinbase at least.
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\MerkleTreeEmpty('Cannot compute Merkle root of an empty tree');
        }
        if ($txCount === 1) {
            $binary = $hashFxn($this->transactions[0]->getBinary());
        } else {
            // Create a fixed size Merkle Tree
            $tree = new \Ethereumico\EthereumWallet\Dependencies\Pleo\Merkle\FixedSizeTree($txCount + $txCount % 2, $hashFxn);
            // Compute hash of each transaction
            $last = '';
            foreach ($this->transactions as $i => $transaction) {
                $last = $transaction->getBinary();
                $tree->set($i, $last);
            }
            // Check if we need to repeat the last hash (odd number of transactions)
            if (!($txCount % 2 === 0)) {
                $tree->set($txCount, $last);
            }
            $binary = $tree->hash();
        }
        $this->lastHash = (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($binary))->flip();
        return $this->lastHash;
    }
}