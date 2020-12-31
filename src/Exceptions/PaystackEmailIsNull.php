<?php

namespace StarfolkSoftware\Paysub\Exceptions;

use Exception;

class PaystackEmailIsNull extends Exception
{
    /**
     * Create a new PaystackIsNull instance.
     *
     *
     * @return static
     */
    public static function isNull()
    {
        return new static('Paystack email has a null value.');
    }
}
