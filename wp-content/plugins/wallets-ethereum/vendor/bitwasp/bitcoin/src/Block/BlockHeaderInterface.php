<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface BlockHeaderInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface
{
    /**
     * Return the version of this block.
     *
     * @return int
     */
    public function getVersion() : int;
    /**
     * Return the version of this block.
     *
     * @return bool
     */
    public function hasBip9Prefix() : bool;
    /**
     * Return the previous blocks hash.
     *
     * @return BufferInterface
     */
    public function getPrevBlock() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * Return the merkle root of the transactions in the block.
     *
     * @return BufferInterface
     */
    public function getMerkleRoot() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * Get the timestamp of the block.
     *
     * @return int
     */
    public function getTimestamp() : int;
    /**
     * Return the buffer containing the short representation of the difficulty
     *
     * @return int
     */
    public function getBits() : int;
    /**
     * Return the nonce of the block header.
     *
     * @return int
     */
    public function getNonce() : int;
    /**
     * Return whether this header is equal to the other.
     *
     * @param BlockHeaderInterface $header
     * @return bool
     */
    public function equals(self $header) : bool;
    /**
     * @return BufferInterface
     */
    public function getHash() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
}
