<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/*
 * Rector is a dev dependency and is not installed in every test matrix combination.
 * The test case is therefore only defined when Rector is available, so the file can
 * always be loaded safely by PHPUnit.
 */
if (class_exists(AbstractRectorTestCase::class)) {
    final class AddMailbookClosureReturnTypeRectorTest extends AbstractRectorTestCase
    {
        #[DataProvider('provideData')]
        public function test(string $filePath): void
        {
            $this->doTestFile($filePath);
        }

        public static function provideData(): Iterator
        {
            return self::yieldFilesFromDirectory(__DIR__.'/Fixture');
        }

        public function provideConfigFilePath(): string
        {
            return __DIR__.'/config/configured_rule.php';
        }
    }
}
