<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key;

/**
 * *********************************************************************
 * Copyright (C) 2012 Matyas Danter
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * ***********************************************************************
 */
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\EcDH\EcDH;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\EcDH\EcDHInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface;
/**
 * This class serves as public - private key exchange for signature verification.
 */
class PrivateKey implements \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface
{
    /**
     * @var GeneratorPoint
     */
    private $generator;
    /**
     * @var \GMP
     */
    private $secretMultiplier;
    /**
     * @var GmpMathInterface
     */
    private $adapter;
    /**
     * @param GmpMathInterface $adapter
     * @param GeneratorPoint $generator
     * @param \GMP $secretMultiplier
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint $generator, \GMP $secretMultiplier)
    {
        $this->adapter = $adapter;
        $this->generator = $generator;
        $this->secretMultiplier = $secretMultiplier;
    }
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\Key\PrivateKeyInterface::getPublicKey()
     */
    public function getPublicKey() : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKey($this->adapter, $this->generator, $this->generator->mul($this->secretMultiplier));
    }
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\Key\PrivateKeyInterface::getPoint()
     */
    public function getPoint() : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint
    {
        return $this->generator;
    }
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\Key\PrivateKeyInterface::getCurve()
     */
    public function getCurve() : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\CurveFpInterface
    {
        return $this->generator->getCurve();
    }
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\Key\PrivateKeyInterface::getSecret()
     */
    public function getSecret() : \GMP
    {
        return $this->secretMultiplier;
    }
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\Key\PrivateKeyInterface::createExchange()
     */
    public function createExchange(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Key\PublicKeyInterface $recipient = null) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\EcDH\EcDHInterface
    {
        $ecdh = new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\EcDH\EcDH($this->adapter);
        $ecdh->setSenderKey($this)->setRecipientKey($recipient);
        return $ecdh;
    }
}
