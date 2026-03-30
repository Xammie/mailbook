<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Support;

use Xammie\Mailbook\Support\ConfigInjector;
use Xammie\Mailbook\Tests\TestCase;

class ConfigInjectorTest extends TestCase
{
    public function test_can_set_and_revert_config(): void
    {
        config()->set('example.test', 'foo');

        $injector = new ConfigInjector;

        $injector->set('example.test', 'bar');
        self::assertSame('bar', config('example.test'));

        $injector->revert();
        self::assertSame('foo', config('example.test'));
    }
}
