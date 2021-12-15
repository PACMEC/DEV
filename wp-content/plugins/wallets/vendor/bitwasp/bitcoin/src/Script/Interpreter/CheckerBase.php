<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Locktime;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature\TransactionSignature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInput;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
abstract class CheckerBase
{
    /**
     * @var EcAdapterInterface
     */
    protected $adapter;
    /**
     * @var TransactionInterface
     */
    protected $transaction;
    /**
     * @var int
     */
    protected $nInput;
    /**
     * @var int
     */
    protected $amount;
    /**
     * @var array
     */
    protected $sigCache = [];
    /**
     * @var TransactionSignatureSerializer
     */
    private $sigSerializer;
    /**
     * @var PublicKeySerializerInterface
     */
    private $pubKeySerializer;
    /**
     * @var int
     */
    protected $sigHashOptionalBits = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::ANYONECANPAY;
    /**
     * Checker constructor.
     * @param EcAdapterInterface $ecAdapter
     * @param TransactionInterface $transaction
     * @param int $nInput
     * @param int $amount
     * @param TransactionSignatureSerializer|null $sigSerializer
     * @param PublicKeySerializerInterface|null $pubKeySerializer
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface $transaction, int $nInput, int $amount, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer $sigSerializer = null, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface $pubKeySerializer = null)
    {
        $this->sigSerializer = $sigSerializer ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface::class, \true, $ecAdapter));
        $this->pubKeySerializer = $pubKeySerializer ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface::class, \true, $ecAdapter);
        $this->adapter = $ecAdapter;
        $this->transaction = $transaction;
        $this->nInput = $nInput;
        $this->amount = $amount;
    }
    /**
     * @param ScriptInterface $script
     * @param int $hashType
     * @param int $sigVersion
     * @return BufferInterface
     */
    public abstract function getSigHash(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, int $hashType, int $sigVersion) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * @param BufferInterface $signature
     * @return bool
     */
    public function isValidSignatureEncoding(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $signature) : bool
    {
        try {
            \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature\TransactionSignature::isDERSignature($signature);
            return \true;
        } catch (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical $e) {
            /* In any case, we will return false outside this block */
        }
        return \false;
    }
    /**
     * @param BufferInterface $signature
     * @return bool
     * @throws ScriptRuntimeException
     * @throws \Exception
     */
    public function isLowDerSignature(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $signature) : bool
    {
        if (!$this->isValidSignatureEncoding($signature)) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter::VERIFY_DERSIG, 'Signature with incorrect encoding');
        }
        $binary = $signature->getBinary();
        $nLenR = \ord($binary[3]);
        $nLenS = \ord($binary[5 + $nLenR]);
        $s = $signature->slice(6 + $nLenR, $nLenS)->getGmp();
        return $this->adapter->validateSignatureElement($s, \true);
    }
    /**
     * @param int $hashType
     * @return bool
     */
    public function isDefinedHashtype(int $hashType) : bool
    {
        $nHashType = $hashType & ~$this->sigHashOptionalBits;
        return !($nHashType < \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::ALL || $nHashType > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::SINGLE);
    }
    /**
     * Determine whether the sighash byte appended to the signature encodes
     * a valid sighash type.
     *
     * @param BufferInterface $signature
     * @return bool
     */
    public function isDefinedHashtypeSignature(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $signature) : bool
    {
        if ($signature->getSize() === 0) {
            return \false;
        }
        $binary = $signature->getBinary();
        return $this->isDefinedHashtype(\ord(\substr($binary, -1)));
    }
    /**
     * @param BufferInterface $signature
     * @param int $flags
     * @return $this
     * @throws \BitWasp\Bitcoin\Exceptions\ScriptRuntimeException
     */
    public function checkSignatureEncoding(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $signature, int $flags)
    {
        if ($signature->getSize() === 0) {
            return $this;
        }
        if (($flags & (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter::VERIFY_DERSIG | \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter::VERIFY_LOW_S | \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter::VERIFY_STRICTENC)) !== 0 && !$this->isValidSignatureEncoding($signature)) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter::VERIFY_DERSIG, 'Signature with incorrect encoding');
        } else {
            if (($flags & \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter::VERIFY_LOW_S) !== 0 && !$this->isLowDerSignature($signature)) {
                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter::VERIFY_LOW_S, 'Signature s element was not low');
            } else {
                if (($flags & \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter::VERIFY_STRICTENC) !== 0 && !$this->isDefinedHashtypeSignature($signature)) {
                    throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter::VERIFY_STRICTENC, 'Signature with invalid hashtype');
                }
            }
        }
        return $this;
    }
    /**
     * @param BufferInterface $publicKey
     * @param int $flags
     * @return $this
     * @throws \Exception
     */
    public function checkPublicKeyEncoding(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $publicKey, int $flags)
    {
        if (($flags & \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter::VERIFY_STRICTENC) !== 0 && !\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey::isCompressedOrUncompressed($publicKey)) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Interpreter::VERIFY_STRICTENC, 'Public key with incorrect encoding');
        }
        return $this;
    }
    /**
     * @param ScriptInterface $script
     * @param BufferInterface $sigBuf
     * @param BufferInterface $keyBuf
     * @param int $sigVersion
     * @param int $flags
     * @return bool
     * @throws ScriptRuntimeException
     */
    public function checkSig(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $sigBuf, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $keyBuf, int $sigVersion, int $flags)
    {
        $this->checkSignatureEncoding($sigBuf, $flags)->checkPublicKeyEncoding($keyBuf, $flags);
        try {
            $cacheCheck = "{$script->getBinary()}{$sigVersion}{$keyBuf->getBinary()}{$sigBuf->getBinary()}";
            if (!isset($this->sigCache[$cacheCheck])) {
                $txSignature = $this->sigSerializer->parse($sigBuf);
                $publicKey = $this->pubKeySerializer->parse($keyBuf);
                $hash = $this->getSigHash($script, $txSignature->getHashType(), $sigVersion);
                $result = $this->sigCache[$cacheCheck] = $publicKey->verify($hash, $txSignature->getSignature());
            } else {
                $result = $this->sigCache[$cacheCheck];
            }
            return $result;
        } catch (\Exception $e) {
            return \false;
        }
    }
    /**
     * @param \BitWasp\Bitcoin\Script\Interpreter\Number $scriptLockTime
     * @return bool
     */
    public function checkLockTime(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number $scriptLockTime) : bool
    {
        $input = $this->transaction->getInput($this->nInput);
        $nLockTime = $scriptLockTime->getInt();
        $txLockTime = $this->transaction->getLockTime();
        if (!($txLockTime < \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Locktime::BLOCK_MAX && $nLockTime < \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Locktime::BLOCK_MAX || $txLockTime >= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Locktime::BLOCK_MAX && $nLockTime >= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Locktime::BLOCK_MAX)) {
            return \false;
        }
        if ($nLockTime > $txLockTime) {
            return \false;
        }
        if ($input->isFinal()) {
            return \false;
        }
        return \true;
    }
    /**
     * @param \BitWasp\Bitcoin\Script\Interpreter\Number $sequence
     * @return bool
     */
    public function checkSequence(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number $sequence) : bool
    {
        $txSequence = $this->transaction->getInput($this->nInput)->getSequence();
        if ($this->transaction->getVersion() < 2) {
            return \false;
        }
        if (($txSequence & \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface::SEQUENCE_LOCKTIME_DISABLE_FLAG) !== 0) {
            return \false;
        }
        $mask = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface::SEQUENCE_LOCKTIME_TYPE_FLAG | \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface::SEQUENCE_LOCKTIME_MASK;
        $txToSequenceMasked = $txSequence & $mask;
        $nSequenceMasked = $sequence->getInt() & $mask;
        if (!($txToSequenceMasked < \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInput::SEQUENCE_LOCKTIME_TYPE_FLAG && $nSequenceMasked < \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInput::SEQUENCE_LOCKTIME_TYPE_FLAG || $txToSequenceMasked >= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInput::SEQUENCE_LOCKTIME_TYPE_FLAG && $nSequenceMasked >= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInput::SEQUENCE_LOCKTIME_TYPE_FLAG)) {
            return \false;
        }
        if ($nSequenceMasked > $txToSequenceMasked) {
            return \false;
        }
        return \true;
    }
}
