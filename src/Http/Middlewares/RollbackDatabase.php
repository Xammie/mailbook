<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Http\Middlewares;

use Closure;
use Exception;
use Illuminate\Support\Facades\DB;

class RollbackDatabase
{
    public function handle(mixed $request, Closure $next): mixed
    {
        if (! config('mailbook.database_rollback')) {
            return $next($request);
        }

        DB::beginTransaction();

        try {
            $response = $next($request);
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }

        DB::rollBack();

        return $response;
    }
}
