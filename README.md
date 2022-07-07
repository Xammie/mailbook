# Mailbook

[![Latest Version on Packagist](https://img.shields.io/packagist/v/xammie/mailbook.svg?style=flat-square)](https://packagist.org/packages/xammie/mailbook)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/xammie/mailbook/run-tests?label=tests)](https://github.com/xammie/mailbook/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/xammie/mailbook/Check%20&%20fix%20styling?label=code%20style)](https://github.com/xammie/mailbook/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/xammie/mailbook.svg?style=flat-square)](https://packagist.org/packages/xammie/mailbook)

Laravel Mailbook lets you explore your mailables.

## Installation

You can install the package via composer:

```bash
composer require --dev xammie/mailbook
```

## Usage

Before you can view transactional emails in the mailbook you have to register them.

```php
// This will use dependency injection if your mailable has parameters
Mailbook::add(\App\Mai\VerificationMail::class);

// Use a closure to customize the parameters of the mail instance
Mailbook::add(function () {
    $user = User::factory()->make();

    return new VerificationMail($user, '/example/url')
});
```

## Customization

You can publish the config file with:

```bash
php artisan vendor:publish --tag="mailbook-config"
```

This is the contents of the published config file:

```php
return [];
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
