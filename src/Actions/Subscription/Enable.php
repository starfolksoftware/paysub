<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Subscription;

use StarfolkSoftware\PaystackSubscription\Api\Subscription;

class Enable
{
    public function execute($code, $token)
    {
        $subscription = new Subscription();

        return $subscription->enable($code, $token);
    }
}
