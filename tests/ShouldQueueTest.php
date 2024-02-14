<?php

declare(strict_types=1);

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableSender;
use Xammie\Mailbook\Tests\Fixtures\Mails\ShouldQueueMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;

it('can get mail content from mail with ShouldQueue interface', function (): void {
    config()->set('queue.default', 'redis');

    $mail = Mailbook::add(ShouldQueueMail::class);

    expect($mail->content())->toBeString();
});

it('will not overwrite set queue driver', function (): void {
    config()->set('queue.default', 'redis');

    Mailbook::add(ShouldQueueMail::class);

    expect(config()->get('queue.default'))->toBe('redis');
});

it('will inject sync queue driver', function (): void {
    $mailableSender = new MailableSender(new TestMail());
    invade($mailableSender)->inject();

    expect(config('queue.default'))->toBe('sync');
});
