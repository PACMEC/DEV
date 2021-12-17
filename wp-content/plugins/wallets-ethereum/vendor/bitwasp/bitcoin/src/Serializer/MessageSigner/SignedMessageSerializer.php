<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\MessageSigner;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\CompactSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\MessageSigner\SignedMessage;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class SignedMessageSerializer
{
    // Message headers
    const HEADER = '-----BEGIN BITCOIN SIGNED MESSAGE-----';
    const SIG_START = '-----BEGIN SIGNATURE-----';
    const FOOTER = '-----END BITCOIN SIGNED MESSAGE-----';
    /**
     * @var CompactSignatureSerializerInterface
     */
    private $csSerializer;
    /**
     * @param CompactSignatureSerializerInterface $csSerializer
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\CompactSignatureSerializerInterface $csSerializer)
    {
        $this->csSerializer = $csSerializer;
    }
    /**
     * @param SignedMessage $signedMessage
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\MessageSigner\SignedMessage $signedMessage) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $content = self::HEADER . \PHP_EOL . $signedMessage->getMessage() . \PHP_EOL . self::SIG_START . \PHP_EOL . \base64_encode($signedMessage->getCompactSignature()->getBinary()) . \PHP_EOL . self::FOOTER;
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($content);
    }
    /**
     * @param string $content
     * @return SignedMessage
     */
    public function parse(string $content) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\MessageSigner\SignedMessage
    {
        if (0 !== \strpos($content, self::HEADER)) {
            throw new \RuntimeException('Message must begin with ' . self::HEADER);
        }
        $sigHeaderPos = \strpos($content, self::SIG_START);
        if (\false === $sigHeaderPos) {
            throw new \RuntimeException('Unable to find start of signature');
        }
        $sigEnd = \strlen($content) - \strlen(self::FOOTER);
        if (\strpos($content, self::FOOTER) !== $sigEnd) {
            throw new \RuntimeException('Message must end with ' . self::FOOTER);
        }
        $messageStartPos = \strlen(self::HEADER) + 1;
        $messageEndPos = $sigHeaderPos - $messageStartPos - 1;
        $message = \substr($content, $messageStartPos, $messageEndPos);
        $sigStart = $sigHeaderPos + \strlen(self::SIG_START);
        $sig = \trim(\substr($content, $sigStart, $sigEnd - $sigStart));
        $decoded = \base64_decode($sig);
        if (\false === $decoded) {
            throw new \RuntimeException('Invalid base64');
        }
        $compactSig = $this->csSerializer->parse(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($decoded));
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\MessageSigner\SignedMessage($message, $compactSig);
    }
}
