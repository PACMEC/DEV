<?php

namespace Ethereumico\EthereumWallet\Dependencies\Elliptic\Curve;

use Ethereumico\EthereumWallet\Dependencies\Elliptic\Curve\MontCurve\Point;
use Ethereumico\EthereumWallet\Dependencies\Elliptic\Utils;
use Ethereumico\EthereumWallet\Dependencies\BN\BN;
class MontCurve extends \Ethereumico\EthereumWallet\Dependencies\Elliptic\Curve\BaseCurve
{
    public $a;
    public $b;
    public $i4;
    public $a24;
    function __construct($conf)
    {
        parent::__construct("mont", $conf);
        $this->a = (new \Ethereumico\EthereumWallet\Dependencies\BN\BN($conf["a"], 16))->toRed($this->red);
        $this->b = (new \Ethereumico\EthereumWallet\Dependencies\BN\BN($conf["b"], 16))->toRed($this->red);
        $this->i4 = (new \Ethereumico\EthereumWallet\Dependencies\BN\BN(4))->toRed($this->red)->redInvm();
        $this->a24 = $this->i4->redMul($this->a->redAdd($this->two));
    }
    public function validate($point)
    {
        $x = $point->normalize()->x;
        $x2 = $x->redSqr();
        $rhs = $x2->redMul($x)->redAdd($x2->redMul($this->a))->redAdd($x);
        $y = $rhs->redSqr();
        return $y->redSqr()->cmp($rhs) === 0;
    }
    public function decodePoint($bytes, $enc = \false)
    {
        return $this->point(\Ethereumico\EthereumWallet\Dependencies\Elliptic\Utils::toArray($bytes, $enc), 1);
    }
    public function point($x, $z)
    {
        return new \Ethereumico\EthereumWallet\Dependencies\Elliptic\Curve\MontCurve\Point($this, $x, $z);
    }
    public function pointFromJSON($obj)
    {
        return \Ethereumico\EthereumWallet\Dependencies\Elliptic\Curve\MontCurve\Point::fromJSON($this, $obj);
    }
}
