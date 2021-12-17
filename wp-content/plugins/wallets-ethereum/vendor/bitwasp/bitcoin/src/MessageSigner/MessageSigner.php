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
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null)
    {
        $this->ecAdapter = $ecAdapter ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter();
    }
    /**
     * @param NetworkInterface $network
     * @param string $message
     * @return BufferInterface
     * @throws \Exception
     */
    private function calculateBody(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network, string $message) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $prefix = \sprintf("%s:\n", $network->getSignedMessageMagic());
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\sprintf("%s%s%s%s", \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::numToVarInt(\strlen($prefix))->getBinary(), $prefix, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::numToVarInt(\strlen($message))->getBinary(), $message));
    }
    /**
     * @param NetworkInterface $network
     * @param string $message
     * @return BufferInterface
     */
    public function calculateMessageHash(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network, string $message) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Hash::sha256d($this->calculateBody($network, $message));
    }
    /**
     * @param SignedMessage $signedMessage
     * @param PayToPubKeyHashAddress $address
     * @param NetworkInterface|null $network
     * @return bool
     */
    public function verify(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\MessageSigner\SignedMessage $signedMessage, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\PayToPubKeyHashAddress $address, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : bool
    {
        $network = $network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork();
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
    public function sign(string $message, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface $privateKey, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\MessageSigner\SignedMessage
    {
        $network = $network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork();
        $hash = $this->calculateMessageHash($network, $message);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\MessageSigner\SignedMessage($message, $privateKey->signCompact($hash, new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\Random\Rfc6979($this->ecAdapter, $privateKey, $hash, 'sha256')));
    }
}
