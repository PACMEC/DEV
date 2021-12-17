<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class ElectrumKey
{
    /**
     * @var null|PrivateKeyInterface
     */
    private $masterPrivate;
    /**
     * @var PublicKeyInterface
     */
    private $masterPublic;
    /**
     * @param KeyInterface $masterKey
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface $masterKey)
    {
        if ($masterKey->isCompressed()) {
            throw new \RuntimeException('Electrum keys are not compressed');
        }
        if ($masterKey instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface) {
            $this->masterPrivate = $masterKey;
            $this->masterPublic = $masterKey->getPublicKey();
        } elseif ($masterKey instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface) {
            $this->masterPublic = $masterKey;
        }
    }
    /**
     * @return PrivateKeyInterface
     */
    public function getMasterPrivateKey() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
    {
        if (null === $this->masterPrivate) {
            throw new \RuntimeException("Cannot produce master private key from master public key");
        }
        return $this->masterPrivate;
    }
    /**
     * @return PublicKeyInterface
     */
    public function getMasterPublicKey() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface
    {
        return $this->masterPublic;
    }
    /**
     * @return BufferInterface
     */
    public function getMPK() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return $this->getMasterPublicKey()->getBuffer()->slice(1);
    }
    /**
     * @param int $sequence
     * @param bool $change
     * @return \GMP
     */
    public function getSequenceOffset(int $sequence, bool $change = \false) : \GMP
    {
        $seed = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\sprintf("%s:%d:%s", $sequence, $change ? 1 : 0, $this->getMPK()->getBinary()));
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d($seed)->getGmp();
    }
    /**
     * @param int $sequence
     * @param bool $change
     * @return KeyInterface
     */
    public function deriveChild(int $sequence, bool $change = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface
    {
        $key = \is_null($this->masterPrivate) ? $this->masterPublic : $this->masterPrivate;
        return $key->tweakAdd($this->getSequenceOffset($sequence, $change));
    }
    /**
     * @return ElectrumKey
     */
    public function withoutPrivateKey() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\ElectrumKey
    {
        $clone = clone $this;
        $clone->masterPrivate = null;
        return $clone;
    }
}
