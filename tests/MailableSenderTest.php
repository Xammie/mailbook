<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Xammie\Mailbook\Data\ResolvedMail;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableSender;
use Xammie\Mailbook\Tests\Fixtures\Mails\AfterCommitMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\ShouldQueueMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestNotification;
use Xammie\Mailbook\Tests\Fixtures\User;

class MailableSenderTest extends TestCase
{
    public function test_can_collect_mail(): void
    {
        Event::fake();

        $mailableSender = new MailableSender(new TestMail);
        $mail = $mailableSender->collect();

        self::assertInstanceOf(ResolvedMail::class, $mail);
        Event::assertDispatched(MessageSending::class);
        Event::assertDispatched(MessageSent::class);
    }

    public function test_can_collect_queued_mail(): void
    {
        Event::fake();
        $mailableSender = new MailableSender(new ShouldQueueMail);
        $mail = $mailableSender->collect();
        self::assertInstanceOf(ResolvedMail::class, $mail);
        Event::assertDispatched(MessageSending::class);
        Event::assertDispatched(MessageSent::class);
    }

    public function test_can_collect_after_commit_mail(): void
    {
        Event::fake();
        Mail::shouldReceive('to')
            ->once()
            ->with('remove@mailbook.dev')
            ->andReturn(Mockery::mock(PendingMail::class)
                ->shouldReceive('send')
                ->once()
                ->withArgs(function (AfterCommitMail $mail) {
                    self::assertFalse($mail->afterCommit);

                    return true;
                })
                ->getMock()
            );
        $mailableSender = new MailableSender(new AfterCommitMail);
        invade($mailableSender)->send();
    }

    public function test_will_add_new_mailer(): void
    {
        $mailableSender = new MailableSender(new TestMail);
        invade($mailableSender)->inject();
        self::assertSame(['transport' => 'mailbook'], config('mail.mailers.mailbook'));
    }

    public function test_will_cleanup_driver(): void
    {
        $mailableSender = new MailableSender(new TestMail);
        $mailableSender->collect();
        self::assertNotSame('mailbook', config('mail.default'));
        self::assertNotSame('mailbook', config('mail.driver'));
    }

    public function test_will_cleanup_message(): void
    {
        $mailableSender = new MailableSender(new TestMail);
        $mailableSender->collect();
        self::assertNull(Mailbook::getMessage());
    }

    public function test_will_inject_driver_config(): void
    {
        $mailableSender = new MailableSender(new TestMail);
        invade($mailableSender)->inject();
        self::assertSame('mailbook', config('mail.default'));
    }

    public function test_will_inject_old_driver_config(): void
    {
        $mailableSender = new MailableSender(new TestMail);
        invade($mailableSender)->inject();
        self::assertSame('mailbook', config('mail.driver'));
    }

    public function test_can_send_mailable_with_email(): void
    {
        $mailableSender = new MailableSender(new TestMail, 'test@mailbook.dev');
        $mail = $mailableSender->collect();
        self::assertSame(['test@mailbook.dev'], $mail->to());
    }

    public function test_can_send_mailable_with_notifiable(): void
    {
        $mailableSender = new MailableSender(new TestNotification, new User(['email' => 'test@mailbook.dev']));
        $mail = $mailableSender->collect();
        self::assertSame(['test@mailbook.dev'], $mail->to());
    }

    public function test_can_send_notification_with_email(): void
    {
        $mailableSender = new MailableSender(new TestNotification, 'test@mailbook.dev');
        $mail = $mailableSender->collect();
        self::assertSame(['test@mailbook.dev'], $mail->to());
    }

    public function test_can_send_notification_with_notifiable(): void
    {
        $mailableSender = new MailableSender(new TestNotification, new User(['email' => 'test@mailbook.dev']));
        $mail = $mailableSender->collect();
        self::assertSame(['test@mailbook.dev'], $mail->to());
    }
}
