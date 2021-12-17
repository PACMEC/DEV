<?php

namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Consensus;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Checker;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
class NativeConsensus implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Consensus\ConsensusInterface
{
    /**
     * @var EcAdapterInterface
     */
    private $adapter;
    /**
     * NativeConsensus constructor.
     * @param EcAdapterInterface $ecAdapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null)
    {
        $this->adapter = $ecAdapter ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter();
    }
    /**
     * @param TransactionInterface $tx
     * @param ScriptInterface $scriptPubKey
     * @param int $nInputToSign
     * @param int $flags
     * @param int $amount
     * @return bool
     */
    public function verify(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface $tx, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $scriptPubKey, int $flags, int $nInputToSign, int $amount) : bool
    {
        $inputs = $tx->getInputs();
        $interpreter = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter($this->adapter);
        return $interpreter->verify($inputs[$nInputToSign]->getScript(), $scriptPubKey, $flags, new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Checker($this->adapter, $tx, $nInputToSign, $amount), isset($tx->getWitnesses()[$nInputToSign]) ? $tx->getWitness($nInputToSign) : null);
    }
}
