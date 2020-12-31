<?php

namespace StarfolkSoftware\Paysub\Actions\Customer;

use StarfolkSoftware\Paysub\Api\Customer;

class RetrieveAll
{
    public function execute()
    {
        $customer = new Customer();

        return $customer->all();
    }
}
