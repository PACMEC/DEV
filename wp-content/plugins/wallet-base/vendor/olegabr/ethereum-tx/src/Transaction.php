<?php

/**
 * This file is part of ethereum-tx package.
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\EthereumWallet\Dependencies\Web3p\EthereumTx;

use InvalidArgumentException;
use RuntimeException;
use Ethereumico\EthereumWallet\Dependencies\Web3p\RLP\RLP;
use Ethereumico\EthereumWallet\Dependencies\Elliptic\EC;
use Ethereumico\EthereumWallet\Dependencies\Elliptic\EC\KeyPair;
use ArrayAccess;
use Ethereumico\EthereumWallet\Dependencies\Web3p\EthereumUtil\Util;
/**
 * It's a instance for generating/serializing ethereum transaction.
 *
 * ```php
 * use Web3p\EthereumTx\Transaction;
 *
 * // generate transaction instance with transaction parameters
 * $transaction = new Transaction([
 *     'nonce' => '0x01',
 *     'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
 *     'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
 *     'gas' => '0x76c0',
 *     'gasPrice' => '0x9184e72a000',
 *     'value' => '0x9184e72a',
 *     'chainId' => 1, // optional
 *     'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
 * ]);
 *
 * // generate transaction instance with hex encoded transaction
 * $transaction = new Transaction('0xf86c098504a817c800825208943535353535353535353535353535353535353535880de0b6b3a76400008025a028ef61340bd939bc2195fe537567866003e1a15d3c71ff63e1590620aa636276a067cbe9d8997f761aecb703304b3800ccf555c9f3dc64214b297fb1966a3b6d83');
 * ```
 *
 * ```php
 * After generate transaction instance, you can sign transaction with your private key.
 * <code>
 * $signedTransaction = $transaction->sign('your private key');
 * ```
 *
 * Then you can send serialized transaction to ethereum through http rpc with web3.php.
 * ```php
 * $hashedTx = $transaction->serialize();
 * ```
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @link https://www.web3p.xyz
 * @filesource https://github.com/web3p/ethereum-tx
 */
class Transaction implements ArrayAccess
{
    /**
     * The actual transaction version implementation
     *
     * @var array
     */
    protected $txImpl;
    /**
     * Ethereum util instance
     *
     * @var \Web3p\EthereumUtil\Util
     */
    protected $util;
    /**
     * construct
     *
     * @param array|string $txData
     * @return void
     */
    public function __construct($txData = [])
    {
        $this->util = new Util();
        if (\is_array($txData)) {
            if (isset($txData['maxPriorityFeePerGas']) || isset($txData['maxFeePerGas'])) {
                $this->txImpl = new EIP1559Transaction($txData);
            } else {
                if (isset($txData['accessList'])) {
                    $this->txImpl = new EIP2930Transaction($txData);
                } else {
                    $this->txImpl = new TransactionLegacy($txData);
                }
            }
        } elseif (\is_string($txData)) {
            if (!$this->util->isHex($txData)) {
                throw new InvalidArgumentException('String tx data should be hex encoded');
            }
            $txData = $this->util->stripZero($txData);
            $firstByteStr = \substr($txData, 0, 2);
            switch ($firstByteStr) {
                case '00':
                    $this->txImpl = new TransactionLegacy($txData);
                    break;
                case '01':
                    $this->txImpl = new EIP2930Transaction($txData);
                    break;
                case '02':
                    $this->txImpl = new EIP1559Transaction($txData);
                    break;
                default:
                    $this->txImpl = new TransactionLegacy($txData);
                    break;
            }
        } else {
            $this->txImpl = new TransactionLegacy($txData);
        }
    }
    /**
     * Return the value in the transaction with given key or return the protected property value if get(property_name} function is existed.
     *
     * @param string $name key or protected property name
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . \ucfirst($name);
        if (\method_exists($this, $method)) {
            return \call_user_func_array([$this, $method], []);
        }
        return $this->offsetGet($name);
    }
    /**
     * Set the value in the transaction with given key or return the protected value if set(property_name} function is existed.
     *
     * @param string $name key, eg: to
     * @param mixed value
     * @return void
     */
    public function __set($name, $value)
    {
        $method = 'set' . \ucfirst($name);
        if (\method_exists($this, $method)) {
            return \call_user_func_array([$this, $method], [$value]);
        }
        return $this->offsetSet($name, $value);
    }
    /**
     * Return hash of the ethereum transaction without signature.
     *
     * @return string hex encoded of the transaction
     */
    public function __toString()
    {
        return $this->txImpl->hash(\false);
    }
    /**
     * Set the value in the transaction with given key.
     *
     * @param string $offset key, eg: to
     * @param string value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        return $this->txImpl->offsetSet($offset, $value);
    }
    /**
     * Return whether the value is in the transaction with given key.
     *
     * @param string $offset key, eg: to
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->txImpl->offsetExists($offset);
    }
    /**
     * Unset the value in the transaction with given key.
     *
     * @param string $offset key, eg: to
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->txImpl->offsetUnset($offset);
    }
    /**
     * Return the value in the transaction with given key.
     *
     * @param string $offset key, eg: to
     * @return mixed value of the transaction
     */
    public function offsetGet($offset)
    {
        return $this->txImpl->offsetGet($offset);
    }
    /**
     * Return raw ethereum transaction data.
     *
     * @return array raw ethereum transaction data
     */
    public function getTxData()
    {
        return $this->txImpl->getTxData();
    }
    /**
     * RLP serialize the ethereum transaction.
     *
     * @return \Web3p\RLP\RLP\Buffer serialized ethereum transaction
     */
    public function serialize()
    {
        return $this->txImpl->serialize();
    }
    /**
     * Sign the transaction with given hex encoded private key.
     *
     * @param string $privateKey hex encoded private key
     * @return string hex encoded signed ethereum transaction
     */
    public function sign($privateKey)
    {
        return $this->txImpl->sign($privateKey);
    }
    /**
     * Return hash of the ethereum transaction with/without signature.
     *
     * @param bool $includeSignature hash with signature
     * @return string hex encoded hash of the ethereum transaction
     */
    public function hash($includeSignature = \false)
    {
        return $this->txImpl->hash($includeSignature);
    }
    /**
     * Recover from address with given signature (r, s, v) if didn't set from.
     *
     * @return string hex encoded ethereum address
     */
    public function getFromAddress()
    {
        return $this->txImpl->getFromAddress();
    }
}
