<?php

namespace Ethereumico\Epg\Dependencies;

// Don't redefine the functions if included multiple times.
if (!\function_exists('Ethereumico\\Epg\\Dependencies\\GuzzleHttp\\Promise\\promise_for')) {
    require __DIR__ . '/functions.php';
}
