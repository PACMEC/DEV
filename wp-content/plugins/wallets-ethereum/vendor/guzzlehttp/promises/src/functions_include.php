<?php

namespace Ethereumico\EthereumWallet\Dependencies;

// Don't redefine the functions if included multiple times.
if (!\function_exists('Ethereumico\\EthereumWallet\\Dependencies\\GuzzleHttp\\Promise\\promise_for')) {
    require __DIR__ . '/functions.php';
}
