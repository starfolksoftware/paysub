<?php

namespace StarfolkSoftware\PaystackSubscription;

use StarfolkSoftware\PaystackSubscription\Exceptions\{PaystackCustomerCodeIsEmpty, PaystackEmailIsNull, PaystackPlanCodeIsEmpty};
use StarfolkSoftware\PaystackSubscription\Utilities\CurlRequest;
use \Exception;

class Customer
{
    use HasAttributes;

    public function email(string $email) {
        $this->setAttribute('email', $email);

        return $this;
    }

    public function paystackCode(string $paystackCode) {
        $this->setAttribute('paystack_code', $paystackCode);

        return $this;
    }

    public static function create(array $fields) {
        if (! $fields['email']) {
            throw PaystackEmailIsNull::isNull();
        }

        return (object) (new CurlRequest())(
            'post', 
            'https://api.paystack.co/customer',
            $fields
        );
    }

    public function find(string $identifier)
    {
        if (! $identifier) {
            throw new Exception('Paystack email or code is not provided');
        }
        
        $this->setAttributes((new CurlRequest())(
            'get', 
            'https://api.paystack.co/customer/'.$identifier
        ));

        return $this;
    }

    public function update($fields)
    {
        if (! $this->paystack_code) {
            throw PaystackCustomerCodeIsEmpty::isNotSet();
        }
        
        $this->setAttributes((new CurlRequest())(
            'put', 
            'https://api.paystack.co/customer/'.$this->paystack_code,
            $fields
        ));

        return $this;
    }

    public static function all(int $perPage = 50, int $page = 1) {
        return collect((new CurlRequest())(
            'get', 
            'https://api.paystack.co/customer', [
                'perPage' => $perPage,
                'page' => $page
            ]
        ));
    }
}
