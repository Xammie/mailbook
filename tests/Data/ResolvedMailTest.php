<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Data;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Xammie\Mailbook\Data\ResolvedMail;
use Xammie\Mailbook\Tests\Fixtures\EmailExpectation;
use Xammie\Mailbook\Tests\TestCase;

class ResolvedMailTest extends TestCase
{
    private EmailExpectation $emailExpectation;

    private ResolvedMail $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emailExpectation = EmailExpectation::factory();
        $this->subject = new ResolvedMail($this->emailExpectation->mock);
    }

    public function test_can_get_subject(): void
    {
        $this->emailExpectation->expectsGetSubject('This is the subject');

        self::assertSame('This is the subject', $this->subject->subject());
    }

    public function test_can_get_to_address(): void
    {
        $this->emailExpectation->expectsGetTo([
            new Address('hello@mailbook.dev', 'User'),
        ]);

        self::assertSame(['"User" <hello@mailbook.dev>'], $this->subject->to());
    }

    public function test_can_get_to_address_without_remove_address(): void
    {
        $this->emailExpectation->expectsGetTo([
            new Address('remove@mailbook.dev', 'Mailbook'),
        ]);

        self::assertSame([], $this->subject->to());
    }

    public function test_can_get_reply_to_address(): void
    {
        $this->emailExpectation->expectsGetReplyTo([
            new Address('hello@mailbook.dev', 'User'),
        ]);

        self::assertSame(['"User" <hello@mailbook.dev>'], $this->subject->replyTo());
    }

    public function test_can_get_from_address(): void
    {
        $this->emailExpectation->expectsGetFrom([
            new Address('hello@mailbook.dev', 'User'),
        ]);

        self::assertSame(['"User" <hello@mailbook.dev>'], $this->subject->from());
    }

    public function test_can_get_cc_address(): void
    {
        $this->emailExpectation->expectsGetCc([
            new Address('hello@mailbook.dev', 'User'),
        ]);

        self::assertSame(['"User" <hello@mailbook.dev>'], $this->subject->cc());
    }

    public function test_can_get_bcc_address(): void
    {
        $this->emailExpectation->expectsGetBcc([
            new Address('hello@mailbook.dev', 'User'),
        ]);

        self::assertSame(['"User" <hello@mailbook.dev>'], $this->subject->bcc());
    }

    public function test_can_get_content_from_string(): void
    {
        $this->emailExpectation->expectsGetHtmlBody('this is some mail content');
        $this->emailExpectation->expectsGetAttachments([]);

        self::assertSame('this is some mail content', $this->subject->content());
    }

    public function test_can_get_content_from_a_stream(): void
    {
        $this->emailExpectation->expectsGetHtmlBody(fopen('data://text/plain,this is some mail content', 'r'));
        $this->emailExpectation->expectsGetAttachments([]);

        self::assertSame('this is some mail content', $this->subject->content());
    }

    public function test_can_get_null_content(): void
    {
        $this->emailExpectation->expectsGetHtmlBody(null);
        $this->emailExpectation->expectsGetAttachments([]);

        self::assertSame('', $this->subject->content());
    }

    public function test_can_get_null_content_from_a_stream(): void
    {
        $this->emailExpectation->expectsGetHtmlBody(fopen('data://text/plain,', 'r'));
        $this->emailExpectation->expectsGetAttachments([]);

        self::assertSame('', $this->subject->content());
    }

    public function test_will_replace_inline_attachments(): void
    {
        $this->emailExpectation->expectsGetHtmlBody('<img src="cid:GRM6dF7cV3@symfony" alt="img alt">');
        $this->emailExpectation->expectsGetAttachments([
            (new DataPart(body: 'attachment1', filename: 'GRM6dF7cV3@symfony', contentType: 'image/png')),
        ]);

        self::assertSame('<img src="data:image/png;base64,YXR0YWNobWVudDE=" alt="img alt">', $this->subject->content());
    }

    public function test_will_replace_inline_attachments_with_content_id(): void
    {
        $this->emailExpectation->expectsGetHtmlBody('<img src="cid:GRM6dF7cV3@symfony" alt="img alt">');
        $this->emailExpectation->expectsGetAttachments([
            (new DataPart(body: 'attachment1', filename: 'file.png', contentType: 'image/png'))->setContentId('GRM6dF7cV3@symfony'),
        ]);

        self::assertSame('<img src="data:image/png;base64,YXR0YWNobWVudDE=" alt="img alt">', $this->subject->content());
    }

    public function test_wont_replace_inline_attachments_when_ids_dont_match(): void
    {
        $this->emailExpectation->expectsGetHtmlBody('<img src="cid:GRM6dF7cV3@symfony" alt="img alt">');
        $this->emailExpectation->expectsGetAttachments([
            new DataPart(body: 'attachment1', filename: 'file.png', contentType: 'image/png'),
        ]);

        self::assertSame('<img src="cid:GRM6dF7cV3@symfony" alt="img alt">', $this->subject->content());
    }

    public function test_can_get_attachments(): void
    {
        $this->emailExpectation->expectsGetAttachments([
            new DataPart('attachment1', 'file1.txt', 'text/plain'),
            new DataPart('attachment2', 'file2.txt', 'text/plain'),
        ]);

        self::assertSame(['file1.txt', 'file2.txt'], $this->subject->attachments());
    }

    public function test_can_get_raw_message(): void
    {
        $mail = new ResolvedMail((new Email)
            ->from(new Address('hello@example.com', 'Example'))
            ->to(new Address('hello@mailbook.dev', 'User'))
            ->replyTo(new Address('questions@example.com', 'Support'))
            ->subject('This is the subject')
            ->html('<h1>Test mail content</h1>')
        );

        $raw = $mail->raw();

        self::assertStringContainsString('From: Example <hello@example.com>', $raw);
        self::assertStringContainsString('To: User <hello@mailbook.dev>', $raw);
        self::assertStringContainsString('Reply-To: Support <questions@example.com>', $raw);
        self::assertStringContainsString('Subject: This is the subject', $raw);
        self::assertStringContainsString('MIME-Version: 1.0', $raw);
        self::assertStringContainsString('Date:', $raw);
        self::assertStringContainsString('Message-ID:', $raw);
        self::assertStringContainsString('<h1>Test mail content</h1>', $raw);
    }

    public function test_raw_message_hides_bcc_recipients(): void
    {
        $mail = new ResolvedMail((new Email)
            ->from(new Address('hello@example.com'))
            ->to(new Address('hello@mailbook.dev'))
            ->bcc(new Address('secret@mailbook.dev'))
            ->subject('This is the subject')
            ->html('Test mail content')
        );

        self::assertStringNotContainsString('secret@mailbook.dev', $mail->raw());
    }

    public function test_raw_message_will_filter_out_the_preview_recipient(): void
    {
        $mail = new ResolvedMail((new Email)
            ->from(new Address('hello@example.com'))
            ->to(new Address('remove@mailbook.dev'))
            ->subject('This is the subject')
            ->html('Test mail content')
        );

        $raw = $mail->raw();

        self::assertStringNotContainsString('remove@mailbook.dev', $raw);
        self::assertStringNotContainsString('To:', $raw);
    }

    public function test_raw_message_will_filter_out_the_preview_recipient_from_multiple_recipients(): void
    {
        $mail = new ResolvedMail((new Email)
            ->from(new Address('hello@example.com'))
            ->to(new Address('remove@mailbook.dev'))
            ->addTo(new Address('hello@mailbook.dev'))
            ->subject('This is the subject')
            ->html('Test mail content')
        );

        $raw = $mail->raw();

        self::assertStringNotContainsString('remove@mailbook.dev', $raw);
        self::assertStringContainsString('To: hello@mailbook.dev', $raw);
    }

    public function test_raw_message_will_not_modify_the_original_message(): void
    {
        $email = (new Email)
            ->from(new Address('hello@example.com'))
            ->to(new Address('remove@mailbook.dev'))
            ->subject('This is the subject')
            ->html('Test mail content');

        $mail = new ResolvedMail($email);

        $mail->raw();

        self::assertEquals([new Address('remove@mailbook.dev')], $email->getTo());
    }
}
