<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Utxo;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPoint;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
class Utxo implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Utxo\UtxoInterface
{
    /**
     * @var OutPointInterface
     */
    private $outPoint;
    /**
     * @var TransactionOutputInterface
     */
    private $prevOut;
    /**
     * @param OutPointInterface $outPoint
     * @param TransactionOutputInterface $prevOut
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface $outPoint, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface $prevOut)
    {
        $this->outPoint = $outPoint;
        $this->prevOut = $prevOut;
    }
    /**
     * @return OutPointInterface
     */
    public function getOutPoint() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface
    {
        return $this->outPoint;
    }
    /**
     * @return TransactionOutputInterface
     */
    public function getOutput() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface
    {
        return $this->prevOut;
    }
}
