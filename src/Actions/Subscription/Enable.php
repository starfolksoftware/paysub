<?php

namespace StarfolkSoftware\Paysub\Actions\Subscription;

use StarfolkSoftware\Paysub\Api\Subscription;

class Enable
{
    public function execute($code, $token)
    {
        $subscription = new Subscription();

        return $subscription->enable($code, $token);
    }
}
