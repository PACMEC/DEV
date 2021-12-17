<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class TransactionSignature extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature\TransactionSignatureInterface
{
    /**
     * @var EcAdapterInterface
     */
    private $ecAdapter;
    /**
     * @var SignatureInterface
     */
    private $signature;
    /**
     * @var int
     */
    private $hashType;
    /**
     * @param EcAdapterInterface $ecAdapter
     * @param SignatureInterface $signature
     * @param int $hashType
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface $signature, int $hashType)
    {
        $this->ecAdapter = $ecAdapter;
        $this->signature = $signature;
        $this->hashType = $hashType;
    }
    /**
     * @return SignatureInterface
     */
    public function getSignature() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface
    {
        return $this->signature;
    }
    /**
     * @return int
     */
    public function getHashType() : int
    {
        return $this->hashType;
    }
    /**
     * @param TransactionSignatureInterface $other
     * @return bool
     */
    public function equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Signature\TransactionSignatureInterface $other) : bool
    {
        return $this->signature->equals($other->getSignature()) && $this->hashType === $other->getHashType();
    }
    private static function verifyElement(string $fieldName, int $start, int $length, string $binaryString)
    {
        if ($length === 0) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical('Signature ' . $fieldName . ' length is zero');
        }
        $typePrefix = \ord($binaryString[$start - 2]);
        if ($typePrefix !== 0x2) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical('Signature ' . $fieldName . ' value type mismatch');
        }
        $first = \ord($binaryString[$start + 0]);
        if (($first & 0x80) === 128) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical('Signature ' . $fieldName . ' value is negative');
        }
        if ($length > 1 && $first === 0 && (\ord($binaryString[$start + 1]) & 0x80) === 0) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical('Signature ' . $fieldName . ' value excessively padded');
        }
    }
    /**
     * @param BufferInterface $sig
     * @return bool
     * @throws SignatureNotCanonical
     */
    public static function isDERSignature(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $sig) : bool
    {
        $bin = $sig->getBinary();
        $size = $sig->getSize();
        if ($size < 9) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical('Signature too short');
        }
        if ($size > 73) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical('Signature too long');
        }
        if (\ord($bin[0]) !== 0x30) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical('Signature has wrong type');
        }
        if (\ord($bin[1]) !== $size - 3) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical('Signature has wrong length marker');
        }
        $lenR = \ord($bin[3]);
        $startR = 4;
        if (5 + $lenR >= $size) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical('Signature S length misplaced');
        }
        $lenS = \ord($bin[5 + $lenR]);
        $startS = 4 + $lenR + 2;
        if ($lenR + $lenS + 7 !== $size) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\SignatureNotCanonical('Signature R+S length mismatch');
        }
        self::verifyElement('R', $startR, $lenR, $bin);
        self::verifyElement('S', $startS, $lenS, $bin);
        return \true;
    }
    /**
     * @return BufferInterface
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $txSigSerializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface::class, \true, $this->ecAdapter));
        return $txSigSerializer->serialize($this);
    }
}
