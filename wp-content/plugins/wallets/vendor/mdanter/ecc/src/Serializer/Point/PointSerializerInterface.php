<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Point;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface;
interface PointSerializerInterface
{
    /**
     *
     * @param  PointInterface $point
     * @return string
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface $point) : string;
    /**
     * @param  CurveFpInterface $curve  Curve that contains the serialized point
     * @param  string           $string
     * @return PointInterface
     */
    public function unserialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface $curve, string $string) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface;
}
