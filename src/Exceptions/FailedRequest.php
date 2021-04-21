<?php

namespace StarfolkSoftware\Paysub\Exceptions;

use Exception;

class FailedRequest extends Exception
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
    public static function default($message)
    {
        return new static($message);
    }
}
