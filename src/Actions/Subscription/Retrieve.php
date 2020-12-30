<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Subscription;

use StarfolkSoftware\PaystackSubscription\Api\Subscription;

class Retrieve
{
    public function execute(string $identifier)
    {
        $subscription = new Subscription();

        return $subscription->retrieve($identifier);
    }
}
