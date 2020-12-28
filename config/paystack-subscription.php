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

    'path' => env('PAYSTACK_SUBSCRIPTION_PATH', 'paystack-subscription'),

    /*
    |--------------------------------------------------------------------------
    | Paystack Webhooks
    |--------------------------------------------------------------------------
    |
    | Your Paystack webhook secret is used to prevent unauthorized requests to
    | your Paystack webhook handling controllers. The tolerance setting will
    | check the drift between the current time and the signed request's.
    |
    */

    'webhook' => [
        'secret' => env('PAYSTACK_WEBHOOK_SECRET'),
        'tolerance' => env('PAYSTACK_WEBHOOK_TOLERANCE', 300),
    ],

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

    'subscriber_model' => env('PAYSTACK_SUBSCRIPTION_MODEL', class_exists(App\Models\User::class) ? App\Models\User::class : App\User::class),

    /*
    |--------------------------------------------------------------------------
    | Paystack Subscription Model
    |--------------------------------------------------------------------------
    |
    | This is the table in your application that holds the subscribers. 
    |
    */
    'subscriber_table_name' => env('PAYSTACK_SUBSCRIPTION_TABLE_NAME', 'users'),

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

    'currency' => env('PAYSTACK_SUBSCRIPTION_CURRENCY', 'ngn'),

    /*
    |--------------------------------------------------------------------------
    | Currency Locale
    |--------------------------------------------------------------------------
    |
    | This is the default locale in which your money values are formatted in
    | for display. To utilize other locales besides the default en locale
    | verify you have the "intl" PHP extension installed on the system.
    |
    */

    'currency_locale' => env('PAYSTACK_SUBSCRIPTION_CURRENCY_LOCALE', 'en-GB'),

    /*
    |--------------------------------------------------------------------------
    | Payment Confirmation Notification
    |--------------------------------------------------------------------------
    |
    | If this setting is enabled, Paystack Subscription will automatically notify customers
    | whose payments require additional verification. You should listen to
    | Paystack's webhooks in order for this feature to function correctly.
    |
    */

    'payment_notification' => env('PAYSTACK_SUBSCRIPTION_PAYMENT_NOTIFICATION'),

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

    'paper' => env('PAYSTACK_SUBSCRIPTION_PAPER', 'letter'),

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

    'logger' => env('PAYSTACK_SUBSCRIPTION_LOGGER'),
];
