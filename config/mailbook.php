<?php

return [
    'enabled' => env('APP_ENV') === 'local',
    'database_rollback' => true,
    'display_preview' => true,
    'refresh_button' => true,
    'route_prefix' => '/mailbook',
    'middlewares' => [
        Xammie\Mailbook\Http\Middlewares\RollbackDatabase::class,
    ],
];
