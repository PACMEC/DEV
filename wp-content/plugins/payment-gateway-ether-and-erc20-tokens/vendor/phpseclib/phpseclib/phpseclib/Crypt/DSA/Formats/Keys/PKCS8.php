<?php

/**
 * PKCS#8 Formatted DSA Key Handler
 *
 * PHP version 5
 *
 * Processes keys with the following headers:
 *
 * -----BEGIN ENCRYPTED PRIVATE KEY-----
 * -----BEGIN PRIVATE KEY-----
 * -----BEGIN PUBLIC KEY-----
 *
 * Analogous to ssh-keygen's pkcs8 format (as specified by -m). Although PKCS8
 * is specific to private keys it's basically creating a DER-encoded wrapper
 * for keys. This just extends that same concept to public keys (much like ssh-keygen)
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
use Ethereumico\Epg\Dependencies\phpseclib3\Crypt\Common\Formats\Keys\PKCS8 as Progenitor;
use Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1;
use Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps;
use Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings;
/**
 * PKCS#8 Formatted DSA Key Handler
 *
 * @package DSA
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PKCS8 extends \Ethereumico\Epg\Dependencies\phpseclib3\Crypt\Common\Formats\Keys\PKCS8
{
    /**
     * OID Name
     *
     * @var string
     * @access private
     */
    const OID_NAME = 'id-dsa';
    /**
     * OID Value
     *
     * @var string
     * @access private
     */
    const OID_VALUE = '1.2.840.10040.4.1';
    /**
     * Child OIDs loaded
     *
     * @var bool
     * @access private
     */
    protected static $childOIDsLoaded = \false;
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
        if (!\Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings::is_stringable($key)) {
            throw new \UnexpectedValueException('Key should be a string - not a ' . \gettype($key));
        }
        $isPublic = \strpos($key, 'PUBLIC') !== \false;
        $key = parent::load($key, $password);
        $type = isset($key['privateKey']) ? 'privateKey' : 'publicKey';
        switch (\true) {
            case !$isPublic && $type == 'publicKey':
                throw new \UnexpectedValueException('Human readable string claims non-public key but DER encoded string claims public key');
            case $isPublic && $type == 'privateKey':
                throw new \UnexpectedValueException('Human readable string claims public key but DER encoded string claims private key');
        }
        $decoded = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::decodeBER($key[$type . 'Algorithm']['parameters']->element);
        if (empty($decoded)) {
            throw new \RuntimeException('Unable to decode BER of parameters');
        }
        $components = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::asn1map($decoded[0], \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAParams::MAP);
        if (!\is_array($components)) {
            throw new \RuntimeException('Unable to perform ASN1 mapping on parameters');
        }
        $decoded = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::decodeBER($key[$type]);
        if (empty($decoded)) {
            throw new \RuntimeException('Unable to decode BER');
        }
        $var = $type == 'privateKey' ? 'x' : 'y';
        $components[$var] = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::asn1map($decoded[0], \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAPublicKey::MAP);
        if (!$components[$var] instanceof \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger) {
            throw new \RuntimeException('Unable to perform ASN1 mapping');
        }
        if (isset($key['meta'])) {
            $components['meta'] = $key['meta'];
        }
        return $components;
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
        $params = ['p' => $p, 'q' => $q, 'g' => $g];
        $params = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::encodeDER($params, \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAParams::MAP);
        $params = new \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Element($params);
        $key = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::encodeDER($x, \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAPublicKey::MAP);
        return self::wrapPrivateKey($key, [], $params, $password, $options);
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
    public static function savePublicKey(\Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $p, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $q, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $g, \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $y, array $options = [])
    {
        $params = ['p' => $p, 'q' => $q, 'g' => $g];
        $params = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::encodeDER($params, \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAParams::MAP);
        $params = new \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Element($params);
        $key = \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1::encodeDER($y, \Ethereumico\Epg\Dependencies\phpseclib3\File\ASN1\Maps\DSAPublicKey::MAP);
        return self::wrapPublicKey($key, $params);
    }
}
