<?php

namespace StarfolkSoftware\PaystackSubscription\Core;

use InvalidArgumentException;
use StarfolkSoftware\PaystackSubscription\Exceptions\{PaystackCustomerCodeIsEmpty, PaystackPlanCodeIsEmpty};
use StarfolkSoftware\PaystackSubscription\Utilities\CurlRequest;

class Subscription {
    use HasAttributes;

    const OBJECT_NAME = 'subscription';

    public static string $classUrl = 'https://api.paystack.co/subscription/';

    /**
     * @return object
     * @param array $fields
     * 
     * associative array with customer, plan and authorization keys
     */
    public static function create(array $fields) {
        if (! $fields['customer']) {
            throw PaystackCustomerCodeIsEmpty::isNotSet();
        }

        if (! $fields['plan']) {
            throw PaystackPlanCodeIsEmpty::isNotSet();
        }

        if (! $fields['authorization']) {
            throw new InvalidArgumentException('Authorization value must be provided');
        }

        /**
         * @start_date
         * Set the date for the first debit. (ISO 8601 format) 
         * e.g. 2017-05-16T00:30:13+01:00
         */

        return (object) (new CurlRequest())(
            'post',
            self::$classUrl,
            $fields
        );
    }

    /**
     * @return $this
     * @param string $identifier
     * 
     * The subscription ID or code you want to fetch
     */
    public function retrieve(string $identifier) {
        if (! $identifier) {
            throw new InvalidArgumentException('Paystack email or code is not provided');
        }
        
        $this->setAttributes((new CurlRequest())(
            'get',
            self::$classUrl.$identifier
        ));

        return $this;
    }

    public function all(array $fields = []) {
        return collect((new CurlRequest())(
            'get',
            self::$classUrl,
            $fields
        ));
    }

    public function enable($code, $token) {
        if (! $code) {
            throw new InvalidArgumentException('subscription code is not provided');
        }

        if (! $token) {
            throw new InvalidArgumentException('email token is not provided');
        }

        return (new CurlRequest())(
            'post',
            self::$classUrl.'/enable',[
                'code' => $code,
                'token' => $token
            ]
        );
    }

    public function disable($code, $token) {
        if (! $code) {
            throw new InvalidArgumentException('subscription code is not provided');
        }

        if (! $token) {
            throw new InvalidArgumentException('email token is not provided');
        }

        return (new CurlRequest())(
            'post',
            self::$classUrl.'/disable',[
                'code' => $code,
                'token' => $token
            ]
        );
    }
}