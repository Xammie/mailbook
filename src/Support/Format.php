<?php

namespace Xammie\Mailbook\Support;

/**
 * @internal
 */
class Format
{
    public static function bytesToHuman(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes !== 0 ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= 1024 ** $pow;

        return round($bytes, 2).' '.$units[$pow];
    }
}
