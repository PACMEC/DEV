<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature\TransactionSignature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature\TransactionSignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class TransactionSignatureSerializer
{
    /**
     * @var DerSignatureSerializerInterface
     */
    private $sigSerializer;
    /**
     * @param DerSignatureSerializerInterface $sigSerializer
     */
    public function __construct(DerSignatureSerializerInterface $sigSerializer)
    {
        $this->sigSerializer = $sigSerializer;
    }
    /**
     * @param TransactionSignatureInterface $txSig
     * @return BufferInterface
     */
    public function serialize(TransactionSignatureInterface $txSig) : BufferInterface
    {
        return new Buffer($this->sigSerializer->serialize($txSig->getSignature())->getBinary() . \pack('C', $txSig->getHashType()));
    }
    /**
     * @param BufferInterface $buffer
     * @return TransactionSignatureInterface
     * @throws \Exception
     */
    public function parse(BufferInterface $buffer) : TransactionSignatureInterface
    {
        $adapter = $this->sigSerializer->getEcAdapter();
        if ($buffer->getSize() < 1) {
            throw new \RuntimeException("Empty signature");
        }
        return new TransactionSignature($adapter, $this->sigSerializer->parse($buffer->slice(0, -1)), (int) $buffer->slice(-1)->getInt());
    }
}
