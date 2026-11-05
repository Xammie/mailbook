<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Support;

use PHPUnit\Framework\Attributes\DataProvider;
use Xammie\Mailbook\Support\Format;
use Xammie\Mailbook\Tests\TestCase;

final class FormatTest extends TestCase
{
    public static function bytesProvider(): \Iterator
    {
        yield 'negative' => [-10, '0 B'];
        yield 'single byte' => [1, '1 B'];
        yield 'small number' => [64, '64 B'];
        yield 'kb' => [64 * 64, '4 KB'];
        yield 'many kb' => [64 * 64 * 64, '256 KB'];
        yield 'mb' => [64 * 64 * 64 * 64, '16 MB'];
    }

    #[DataProvider('bytesProvider')]
    public function test_can_convert_to_human_readable_sizes(int $bytes, string $expected): void
    {
        self::assertSame($expected, Format::bytesToHuman($bytes));
    }
}
