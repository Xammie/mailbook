<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | This option allows you to enable or disable mailbook.
    */
    'enabled' => env('APP_ENV') === 'local',

    /*
    |--------------------------------------------------------------------------
    | Database rollback
    |--------------------------------------------------------------------------
    |
    | This option allows you to enable or disable database rollback.
    | When enabled any changes to the database during the rendering
    | of a mail will be rolled back.
    */
    'database_rollback' => false,

    /*
    |--------------------------------------------------------------------------
    | Display preview
    |--------------------------------------------------------------------------
    |
    | This option allows you to enable or disable the screen size preview button.
    */
    'display_preview' => true,

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | This option allows you to define which languages you want
    | to preview in mailbook.
    */
    //    'locales' => [
    //        'en' => 'English',
    //        'nl' => 'Dutch',
    //        'de' => 'German',
    //    ],

    /*
    |--------------------------------------------------------------------------
    | Send
    |--------------------------------------------------------------------------
    |
    | This option allows you to enable the send mail button.
    */
    'send' => false,

    /*
    |--------------------------------------------------------------------------
    | Send to
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify where the send mail button should be
    | sending to.
    */
    'send_to' => 'test@mailbook.dev',

    /*
    |--------------------------------------------------------------------------
    | Route prefix
    |--------------------------------------------------------------------------
    |
    | This option allows you to define the route prefix that will be used on
    | every route defined by mailbook.
    */
    'route_prefix' => '/mailbook',

    /*
    |--------------------------------------------------------------------------
    | Middlewares
    |--------------------------------------------------------------------------
    |
    | This option allows you to define which middlewares will be used on
    | every route defined by mailbook.
    */
    'middlewares' => [
        Xammie\Mailbook\Http\Middlewares\RollbackDatabase::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Show credits
    |--------------------------------------------------------------------------
    |
    | This option allows you to disable the text "Created with mailbook"
    */
    'show_credits' => true,
];
