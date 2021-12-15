<?php

namespace Ethereumico\EthereumWallet\Dependencies\GuzzleHttp;

use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\MessageInterface;
final class BodySummarizer implements \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\BodySummarizerInterface
{
    /**
     * @var int|null
     */
    private $truncateAt;
    public function __construct(int $truncateAt = null)
    {
        $this->truncateAt = $truncateAt;
    }
    /**
     * Returns a summarized message body.
     */
    public function summarize(\Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\MessageInterface $message) : ?string
    {
        return $this->truncateAt === null ? \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7\Message::bodySummary($message) : \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7\Message::bodySummary($message, $this->truncateAt);
    }
}
