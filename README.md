# Do not user in production

[![Latest Version on Packagist](https://img.shields.io/packagist/v/starfolksoftware/paysub.svg?style=flat-square)](https://packagist.org/packages/starfolksoftware/paysub)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/starfolksoftware/paysub/run-tests?label=tests)](https://github.com/starfolksoftware/paysub/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/starfolksoftware/paysub.svg?style=flat-square)](https://packagist.org/packages/starfolksoftware/paysub)

Paysub offers basic subscription management for laravel applications. Currently supports only paystack for payment.

## Developer Preview

This project is in Developer Preview stage, All API's might change without warning and no guarantees are given about stability. Do not use it in production.

## Installation

You can install the package via composer:

```bash
composer require starfolksoftware/paysub
```

Run the migrations with:

```bash
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="StarfolkSoftware\Paysub\PaysubServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paystack Keys
    |--------------------------------------------------------------------------
    |
    | The Paystack publishable key and secret key give you access to Paystack's
    | API. The "publishable" key is typically used when interacting with
    | Paystack.js while the "secret" key accesses private API endpoints.
    |
    */

    'key' => env('PAYSTACK_KEY'),

    'secret' => env('PAYSTACK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Paystack Subscription Path
    |--------------------------------------------------------------------------
    |
    | This is the base URI path where Paystack Subscription's views, such as the payment
    | verification screen, will be available from. You're free to tweak
    | this path according to your preferences and application design.
    |
    */

    'path' => env('PAYSUB_SUBSCRIPTION_PATH', 'paysub'),

    /*
    |--------------------------------------------------------------------------
    | Paystack Subscription Model
    |--------------------------------------------------------------------------
    |
    | This is the model in your application that implements the Billable trait
    | provided by Paystack Subscription. It will serve as the primary model you use while
    | interacting with Paystack Subscription related methods, subscriptions, and so on.
    |
    */

    'subscriber_model' => env('PAYSUB_SUBSCRIPTION_MODEL', class_exists(App\Models\User::class) ? App\Models\User::class : App\User::class),

    /*
    |--------------------------------------------------------------------------
    | Tables' names
    |--------------------------------------------------------------------------
    |
    | Define the package's table names here
    |
    */
    'subscriber_table_name' => env('PAYSUB_SUBSCRIBER_TABLE', 'users'),
    'subscription_table_name' => env('PAYSUB_SUBSCRIPTION_TABLE', 'paysub_subscriptions'),
    'plan_table_name' => env('PAYSUB_PLAN_TABLE', 'paysub_plans'),
    'authorization_table_name' => env('PAYSUB_AUTHORIZATION_TABLE', 'paysub_authorizations'),
    'invoice_table_name' => env('PAYSUB_INVOICE_TABLE', 'paysub_invoices'),
    'payment_table_name' => env('PAYSUB_PAYMENT_TABLE', 'paysub_payments'),
    'card_table_name' => env('PAYSUB_CARD_TABLE', 'paysub_cards'),
    'auth_table_name' => env('PAYSUB_AUTH_TABLE', 'paysub_authorizations'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used when generating charges
    | from your application. Of course, you are welcome to use any of the
    | various world currencies that are currently supported via Paystack.
    |
    */

    'currency' => env('PAYSUB_CURRENCY', 'NGN'),

    /*
    |--------------------------------------------------------------------------
    | Taxes
    |--------------------------------------------------------------------------
    |
    | Tax definitions for invoice. 
    |
    */

    'invoice_taxes' => [
        // ['name' => 'VAT', 'percentage' => 7.5]
    ],

    /*
    |--------------------------------------------------------------------------
    | Company Contact Detail
    |--------------------------------------------------------------------------
    |
    | Contact details for the billable. This will be shown in invoices sent
    | to billables.
    |
    */
    'contact_detail' => [
        'vendor' => '',
        'street' => '',
        'location' => '',
        'phone' => '',
        'url' => '',
        'vatInfo' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Paper Size
    |--------------------------------------------------------------------------
    |
    | This option is the default paper size for all invoices generated using
    | Paystack Subscription. You are free to customize this settings based on the usual
    | paper size used by the customers using your Laravel applications.
    |
    | Supported sizes: 'letter', 'legal', 'A4'
    |
    */

    'paper' => env('PAYSUB_PAPER', 'letter'),

    /*
    |--------------------------------------------------------------------------
    | Paystack Logger
    |--------------------------------------------------------------------------
    |
    | This setting defines which logging channel will be used by the Paystack
    | library to write log messages. You are free to specify any of your
    | logging channels listed inside the "logging" configuration file.
    |
    */

    'logger' => env('PAYSUB_LOGGER'),
];
```

## Usage

```php
<?php

...
StarfolkSoftware\Paysub\Traits\CanBeBilled;

class User extends Model {
    use CanBeBilled;

    public function paystackEmail(): string {
        return 'user@example.com';
    }

    public function invoiceMailables(): array {
        return [
            'user@example.com'
        ];
    }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Road Map

- [x] Multiple plans support
- [x] Invoice period(invoice starts at, invoice ends at)
- [ ] Coupons handling

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Faruk Nasir](https://github.com/frknasir)
- [Spatie](https://github.com/spatie)
- [Laravel Cashier Contributors](https://github.com/laravel/cashier-stripe)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
