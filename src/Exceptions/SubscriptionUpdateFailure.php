<?php

namespace StarfolkSoftware\Paysub\Exceptions;

use Exception;

class SubscriptionUpdateFailure extends Exception
{
    /**
     * Create a new InvoiceCreationError instance.
     *
     *
     * @return static
     */
    public static function default()
    {
        return new static('subscription update failed.');
    }
}