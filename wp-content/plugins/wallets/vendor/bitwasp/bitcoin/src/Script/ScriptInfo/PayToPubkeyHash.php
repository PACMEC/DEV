<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInfo;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Parser\Operation;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class PayToPubkeyHash
{
    /**
     * @var BufferInterface
     */
    private $hash;
    /**
     * @var bool
     */
    private $verify;
    /**
     * @var int
     */
    private $opcode;
    /**
     * PayToPubkeyHash constructor.
     * @param int $opcode
     * @param BufferInterface $hash160
     * @param bool $allowVerify
     */
    public function __construct(int $opcode, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $hash160, bool $allowVerify = \false)
    {
        if ($hash160->getSize() !== 20) {
            throw new \RuntimeException('Malformed pay-to-pubkey-hash script');
        }
        if ($opcode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSIG) {
            $verify = \false;
        } else {
            if ($allowVerify && $opcode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSIGVERIFY) {
                $verify = \true;
            } else {
                throw new \RuntimeException("Malformed pay-to-pubkey-hash script - invalid opcode");
            }
        }
        $this->hash = $hash160;
        $this->opcode = $opcode;
        $this->verify = $verify;
    }
    /**
     * @param Operation[] $chunks
     * @param bool $allowVerify
     * @return PayToPubKeyHash
     */
    public static function fromDecodedScript(array $chunks, bool $allowVerify = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInfo\PayToPubKeyHash
    {
        if (\count($chunks) !== 5) {
            throw new \RuntimeException('Malformed pay-to-pubkey-hash script');
        }
        if ($chunks[0]->getOp() !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_DUP || $chunks[1]->getOp() !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_HASH160 || $chunks[3]->getOp() !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_EQUALVERIFY) {
            throw new \RuntimeException('Malformed pay-to-pubkey-hash script');
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInfo\PayToPubkeyHash($chunks[4]->getOp(), $chunks[2]->getData(), $allowVerify);
    }
    /**
     * @param ScriptInterface $script
     * @param bool $allowVerify
     * @return PayToPubkeyHash
     */
    public static function fromScript(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, bool $allowVerify = \false)
    {
        return self::fromDecodedScript($script->getScriptParser()->decode(), $allowVerify);
    }
    /**
     * @return string
     */
    public function getType() : string
    {
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PK;
    }
    /**
     * @return int
     */
    public function getRequiredSigCount() : int
    {
        return 1;
    }
    /**
     * @return int
     */
    public function getKeyCount() : int
    {
        return 1;
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
        return $publicKey->getPubKeyHash()->equals($this->hash);
    }
    /**
     * @return BufferInterface
     */
    public function getPubKeyHash() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return $this->hash;
    }
}
