<?php

namespace Starfolksoftware\Subscription;

use Illuminate\Support\ServiceProvider;
use Starfolksoftware\Subscription\Commands\SubscriptionCommand;

class SubscriptionServiceProvider extends ServiceProvider
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

            $migrationFileName = 'create_paystack_subscription_table.php';
            if (! $this->migrationFileExists($migrationFileName)) {
                $this->publishes([
                    __DIR__ . "/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $migrationFileName),
                ], 'migrations');
            }

            $this->commands([
                SubscriptionCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'paystack-subscription');
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
