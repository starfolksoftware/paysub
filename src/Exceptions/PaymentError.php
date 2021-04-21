<?php

namespace StarfolkSoftware\Paysub\Exceptions;

use Exception;

class PaymentError extends Exception
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
    public static function default($message)
    {
        return new static($message);
    }

    /**
     * Create a new InvoiceCreationError instance.
     *
     *
     * @return static
     */
    public static function invoiceIsNull()
    {
        return new static('provided invoice is null');
    }

    /**
     * Create a new InvoiceCreationError instance.
     *
     *
     * @return static
     */
    public static function paystackAuthCodeIsNull()
    {
        return new static('paystack authorization code is null');
    }

    /**
     * Create a new InvoiceCreationError instance.
     *
     *
     * @return static
     */
    public static function paystackEmailNotDefined()
    {
        return new static('paystack email is not defined. Please implement abstract function paystackEmail()');
    }
}
