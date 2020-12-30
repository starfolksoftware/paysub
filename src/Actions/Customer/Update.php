<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Customer;

use StarfolkSoftware\PaystackSubscription\Core\Customer;

class Update
{
    public function execute($paystack_code, array $fields)
    {
        $customer = new Customer();

        return $customer->paystackCode($paystack_code)->update($fields);
    }
}
