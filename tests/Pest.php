<?php

use Xammie\Mailbook\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(fn () => cleanInstallFiles())
    ->afterEach(fn () => cleanInstallFiles())
    ->in(__DIR__);

function cleanInstallFiles(): void
{
    @unlink(base_path('routes/mailbook.php'));
    @unlink(base_path('app/Mail/MailbookMail.php'));
    @unlink(base_path('resources/views/mail/mailbook.blade.php'));
    @rmdir(base_path('app/Mail'));
}

function testFilepath(string $path): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}
