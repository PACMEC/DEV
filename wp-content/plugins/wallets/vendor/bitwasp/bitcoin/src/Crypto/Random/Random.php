<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\RandomBytesFailure;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class Random implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\RbgInterface
{
    /**
     * Return $length bytes. Throws an exception if
     * @param int $length
     * @return BufferInterface
     * @throws RandomBytesFailure
     */
    public function bytes(int $length = 32) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\random_bytes($length), $length);
    }
}
