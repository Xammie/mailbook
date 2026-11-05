<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\AlternativeMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\ClassicMail;

class AlternativeMailTest extends TestCase
{
    #[DataProvider('mailProvider')]
    public function test_can_get_mail_subject_and_content($mailable): void
    {
        $item = Mailbook::add($mailable);
        self::assertSame('Invoice Paid', $item->subject());
        self::assertSame('Test mail content', $item->content());
        self::assertSame(['"Example" <hello@example.com>'], $item->from());
        self::assertSame([], $item->replyTo());
        self::assertSame([], $item->to());
        self::assertSame(['"Example Name" <foo@example.com>'], $item->cc());
        self::assertSame([], $item->bcc());
    }

    public static function mailProvider(): array
    {
        return [
            [new ClassicMail],
            [new AlternativeMail],
        ];
    }
}
