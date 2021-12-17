<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface;
interface TransactionSignatureInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface
{
    /**
     * @return SignatureInterface
     */
    public function getSignature() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
    /**
     * @return int
     */
    public function getHashType() : int;
    /**
     * @param TransactionSignatureInterface $other
     * @return bool
     */
    public function equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature\TransactionSignatureInterface $other) : bool;
}
