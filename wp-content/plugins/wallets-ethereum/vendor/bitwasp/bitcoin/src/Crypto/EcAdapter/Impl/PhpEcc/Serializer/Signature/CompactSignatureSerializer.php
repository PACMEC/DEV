<?php

namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\CompactSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Template;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory;
class CompactSignatureSerializer implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\CompactSignatureSerializerInterface
{
    /**
     * @var EcAdapter
     */
    private $ecAdapter;
    /**
     * @param EcAdapter $adapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter $adapter)
    {
        $this->ecAdapter = $adapter;
    }
    /**
     * @return \BitWasp\Buffertools\Template
     */
    private function getTemplate() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Template
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory())->uint8()->uint256()->uint256()->getTemplate();
    }
    /**
     * @param CompactSignature $signature
     * @return BufferInterface
     */
    private function doSerialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature $signature) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return $this->getTemplate()->write([$signature->getFlags(), \gmp_strval($signature->getR(), 10), \gmp_strval($signature->getS(), 10)]);
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
     * @param Parser $parser
     * @return CompactSignature
     * @throws ParserOutOfRange
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature
    {
        $math = $this->ecAdapter->getMath();
        try {
            list($byte, $r, $s) = $this->getTemplate()->parse($parser);
            $recoveryFlags = $byte - 27;
            if ($recoveryFlags < 0 || $recoveryFlags > 7) {
                throw new \InvalidArgumentException('invalid signature type');
            }
            $isCompressed = $math->cmp($math->bitwiseAnd(\gmp_init($recoveryFlags), \gmp_init(4)), \gmp_init(0)) !== 0;
            $recoveryId = $recoveryFlags - ($isCompressed ? 4 : 0);
        } catch (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange $e) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange('Failed to extract full signature from parser');
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature($this->ecAdapter, \gmp_init($r, 10), \gmp_init($s, 10), $recoveryId, $isCompressed);
    }
    /**
     * @param BufferInterface $string
     * @return CompactSignatureInterface
     * @throws ParserOutOfRange
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $string) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($string));
    }
}
