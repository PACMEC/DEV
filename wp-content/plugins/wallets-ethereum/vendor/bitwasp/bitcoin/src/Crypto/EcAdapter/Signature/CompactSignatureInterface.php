<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature;

interface CompactSignatureInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface
{
    /**
     * @return SignatureInterface
     */
    public function convert();
    /**
     * @return int
     */
    public function getRecoveryId();
    /**
     * @return bool
     */
    public function isCompressed();
}
