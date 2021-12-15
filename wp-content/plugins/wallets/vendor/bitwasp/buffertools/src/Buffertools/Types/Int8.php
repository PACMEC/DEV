<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types;

class Int8 extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\AbstractSignedInt
{
    /**
     * {@inheritdoc}
     * @see \BitWasp\Buffertools\Types\TypeInterface::getBitSize()
     */
    public function getBitSize() : int
    {
        return 8;
    }
}
