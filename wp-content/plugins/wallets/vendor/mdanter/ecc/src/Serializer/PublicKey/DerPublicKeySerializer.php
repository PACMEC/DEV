<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point\PointSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\Der\Formatter;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\Der\Parser;
/**
 *
 * @link https://tools.ietf.org/html/rfc5480#page-3
 * @todo: review for full spec, should we support all prefixes here?
 */
class DerPublicKeySerializer implements \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\PublicKeySerializerInterface
{
    const X509_ECDSA_OID = '1.2.840.10045.2.1';
    /**
     *
     * @var GmpMathInterface
     */
    private $adapter;
    /**
     *
     * @var Formatter
     */
    private $formatter;
    /**
     *
     * @var Parser
     */
    private $parser;
    /**
     * @param GmpMathInterface|null $adapter
     * @param PointSerializerInterface|null $pointSerializer
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter = null, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point\PointSerializerInterface $pointSerializer = null)
    {
        $this->adapter = $adapter ?: \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory::getAdapter();
        $this->formatter = new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\Der\Formatter();
        $this->parser = new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PublicKey\Der\Parser($this->adapter, $pointSerializer ?: new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer());
    }
    /**
     *
     * @param  PublicKeyInterface $key
     * @return string
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface $key) : string
    {
        return $this->formatter->format($key);
    }
    /**
     * @param PublicKeyInterface $key
     * @return string
     */
    public function getUncompressedKey(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface $key) : string
    {
        return $this->formatter->encodePoint($key->getPoint());
    }
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Serializer\PublicKey\PublicKeySerializerInterface::parse()
     */
    public function parse(string $string) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface
    {
        return $this->parser->parse($string);
    }
}
