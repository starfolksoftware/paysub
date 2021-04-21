<?php

namespace StarfolkSoftware\Paysub\Exceptions;

use Exception;

class ApiKeyInvalid extends Exception
{
    final public function __construct()
    {
    }
    
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
