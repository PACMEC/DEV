<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types;

class Int256 extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\AbstractSignedInt
{
    /**
     * @return int
     */
    public function getBitSize() : int
    {
        return 256;
    }
}
