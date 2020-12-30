<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Customer;

use StarfolkSoftware\PaystackSubscription\Core\Customer;

class RetrieveAll
{
    public function execute()
    {
        $customer = new Customer();

        return $customer->all();
    }
}
