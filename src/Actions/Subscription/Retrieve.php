<?php

namespace StarfolkSoftware\Paysub\Actions\Subscription;

use StarfolkSoftware\Paysub\Api\Subscription;

class Retrieve
{
    public function execute(string $identifier)
    {
        $subscription = new Subscription();

        return $subscription->retrieve($identifier);
    }
}
