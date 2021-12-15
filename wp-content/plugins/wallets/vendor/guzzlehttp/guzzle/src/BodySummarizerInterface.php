<?php

namespace Ethereumico\EthereumWallet\Dependencies\GuzzleHttp;

use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(\Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\MessageInterface $message) : ?string;
}
