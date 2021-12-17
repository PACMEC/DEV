<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types;

class Uint128 extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\AbstractUint
{
    /**
     * {@inheritdoc}
     * @see \BitWasp\Buffertools\Types\TypeInterface::getBitSize()
     */
    public function getBitSize() : int
    {
        return 128;
    }
}
