<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface SerializableInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\SerializableInterface
{
    /**
     * @return BufferInterface
     */
    public function getBuffer() : BufferInterface;
    /**
     * @return string
     */
    public function getHex() : string;
    /**
     * @return string
     */
    public function getBinary() : string;
    /**
     * @return string
     */
    public function getInt();
}
