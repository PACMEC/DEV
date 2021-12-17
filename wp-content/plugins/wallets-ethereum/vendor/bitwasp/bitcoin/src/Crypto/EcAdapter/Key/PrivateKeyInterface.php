<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\RbgInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface PrivateKeyInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface
{
    /**
     * Return the decimal secret multiplier
     *
     * @return \GMP
     */
    public function getSecret();
    /**
     * @param BufferInterface $msg32
     * @param RbgInterface $rbg
     * @return SignatureInterface
     */
    public function sign(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $msg32, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\RbgInterface $rbg = null);
    /**
     * @param BufferInterface $msg32
     * @param RbgInterface|null $rbgInterface
     * @return CompactSignature
     */
    public function signCompact(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $msg32, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\RbgInterface $rbgInterface = null);
    /**
     * Return the public key.
     *
     * @return PublicKeyInterface
     */
    public function getPublicKey();
    /**
     * Convert the private key to wallet import format. This function
     * optionally takes a NetworkInterface for exporting keys for other networks.
     *
     * @param NetworkInterface $network
     * @return string
     */
    public function toWif(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null);
}
