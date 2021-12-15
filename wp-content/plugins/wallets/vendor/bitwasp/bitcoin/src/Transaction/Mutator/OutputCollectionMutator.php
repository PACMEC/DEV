<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
class OutputCollectionMutator extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator\AbstractCollectionMutator
{
    /**
     * @param TransactionOutputInterface[] $outputs
     */
    public function __construct(array $outputs)
    {
        /** @var OutputMutator[] $set */
        $this->set = new \SplFixedArray(\count($outputs));
        foreach ($outputs as $i => $output) {
            /** @var int $i */
            $this->set[$i] = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator\OutputMutator($output);
        }
    }
    /**
     * @return OutputMutator
     */
    public function current() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator\OutputMutator
    {
        return $this->set->current();
    }
    /**
     * @param int $offset
     * @return OutputMutator
     */
    public function offsetGet($offset) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator\OutputMutator
    {
        if (!$this->set->offsetExists($offset)) {
            throw new \OutOfRangeException('Nothing found at this offset');
        }
        return $this->set->offsetGet($offset);
    }
    /**
     * @return TransactionOutputInterface[]
     */
    public function done() : array
    {
        $set = [];
        foreach ($this->set as $mutator) {
            $set[] = $mutator->done();
        }
        return $set;
    }
    /**
     * @param int $start
     * @param int $length
     * @return $this
     */
    public function slice(int $start, int $length)
    {
        $end = \count($this->set);
        if ($start > $end || $length > $end) {
            throw new \RuntimeException('Invalid start or length');
        }
        $this->set = \SplFixedArray::fromArray(\array_slice($this->set->toArray(), $start, $length), \false);
        return $this;
    }
    /**
     * @return $this
     */
    public function null()
    {
        $this->slice(0, 0);
        return $this;
    }
    /**
     * @param TransactionOutputInterface $output
     * @return $this
     */
    public function add(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface $output)
    {
        $size = $this->set->getSize();
        $this->set->setSize($size + 1);
        $this->set[$size] = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator\OutputMutator($output);
        return $this;
    }
    /**
     * @param int $i
     * @param TransactionOutputInterface $output
     * @return $this
     */
    public function set($i, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface $output)
    {
        $this->set[$i] = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator\OutputMutator($output);
        return $this;
    }
}
