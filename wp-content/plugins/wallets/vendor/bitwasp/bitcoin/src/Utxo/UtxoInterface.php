<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Utxo;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
interface UtxoInterface
{
    /**
     * @return OutPointInterface
     */
    public function getOutPoint() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface;
    /**
     * @return TransactionOutputInterface
     */
    public function getOutput() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
}
