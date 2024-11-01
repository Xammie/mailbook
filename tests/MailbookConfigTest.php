<?php

declare(strict_types=1);

use Xammie\Mailbook\MailbookConfig;

it('can get send_to', function (): void {
    config()->set('mailbook.send_to', 'test@mailbook.dev');

    $config = new MailbookConfig;

    expect($config->getSendTo())->toBe('test@mailbook.dev');
    expect($config->getSendToStrict())->toBe('test@mailbook.dev');
});

it('can get send_to from array', function (): void {
    config()->set('mailbook.send_to', ['test@mailbook.dev']);

    $config = new MailbookConfig;

    expect($config->getSendTo())->toBe('test@mailbook.dev');
    expect($config->getSendToStrict())->toBe('test@mailbook.dev');
});

it('can get send_to from array with multiple', function (): void {
    config()->set('mailbook.send_to', ['example@mailbook.dev', 'test@mailbook.dev']);

    $config = new MailbookConfig;

    expect($config->getSendTo())->toBe('example@mailbook.dev');
    expect($config->getSendToStrict())->toBe('example@mailbook.dev');
});

it('cannot get send_to when not a string', function (): void {
    config()->set('mailbook.send_to', null);

    $config = new MailbookConfig;

    expect($config->getSendTo())->toBe(null);
    $config->getSendToStrict();
})
    ->throws(RuntimeException::class, 'invalid config mailbook.send_to should be string');

it('cannot get send_to when empty', function (): void {
    config()->set('mailbook.send_to', '');

    $config = new MailbookConfig;

    expect($config->getSendTo())->toBe(null);

    $config->getSendToStrict();
})
    ->throws(RuntimeException::class, 'invalid config mailbook.send_to should not be empty');
