<?php

namespace StarfolkSoftware\Paysub\Concerns;

use InvalidArgumentException;
use StarfolkSoftware\Paysub\Utilities\CurlRequest;

trait HasPaystackTransactionApi
{
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
            'https://api.paystack.co/transaction/initialize',
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
            'https://api.paystack.co/transaction/verify/'.$reference
        );
    }

    public function chargeUsingPaystack(string $amount, string $email, string $auth_code)
    {
        return (new CurlRequest())(
            'post',
            'https://api.paystack.co/transaction/charge_authorization',
            [
                'email' => $email,
                'amount' => $amount,
                'authorization_code' => $auth_code,
            ]
        );
    }
}
