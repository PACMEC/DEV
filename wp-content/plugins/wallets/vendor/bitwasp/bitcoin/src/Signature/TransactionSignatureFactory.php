<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class TransactionSignatureFactory
{
    /**
     * @param string $string
     * @param EcAdapterInterface|null $ecAdapter
     * @return TransactionSignatureInterface
     * @throws \Exception
     */
    public static function fromHex(string $string, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature\TransactionSignatureInterface
    {
        return self::fromBuffer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex($string), $ecAdapter);
    }
    /**
     * @param BufferInterface $buffer
     * @param EcAdapterInterface|null $ecAdapter
     * @return TransactionSignatureInterface
     * @throws \Exception
     */
    public static function fromBuffer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature\TransactionSignatureInterface
    {
        $serializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface::class, \true, $ecAdapter));
        return $serializer->parse($buffer);
    }
}