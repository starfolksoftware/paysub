<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Subscription;

use StarfolkSoftware\PaystackSubscription\Api\Subscription;

class Create
{
    public function execute(array $fields = [])
    {
        $subscription = new Subscription();

        return $subscription->create($fields);
    }
}
