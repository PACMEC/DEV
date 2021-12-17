<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script;

function decodeOpN(int $op) : int
{
    if ($op === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_0) {
        return 0;
    }
    if (!($op === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1NEGATE || $op >= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1 && $op <= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_16)) {
        throw new \RuntimeException("Invalid opcode");
    }
    return $op - (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1 - 1);
}
function encodeOpN(int $op) : int
{
    if ($op === 0) {
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_0;
    }
    if (!($op === -1 || $op >= 1 && $op <= 16)) {
        throw new \RuntimeException("Invalid value");
    }
    return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_1 + $op - 1;
}
