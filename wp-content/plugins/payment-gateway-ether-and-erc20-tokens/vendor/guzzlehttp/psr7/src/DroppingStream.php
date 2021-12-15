<?php

declare (strict_types=1);
namespace Ethereumico\Epg\Dependencies\GuzzleHttp\Psr7;

use Ethereumico\Epg\Dependencies\Psr\Http\Message\StreamInterface;
/**
 * Stream decorator that begins dropping data once the size of the underlying
 * stream becomes too full.
 */
final class DroppingStream implements \Ethereumico\Epg\Dependencies\Psr\Http\Message\StreamInterface
{
    use StreamDecoratorTrait;
    /** @var int */
    private $maxLength;
    /**
     * @param StreamInterface $stream    Underlying stream to decorate.
     * @param int             $maxLength Maximum size before dropping data.
     */
    public function __construct(\Ethereumico\Epg\Dependencies\Psr\Http\Message\StreamInterface $stream, int $maxLength)
    {
        $this->stream = $stream;
        $this->maxLength = $maxLength;
    }
    public function write($string) : int
    {
        $diff = $this->maxLength - $this->stream->getSize();
        // Begin returning 0 when the underlying stream is too large.
        if ($diff <= 0) {
            return 0;
        }
        // Write the stream or a subset of the stream if needed.
        if (\strlen($string) < $diff) {
            return $this->stream->write($string);
        }
        return $this->stream->write(\substr($string, 0, $diff));
    }
}
