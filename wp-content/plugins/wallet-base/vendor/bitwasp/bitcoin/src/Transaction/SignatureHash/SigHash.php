<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
abstract class SigHash implements SigHashInterface
{
    const V0 = 0;
    const V1 = 1;
    /**
     * @var TransactionInterface
     */
    protected $tx;
    /**
     * SigHash constructor.
     * @param TransactionInterface $transaction
     */
    public function __construct(TransactionInterface $transaction)
    {
        $this->tx = $transaction;
    }
    /**
     * @param ScriptInterface $txOutScript
     * @param int $inputToSign
     * @param int $sighashType
     * @return BufferInterface
     */
    public abstract function calculate(ScriptInterface $txOutScript, int $inputToSign, int $sighashType = self::ALL) : BufferInterface;
}
