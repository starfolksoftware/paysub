<?php

namespace StarfolkSoftware\Paysub\Actions\Subscription;

use StarfolkSoftware\Paysub\Api\Subscription;

class Disable
{
    public function execute($code, $token)
    {
        $subscription = new Subscription();

        return $subscription->disable($code, $token);
    }
}
