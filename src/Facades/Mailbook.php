<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \Xammie\Mailbook\Mailbook
 */
class Mailbook extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Xammie\Mailbook\Mailbook::class;
    }
}
