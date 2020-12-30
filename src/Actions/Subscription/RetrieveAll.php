<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Subscription;

use StarfolkSoftware\PaystackSubscription\Core\Subscription;

class RetrieveAll
{
    public function execute()
    {
        $subscription = new Subscription();

        return $subscription->all();
    }
}
