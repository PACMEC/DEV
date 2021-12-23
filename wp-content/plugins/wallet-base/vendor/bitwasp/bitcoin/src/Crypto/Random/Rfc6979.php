<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKey as MdPrivateKey;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\RandomGeneratorFactory;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\RandomNumberGeneratorInterface;
class Rfc6979 implements RbgInterface
{
    /**
     * @var EcAdapterInterface
     */
    private $ecAdapter;
    /**
     * @var RandomNumberGeneratorInterface
     */
    private $hmac;
    /**
     * @param EcAdapterInterface $ecAdapter
     * @param PrivateKeyInterface $privateKey
     * @param BufferInterface $messageHash
     * @param string $algo
     */
    public function __construct(EcAdapterInterface $ecAdapter, PrivateKeyInterface $privateKey, BufferInterface $messageHash, string $algo = 'sha256')
    {
        $mdPk = new MdPrivateKey($ecAdapter->getMath(), $ecAdapter->getGenerator(), \gmp_init($privateKey->getInt(), 10));
        $this->ecAdapter = $ecAdapter;
        $this->hmac = RandomGeneratorFactory::getHmacRandomGenerator($mdPk, \gmp_init($messageHash->getInt(), 10), $algo);
    }
    /**
     * @param int $bytes
     * @return BufferInterface
     */
    public function bytes(int $bytes) : BufferInterface
    {
        $integer = $this->hmac->generate($this->ecAdapter->getOrder());
        return Buffer::int(\gmp_strval($integer, 10), $bytes);
    }
}
