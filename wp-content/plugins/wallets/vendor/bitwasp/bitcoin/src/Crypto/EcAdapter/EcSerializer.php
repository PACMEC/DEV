<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PrivateKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\CompactSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface;
class EcSerializer
{
    const PATH_PHPECC = '\\Ethereumico\\EthereumWallet\\Dependencies\\' . 'BitWasp\\Bitcoin\\Crypto\\EcAdapter\\Impl\\PhpEcc\\';
    const PATH_SECP256K1 = '\\Ethereumico\\EthereumWallet\\Dependencies\\' . 'BitWasp\\Bitcoin\\Crypto\\EcAdapter\\Impl\\Secp256k1\\';
    /**
     * @var string[]
     */
    private static $serializerInterface = [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PrivateKeySerializerInterface::class, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface::class, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\CompactSignatureSerializerInterface::class, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface::class];
    /**
     * @var string[]
     */
    private static $serializerImpl = ['Serializer\\Key\\PrivateKeySerializer', 'Serializer\\Key\\PublicKeySerializer', 'Serializer\\Signature\\CompactSignatureSerializer', 'Serializer\\Signature\\DerSignatureSerializer'];
    /**
     * @var array
     */
    private static $map = [];
    /**
     * @var bool
     */
    private static $useCache = \true;
    /**
     * @var array
     */
    private static $cache = [];
    /**
     * @param string $interface
     * @return string
     */
    public static function getImplRelPath(string $interface) : string
    {
        if (0 === \count(self::$map)) {
            if (!\in_array($interface, self::$serializerInterface, \true)) {
                throw new \InvalidArgumentException('Interface not known');
            }
            $cInterface = \count(self::$serializerInterface);
            if ($cInterface !== \count(self::$serializerImpl)) {
                throw new \InvalidArgumentException('Invalid serializer interface map');
            }
            for ($i = 0; $i < $cInterface; $i++) {
                /** @var string $iface */
                $iface = self::$serializerInterface[$i];
                $ipath = self::$serializerImpl[$i];
                self::$map[$iface] = $ipath;
            }
        }
        return self::$map[$interface];
    }
    /**
     * @return array
     */
    public static function getImplPaths() : array
    {
        return ['Ethereumico\\EthereumWallet\\Dependencies\\BitWasp\\Bitcoin\\Crypto\\EcAdapter\\Impl\\PhpEcc\\Adapter\\EcAdapter' => '\\Ethereumico\\EthereumWallet\\Dependencies\\' . 'BitWasp\\Bitcoin\\Crypto\\EcAdapter\\Impl\\PhpEcc\\', 'Ethereumico\\EthereumWallet\\Dependencies\\BitWasp\\Bitcoin\\Crypto\\EcAdapter\\Impl\\Secp256k1\\Adapter\\EcAdapter' => '\\Ethereumico\\EthereumWallet\\Dependencies\\' . 'BitWasp\\Bitcoin\\Crypto\\EcAdapter\\Impl\\Secp256k1\\'];
    }
    /**
     * @param EcAdapterInterface $adapter
     * @return string
     */
    public static function getAdapterImplPath(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $adapter) : string
    {
        $paths = static::getImplPaths();
        $class = \get_class($adapter);
        if (!isset($paths[$class])) {
            throw new \RuntimeException('Unknown EcAdapter');
        }
        return $paths[$class];
    }
    /**
     * @param string $interface
     * @param bool $useCache
     * @param EcAdapterInterface $adapter
     * @return mixed
     */
    public static function getSerializer(string $interface, $useCache = \true, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $adapter = null)
    {
        if (null === $adapter) {
            $adapter = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter();
        }
        $key = \get_class($adapter) . ":" . $interface;
        if (\array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }
        $classPath = self::getAdapterImplPath($adapter) . self::getImplRelPath($interface);
        $class = new $classPath($adapter);
        if ($useCache && self::$useCache) {
            self::$cache[$key] = $class;
        }
        return $class;
    }
    /**
     * Disables caching of serializers
     */
    public static function disableCache()
    {
        self::$useCache = \false;
        self::$cache = [];
    }
}
