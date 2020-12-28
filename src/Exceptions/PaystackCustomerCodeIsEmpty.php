<?php

namespace Starfolksoftware\PaystackSubscription\Exceptions;

use Exception;

class PaystackCustomerCodeIsEmpty extends Exception {
    /**
     * Create a new PaystackIsNull instance.
     *
     * 
     * @return static
     */
    public static function isNotSet()
    {
        return new static('Paystack customer code is not set.');
    }
}