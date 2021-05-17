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

    'subscriber_model' => env('PAYSUB_SUBSCRIPTION_MODEL', App\Models\User::class),

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
    'subscription_items_table_name' => env('PAYSUB_SUBSCRIPTION_ITEMS_TABLE', 'paysub_subscription_items'),
    'plan_table_name' => env('PAYSUB_PLAN_TABLE', 'paysub_plans'),
    'authorization_table_name' => env('PAYSUB_AUTHORIZATION_TABLE', 'paysub_authorizations'),
    'invoice_table_name' => env('PAYSUB_INVOICE_TABLE', 'paysub_invoices'),
    'payment_table_name' => env('PAYSUB_PAYMENT_TABLE', 'paysub_payments'),
    'card_table_name' => env('PAYSUB_CARD_TABLE', 'paysub_cards'),
    'auth_table_name' => env('PAYSUB_AUTH_TABLE', 'paysub_authorizations'),
    'feature_table_name' => env('PAYSUB_FEAT_TABLE', 'paysub_features'),
    'feature_plan_table_name' => env('PAYSUB_FEAT_PLAN_TABLE', 'paysub_features_plans'),
    'usage_table_name' => env('PAYSUB_USAGE_TABLE', 'paysub_usages'),

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
    | Auto-generate invoices
    |--------------------------------------------------------------------------
    |
    | If set to `true`, invoice will be automatically generated for the subscriber 
    | according to their subscription details. 
    |
    */

    'auto_invoice' => true,

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
