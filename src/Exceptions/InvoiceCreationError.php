<?php

namespace StarfolkSoftware\Paysub\Exceptions;

use Exception;

class InvoiceCreationError extends Exception
{
    final public function __construct()
    {
    }
    
    /**
     * Create a new InvoiceCreationError instance.
     *
     *
     * @return static
     */
    public static function hasOpenInvoice($owner)
    {
        return new static(get_class($owner).' has an open invoice.');
    }

    /**
     * Create a new InvoiceCreationError instance.
     *
     *
     * @return static
     */
    public static function multipleOpenInvoice($owner)
    {
        return new static(get_class($owner).' has more than one open invoice.');
    }
}
