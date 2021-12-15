<?php

/**
 * OpenSSH Formatted DSA Key Handler
 *
 * PHP version 5
 *
 * Place in $HOME/.ssh/authorized_keys
 *
 * @category  Crypt
 * @package   DSA
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2015 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */
namespace Ethereumico\EthereumWallet\Dependencies\phpseclib3\Crypt\DSA\Formats\Keys;

use Ethereumico\EthereumWallet\Dependencies\ParagonIE\ConstantTime\Base64;
use Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger;
use Ethereumico\EthereumWallet\Dependencies\phpseclib3\Common\Functions\Strings;
use Ethereumico\EthereumWallet\Dependencies\phpseclib3\Crypt\Common\Formats\Keys\OpenSSH as Progenitor;
/**
 * OpenSSH Formatted DSA Key Handler
 *
 * @package DSA
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class OpenSSH extends \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Crypt\Common\Formats\Keys\OpenSSH
{
    /**
     * Supported Key Types
     *
     * @var array
     */
    protected static $types = ['ssh-dss'];
    /**
     * Break a public or private key down into its constituent components
     *
     * @access public
     * @param string $key
     * @param string $password optional
     * @return array
     */
    public static function load($key, $password = '')
    {
        $parsed = parent::load($key, $password);
        if (isset($parsed['paddedKey'])) {
            list($type) = \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Common\Functions\Strings::unpackSSH2('s', $parsed['paddedKey']);
            if ($type != $parsed['type']) {
                throw new \RuntimeException("The public and private keys are not of the same type ({$type} vs {$parsed['type']})");
            }
            list($p, $q, $g, $y, $x, $comment) = \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Common\Functions\Strings::unpackSSH2('i5s', $parsed['paddedKey']);
            return \compact('p', 'q', 'g', 'y', 'x', 'comment');
        }
        list($p, $q, $g, $y) = \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Common\Functions\Strings::unpackSSH2('iiii', $parsed['publicKey']);
        $comment = $parsed['comment'];
        return \compact('p', 'q', 'g', 'y', 'comment');
    }
    /**
     * Convert a public key to the appropriate format
     *
     * @access public
     * @param \phpseclib3\Math\BigInteger $p
     * @param \phpseclib3\Math\BigInteger $q
     * @param \phpseclib3\Math\BigInteger $g
     * @param \phpseclib3\Math\BigInteger $y
     * @param array $options optional
     * @return string
     */
    public static function savePublicKey(\Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger $p, \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger $q, \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger $g, \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger $y, array $options = [])
    {
        if ($q->getLength() != 160) {
            throw new \InvalidArgumentException('SSH only supports keys with an N (length of Group Order q) of 160');
        }
        // from <http://tools.ietf.org/html/rfc4253#page-15>:
        // string    "ssh-dss"
        // mpint     p
        // mpint     q
        // mpint     g
        // mpint     y
        $DSAPublicKey = \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Common\Functions\Strings::packSSH2('siiii', 'ssh-dss', $p, $q, $g, $y);
        if (isset($options['binary']) ? $options['binary'] : self::$binary) {
            return $DSAPublicKey;
        }
        $comment = isset($options['comment']) ? $options['comment'] : self::$comment;
        $DSAPublicKey = 'ssh-dss ' . \base64_encode($DSAPublicKey) . ' ' . $comment;
        return $DSAPublicKey;
    }
    /**
     * Convert a private key to the appropriate format.
     *
     * @access public
     * @param \phpseclib3\Math\BigInteger $p
     * @param \phpseclib3\Math\BigInteger $q
     * @param \phpseclib3\Math\BigInteger $g
     * @param \phpseclib3\Math\BigInteger $y
     * @param \phpseclib3\Math\BigInteger $x
     * @param string $password optional
     * @param array $options optional
     * @return string
     */
    public static function savePrivateKey(\Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger $p, \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger $q, \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger $g, \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger $y, \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger $x, $password = '', array $options = [])
    {
        $publicKey = self::savePublicKey($p, $q, $g, $y, ['binary' => \true]);
        $privateKey = \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Common\Functions\Strings::packSSH2('si5', 'ssh-dss', $p, $q, $g, $y, $x);
        return self::wrapPrivateKey($publicKey, $privateKey, $password, $options);
    }
}
