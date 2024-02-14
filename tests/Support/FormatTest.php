<?php

declare(strict_types=1);

use Xammie\Mailbook\Support\Format;

it('can convert to human readable sizes', function ($bytes, $expected): void {
    expect(Format::bytesToHuman($bytes))->toBe($expected);
})
    ->with([
        [-10, '0 B'],
        [1, '1 B'],
        [64, '64 B'],
        [64 * 64, '4 KB'],
        [64 * 64 * 64, '256 KB'],
        [64 * 64 * 64 * 64, '16 MB'],
    ]);
