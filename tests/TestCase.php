<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Xammie\Mailbook\MailbookServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MailbookServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('mailbook.enabled', true);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanInstallFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanInstallFiles();
        parent::tearDown();
    }

    protected function cleanInstallFiles(): void
    {
        @unlink(base_path('routes/mailbook.php'));
        @unlink(base_path('app/Mail/MailbookMail.php'));
        @unlink(base_path('resources/views/mail/mailbook.blade.php'));
        @rmdir(base_path('app/Mail'));
    }
}
