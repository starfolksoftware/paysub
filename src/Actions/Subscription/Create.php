<?php

namespace StarfolkSoftware\Paysub\Actions\Subscription;

use StarfolkSoftware\Paysub\Api\Subscription;

class Create
{
    public function execute(array $fields = [])
    {
        $subscription = new Subscription();

        return $subscription->create($fields);
    }
}
