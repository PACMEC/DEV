<?php

/**
 * PKCS#1 Formatted DSA Key Handler
 *
 * PHP version 5
 *
 * Used by File/X509.php
 *
 * Processes keys with the following headers:
 *
 * -----BEGIN DSA PRIVATE KEY-----
 * -----BEGIN DSA PUBLIC KEY-----
 * -----BEGIN DSA PARAMETERS-----
 *
 * Analogous to ssh-keygen's pem format (as specified by -m)
 *
 * Also, technically, PKCS1 decribes RSA but I am not aware of a formal specification for DSA.
 * The DSA private key format seems to have been adapted from the RSA private key format so
 * we're just re-using that as the name.
 *
 * @category  Crypt
 * @package   DSA
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2015 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */
namespace Ethereumico\Epg\Dependencies\phpseclib3\Crypt\DSA\Formats\Keys;

use Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger;
use Ethereumico\Epg\Dependencies\phpseclib3\Crypt\Common\Formats\Keys\PKCS1 as Progenitor;
use Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1;
use Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps;
use Ethereumico\Epg\Dependencies\ParagonIE\ConstantTime\Base64;
/**
 * PKCS#1 Formatted DSA Key Handler
 *
 * @package RSA
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PKCS1 extends \Ethereumico\Epg\Dependencies\phpseclib3\Crypt\Common\Formats\Keys\PKCS1
{
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
        $key = parent::load($key, $password);
        $decoded = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::decodeBER($key);
        if (empty($decoded)) {
            throw new \RuntimeException('Unable to decode BER');
        }
        $key = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::asn1map($decoded[0], \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAParams::MAP);
        if (\is_array($key)) {
            return $key;
        }
        $key = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::asn1map($decoded[0], \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAPrivateKey::MAP);
        if (\is_array($key)) {
            return $key;
        }
        $key = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::asn1map($decoded[0], \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAPublicKey::MAP);
        if (\is_array($key)) {
            return $key;
        }
        throw new \RuntimeException('Unable to perform ASN1 mapping');
    }
    /**
     * Convert DSA parameters to the appropriate format
     *
     * @access public
     * @param \phpseclib3\Math\BigInteger $p
     * @param \phpseclib3\Math\BigInteger $q
     * @param \phpseclib3\Math\BigInteger $g
     * @return string
     */
    public static function saveParameters(\Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $p, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $q, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $g)
    {
        $key = ['p' => $p, 'q' => $q, 'g' => $g];
        $key = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::encodeDER($key, \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAParams::MAP);
        return "-----BEGIN DSA PARAMETERS-----\r\n" . \chunk_split(\Ethereumico\Epg\Dependencies\ParagonIE\ConstantTime\Base64::encode($key), 64) . "-----END DSA PARAMETERS-----\r\n";
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
    public static function savePrivateKey(\Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $p, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $q, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $g, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $y, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $x, $password = '', array $options = [])
    {
        $key = ['version' => 0, 'p' => $p, 'q' => $q, 'g' => $g, 'y' => $y, 'x' => $x];
        $key = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::encodeDER($key, \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAPrivateKey::MAP);
        return self::wrapPrivateKey($key, 'DSA', $password, $options);
    }
    /**
     * Convert a public key to the appropriate format
     *
     * @access public
     * @param \phpseclib3\Math\BigInteger $p
     * @param \phpseclib3\Math\BigInteger $q
     * @param \phpseclib3\Math\BigInteger $g
     * @param \phpseclib3\Math\BigInteger $y
     * @return string
     */
    public static function savePublicKey(\Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $p, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $q, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $g, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $y)
    {
        $key = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::encodeDER($y, \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAPublicKey::MAP);
        return self::wrapPublicKey($key, 'DSA');
    }
}
