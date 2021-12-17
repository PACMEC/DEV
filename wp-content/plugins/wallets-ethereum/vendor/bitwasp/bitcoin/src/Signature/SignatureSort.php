<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class SignatureSort implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature\SignatureSortInterface
{
    /**
     * @var EcAdapterInterface
     */
    private $ecAdapter;
    /**
     * SignatureSort constructor.
     * @param EcAdapterInterface $ecAdapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null)
    {
        $this->ecAdapter = $ecAdapter ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter();
    }
    /**
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface[] $signatures
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface[] $publicKeys
     * @param BufferInterface $messageHash
     * @return \SplObjectStorage
     */
    public function link(array $signatures, array $publicKeys, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $messageHash) : \SplObjectStorage
    {
        $sigCount = \count($signatures);
        $storage = new \SplObjectStorage();
        foreach ($signatures as $signature) {
            foreach ($publicKeys as $key) {
                if ($key->verify($messageHash, $signature)) {
                    $storage->attach($key, $signature);
                    if (\count($storage) === $sigCount) {
                        break 2;
                    }
                    break;
                }
            }
        }
        return $storage;
    }
}
