<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface;
class PointNotOnCurveException extends \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception\PointException
{
    public function __construct(\GMP $x, \GMP $y, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface $curve)
    {
        parent::__construct("Curve " . $curve . " does not contain point (" . \gmp_strval($x, 10) . ", " . \gmp_strval($y, 10) . ")");
    }
}
