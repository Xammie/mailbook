<?php

declare(strict_types=1);

use Xammie\Mailbook\Data\MailableVariant;
use Xammie\Mailbook\MailableResolver;

it('can create a mailable variant', function (): void {
    $variant = new MailableVariant('label', 'slug', 'closure');
    expect($variant->label)->toBe('label');
    expect($variant->slug)->toBe('slug');
    expect($variant->closure)->toBe('closure');
    expect($variant->notifiable)->toBeNull();
});

it('can get a new resolver', function (): void {
    $variant = new MailableVariant('label', 'slug', 'closure');

    expect($variant->resolver())->toBeInstanceOf(MailableResolver::class);
});

it('will only create one resolver', function (): void {
    $variant = new MailableVariant('label', 'slug', 'closure');

    expect($variant->resolver())->toBe($variant->resolver());
});
