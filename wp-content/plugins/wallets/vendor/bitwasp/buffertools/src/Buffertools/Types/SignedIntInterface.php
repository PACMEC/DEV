<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types;

interface SignedIntInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\TypeInterface
{
    /**
     * @return int
     */
    public function getBitSize() : int;
}
