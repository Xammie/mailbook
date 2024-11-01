<?php

declare(strict_types=1);

use Xammie\Mailbook\Support\ConfigInjector;

it('can set and revert config', function (): void {
    config()->set('example.test', 'foo');

    $injector = new ConfigInjector;

    $injector->set('example.test', 'bar');

    expect(config('example.test'))->toBe('bar');

    $injector->revert();

    expect(config('example.test'))->toBe('foo');
});
