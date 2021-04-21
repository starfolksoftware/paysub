<?php

namespace StarfolkSoftware\Paysub;

use Illuminate\Support\ServiceProvider;

// use StarfolkSoftware\Paysub\Commands\SubscriptionCommand;

class PaysubServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/paysub.php' => config_path('paysub.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/paysub'),
            ], 'views');

            // $this->publishes([
            //     __DIR__ . "/../resources/stubs/create_paysub_tables.php.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_paysub_tables.php'),
            // ], 'migrations');

            // $this->commands([
            //     SubscriptionCommand::class,
            // ]);
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'paysub');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'paysub');

        if (Paysub::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/paysub.php', 'paysub');
        $this->app->register(EventServiceProvider::class);
    }
}
