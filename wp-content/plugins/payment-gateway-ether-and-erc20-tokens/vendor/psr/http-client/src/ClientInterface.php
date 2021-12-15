<?php

namespace Ethereumico\Epg\Dependencies\Psr\Http\Client;

use Ethereumico\Epg\Dependencies\Psr\Http\Message\RequestInterface;
use Ethereumico\Epg\Dependencies\Psr\Http\Message\ResponseInterface;
interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(\Ethereumico\Epg\Dependencies\Psr\Http\Message\RequestInterface $request) : \Ethereumico\Epg\Dependencies\Psr\Http\Message\ResponseInterface;
}
