<?php

use Xammie\Mailbook\Tests\Mails\TestMail;
use Xammie\Mailbook\Tests\Mails\TestNotification;

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
