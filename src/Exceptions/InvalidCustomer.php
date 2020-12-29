<?php

namespace Starfolksoftware\PaystackSubscription\Exceptions;

use Exception;

class InvalidCustomer extends Exception
{
    /**
     * Create a new InvalidCustomer instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return static
     */
    public static function notYetCreated($owner)
    {
        return new static(class_basename($owner).' is not a Paystack customer yet. See the createAsPaystackCustomer method.');
    }
}
