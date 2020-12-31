<?php

namespace StarfolkSoftware\Paysub\Actions\Plan;

use StarfolkSoftware\Paysub\Api\Plan;

class RetrieveAll
{
    public function execute()
    {
        $plan = new Plan();

        return $plan->all();
    }
}
