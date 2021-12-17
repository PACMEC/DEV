<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\BaseAddressCreator;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\InvalidDerivationException;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\Base58ExtendedKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\ExtendedKeySerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Util\IntRange;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class HierarchicalKey
{
    /**
     * @var EcAdapterInterface
     */
    protected $ecAdapter;
    /**
     * @var int
     */
    private $depth;
    /**
     * @var int
     */
    private $parentFingerprint;
    /**
     * @var int
     */
    private $sequence;
    /**
     * @var BufferInterface
     */
    private $chainCode;
    /**
     * @var KeyInterface
     */
    private $key;
    /**
     * @var ScriptDataFactory
     */
    private $scriptDataFactory;
    /**
     * @var ScriptAndSignData|null
     */
    private $scriptAndSignData;
    /**
     * @param EcAdapterInterface $ecAdapter
     * @param ScriptDataFactory $scriptDataFactory
     * @param int $depth
     * @param int $parentFingerprint
     * @param int $sequence
     * @param BufferInterface $chainCode
     * @param KeyInterface $key
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory $scriptDataFactory, int $depth, int $parentFingerprint, int $sequence, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $chainCode, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface $key)
    {
        if ($depth < 0 || $depth > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Util\IntRange::U8_MAX) {
            throw new \InvalidArgumentException('Invalid depth for BIP32 key, must be in range [0 - 255] inclusive');
        }
        if ($parentFingerprint < 0 || $parentFingerprint > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Util\IntRange::U32_MAX) {
            throw new \InvalidArgumentException('Invalid fingerprint for BIP32 key, must be in range [0 - (2^31)-1] inclusive');
        }
        if ($sequence < 0 || $sequence > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Util\IntRange::U32_MAX) {
            throw new \InvalidArgumentException('Invalid sequence for BIP32 key, must be in range [0 - (2^31)-1] inclusive');
        }
        if ($chainCode->getSize() !== 32) {
            throw new \RuntimeException('Chaincode should be 32 bytes');
        }
        if (!$key->isCompressed()) {
            throw new \InvalidArgumentException('A HierarchicalKey must always be compressed');
        }
        $this->ecAdapter = $ecAdapter;
        $this->depth = $depth;
        $this->sequence = $sequence;
        $this->parentFingerprint = $parentFingerprint;
        $this->chainCode = $chainCode;
        $this->key = $key;
        $this->scriptDataFactory = $scriptDataFactory;
    }
    /**
     * Return the depth of this key. This is limited to 256 sequential derivations.
     *
     * @return int
     */
    public function getDepth() : int
    {
        return $this->depth;
    }
    /**
     * Get the sequence number for this address. Hardened keys are
     * created with sequence > 0x80000000. a sequence number lower
     * than this can be derived with the public key.
     *
     * @return int
     */
    public function getSequence() : int
    {
        return $this->sequence;
    }
    /**
     * Get the fingerprint of the parent key. For master keys, this is 00000000.
     *
     * @return int
     */
    public function getFingerprint() : int
    {
        if ($this->getDepth() === 0) {
            return 0;
        }
        return $this->parentFingerprint;
    }
    /**
     * Return the fingerprint to be used for child keys.
     * @return int
     */
    public function getChildFingerprint() : int
    {
        $pubKeyHash = $this->getPublicKey()->getPubKeyHash();
        return (int) $pubKeyHash->slice(0, 4)->getInt();
    }
    /**
     * Return the chain code - a deterministic 'salt' for HMAC-SHA512
     * in child derivations
     *
     * @return BufferInterface
     */
    public function getChainCode() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return $this->chainCode;
    }
    /**
     * @return PrivateKeyInterface
     */
    public function getPrivateKey() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface
    {
        if ($this->key->isPrivate()) {
            /** @var PrivateKeyInterface $key */
            $key = $this->key;
            return $key;
        }
        throw new \RuntimeException('Unable to get private key, not known');
    }
    /**
     * Get the public key the private key or public key.
     *
     * @return PublicKeyInterface
     */
    public function getPublicKey() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface
    {
        if ($this->isPrivate()) {
            return $this->getPrivateKey()->getPublicKey();
        } else {
            /** @var PublicKeyInterface $key */
            $key = $this->key;
            return $key;
        }
    }
    /**
     * @return HierarchicalKey
     */
    public function withoutPrivateKey() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey
    {
        $clone = clone $this;
        $clone->key = $clone->getPublicKey();
        return $clone;
    }
    /**
     * @return ScriptDataFactory
     */
    public function getScriptDataFactory()
    {
        return $this->scriptDataFactory;
    }
    /**
     * @return \BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData
     */
    public function getScriptAndSignData()
    {
        if (null === $this->scriptAndSignData) {
            $this->scriptAndSignData = $this->scriptDataFactory->convertKey($this->key);
        }
        return $this->scriptAndSignData;
    }
    /**
     * @param BaseAddressCreator $addressCreator
     * @return \BitWasp\Bitcoin\Address\Address
     */
    public function getAddress(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\BaseAddressCreator $addressCreator)
    {
        return $this->getScriptAndSignData()->getAddress($addressCreator);
    }
    /**
     * Return whether this is a private key
     *
     * @return bool
     */
    public function isPrivate() : bool
    {
        return $this->key->isPrivate();
    }
    /**
     * Return whether the key is hardened
     *
     * @return bool
     */
    public function isHardened() : bool
    {
        return $this->sequence >> 31 === 1;
    }
    /**
     * Create a buffer containing data to be hashed hashed to yield the child offset
     *
     * @param int $sequence
     * @return BufferInterface
     * @throws \Exception
     */
    public function getHmacSeed(int $sequence) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        if ($sequence < 0 || $sequence > \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Util\IntRange::U32_MAX) {
            throw new \InvalidArgumentException("Sequence is outside valid range, must be >= 0 && <= (2^31)-1");
        }
        if ($sequence >> 31 === 1) {
            if ($this->isPrivate() === \false) {
                throw new \Exception("Can't derive a hardened key without the private key");
            }
            $data = "\0{$this->getPrivateKey()->getBinary()}";
        } else {
            $data = $this->getPublicKey()->getBinary();
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($data . \pack("N", $sequence));
    }
    /**
     * Derive a child key
     *
     * @param int $sequence
     * @return HierarchicalKey
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidDerivationException
     */
    public function deriveChild(int $sequence) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey
    {
        $nextDepth = $this->depth + 1;
        if ($nextDepth > 255) {
            throw new \InvalidArgumentException('Invalid depth for BIP32 key, cannot exceed 255');
        }
        $hash = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::hmac('sha512', $this->getHmacSeed($sequence), $this->chainCode);
        $offset = $hash->slice(0, 32);
        $chain = $hash->slice(32);
        if (!$this->ecAdapter->validatePrivateKey($offset)) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\InvalidDerivationException("Derived invalid key for index {$sequence}, use next index");
        }
        $key = $this->isPrivate() ? $this->getPrivateKey() : $this->getPublicKey();
        $key = $key->tweakAdd($offset->getGmp());
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey($this->ecAdapter, $this->scriptDataFactory, $nextDepth, $this->getChildFingerprint(), $sequence, $chain, $key);
    }
    /**
     * Decodes a BIP32 path into actual 32bit sequence numbers and derives the child key
     *
     * @param string $path
     * @return HierarchicalKey
     * @throws \Exception
     */
    public function derivePath(string $path) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey
    {
        $sequences = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HierarchicalKeySequence();
        $parts = $sequences->decodeRelative($path);
        $numParts = \count($parts);
        $key = $this;
        for ($i = 0; $i < $numParts; $i++) {
            try {
                $key = $key->deriveChild($parts[$i]);
            } catch (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\InvalidDerivationException $e) {
                if ($i === $numParts - 1) {
                    throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\InvalidDerivationException($e->getMessage());
                } else {
                    throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\InvalidDerivationException("Invalid derivation for non-terminal index: cannot use this path!");
                }
            }
        }
        return $key;
    }
    /**
     * Serializes the instance according to whether it wraps a private or public key.
     * @param NetworkInterface $network
     * @return string
     */
    public function toExtendedKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : string
    {
        $network = $network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork();
        $extendedSerializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\Base58ExtendedKeySerializer(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\ExtendedKeySerializer($this->ecAdapter));
        $extended = $extendedSerializer->serialize($network, $this);
        return $extended;
    }
    /**
     * Explicitly serialize as a private key. Throws an exception if
     * the key isn't private.
     *
     * @param NetworkInterface $network
     * @return string
     */
    public function toExtendedPrivateKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : string
    {
        if (!$this->isPrivate()) {
            throw new \LogicException('Cannot create extended private key from public');
        }
        return $this->toExtendedKey($network);
    }
    /**
     * Explicitly serialize as a public key. This will always work.
     *
     * @param NetworkInterface $network
     * @return string
     */
    public function toExtendedPublicKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : string
    {
        return $this->withoutPrivateKey()->toExtendedKey($network);
    }
}
