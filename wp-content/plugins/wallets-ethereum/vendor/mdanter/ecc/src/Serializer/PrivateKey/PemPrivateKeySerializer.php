<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PrivateKey;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
/**
 * PEM Private key formatter
 *
 * @link https://tools.ietf.org/html/rfc5915
 */
class PemPrivateKeySerializer implements \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PrivateKey\PrivateKeySerializerInterface
{
    /**
     * @var DerPrivateKeySerializer
     */
    private $derSerializer;
    /**
     * @param DerPrivateKeySerializer $derSerializer
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer $derSerializer)
    {
        $this->derSerializer = $derSerializer;
    }
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Serializer\PrivateKey\PrivateKeySerializerInterface::serialize()
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface $key) : string
    {
        $privateKeyInfo = $this->derSerializer->serialize($key);
        $content = '-----BEGIN EC PRIVATE KEY-----' . \PHP_EOL;
        $content .= \trim(\chunk_split(\base64_encode($privateKeyInfo), 64, \PHP_EOL)) . \PHP_EOL;
        $content .= '-----END EC PRIVATE KEY-----';
        return $content;
    }
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Serializer\PrivateKey\PrivateKeySerializerInterface::parse()
     */
    public function parse(string $formattedKey) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface
    {
        $formattedKey = \str_replace('-----BEGIN EC PRIVATE KEY-----', '', $formattedKey);
        $formattedKey = \str_replace('-----END EC PRIVATE KEY-----', '', $formattedKey);
        $data = \base64_decode($formattedKey);
        return $this->derSerializer->parse($data);
    }
}
