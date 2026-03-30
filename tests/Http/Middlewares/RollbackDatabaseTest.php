<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Http\Middlewares;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use stdClass;
use Xammie\Mailbook\Http\Middlewares\RollbackDatabase;
use Xammie\Mailbook\Tests\TestCase;

class RollbackDatabaseTest extends TestCase
{
    public function test_will_rollback_database(): void
    {
        config()->set('mailbook.database_rollback', true);
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollback')->once();
        $middleware = new RollbackDatabase;
        $middleware->handle(new stdClass, fn () => 'response');
    }

    public function test_will_rollback_database_when_exception_occurs(): void
    {
        config()->set('mailbook.database_rollback', true);
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollback')->once();
        $middleware = new RollbackDatabase;
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('test exception');
        $middleware->handle(new stdClass, function (): void {
            throw new RuntimeException('test exception');
        });
    }

    public function test_will_not_rollback_database_when_disabled(): void
    {
        config()->set('mailbook.database_rollback', false);
        DB::shouldReceive('beginTransaction')->never();
        DB::shouldReceive('rollback')->never();
        $middleware = new RollbackDatabase;
        $middleware->handle(new stdClass, fn () => 'response');
    }
}
