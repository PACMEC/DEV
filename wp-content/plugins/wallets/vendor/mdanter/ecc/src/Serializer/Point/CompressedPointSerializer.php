<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Util\CurveOidMapper;
class CompressedPointSerializer implements \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point\PointSerializerInterface
{
    /**
     * @var GmpMathInterface
     */
    private $adapter;
    /**
     * @var \Mdanter\Ecc\Math\NumberTheory
     */
    private $theory;
    /**
     * CompressedPointSerializer constructor.
     * @param GmpMathInterface $adapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->theory = $adapter->getNumberTheory();
    }
    /**
     * @param PointInterface $point
     * @return string
     */
    public function getPrefix(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface $point) : string
    {
        if ($this->adapter->equals($this->adapter->mod($point->getY(), \gmp_init(2, 10)), \gmp_init(0))) {
            return '02';
        } else {
            return '03';
        }
    }
    /**
     * @param PointInterface $point
     * @return string
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface $point) : string
    {
        $length = \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Util\CurveOidMapper::getByteSize($point->getCurve()) * 2;
        $hexString = $this->getPrefix($point);
        $hexString .= \str_pad(\gmp_strval($point->getX(), 16), $length, '0', \STR_PAD_LEFT);
        return $hexString;
    }
    /**
     * @param CurveFpInterface $curve
     * @param string $data - hex serialized compressed point
     * @return PointInterface
     */
    public function unserialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface $curve, string $data) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface
    {
        $prefix = \substr($data, 0, 2);
        if ($prefix !== '03' && $prefix !== '02') {
            throw new \InvalidArgumentException('Invalid data: only compressed keys are supported.');
        }
        $x = \gmp_init(\substr($data, 2), 16);
        $y = $curve->recoverYfromX($prefix === '03', $x);
        return $curve->getPoint($x, $y);
    }
}
