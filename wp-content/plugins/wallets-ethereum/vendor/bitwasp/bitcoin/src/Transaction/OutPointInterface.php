<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface OutPointInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface
{
    /**
     * @return BufferInterface
     */
    public function getTxId() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * @return int
     */
    public function getVout() : int;
    /**
     * @param OutPointInterface $outPoint
     * @return bool
     */
    public function equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface $outPoint) : bool;
}
