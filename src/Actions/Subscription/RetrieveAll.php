<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Subscription;

use StarfolkSoftware\PaystackSubscription\Api\Subscription;

class RetrieveAll
{
    public function execute()
    {
        $subscription = new Subscription();

        return $subscription->all();
    }
}
