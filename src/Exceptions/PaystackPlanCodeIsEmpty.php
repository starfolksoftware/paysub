<?php

namespace StarfolkSoftware\PaystackSubscription\Exceptions;

use Exception;

class PaystackPlanCodeIsEmpty extends Exception
{
    /**
     * Create a new PaystackIsNull instance.
     *
     *
     * @return static
     */
    public static function isNotSet()
    {
        return new static('Paystack plan code is not set.');
    }
}
