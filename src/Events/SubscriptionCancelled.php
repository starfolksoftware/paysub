<?php

namespace StarfolkSoftware\Paysub\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use StarfolkSoftware\Paysub\Models\Subscription;

class SubscriptionCancelled {
    use Dispatchable, SerializesModels;

    public $subscription;

    public function __construct(Subscription $subscription) {
        $this->subscription = $subscription;
    }
}
