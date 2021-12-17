<?php

namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint;
interface HasherInterface
{
    /**
     * @return string
     */
    public function getAlgorithm() : string;
    /**
     * @return int
     */
    public function getLengthInBytes() : int;
    /**
     * @param string $data
     * @return string
     */
    public function makeRawHash(string $data) : string;
    /**
     * @param string $data
     * @param GeneratorPoint $G
     * @return \GMP
     */
    public function makeHash(string $data, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint $G) : \GMP;
}
