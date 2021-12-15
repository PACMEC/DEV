<?php

namespace Ethereumico\Epg\Dependencies;

// Don't redefine the functions if included multiple times.
if (!\function_exists('Ethereumico\\Epg\\Dependencies\\GuzzleHttp\\describe_type')) {
    require __DIR__ . '/functions.php';
}
