<?php

namespace Ethereumico\Epg\Dependencies\GuzzleHttp;

use Ethereumico\Epg\Dependencies\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(\Ethereumico\Epg\Dependencies\Psr\Http\Message\MessageInterface $message) : ?string;
}
