<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Utxo;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPoint;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
class Utxo implements UtxoInterface
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
    public function __construct(OutPointInterface $outPoint, TransactionOutputInterface $prevOut)
    {
        $this->outPoint = $outPoint;
        $this->prevOut = $prevOut;
    }
    /**
     * @return OutPointInterface
     */
    public function getOutPoint() : OutPointInterface
    {
        return $this->outPoint;
    }
    /**
     * @return TransactionOutputInterface
     */
    public function getOutput() : TransactionOutputInterface
    {
        return $this->prevOut;
    }
}
