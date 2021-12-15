<?php

namespace Ethereumico\Epg\Dependencies\Composer;

use Ethereumico\Epg\Dependencies\Composer\Autoload\ClassLoader;
use Ethereumico\Epg\Dependencies\Composer\Semver\VersionParser;
class InstalledVersions
{
    private static $installed = array('root' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(), 'reference' => '7e9a63575dc785a60a7c6c3ab78dca2c7592d16e', 'name' => 'ethereumico/ether-and-erc20-tokens-woocommerce-payment-gateway'), 'versions' => array('bamarni/composer-bin-plugin' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '9999999-dev'), 'reference' => '9329fb0fbe29e0e1b2db8f4639a193e4f5406225'), 'ethereumico/ether-and-erc20-tokens-woocommerce-payment-gateway' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(), 'reference' => '7e9a63575dc785a60a7c6c3ab78dca2c7592d16e'), 'freemius/wordpress-sdk' => array('pretty_version' => '2.4.2', 'version' => '2.4.2.0', 'aliases' => array(), 'reference' => '84a9be4717effd7697a217e0d931f48ae0d2ecc6'), 'guzzlehttp/guzzle' => array('pretty_version' => '7.4.1', 'version' => '7.4.1.0', 'aliases' => array(), 'reference' => 'ee0a041b1760e6a53d2a39c8c34115adc2af2c79'), 'guzzlehttp/promises' => array('pretty_version' => '1.5.1', 'version' => '1.5.1.0', 'aliases' => array(), 'reference' => 'fe752aedc9fd8fcca3fe7ad05d419d32998a06da'), 'guzzlehttp/psr7' => array('pretty_version' => '2.1.0', 'version' => '2.1.0.0', 'aliases' => array(), 'reference' => '089edd38f5b8abba6cb01567c2a8aaa47cec4c72'), 'olegabr/keccak' => array('pretty_version' => '1.0.6', 'version' => '1.0.6.0', 'aliases' => array(), 'reference' => '31011dfdc4aace3b9786e005105bd41fa17574e4'), 'olegabr/web3.php' => array('pretty_version' => '0.2.0', 'version' => '0.2.0.0', 'aliases' => array(), 'reference' => '77db70e7f03930a9da6900074547285c1ce451b4'), 'paragonie/constant_time_encoding' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => 'f34c2b11eb9d2c9318e13540a1dbc2a3afbd939c'), 'paragonie/random_compat' => array('pretty_version' => 'v9.99.100', 'version' => '9.99.100.0', 'aliases' => array(), 'reference' => '996434e5492cb4c3edcb9168db6fbb1359ef965a'), 'phpseclib/phpseclib' => array('pretty_version' => '3.0.12', 'version' => '3.0.12.0', 'aliases' => array(), 'reference' => '89bfb45bd8b1abc3b37e910d57f5dbd3174f40fb'), 'psr/http-client' => array('pretty_version' => '1.0.1', 'version' => '1.0.1.0', 'aliases' => array(), 'reference' => '2dfb5f6c5eff0e91e20e913f8c5452ed95b86621'), 'psr/http-client-implementation' => array('provided' => array(0 => '1.0')), 'psr/http-factory' => array('pretty_version' => '1.0.1', 'version' => '1.0.1.0', 'aliases' => array(), 'reference' => '12ac7fcd07e5b077433f5f2bee95b3a771bf61be'), 'psr/http-factory-implementation' => array('provided' => array(0 => '1.0')), 'psr/http-message' => array('pretty_version' => '1.0.1', 'version' => '1.0.1.0', 'aliases' => array(), 'reference' => 'f6561bf28d520154e4b0ec72be95418abe6d9363'), 'psr/http-message-implementation' => array('provided' => array(0 => '1.0')), 'ralouphie/getallheaders' => array('pretty_version' => '3.0.3', 'version' => '3.0.3.0', 'aliases' => array(), 'reference' => '120b605dfeb996808c31b6477290a714d356e822'), 'symfony/deprecation-contracts' => array('pretty_version' => 'v2.5.0', 'version' => '2.5.0.0', 'aliases' => array(), 'reference' => '6f981ee24cf69ee7ce9736146d1c57c2780598a8'), 'symfony/polyfill-mbstring' => array('pretty_version' => 'v1.19.0', 'version' => '1.19.0.0', 'aliases' => array(), 'reference' => 'b5f7b932ee6fa802fc792eabd77c4c88084517ce'), 'woocommerce/action-scheduler' => array('pretty_version' => 'dev-issue-730', 'version' => 'dev-issue-730', 'aliases' => array(), 'reference' => 'b4c897fe2b0f347f2a8f558272e53fe196ba2378')));
    private static $canGetVendors;
    private static $installedByVendor = array();
    public static function getInstalledPackages()
    {
        $packages = array();
        foreach (self::getInstalled() as $installed) {
            $packages[] = \array_keys($installed['versions']);
        }
        if (1 === \count($packages)) {
            return $packages[0];
        }
        return \array_keys(\array_flip(\call_user_func_array('array_merge', $packages)));
    }
    public static function isInstalled($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (isset($installed['versions'][$packageName])) {
                return \true;
            }
        }
        return \false;
    }
    public static function satisfies(\Ethereumico\Epg\Dependencies\Composer\Semver\VersionParser $parser, $packageName, $constraint)
    {
        $constraint = $parser->parseConstraints($constraint);
        $provided = $parser->parseConstraints(self::getVersionRanges($packageName));
        return $provided->matches($constraint);
    }
    public static function getVersionRanges($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            $ranges = array();
            if (isset($installed['versions'][$packageName]['pretty_version'])) {
                $ranges[] = $installed['versions'][$packageName]['pretty_version'];
            }
            if (\array_key_exists('aliases', $installed['versions'][$packageName])) {
                $ranges = \array_merge($ranges, $installed['versions'][$packageName]['aliases']);
            }
            if (\array_key_exists('replaced', $installed['versions'][$packageName])) {
                $ranges = \array_merge($ranges, $installed['versions'][$packageName]['replaced']);
            }
            if (\array_key_exists('provided', $installed['versions'][$packageName])) {
                $ranges = \array_merge($ranges, $installed['versions'][$packageName]['provided']);
            }
            return \implode(' || ', $ranges);
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getVersion($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            if (!isset($installed['versions'][$packageName]['version'])) {
                return null;
            }
            return $installed['versions'][$packageName]['version'];
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getPrettyVersion($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            if (!isset($installed['versions'][$packageName]['pretty_version'])) {
                return null;
            }
            return $installed['versions'][$packageName]['pretty_version'];
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getReference($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            if (!isset($installed['versions'][$packageName]['reference'])) {
                return null;
            }
            return $installed['versions'][$packageName]['reference'];
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getRootPackage()
    {
        $installed = self::getInstalled();
        return $installed[0]['root'];
    }
    public static function getRawData()
    {
        return self::$installed;
    }
    public static function reload($data)
    {
        self::$installed = $data;
        self::$installedByVendor = array();
    }
    private static function getInstalled()
    {
        if (null === self::$canGetVendors) {
            self::$canGetVendors = \method_exists('Ethereumico\\Epg\\Dependencies\\Composer\\Autoload\\ClassLoader', 'getRegisteredLoaders');
        }
        $installed = array();
        if (self::$canGetVendors) {
            foreach (\Ethereumico\Epg\Dependencies\Composer\Autoload\ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
                if (isset(self::$installedByVendor[$vendorDir])) {
                    $installed[] = self::$installedByVendor[$vendorDir];
                } elseif (\is_file($vendorDir . '/composer/installed.php')) {
                    $installed[] = self::$installedByVendor[$vendorDir] = (require $vendorDir . '/composer/installed.php');
                }
            }
        }
        $installed[] = self::$installed;
        return $installed;
    }
}
