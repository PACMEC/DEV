<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInfo;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Parser\Operation;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class PayToPubkey
{
    /**
     * @var BufferInterface
     */
    private $publicKey;
    /**
     * @var bool
     */
    private $verify;
    /**
     * @var int
     */
    private $opcode;
    /**
     * PayToPubkey constructor.
     * @param int $opcode
     * @param BufferInterface $publicKey
     * @param bool $allowVerify
     */
    public function __construct(int $opcode, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $publicKey, bool $allowVerify = \false)
    {
        if ($opcode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSIG) {
            $verify = \false;
        } else {
            if ($allowVerify && $opcode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKSIGVERIFY) {
                $verify = \true;
            } else {
                throw new \InvalidArgumentException('Malformed pay-to-pubkey script - invalid opcode');
            }
        }
        $this->verify = $verify;
        $this->opcode = $opcode;
        $this->publicKey = $publicKey;
    }
    /**
     * @param Operation[] $chunks
     * @param bool $allowVerify
     * @return static
     */
    public static function fromDecodedScript(array $chunks, bool $allowVerify = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInfo\PayToPubkey
    {
        if (\count($chunks) !== 2 || !$chunks[0]->isPush() || $chunks[1]->isPush()) {
            throw new \InvalidArgumentException('Malformed pay-to-pubkey script');
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInfo\PayToPubkey($chunks[1]->getOp(), $chunks[0]->getData(), $allowVerify);
    }
    /**
     * @param ScriptInterface $script
     * @param bool $allowVerify
     * @return PayToPubkey
     */
    public static function fromScript(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, bool $allowVerify = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInfo\PayToPubkey
    {
        return static::fromDecodedScript($script->getScriptParser()->decode(), $allowVerify);
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
        return $publicKey->getBuffer()->equals($this->publicKey);
    }
    /**
     * @return BufferInterface
     */
    public function getKeyBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return $this->publicKey;
    }
}
