<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Signature\CompactSignatureSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class CompactSignature extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\Signature implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignatureInterface
{
    /**
     * @var EcAdapter
     */
    private $ecAdapter;
    /**
     * @var int|string
     */
    private $recid;
    /**
     * @var bool
     */
    private $compressed;
    /**
     * @param EcAdapter $adapter
     * @param \GMP $r
     * @param \GMP $s
     * @param int $recid
     * @param bool $compressed
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter $adapter, \GMP $r, \GMP $s, int $recid, bool $compressed)
    {
        $this->ecAdapter = $adapter;
        $this->recid = $recid;
        $this->compressed = $compressed;
        parent::__construct($adapter, $r, $s);
    }
    /**
     * @return Signature
     */
    public function convert() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\Signature
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\Signature($this->ecAdapter, $this->getR(), $this->getS());
    }
    /**
     * @return int
     */
    public function getRecoveryId() : int
    {
        return $this->recid;
    }
    /**
     * @return bool
     */
    public function isCompressed() : bool
    {
        return $this->compressed;
    }
    /**
     * @return int
     */
    public function getFlags() : int
    {
        return $this->getRecoveryId() + 27 + ($this->isCompressed() ? 4 : 0);
    }
    /**
     * @return BufferInterface
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Signature\CompactSignatureSerializer($this->ecAdapter))->serialize($this);
    }
}
