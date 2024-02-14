<?php

declare(strict_types=1);

use Mockery\MockInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Xammie\Mailbook\Data\ResolvedMail;

it('can get subject', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getSubject')->andReturn('This is the subject');
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->subject())->toBe('This is the subject');
});

it('can get to address', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getTo')->once()->andReturn([
            new Address('hello@mailbook.dev', 'User'),
        ]);
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->to())->toBe([
        '"User" <hello@mailbook.dev>',
    ]);
});

it('can get to address without remove address', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getTo')->once()->andReturn([
            new Address('remove@mailbook.dev', 'Mailbook'),
        ]);
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->to())->toBe([]);
});

it('can get reply to address', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getReplyTo')->once()->andReturn([
            new Address('hello@mailbook.dev', 'User'),
        ]);
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->replyTo())->toBe([
        '"User" <hello@mailbook.dev>',
    ]);
});

it('can get from address', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getFrom')->once()->andReturn([
            new Address('hello@mailbook.dev', 'User'),
        ]);
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->from())->toBe([
        '"User" <hello@mailbook.dev>',
    ]);
});

it('can get cc address', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getCc')->once()->andReturn([
            new Address('hello@mailbook.dev', 'User'),
        ]);
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->cc())->toBe([
        '"User" <hello@mailbook.dev>',
    ]);
});

it('can get bcc address', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getBcc')->once()->andReturn([
            new Address('hello@mailbook.dev', 'User'),
        ]);
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->bcc())->toBe([
        '"User" <hello@mailbook.dev>',
    ]);
});

it('can get the content from string', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getHtmlBody')->andReturn('this is some mail content');
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->content())->toEqual('this is some mail content');
});

it('can get the content from a steam', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $content = 'this is some mail content';
        $stream = fopen('data://text/plain,'.$content, 'r');
        $mock->shouldReceive('getHtmlBody')->andReturn($stream);
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->content())->toEqual('this is some mail content');
});

it('can get null content', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getHtmlBody')->andReturn(null);
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->content())->toBeNull();
});

it('can get null content from a stream', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $stream = fopen('data://text/plain,', 'r');
        $mock->shouldReceive('getHtmlBody')->andReturn($stream);
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->content())->toBeNull();
});

it('can get attachments', function (): void {
    $email = $this->mock(Email::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getAttachments')->andReturn([
            new DataPart('attachment1', 'file1.txt', 'text/plain'),
            new DataPart('attachment2', 'file2.txt', 'text/plain'),
        ]);
    });
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->attachments())->toBe([
        'file1.txt',
        'file2.txt',
    ]);
});
