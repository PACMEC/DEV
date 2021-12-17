<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PrivateKey;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
interface PrivateKeySerializerInterface
{
    /**
     *
     * @param  PrivateKeyInterface $key
     * @return string
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface $key) : string;
    /**
     *
     * @param  string $formattedKey
     * @return PrivateKeyInterface
     */
    public function parse(string $formattedKey) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
}
