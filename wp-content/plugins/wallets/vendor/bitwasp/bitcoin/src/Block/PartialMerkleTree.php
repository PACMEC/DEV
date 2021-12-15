<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\PartialMerkleTreeSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools;
class PartialMerkleTree extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable
{
    /**
     * @var int
     */
    private $elementCount;
    /**
     * @var BufferInterface[]
     */
    private $vHashes = [];
    /**
     * @var array
     */
    private $vFlagBits = [];
    /**
     * @var bool
     */
    private $fBad = \false;
    /**
     * Takes array of hashes and flag array only. Use PartialMerkleTree::create() instead of creating instance directly..
     *
     * @param int $txCount
     * @param array $vHashes
     * @param array $vBits
     */
    public function __construct(int $txCount = 0, array $vHashes = [], array $vBits = [])
    {
        $this->elementCount = $txCount;
        $this->vHashes = $vHashes;
        $this->vFlagBits = $vBits;
    }
    /**
     * Construct the Merkle tree
     *
     * @param int $txCount
     * @param array $vTxHashes
     * @param array $vMatch
     * @return PartialMerkleTree
     */
    public static function create(int $txCount, array $vTxHashes, array $vMatch)
    {
        $tree = new self($txCount);
        $tree->traverseAndBuild($tree->calcTreeHeight(), 0, $vTxHashes, $vMatch);
        return $tree;
    }
    /**
     * Calculate tree width for a given height.
     *
     * @param int $height
     * @return int
     */
    public function calcTreeWidth(int $height)
    {
        return $this->elementCount + (1 << $height) - 1 >> $height;
    }
    /**
     * Calculate the tree height.
     *
     * @return int
     */
    public function calcTreeHeight() : int
    {
        $height = 0;
        while ($this->calcTreeWidth($height) > 1) {
            $height++;
        }
        return $height;
    }
    /**
     * @return int
     */
    public function getTxCount() : int
    {
        return $this->elementCount;
    }
    /**
     * @return BufferInterface[]
     */
    public function getHashes() : array
    {
        return $this->vHashes;
    }
    /**
     * @return array
     */
    public function getFlagBits() : array
    {
        return $this->vFlagBits;
    }
    /**
     * Calculate the hash for the given $height and $position
     *
     * @param int $height
     * @param int $position
     * @param \BitWasp\Buffertools\BufferInterface[] $vTxid
     * @return \BitWasp\Buffertools\BufferInterface
     */
    public function calculateHash(int $height, $position, array $vTxid) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if ($height === 0) {
            return $vTxid[$position];
        } else {
            $left = $this->calculateHash($height - 1, $position * 2, $vTxid);
            if ($position * 2 + 1 < $this->calcTreeWidth($height - 1)) {
                $right = $this->calculateHash($height - 1, $position * 2 + 1, $vTxid);
            } else {
                $right = $left;
            }
            return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::concat($left, $right));
        }
    }
    /**
     * Construct the list of Merkle Tree hashes
     *
     * @param int $height
     * @param int $position
     * @param array $vTxid - array of Txid's in the block
     * @param array $vMatch - reference to array to populate
     */
    public function traverseAndBuild(int $height, int $position, array $vTxid, array &$vMatch)
    {
        $parent = \false;
        for ($p = $position << $height; $p < $position + 1 << $height && $p < $this->elementCount; $p++) {
            $parent |= $vMatch[$p];
        }
        $this->vFlagBits[] = $parent;
        if (0 === $height || !$parent) {
            $this->vHashes[] = $this->calculateHash($height, $position, $vTxid);
        } else {
            $this->traverseAndBuild($height - 1, $position * 2, $vTxid, $vMatch);
            if ($position * 2 + 1 < $this->calcTreeWidth($height - 1)) {
                $this->traverseAndBuild($height - 1, $position * 2 + 1, $vTxid, $vMatch);
            }
        }
    }
    /**
     * Traverse the Merkle Tree hashes and extract those which have a matching bit.
     *
     * @param int $height
     * @param int $position
     * @param int $nBitsUsed
     * @param int $nHashUsed
     * @param BufferInterface[] $vMatch
     * @return BufferInterface
     */
    public function traverseAndExtract(int $height, int $position, &$nBitsUsed, &$nHashUsed, &$vMatch) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if ($nBitsUsed >= \count($this->vFlagBits)) {
            $this->fBad = \true;
            return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer();
        }
        $parent = $this->vFlagBits[$nBitsUsed++];
        if (0 === $height || !$parent) {
            if ($nHashUsed >= \count($this->vHashes)) {
                $this->fBad = \true;
                return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer();
            }
            $hash = $this->vHashes[$nHashUsed++];
            if ($height === 0 && $parent) {
                $vMatch[] = $hash->flip();
            }
            return $hash;
        } else {
            $left = $this->traverseAndExtract($height - 1, $position * 2, $nBitsUsed, $nHashUsed, $vMatch);
            if ($position * 2 + 1 < $this->calcTreeWidth($height - 1)) {
                $right = $this->traverseAndExtract($height - 1, $position * 2 + 1, $nBitsUsed, $nHashUsed, $vMatch);
                if ($right === $left) {
                    $this->fBad = \true;
                }
            } else {
                $right = $left;
            }
            return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::concat($left, $right));
        }
    }
    /**
     * Extract matches from the tree into provided $vMatch reference.
     *
     * @param BufferInterface[] $vMatch - reference to array of extracted 'matching' hashes
     * @return BufferInterface - this will be the merkle root
     * @throws \Exception
     */
    public function extractMatches(array &$vMatch) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $nTx = $this->getTxCount();
        if (0 === $nTx) {
            throw new \Exception('ntx = 0');
        }
        if ($nTx > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockInterface::MAX_BLOCK_SIZE / 60) {
            throw new \Exception('ntx > bound size');
        }
        if (\count($this->vHashes) > $nTx) {
            throw new \Exception('nHashes > nTx');
        }
        if (\count($this->vFlagBits) < \count($this->vHashes)) {
            throw new \Exception('nBits < nHashes');
        }
        $height = $this->calcTreeHeight();
        $nBitsUsed = 0;
        $nHashesUsed = 0;
        $merkleRoot = $this->traverseAndExtract($height, 0, $nBitsUsed, $nHashesUsed, $vMatch);
        $merkleRoot = $merkleRoot->flip();
        if ($this->fBad) {
            throw new \Exception('bad data');
        }
        if (\ceil(($nBitsUsed + 7) / 8) !== \ceil((\count($this->vFlagBits) + 7) / 8)) {
            throw new \Exception('Not all bits consumed');
        }
        if ($nHashesUsed !== \count($this->vHashes)) {
            throw new \Exception('Not all hashes consumed');
        }
        return $merkleRoot;
    }
    /**
     * @return BufferInterface
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\PartialMerkleTreeSerializer())->serialize($this);
    }
}
