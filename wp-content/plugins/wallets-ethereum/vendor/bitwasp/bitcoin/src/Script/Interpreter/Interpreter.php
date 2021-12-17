<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputClassifier;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitness;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessProgram;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature\TransactionSignature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class Interpreter implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\InterpreterInterface
{
    /**
     * @var \BitWasp\Bitcoin\Math\Math
     */
    private $math;
    /**
     * @var BufferInterface
     */
    private $vchFalse;
    /**
     * @var BufferInterface
     */
    private $vchTrue;
    /**
     * @var array
     */
    private $disabledOps = [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CAT, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_SUBSTR, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_LEFT, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_RIGHT, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_INVERT, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_AND, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_OR, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_XOR, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_2MUL, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_2DIV, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_MUL, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_DIV, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_MOD, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_LSHIFT, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_RSHIFT];
    /**
     * @param EcAdapterInterface $ecAdapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null)
    {
        $ecAdapter = $ecAdapter ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter();
        $this->math = $ecAdapter->getMath();
        $this->vchFalse = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer("", 0);
        $this->vchTrue = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer("\1", 1);
    }
    /**
     * Cast the value to a boolean
     *
     * @param BufferInterface $value
     * @return bool
     */
    public function castToBool(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $value) : bool
    {
        $val = $value->getBinary();
        for ($i = 0, $size = \strlen($val); $i < $size; $i++) {
            $chr = \ord($val[$i]);
            if ($chr !== 0) {
                if ($i === $size - 1 && $chr === 0x80) {
                    return \false;
                }
                return \true;
            }
        }
        return \false;
    }
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
     * @param int $opCode
     * @param BufferInterface $pushData
     * @return bool
     * @throws \Exception
     */
    public function checkMinimalPush($opCode, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $pushData) : bool
    {
        $pushSize = $pushData->getSize();
        $binary = $pushData->getBinary();
        if ($pushSize === 0) {
            return $opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_0;
        } elseif ($pushSize === 1) {
            $first = \ord($binary[0]);
            if ($first >= 1 && $first <= 16) {
                return $opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1 + ($first - 1);
            } elseif ($first === 0x81) {
                return $opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1NEGATE;
            }
        } elseif ($pushSize <= 75) {
            return $opCode === $pushSize;
        } elseif ($pushSize <= 255) {
            return $opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_PUSHDATA1;
        } elseif ($pushSize <= 65535) {
            return $opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_PUSHDATA2;
        }
        return \true;
    }
    /**
     * @param int $count
     * @return $this
     */
    private function checkOpcodeCount(int $count)
    {
        if ($count > 201) {
            throw new \RuntimeException('Error: Script op code count');
        }
        return $this;
    }
    /**
     * @param WitnessProgram $witnessProgram
     * @param ScriptWitnessInterface $scriptWitness
     * @param int $flags
     * @param CheckerBase $checker
     * @return bool
     */
    private function verifyWitnessProgram(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessProgram $witnessProgram, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface $scriptWitness, int $flags, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\CheckerBase $checker) : bool
    {
        $witnessCount = \count($scriptWitness);
        if ($witnessProgram->getVersion() === 0) {
            $buffer = $witnessProgram->getProgram();
            if ($buffer->getSize() === 32) {
                // Version 0 segregated witness program: SHA256(Script) in program, Script + inputs in witness
                if ($witnessCount === 0) {
                    // Must contain script at least
                    return \false;
                }
                $scriptPubKey = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script($scriptWitness[$witnessCount - 1]);
                $stackValues = $scriptWitness->slice(0, -1);
                if (!$buffer->equals($scriptPubKey->getWitnessScriptHash())) {
                    return \false;
                }
            } elseif ($buffer->getSize() === 20) {
                // Version 0 special case for pay-to-pubkeyhash
                if ($witnessCount !== 2) {
                    // 2 items in witness - <signature> <pubkey>
                    return \false;
                }
                $scriptPubKey = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::scriptPubKey()->payToPubKeyHash($buffer);
                $stackValues = $scriptWitness;
            } else {
                return \false;
            }
        } elseif ($flags & self::VERIFY_DISCOURAGE_UPGRADABLE_WITNESS_PROGRAM) {
            return \false;
        } else {
            // Unknown versions are always 'valid' to permit future soft forks
            return \true;
        }
        $mainStack = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack();
        foreach ($stackValues as $value) {
            $mainStack->push($value);
        }
        if (!$this->evaluate($scriptPubKey, $mainStack, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::V1, $flags, $checker)) {
            return \false;
        }
        if ($mainStack->count() !== 1) {
            return \false;
        }
        if (!$this->castToBool($mainStack->bottom())) {
            return \false;
        }
        return \true;
    }
    /**
     * @param ScriptInterface $scriptSig
     * @param ScriptInterface $scriptPubKey
     * @param int $flags
     * @param CheckerBase $checker
     * @param ScriptWitnessInterface|null $witness
     * @return bool
     */
    public function verify(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $scriptSig, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $scriptPubKey, int $flags, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\CheckerBase $checker, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface $witness = null) : bool
    {
        static $emptyWitness = null;
        if ($emptyWitness === null) {
            $emptyWitness = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitness();
        }
        $witness = \is_null($witness) ? $emptyWitness : $witness;
        if (($flags & self::VERIFY_SIGPUSHONLY) !== 0 && !$scriptSig->isPushOnly()) {
            return \false;
        }
        $stack = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack();
        if (!$this->evaluate($scriptSig, $stack, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::V0, $flags, $checker)) {
            return \false;
        }
        $backup = [];
        if ($flags & self::VERIFY_P2SH) {
            foreach ($stack as $s) {
                $backup[] = $s;
            }
        }
        if (!$this->evaluate($scriptPubKey, $stack, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::V0, $flags, $checker)) {
            return \false;
        }
        if ($stack->isEmpty()) {
            return \false;
        }
        if (\false === $this->castToBool($stack[-1])) {
            return \false;
        }
        $program = null;
        if ($flags & self::VERIFY_WITNESS) {
            if ($scriptPubKey->isWitness($program)) {
                /** @var WitnessProgram $program */
                if ($scriptSig->getBuffer()->getSize() !== 0) {
                    return \false;
                }
                if (!$this->verifyWitnessProgram($program, $witness, $flags, $checker)) {
                    return \false;
                }
                $stack->resize(1);
            }
        }
        if ($flags & self::VERIFY_P2SH && (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputClassifier())->isPayToScriptHash($scriptPubKey)) {
            if (!$scriptSig->isPushOnly()) {
                return \false;
            }
            $stack = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack();
            foreach ($backup as $i) {
                $stack->push($i);
            }
            // Restore mainStack to how it was after evaluating scriptSig
            if ($stack->isEmpty()) {
                return \false;
            }
            // Load redeemscript as the scriptPubKey
            $scriptPubKey = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script($stack->bottom());
            $stack->pop();
            if (!$this->evaluate($scriptPubKey, $stack, 0, $flags, $checker)) {
                return \false;
            }
            if ($stack->isEmpty()) {
                return \false;
            }
            if (!$this->castToBool($stack->bottom())) {
                return \false;
            }
            if ($flags & self::VERIFY_WITNESS) {
                if ($scriptPubKey->isWitness($program)) {
                    /** @var WitnessProgram $program */
                    if (!$scriptSig->equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::sequence([$scriptPubKey->getBuffer()]))) {
                        return \false;
                        // SCRIPT_ERR_WITNESS_MALLEATED_P2SH
                    }
                    if (!$this->verifyWitnessProgram($program, $witness, $flags, $checker)) {
                        return \false;
                    }
                    $stack->resize(1);
                }
            }
        }
        if ($flags & self::VERIFY_CLEAN_STACK) {
            if (!($flags & self::VERIFY_P2SH !== 0) && $flags & self::VERIFY_WITNESS !== 0) {
                return \false;
                // implied flags required
            }
            if (\count($stack) !== 1) {
                return \false;
                // Cleanstack
            }
        }
        if ($flags & self::VERIFY_WITNESS) {
            if (!$flags & self::VERIFY_P2SH) {
                return \false;
                //
            }
            if ($program === null && !$witness->isNull()) {
                return \false;
                // SCRIPT_ERR_WITNESS_UNEXPECTED
            }
        }
        return \true;
    }
    /**
     * @param Stack $vfStack
     * @param bool $value
     * @return bool
     */
    public function checkExec(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack $vfStack, bool $value) : bool
    {
        $ret = 0;
        foreach ($vfStack as $item) {
            if ($item === $value) {
                $ret++;
            }
        }
        return (bool) $ret;
    }
    /**
     * @param ScriptInterface $script
     * @param Stack $mainStack
     * @param int $sigVersion
     * @param int $flags
     * @param CheckerBase $checker
     * @return bool
     */
    public function evaluate(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack $mainStack, int $sigVersion, int $flags, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\CheckerBase $checker) : bool
    {
        $hashStartPos = 0;
        $opCount = 0;
        $zero = \gmp_init(0, 10);
        $altStack = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack();
        $vfStack = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack();
        $minimal = ($flags & self::VERIFY_MINIMALDATA) !== 0;
        $parser = $script->getScriptParser();
        if ($script->getBuffer()->getSize() > 10000) {
            return \false;
        }
        try {
            foreach ($parser as $operation) {
                $opCode = $operation->getOp();
                $pushData = $operation->getData();
                $fExec = !$this->checkExec($vfStack, \false);
                // If pushdata was written to
                if ($operation->isPush() && $operation->getDataSize() > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\InterpreterInterface::MAX_SCRIPT_ELEMENT_SIZE) {
                    throw new \RuntimeException('Error - push size');
                }
                // OP_RESERVED should not count towards opCount
                if ($opCode > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_16 && ++$opCount) {
                    $this->checkOpcodeCount($opCount);
                }
                if (\in_array($opCode, $this->disabledOps, \true)) {
                    throw new \RuntimeException('Disabled Opcode');
                }
                if ($fExec && $operation->isPush()) {
                    // In range of a pushdata opcode
                    if ($minimal && !$this->checkMinimalPush($opCode, $pushData)) {
                        throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_MINIMALDATA, 'Minimal pushdata required');
                    }
                    $mainStack->push($pushData);
                    // echo " - [pushed '" . $pushData->getHex() . "']\n";
                } elseif ($fExec || \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_IF <= $opCode && $opCode <= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ENDIF) {
                    // echo "OPCODE - " . $script->getOpcodes()->getOp($opCode) . "\n";
                    switch ($opCode) {
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1NEGATE:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_2:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_3:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_4:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_5:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_6:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_7:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_8:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_9:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_10:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_11:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_12:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_13:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_14:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_15:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_16:
                            $num = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\decodeOpN($opCode);
                            $mainStack->push(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::int($num)->getBuffer());
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKLOCKTIMEVERIFY:
                            if (!($flags & self::VERIFY_CHECKLOCKTIMEVERIFY)) {
                                if ($flags & self::VERIFY_DISCOURAGE_UPGRADABLE_NOPS) {
                                    throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_DISCOURAGE_UPGRADABLE_NOPS, 'Upgradable NOP found - this is discouraged');
                                }
                                break;
                            }
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation - CLTV');
                            }
                            $lockTime = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack[-1], $minimal, 5, $this->math);
                            if (!$checker->checkLockTime($lockTime)) {
                                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_CHECKLOCKTIMEVERIFY, 'Unsatisfied locktime');
                            }
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSEQUENCEVERIFY:
                            if (!($flags & self::VERIFY_CHECKSEQUENCEVERIFY)) {
                                if ($flags & self::VERIFY_DISCOURAGE_UPGRADABLE_NOPS) {
                                    throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_DISCOURAGE_UPGRADABLE_NOPS, 'Upgradable NOP found - this is discouraged');
                                }
                                break;
                            }
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation - CSV');
                            }
                            $sequence = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack[-1], $minimal, 5, $this->math);
                            $nSequence = $sequence->getGmp();
                            if ($this->math->cmp($nSequence, $zero) < 0) {
                                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_CHECKSEQUENCEVERIFY, 'Negative locktime');
                            }
                            if ($this->math->cmp($this->math->bitwiseAnd($nSequence, \gmp_init(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface::SEQUENCE_LOCKTIME_DISABLE_FLAG, 10)), $zero) !== 0) {
                                break;
                            }
                            if (!$checker->checkSequence($sequence)) {
                                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_CHECKSEQUENCEVERIFY, 'Unsatisfied sequence');
                            }
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOP1:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOP4:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOP5:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOP6:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOP7:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOP8:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOP9:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOP10:
                            if ($flags & self::VERIFY_DISCOURAGE_UPGRADABLE_NOPS) {
                                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_DISCOURAGE_UPGRADABLE_NOPS, 'Upgradable NOP found - this is discouraged');
                            }
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOP:
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_IF:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOTIF:
                            // <expression> if [statements] [else [statements]] endif
                            $value = \false;
                            if ($fExec) {
                                if ($mainStack->isEmpty()) {
                                    throw new \RuntimeException('Unbalanced conditional');
                                }
                                $vch = $mainStack[-1];
                                if ($sigVersion === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\SignatureHash\SigHash::V1 && $flags & self::VERIFY_MINIMALIF) {
                                    if ($vch->getSize() > 1) {
                                        throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_MINIMALIF, 'Input to OP_IF/NOTIF should be minimally encoded');
                                    }
                                    if ($vch->getSize() === 1 && $vch->getBinary() !== "\1") {
                                        throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_MINIMALIF, 'Input to OP_IF/NOTIF should be minimally encoded');
                                    }
                                }
                                $buffer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack->pop(), $minimal)->getBuffer();
                                $value = $this->castToBool($buffer);
                                if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOTIF) {
                                    $value = !$value;
                                }
                            }
                            $vfStack->push($value);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ELSE:
                            if ($vfStack->isEmpty()) {
                                throw new \RuntimeException('Unbalanced conditional');
                            }
                            $vfStack->push(!$vfStack->pop());
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ENDIF:
                            if ($vfStack->isEmpty()) {
                                throw new \RuntimeException('Unbalanced conditional');
                            }
                            $vfStack->pop();
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_VERIFY:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation');
                            }
                            $value = $this->castToBool($mainStack[-1]);
                            if (!$value) {
                                throw new \RuntimeException('Error: verify');
                            }
                            $mainStack->pop();
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_TOALTSTACK:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation OP_TOALTSTACK');
                            }
                            $altStack->push($mainStack->pop());
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_FROMALTSTACK:
                            if ($altStack->isEmpty()) {
                                throw new \RuntimeException('Invalid alt-stack operation OP_FROMALTSTACK');
                            }
                            $mainStack->push($altStack->pop());
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_IFDUP:
                            // If top value not zero, duplicate it.
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation OP_IFDUP');
                            }
                            $vch = $mainStack[-1];
                            if ($this->castToBool($vch)) {
                                $mainStack->push($vch);
                            }
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_DEPTH:
                            $num = \count($mainStack);
                            $depth = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::int($num)->getBuffer();
                            $mainStack->push($depth);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_DROP:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation OP_DROP');
                            }
                            $mainStack->pop();
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_DUP:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation OP_DUP');
                            }
                            $vch = $mainStack[-1];
                            $mainStack->push($vch);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NIP:
                            if (\count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_NIP');
                            }
                            unset($mainStack[-2]);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_OVER:
                            if (\count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_OVER');
                            }
                            $vch = $mainStack[-2];
                            $mainStack->push($vch);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ROT:
                            if (\count($mainStack) < 3) {
                                throw new \RuntimeException('Invalid stack operation OP_ROT');
                            }
                            $mainStack->swap(-3, -2);
                            $mainStack->swap(-2, -1);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_SWAP:
                            if (\count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_SWAP');
                            }
                            $mainStack->swap(-2, -1);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_TUCK:
                            if (\count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_TUCK');
                            }
                            $vch = $mainStack[-1];
                            $mainStack->add(-2, $vch);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_PICK:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ROLL:
                            if (\count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_PICK');
                            }
                            $n = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack[-1], $minimal, 4)->getGmp();
                            $mainStack->pop();
                            if ($this->math->cmp($n, $zero) < 0 || $this->math->cmp($n, \gmp_init(\count($mainStack))) >= 0) {
                                throw new \RuntimeException('Invalid stack operation OP_PICK');
                            }
                            $pos = (int) \gmp_strval($this->math->sub($this->math->sub($zero, $n), \gmp_init(1)), 10);
                            $vch = $mainStack[$pos];
                            if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ROLL) {
                                unset($mainStack[$pos]);
                            }
                            $mainStack->push($vch);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_2DROP:
                            if (\count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_2DROP');
                            }
                            $mainStack->pop();
                            $mainStack->pop();
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_2DUP:
                            if (\count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_2DUP');
                            }
                            $string1 = $mainStack[-2];
                            $string2 = $mainStack[-1];
                            $mainStack->push($string1);
                            $mainStack->push($string2);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_3DUP:
                            if (\count($mainStack) < 3) {
                                throw new \RuntimeException('Invalid stack operation OP_3DUP');
                            }
                            $string1 = $mainStack[-3];
                            $string2 = $mainStack[-2];
                            $string3 = $mainStack[-1];
                            $mainStack->push($string1);
                            $mainStack->push($string2);
                            $mainStack->push($string3);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_2OVER:
                            if (\count($mainStack) < 4) {
                                throw new \RuntimeException('Invalid stack operation OP_2OVER');
                            }
                            $string1 = $mainStack[-4];
                            $string2 = $mainStack[-3];
                            $mainStack->push($string1);
                            $mainStack->push($string2);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_2ROT:
                            if (\count($mainStack) < 6) {
                                throw new \RuntimeException('Invalid stack operation OP_2ROT');
                            }
                            $string1 = $mainStack[-6];
                            $string2 = $mainStack[-5];
                            unset($mainStack[-6], $mainStack[-5]);
                            $mainStack->push($string1);
                            $mainStack->push($string2);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_2SWAP:
                            if (\count($mainStack) < 4) {
                                throw new \RuntimeException('Invalid stack operation OP_2SWAP');
                            }
                            $mainStack->swap(-3, -1);
                            $mainStack->swap(-4, -2);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_SIZE:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation OP_SIZE');
                            }
                            $size = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::int($mainStack[-1]->getSize());
                            $mainStack->push($size->getBuffer());
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_EQUAL:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_EQUALVERIFY:
                            if (\count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_EQUAL');
                            }
                            $equal = $mainStack[-2]->equals($mainStack[-1]);
                            $mainStack->pop();
                            $mainStack->pop();
                            $mainStack->push($equal ? $this->vchTrue : $this->vchFalse);
                            if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_EQUALVERIFY) {
                                if ($equal) {
                                    $mainStack->pop();
                                } else {
                                    throw new \RuntimeException('Error EQUALVERIFY');
                                }
                            }
                            break;
                        // Arithmetic operations
                        case $opCode >= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1ADD && $opCode <= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_0NOTEQUAL:
                            if ($mainStack->isEmpty()) {
                                throw new \Exception('Invalid stack operation 1ADD-OP_0NOTEQUAL');
                            }
                            $num = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack[-1], $minimal)->getGmp();
                            if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1ADD) {
                                $num = $this->math->add($num, \gmp_init(1));
                            } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1SUB) {
                                $num = $this->math->sub($num, \gmp_init(1));
                            } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_2MUL) {
                                $num = $this->math->mul(\gmp_init(2), $num);
                            } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NEGATE) {
                                $num = $this->math->sub($zero, $num);
                            } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ABS) {
                                if ($this->math->cmp($num, $zero) < 0) {
                                    $num = $this->math->sub($zero, $num);
                                }
                            } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOT) {
                                $num = \gmp_init($this->math->cmp($num, $zero) === 0 ? 1 : 0);
                            } else {
                                // is OP_0NOTEQUAL
                                $num = \gmp_init($this->math->cmp($num, $zero) !== 0 ? 1 : 0);
                            }
                            $mainStack->pop();
                            $buffer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::int(\gmp_strval($num, 10))->getBuffer();
                            $mainStack->push($buffer);
                            break;
                        case $opCode >= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ADD && $opCode <= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_MAX:
                            if (\count($mainStack) < 2) {
                                throw new \Exception('Invalid stack operation (OP_ADD - OP_MAX)');
                            }
                            $num1 = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack[-2], $minimal)->getGmp();
                            $num2 = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack[-1], $minimal)->getGmp();
                            if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ADD) {
                                $num = $this->math->add($num1, $num2);
                            } else {
                                if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_SUB) {
                                    $num = $this->math->sub($num1, $num2);
                                } else {
                                    if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_BOOLAND) {
                                        $num = (int) ($this->math->cmp($num1, $zero) !== 0 && $this->math->cmp($num2, $zero) !== 0);
                                    } else {
                                        if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_BOOLOR) {
                                            $num = (int) ($this->math->cmp($num1, $zero) !== 0 || $this->math->cmp($num2, $zero) !== 0);
                                        } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NUMEQUAL) {
                                            $num = (int) ($this->math->cmp($num1, $num2) === 0);
                                        } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NUMEQUALVERIFY) {
                                            $num = (int) ($this->math->cmp($num1, $num2) === 0);
                                        } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NUMNOTEQUAL) {
                                            $num = (int) ($this->math->cmp($num1, $num2) !== 0);
                                        } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_LESSTHAN) {
                                            $num = (int) ($this->math->cmp($num1, $num2) < 0);
                                        } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_GREATERTHAN) {
                                            $num = (int) ($this->math->cmp($num1, $num2) > 0);
                                        } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_LESSTHANOREQUAL) {
                                            $num = (int) ($this->math->cmp($num1, $num2) <= 0);
                                        } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_GREATERTHANOREQUAL) {
                                            $num = (int) ($this->math->cmp($num1, $num2) >= 0);
                                        } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_MIN) {
                                            $num = $this->math->cmp($num1, $num2) <= 0 ? $num1 : $num2;
                                        } else {
                                            $num = $this->math->cmp($num1, $num2) >= 0 ? $num1 : $num2;
                                        }
                                    }
                                }
                            }
                            $mainStack->pop();
                            $mainStack->pop();
                            $buffer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::int(\gmp_strval($num, 10))->getBuffer();
                            $mainStack->push($buffer);
                            if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NUMEQUALVERIFY) {
                                if ($this->castToBool($mainStack[-1])) {
                                    $mainStack->pop();
                                } else {
                                    throw new \RuntimeException('NUM EQUAL VERIFY error');
                                }
                            }
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_WITHIN:
                            if (\count($mainStack) < 3) {
                                throw new \RuntimeException('Invalid stack operation');
                            }
                            $num1 = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack[-3], $minimal)->getGmp();
                            $num2 = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack[-2], $minimal)->getGmp();
                            $num3 = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack[-1], $minimal)->getGmp();
                            $value = $this->math->cmp($num2, $num1) <= 0 && $this->math->cmp($num1, $num3) < 0;
                            $mainStack->pop();
                            $mainStack->pop();
                            $mainStack->pop();
                            $mainStack->push($value ? $this->vchTrue : $this->vchFalse);
                            break;
                        // Hash operation
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_RIPEMD160:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_SHA1:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_SHA256:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_HASH160:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_HASH256:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation');
                            }
                            $buffer = $mainStack[-1];
                            if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_RIPEMD160) {
                                $hash = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::ripemd160($buffer);
                            } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_SHA1) {
                                $hash = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha1($buffer);
                            } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_SHA256) {
                                $hash = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256($buffer);
                            } elseif ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_HASH160) {
                                $hash = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256ripe160($buffer);
                            } else {
                                $hash = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d($buffer);
                            }
                            $mainStack->pop();
                            $mainStack->push($hash);
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CODESEPARATOR:
                            $hashStartPos = $parser->getPosition();
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSIG:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSIGVERIFY:
                            if (\count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation');
                            }
                            $vchPubKey = $mainStack[-1];
                            $vchSig = $mainStack[-2];
                            $scriptCode = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script($script->getBuffer()->slice($hashStartPos));
                            $success = $checker->checkSig($scriptCode, $vchSig, $vchPubKey, $sigVersion, $flags);
                            $mainStack->pop();
                            $mainStack->pop();
                            $mainStack->push($success ? $this->vchTrue : $this->vchFalse);
                            if (!$success && $flags & self::VERIFY_NULLFAIL && $vchSig->getSize() > 0) {
                                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_NULLFAIL, 'Signature must be zero for failed OP_CHECK(MULTIS)SIG operation');
                            }
                            if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSIGVERIFY) {
                                if ($success) {
                                    $mainStack->pop();
                                } else {
                                    throw new \RuntimeException('Checksig verify');
                                }
                            }
                            break;
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKMULTISIG:
                        case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKMULTISIGVERIFY:
                            $i = 1;
                            if (\count($mainStack) < $i) {
                                throw new \RuntimeException('Invalid stack operation');
                            }
                            $keyCount = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack[-$i], $minimal)->getInt();
                            if ($keyCount < 0 || $keyCount > 20) {
                                throw new \RuntimeException('OP_CHECKMULTISIG: Public key count exceeds 20');
                            }
                            $opCount += $keyCount;
                            $this->checkOpcodeCount($opCount);
                            // Extract positions of the keys, and signatures, from the stack.
                            $ikey = ++$i;
                            $ikey2 = $keyCount + 2;
                            $i += $keyCount;
                            if (\count($mainStack) < $i) {
                                throw new \RuntimeException('Invalid stack operation');
                            }
                            $sigCount = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($mainStack[-$i], $minimal)->getInt();
                            if ($sigCount < 0 || $sigCount > $keyCount) {
                                throw new \RuntimeException('Invalid Signature count');
                            }
                            $isig = ++$i;
                            $i += $sigCount;
                            // Extract the script since the last OP_CODESEPARATOR
                            $scriptCode = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script($script->getBuffer()->slice($hashStartPos));
                            $fSuccess = \true;
                            while ($fSuccess && $sigCount > 0) {
                                // Fetch the signature and public key
                                $sig = $mainStack[-$isig];
                                $pubkey = $mainStack[-$ikey];
                                if ($checker->checkSig($scriptCode, $sig, $pubkey, $sigVersion, $flags)) {
                                    $isig++;
                                    $sigCount--;
                                }
                                $ikey++;
                                $keyCount--;
                                // If there are more signatures left than keys left,
                                // then too many signatures have failed. Exit early,
                                // without checking any further signatures.
                                if ($sigCount > $keyCount) {
                                    $fSuccess = \false;
                                }
                            }
                            while ($i-- > 1) {
                                // If the operation failed, we require that all signatures must be empty vector
                                if (!$fSuccess && $flags & self::VERIFY_NULLFAIL && !$ikey2 && $mainStack[-1]->getSize() > 0) {
                                    throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_NULLFAIL, 'Bad signature must be empty vector');
                                }
                                if ($ikey2 > 0) {
                                    $ikey2--;
                                }
                                $mainStack->pop();
                            }
                            // A bug causes CHECKMULTISIG to consume one extra argument
                            // whose contents were not checked in any way.
                            //
                            // Unfortunately this is a potential source of mutability,
                            // so optionally verify it is exactly equal to zero prior
                            // to removing it from the stack.
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation');
                            }
                            if ($flags & self::VERIFY_NULL_DUMMY && $mainStack[-1]->getSize() !== 0) {
                                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException(self::VERIFY_NULL_DUMMY, 'Extra P2SH stack value should be OP_0');
                            }
                            $mainStack->pop();
                            $mainStack->push($fSuccess ? $this->vchTrue : $this->vchFalse);
                            if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKMULTISIGVERIFY) {
                                if ($fSuccess) {
                                    $mainStack->pop();
                                } else {
                                    throw new \RuntimeException('OP_CHECKMULTISIG verify');
                                }
                            }
                            break;
                        default:
                            throw new \RuntimeException('Opcode not found');
                    }
                    if (\count($mainStack) + \count($altStack) > 1000) {
                        throw new \RuntimeException('Invalid stack size, exceeds 1000');
                    }
                }
            }
            if (\count($vfStack) !== 0) {
                throw new \RuntimeException('Unbalanced conditional at script end');
            }
            return \true;
        } catch (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\ScriptRuntimeException $e) {
            // echo "\n Runtime: " . $e->getMessage() . "\n" . $e->getTraceAsString() . PHP_EOL;
            // Failure due to script tags, can access flag: $e->getFailureFlag()
            return \false;
        } catch (\Exception $e) {
            // echo "\n General: " . $e->getMessage()  . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
            return \false;
        }
    }
}
