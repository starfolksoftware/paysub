# Provides an inteface to paystack's subscription service

[![Latest Version on Packagist](https://img.shields.io/packagist/v/starfolksoftware/paystack-subscription.svg?style=flat-square)](https://packagist.org/packages/starfolksoftware/paystack-subscription)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/starfolksoftware/paystack-subscription/run-tests?label=tests)](https://github.com/starfolksoftware/paystack-subscription/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/starfolksoftware/paystack-subscription.svg?style=flat-square)](https://packagist.org/packages/starfolksoftware/paystack-subscription)


This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require starfolksoftware/paystack-subscription
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="StarfolkSoftware\Paysub\PaystackSubscriptionServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="StarfolkSoftware\Paysub\PaystackSubscriptionServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$paystack-subscription = new StarfolkSoftware\Paysub();
echo $paystack-subscription->echoPhrase('Hello, StarfolkSoftware!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Road Map

- [ ] Invoice period(invoice starts at, invoice ends at)

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Faruk Nasir](https://github.com/frknasir)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
