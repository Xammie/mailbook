<?php

declare(strict_types=1);

use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestNotification;

dataset('mailables', fn () => [
    TestMail::class,
    new TestMail(),
    fn () => new TestMail(),
    fn (): TestMail => new TestMail(),
]);

dataset('notifications', fn () => [
    TestNotification::class,
    new TestNotification(),
    fn () => new TestNotification(),
    fn (): TestNotification => new TestNotification(),
]);
