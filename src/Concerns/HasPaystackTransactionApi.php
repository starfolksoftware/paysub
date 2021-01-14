<?php

namespace StarfolkSoftware\Paysub\Concerns;

use InvalidArgumentException;
use StarfolkSoftware\Paysub\Utilities\CurlRequest;

trait HasPaystackTransactionApi
{
    public static string $paystackTransUrl = 'https://api.paystack.co/transaction/';

    /**
     * Get the subscriber valid email to be used with paystack
     *
     * @return string
     */
    abstract public function paystackEmail(): string;

    public function initializePaystackPayment(string $amount)
    {
        return (new CurlRequest())(
            'post',
            self::$paystackTransUrl.'initialize',
            [
                'email' => $this->paystackEmail(),
                'amount' => $amount,
            ]
        );
    }

    public function verifyPaystackPayment($reference)
    {
        if (! $reference) {
            throw new InvalidArgumentException('reference is not provided');
        }
        
        return (new CurlRequest())(
            'get',
            self::$paystackTransUrl.'verify/'.$reference
        );
    }

    public function chargeUsingPaystack(string $amount, string $email, string $auth_code)
    {
        return (new CurlRequest())(
            'post',
            self::$paystackTransUrl.'charge_authorization',
            [
                'email' => $email,
                'amount' => $amount,
                'authorization_code' => $auth_code,
            ]
        );
    }
}
