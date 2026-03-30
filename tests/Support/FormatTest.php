<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Support;

use PHPUnit\Framework\Attributes\DataProvider;
use Xammie\Mailbook\Support\Format;
use Xammie\Mailbook\Tests\TestCase;

class FormatTest extends TestCase
{
    public static function bytesProvider(): array
    {
        return [
            'negative' => [-10, '0 B'],
            'single byte' => [1, '1 B'],
            'small number' => [64, '64 B'],
            'kb' => [64 * 64, '4 KB'],
            'many kb' => [64 * 64 * 64, '256 KB'],
            'mb' => [64 * 64 * 64 * 64, '16 MB'],
        ];
    }

    #[DataProvider('bytesProvider')]
    public function test_can_convert_to_human_readable_sizes(int $bytes, string $expected): void
    {
        self::assertSame($expected, Format::bytesToHuman($bytes));
    }
}
