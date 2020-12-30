<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Plan;

use StarfolkSoftware\PaystackSubscription\Api\Plan;

class Retrieve
{
    public function execute(string $identifier)
    {
        $plan = new Plan();

        return $plan->retrieve($identifier);
    }
}
