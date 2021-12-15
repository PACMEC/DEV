<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Curves;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFp;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveParameters;
class NamedCurveFp extends \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFp
{
    /**
     * @var string
     */
    private $name;
    /**
     * @param string           $name
     * @param CurveParameters  $parameters
     * @param GmpMathInterface $adapter
     */
    public function __construct(string $name, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveParameters $parameters, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter)
    {
        $this->name = $name;
        parent::__construct($parameters, $adapter);
    }
    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
}
