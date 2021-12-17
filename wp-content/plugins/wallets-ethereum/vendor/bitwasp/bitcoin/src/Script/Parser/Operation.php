<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Parser;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class Operation
{
    /**
     * @var int[]
     */
    protected static $logical = [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_IF, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOTIF, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ELSE, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ENDIF];
    /**
     * @var bool
     */
    private $push;
    /**
     * @var int
     */
    private $opCode;
    /**
     * @var BufferInterface
     */
    private $pushData;
    /**
     * @var int
     */
    private $pushDataSize;
    /**
     * Operation constructor.
     * @param int $opCode
     * @param BufferInterface $pushData
     * @param int $pushDataSize
     */
    public function __construct(int $opCode, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $pushData, int $pushDataSize = 0)
    {
        $this->push = $opCode >= 0 && $opCode <= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_PUSHDATA4;
        $this->opCode = $opCode;
        $this->pushData = $pushData;
        $this->pushDataSize = $pushDataSize;
    }
    /**
     * @return BufferInterface|int
     */
    public function encode()
    {
        if ($this->push) {
            return $this->pushData;
        } else {
            return $this->opCode;
        }
    }
    /**
     * @return bool
     */
    public function isPush() : bool
    {
        return $this->push;
    }
    /**
     * @return bool
     */
    public function isLogical() : bool
    {
        return !$this->isPush() && \in_array($this->opCode, self::$logical);
    }
    /**
     * @return int
     */
    public function getOp() : int
    {
        return $this->opCode;
    }
    /**
     * @return BufferInterface
     */
    public function getData() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return $this->pushData;
    }
    /**
     * @return int
     */
    public function getDataSize() : int
    {
        if (!$this->push) {
            throw new \RuntimeException("Op wasn't a push operation");
        }
        return $this->pushDataSize;
    }
}
