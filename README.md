# Mailbook

[![Latest Version on Packagist](https://img.shields.io/packagist/v/xammie/mailbook.svg?style=flat-square)](https://packagist.org/packages/xammie/mailbook)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/xammie/mailbook/run-tests?label=tests)](https://github.com/xammie/mailbook/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/xammie/mailbook/Check%20&%20fix%20styling?label=code%20style)](https://github.com/xammie/mailbook/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/xammie/mailbook.svg?style=flat-square)](https://packagist.org/packages/xammie/mailbook)

Mailbook is a Laravel package that lets you easily inspect your mails without having to actually trigger it in your application.

![Example screenshot](./screenshot.png)

<p align="center"><a href="https://mailbook.dev/">View demo</a></p>

## Installation

You can install the package via composer:

```bash
composer require --dev xammie/mailbook
```

You can register mails in a new service provider.

```php
php artisan make:provider MailbookProvider
```

Make sure to return early if your application is not in debug mode.

```php
public function boot(): void
{
    if (! config('app.debug')) {
        return;
    }
    
    Mailbook::add(...);
}
```

and register it in `config/app.php`.

```php{4}
'providers' => [
    ...  
       
    /*
     * Application Service Providers...
     */
    App\Providers\MailbookProvider::class,
],
```

## Usage

Before you can view transactional emails in the mailbook you have to register them.

```php
// This will use dependency injection if your mailable has parameters
Mailbook::add(VerificationMail::class);

// Use a closure to customize the parameters of the mail instance
Mailbook::add(function () {
    $user = User::factory()->make();

    return new VerificationMail($user, '/example/url')
});

// You can also use dependency injection in the closure
Mailbook::add(function (User $user) {
    return new VerificationMail($user, '/example/url');
});
```

Next head over to `/mailbook` to preview the mailables.

## Variants

When creating mails you might have a couple of different scenario's that you want to test for one mail, you can use
variants to solve this.

```php
// Use a closure to customize the parameters of the mail instance
Mailbook::add(OrderCreatedMail::class)
    ->variant('1 item', fn () => new OrderCreatedMail(Order::factory()->withOneProduct()->create()))
    ->variant('2 items', fn () => new OrderCreatedMail(Order::factory()->withTwoProducts()->create()));
```

## Using the database

Most of the time your mailables will need database models. Sometimes you will even preform queries when rendering these
mailables. You can safely use factories and other queries when registering your mailables. Mailbook will automatically
rollback these changes after rendering.

```php
// All database changes are rolled back after rendering the mail.
Mailbook::add(function (): OrderShippedMail {
    $order = Order::factory()->create();
    $tracker = Tracker::factory()->create();
        
    return new OrderShippedMail($order, $tracker);
});
```

By default, mailbook will roll back any database changes that are executed while rendering the mails. If you don't have a
database connection, or if you don't want it to rollback you can disable it in the config.

```php
'database_rollback' => false,
```

## Customization

You can publish the config file with:

```bash
php artisan vendor:publish --tag="mailbook-config"
```

This is the contents of the published config file:

```php
return [
    'route_prefix' => '/mailbook',
    'database_rollback' => true,
    'display_preview' => true,
    'refresh_button' => true,
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="mailbook-views"
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/Xammie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Max Hoogenbosch](https://github.com/Xammie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
