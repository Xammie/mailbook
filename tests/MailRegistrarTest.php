<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests;

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\ClassicMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestNotification;

class MailRegistrarTest extends TestCase
{
    public function test_can_register_mailable_with_label_as_first_method(): void
    {
        $item = Mailbook::label('Test label')->add(TestMail::class);
        self::assertSame('Test label', $item->getLabel());
    }

    public function test_will_clear_label_in_next_call(): void
    {
        Mailbook::label('Test label');
        $item = Mailbook::add(TestMail::class);
        self::assertSame('Test Mail', $item->getLabel());
    }

    public function test_can_group_mails(): void
    {
        Mailbook::to('test@mailbook.dev')
            ->group(function (): void {
                Mailbook::add(TestMail::class);
                Mailbook::add(TestNotification::class);
            });
        $first = Mailbook::mailables()->first();
        $last = Mailbook::mailables()->last();
        self::assertSame(['test@mailbook.dev'], $first->to());
        self::assertSame(['test@mailbook.dev'], $last->to());
    }

    public function test_will_clear_registrar_after_group_call(): void
    {
        Mailbook::to('test@mailbook.dev')
            ->group(function (): void {
                Mailbook::add(TestMail::class);
                Mailbook::add(TestNotification::class);
            });
        $item = Mailbook::add(ClassicMail::class);
        self::assertSame([], $item->to());
    }

    public function test_can_pass_notifiable(): void
    {
        $item = Mailbook::to('test@mailbook.dev')->add(TestMail::class);
        self::assertSame(['test@mailbook.dev'], $item->to());
    }

    public function test_will_reset_notifiable(): void
    {
        Mailbook::to('test@mailbook.dev')->add(OtherMail::class);
        $item = Mailbook::add(TestMail::class);
        self::assertSame([], $item->to());
    }

    public function test_can_pass_notifiable_as_closure(): void
    {
        $item = Mailbook::to(fn () => 'test@mailbook.dev')->add(TestMail::class);
        self::assertSame(['test@mailbook.dev'], $item->to());
    }

    public function test_can_pass_notifiable_as_closure_to_group(): void
    {
        Mailbook::to(fn () => 'test@mailbook.dev')
            ->group(function (): void {
                Mailbook::add(TestMail::class);
                Mailbook::add(TestNotification::class);
            });
        $first = Mailbook::mailables()->first();
        self::assertSame(['test@mailbook.dev'], $first->to());
    }
}
