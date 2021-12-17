<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class Hasher extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash
{
    /**
     * Calculate the hash of the current transaction, when you are looking to
     * spend $txOut, and are signing $inputToSign. The SigHashType defaults to
     * SIGHASH_ALL, though SIGHASH_SINGLE, SIGHASH_NONE, SIGHASH_ANYONECANPAY
     * can be used.
     *
     * @param ScriptInterface $txOutScript
     * @param int $inputToSign
     * @param int $sighashType
     * @return BufferInterface
     * @throws \Exception
     */
    public function calculate(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $txOutScript, int $inputToSign, int $sighashType = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::ALL) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if ($inputToSign >= \count($this->tx->getInputs())) {
            return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex('0100000000000000000000000000000000000000000000000000000000000000', 32);
        }
        if (($sighashType & 0x1f) == \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::SINGLE) {
            if ($inputToSign >= \count($this->tx->getOutputs())) {
                return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex('0100000000000000000000000000000000000000000000000000000000000000', 32);
            }
        }
        $serializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\TxSigHashSerializer($this->tx, $txOutScript, $inputToSign, $sighashType);
        $sigHashData = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($serializer->serializeTransaction() . \pack('V', $sighashType));
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d($sigHashData);
    }
}
