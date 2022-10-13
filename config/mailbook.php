<?php

return [
    'enabled' => env('APP_ENV') === 'local',
    'database_rollback' => false,
    'display_preview' => true,
    'refresh_button' => true,
    'route_prefix' => '/mailbook',
    'middlewares' => [
        Xammie\Mailbook\Http\Middlewares\RollbackDatabase::class,
    ],
    'show_credits' => true,

    'localization' => [
        'locales' => [
            'en' => 'English',
            'nl' => 'Dutch',
        ],
    ],
];
