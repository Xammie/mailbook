<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests;

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableSender;
use Xammie\Mailbook\Tests\Fixtures\Mails\ShouldQueueMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;

class ShouldQueueTest extends TestCase
{
    public function test_can_get_mail_content_from_mail_with_should_queue_interface(): void
    {
        config()->set('queue.default', 'redis');

        $mail = Mailbook::add(ShouldQueueMail::class);

        $this->assertIsString($mail->content());
    }

    public function test_will_not_overwrite_set_queue_driver(): void
    {
        config()->set('queue.default', 'redis');

        Mailbook::add(ShouldQueueMail::class);

        $this->assertSame('redis', config()->get('queue.default'));
    }

    public function test_will_inject_sync_queue_driver(): void
    {
        $mailableSender = new MailableSender(new TestMail);
        invade($mailableSender)->inject();

        $this->assertSame('sync', config('queue.default'));
    }
}
