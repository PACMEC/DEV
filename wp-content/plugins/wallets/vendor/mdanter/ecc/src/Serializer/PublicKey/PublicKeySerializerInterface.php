<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
interface PublicKeySerializerInterface
{
    /**
     * @param  PublicKeyInterface $key
     * @return string
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface $key) : string;
    /**
     * @param  string $formattedKey
     * @return PublicKeyInterface
     */
    public function parse(string $formattedKey) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
}
