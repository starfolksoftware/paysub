<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Customer;

use StarfolkSoftware\PaystackSubscription\Core\Customer;

class Retrieve
{
    public function execute(string $identifier)
    {
        $customer = new Customer();

        return $customer->retrieve($identifier);
    }
}
