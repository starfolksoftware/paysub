<?php

namespace StarfolkSoftware\Paysub\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use StarfolkSoftware\Paysub\Traits\CanBeBilled;

class Subscriber extends Model {
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