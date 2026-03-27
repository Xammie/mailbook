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
        $this->assertCount(2, $mailables);
        $this->assertSame('Test Mail', $mailables->get(0)?->getLabel());
        $this->assertSame('Other Mail', $mailables->get(1)?->getLabel());
    }

    public function test_can_get_grouped_mailables(): void
    {
        Mailbook::add(TestMail::class);
        Mailbook::category('Other')->group(function (): void {
            Mailbook::add(OtherMail::class);
            Mailbook::add(TranslatedMail::class);
        });
        $mailables = Mailbook::groupedMailables();
        $this->assertCount(2, $mailables);
        $this->assertInstanceOf(MailableItem::class, $mailables->get(0));
        $this->assertInstanceOf(MailableGroup::class, $mailables->get(1));
        /** @var MailableGroup $group */
        $group = $mailables->get(1);
        $this->assertInstanceOf(MailableItem::class, $group->items->get(0));
        $this->assertSame('Other Mail', $group->items->get(0)?->getLabel());
        $this->assertSame('Translated Mail', $group->items->get(1)?->getLabel());
    }
}
