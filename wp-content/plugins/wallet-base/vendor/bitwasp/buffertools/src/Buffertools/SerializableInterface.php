<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools;

interface SerializableInterface
{
    /**
     * @return Buffer
     */
    public function getBuffer();
}
