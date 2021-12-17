<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface RbgInterface
{
    /**
     * Return $numBytes bytes deterministically derived from a seed
     *
     * @param int $numNumBytes
     * @return BufferInterface
     */
    public function bytes(int $numNumBytes) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
}
