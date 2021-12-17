<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignerException;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\Checker\CheckerCreator;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\Checker\CheckerCreatorBase;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
class Signer
{
    /**
     * @var EcAdapterInterface
     */
    private $ecAdapter;
    /**
     * @var TransactionInterface
     */
    private $tx;
    /**
     * @var TransactionSignatureSerializer
     */
    private $sigSerializer;
    /**
     * @var PublicKeySerializerInterface
     */
    private $pubKeySerializer;
    /**
     * @var bool
     */
    private $tolerateInvalidPublicKey = \false;
    /**
     * @var bool
     */
    private $padUnsignedMultisigs = \false;
    /**
     * @var bool
     */
    private $allowComplexScripts = \false;
    /**
     * @var CheckerCreatorBase
     */
    private $checkerCreator;
    /**
     * @var InputSignerInterface[]
     */
    private $signatureCreator = [];
    /**
     * TxWitnessSigner constructor.
     * @param TransactionInterface $tx
     * @param EcAdapterInterface $ecAdapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface $tx, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null)
    {
        $this->tx = $tx;
        $this->ecAdapter = $ecAdapter ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter();
        $this->sigSerializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface::class, \true, $this->ecAdapter));
        $this->pubKeySerializer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface::class, \true, $this->ecAdapter);
        $this->checkerCreator = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\Checker\CheckerCreator($this->ecAdapter, $this->sigSerializer, $this->pubKeySerializer);
    }
    /**
     * @param CheckerCreatorBase $checker
     * @return $this
     * @throws SignerException
     */
    public function setCheckerCreator(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\Checker\CheckerCreatorBase $checker)
    {
        if (\count($this->signatureCreator) === 0) {
            $this->checkerCreator = $checker;
            return $this;
        } else {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignerException("Cannot change CheckerCreator after inputs have been parsed");
        }
    }
    /**
     * @param bool $setting
     * @return $this
     */
    public function padUnsignedMultisigs(bool $setting)
    {
        $this->padUnsignedMultisigs = $setting;
        return $this;
    }
    /**
     * @param bool $setting
     * @return $this
     */
    public function tolerateInvalidPublicKey(bool $setting)
    {
        $this->tolerateInvalidPublicKey = $setting;
        return $this;
    }
    /**
     * @param bool $setting
     * @return $this
     */
    public function allowComplexScripts(bool $setting)
    {
        $this->allowComplexScripts = $setting;
        return $this;
    }
    /**
     * @param int $nIn
     * @param PrivateKeyInterface $key
     * @param TransactionOutputInterface $txOut
     * @param SignData $signData
     * @param int $sigHashType
     * @return $this
     */
    public function sign(int $nIn, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface $key, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface $txOut, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData $signData = null, int $sigHashType = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::ALL)
    {
        $input = $this->input($nIn, $txOut, $signData);
        foreach ($input->getSteps() as $idx => $step) {
            $input->sign($key, $sigHashType);
        }
        return $this;
    }
    /**
     * @param int $nIn
     * @param TransactionOutputInterface $txOut
     * @param SignData|null $signData
     * @return InputSignerInterface
     */
    public function input(int $nIn, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface $txOut, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData $signData = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\InputSignerInterface
    {
        if (null === $signData) {
            $signData = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData();
        }
        if (!isset($this->signatureCreator[$nIn])) {
            $checker = $this->checkerCreator->create($this->tx, $nIn, $txOut);
            $input = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\InputSigner($this->ecAdapter, $this->tx, $nIn, $txOut, $signData, $checker, $this->sigSerializer, $this->pubKeySerializer);
            $input->padUnsignedMultisigs($this->padUnsignedMultisigs);
            $input->tolerateInvalidPublicKey($this->tolerateInvalidPublicKey);
            $input->allowComplexScripts($this->allowComplexScripts);
            $input->extract();
            $this->signatureCreator[$nIn] = $input;
        }
        return $this->signatureCreator[$nIn];
    }
    /**
     * @return TransactionInterface
     */
    public function get() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface
    {
        $mutable = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionFactory::mutate($this->tx);
        $witnesses = [];
        foreach ($mutable->inputsMutator() as $idx => $input) {
            if (isset($this->signatureCreator[$idx])) {
                $sig = $this->signatureCreator[$idx]->serializeSignatures();
                $input->script($sig->getScriptSig());
                $witnesses[$idx] = $sig->getScriptWitness();
            }
        }
        if (\count($witnesses) > 0) {
            $mutable->witness($witnesses);
        }
        return $mutable->done();
    }
}
