<?php

namespace Starfolksoftware\PaystackSubscription\Actions\Customer;

use Starfolksoftware\PaystackSubscription\Customer;

class Create
{
    public function execute(array $options)
    {
        $customer = new Customer();

        return $customer
            ->email($options['email'])
            ->firstName($options['first_name'] ?? '')
            ->lastName($options['last_name'] ?? '')
            ->create();
    }
}
