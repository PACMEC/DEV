<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface PublicKeyInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface
{
    /**
     * Length of an uncompressed key
     */
    const LENGTH_UNCOMPRESSED = 65;
    /**
     * Length of a compressed key
     */
    const LENGTH_COMPRESSED = 33;
    /**
     * When key is uncompressed, this is the prefix.
     */
    const KEY_UNCOMPRESSED = "\4";
    /**
     * When y coordinate is even, prepend x coordinate with this byte
     */
    const KEY_COMPRESSED_EVEN = "\2";
    /**
     * When y coordinate is odd, prepend x coordinate with this byte
     */
    const KEY_COMPRESSED_ODD = "\3";
    /**
     * @param PublicKeyInterface $other
     * @return bool
     */
    public function equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface $other) : bool;
    /**
     * @param BufferInterface $msg32
     * @param SignatureInterface $signature
     * @return bool
     */
    public function verify(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $msg32, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface $signature) : bool;
}
