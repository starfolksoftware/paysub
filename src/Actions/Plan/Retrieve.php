<?php

namespace StarfolkSoftware\Paysub\Actions\Plan;

use StarfolkSoftware\Paysub\Api\Plan;

class Retrieve
{
    public function execute(string $identifier)
    {
        $plan = new Plan();

        return $plan->retrieve($identifier);
    }
}
