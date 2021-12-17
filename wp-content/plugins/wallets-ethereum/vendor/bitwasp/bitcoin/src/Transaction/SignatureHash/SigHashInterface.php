<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface SigHashInterface
{
    /**
     * Default procedure: Sign ALL of the outputs.
     */
    const ALL = 1;
    /**
     * Sign NONE of the outputs, I don't care where the bitcoins go.
     */
    const NONE = 2;
    /**
     * Sign ONE of the outputs, I don't care where the others go.
     */
    const SINGLE = 3;
    /**
     * Let other people add inputs to this transaction paying X. I don't
     * care who else pays. (can be used with other sighash flags)
     */
    const ANYONECANPAY = 128;
    /**
     * Calculate the hash of the current transaction, when you are looking to
     * spend $txOut, and are signing $inputToSign. The SigHashType defaults to
     * SIGHASH_ALL, though SIGHASH_SINGLE, SIGHASH_NONE, SIGHASH_ANYONECANPAY
     * can be used.
     *
     * @param ScriptInterface $txOutScript
     * @param int $inputToSign
     * @param int $sighashType
     * @return \BitWasp\Buffertools\BufferInterface
     */
    public function calculate(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $txOutScript, int $inputToSign, int $sighashType = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::ALL) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
}
