<?php

namespace StarfolkSoftware\Paysub\Api;

use InvalidArgumentException;
use StarfolkSoftware\Paysub\Utilities\CurlRequest;

class Transaction
{
    use HasAttributes;

    public static string $classUrl = 'https://api.paystack.co/transaction/';

    public function initialize(array $fields)
    {
        if (! $fields['email']) {
            throw new InvalidArgumentException('email is not provided');
        }

        if (! $fields['amount']) {
            throw new InvalidArgumentException('amount is not provided');
        }

        $this->setAttributes((new CurlRequest())(
            'post',
            self::$classUrl.'initialize',
            $fields
        ));

        return $this;
    }

    public function verify($reference)
    {
        if (! $reference) {
            throw new InvalidArgumentException('reference is not provided');
        }
        
        $this->setAttributes((new CurlRequest())(
            'get',
            self::$classUrl.$reference
        ));

        return $this;
    }

    public function retrieve(int $id)
    {
        if (! $id) {
            throw new InvalidArgumentException('ID is not provided');
        }
        
        $this->setAttributes((new CurlRequest())(
            'get',
            self::$classUrl.$id
        ));

        return $this;
    }

    public static function all(array $fields)
    {
        return collect((new CurlRequest())(
            'get',
            self::$classUrl,
            $fields
        ));
    }

    public function charge(array $fields)
    {
        if (! $fields['email']) {
            throw new InvalidArgumentException('email is not provided');
        }

        if (! $fields['amount']) {
            throw new InvalidArgumentException('amount is not provided');
        }

        if (! $fields['authorization_code']) {
            throw new InvalidArgumentException('authorization code is not provided');
        }

        $this->setAttributes((new CurlRequest())(
            'post',
            self::$classUrl.'charge_authorization',
            $fields
        ));

        return $this;
    }
}
