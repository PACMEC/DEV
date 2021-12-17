<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface SignatureSortInterface
{
    /**
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface[] $signatures
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface[] $publicKeys
     * @param BufferInterface $messageHash
     * @return \SplObjectStorage
     */
    public function link(array $signatures, array $publicKeys, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $messageHash) : \SplObjectStorage;
}
