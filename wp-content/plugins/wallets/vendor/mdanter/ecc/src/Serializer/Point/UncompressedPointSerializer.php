<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Util\BinaryString;
class UncompressedPointSerializer implements \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point\PointSerializerInterface
{
    /**
     * @param PointInterface $point
     * @return string
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface $point) : string
    {
        $length = \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Util\CurveOidMapper::getByteSize($point->getCurve()) * 2;
        $hexString = '04';
        $hexString .= \str_pad(\gmp_strval($point->getX(), 16), $length, '0', \STR_PAD_LEFT);
        $hexString .= \str_pad(\gmp_strval($point->getY(), 16), $length, '0', \STR_PAD_LEFT);
        return $hexString;
    }
    /**
     * @param CurveFpInterface $curve
     * @param string           $data
     * @return PointInterface
     */
    public function unserialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface $curve, string $data) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface
    {
        if (\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Util\BinaryString::substring($data, 0, 2) != '04') {
            throw new \InvalidArgumentException('Invalid data: only uncompressed keys are supported.');
        }
        $data = \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Util\BinaryString::substring($data, 2);
        $dataLength = \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Util\BinaryString::length($data);
        $x = \gmp_init(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Util\BinaryString::substring($data, 0, $dataLength / 2), 16);
        $y = \gmp_init(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Util\BinaryString::substring($data, $dataLength / 2), 16);
        return $curve->getPoint($x, $y);
    }
}
