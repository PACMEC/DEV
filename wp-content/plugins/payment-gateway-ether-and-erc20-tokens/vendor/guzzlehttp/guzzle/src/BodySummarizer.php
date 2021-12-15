<?php

namespace Ethereumico\Epg\Dependencies\GuzzleHttp;

use Ethereumico\Epg\Dependencies\Psr\Http\Message\MessageInterface;
final class BodySummarizer implements \Ethereumico\Epg\Dependencies\GuzzleHttp\BodySummarizerInterface
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
    public function summarize(\Ethereumico\Epg\Dependencies\Psr\Http\Message\MessageInterface $message) : ?string
    {
        return $this->truncateAt === null ? \Ethereumico\Epg\Dependencies\GuzzleHttp\Psr7\Message::bodySummary($message) : \Ethereumico\Epg\Dependencies\GuzzleHttp\Psr7\Message::bodySummary($message, $this->truncateAt);
    }
}
