<?php

namespace Ethereumico\EthereumWallet\Dependencies\BN;

use Exception;
use Ethereumico\EthereumWallet\Dependencies\BI\BigInteger;
class Red
{
    public $m;
    function __construct($m)
    {
        if (\is_string($m)) {
            $this->m = \Ethereumico\EthereumWallet\Dependencies\BN\Red::primeByName($m);
        } else {
            $this->m = $m;
        }
        if (!$this->m->gtn(1)) {
            throw new \Exception("Modulus must be greater than 1");
        }
    }
    public static function primeByName($name)
    {
        switch ($name) {
            case "k256":
                return new \Ethereumico\EthereumWallet\Dependencies\BN\BN("ffffffff ffffffff ffffffff ffffffff ffffffff ffffffff fffffffe fffffc2f", 16);
            case "p224":
                return new \Ethereumico\EthereumWallet\Dependencies\BN\BN("ffffffff ffffffff ffffffff ffffffff 00000000 00000000 00000001", 16);
            case "p192":
                return new \Ethereumico\EthereumWallet\Dependencies\BN\BN("ffffffff ffffffff ffffffff fffffffe ffffffff ffffffff", 16);
            case "p25519":
                return new \Ethereumico\EthereumWallet\Dependencies\BN\BN("7fffffffffffffff ffffffffffffffff ffffffffffffffff ffffffffffffffed", 16);
            default:
                throw new \Exception("Unknown prime name " . $name);
        }
    }
    public function verify1(\Ethereumico\EthereumWallet\Dependencies\BN\BN $num)
    {
        if (\assert_options(\ASSERT_ACTIVE)) {
            \assert(!$num->negative());
        }
        //,"red works only with positives");
        \assert($num->red);
        //, "red works only with red numbers");
    }
    public function verify2(\Ethereumico\EthereumWallet\Dependencies\BN\BN $a, \Ethereumico\EthereumWallet\Dependencies\BN\BN $b)
    {
        if (\assert_options(\ASSERT_ACTIVE)) {
            \assert(!$a->negative() && !$b->negative());
        }
        //, "red works only with positives");
        \assert($a->red && $a->red == $b->red);
        //, "red works only with red numbers");
    }
    public function imod(\Ethereumico\EthereumWallet\Dependencies\BN\BN &$a)
    {
        return $a->umod($this->m)->_forceRed($this);
    }
    public function neg(\Ethereumico\EthereumWallet\Dependencies\BN\BN $a)
    {
        if ($a->isZero()) {
            return $a->_clone();
        }
        return $this->m->sub($a)->_forceRed($this);
    }
    public function add(\Ethereumico\EthereumWallet\Dependencies\BN\BN $a, \Ethereumico\EthereumWallet\Dependencies\BN\BN $b)
    {
        $this->verify2($a, $b);
        $res = $a->add($b);
        if ($res->cmp($this->m) >= 0) {
            $res->isub($this->m);
        }
        return $res->_forceRed($this);
    }
    public function iadd(\Ethereumico\EthereumWallet\Dependencies\BN\BN &$a, \Ethereumico\EthereumWallet\Dependencies\BN\BN $b)
    {
        $this->verify2($a, $b);
        $a->iadd($b);
        if ($a->cmp($this->m) >= 0) {
            $a->isub($this->m);
        }
        return $a;
    }
    public function sub(\Ethereumico\EthereumWallet\Dependencies\BN\BN $a, \Ethereumico\EthereumWallet\Dependencies\BN\BN $b)
    {
        $this->verify2($a, $b);
        $res = $a->sub($b);
        if ($res->negative()) {
            $res->iadd($this->m);
        }
        return $res->_forceRed($this);
    }
    public function isub(\Ethereumico\EthereumWallet\Dependencies\BN\BN &$a, $b)
    {
        $this->verify2($a, $b);
        $a->isub($b);
        if ($a->negative()) {
            $a->iadd($this->m);
        }
        return $a;
    }
    public function shl(\Ethereumico\EthereumWallet\Dependencies\BN\BN $a, $num)
    {
        $this->verify1($a);
        return $this->imod($a->ushln($num));
    }
    public function imul(\Ethereumico\EthereumWallet\Dependencies\BN\BN &$a, \Ethereumico\EthereumWallet\Dependencies\BN\BN $b)
    {
        $this->verify2($a, $b);
        $res = $a->imul($b);
        return $this->imod($res);
    }
    public function mul(\Ethereumico\EthereumWallet\Dependencies\BN\BN $a, \Ethereumico\EthereumWallet\Dependencies\BN\BN $b)
    {
        $this->verify2($a, $b);
        $res = $a->mul($b);
        return $this->imod($res);
    }
    public function sqr(\Ethereumico\EthereumWallet\Dependencies\BN\BN $a)
    {
        $res = $a->_clone();
        return $this->imul($res, $a);
    }
    public function isqr(\Ethereumico\EthereumWallet\Dependencies\BN\BN &$a)
    {
        return $this->imul($a, $a);
    }
    public function sqrt(\Ethereumico\EthereumWallet\Dependencies\BN\BN $a)
    {
        if ($a->isZero()) {
            return $a->_clone();
        }
        $mod3 = $this->m->andln(3);
        \assert($mod3 % 2 == 1);
        // Fast case
        if ($mod3 == 3) {
            $pow = $this->m->add(new \Ethereumico\EthereumWallet\Dependencies\BN\BN(1))->iushrn(2);
            return $this->pow($a, $pow);
        }
        // Tonelli-Shanks algorithm (Totally unoptimized and slow)
        //
        // Find Q and S, that Q * 2 ^ S = (P - 1)
        $q = $this->m->subn(1);
        $s = 0;
        while (!$q->isZero() && $q->andln(1) == 0) {
            $s++;
            $q->iushrn(1);
        }
        if (\assert_options(\ASSERT_ACTIVE)) {
            \assert(!$q->isZero());
        }
        $one = (new \Ethereumico\EthereumWallet\Dependencies\BN\BN(1))->toRed($this);
        $nOne = $one->redNeg();
        // Find quadratic non-residue
        // NOTE: Max is such because of generalized Riemann hypothesis.
        $lpow = $this->m->subn(1)->iushrn(1);
        $z = $this->m->bitLength();
        $z = (new \Ethereumico\EthereumWallet\Dependencies\BN\BN(2 * $z * $z))->toRed($this);
        while ($this->pow($z, $lpow)->cmp($nOne) != 0) {
            $z->redIAdd($nOne);
        }
        $c = $this->pow($z, $q);
        $r = $this->pow($a, $q->addn(1)->iushrn(1));
        $t = $this->pow($a, $q);
        $m = $s;
        while ($t->cmp($one) != 0) {
            $tmp = $t;
            for ($i = 0; $tmp->cmp($one) != 0; $i++) {
                $tmp = $tmp->redSqr();
            }
            if ($i >= $m) {
                throw new \Exception("Assertion failed");
            }
            if ($m - $i - 1 > 54) {
                $b = $this->pow($c, (new \Ethereumico\EthereumWallet\Dependencies\BN\BN(1))->iushln($m - $i - 1));
            } else {
                $b = clone $c;
                $b->bi = $c->bi->powMod(1 << $m - $i - 1, $this->m->bi);
            }
            $r = $r->redMul($b);
            $c = $b->redSqr();
            $t = $t->redMul($c);
            $m = $i;
        }
        return $r;
    }
    public function invm(\Ethereumico\EthereumWallet\Dependencies\BN\BN &$a)
    {
        $res = $a->invm($this->m);
        return $this->imod($res);
    }
    public function pow(\Ethereumico\EthereumWallet\Dependencies\BN\BN $a, \Ethereumico\EthereumWallet\Dependencies\BN\BN $num)
    {
        $r = clone $a;
        $r->bi = $a->bi->powMod($num->bi, $this->m->bi);
        return $r;
    }
    public function convertTo(\Ethereumico\EthereumWallet\Dependencies\BN\BN $num)
    {
        $r = $num->umod($this->m);
        return $r === $num ? $r->_clone() : $r;
    }
    public function convertFrom(\Ethereumico\EthereumWallet\Dependencies\BN\BN $num)
    {
        $res = $num->_clone();
        $res->red = null;
        return $res;
    }
}
