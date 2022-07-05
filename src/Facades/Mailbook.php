<?php

namespace Xammie\Mailbook\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Xammie\Mailbook\Mailbook
 * @mixin \Xammie\Mailbook\Mailbook
 */
class Mailbook extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Xammie\Mailbook\Mailbook::class;
    }
}
