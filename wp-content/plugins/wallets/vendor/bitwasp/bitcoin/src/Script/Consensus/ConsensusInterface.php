<?php

namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Consensus;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
interface ConsensusInterface
{
    /**
     * @param TransactionInterface $tx
     * @param ScriptInterface $scriptPubKey
     * @param integer $nInputToSign
     * @param int $flags
     * @param integer $amount
     * @return bool
     */
    public function verify(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface $tx, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $scriptPubKey, int $flags, int $nInputToSign, int $amount) : bool;
}
