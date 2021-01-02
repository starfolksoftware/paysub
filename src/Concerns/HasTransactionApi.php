<?php

namespace StarfolkSoftware\Paysub\Concerns;

use InvalidArgumentException;
use StarfolkSoftware\Paysub\Utilities\CurlRequest;

trait HasTransactionApi
{
    public static string $classUrl = 'https://api.paystack.co/transaction/';

    /**
     * Get the subscriber valid email to be used with paystack
     *
     * @return string
     */
    abstract public function paystackEmail(): string;

    public function initialize(string $amount)
    {
        return (new CurlRequest())(
            'post',
            self::$classUrl.'initialize',
            [
                'email' => $this->paystackEmail(),
                'amount' => $amount,
            ]
        );
    }

    public function verify($reference)
    {
        if (! $reference) {
            throw new InvalidArgumentException('reference is not provided');
        }
        
        return (new CurlRequest())(
            'get',
            self::$classUrl.$reference
        );
    }

    public function charge(string $amount, string $email, string $auth_code)
    {
        return (new CurlRequest())(
            'post',
            self::$classUrl.'charge_authorization',
            [
                'email' => $email,
                'amount' => $amount,
                'authorization_code' => $auth_code,
            ]
        );
    }
}
