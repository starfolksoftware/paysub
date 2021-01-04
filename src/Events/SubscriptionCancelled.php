<?php

namespace StarfolkSoftware\Paysub\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use StarfolkSoftware\Paysub\Models\Subscription;

class SubscriptionCancelled
{
    use Dispatchable;
    use SerializesModels;

    public $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }
}
