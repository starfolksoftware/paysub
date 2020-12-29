<?php

namespace Starfolksoftware\PaystackSubscription\Exceptions;

use Exception;

class FailedRequest extends Exception
{
    /**
     * Create a new PaystackIsNull instance.
     *
     *
     * @return static
     */
    public static function default($message)
    {
        return new static($message);
    }
}
