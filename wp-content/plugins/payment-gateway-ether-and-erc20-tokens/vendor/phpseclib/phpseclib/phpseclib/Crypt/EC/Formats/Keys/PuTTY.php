<?php

/**
 * PuTTY Formatted EC Key Handler
 *
 * PHP version 5
 *
 * @category  Crypt
 * @package   EC
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2015 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */
namespace Ethereumico\Epg\Dependencies\phpseclib3\Crypt\EC\Formats\Keys;

use Ethereumico\Epg\Dependencies\ParagonIE\ConstantTime\Base64;
use Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger;
use Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings;
use Ethereumico\Epg\Dependencies\phpseclib3\Crypt\Common\Formats\Keys\PuTTY as Progenitor;
use Ethereumico\Epg\Dependencies\phpseclib3\Crypt\EC\BaseCurves\Base as BaseCurve;
use Ethereumico\Epg\Dependencies\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards as TwistedEdwardsCurve;
/**
 * PuTTY Formatted EC Key Handler
 *
 * @package EC
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PuTTY extends \Ethereumico\Epg\Dependencies\phpseclib3\Crypt\Common\Formats\Keys\PuTTY
{
    use Common;
    /**
     * Public Handler
     *
     * @var string
     * @access private
     */
    const PUBLIC_HANDLER = 'Ethereumico\\Epg\\Dependencies\\phpseclib3\\Crypt\\EC\\Formats\\Keys\\OpenSSH';
    /**
     * Supported Key Types
     *
     * @var array
     * @access private
     */
    protected static $types = ['ecdsa-sha2-nistp256', 'ecdsa-sha2-nistp384', 'ecdsa-sha2-nistp521', 'ssh-ed25519'];
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
        $components = parent::load($key, $password);
        if (!isset($components['private'])) {
            return $components;
        }
        $private = $components['private'];
        $temp = \Ethereumico\Epg\Dependencies\ParagonIE\ConstantTime\Base64::encode(\Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings::packSSH2('s', $components['type']) . $components['public']);
        $components = \Ethereumico\Epg\Dependencies\phpseclib3\Crypt\EC\Formats\Keys\OpenSSH::load($components['type'] . ' ' . $temp . ' ' . $components['comment']);
        if ($components['curve'] instanceof \Ethereumico\Epg\Dependencies\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards) {
            if (\Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings::shift($private, 4) != "\0\0\0 ") {
                throw new \RuntimeException('Length of ssh-ed25519 key should be 32');
            }
            $components['dA'] = $components['curve']->extractSecret($private);
        } else {
            list($components['dA']) = \Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings::unpackSSH2('i', $private);
            $components['curve']->rangeCheck($components['dA']);
        }
        return $components;
    }
    /**
     * Convert a private key to the appropriate format.
     *
     * @access public
     * @param \phpseclib3\Math\BigInteger $privateKey
     * @param \phpseclib3\Crypt\EC\BaseCurves\Base $curve
     * @param \phpseclib3\Math\Common\FiniteField\Integer[] $publicKey
     * @param string $password optional
     * @param array $options optional
     * @return string
     */
    public static function savePrivateKey(\Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger $privateKey, \Ethereumico\Epg\Dependencies\phpseclib3\Crypt\EC\BaseCurves\Base $curve, array $publicKey, $password = \false, array $options = [])
    {
        self::initialize_static_variables();
        $public = \explode(' ', \Ethereumico\Epg\Dependencies\phpseclib3\Crypt\EC\Formats\Keys\OpenSSH::savePublicKey($curve, $publicKey));
        $name = $public[0];
        $public = \Ethereumico\Epg\Dependencies\ParagonIE\ConstantTime\Base64::decode($public[1]);
        list(, $length) = \unpack('N', \Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings::shift($public, 4));
        \Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings::shift($public, $length);
        // PuTTY pads private keys with a null byte per the following:
        // https://github.com/github/putty/blob/a3d14d77f566a41fc61dfdc5c2e0e384c9e6ae8b/sshecc.c#L1926
        if (!$curve instanceof \Ethereumico\Epg\Dependencies\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards) {
            $private = $privateKey->toBytes();
            if (!(\strlen($privateKey->toBits()) & 7)) {
                $private = "\0{$private}";
            }
        }
        $private = $curve instanceof \Ethereumico\Epg\Dependencies\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards ? \Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings::packSSH2('s', $privateKey->secret) : \Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings::packSSH2('s', $private);
        return self::wrapPrivateKey($public, $private, $name, $password, $options);
    }
    /**
     * Convert an EC public key to the appropriate format
     *
     * @access public
     * @param \phpseclib3\Crypt\EC\BaseCurves\Base $curve
     * @param \phpseclib3\Math\Common\FiniteField[] $publicKey
     * @return string
     */
    public static function savePublicKey(\Ethereumico\Epg\Dependencies\phpseclib3\Crypt\EC\BaseCurves\Base $curve, array $publicKey)
    {
        $public = \explode(' ', \Ethereumico\Epg\Dependencies\phpseclib3\Crypt\EC\Formats\Keys\OpenSSH::savePublicKey($curve, $publicKey));
        $type = $public[0];
        $public = \Ethereumico\Epg\Dependencies\ParagonIE\ConstantTime\Base64::decode($public[1]);
        list(, $length) = \unpack('N', \Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings::shift($public, 4));
        \Ethereumico\Epg\Dependencies\phpseclib3\Common\Functions\Strings::shift($public, $length);
        return self::wrapPublicKey($public, $type);
    }
}
