<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKey;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKey;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\RandomGeneratorFactory;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\RandomNumberGeneratorInterface;
/**
 * Curve point from which public and private keys can be derived.
 */
class GeneratorPoint extends \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\Point
{
    /**
     * @var RandomNumberGeneratorInterface
     */
    private $generator;
    /**
     * @param GmpMathInterface               $adapter
     * @param CurveFpInterface               $curve
     * @param \GMP                           $x
     * @param \GMP                           $y
     * @param \GMP                           $order
     * @param RandomNumberGeneratorInterface $generator
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface $curve, \GMP $x, \GMP $y, \GMP $order, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\RandomNumberGeneratorInterface $generator = null)
    {
        $this->generator = $generator ?: \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Random\RandomGeneratorFactory::getRandomGenerator();
        parent::__construct($adapter, $curve, $x, $y, $order);
    }
    /**
     * Verifies validity of given coordinates against the current point and its point.
     *
     * @todo   Check if really necessary here (only used for testing in lib)
     * @param  \GMP $x
     * @param  \GMP $y
     * @return bool
     */
    public function isValid(\GMP $x, \GMP $y) : bool
    {
        $math = $this->getAdapter();
        $n = $this->getOrder();
        $zero = \gmp_init(0, 10);
        $curve = $this->getCurve();
        if ($math->cmp($x, $zero) < 0 || $math->cmp($n, $x) <= 0 || $math->cmp($y, $zero) < 0 || $math->cmp($n, $y) <= 0) {
            return \false;
        }
        if (!$curve->contains($x, $y)) {
            return \false;
        }
        $point = $curve->getPoint($x, $y)->mul($n);
        if (!$point->isInfinity()) {
            return \false;
        }
        return \true;
    }
    /**
     * @return PrivateKeyInterface
     */
    public function createPrivateKey() : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface
    {
        $secret = $this->generator->generate($this->getOrder());
        return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKey($this->getAdapter(), $this, $secret);
    }
    /**
     * @param \GMP $x
     * @param \GMP $y
     * @return PublicKeyInterface
     */
    public function getPublicKeyFrom(\GMP $x, \GMP $y) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface
    {
        $pubPoint = $this->getCurve()->getPoint($x, $y, $this->getOrder());
        return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKey($this->getAdapter(), $this, $pubPoint);
    }
    /**
     * @param \GMP $secretMultiplier
     * @return PrivateKeyInterface
     */
    public function getPrivateKeyFrom(\GMP $secretMultiplier) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKey($this->getAdapter(), $this, $secretMultiplier);
    }
}
