<?php

namespace StarfolkSoftware\PaystackSubscription;

use StarfolkSoftware\PaystackSubscription\Utilities\CurlRequest;
use \Exception;

class Plan {
    use HasAttributes;

    public function find($identifier) {
        if (! $identifier) {
            throw new Exception('Paystack plan code or id is not provided');
        }

        $this->setAttributes((new CurlRequest())(
            'get', 
            'https://api.paystack.co/plan/'.$identifier
        ));

        return $this;
    }

    public static function all(int $perPage = 50, int $page = 1) {
        return collect((new CurlRequest())(
            'get', 
            'https://api.paystack.co/plan', [
                'perPage' => $perPage,
                'page' => $page
            ]
        ));
    }
}
