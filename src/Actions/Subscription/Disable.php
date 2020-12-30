<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Subscription;

use StarfolkSoftware\PaystackSubscription\Core\Subscription;

class Disable
{
    public function execute($code, $token)
    {
        $subscription = new Subscription();

        return $subscription->disable($code, $token);
    }
}
