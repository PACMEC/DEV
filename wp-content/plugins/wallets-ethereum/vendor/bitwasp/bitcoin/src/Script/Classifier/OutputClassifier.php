<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Parser\Operation;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class OutputClassifier
{
    /**
     * @deprecated
     */
    const PAYTOPUBKEY = 'pubkey';
    /**
     * @deprecated
     */
    const PAYTOPUBKEYHASH = 'pubkeyhash';
    /**
     * @deprecated
     */
    const PAYTOSCRIPTHASH = 'scripthash';
    /**
     * @deprecated
     */
    const WITNESS_V0_KEYHASH = 'witness_v0_keyhash';
    /**
     * @deprecated
     */
    const WITNESS_V0_SCRIPTHASH = 'witness_v0_scripthash';
    /**
     * @deprecated
     */
    const MULTISIG = 'multisig';
    /**
     * @deprecated
     */
    const NULLDATA = 'nulldata';
    /**
     * @deprecated
     */
    const UNKNOWN = 'nonstandard';
    /**
     * @deprecated
     */
    const NONSTANDARD = 'nonstandard';
    /**
     * @deprecated
     */
    const P2PK = 'pubkey';
    /**
     * @deprecated
     */
    const P2PKH = 'pubkeyhash';
    /**
     * @deprecated
     */
    const P2SH = 'scripthash';
    /**
     * @deprecated
     */
    const P2WSH = 'witness_v0_scripthash';
    /**
     * @deprecated
     */
    const P2WKH = 'witness_v0_keyhash';
    /**
     * @deprecated
     */
    const WITNESS_COINBASE_COMMITMENT = 'witness_coinbase_commitment';
    /**
     * @param Operation[] $decoded
     * @return false|BufferInterface
     */
    private function decodeP2PK(array $decoded)
    {
        if (\count($decoded) !== 2 || !$decoded[0]->isPush()) {
            return \false;
        }
        $size = $decoded[0]->getDataSize();
        if ($size === 33 || $size === 65) {
            $op = $decoded[1];
            if ($op->getOp() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSIG) {
                return $decoded[0]->getData();
            }
        }
        return \false;
    }
    /**
     * @param ScriptInterface $script
     * @return bool
     */
    public function isPayToPublicKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : bool
    {
        try {
            return $this->decodeP2PK($script->getScriptParser()->decode()) !== \false;
        } catch (\Exception $e) {
            /** Return false later */
        }
        return \false;
    }
    /**
     * @param Operation[] $decoded
     * @return BufferInterface|false
     */
    private function decodeP2PKH(array $decoded)
    {
        if (\count($decoded) !== 5) {
            return \false;
        }
        $dup = $decoded[0];
        $hash = $decoded[1];
        $buf = $decoded[2];
        $eq = $decoded[3];
        $checksig = $decoded[4];
        foreach ([$dup, $hash, $eq, $checksig] as $op) {
            /** @var Operation $op */
            if ($op->isPush()) {
                return \false;
            }
        }
        if ($dup->getOp() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_DUP && $hash->getOp() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_HASH160 && $buf->isPush() && $buf->getDataSize() === 20 && $eq->getOp() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_EQUALVERIFY && $checksig->getOp() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSIG) {
            return $decoded[2]->getData();
        }
        return \false;
    }
    /**
     * @param ScriptInterface $script
     * @return bool
     */
    public function isPayToPublicKeyHash(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : bool
    {
        try {
            return $this->decodeP2PKH($script->getScriptParser()->decode()) !== \false;
        } catch (\Exception $e) {
            /** Return false later */
        }
        return \false;
    }
    /**
     * @param array $decoded
     * @return bool|BufferInterface
     */
    private function decodeP2SH(array $decoded)
    {
        if (\count($decoded) !== 3) {
            return \false;
        }
        $op_hash = $decoded[0];
        if ($op_hash->isPush() || $op_hash->getOp() !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_HASH160) {
            return \false;
        }
        $buffer = $decoded[1];
        if (!$buffer->isPush() || $buffer->getOp() !== 20) {
            return \false;
        }
        $eq = $decoded[2];
        if (!$eq->isPush() && $eq->getOp() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_EQUAL) {
            return $decoded[1]->getData();
        }
        return \false;
    }
    /**
     * @param ScriptInterface $script
     * @return bool
     */
    public function isPayToScriptHash(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : bool
    {
        try {
            return $this->decodeP2SH($script->getScriptParser()->decode()) !== \false;
        } catch (\Exception $e) {
            /** Return false later */
        }
        return \false;
    }
    /**
     * @param Operation[] $decoded
     * @return bool|BufferInterface[]
     */
    private function decodeMultisig(array $decoded)
    {
        $count = \count($decoded);
        if ($count <= 3) {
            return \false;
        }
        $mOp = $decoded[0];
        $nOp = $decoded[$count - 2];
        $checksig = $decoded[$count - 1];
        if ($mOp->isPush() || $nOp->isPush() || $checksig->isPush()) {
            return \false;
        }
        /** @var Operation[] $vKeys */
        $vKeys = \array_slice($decoded, 1, -2);
        $solutions = [];
        foreach ($vKeys as $key) {
            if (!$key->isPush() || !\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey::isCompressedOrUncompressed($key->getData())) {
                return \false;
            }
            $solutions[] = $key->getData();
        }
        if ($mOp->getOp() >= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_0 && $nOp->getOp() <= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_16 && $checksig->getOp() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKMULTISIG) {
            return $solutions;
        }
        return \false;
    }
    /**
     * @param ScriptInterface $script
     * @return bool
     */
    public function isMultisig(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : bool
    {
        try {
            return $this->decodeMultisig($script->getScriptParser()->decode()) !== \false;
        } catch (\Exception $e) {
            /** Return false later */
        }
        return \false;
    }
    /**
     * @param ScriptInterface $script
     * @param Operation[] $decoded
     * @return false|BufferInterface
     */
    private function decodeWitnessNoLimit(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, array $decoded)
    {
        $size = $script->getBuffer()->getSize();
        if ($size < 4 || $size > 40) {
            return \false;
        }
        if (\count($decoded) !== 2 || !$decoded[1]->isPush()) {
            return \false;
        }
        $version = $decoded[0]->getOp();
        if ($version !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_0 && ($version < \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1 || $version > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_16)) {
            return \false;
        }
        $witness = $decoded[1];
        if ($size === $witness->getDataSize() + 2) {
            return $witness->getData();
        }
        return \false;
    }
    /**
     * @param Operation[] $decoded
     * @return BufferInterface|false
     */
    private function decodeP2WKH2(array $decoded)
    {
        if (\count($decoded) === 2 && $decoded[0]->getOp() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_0 && $decoded[1]->isPush() && $decoded[1]->getDataSize() === 20) {
            return $decoded[1]->getData();
        }
        return \false;
    }
    /**
     * @param Operation[] $decoded
     * @return BufferInterface|false
     */
    private function decodeP2WSH2(array $decoded)
    {
        if (\count($decoded) === 2 && $decoded[0]->getOp() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_0 && $decoded[1]->isPush() && $decoded[1]->getDataSize() === 32) {
            return $decoded[1]->getData();
        }
        return \false;
    }
    /**
     * @param ScriptInterface $script
     * @return bool
     */
    public function isWitness(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : bool
    {
        try {
            return $this->decodeWitnessNoLimit($script, $script->getScriptParser()->decode()) !== \false;
        } catch (\Exception $e) {
            /** Return false later */
        }
        return \false;
    }
    /**
     * @param Operation[] $decoded
     * @return false|BufferInterface
     */
    private function decodeNullData(array $decoded)
    {
        if (\count($decoded) !== 2) {
            return \false;
        }
        if ($decoded[0]->getOp() === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_RETURN && $decoded[1]->isPush()) {
            return $decoded[1]->getData();
        }
        return \false;
    }
    /**
     * @param ScriptInterface $script
     * @return bool
     */
    public function isNullData(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : bool
    {
        try {
            return $this->decodeNullData($script->getScriptParser()->decode()) !== \false;
        } catch (\Exception $e) {
        }
        return \false;
    }
    /**
     * @param array $decoded
     * @return bool|BufferInterface
     */
    private function decodeWitnessCoinbaseCommitment(array $decoded)
    {
        if (\count($decoded) !== 2) {
            return \false;
        }
        if ($decoded[0]->isPush() || $decoded[0]->getOp() !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_RETURN) {
            return \false;
        }
        if ($decoded[1]->isPush()) {
            $data = $decoded[1]->getData()->getBinary();
            if ($decoded[1]->getDataSize() === 0x24 && \substr($data, 0, 4) === "ª!©í") {
                return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\substr($data, 4));
            }
        }
        return \false;
    }
    /**
     * @param ScriptInterface $script
     * @return bool
     */
    public function isWitnessCoinbaseCommitment(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : bool
    {
        try {
            return $this->decodeWitnessCoinbaseCommitment($script->getScriptParser()->decode()) !== \false;
        } catch (\Exception $e) {
        }
        return \false;
    }
    /**
     * @param array $decoded
     * @param null $solution
     * @return string
     */
    private function classifyDecoded(array $decoded, &$solution = null) : string
    {
        $type = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::NONSTANDARD;
        if ($pubKey = $this->decodeP2PK($decoded)) {
            $type = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PK;
            $solution = $pubKey;
        } else {
            if ($pubKeyHash = $this->decodeP2PKH($decoded)) {
                $type = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PKH;
                $solution = $pubKeyHash;
            } else {
                if ($multisig = $this->decodeMultisig($decoded)) {
                    $type = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::MULTISIG;
                    $solution = $multisig;
                } else {
                    if ($scriptHash = $this->decodeP2SH($decoded)) {
                        $type = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH;
                        $solution = $scriptHash;
                    } else {
                        if ($witnessScriptHash = $this->decodeP2WSH2($decoded)) {
                            $type = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WSH;
                            $solution = $witnessScriptHash;
                        } else {
                            if ($witnessKeyHash = $this->decodeP2WKH2($decoded)) {
                                $type = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WKH;
                                $solution = $witnessKeyHash;
                            } else {
                                if ($witCommitHash = $this->decodeWitnessCoinbaseCommitment($decoded)) {
                                    $type = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::WITNESS_COINBASE_COMMITMENT;
                                    $solution = $witCommitHash;
                                } else {
                                    if ($nullData = $this->decodeNullData($decoded)) {
                                        $type = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::NULLDATA;
                                        $solution = $nullData;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $type;
    }
    /**
     * @param ScriptInterface $script
     * @param mixed $solution
     * @return string
     */
    public function classify(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, &$solution = null) : string
    {
        $decoded = $script->getScriptParser()->decode();
        $type = $this->classifyDecoded($decoded, $solution);
        return $type;
    }
    /**
     * @param ScriptInterface $script
     * @return OutputData
     */
    public function decode(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData
    {
        $solution = null;
        $type = $this->classify($script, $solution);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData($type, $script, $solution);
    }
    /**
     * @param ScriptInterface $script
     * @param bool $allowNonstandard
     * @return OutputData[]
     */
    public function decodeSequence(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, bool $allowNonstandard = \false) : array
    {
        $decoded = $script->getScriptParser()->decode();
        $j = 0;
        $l = \count($decoded);
        $result = [];
        while ($j < $l) {
            $type = null;
            $slice = null;
            $solution = null;
            // increment the $last, and break if it's valid
            for ($i = 0; $i < $l - $j + 1; $i++) {
                $slice = \array_slice($decoded, $j, $i);
                $chkType = $this->classifyDecoded($slice, $solution);
                if ($chkType !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::NONSTANDARD) {
                    $type = $chkType;
                    break;
                }
            }
            if (null === $type) {
                if (!$allowNonstandard) {
                    throw new \RuntimeException("Unable to classify script as a sequence of templated types");
                }
                $j++;
            } else {
                $j += $i;
                /** @var Operation[] $slice */
                /** @var mixed $solution */
                $result[] = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputData($type, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::fromOperations($slice), $solution);
            }
        }
        return $result;
    }
}
