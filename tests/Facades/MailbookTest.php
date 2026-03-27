<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Facades;

use PHPUnit\Framework\Attributes\DataProvider;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\TestCase;

class MailbookTest extends TestCase
{
    public static function localeProvider(): array
    {
        return [
            ['en'],
            ['nl'],
            ['de'],
        ];
    }

    #[DataProvider('localeProvider')]
    public function test_can_set_locale($locale): void
    {
        config()->set('mailbook.locales', [
            'en' => 'English',
            'nl' => 'Dutch',
            'de' => 'German',
        ]);
        $this->assertNull(Mailbook::getLocale());
        Mailbook::setLocale($locale);
        $this->assertSame($locale, Mailbook::getLocale());
    }

    public function test_cannot_set_invalid_locale(): void
    {
        config()->set('mailbook.locales', [
            'en' => 'English',
            'nl' => 'Dutch',
            'de' => 'German',
        ]);
        Mailbook::setLocale(123);
        $this->assertNull(Mailbook::getLocale());
    }

    public function test_cannot_set_unknown_locale(): void
    {
        config()->set('mailbook.locales', [
            'en' => 'English',
            'nl' => 'Dutch',
            'de' => 'German',
        ]);

        Mailbook::setLocale('be');

        $this->assertNull(Mailbook::getLocale());
    }

    public function test_cannot_set_locales_when_none_are_set(): void
    {
        config()->set('mailbook.locales', null);
        Mailbook::setLocale('be');
        $this->assertNull(Mailbook::getLocale());
    }
}
