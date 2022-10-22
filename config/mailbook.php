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

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
    | This option allows you to define in which languages you wish
    | to use in mailbook.
    |
    */
    'locales' => [
//        'en' => 'English',
//        'nl' => 'Dutch',
//        'de' => 'German',
    ],
];
