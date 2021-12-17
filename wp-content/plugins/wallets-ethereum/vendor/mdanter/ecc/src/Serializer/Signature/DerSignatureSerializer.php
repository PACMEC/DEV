<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Signature;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\SignatureInterface;
class DerSignatureSerializer implements \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Signature\DerSignatureSerializerInterface
{
    /**
     * @var Der\Parser
     */
    private $parser;
    /**
     * @var Der\Formatter
     */
    private $formatter;
    public function __construct()
    {
        $this->parser = new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Signature\Der\Parser();
        $this->formatter = new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Signature\Der\Formatter();
    }
    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\SignatureInterface $signature) : string
    {
        return $this->formatter->serialize($signature);
    }
    /**
     * @param string $binary
     * @return SignatureInterface
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse(string $binary) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\SignatureInterface
    {
        return $this->parser->parse($binary);
    }
}
