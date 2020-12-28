<?php

namespace Starfolksoftware\PaystackSubscription;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

class PaystackSubscription
{
    /**
     * The library version.
     *
     * @var string
     */
    const VERSION = '0.0.1';

    /**
     * The Stripe API version.
     *
     * @var string
     */
    const PAYSTACK_VERSION = '2020-12-27';

    /**
     * The custom currency formatter.
     *
     * @var callable
     */
    protected static $formatCurrencyUsing;

    /**
     * Indicates if Cashier migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * Indicates if Cashier routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * Indicates if Cashier will mark past due subscriptions as inactive.
     *
     * @var bool
     */
    public static $deactivatePastDue = true;

    /**
     * Get the billable entity instance by Stripe ID.
     *
     * @param  string  $paystackId
     * @return \Starfolksoftware\PaystackSubscription\Billable|null
     */
    public static function findBillable($paystackId)
    {
        if ($paystackId === null) {
            return;
        }

        $model = config('paystack-subscription.subscriber_model');

        return (new $model)->where('stripe_id', $paystackId)->first();
    }

    /**
     * Get the default Stripe API options.
     *
     * @param  array  $options
     * @return array
     */
    public static function paystackOptions(array $options = [])
    {
        return array_merge([
            'api_key' => config('paystack-subscription.secret'),
            'paystack_version' => static::PAYSTACK_VERSION,
        ], $options);
    }

    /**
     * Set the custom currency formatter.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function formatCurrencyUsing(callable $callback)
    {
        static::$formatCurrencyUsing = $callback;
    }

    /**
     * Format the given amount into a displayable currency.
     *
     * @param  int  $amount
     * @param  string|null  $currency
     * @param  string|null  $locale
     * @return string
     */
    public static function formatAmount($amount, $currency = null, $locale = null)
    {
        if (static::$formatCurrencyUsing) {
            return call_user_func(static::$formatCurrencyUsing, $amount, $currency);
        }

        $money = new Money($amount, new Currency(strtoupper($currency ?? config('paystack-subscription.currency'))));

        $locale = $locale ?? config('paystack-subscription.currency_locale');

        $numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return $moneyFormatter->format($money);
    }

    /**
     * Configure Cashier to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static;
    }

    /**
     * Configure Cashier to not register its routes.
     *
     * @return static
     */
    public static function ignoreRoutes()
    {
        static::$registersRoutes = false;

        return new static;
    }

    /**
     * Configure Cashier to maintain past due subscriptions as active.
     *
     * @return static
     */
    public static function keepPastDueSubscriptionsActive()
    {
        static::$deactivatePastDue = false;

        return new static;
    }
}
