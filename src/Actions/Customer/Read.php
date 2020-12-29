<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Customer;

use StarfolkSoftware\PaystackSubscription\Customer;

class Read
{
    public function execute(string $identifier)
    {
        $customer = new Customer();

        return $customer->find($identifier);
    }
}
