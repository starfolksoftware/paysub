<?php

namespace Starfolksoftware\PaystackSubscription\Actions\Customer;

use Starfolksoftware\PaystackSubscription\PaystackCustomer as Customer;

class Update
{
    public function execute(array $options, $paystack_code)
    {
        $customer = new Customer();

        if ($options['first_name']) {
            $customer->firstName($options['first_name']);
        }

        if ($options['last_name']) {
            $customer->lastName($options['last_name']);
        }

        if ($options['phone']) {
            $customer->phone($options['phone']);
        }

        return $customer
            ->apiKey($options['api_key'])
            ->code($paystack_code)
            ->update();
    }
}
