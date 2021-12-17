<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception\UnknownCurveException;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception\UnsupportedCurveException;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint;
class CurveFactory
{
    /**
     * @param string $name
     * @return NamedCurveFp
     */
    public static function getCurveByName(string $name) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NamedCurveFp
    {
        $adapter = \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory::getAdapter();
        $nistFactory = self::getNistFactory($adapter);
        $secpFactory = self::getSecpFactory($adapter);
        switch ($name) {
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve::NAME_P192:
                return $nistFactory->curve192();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve::NAME_P224:
                return $nistFactory->curve224();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve::NAME_P256:
                return $nistFactory->curve256();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve::NAME_P384:
                return $nistFactory->curve384();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve::NAME_P521:
                return $nistFactory->curve521();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve::NAME_SECP_112R1:
                return $secpFactory->curve112r1();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve::NAME_SECP_192K1:
                return $secpFactory->curve192k1();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve::NAME_SECP_256K1:
                return $secpFactory->curve256k1();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve::NAME_SECP_256R1:
                return $secpFactory->curve256r1();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve::NAME_SECP_384R1:
                return $secpFactory->curve384r1();
            default:
                $error = new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception\UnsupportedCurveException('Unknown curve.');
                $error->setCurveName($name);
                throw $error;
        }
    }
    /**
     * @param string $name
     * @return GeneratorPoint
     */
    public static function getGeneratorByName(string $name) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint
    {
        $adapter = \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\MathAdapterFactory::getAdapter();
        $nistFactory = self::getNistFactory($adapter);
        $secpFactory = self::getSecpFactory($adapter);
        switch ($name) {
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve::NAME_P192:
                return $nistFactory->generator192();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve::NAME_P224:
                return $nistFactory->generator224();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve::NAME_P256:
                return $nistFactory->generator256();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve::NAME_P384:
                return $nistFactory->generator384();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve::NAME_P521:
                return $nistFactory->generator521();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve::NAME_SECP_112R1:
                return $secpFactory->generator112r1();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve::NAME_SECP_192K1:
                return $secpFactory->generator192k1();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve::NAME_SECP_256K1:
                return $secpFactory->generator256k1();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve::NAME_SECP_256R1:
                return $secpFactory->generator256r1();
            case \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve::NAME_SECP_384R1:
                return $secpFactory->generator384r1();
            default:
                $error = new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception\UnsupportedCurveException('Unknown generator.');
                $error->setCurveName($name);
                throw $error;
        }
    }
    /**
     * @param GmpMathInterface $math
     * @return NistCurve
     */
    private static function getNistFactory(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $math) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve
    {
        return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\NistCurve($math);
    }
    /**
     * @param GmpMathInterface $math
     * @return SecgCurve
     */
    private static function getSecpFactory(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $math) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve
    {
        return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves\SecgCurve($math);
    }
}
