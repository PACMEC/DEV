<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\MessageSigner;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\CompactSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\MessageSigner\SignedMessageSerializer;
class SignedMessage
{
    /**
     * @var string
     */
    private $message;
    /**
     * @var CompactSignatureInterface
     */
    private $compactSignature;
    /**
     * @param string $message
     * @param CompactSignatureInterface $signature
     */
    public function __construct(string $message, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface $signature)
    {
        $this->message = $message;
        $this->compactSignature = $signature;
    }
    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }
    /**
     * @return CompactSignatureInterface
     */
    public function getCompactSignature() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface
    {
        return $this->compactSignature;
    }
    /**
     * @return \BitWasp\Buffertools\BufferInterface
     */
    public function getBuffer()
    {
        $serializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\MessageSigner\SignedMessageSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\CompactSignatureSerializerInterface::class));
        return $serializer->serialize($this);
    }
}
