<?php

use Xammie\Mailbook\Tests\Mails\TestMail;

dataset('mailables', function () {
    return [
        TestMail::class,
        new TestMail(),
        fn () => new TestMail(),
        fn (): TestMail => new TestMail(),
    ];
});
