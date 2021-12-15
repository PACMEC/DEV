<?php

/**
 * Prime Finite Fields
 *
 * Utilizes the factory design pattern
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
namespace Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math;

use Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\Common\FiniteField;
use Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\PrimeField\Integer;
/**
 * Prime Finite Fields
 *
 * @package Math
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
class PrimeField extends \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\Common\FiniteField
{
    /**
     * Instance Counter
     *
     * @var int
     */
    private static $instanceCounter = 0;
    /**
     * Keeps track of current instance
     *
     * @var int
     */
    protected $instanceID;
    /**
     * Default constructor
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger $modulo)
    {
        //if (!$modulo->isPrime()) {
        //    throw new \UnexpectedValueException('PrimeField requires a prime number be passed to the constructor');
        //}
        $this->modulo = $modulo;
        $this->instanceID = self::$instanceCounter++;
        \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\PrimeField\Integer::setModulo($this->instanceID, $modulo);
        \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\PrimeField\Integer::setRecurringModuloFunction($this->instanceID, $modulo->createRecurringModuloFunction());
    }
    /**
     * Use a custom defined modular reduction function
     */
    public function setReduction(callable $func)
    {
        $this->reduce = $func->bindTo($this, $this);
    }
    /**
     * Returns an instance of a dynamically generated PrimeFieldInteger class
     *
     * @return object
     */
    public function newInteger(\Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger $num)
    {
        return new \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\PrimeField\Integer($this->instanceID, $num);
    }
    /**
     * Returns an integer on the finite field between one and the prime modulo
     *
     * @return object
     */
    public function randomInteger()
    {
        static $one;
        if (!isset($one)) {
            $one = new \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger(1);
        }
        return new \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\PrimeField\Integer($this->instanceID, \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\BigInteger::randomRange($one, \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\PrimeField\Integer::getModulo($this->instanceID)));
    }
    /**
     * Returns the length of the modulo in bytes
     *
     * @return integer
     */
    public function getLengthInBytes()
    {
        return \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\PrimeField\Integer::getModulo($this->instanceID)->getLengthInBytes();
    }
    /**
     * Returns the length of the modulo in bits
     *
     * @return integer
     */
    public function getLength()
    {
        return \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\PrimeField\Integer::getModulo($this->instanceID)->getLength();
    }
    /**
     *  Destructor
     */
    public function __destruct()
    {
        \Ethereumico\EthereumWallet\Dependencies\phpseclib3\Math\PrimeField\Integer::cleanupCache($this->instanceID);
    }
}