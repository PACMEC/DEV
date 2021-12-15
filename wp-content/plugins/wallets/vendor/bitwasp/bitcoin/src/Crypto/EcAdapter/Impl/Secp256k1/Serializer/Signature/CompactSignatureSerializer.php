<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Serializer\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Adapter\EcAdapter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\CompactSignature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\CompactSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class CompactSignatureSerializer implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\CompactSignatureSerializerInterface
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
     * @param CompactSignature $signature
     * @return BufferInterface
     */
    private function doSerialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\CompactSignature $signature)
    {
        $sig_t = '';
        $recid = 0;
        if (!secp256k1_ecdsa_recoverable_signature_serialize_compact($this->ecAdapter->getContext(), $sig_t, $recid, $signature->getResource())) {
            throw new \RuntimeException('Secp256k1 serialize compact failure');
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\chr($signature->getFlags()) . $sig_t, 65);
    }
    /**
     * @param CompactSignatureInterface $signature
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface $signature) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        /** @var CompactSignature $signature */
        return $this->doSerialize($signature);
    }
    /**
     * @param BufferInterface $buffer
     * @return CompactSignatureInterface
     * @throws \Exception
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface
    {
        if ($buffer->getSize() !== 65) {
            throw new \RuntimeException('Compact Sig must be 65 bytes');
        }
        $byte = (int) $buffer->slice(0, 1)->getInt();
        $sig = $buffer->slice(1, 64);
        $recoveryFlags = $byte - 27;
        if ($recoveryFlags > 7) {
            throw new \RuntimeException('Invalid signature type');
        }
        $isCompressed = ($recoveryFlags & 4) !== 0;
        $recoveryId = $recoveryFlags - ($isCompressed ? 4 : 0);
        $sig_t = null;
        if (!secp256k1_ecdsa_recoverable_signature_parse_compact($this->ecAdapter->getContext(), $sig_t, $sig->getBinary(), $recoveryId)) {
            throw new \RuntimeException('Unable to parse compact signature');
        }
        /** @var resource $sig_t */
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Signature\CompactSignature($this->ecAdapter, $sig_t, $recoveryId, $isCompressed);
    }
}
