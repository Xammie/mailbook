<?php

use Xammie\Mailbook\MailbookConfig;

it('can get send_to', function () {
    config()->set('mailbook.send_to', 'test@mailbook.dev');

    $config = new MailbookConfig();

    expect($config->getSendTo())->toBe('test@mailbook.dev');
    expect($config->getSendToStrict())->toBe('test@mailbook.dev');
});

it('can get send_to strictly from array', function () {
    config()->set('mailbook.send_to', ['test@mailbook.dev']);

    $config = new MailbookConfig();

    expect($config->getSendTo())->toBe('test@mailbook.dev');
    expect($config->getSendToStrict())->toBe('test@mailbook.dev');
});

it('can get send_to strictly from array with multiple', function () {
    config()->set('mailbook.send_to', ['example@mailbook.dev', 'test@mailbook.dev']);

    $config = new MailbookConfig();

    expect($config->getSendTo())->toBe('example@mailbook.dev');
    expect($config->getSendToStrict())->toBe('example@mailbook.dev');
});

it('cannot get send_to stricly when not a string', function () {
    config()->set('mailbook.send_to', null);

    $config = new MailbookConfig();

    expect($config->getSendTo())->toBe(null);
    $config->getSendToStrict();
})
    ->throws(RuntimeException::class, 'invalid config mailbook.send_to should be string');

it('cannot get send_to when empty', function () {
    config()->set('mailbook.send_to', '');

    $config = new MailbookConfig();

    expect($config->getSendTo())->toBe(null);

    $config->getSendToStrict();
})
    ->throws(RuntimeException::class, 'invalid config mailbook.send_to should not be empty');
