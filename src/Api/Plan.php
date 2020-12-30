<?php

namespace StarfolkSoftware\PaystackSubscription\Api;

use InvalidArgumentException;
use StarfolkSoftware\PaystackSubscription\Utilities\CurlRequest;

class Plan
{
    use HasAttributes;

    public static string $classUrl = 'https://api.paystack.co/plan/';

    // public static function create(array $fields) {
    //     if (! $fields['name']) {
    //         throw new InvalidArgumentException('Plan name is not provided');
    //     }

    //     if (! $fields['amount']) {
    //         throw new InvalidArgumentException('Plan amount is not provided');
    //     }

    //     if (! $fields['interval']) {
    //         throw new InvalidArgumentException('Plan interval is not provided');
    //     }

    //     return (object) (new CurlRequest())(
    //         'post',
    //         self::$classUrl,
    //         $fields
    //     );
    // }

    public function retrieve(string $identifier)
    {
        if (! $identifier) {
            throw new InvalidArgumentException('Paystack plan code or id is not provided');
        }

        $this->setAttributes((new CurlRequest())(
            'get',
            self::$classUrl.$identifier
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
