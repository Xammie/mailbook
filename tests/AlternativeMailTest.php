<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests;

use Illuminate\Mail\Mailables\Envelope;
use PHPUnit\Framework\Attributes\DataProvider;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\AlternativeMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\ClassicMail;

class AlternativeMailTest extends TestCase
{
    #[DataProvider('mailProvider')]
    public function test_can_get_mail_subject_and_content($mailable): void
    {
        if (! class_exists(Envelope::class)) {
            $this->markTestSkipped('Alternative mailables are not available in this version of Laravel');
        }

        $item = Mailbook::add($mailable);
        $this->assertSame('Invoice Paid', $item->subject());
        $this->assertSame('Test mail content', $item->content());
        $this->assertSame(['"Example" <hello@example.com>'], $item->from());
        $this->assertSame([], $item->replyTo());
        $this->assertSame([], $item->to());
        $this->assertSame(['"Example Name" <foo@example.com>'], $item->cc());
        $this->assertSame([], $item->bcc());
    }

    public static function mailProvider(): array
    {
        return [
            [new ClassicMail],
            [new AlternativeMail],
        ];
    }
}
