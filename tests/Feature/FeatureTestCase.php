<?php

namespace StarfolkSoftware\Paysub\Tests\Feature;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Schema\Blueprint;
use StarfolkSoftware\Paysub\Tests\Fixtures\User;
use StarfolkSoftware\Paysub\Tests\TestCase;

abstract class FeatureTestCase extends TestCase {
    public function setUp(): void {
        parent::setUp();

        Eloquent::unguard();

        $this->setUpDatabase();

        $this->loadLaravelMigrations();

        $this->artisan('migrate')->run();
    }

    protected function createCustomer($description = 'faruk', $options = []): User {
        return User::create(array_merge([
            'email' => "{$description}@starfolksoftware.test",
            'name' => 'Starfolk Software',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
        ], $options));
    }

    protected function setUpDatabase() {
        $this->app['db']->connection()->getSchemaBuilder()->create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->timestamp('trial_ends_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
}