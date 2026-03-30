<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Data;

use Symfony\Component\Mime\Address;
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
        $this->emailExpectation->expectsGetHtmlBody('<img src="cid:GRM6dF7cV3" alt="img alt">');
        $this->emailExpectation->expectsGetAttachments([
            new DataPart('attachment1', 'GRM6dF7cV3', 'image/png'),
        ]);

        self::assertSame('<img src="data:image/png;base64,YXR0YWNobWVudDE=" alt="img alt">', $this->subject->content());
    }

    public function test_can_get_attachments(): void
    {
        $this->emailExpectation->expectsGetAttachments([
            new DataPart('attachment1', 'file1.txt', 'text/plain'),
            new DataPart('attachment2', 'file2.txt', 'text/plain'),
        ]);

        self::assertSame(['file1.txt', 'file2.txt'], $this->subject->attachments());
    }
}
