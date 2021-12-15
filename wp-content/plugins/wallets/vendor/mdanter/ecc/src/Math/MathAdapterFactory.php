<?php

namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math;

class MathAdapterFactory
{
    /**
     * @var GmpMathInterface
     */
    private static $forcedAdapter = null;
    /**
     * @param GmpMathInterface $adapter
     */
    public static function forceAdapter(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter = null)
    {
        self::$forcedAdapter = $adapter;
    }
    /**
     * @param bool $debug
     * @return DebugDecorator|GmpMathInterface|null
     */
    public static function getAdapter(bool $debug = \false) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface
    {
        if (self::$forcedAdapter !== null) {
            return self::$forcedAdapter;
        }
        $adapter = new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMath();
        return self::wrapAdapter($adapter, $debug);
    }
    /**
     * @param GmpMathInterface $adapter
     * @param bool $debug
     * @return DebugDecorator|GmpMathInterface
     */
    private static function wrapAdapter(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface $adapter, bool $debug) : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\GmpMathInterface
    {
        if ($debug === \true) {
            return new \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Math\DebugDecorator($adapter);
        }
        return $adapter;
    }
}
