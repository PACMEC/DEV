<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools;

interface BufferInterface
{
    /**
     * @param int              $start
     * @param int|null         $end
     * @return BufferInterface
     * @throws \Exception
     */
    public function slice(int $start, int $end = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * Get the size of the buffer to be returned
     *
     * @return int
     */
    public function getSize() : int;
    /**
     * Get the size of the value stored in the buffer
     *
     * @return int
     */
    public function getInternalSize() : int;
    /**
     * @return string
     */
    public function getBinary() : string;
    /**
     * @return string
     */
    public function getHex() : string;
    /**
     * @return int|string
     */
    public function getInt();
    /**
     * @return \GMP
     */
    public function getGmp() : \GMP;
    /**
     * @return Buffer
     */
    public function flip() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * @param BufferInterface $other
     * @return bool
     */
    public function equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $other) : bool;
}
