<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class Conditional
{
    /**
     * @var int
     */
    private $opcode;
    /**
     * @var bool
     */
    private $value;
    /**
     * @var null
     */
    private $providedBy = null;
    /**
     * Conditional constructor.
     * @param int $opcode
     */
    public function __construct(int $opcode)
    {
        if ($opcode !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_IF && $opcode !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOTIF) {
            throw new \RuntimeException("Opcode for conditional is only IF / NOTIF");
        }
        $this->opcode = $opcode;
    }
    /**
     * @return int
     */
    public function getOp() : int
    {
        return $this->opcode;
    }
    /**
     * @param bool $value
     */
    public function setValue(bool $value)
    {
        $this->value = $value;
    }
    /**
     * @return bool
     */
    public function hasValue() : bool
    {
        return null !== $this->value;
    }
    /**
     * @return bool
     */
    public function getValue() : bool
    {
        if (null === $this->value) {
            throw new \RuntimeException("Value not set on conditional");
        }
        return $this->value;
    }
    /**
     * @param Checksig $checksig
     */
    public function providedBy(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\Checksig $checksig)
    {
        $this->providedBy = $checksig;
    }
    /**
     * @return BufferInterface[]
     */
    public function serialize() : array
    {
        if ($this->hasValue() && null === $this->providedBy) {
            return [$this->value ? new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer("\1") : new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer()];
        }
        return [];
    }
}
