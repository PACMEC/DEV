<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\AddressInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Locktime;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Bip69\Bip69;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPoint;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Transaction;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInput;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutput;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class TxBuilder
{
    /**
     * @var int
     */
    private $nVersion;
    /**
     * @var array
     */
    private $inputs;
    /**
     * @var array
     */
    private $outputs;
    /**
     * @var array
     */
    private $witness;
    /**
     * @var int
     */
    private $nLockTime;
    public function __construct()
    {
        $this->reset();
    }
    /**
     * @return $this
     */
    public function reset()
    {
        $this->nVersion = 1;
        $this->inputs = [];
        $this->outputs = [];
        $this->witness = [];
        $this->nLockTime = 0;
        return $this;
    }
    /**
     * @return TransactionInterface
     */
    private function makeTransaction() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Transaction($this->nVersion, $this->inputs, $this->outputs, $this->witness, $this->nLockTime);
    }
    /**
     * @return TransactionInterface
     */
    public function get() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface
    {
        return $this->makeTransaction();
    }
    /**
     * @return TransactionInterface
     */
    public function getAndReset() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface
    {
        $transaction = $this->makeTransaction();
        $this->reset();
        return $transaction;
    }
    /**
     * @param int $nVersion
     * @return $this
     */
    public function version(int $nVersion)
    {
        $this->nVersion = $nVersion;
        return $this;
    }
    /**
     * @param BufferInterface|string $hashPrevOut - hex or BufferInterface
     * @param int $nPrevOut
     * @param ScriptInterface $script
     * @param int $nSequence
     * @return $this
     */
    public function input($hashPrevOut, int $nPrevOut, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script = null, int $nSequence = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface::SEQUENCE_FINAL)
    {
        if ($hashPrevOut instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface) {
            if ($hashPrevOut->getSize() !== 32) {
                throw new \InvalidArgumentException("Invalid size for txid buffer");
            }
        } else {
            if (\is_string($hashPrevOut)) {
                $hashPrevOut = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex($hashPrevOut, 32);
            } else {
                throw new \InvalidArgumentException("Invalid value for hashPrevOut in TxBuilder::input");
            }
        }
        $this->inputs[] = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInput(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPoint($hashPrevOut, $nPrevOut), $script ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script(), $nSequence);
        return $this;
    }
    /**
     * @param TransactionInputInterface[] $inputs
     * @return $this
     */
    public function inputs(array $inputs)
    {
        \array_walk($inputs, function (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface $input) {
            $this->inputs[] = $input;
        });
        return $this;
    }
    /**
     * @param integer $value
     * @param ScriptInterface $script
     * @return $this
     */
    public function output(int $value, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script)
    {
        $this->outputs[] = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutput($value, $script);
        return $this;
    }
    /**
     * @param TransactionOutputInterface[] $outputs
     * @return $this
     */
    public function outputs(array $outputs)
    {
        \array_walk($outputs, function (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface $output) {
            $this->outputs[] = $output;
        });
        return $this;
    }
    /**
     * @param ScriptWitnessInterface[] $witness
     * @return $this
     */
    public function witnesses(array $witness)
    {
        \array_walk($witness, function (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface $witness) {
            $this->witness[] = $witness;
        });
        return $this;
    }
    /**
     * @param int $locktime
     * @return $this
     */
    public function locktime(int $locktime)
    {
        $this->nLockTime = $locktime;
        return $this;
    }
    /**
     * @param Locktime $lockTime
     * @param int $nTimestamp
     * @return $this
     * @throws \Exception
     */
    public function lockToTimestamp(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Locktime $lockTime, int $nTimestamp)
    {
        $this->locktime($lockTime->fromTimestamp($nTimestamp));
        return $this;
    }
    /**
     * @param Locktime $lockTime
     * @param int $blockHeight
     * @return $this
     * @throws \Exception
     */
    public function lockToBlockHeight(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Locktime $lockTime, int $blockHeight)
    {
        $this->locktime($lockTime->fromBlockHeight($blockHeight));
        return $this;
    }
    /**
     * @param OutPointInterface $outpoint
     * @param ScriptInterface|null $script
     * @param int $nSequence
     * @return $this
     */
    public function spendOutPoint(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface $outpoint, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script = null, int $nSequence = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface::SEQUENCE_FINAL)
    {
        $this->inputs[] = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInput($outpoint, $script ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script(), $nSequence);
        return $this;
    }
    /**
     * @param TransactionInterface $transaction
     * @param int $outputToSpend
     * @param ScriptInterface|null $script
     * @param int $nSequence
     * @return $this
     */
    public function spendOutputFrom(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface $transaction, int $outputToSpend, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script = null, int $nSequence = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface::SEQUENCE_FINAL)
    {
        // Check TransactionOutput exists in $tx
        $transaction->getOutput($outputToSpend);
        $this->input($transaction->getTxId(), $outputToSpend, $script, $nSequence);
        return $this;
    }
    /**
     * Create an output paying $value to an Address.
     *
     * @param int $value
     * @param AddressInterface $address
     * @return $this
     */
    public function payToAddress(int $value, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\AddressInterface $address)
    {
        // Create Script from address, then create an output.
        $this->output($value, $address->getScriptPubKey());
        return $this;
    }
    /**
     * Sorts the transaction inputs and outputs lexicographically,
     * according to BIP69
     *
     * @param Bip69 $bip69
     * @return $this
     */
    public function bip69(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Bip69\Bip69 $bip69)
    {
        list($inputs, $witness) = $bip69->sortInputsAndWitness($this->inputs, $this->witness);
        $this->inputs = $inputs;
        $this->outputs = $bip69->sortOutputs($this->outputs);
        $this->witness = $witness;
        return $this;
    }
}
