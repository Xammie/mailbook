<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests;

use Xammie\Mailbook\Data\MailableGroup;
use Xammie\Mailbook\Data\MailableItem;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedMail;

class MailbookTest extends TestCase
{
    public function test_can_get_mailables(): void
    {
        Mailbook::add(TestMail::class);
        Mailbook::add(OtherMail::class);
        $mailables = Mailbook::mailables();
        self::assertCount(2, $mailables);
        self::assertSame('Test Mail', $mailables->get(0)?->getLabel());
        self::assertSame('Other Mail', $mailables->get(1)?->getLabel());
    }

    public function test_can_get_grouped_mailables(): void
    {
        Mailbook::add(TestMail::class);
        Mailbook::category('Other')->group(function (): void {
            Mailbook::add(OtherMail::class);
            Mailbook::add(TranslatedMail::class);
        });
        $mailables = Mailbook::groupedMailables();
        self::assertCount(2, $mailables);
        self::assertInstanceOf(MailableItem::class, $mailables->get(0));
        self::assertInstanceOf(MailableGroup::class, $mailables->get(1));
        /** @var MailableGroup $group */
        $group = $mailables->get(1);
        self::assertSame('Other', $group->label);
        self::assertInstanceOf(MailableItem::class, $group->items->get(0));
        self::assertSame('Other Mail', $group->items->get(0)?->getLabel());
        self::assertSame('Translated Mail', $group->items->get(1)?->getLabel());
    }
}
