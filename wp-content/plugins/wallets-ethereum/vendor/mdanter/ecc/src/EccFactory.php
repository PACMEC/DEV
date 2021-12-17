<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\Signer;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFp;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveParameters;
/**
 * Static factory class providing factory methods to work with NIST and SECG recommended curves.
 */
class EccFactory
{
    /**
     * Selects and creates the most appropriate adapter for the running environment.
     *
     * @param bool $debug [optional] Set to true to get a trace of all mathematical operations
     *
     * @throws \RuntimeException
     * @return GmpMathInterface
     */
    public static function getAdapter(bool $debug = \false) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface
    {
        return \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory::getAdapter($debug);
    }
    /**
     * Returns a factory to create NIST Recommended curves and generators.
     *
     * @param  GmpMathInterface $adapter [optional] Defaults to the return value of EccFactory::getAdapter().
     * @return NistCurve
     */
    public static function getNistCurves(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter = null) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve
    {
        return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve($adapter ?: self::getAdapter());
    }
    /**
     * Returns a factory to return SECG Recommended curves and generators.
     *
     * @param  GmpMathInterface $adapter [optional] Defaults to the return value of EccFactory::getAdapter().
     * @return SecgCurve
     */
    public static function getSecgCurves(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter = null) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve
    {
        return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve($adapter ?: self::getAdapter());
    }
    /**
     * Creates a new curve from arbitrary parameters.
     *
     * @param  int              $bitSize
     * @param  \GMP             $prime
     * @param  \GMP             $a
     * @param  \GMP             $b
     * @param  GmpMathInterface $adapter [optional] Defaults to the return value of EccFactory::getAdapter().
     * @return CurveFpInterface
     */
    public static function createCurve(int $bitSize, \GMP $prime, \GMP $a, \GMP $b, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter = null) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFp(new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveParameters($bitSize, $prime, $a, $b), $adapter ?: self::getAdapter());
    }
    /**
     * @param  GmpMathInterface $adapter [optional] Defaults to the return value of EccFactory::getAdapteR()
     * @return Signer
     */
    public static function getSigner(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter = null) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\Signer
    {
        return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\Signer($adapter ?: self::getAdapter());
    }
}
