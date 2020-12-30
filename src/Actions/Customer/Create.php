<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Customer;

use StarfolkSoftware\PaystackSubscription\Api\Customer;

class Create
{
    public function execute(string $email, array $fields = [])
    {
        $customer = new Customer();

        return $customer->email($email)->create($fields);
    }
}
