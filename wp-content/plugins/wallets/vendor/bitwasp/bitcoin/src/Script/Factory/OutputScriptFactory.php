<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Factory;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools;
class OutputScriptFactory
{
    /**
     * @param PublicKeyInterface $publicKey
     * @return ScriptInterface
     */
    public function p2pk(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface $publicKey) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return $this->payToPubKey($publicKey);
    }
    /**
     * @param BufferInterface $pubKeyHash
     * @return ScriptInterface
     */
    public function p2pkh(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $pubKeyHash) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return $this->payToPubKeyHash($pubKeyHash);
    }
    /**
     * @param BufferInterface $scriptHash
     * @return ScriptInterface
     */
    public function p2sh(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $scriptHash) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return $this->payToScriptHash($scriptHash);
    }
    /**
     * @param BufferInterface $witnessScriptHash
     * @return ScriptInterface
     */
    public function p2wsh(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $witnessScriptHash) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return $this->witnessScriptHash($witnessScriptHash);
    }
    /**
     * @param BufferInterface $witnessKeyHash
     * @return ScriptInterface
     */
    public function p2wkh(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $witnessKeyHash) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return $this->witnessKeyHash($witnessKeyHash);
    }
    /**
     * Create a Pay to pubkey output
     *
     * @param PublicKeyInterface  $publicKey
     * @return ScriptInterface
     */
    public function payToPubKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface $publicKey) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::sequence([$publicKey->getBuffer(), \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSIG]);
    }
    /**
     * Create a P2PKH output script
     *
     * @param BufferInterface $pubKeyHash
     * @return ScriptInterface
     */
    public function payToPubKeyHash(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $pubKeyHash) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        if ($pubKeyHash->getSize() !== 20) {
            throw new \RuntimeException('Public key hash must be exactly 20 bytes');
        }
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::sequence([\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_DUP, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_HASH160, $pubKeyHash, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_EQUALVERIFY, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSIG]);
    }
    /**
        /**
    * Create a P2SH output script
    *
    * @param BufferInterface $scriptHash
    * @return ScriptInterface
    */
    public function payToScriptHash(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $scriptHash) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        if ($scriptHash->getSize() !== 20) {
            throw new \RuntimeException('P2SH scriptHash must be exactly 20 bytes');
        }
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::sequence([\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_HASH160, $scriptHash, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_EQUAL]);
    }
    /**
     * @param int $m
     * @param PublicKeyInterface[] $keys
     * @param bool|true $sort
     * @return ScriptInterface
     */
    public function multisig(int $m, array $keys = [], bool $sort = \true) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return self::multisigKeyBuffers($m, \array_map(function (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface $key) : BufferInterface {
            return $key->getBuffer();
        }, $keys), $sort);
    }
    /**
     * @param int $m
     * @param BufferInterface[] $keys
     * @param bool $sort
     * @return ScriptInterface
     */
    public function multisigKeyBuffers(int $m, array $keys = [], bool $sort = \true) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        $n = \count($keys);
        if ($m < 0) {
            throw new \LogicException('Number of signatures cannot be less than zero');
        }
        if ($m > $n) {
            throw new \LogicException('Required number of sigs exceeds number of public keys');
        }
        if ($n > 20) {
            throw new \LogicException('Number of public keys is greater than 16');
        }
        if ($sort) {
            $keys = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::sort($keys);
        }
        $new = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::create();
        $new->int($m);
        foreach ($keys as $key) {
            if ($key->getSize() !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey::LENGTH_COMPRESSED && $key->getSize() !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey::LENGTH_UNCOMPRESSED) {
                throw new \RuntimeException("Invalid length for public key buffer");
            }
            $new->push($key);
        }
        return $new->int($n)->opcode(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKMULTISIG)->getScript();
    }
    /**
     * @param BufferInterface $keyHash
     * @return ScriptInterface
     */
    public function witnessKeyHash(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $keyHash) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        if ($keyHash->getSize() !== 20) {
            throw new \RuntimeException('witness key-hash should be 20 bytes');
        }
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::sequence([\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_0, $keyHash]);
    }
    /**
     * @param BufferInterface $scriptHash
     * @return ScriptInterface
     */
    public function witnessScriptHash(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $scriptHash) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        if ($scriptHash->getSize() !== 32) {
            throw new \RuntimeException('witness script-hash should be 32 bytes');
        }
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::sequence([\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_0, $scriptHash]);
    }
    /**
     * @param BufferInterface $commitment
     * @return ScriptInterface
     */
    public function witnessCoinbaseCommitment(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $commitment) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        if ($commitment->getSize() !== 32) {
            throw new \RuntimeException('Witness commitment hash must be exactly 32-bytes');
        }
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::sequence([\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_RETURN, new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer("ª!©í" . $commitment->getBinary())]);
    }
}
