<?php

namespace StarfolkSoftware\Paysub\Actions\Customer;

use StarfolkSoftware\Paysub\Api\Customer;

class Update
{
    public function execute($paystack_code, array $fields)
    {
        $customer = new Customer();

        return $customer->paystackCode($paystack_code)->update($fields);
    }
}
