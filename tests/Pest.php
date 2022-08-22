<?php

use Xammie\Mailbook\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

beforeAll(function () {
    @unlink(base_path('routes/mailbook.php'));
    @unlink(base_path('app/Mail/MailbookMail.php'));
    @unlink(base_path('resources/views/mail/mailbook.blade.php'));
});
