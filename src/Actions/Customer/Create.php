<?php

namespace StarfolkSoftware\Paysub\Actions\Customer;

use StarfolkSoftware\Paysub\Api\Customer;

class Create
{
    public function execute(string $email, array $fields = [])
    {
        $customer = new Customer();

        return $customer->email($email)->create($fields);
    }
}
