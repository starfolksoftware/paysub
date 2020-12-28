<?php

namespace Starfolksoftware\PaystackSubscription\Actions\Customer;

use Starfolksoftware\PaystackSubscription\PaystackCustomer as Customer;

class Read {
    public function execute(array $options, $paystack_code = "") {
        $customer = new Customer();
        return $customer
            ->apiKey($options['api_key'])
            ->email($options['email'])
            ->code($paystack_code)
            ->find();
    }
}
