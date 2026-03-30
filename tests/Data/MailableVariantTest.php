<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Data;

use Xammie\Mailbook\Data\MailableVariant;
use Xammie\Mailbook\MailableResolver;
use Xammie\Mailbook\Tests\TestCase;

class MailableVariantTest extends TestCase
{
    public function test_can_create_a_mailable_variant(): void
    {
        $variant = new MailableVariant('label', 'slug', 'closure');
        self::assertSame('label', $variant->label);
        self::assertSame('slug', $variant->slug);
        self::assertSame('closure', $variant->closure);
        self::assertNull($variant->notifiable);
    }

    public function test_can_get_a_new_resolver(): void
    {
        $variant = new MailableVariant('label', 'slug', 'closure');
        self::assertInstanceOf(MailableResolver::class, $variant->resolver());
    }

    public function test_will_only_create_one_resolver(): void
    {
        $variant = new MailableVariant('label', 'slug', 'closure');
        self::assertSame($variant->resolver(), $variant->resolver());
    }
}
