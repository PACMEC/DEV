<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7;

use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\RequestFactoryInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\RequestInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\ResponseFactoryInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\ResponseInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\ServerRequestFactoryInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\ServerRequestInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\StreamFactoryInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\StreamInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\UploadedFileFactoryInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\UploadedFileInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\UriFactoryInterface;
use Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\UriInterface;
/**
 * Implements all of the PSR-17 interfaces.
 *
 * Note: in consuming code it is recommended to require the implemented interfaces
 * and inject the instance of this class multiple times.
 */
final class HttpFactory implements \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\RequestFactoryInterface, \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\ResponseFactoryInterface, \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\ServerRequestFactoryInterface, \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\StreamFactoryInterface, \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\UploadedFileFactoryInterface, \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\UriFactoryInterface
{
    public function createUploadedFile(\Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null) : \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\UploadedFileInterface
    {
        if ($size === null) {
            $size = $stream->getSize();
        }
        return new \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7\UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }
    public function createStream(string $content = '') : \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\StreamInterface
    {
        return \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7\Utils::streamFor($content);
    }
    public function createStreamFromFile(string $file, string $mode = 'r') : \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\StreamInterface
    {
        try {
            $resource = \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7\Utils::tryFopen($file, $mode);
        } catch (\RuntimeException $e) {
            if ('' === $mode || \false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'], \true)) {
                throw new \InvalidArgumentException(\sprintf('Invalid file opening mode "%s"', $mode), 0, $e);
            }
            throw $e;
        }
        return \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7\Utils::streamFor($resource);
    }
    public function createStreamFromResource($resource) : \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\StreamInterface
    {
        return \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7\Utils::streamFor($resource);
    }
    public function createServerRequest(string $method, $uri, array $serverParams = []) : \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\ServerRequestInterface
    {
        if (empty($method)) {
            if (!empty($serverParams['REQUEST_METHOD'])) {
                $method = $serverParams['REQUEST_METHOD'];
            } else {
                throw new \InvalidArgumentException('Cannot determine HTTP method');
            }
        }
        return new \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7\ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
    public function createResponse(int $code = 200, string $reasonPhrase = '') : \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\ResponseInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7\Response($code, [], null, '1.1', $reasonPhrase);
    }
    public function createRequest(string $method, $uri) : \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\RequestInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7\Request($method, $uri);
    }
    public function createUri(string $uri = '') : \Ethereumico\EthereumWallet\Dependencies\Psr\Http\Message\UriInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\GuzzleHttp\Psr7\Uri($uri);
    }
}
