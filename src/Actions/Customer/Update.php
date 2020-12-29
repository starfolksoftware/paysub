<?php

namespace Starfolksoftware\PaystackSubscription\Actions\Customer;

use Starfolksoftware\PaystackSubscription\PaystackCustomer as Customer;

class Update
{
    public function execute($paystack_code, array $fields, array $options)
    {
        $customer = new Customer();

        if ($fields['first_name']) {
            $customer->firstName($fields['first_name']);
        }

        if ($fields['last_name']) {
            $customer->lastName($fields['last_name']);
        }

        if ($fields['phone']) {
            $customer->phone($fields['phone']);
        }

        return $customer
            ->code($paystack_code)
            ->update();
    }
}
