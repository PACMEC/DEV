<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory;
class RandomGeneratorFactory
{
    /**
     * @param bool $debug
     * @return DebugDecorator|RandomNumberGeneratorInterface
     */
    public static function getRandomGenerator(bool $debug = \false) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\RandomNumberGeneratorInterface
    {
        return self::wrapAdapter(new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\RandomNumberGenerator(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory::getAdapter($debug)), 'random_bytes', $debug);
    }
    /**
     * @param PrivateKeyInterface $privateKey
     * @param \GMP                $messageHash
     * @param string              $algorithm
     * @param bool                $debug
     * @return DebugDecorator|RandomNumberGeneratorInterface
     */
    public static function getHmacRandomGenerator(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface $privateKey, \GMP $messageHash, string $algorithm, bool $debug = \false) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\RandomNumberGeneratorInterface
    {
        return self::wrapAdapter(new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\HmacRandomNumberGenerator(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory::getAdapter($debug), $privateKey, $messageHash, $algorithm), 'rfc6979', $debug);
    }
    /**
     * @param RandomNumberGeneratorInterface $generator
     * @param string                         $name
     * @param bool                           $debug
     * @return DebugDecorator|RandomNumberGeneratorInterface
     */
    private static function wrapAdapter(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\RandomNumberGeneratorInterface $generator, string $name, bool $debug = \false) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\RandomNumberGeneratorInterface
    {
        if ($debug === \true) {
            return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\DebugDecorator($generator, $name);
        }
        return $generator;
    }
}
