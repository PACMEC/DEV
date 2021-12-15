<?php

namespace Ethereumico\Epg\Dependencies\GuzzleHttp\Promise;

final class Is
{
    /**
     * Returns true if a promise is pending.
     *
     * @return bool
     */
    public static function pending(\Ethereumico\Epg\Dependencies\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \Ethereumico\Epg\Dependencies\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled or rejected.
     *
     * @return bool
     */
    public static function settled(\Ethereumico\Epg\Dependencies\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() !== \Ethereumico\Epg\Dependencies\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled.
     *
     * @return bool
     */
    public static function fulfilled(\Ethereumico\Epg\Dependencies\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \Ethereumico\Epg\Dependencies\GuzzleHttp\Promise\PromiseInterface::FULFILLED;
    }
    /**
     * Returns true if a promise is rejected.
     *
     * @return bool
     */
    public static function rejected(\Ethereumico\Epg\Dependencies\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \Ethereumico\Epg\Dependencies\GuzzleHttp\Promise\PromiseInterface::REJECTED;
    }
}
