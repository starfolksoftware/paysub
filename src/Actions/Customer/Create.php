<?php

namespace Starfolksoftware\PaystackSubscription\Actions\Customer;

use Starfolksoftware\PaystackSubscription\PaystackCustomer as Customer;

class Create
{
    public function execute(array $options)
    {
        $customer = new Customer();

        return $customer
            ->apiKey($options['api_key'])
            ->email($options['email'])
            ->firstName($options['first_name'] ?? '')
            ->lastName($options['last_name'] ?? '')
            ->create();
    }
}
