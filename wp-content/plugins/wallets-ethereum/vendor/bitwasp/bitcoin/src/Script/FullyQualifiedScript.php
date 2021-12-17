<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\MissingScriptException;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptHashMismatch;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptQualificationError;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SuperfluousScriptData;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputClassifier;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SigValues;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class FullyQualifiedScript
{
    /**
     * @var OutputData
     */
    private $spkData;
    /**
     * @var OutputData|null
     */
    private $rsData;
    /**
     * @var OutputData|null
     */
    private $wsData;
    /**
     * @var OutputData
     */
    private $signData;
    /**
     * @var int
     */
    private $sigVersion;
    /**
     * This is responsible for checking that the script-hash
     * commitments between scripts were satisfied, and determines
     * the sigVersion.
     *
     * It rejects superfluous redeem & witness scripts, and refuses
     * to construct unless all necessary scripts are provided.
     *
     * @param OutputData $spkData
     * @param OutputData|null $rsData
     * @param OutputData|null $wsData
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData $spkData, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData $rsData = null, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData $wsData = null)
    {
        $signScript = $spkData;
        $sigVersion = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::V0;
        if ($spkData->getType() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH) {
            if (!$rsData instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData) {
                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\MissingScriptException("Missing redeemScript");
            }
            if (!$rsData->getScript()->getScriptHash()->equals($spkData->getSolution())) {
                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptHashMismatch("Redeem script fails to solve pay-to-script-hash");
            }
            $signScript = $rsData;
        } else {
            if ($rsData) {
                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SuperfluousScriptData("Data provided for redeemScript was not necessary");
            }
        }
        if ($signScript->getType() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WKH) {
            $classifier = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputClassifier();
            $signScript = $classifier->decode(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::scriptPubKey()->p2pkh($signScript->getSolution()));
            $sigVersion = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::V1;
        } else {
            if ($signScript->getType() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WSH) {
                if (!$wsData instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData) {
                    throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\MissingScriptException("Missing witnessScript");
                }
                if (!$wsData->getScript()->getWitnessScriptHash()->equals($signScript->getSolution())) {
                    $origin = $rsData ? "redeemScript" : "scriptPubKey";
                    throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptHashMismatch("Witness script does not match witness program in {$origin}");
                }
                $signScript = $wsData;
                $sigVersion = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::V1;
            } else {
                if ($wsData) {
                    throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SuperfluousScriptData("Data provided for witnessScript was not necessary");
                }
            }
        }
        $this->spkData = $spkData;
        $this->rsData = $rsData;
        $this->wsData = $wsData;
        $this->signData = $signScript;
        $this->sigVersion = $sigVersion;
    }
    /**
     * Checks $chunks (a decompiled scriptSig) for it's last element,
     * or defers to SignData. If both are provided, it checks the
     * value obtained from $chunks against SignData.
     *
     * @param BufferInterface[] $chunks
     * @param SignData $signData
     * @return P2shScript
     */
    public static function findRedeemScript(array $chunks, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData $signData)
    {
        if (\count($chunks) > 0) {
            $redeemScript = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script($chunks[\count($chunks) - 1]);
            if ($signData->hasRedeemScript()) {
                if (!$redeemScript->equals($signData->getRedeemScript())) {
                    throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptQualificationError('Extracted redeemScript did not match sign data');
                }
            }
        } else {
            if (!$signData->hasRedeemScript()) {
                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptQualificationError('Redeem script not provided in sign data or scriptSig');
            }
            $redeemScript = $signData->getRedeemScript();
        }
        if (!$redeemScript instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\P2shScript) {
            $redeemScript = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\P2shScript($redeemScript);
        }
        return $redeemScript;
    }
    /**
     * Checks the witness for it's last element, or whatever
     * the SignData happens to have. If SignData has a WS,
     * it will ensure that if chunks has a script, it matches WS.
     * @param ScriptWitnessInterface $witness
     * @param SignData $signData
     * @return Script|ScriptInterface|WitnessScript
     */
    public static function findWitnessScript(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface $witness, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData $signData)
    {
        if (\count($witness) > 0) {
            $witnessScript = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script($witness->bottom());
            if ($signData->hasWitnessScript()) {
                if (!$witnessScript->equals($signData->getWitnessScript())) {
                    throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptQualificationError('Extracted witnessScript did not match sign data');
                }
            }
        } else {
            if (!$signData->hasWitnessScript()) {
                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptQualificationError('Witness script not provided in sign data or witness');
            }
            $witnessScript = $signData->getWitnessScript();
        }
        if (!$witnessScript instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessScript) {
            $witnessScript = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessScript($witnessScript);
        }
        return $witnessScript;
    }
    /**
     * This function attempts to produce a FQS from
     * raw scripts and witnesses. High level checking
     * of script types is done to determine what we need
     * from all this, before initializing the constructor
     * for final validation.
     *
     * @param ScriptInterface $scriptPubKey
     * @param ScriptInterface $scriptSig
     * @param ScriptWitnessInterface $witness
     * @param SignData|null $signData
     * @param OutputClassifier|null $classifier
     * @return FullyQualifiedScript
     */
    public static function fromTxData(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $scriptPubKey, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $scriptSig, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface $witness, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData $signData = null, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputClassifier $classifier = null)
    {
        $classifier = $classifier ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputClassifier();
        $signData = $signData ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData();
        $wsData = null;
        $rsData = null;
        $solution = $spkData = $classifier->decode($scriptPubKey);
        $sigChunks = [];
        if (!$scriptSig->isPushOnly($sigChunks)) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptQualificationError("Invalid script signature - must be PUSHONLY.");
        }
        if ($solution->getType() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH) {
            $redeemScript = self::findRedeemScript($sigChunks, $signData);
            $solution = $rsData = $classifier->decode($redeemScript);
        }
        if ($solution->getType() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WSH) {
            $witnessScript = self::findWitnessScript($witness, $signData);
            $wsData = $classifier->decode($witnessScript);
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\FullyQualifiedScript($spkData, $rsData, $wsData);
    }
    /**
     * Was the FQS's scriptPubKey P2SH?
     * @return bool
     */
    public function isP2SH() : bool
    {
        return $this->rsData instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData;
    }
    /**
     * Was the FQS's scriptPubKey, or redeemScript, P2WSH?
     * @return bool
     */
    public function isP2WSH() : bool
    {
        return $this->wsData instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData;
    }
    /**
     * Returns the scriptPubKey.
     * @return OutputData
     */
    public function scriptPubKey() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData
    {
        return $this->spkData;
    }
    /**
     * Returns the sign script we qualified from
     * the spk/rs/ws. Essentially this is the script
     * that actually locks the coins (the CScript
     * passed into EvalScript in interpreter.cpp)
     *
     * @return OutputData
     */
    public function signScript() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData
    {
        return $this->signData;
    }
    /**
     * Returns the signature hashing algorithm version.
     * Defaults to V0, unless script was segwit.
     * @return int
     */
    public function sigVersion() : int
    {
        return $this->sigVersion;
    }
    /**
     * Returns the redeemScript, if we had one.
     * Throws an exception otherwise.
     * @return OutputData
     * @throws \RuntimeException
     */
    public function redeemScript() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData
    {
        if (null === $this->rsData) {
            throw new \RuntimeException("No redeemScript for this script!");
        }
        return $this->rsData;
    }
    /**
     * Returns the witnessScript, if we had one.
     * Throws an exception otherwise.
     * @return OutputData
     * @throws \RuntimeException
     */
    public function witnessScript() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData
    {
        if (null === $this->wsData) {
            throw new \RuntimeException("No witnessScript for this script!");
        }
        return $this->wsData;
    }
    /**
     * Encodes the stack (the stack passed as an
     * argument to EvalScript in interpreter.cpp)
     * into a scriptSig and witness structure. These
     * are suitable for directly encoding in a transaction.
     * @param Stack $stack
     * @return SigValues
     */
    public function encodeStack(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack $stack) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SigValues
    {
        $scriptSigChunks = $stack->all();
        $witness = [];
        $solution = $this->spkData;
        $p2sh = \false;
        if ($solution->getType() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH) {
            $p2sh = \true;
            $solution = $this->rsData;
        }
        if ($solution->getType() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WKH) {
            $witness = $stack->all();
            $scriptSigChunks = [];
        } else {
            if ($solution->getType() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WSH) {
                $witness = $stack->all();
                $witness[] = $this->wsData->getScript()->getBuffer();
                $scriptSigChunks = [];
            }
        }
        if ($p2sh) {
            $scriptSigChunks[] = $this->rsData->getScript()->getBuffer();
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SigValues(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::pushAll($scriptSigChunks), new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitness(...$witness));
    }
    /**
     * @param ScriptInterface $scriptSig
     * @param ScriptWitnessInterface $witness
     * @return Stack
     */
    public function extractStack(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $scriptSig, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface $witness) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack
    {
        $sigChunks = [];
        if (!$scriptSig->isPushOnly($sigChunks)) {
            throw new \RuntimeException("Invalid signature script - must be push only");
        }
        $solution = $this->spkData;
        if ($solution->getType() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH) {
            $solution = $this->rsData;
            $nChunks = \count($sigChunks);
            if ($nChunks > 0 && $sigChunks[$nChunks - 1]->equals($this->rsData->getScript()->getBuffer())) {
                $sigChunks = \array_slice($sigChunks, 0, -1);
            }
        }
        if ($solution->getType() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WKH) {
            $sigChunks = $witness->all();
        } else {
            if ($solution->getType() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WSH) {
                $sigChunks = $witness->all();
                $nChunks = \count($sigChunks);
                if ($nChunks > 0 && $sigChunks[$nChunks - 1]->equals($this->wsData->getScript()->getBuffer())) {
                    $sigChunks = \array_slice($sigChunks, 0, -1);
                }
            }
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack($sigChunks);
    }
}
