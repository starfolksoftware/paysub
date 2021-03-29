<?php

namespace StarfolkSoftware\Paysub\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Notifications\Notifiable;
use StarfolkSoftware\Paysub\Traits\CanBeBilled;

class User extends Model {
    use CanBeBilled, Notifiable;

    public function paystackEmail(): string {
        return 'user@example.com';
    }

    public function invoiceMailables(): array {
        return [
            'user@example.com'
        ];
    }
}