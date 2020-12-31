<?php

namespace StarfolkSoftware\Paysub\Exceptions;

use Exception;

class InvalidSubscription extends Exception
{
    /**
     * Create a new InvalidSubscription instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return static
     */
    public static function notYetCreated($owner)
    {
        return new static(class_basename($owner).' is not a Paystack subscription.');
    }
}
