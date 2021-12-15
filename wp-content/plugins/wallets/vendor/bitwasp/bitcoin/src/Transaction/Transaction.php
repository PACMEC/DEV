<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Util\IntRange;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Utxo\Utxo;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class Transaction extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface
{
    /**
     * @var int
     */
    private $version;
    /**
     * @var TransactionInputInterface[]
     */
    private $inputs;
    /**
     * @var TransactionOutputInterface[]
     */
    private $outputs;
    /**
     * @var ScriptWitnessInterface[]
     */
    private $witness;
    /**
     * @var int
     */
    private $lockTime;
    /**
     * @var BufferInterface
     */
    private $wtxid;
    /**
     * @var BufferInterface
     */
    private $hash;
    /**
     * Transaction constructor.
     *
     * @param int $nVersion
     * @param TransactionInputInterface[] $vin
     * @param TransactionOutputInterface[] $vout
     * @param ScriptWitnessInterface[] $vwit
     * @param int $nLockTime
     */
    public function __construct(int $nVersion = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface::DEFAULT_VERSION, array $vin = [], array $vout = [], array $vwit = [], int $nLockTime = 0)
    {
        if ($nVersion < \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Util\IntRange::I32_MIN || $nVersion > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Util\IntRange::I32_MAX) {
            throw new \InvalidArgumentException('Transaction version is outside valid range');
        }
        if ($nLockTime < 0 || $nLockTime > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface::MAX_LOCKTIME) {
            throw new \InvalidArgumentException('Locktime must be positive and less than ' . \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface::MAX_LOCKTIME);
        }
        $this->version = $nVersion;
        $this->lockTime = $nLockTime;
        $this->inputs = \array_map(function (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface $input) {
            return $input;
        }, $vin);
        $this->outputs = \array_map(function (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface $output) {
            return $output;
        }, $vout);
        $this->witness = \array_map(function (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface $scriptWitness) {
            return $scriptWitness;
        }, $vwit);
    }
    /**
     * @return BufferInterface
     */
    public function getTxHash() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if (null === $this->hash) {
            $this->hash = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d($this->getBaseSerialization());
        }
        return $this->hash;
    }
    /**
     * @return BufferInterface
     */
    public function getTxId() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return $this->getTxHash()->flip();
    }
    /**
     * @return BufferInterface
     */
    public function getWitnessTxId() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if (null === $this->wtxid) {
            $this->wtxid = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d($this->getBuffer())->flip();
        }
        return $this->wtxid;
    }
    /**
     * @return int
     */
    public function getVersion() : int
    {
        return $this->version;
    }
    /**
     * Get the array of inputs in the transaction
     *
     * @return TransactionInputInterface[]
     */
    public function getInputs() : array
    {
        return $this->inputs;
    }
    /**
     * @param int $index
     * @return TransactionInputInterface
     */
    public function getInput(int $index) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface
    {
        if (!isset($this->inputs[$index])) {
            throw new \RuntimeException('No input at this index');
        }
        return $this->inputs[$index];
    }
    /**
     * Get Outputs
     *
     * @return TransactionOutputInterface[]
     */
    public function getOutputs() : array
    {
        return $this->outputs;
    }
    /**
     * @param int $vout
     * @return TransactionOutputInterface
     */
    public function getOutput(int $vout) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface
    {
        if (!isset($this->outputs[$vout])) {
            throw new \RuntimeException('No output at this index');
        }
        return $this->outputs[$vout];
    }
    /**
     * @return bool
     */
    public function hasWitness() : bool
    {
        for ($l = \count($this->inputs), $i = 0; $i < $l; $i++) {
            if (isset($this->witness[$i]) && \count($this->witness[$i]) > 0) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @return ScriptWitnessInterface[]
     */
    public function getWitnesses() : array
    {
        return $this->witness;
    }
    /**
     * @param int $index
     * @return ScriptWitnessInterface
     */
    public function getWitness(int $index) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface
    {
        if (!isset($this->witness[$index])) {
            throw new \RuntimeException('No witness at this index');
        }
        return $this->witness[$index];
    }
    /**
     * @param int $vout
     * @return OutPointInterface
     */
    public function makeOutpoint(int $vout) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface
    {
        $this->getOutput($vout);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPoint($this->getTxId(), $vout);
    }
    /**
     * @param int $vout
     * @return Utxo
     */
    public function makeUtxo(int $vout) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Utxo\Utxo
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Utxo\Utxo(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPoint($this->getTxId(), $vout), $this->getOutput($vout));
    }
    /**
     * Get Lock Time
     *
     * @return int
     */
    public function getLockTime() : int
    {
        return $this->lockTime;
    }
    /**
     * @return int
     */
    public function getValueOut() : int
    {
        $value = 0;
        foreach ($this->outputs as $output) {
            $value = $value + $output->getValue();
        }
        return $value;
    }
    /**
     * @return bool
     */
    public function isCoinbase() : bool
    {
        return \count($this->inputs) === 1 && $this->getInput(0)->isCoinBase();
    }
    /**
     * @param TransactionInterface $tx
     * @return bool
     */
    public function equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface $tx) : bool
    {
        $version = \gmp_cmp($this->version, $tx->getVersion());
        if ($version !== 0) {
            return \false;
        }
        $nIn = \count($this->inputs);
        $nOut = \count($this->outputs);
        $nWit = \count($this->witness);
        // Check the length of each field is equal
        if ($nIn !== \count($tx->getInputs()) || $nOut !== \count($tx->getOutputs()) || $nWit !== \count($tx->getWitnesses())) {
            return \false;
        }
        // Check each field
        for ($i = 0; $i < $nIn; $i++) {
            if (\false === $this->getInput($i)->equals($tx->getInput($i))) {
                return \false;
            }
        }
        for ($i = 0; $i < $nOut; $i++) {
            if (\false === $this->getOutput($i)->equals($tx->getOutput($i))) {
                return \false;
            }
        }
        for ($i = 0; $i < $nWit; $i++) {
            if (\false === $this->getWitness($i)->equals($tx->getWitness($i))) {
                return \false;
            }
        }
        return \gmp_cmp($this->lockTime, $tx->getLockTime()) === 0;
    }
    /**
     * @return BufferInterface
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionSerializer())->serialize($this);
    }
    /**
     * @return BufferInterface
     */
    public function getBaseSerialization() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionSerializer())->serialize($this, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionSerializer::NO_WITNESS);
    }
    /**
     * @return BufferInterface
     */
    public function getWitnessSerialization() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if (!$this->hasWitness()) {
            throw new \RuntimeException('Cannot get witness serialization for transaction without witnesses');
        }
        return $this->getBuffer();
    }
    /**
     * {@inheritdoc}
     * @see TransactionInterface::getWitnessBuffer()
     * @see TransactionInterface::getWitnessSerialization()
     * @deprecated
     */
    public function getWitnessBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return $this->getWitnessSerialization();
    }
}
