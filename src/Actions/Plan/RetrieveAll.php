<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Plan;

use StarfolkSoftware\PaystackSubscription\Core\Plan;

class RetrieveAll
{
    public function execute()
    {
        $plan = new Plan();

        return $plan->all();
    }
}
