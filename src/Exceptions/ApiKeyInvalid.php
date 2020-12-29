<?php

namespace StarfolkSoftware\PaystackSubscription\Exceptions;

use Exception;

class ApiKeyInvalid extends Exception
{
    /**
     * Create a new PaystackIsNull instance.
     *
     *
     * @return static
     */
    public static function isNull()
    {
        return new static('Paystack api key has a null value.');
    }
}
