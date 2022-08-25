<?php

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

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('mailbook.enabled', true);
    }
}
