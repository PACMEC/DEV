<?php

namespace Ethereumico\EthereumWallet\Dependencies\Composer;

use Ethereumico\EthereumWallet\Dependencies\Composer\Autoload\ClassLoader;
use Ethereumico\EthereumWallet\Dependencies\Composer\Semver\VersionParser;
class InstalledVersions
{
    private static $installed = array('root' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(), 'reference' => 'd55310fe58991c7b8de589fef97e5b33427e9308', 'name' => 'ethereumico/pacmec-wallet'), 'versions' => array('bamarni/composer-bin-plugin' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '9999999-dev'), 'reference' => '9329fb0fbe29e0e1b2db8f4639a193e4f5406225'), 'bitwasp/bech32' => array('pretty_version' => 'v0.0.1', 'version' => '0.0.1.0', 'aliases' => array(), 'reference' => 'e1ea58c848a4ec59d81b697b3dfe9cc99968d0e7'), 'bitwasp/bitcoin' => array('pretty_version' => '1.0.x-dev', 'version' => '1.0.9999999.9999999-dev', 'aliases' => array(), 'reference' => '670063be60655500a327d5d470d6eba0d77c6941'), 'bitwasp/buffertools' => array('pretty_version' => '0.5.x-dev', 'version' => '0.5.9999999.9999999-dev', 'aliases' => array(), 'reference' => '133746d0b514e0016d8479b54aa97475405a9f1f'), 'composer/semver' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '3.x-dev'), 'reference' => '31f3ea725711245195f62e54ffa402d8ef2fdba9'), 'doctrine/instantiator' => array('pretty_version' => '1.5.x-dev', 'version' => '1.5.9999999.9999999-dev', 'aliases' => array(), 'reference' => '6410c4b8352cb64218641457cef64997e6b784fb'), 'ethereumico/pacmec-wallet' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(), 'reference' => 'd55310fe58991c7b8de589fef97e5b33427e9308'), 'fgrosse/phpasn1' => array('pretty_version' => 'v2.3.0', 'version' => '2.3.0.0', 'aliases' => array(), 'reference' => '20299033c35f4300eb656e7e8e88cf52d1d6694e'), 'freemius/wordpress-sdk' => array('pretty_version' => '2.4.2', 'version' => '2.4.2.0', 'aliases' => array(), 'reference' => '84a9be4717effd7697a217e0d931f48ae0d2ecc6'), 'guzzlehttp/guzzle' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '7.4.x-dev'), 'reference' => 'eeac96d112cb0e836749566e82a61126809e8d38'), 'guzzlehttp/promises' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '1.5.x-dev'), 'reference' => 'b2e8301545358f39b94576b34b305b89cee04193'), 'guzzlehttp/psr7' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '2.1.x-dev'), 'reference' => '089edd38f5b8abba6cb01567c2a8aaa47cec4c72'), 'lastguest/murmurhash' => array('pretty_version' => '2.0.0', 'version' => '2.0.0.0', 'aliases' => array(), 'reference' => '4fb7516f67e695e5d7fa129d1bbb925ec0ebe408'), 'mdanter/ecc' => array('pretty_version' => '0.5.x-dev', 'version' => '0.5.9999999.9999999-dev', 'aliases' => array(), 'reference' => 'b95f25cc1bacc83a9f0ccd375900b7cfd343029e'), 'myclabs/deep-copy' => array('pretty_version' => '1.x-dev', 'version' => '1.9999999.9999999.9999999-dev', 'aliases' => array(), 'reference' => '776f831124e9c62e1a2c601ecc52e776d8bb7220', 'replaced' => array(0 => '1.x-dev')), 'olegabr/ethereum-tx' => array('pretty_version' => '0.5.1', 'version' => '0.5.1.0', 'aliases' => array(), 'reference' => '99a0b74ec83b4c05292811d7072ff49248502d17'), 'olegabr/ethereum-util' => array('pretty_version' => '0.1.2', 'version' => '0.1.2.0', 'aliases' => array(), 'reference' => '9231d5c38d259f615938462975ac7f1e32124f4b'), 'olegabr/keccak' => array('pretty_version' => '1.0.6', 'version' => '1.0.6.0', 'aliases' => array(), 'reference' => '31011dfdc4aace3b9786e005105bd41fa17574e4'), 'olegabr/rlp' => array('pretty_version' => '0.3.6', 'version' => '0.3.6.0', 'aliases' => array(), 'reference' => '76cda212de61b8e5d32fc9cf646c355c8d61f2fe'), 'olegabr/web3.php' => array('pretty_version' => '0.2.0', 'version' => '0.2.0.0', 'aliases' => array(), 'reference' => '77db70e7f03930a9da6900074547285c1ce451b4'), 'paragonie/constant_time_encoding' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => 'f34c2b11eb9d2c9318e13540a1dbc2a3afbd939c'), 'paragonie/random_compat' => array('pretty_version' => 'v9.99.100', 'version' => '9.99.100.0', 'aliases' => array(), 'reference' => '996434e5492cb4c3edcb9168db6fbb1359ef965a'), 'phar-io/manifest' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '2.0.x-dev'), 'reference' => '97803eca37d319dfa7826cc2437fc020857acb53'), 'phar-io/version' => array('pretty_version' => '3.1.0', 'version' => '3.1.0.0', 'aliases' => array(), 'reference' => 'bae7c545bef187884426f042434e561ab1ddb182'), 'phpdocumentor/reflection-common' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '2.x-dev'), 'reference' => 'a0eeab580cbdf4414fef6978732510a36ed0a9d6'), 'phpdocumentor/reflection-docblock' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '5.x-dev'), 'reference' => '0005eb9eaecc2a3a00b8ee34c06643a316ebb228'), 'phpdocumentor/type-resolver' => array('pretty_version' => '1.x-dev', 'version' => '1.9999999.9999999.9999999-dev', 'aliases' => array(), 'reference' => 'a12f7e301eb7258bb68acd89d4aefa05c2906cae'), 'phpseclib/phpseclib' => array('pretty_version' => '3.0.x-dev', 'version' => '3.0.9999999.9999999-dev', 'aliases' => array(), 'reference' => '01cdf396b2df000b19b5c9b8fdbfcfb8d9c6c002'), 'phpspec/prophecy' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '1.x-dev'), 'reference' => 'd86dfc2e2a3cd366cee475e52c6bb3bbc371aa0e'), 'phpunit/php-code-coverage' => array('pretty_version' => '7.0.x-dev', 'version' => '7.0.9999999.9999999-dev', 'aliases' => array(), 'reference' => '819f92bba8b001d4363065928088de22f25a3a48'), 'phpunit/php-file-iterator' => array('pretty_version' => '2.0.x-dev', 'version' => '2.0.9999999.9999999-dev', 'aliases' => array(), 'reference' => '575fbe4836a85e3fc7f76bac9d4541a5b2872d49'), 'phpunit/php-text-template' => array('pretty_version' => '1.2.1', 'version' => '1.2.1.0', 'aliases' => array(), 'reference' => '31f8b717e51d9a2afca6c9f046f5d69fc27c8686'), 'phpunit/php-timer' => array('pretty_version' => '2.1.x-dev', 'version' => '2.1.9999999.9999999-dev', 'aliases' => array(), 'reference' => '2454ae1765516d20c4ffe103d85a58a9a3bd5662'), 'phpunit/php-token-stream' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '4.0.x-dev'), 'reference' => '76fc0567751d177847112bd3e26e4890529c98da'), 'phpunit/phpunit' => array('pretty_version' => '8.5.x-dev', 'version' => '8.5.9999999.9999999-dev', 'aliases' => array(), 'reference' => '50a58a60b85947b0bee4c8ecfe0f4bbdcf20e984'), 'pleonasm/merkle-tree' => array('pretty_version' => '1.0.0', 'version' => '1.0.0.0', 'aliases' => array(), 'reference' => '9ddc9d0a0e396750fada378f3aa90f6c02dd56a1'), 'psr/http-client' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '1.0.x-dev'), 'reference' => '22b2ef5687f43679481615605d7a15c557ce85b1'), 'psr/http-client-implementation' => array('provided' => array(0 => '1.0')), 'psr/http-factory' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '1.0.x-dev'), 'reference' => '36fa03d50ff82abcae81860bdaf4ed9a1510c7cd'), 'psr/http-factory-implementation' => array('provided' => array(0 => '1.0')), 'psr/http-message' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '1.0.x-dev'), 'reference' => 'efd67d1dc14a7ef4fc4e518e7dee91c271d524e4'), 'psr/http-message-implementation' => array('provided' => array(0 => '1.0')), 'ralouphie/getallheaders' => array('pretty_version' => '3.0.3', 'version' => '3.0.3.0', 'aliases' => array(), 'reference' => '120b605dfeb996808c31b6477290a714d356e822'), 'sebastian/code-unit-reverse-lookup' => array('pretty_version' => '1.0.x-dev', 'version' => '1.0.9999999.9999999-dev', 'aliases' => array(), 'reference' => '1de8cd5c010cb153fcd68b8d0f64606f523f7619'), 'sebastian/comparator' => array('pretty_version' => '3.0.x-dev', 'version' => '3.0.9999999.9999999-dev', 'aliases' => array(), 'reference' => '1071dfcef776a57013124ff35e1fc41ccd294758'), 'sebastian/diff' => array('pretty_version' => '3.0.x-dev', 'version' => '3.0.9999999.9999999-dev', 'aliases' => array(), 'reference' => '14f72dd46eaf2f2293cbe79c93cc0bc43161a211'), 'sebastian/environment' => array('pretty_version' => '4.2.x-dev', 'version' => '4.2.9999999.9999999-dev', 'aliases' => array(), 'reference' => 'a8cb2aa3eca438e75a4b7895f04bc8f5f990bc49'), 'sebastian/exporter' => array('pretty_version' => '3.1.x-dev', 'version' => '3.1.9999999.9999999-dev', 'aliases' => array(), 'reference' => '6b853149eab67d4da22291d36f5b0631c0fd856e'), 'sebastian/global-state' => array('pretty_version' => '3.0.x-dev', 'version' => '3.0.9999999.9999999-dev', 'aliases' => array(), 'reference' => '474fb9edb7ab891665d3bfc6317f42a0a150454b'), 'sebastian/object-enumerator' => array('pretty_version' => '3.0.x-dev', 'version' => '3.0.9999999.9999999-dev', 'aliases' => array(), 'reference' => 'e67f6d32ebd0c749cf9d1dbd9f226c727043cdf2'), 'sebastian/object-reflector' => array('pretty_version' => '1.1.x-dev', 'version' => '1.1.9999999.9999999-dev', 'aliases' => array(), 'reference' => '9b8772b9cbd456ab45d4a598d2dd1a1bced6363d'), 'sebastian/recursion-context' => array('pretty_version' => '3.0.x-dev', 'version' => '3.0.9999999.9999999-dev', 'aliases' => array(), 'reference' => '367dcba38d6e1977be014dc4b22f47a484dac7fb'), 'sebastian/resource-operations' => array('pretty_version' => '2.0.x-dev', 'version' => '2.0.9999999.9999999-dev', 'aliases' => array(), 'reference' => '31d35ca87926450c44eae7e2611d45a7a65ea8b3'), 'sebastian/type' => array('pretty_version' => '1.1.x-dev', 'version' => '1.1.9999999.9999999-dev', 'aliases' => array(), 'reference' => '0150cfbc4495ed2df3872fb31b26781e4e077eb4'), 'sebastian/version' => array('pretty_version' => '2.0.1', 'version' => '2.0.1.0', 'aliases' => array(), 'reference' => '99732be0ddb3361e16ad77b68ba41efc8e979019'), 'simplito/bigint-wrapper-php' => array('pretty_version' => '1.0.0', 'version' => '1.0.0.0', 'aliases' => array(), 'reference' => 'cf21ec76d33f103add487b3eadbd9f5033a25930'), 'simplito/bn-php' => array('pretty_version' => '1.1.2', 'version' => '1.1.2.0', 'aliases' => array(), 'reference' => 'e852fcd27e4acbc32459606d7606e45a85e42465'), 'simplito/elliptic-php' => array('pretty_version' => '1.0.8', 'version' => '1.0.8.0', 'aliases' => array(), 'reference' => '5cebaf29adce31a398368972a43d13def1e7ba15'), 'symfony/deprecation-contracts' => array('pretty_version' => '2.5.x-dev', 'version' => '2.5.9999999.9999999-dev', 'aliases' => array(), 'reference' => '6f981ee24cf69ee7ce9736146d1c57c2780598a8'), 'symfony/polyfill-ctype' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '1.23.x-dev'), 'reference' => 'f24ae462b1d60c333df104f0b81ec7d0e12f6e9f'), 'symfony/polyfill-mbstring' => array('pretty_version' => 'v1.19.0', 'version' => '1.19.0.0', 'aliases' => array(), 'reference' => 'b5f7b932ee6fa802fc792eabd77c4c88084517ce'), 'theseer/tokenizer' => array('pretty_version' => '1.2.1', 'version' => '1.2.1.0', 'aliases' => array(), 'reference' => '34a41e998c2183e22995f158c581e7b5e755ab9e'), 'webmozart/assert' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '1.10.x-dev'), 'reference' => 'b419d648592b0b8911cbbe10d450fe314f4fd262'), 'woocommerce/action-scheduler' => array('pretty_version' => 'dev-issue-730', 'version' => 'dev-issue-730', 'aliases' => array(), 'reference' => 'b4c897fe2b0f347f2a8f558272e53fe196ba2378')));
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
    public static function satisfies(VersionParser $parser, $packageName, $constraint)
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
            self::$canGetVendors = \method_exists('Ethereumico\\EthereumWallet\\Dependencies\\Composer\\Autoload\\ClassLoader', 'getRegisteredLoaders');
        }
        $installed = array();
        if (self::$canGetVendors) {
            foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
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
