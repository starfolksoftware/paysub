<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Subscription;

use StarfolkSoftware\PaystackSubscription\Core\Subscription;

class Create
{
    public function execute(array $fields = [])
    {
        $subscription = new Subscription();

        return $subscription->create($fields);
    }
}
