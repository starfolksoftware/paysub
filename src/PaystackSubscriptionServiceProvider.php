<?php

namespace Starfolksoftware\PaystackSubscription;

use Illuminate\Support\ServiceProvider;
use Starfolksoftware\PaystackSubscription\PaystackSubscription;
use Illuminate\Support\Facades\Route;
// use Starfolksoftware\PaystackSubscription\Commands\SubscriptionCommand;

class PaystackSubscriptionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/paystack-subscription.php' => config_path('paystack-subscription.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/paystack-subscription'),
            ], 'views');

            $mFileNames = array(
                'create_subscriber_columns.php',
                'create_subscriptions_table.php',
                'create_subscription_items_table.php'
            );

            collect($mFileNames)->each(function ($mFileName) {
                if (! $this->migrationFileExists($mFileName)) {
                    $this->publishes([
                        __DIR__ . "/../database/migrations/{$mFileName}.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $mFileName),
                    ], 'migrations');
                }
            });

            // $this->commands([
            //     SubscriptionCommand::class,
            // ]);
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'paystack-subscription');
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');

        if (PaystackSubscription::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        if (PaystackSubscription::$registersRoutes) {
            Route::group([
                'prefix' => config('paystack-subscription.path'),
                'namespace' => 'Laravel\Cashier\Http\Controllers',
                'as' => 'paystack-subscription.',
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/paystack-subscription.php');
            });
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/paystack-subscription.php', 'paystack-subscription');
    }

    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }
}
