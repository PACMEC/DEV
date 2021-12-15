<?php

declare (strict_types=1);
namespace Ethereumico\Epg\Dependencies\GuzzleHttp\Psr7;

use Ethereumico\Epg\Dependencies\Psr\Http\Message\StreamInterface;
/**
 * Stream decorator that prevents a stream from being seeked.
 */
final class NoSeekStream implements \Ethereumico\Epg\Dependencies\Psr\Http\Message\StreamInterface
{
    use StreamDecoratorTrait;
    public function seek($offset, $whence = \SEEK_SET) : void
    {
        throw new \RuntimeException('Cannot seek a NoSeekStream');
    }
    public function isSeekable() : bool
    {
        return \false;
    }
}
