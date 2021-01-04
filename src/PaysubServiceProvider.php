<?php

namespace StarfolkSoftware\Paysub;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

// use StarfolkSoftware\Paysub\Commands\SubscriptionCommand;

class PaysubServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/paysub.php' => config_path('paysub.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/paysub'),
            ], 'views');

            $mFileNames = [
                'create_subscriber_columns.php',
                'create_subscriptions_table.php',
            ];

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

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'paysub');
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');

        if (Paysub::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        if (Paysub::$registersRoutes) {
            Route::group([
                'prefix' => config('paysub.path'),
                'namespace' => 'Laravel\Cashier\Http\Controllers',
                'as' => 'paysub.',
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/paysub.php');
            });
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/paysub.php', 'paysub');
        $this->app->register(EventServiceProvider::class);
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
