<?php

declare(strict_types=1);

/*
 * When rector is installed, include its preload file before anything can autoload
 * classes from nikic/php-parser. Rector ships its own copy of php-parser and its
 * preload must run first, otherwise a rector class loaded earlier (like the test
 * case of the rector rule) can make the preload redeclare classes and fatal.
 *
 * @see https://getrector.com/documentation/troubleshooting-preload
 */
$rectorPreload = __DIR__.'/../vendor/rector/rector/preload.php';

if (file_exists($rectorPreload)) {
    require_once $rectorPreload;
}

require_once __DIR__.'/../vendor/autoload.php';
