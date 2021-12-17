<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\OutPointSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\OutPointSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionOutputSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools;
class V1Hasher extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash
{
    /**
     * @var TransactionInterface
     */
    protected $transaction;
    /**
     * @var int
     */
    protected $amount;
    /**
     * @var TransactionOutputSerializer
     */
    protected $outputSerializer;
    /**
     * @var OutPointSerializerInterface
     */
    protected $outpointSerializer;
    /**
     * V1Hasher constructor.
     * @param TransactionInterface $transaction
     * @param int $amount
     * @param OutPointSerializerInterface $outpointSerializer
     * @param TransactionOutputSerializer|null $outputSerializer
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface $transaction, int $amount, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\OutPointSerializerInterface $outpointSerializer = null, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionOutputSerializer $outputSerializer = null)
    {
        $this->amount = $amount;
        $this->outputSerializer = $outputSerializer ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionOutputSerializer();
        $this->outpointSerializer = $outpointSerializer ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\OutPointSerializer();
        parent::__construct($transaction);
    }
    /**
     * @param int $sighashType
     * @return BufferInterface
     */
    public function hashPrevOuts(int $sighashType) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if (!($sighashType & \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::ANYONECANPAY)) {
            $binary = '';
            foreach ($this->tx->getInputs() as $input) {
                $binary .= $this->outpointSerializer->serialize($input->getOutPoint())->getBinary();
            }
            return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($binary));
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer('', 32);
    }
    /**
     * @param int $sighashType
     * @return BufferInterface
     */
    public function hashSequences(int $sighashType) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if (!($sighashType & \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::ANYONECANPAY) && ($sighashType & 0x1f) !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::SINGLE && ($sighashType & 0x1f) !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::NONE) {
            $binary = '';
            foreach ($this->tx->getInputs() as $input) {
                $binary .= \pack('V', $input->getSequence());
            }
            return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($binary));
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer('', 32);
    }
    /**
     * @param int $sighashType
     * @param int $inputToSign
     * @return BufferInterface
     */
    public function hashOutputs(int $sighashType, int $inputToSign) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if (($sighashType & 0x1f) !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::SINGLE && ($sighashType & 0x1f) !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::NONE) {
            $binary = '';
            foreach ($this->tx->getOutputs() as $output) {
                $binary .= $this->outputSerializer->serialize($output)->getBinary();
            }
            return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($binary));
        } elseif (($sighashType & 0x1f) === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::SINGLE && $inputToSign < \count($this->tx->getOutputs())) {
            return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d($this->outputSerializer->serialize($this->tx->getOutput($inputToSign)));
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer('', 32);
    }
    /**
     * Calculate the hash of the current transaction, when you are looking to
     * spend $txOut, and are signing $inputToSign. The SigHashType defaults to
     * SIGHASH_ALL
     *
     * @param ScriptInterface $txOutScript
     * @param int $inputToSign
     * @param int $sighashType
     * @return BufferInterface
     * @throws \Exception
     */
    public function calculate(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $txOutScript, int $inputToSign, int $sighashType = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::ALL) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $hashPrevOuts = $this->hashPrevOuts($sighashType);
        $hashSequence = $this->hashSequences($sighashType);
        $hashOutputs = $this->hashOutputs($sighashType, $inputToSign);
        $input = $this->tx->getInput($inputToSign);
        $scriptBuf = $txOutScript->getBuffer();
        $preimage = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\pack("V", $this->tx->getVersion()) . $hashPrevOuts->getBinary() . $hashSequence->getBinary() . $this->outpointSerializer->serialize($input->getOutPoint())->getBinary() . \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::numToVarInt($scriptBuf->getSize())->getBinary() . $scriptBuf->getBinary() . \pack("P", $this->amount) . \pack("V", $input->getSequence()) . $hashOutputs->getBinary() . \pack("V", $this->tx->getLockTime()) . \pack("V", $sighashType));
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d($preimage);
    }
}
