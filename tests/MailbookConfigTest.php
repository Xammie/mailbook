<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests;

use RuntimeException;
use Xammie\Mailbook\MailbookConfig;

class MailbookConfigTest extends TestCase
{
    public function test_can_get_send_to(): void
    {
        config()->set('mailbook.send_to', 'test@mailbook.dev');
        $config = new MailbookConfig;
        self::assertSame('test@mailbook.dev', $config->getSendTo());
        self::assertSame('test@mailbook.dev', $config->getSendToStrict());
    }

    public function test_can_get_send_to_from_array(): void
    {
        config()->set('mailbook.send_to', ['test@mailbook.dev']);
        $config = new MailbookConfig;
        self::assertSame('test@mailbook.dev', $config->getSendTo());
        self::assertSame('test@mailbook.dev', $config->getSendToStrict());
    }

    public function test_can_get_send_to_from_array_with_multiple(): void
    {
        config()->set('mailbook.send_to', ['example@mailbook.dev', 'test@mailbook.dev']);
        $config = new MailbookConfig;
        self::assertSame('example@mailbook.dev', $config->getSendTo());
        self::assertSame('example@mailbook.dev', $config->getSendToStrict());
    }

    public function test_cannot_get_send_to_when_not_a_string(): void
    {
        config()->set('mailbook.send_to', null);
        $config = new MailbookConfig;
        self::assertNull($config->getSendTo());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('invalid config mailbook.send_to should be string');
        $config->getSendToStrict();
    }

    public function test_cannot_get_send_to_when_empty(): void
    {
        config()->set('mailbook.send_to', '');
        $config = new MailbookConfig;
        self::assertNull($config->getSendTo());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('invalid config mailbook.send_to should not be empty');
        $config->getSendToStrict();
    }
}
