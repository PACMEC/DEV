<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\lastguest\Murmur;
class Hash
{
    /**
     * Calculate Sha256(RipeMd160()) on the given data
     *
     * @param BufferInterface $data
     * @return BufferInterface
     */
    public static function sha256ripe160(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\hash('ripemd160', \hash('sha256', $data->getBinary(), \true), \true), 20);
    }
    /**
     * Perform SHA256
     *
     * @param BufferInterface $data
     * @return BufferInterface
     */
    public static function sha256(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\hash('sha256', $data->getBinary(), \true), 32);
    }
    /**
     * Perform SHA256 twice
     *
     * @param BufferInterface $data
     * @return BufferInterface
     */
    public static function sha256d(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\hash('sha256', \hash('sha256', $data->getBinary(), \true), \true), 32);
    }
    /**
     * RIPEMD160
     *
     * @param BufferInterface $data
     * @return BufferInterface
     */
    public static function ripemd160(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\hash('ripemd160', $data->getBinary(), \true), 20);
    }
    /**
     * RIPEMD160 twice
     *
     * @param BufferInterface $data
     * @return BufferInterface
     */
    public static function ripemd160d(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\hash('ripemd160', \hash('ripemd160', $data->getBinary(), \true), \true), 20);
    }
    /**
     * Calculate a SHA1 hash
     *
     * @param BufferInterface $data
     * @return BufferInterface
     */
    public static function sha1(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\hash('sha1', $data->getBinary(), \true), 20);
    }
    /**
     * PBKDF2
     *
     * @param string $algorithm
     * @param BufferInterface $password
     * @param BufferInterface $salt
     * @param integer $count
     * @param integer $keyLength
     * @return BufferInterface
     * @throws \Exception
     */
    public static function pbkdf2(string $algorithm, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $password, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $salt, int $count, int $keyLength) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if ($keyLength < 0) {
            throw new \InvalidArgumentException('Cannot have a negative key-length for PBKDF2');
        }
        $algorithm = \strtolower($algorithm);
        if (!\in_array($algorithm, \hash_algos(), \true)) {
            throw new \Exception('PBKDF2 ERROR: Invalid hash algorithm');
        }
        if ($count <= 0 || $keyLength <= 0) {
            throw new \Exception('PBKDF2 ERROR: Invalid parameters.');
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\hash_pbkdf2($algorithm, $password->getBinary(), $salt->getBinary(), $count, $keyLength, \true), $keyLength);
    }
    /**
     * @param BufferInterface $data
     * @param int $seed
     * @return BufferInterface
     */
    public static function murmur3(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data, int $seed) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\pack('N', \Ethereumico\EthereumWallet\Dependencies\lastguest\Murmur::hash3_int($data->getBinary(), $seed)), 4);
    }
    /**
     * Do HMAC hashing on $data and $salt
     *
     * @param string $algo
     * @param BufferInterface $data
     * @param BufferInterface $salt
     * @return BufferInterface
     */
    public static function hmac(string $algo, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $salt) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\hash_hmac($algo, $data->getBinary(), $salt->getBinary(), \true));
    }
}
