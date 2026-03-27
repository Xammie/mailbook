<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests;

use Exception;
use Generator;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use UnexpectedValueException;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableResolver;
use Xammie\Mailbook\Tests\Fixtures\Mails\NotificationMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestNotification;

class MailableResolverTest extends TestCase
{
    #[DataProvider('provideMailables')]
    public function test_can_get_class_from_mailable($mailable): void
    {
        $resolver = new MailableResolver($mailable);
        $this->assertEquals(TestMail::class, $resolver->className());
    }

    #[DataProvider('provideNotifications')]
    public function test_can_get_class_from_notification($mailable): void
    {
        $resolver = new MailableResolver($mailable);
        $this->assertEquals(TestNotification::class, $resolver->className());
    }

    public function test_can_get_class_from_class(): void
    {
        $resolver = new MailableResolver(TestMail::class);
        $this->assertEquals(TestMail::class, $resolver->className());
    }

    public function test_can_get_class_from_closure(): void
    {
        $resolver = new MailableResolver(fn () => new TestMail);
        $this->assertEquals(TestMail::class, $resolver->className());
    }

    public function test_cannot_get_class_from_closure_with_invalid_type(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');
        $resolver = new MailableResolver(fn () => 'invalid');
        $resolver->className();
    }

    public function test_can_get_class_from_closure_with_return_type(): void
    {
        $resolver = new MailableResolver(function (): TestMail {
            throw new Exception('this will not be executed');
        });
        $this->assertEquals(TestMail::class, $resolver->className());
    }

    #[DataProvider('provideMailables')]
    public function test_can_get_instance_from_mailables($mailable): void
    {
        $resolver = new MailableResolver($mailable);
        $this->assertInstanceOf(TestMail::class, $resolver->instance());
    }

    #[DataProvider('provideNotifications')]
    public function test_can_get_instance_from_notifications($mailable): void
    {
        $resolver = new MailableResolver($mailable);
        $this->assertInstanceOf(TestNotification::class, $resolver->instance());
    }

    public function test_can_get_instance_from_class(): void
    {
        $resolver = new MailableResolver(TestMail::class);
        $this->assertInstanceOf(TestMail::class, $resolver->instance());
    }

    public function test_can_get_instance_from_closure(): void
    {
        $resolver = new MailableResolver(fn () => new TestMail);
        $this->assertInstanceOf(TestMail::class, $resolver->instance());
    }

    public function test_cannot_get_instance_from_closure_with_invalid_type(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');
        $resolver = new MailableResolver(fn () => 'invalid');
        $resolver->instance();
    }

    public function test_can_get_instance_from_closure_with_return_type(): void
    {
        $resolver = new MailableResolver(fn (): TestMail => new TestMail);
        $this->assertInstanceOf(TestMail::class, $resolver->instance());
    }

    public function test_can_get_instance_from_mailable(): void
    {
        $resolver = new MailableResolver(new TestMail);
        $this->assertInstanceOf(TestMail::class, $resolver->instance());
    }

    public function test_can_resolve_dependencies_from_closure(): void
    {
        (new MailableResolver(function (TestMail $testMail) {
            $this->assertInstanceOf(TestMail::class, $testMail);

            return $testMail;
        }))->instance();
    }

    public function test_will_resolve_instance_once(): void
    {
        $resolver = new MailableResolver(TestMail::class);
        $this->assertSame($resolver->instance(), $resolver->instance());
    }

    public function test_will_execute_closure_once_when_resolving_class_from_closure(): void
    {
        $executed = 0;
        $resolver = new MailableResolver(function () use (&$executed) {
            $executed++;

            return new TestMail;
        });
        $resolver->className();
        $resolver->className();
        $resolver->className();
        $this->assertEquals(1, $executed);
    }

    public function test_resolved_class_instance_can_be_built(): void
    {
        $resolver = new MailableResolver(fn () => new TestMail);
        $this->assertEquals('Test email subject', $resolver->resolve()->subject());
    }

    public function test_can_resolve_class_name_from_notification_class(): void
    {
        $resolver = new MailableResolver(NotificationMail::class);
        $this->assertEquals(NotificationMail::class, $resolver->className());
    }

    public function test_can_resolve_class_name_from_notification_closure(): void
    {
        $resolver = new MailableResolver(fn () => new NotificationMail);
        $this->assertEquals(NotificationMail::class, $resolver->className());
    }

    public function test_can_resolve_mail_from_notification(): void
    {
        Event::fake();
        $resolver = new MailableResolver(NotificationMail::class);
        $content = $resolver->resolve()->content();
        $this->assertStringContainsString('The introduction to the notification.', $content);
        $this->assertStringContainsString('Thank you for using our application!', $content);
        Event::assertDispatched(MessageSending::class);
        $this->assertNull(Mailbook::getMessage());
    }

    public function test_can_resolve_mail_once(): void
    {
        Event::fake();
        $resolver = new MailableResolver(NotificationMail::class);
        $resolver->resolve()->content();
        $resolver->resolve()->content();
        Event::assertDispatchedTimes(MessageSending::class);
    }

    public function test_can_resolve_mail_to(): void
    {
        $resolver = new MailableResolver(OtherMail::class);
        $this->assertEquals(['"Mailbook" <example@mailbook.dev>'], $resolver->resolve()->to());
    }

    public static function provideMailables(): Generator
    {
        yield 'static class name' => [TestMail::class];
        yield 'instance' => [new TestMail];
        yield 'closure returning instance' => [fn () => new TestMail];
        yield 'closure with return type' => [fn (): TestMail => new TestMail];
    }

    public static function provideNotifications(): Generator
    {
        yield 'static class name' => [TestNotification::class];
        yield 'instance' => [new TestNotification];
        yield 'closure returning instance' => [fn () => new TestNotification];
        yield 'closure with return type' => [fn (): TestNotification => new TestNotification];
    }
}
