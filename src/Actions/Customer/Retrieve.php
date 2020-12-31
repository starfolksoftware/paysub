<?php

namespace StarfolkSoftware\Paysub\Actions\Customer;

use StarfolkSoftware\Paysub\Api\Customer;

class Retrieve
{
    public function execute(string $identifier)
    {
        $customer = new Customer();

        return $customer->retrieve($identifier);
    }
}
