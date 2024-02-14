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
}
