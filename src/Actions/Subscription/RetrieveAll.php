<?php

namespace StarfolkSoftware\Paysub\Actions\Subscription;

use StarfolkSoftware\Paysub\Api\Subscription;

class RetrieveAll
{
    public function execute()
    {
        $subscription = new Subscription();

        return $subscription->all();
    }
}
