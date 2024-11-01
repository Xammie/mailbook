<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Xammie\Mailbook\Http\Middlewares\RollbackDatabase;

it('will rollback database', function (): void {
    config()->set('mailbook.database_rollback', true);

    DB::shouldReceive('beginTransaction')->once();
    DB::shouldReceive('rollback')->once();

    $middleware = new RollbackDatabase;
    $middleware->handle(new stdClass, function () {
        return 'response';
    });
});

it('will rollback database when exception occurs', function (): void {
    config()->set('mailbook.database_rollback', true);

    DB::shouldReceive('beginTransaction')->once();
    DB::shouldReceive('rollback')->once();

    $middleware = new RollbackDatabase;
    $middleware->handle(new stdClass, function (): void {
        throw new RuntimeException('test exception');
    });
})
    ->throws(RuntimeException::class, 'test exception');

it('will not rollback database when disabled', function (): void {
    config()->set('mailbook.database_rollback', false);

    DB::shouldReceive('beginTransaction')->never();
    DB::shouldReceive('rollback')->never();

    $middleware = new RollbackDatabase;
    $middleware->handle(new stdClass, function () {
        return 'response';
    });
});
