<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\MessageSigner;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\PayToPubKeyHashAddress;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\Rfc6979;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools;
class MessageSigner
{
    /**
     * @var EcAdapterInterface
     */
    private $ecAdapter;
    /**
     * @param EcAdapterInterface $ecAdapter
     */
    public function __construct(EcAdapterInterface $ecAdapter = null)
    {
        $this->ecAdapter = $ecAdapter ?: Bitcoin::getEcAdapter();
    }
    /**
     * @param NetworkInterface $network
     * @param string $message
     * @return BufferInterface
     * @throws \Exception
     */
    private function calculateBody(NetworkInterface $network, string $message) : BufferInterface
    {
        $prefix = \sprintf("%s:\n", $network->getSignedMessageMagic());
        return new Buffer(\sprintf("%s%s%s%s", Buffertools::numToVarInt(\strlen($prefix))->getBinary(), $prefix, Buffertools::numToVarInt(\strlen($message))->getBinary(), $message));
    }
    /**
     * @param NetworkInterface $network
     * @param string $message
     * @return BufferInterface
     */
    public function calculateMessageHash(NetworkInterface $network, string $message) : BufferInterface
    {
        return Hash::sha256d($this->calculateBody($network, $message));
    }
    /**
     * @param SignedMessage $signedMessage
     * @param PayToPubKeyHashAddress $address
     * @param NetworkInterface|null $network
     * @return bool
     */
    public function verify(SignedMessage $signedMessage, PayToPubKeyHashAddress $address, NetworkInterface $network = null) : bool
    {
        $network = $network ?: Bitcoin::getNetwork();
        $hash = $this->calculateMessageHash($network, $signedMessage->getMessage());
        $publicKey = $this->ecAdapter->recover($hash, $signedMessage->getCompactSignature());
        return $publicKey->getPubKeyHash()->equals($address->getHash());
    }
    /**
     * @param string $message
     * @param PrivateKeyInterface $privateKey
     * @param NetworkInterface|null $network
     * @return SignedMessage
     */
    public function sign(string $message, PrivateKeyInterface $privateKey, NetworkInterface $network = null) : SignedMessage
    {
        $network = $network ?: Bitcoin::getNetwork();
        $hash = $this->calculateMessageHash($network, $message);
        return new SignedMessage($message, $privateKey->signCompact($hash, new Rfc6979($this->ecAdapter, $privateKey, $hash, 'sha256')));
    }
}
