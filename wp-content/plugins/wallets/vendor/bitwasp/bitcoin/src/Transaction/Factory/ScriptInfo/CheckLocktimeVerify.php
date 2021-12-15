<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\ScriptInfo;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Locktime;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Parser\Operation;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
class CheckLocktimeVerify
{
    /**
     * @var int
     */
    private $nLockTime;
    /**
     * @var bool
     */
    private $toBlock;
    /**
     * CheckLocktimeVerify constructor.
     * @param int $nLockTime
     */
    public function __construct(int $nLockTime)
    {
        if ($nLockTime < 0) {
            throw new \RuntimeException("locktime cannot be negative");
        }
        if ($nLockTime > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Locktime::INT_MAX) {
            throw new \RuntimeException("nLockTime exceeds maximum value");
        }
        $this->nLockTime = $nLockTime;
        $this->toBlock = (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Locktime())->isLockedToBlock($nLockTime);
    }
    /**
     * @param Operation[] $chunks
     * @param bool $fMinimal
     * @return CheckLocktimeVerify
     */
    public static function fromDecodedScript(array $chunks, bool $fMinimal = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\ScriptInfo\CheckLocktimeVerify
    {
        if (\count($chunks) !== 3) {
            throw new \RuntimeException("Invalid number of items for CLTV");
        }
        if (!$chunks[0]->isPush()) {
            throw new \InvalidArgumentException('CLTV script had invalid value for time');
        }
        if ($chunks[1]->getOp() !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CHECKLOCKTIMEVERIFY) {
            throw new \InvalidArgumentException('CLTV script invalid opcode');
        }
        if ($chunks[2]->getOp() !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_DROP) {
            throw new \InvalidArgumentException('CLTV script invalid opcode');
        }
        $numLockTime = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Number::buffer($chunks[0]->getData(), $fMinimal, 5);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\ScriptInfo\CheckLocktimeVerify($numLockTime->getInt());
    }
    /**
     * @param ScriptInterface $script
     * @return CheckLocktimeVerify
     */
    public static function fromScript(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : self
    {
        return static::fromDecodedScript($script->getScriptParser()->decode());
    }
    /**
     * @return int
     */
    public function getLocktime() : int
    {
        return $this->nLockTime;
    }
    /**
     * @return bool
     */
    public function isLockedToBlock() : bool
    {
        return $this->toBlock;
    }
}
