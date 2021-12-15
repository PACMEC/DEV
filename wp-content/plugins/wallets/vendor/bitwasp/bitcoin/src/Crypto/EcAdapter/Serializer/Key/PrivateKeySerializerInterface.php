<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface PrivateKeySerializerInterface
{
    /**
     * @param PrivateKeyInterface $privateKey
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface $privateKey) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * @param BufferInterface $data
     * @param bool $compressed
     * @return PrivateKeyInterface
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data, bool $compressed) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
}
