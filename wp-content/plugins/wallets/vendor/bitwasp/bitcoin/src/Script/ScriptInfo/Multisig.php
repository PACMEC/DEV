<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInfo;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Parser\Operation;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class Multisig
{
    /**
     * @var int
     */
    private $m;
    /**
     * @var int
     */
    private $n;
    /**
     * @var bool
     */
    private $verify = \false;
    /**
     * @var BufferInterface[]
     */
    private $keyBuffers = [];
    /**
     * @var PublicKeySerializerInterface
     */
    private $pubKeySerializer;
    /**
     * Multisig constructor.
     * @param int $requiredSigs
     * @param BufferInterface[] $keys
     * @param int $opcode
     * @param bool $allowVerify
     * @param PublicKeySerializerInterface|null $pubKeySerializer
     */
    public function __construct(int $requiredSigs, array $keys, int $opcode, $allowVerify = \false, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface $pubKeySerializer = null)
    {
        if ($opcode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKMULTISIG) {
            $verify = \false;
        } else {
            if ($allowVerify && $opcode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKMULTISIGVERIFY) {
                $verify = \true;
            } else {
                throw new \InvalidArgumentException('Malformed multisig script');
            }
        }
        foreach ($keys as $key) {
            if (!\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey::isCompressedOrUncompressed($key)) {
                throw new \RuntimeException("Malformed public key");
            }
        }
        $keyCount = \count($keys);
        if ($requiredSigs < 0 || $requiredSigs > $keyCount) {
            throw new \RuntimeException("Invalid number of required signatures");
        }
        if ($keyCount < 1 || $keyCount > 16) {
            throw new \RuntimeException("Invalid number of public keys");
        }
        if (null === $pubKeySerializer) {
            $pubKeySerializer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface::class, \true, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter());
        }
        $this->verify = $verify;
        $this->m = $requiredSigs;
        $this->n = $keyCount;
        $this->keyBuffers = $keys;
        $this->pubKeySerializer = $pubKeySerializer;
    }
    /**
     * @param Operation[] $decoded
     * @param PublicKeySerializerInterface|null $pubKeySerializer
     * @param bool $allowVerify
     * @return Multisig
     */
    public static function fromDecodedScript(array $decoded, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface $pubKeySerializer = null, $allowVerify = \false)
    {
        if (\count($decoded) < 4) {
            throw new \InvalidArgumentException('Malformed multisig script');
        }
        $mCode = $decoded[0]->getOp();
        $nCode = $decoded[\count($decoded) - 2]->getOp();
        $opCode = \end($decoded)->getOp();
        $requiredSigs = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\decodeOpN($mCode);
        $publicKeyBuffers = [];
        foreach (\array_slice($decoded, 1, -2) as $key) {
            /** @var \BitWasp\Bitcoin\Script\Parser\Operation $key */
            if (!$key->isPush()) {
                throw new \RuntimeException('Malformed multisig script');
            }
            $buffer = $key->getData();
            $publicKeyBuffers[] = $buffer;
        }
        $keyCount = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\decodeOpN($nCode);
        if ($keyCount !== \count($publicKeyBuffers)) {
            throw new \LogicException('No public keys found in script');
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInfo\Multisig($requiredSigs, $publicKeyBuffers, $opCode, $allowVerify, $pubKeySerializer);
    }
    /**
     * @param ScriptInterface $script
     * @param PublicKeySerializerInterface|null $pubKeySerializer
     * @param bool $allowVerify
     * @return Multisig
     */
    public static function fromScript(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface $pubKeySerializer = null, bool $allowVerify = \false)
    {
        return static::fromDecodedScript($script->getScriptParser()->decode(), $pubKeySerializer, $allowVerify);
    }
    /**
     * @return string
     */
    public function getType() : string
    {
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::MULTISIG;
    }
    /**
     * @return int
     */
    public function getRequiredSigCount() : int
    {
        return $this->m;
    }
    /**
     * @return int
     */
    public function getKeyCount() : int
    {
        return $this->n;
    }
    /**
     * @return bool
     */
    public function isChecksigVerify() : bool
    {
        return $this->verify;
    }
    /**
     * @param PublicKeyInterface $publicKey
     * @return bool
     */
    public function checkInvolvesKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface $publicKey) : bool
    {
        $buffer = $this->pubKeySerializer->serialize($publicKey);
        foreach ($this->keyBuffers as $key) {
            if ($key->equals($buffer)) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @return array|BufferInterface[]
     */
    public function getKeyBuffers() : array
    {
        return $this->keyBuffers;
    }
}
