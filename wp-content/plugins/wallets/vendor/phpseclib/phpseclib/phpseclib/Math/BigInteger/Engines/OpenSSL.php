<?php

/**
 * OpenSSL Modular Exponentiation Engine
 *
 * PHP version 5 and 7
 *
 * @category  Math
 * @package   BigInteger
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2017 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://pear.php.net/package/Math_BigInteger
 */
namespace Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger\Engines;

use Ethereumico\EthereumWallet\Dependencies\phpseclib3\Crypt\RSA;
use Ethereumico\EthereumWallet\Dependencies\phpseclib3\Crypt\RSA\Formats\Keys\PKCS8;
use Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger;
/**
 * OpenSSL Modular Exponentiation Engine
 *
 * @package Engines
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class OpenSSL
{
    /**
     * Test for engine validity
     *
     * @return bool
     */
    public static function isValidEngine()
    {
        return \extension_loaded('openssl') && static::class != __CLASS__;
    }
    /**
     * Performs modular exponentiation.
     *
     * @param Engine $x
     * @param Engine $e
     * @param Engine $n
     * @return Engine
     */
    public static function powModHelper(\Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger\Engines\Engine $x, \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger\Engines\Engine $e, \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger\Engines\Engine $n)
    {
        if ($n->getLengthInBytes() < 31 || $n->getLengthInBytes() > 16384) {
            throw new \OutOfRangeException('Only modulo between 31 and 16384 bits are accepted');
        }
        $key = \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Crypt\RSA\Formats\Keys\PKCS8::savePublicKey(new \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger($n), new \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger($e));
        $plaintext = \str_pad($x->toBytes(), $n->getLengthInBytes(), "\0", \STR_PAD_LEFT);
        // this is easily prone to failure. if the modulo is a multiple of 2 or 3 or whatever it
        // won't work and you'll get a "failure: error:0906D06C:PEM routines:PEM_read_bio:no start line"
        // error. i suppose, for even numbers, we could do what PHP\Montgomery.php does, but then what
        // about odd numbers divisible by 3, by 5, etc?
        if (!\openssl_public_encrypt($plaintext, $result, $key, \OPENSSL_NO_PADDING)) {
            throw new \UnexpectedValueException(\openssl_error_string());
        }
        $class = \get_class($x);
        return new $class($result, 256);
    }
}
