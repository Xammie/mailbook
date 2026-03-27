<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Data;

use Mockery\MockInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Xammie\Mailbook\Data\ResolvedMail;
use Xammie\Mailbook\Tests\TestCase;

class ResolvedMailTest extends TestCase
{
    public function test_can_get_subject(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getSubject')->andReturn('This is the subject');
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame('This is the subject', $resolvedMail->subject());
    }

    public function test_can_get_to_address(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getTo')->once()->andReturn([
                new Address('hello@mailbook.dev', 'User'),
            ]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame([
            '"User" <hello@mailbook.dev>',
        ], $resolvedMail->to());
    }

    public function test_can_get_to_address_without_remove_address(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getTo')->once()->andReturn([
                new Address('remove@mailbook.dev', 'Mailbook'),
            ]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame([], $resolvedMail->to());
    }

    public function test_can_get_reply_to_address(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getReplyTo')->once()->andReturn([
                new Address('hello@mailbook.dev', 'User'),
            ]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame([
            '"User" <hello@mailbook.dev>',
        ], $resolvedMail->replyTo());
    }

    public function test_can_get_from_address(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getFrom')->once()->andReturn([
                new Address('hello@mailbook.dev', 'User'),
            ]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame([
            '"User" <hello@mailbook.dev>',
        ], $resolvedMail->from());
    }

    public function test_can_get_cc_address(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getCc')->once()->andReturn([
                new Address('hello@mailbook.dev', 'User'),
            ]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame([
            '"User" <hello@mailbook.dev>',
        ], $resolvedMail->cc());
    }

    public function test_can_get_bcc_address(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getBcc')->once()->andReturn([
                new Address('hello@mailbook.dev', 'User'),
            ]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame([
            '"User" <hello@mailbook.dev>',
        ], $resolvedMail->bcc());
    }

    public function test_can_get_content_from_string(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getHtmlBody')->andReturn('this is some mail content');
            $mock->shouldReceive('getAttachments')->andReturn([]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame('this is some mail content', $resolvedMail->content());
    }

    public function test_can_get_content_from_a_stream(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $content = 'this is some mail content';
            $stream = fopen('data://text/plain,'.$content, 'r');
            $mock->shouldReceive('getHtmlBody')->andReturn($stream);
            $mock->shouldReceive('getAttachments')->andReturn([]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame('this is some mail content', $resolvedMail->content());
    }

    public function test_can_get_null_content(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getHtmlBody')->andReturn(null);
            $mock->shouldReceive('getAttachments')->andReturn([]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame('', $resolvedMail->content());
    }

    public function test_can_get_null_content_from_a_stream(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $stream = fopen('data://text/plain,', 'r');
            $mock->shouldReceive('getHtmlBody')->andReturn($stream);
            $mock->shouldReceive('getAttachments')->andReturn([]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame('', $resolvedMail->content());
    }

    public function test_will_replace_inline_attachments(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getHtmlBody')->andReturn('<img src="cid:GRM6dF7cV3" alt="img alt">');
            $mock->shouldReceive('getAttachments')->andReturn([
                new DataPart('attachment1', 'GRM6dF7cV3', 'image/png'),
            ]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame('<img src="data:image/png;base64,YXR0YWNobWVudDE=" alt="img alt">', $resolvedMail->content());
    }

    public function test_can_get_attachments(): void
    {
        $email = $this->mock(Email::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getAttachments')->andReturn([
                new DataPart('attachment1', 'file1.txt', 'text/plain'),
                new DataPart('attachment2', 'file2.txt', 'text/plain'),
            ]);
        });
        $resolvedMail = new ResolvedMail($email);
        $this->assertSame([
            'file1.txt',
            'file2.txt',
        ], $resolvedMail->attachments());
    }
}
