<?php

namespace Xammie\Mailbook\Http\Middlewares;

use Closure;
use Illuminate\Support\Facades\DB;

class RollbackDatabase
{
    public function handle(mixed $request, Closure $next): mixed
    {
        if (! config('mailbook.database_rollback')) {
            return $next($request);
        }

        DB::beginTransaction();

        $response = $next($request);

        DB::rollBack();

        return $response;
    }
}
