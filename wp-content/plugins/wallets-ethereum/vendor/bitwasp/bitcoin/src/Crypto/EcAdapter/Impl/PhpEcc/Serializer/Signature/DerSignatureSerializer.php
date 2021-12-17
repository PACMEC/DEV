<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\Signature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class DerSignatureSerializer implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface
{
    /**
     * @var EcAdapter
     */
    private $ecAdapter;
    /**
     * @var \BitWasp\Buffertools\Types\VarString
     */
    private $varstring;
    /**
     * @param EcAdapter $adapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter $adapter)
    {
        $this->ecAdapter = $adapter;
        $this->varstring = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::varstring();
    }
    /**
     * @return EcAdapterInterface
     */
    public function getEcAdapter() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface
    {
        return $this->ecAdapter;
    }
    /**
     * @param SignatureInterface $signature
     * @return BufferInterface
     * @throws \Exception
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface $signature) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        // Ensure that the R and S hex's are of even length
        $rBin = \gmp_export($signature->getR(), 1, \GMP_MSW_FIRST | \GMP_BIG_ENDIAN);
        $sBin = \gmp_export($signature->getS(), 1, \GMP_MSW_FIRST | \GMP_BIG_ENDIAN);
        // Pad R and S if their highest bit is flipped, ie,
        // they are negative.
        if ((\ord($rBin[0]) & 0x80) === 0x80) {
            $rBin = "\0{$rBin}";
        }
        if ((\ord($sBin[0]) & 0x80) === 0x80) {
            $sBin = "\0{$sBin}";
        }
        $inner = \sprintf("\2%s%s\2%s%s", \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::numToVarIntBin(\strlen($rBin)), $rBin, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::numToVarIntBin(\strlen($sBin)), $sBin);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\sprintf("0%s%s", \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::numToVarIntBin(\strlen($inner)), $inner));
    }
    /**
     * @param Parser $parser
     * @return SignatureInterface
     * @throws ParserOutOfRange
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface
    {
        $prefix = $parser->readBytes(1);
        if ($prefix->getBinary() != "0") {
            throw new \RuntimeException("invalid signature");
        }
        $inner = $this->varstring->read($parser);
        try {
            $pinner = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($inner);
            $rPref = $pinner->readBytes(1);
            if ($rPref->getBinary() != "\2") {
                throw new \RuntimeException("invalid signature");
            }
            $r = $this->varstring->read($pinner);
            $sPref = $pinner->readBytes(1);
            if ($sPref->getBinary() != "\2") {
                throw new \RuntimeException("invalid signature");
            }
            $s = $this->varstring->read($pinner);
        } catch (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange $e) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange('Failed to extract full signature from parser');
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\Signature($this->ecAdapter, $r->getGmp(), $s->getGmp());
    }
    /**
     * @param BufferInterface $derSignature
     * @return SignatureInterface
     * @throws ParserOutOfRange
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $derSignature) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($derSignature));
    }
}
