<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Customer;

use StarfolkSoftware\PaystackSubscription\Api\Customer;

class Retrieve
{
    public function execute(string $identifier)
    {
        $customer = new Customer();

        return $customer->retrieve($identifier);
    }
}
