<?php

namespace Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Handler;

use Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Promise\PromiseInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\RequestInterface;
/**
 * HTTP handler that uses cURL easy handles as a transport layer.
 *
 * When using the CurlHandler, custom curl options can be specified as an
 * associative array of curl option constants mapping to values in the
 * **curl** key of the "client" key of the request.
 *
 * @final
 */
class CurlHandler
{
    /**
     * @var CurlFactoryInterface
     */
    private $factory;
    /**
     * Accepts an associative array of options:
     *
     * - handle_factory: Optional curl factory used to create cURL handles.
     *
     * @param array{handle_factory?: ?CurlFactoryInterface} $options Array of options to use with the handler
     */
    public function __construct(array $options = [])
    {
        $this->factory = $options['handle_factory'] ?? new \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Handler\CurlFactory(3);
    }
    public function __invoke(\Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\RequestInterface $request, array $options) : \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Promise\PromiseInterface
    {
        if (isset($options['delay'])) {
            \usleep($options['delay'] * 1000);
        }
        $easy = $this->factory->create($request, $options);
        \curl_exec($easy->handle);
        $easy->errno = \curl_errno($easy->handle);
        return \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Handler\CurlFactory::finish($this, $easy, $this->factory);
    }
}