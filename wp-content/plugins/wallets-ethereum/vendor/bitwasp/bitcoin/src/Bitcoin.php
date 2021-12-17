<?php

namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Chain\Params;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Chain\ParamsInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\EccFactory;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint;
class Bitcoin
{
    /**
     * @var NetworkInterface
     */
    private static $network;
    /**
     * @var EcAdapterInterface
     */
    private static $adapter;
    /**
     * @var ParamsInterface
     */
    private static $params;
    /**
     * @return Math
     */
    public static function getMath()
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math();
    }
    /**
     * Load the generator to be used throughout
     */
    public static function getGenerator()
    {
        return \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\EccFactory::getSecgCurves(self::getMath())->generator256k1();
    }
    /**
     * @param Math $math
     * @param GeneratorPoint $generator
     * @return EcAdapterInterface
     */
    public static function getEcAdapter(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math $math = null, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint $generator = null)
    {
        if (null === self::$adapter) {
            self::$adapter = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory::getAdapter($math ?: self::getMath(), $generator ?: self::getGenerator());
        }
        return self::$adapter;
    }
    /**
     * @param ParamsInterface $params
     */
    public static function setParams(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Chain\ParamsInterface $params)
    {
        self::$params = $params;
    }
    /**
     * @return ParamsInterface
     */
    public static function getParams()
    {
        if (null === self::$params) {
            self::$params = self::getDefaultParams();
        }
        return self::$params;
    }
    /**
     * @param Math|null $math
     * @return ParamsInterface
     */
    public static function getDefaultParams(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math $math = null)
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Chain\Params($math ?: self::getMath());
    }
    /**
     * @param EcAdapterInterface $adapter
     */
    public static function setAdapter(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $adapter)
    {
        self::$adapter = $adapter;
    }
    /**
     * @param NetworkInterface $network
     */
    public static function setNetwork(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network)
    {
        self::$network = $network;
    }
    /**
     * @return NetworkInterface
     */
    public static function getNetwork()
    {
        if (null === self::$network) {
            self::$network = self::getDefaultNetwork();
        }
        return self::$network;
    }
    /**
     * @return NetworkInterface
     */
    public static function getDefaultNetwork()
    {
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkFactory::bitcoin();
    }
}
