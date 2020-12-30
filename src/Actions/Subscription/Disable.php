<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Subscription;

use StarfolkSoftware\PaystackSubscription\Api\Subscription;

class Disable
{
    public function execute($code, $token)
    {
        $subscription = new Subscription();

        return $subscription->disable($code, $token);
    }
}
