<?php

use Symfony\Component\Mime\Email;
use Xammie\Mailbook\MailableResolver;
use Xammie\Mailbook\ResolvedMail;
use Xammie\Mailbook\Tests\Mails\TestNotification;

it('can get to address', function () {
    $resolver = new MailableResolver(TestNotification::class);

    expect($resolver->resolve()->to())->toBe([]);
});

it('can get the content from a steam', function () {
    $content = 'this is some mail content';
    $stream = fopen(sprintf('data://text/plain,%s', $content), 'r');

    $email = (new Email())->html($stream);
    $resolvedMail = new ResolvedMail($email);

    expect($resolvedMail->content())->toEqual($content);
});
