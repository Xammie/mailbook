<?php

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\TestMail;

it('executes the closure once', function () {
    $executed = 0;

    $mailable = Mailbook::add(TestMail::class)
        ->variant('test', function () use (&$executed) {
            $executed++;

            return new TestMail();
        });

    $mailable->selectVariant('test');

    $mailable->subject();
    $mailable->to();
    $mailable->content();

    expect($executed)->toBe(1);
});
