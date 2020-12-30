<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Subscription;

use StarfolkSoftware\PaystackSubscription\Core\Subscription;

class Retrieve
{
    public function execute(string $identifier)
    {
        $subscription = new Subscription();

        return $subscription->retrieve($identifier);
    }
}
