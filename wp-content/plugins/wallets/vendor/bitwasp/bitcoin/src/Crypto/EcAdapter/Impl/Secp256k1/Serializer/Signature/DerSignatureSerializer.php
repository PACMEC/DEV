<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Serializer\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Adapter\EcAdapter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\Signature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Template;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory;
class DerSignatureSerializer implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface
{
    /**
     * @var EcAdapter
     */
    private $ecAdapter;
    /**
     * @param EcAdapter $ecAdapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Adapter\EcAdapter $ecAdapter)
    {
        $this->ecAdapter = $ecAdapter;
    }
    /**
     * @return EcAdapterInterface
     */
    public function getEcAdapter()
    {
        return $this->ecAdapter;
    }
    /**
     * @param Signature $signature
     * @return BufferInterface
     */
    private function doSerialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\Signature $signature) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $signatureOut = '';
        if (!secp256k1_ecdsa_signature_serialize_der($this->ecAdapter->getContext(), $signatureOut, $signature->getResource())) {
            throw new \RuntimeException('Secp256k1: serialize der failure');
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($signatureOut);
    }
    /**
     * @param SignatureInterface $signature
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface $signature) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        /** @var Signature $signature */
        return $this->doSerialize($signature);
    }
    /**
     * @return Template
     */
    private function getInnerTemplate()
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory())->uint8()->varstring()->uint8()->varstring()->getTemplate();
    }
    /**
     * @return Template
     */
    private function getOuterTemplate()
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory())->uint8()->varstring()->getTemplate();
    }
    /**
     * @param BufferInterface $derSignature
     * @return SignatureInterface
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $derSignature) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface
    {
        $derSignature = (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($derSignature))->getBuffer();
        $binary = $derSignature->getBinary();
        $sig_t = null;
        /** @var resource $sig_t */
        if (!ecdsa_signature_parse_der_lax($this->ecAdapter->getContext(), $sig_t, $binary)) {
            throw new \RuntimeException('Secp256k1: parse der failure');
        }
        // Unfortunately, we need to use the Parser here to get r and s :/
        list(, $inner) = $this->getOuterTemplate()->parse(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($derSignature));
        list(, $r, , $s) = $this->getInnerTemplate()->parse(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($inner));
        /** @var Buffer $r */
        /** @var Buffer $s */
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\Signature($this->ecAdapter, $r->getGmp(), $s->getGmp(), $sig_t);
    }
}
