<?php

namespace Starfolksoftware\PaystackSubscription\Actions\Customer;

use Starfolksoftware\PaystackSubscription\Customer;

class Read
{
    public function execute(array $options, $paystack_code = "")
    {
        $customer = new Customer();

        return $customer
            ->email($options['email'])
            ->code($paystack_code)
            ->find();
    }
}
