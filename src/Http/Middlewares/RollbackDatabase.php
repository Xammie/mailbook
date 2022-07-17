<?php

namespace Xammie\Mailbook\Http\Middlewares;

use Closure;
use DB;

class RollbackDatabase
{
    public function handle($request, Closure $next): mixed
    {
        DB::beginTransaction();

        $response = $next($request);

        DB::rollBack();

        return $response;
    }
}
