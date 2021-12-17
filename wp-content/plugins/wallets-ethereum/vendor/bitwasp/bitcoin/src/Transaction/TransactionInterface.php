<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Utxo\Utxo;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface TransactionInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface
{
    const DEFAULT_VERSION = 1;
    /**
     * The locktime parameter is encoded as a uint32
     */
    const MAX_LOCKTIME = 4294967295;
    /**
     * @return bool
     */
    public function isCoinbase() : bool;
    /**
     * Get the transactions sha256d hash.
     *
     * @return BufferInterface
     */
    public function getTxHash() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * Get the little-endian sha256d hash.
     * @return BufferInterface
     */
    public function getTxId() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * Get the little endian sha256d hash including witness data
     * @return BufferInterface
     */
    public function getWitnessTxId() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * Get the version of this transaction
     *
     * @return int
     */
    public function getVersion() : int;
    /**
     * Return an array of all inputs
     *
     * @return TransactionInputInterface[]
     */
    public function getInputs() : array;
    /**
     * @param int $index
     * @return TransactionInputInterface
     */
    public function getInput(int $index) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface;
    /**
     * Return an array of all outputs
     *
     * @return TransactionOutputInterface[]
     */
    public function getOutputs() : array;
    /**
     * @param int $vout
     * @return TransactionOutputInterface
     */
    public function getOutput(int $vout) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
    /**
     * @param int $index
     * @return ScriptWitnessInterface
     */
    public function getWitness(int $index) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface;
    /**
     * @return ScriptWitnessInterface[]
     */
    public function getWitnesses() : array;
    /**
     * @param int $vout
     * @return OutPointInterface
     */
    public function makeOutPoint(int $vout) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface;
    /**
     * @param int $vout
     * @return Utxo
     */
    public function makeUtxo(int $vout) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Utxo\Utxo;
    /**
     * Return the locktime for this transaction
     *
     * @return int
     */
    public function getLockTime() : int;
    /**
     * @return int
     */
    public function getValueOut();
    /**
     * @return bool
     */
    public function hasWitness() : bool;
    /**
     * @param TransactionInterface $tx
     * @return bool
     */
    public function equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface $tx) : bool;
    /**
     * @return BufferInterface
     */
    public function getBaseSerialization() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * @return BufferInterface
     */
    public function getWitnessSerialization() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * @deprecated
     * @return BufferInterface
     */
    public function getWitnessBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
}
