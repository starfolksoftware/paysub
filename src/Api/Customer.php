<?php

namespace StarfolkSoftware\PaystackSubscription\Api;

use InvalidArgumentException;
use StarfolkSoftware\PaystackSubscription\Exceptions\PaystackCustomerCodeIsEmpty;
use StarfolkSoftware\PaystackSubscription\Exceptions\PaystackEmailIsNull;
use StarfolkSoftware\PaystackSubscription\Utilities\CurlRequest;

class Customer
{
    use HasAttributes;

    public static string $classUrl = 'https://api.paystack.co/customer/';

    public function email(string $email)
    {
        $this->setAttribute('email', $email);

        return $this;
    }

    public function paystackCode(string $paystackCode)
    {
        $this->setAttribute('paystack_code', $paystackCode);

        return $this;
    }

    public static function create(array $fields)
    {
        if (! $fields['email']) {
            throw PaystackEmailIsNull::isNull();
        }

        return (object) (new CurlRequest())(
            'post',
            self::$classUrl,
            $fields
        );
    }

    public function retrieve(string $identifier)
    {
        if (! $identifier) {
            throw new InvalidArgumentException('Paystack email or code is not provided');
        }
        
        $this->setAttributes((new CurlRequest())(
            'get',
            self::$classUrl.$identifier
        ));

        return $this;
    }

    public function update(array $fields)
    {
        if (! $this->paystack_code) {
            throw PaystackCustomerCodeIsEmpty::isNotSet();
        }
        
        $this->setAttributes((new CurlRequest())(
            'put',
            self::$classUrl.$this->paystack_code,
            $fields
        ));

        return $this;
    }

    public static function all(array $fields = [])
    {
        return collect((new CurlRequest())(
            'get',
            self::$classUrl,
            $fields
        ));
    }
}
