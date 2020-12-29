<?php

namespace StarfolkSoftware\PaystackSubscription\Utilities;

use Curl\Curl;
use StarfolkSoftware\PaystackSubscription\Exceptions\ApiKeyInvalid;
use StarfolkSoftware\PaystackSubscription\Exceptions\FailedRequest;
use StarfolkSoftware\PaystackSubscription\PaystackSubscription;

class CurlRequest extends Curl
{
    public Curl $curlInstance;
    private array $paystackOptions;

    public function __construct()
    {
        $this->paystackOptions = PaystackSubscription::paystackOptions();
        
        if (! $this->paystackOptions['api_key']) {
            throw ApiKeyInvalid::isNull();
        }

        $this->curlInstance = new Curl();
        $this->curlInstance->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->curlInstance->setHeaders([
            "Authorization: Bearer ".$this->paystackOptions['api_key'],
            "Cache-Control: no-cache",
        ]);
    }

    public function __invoke(
        string $method,
        string $url,
        array $fields = [],
        array $opts = []
    ) {
        $this->curlInstance->setOpts($opts);
        $result = call_user_func([
            $this->curlInstance,
            $method,
        ], $url, $fields);

        if (is_object($result) && ! $result->status) {
            throw FailedRequest::default($result->message);
        }

        $arrayedResult = is_array($result->data) ? $result->data : $this->toArray($result->data);

        return ! empty($arrayedResult) ? $arrayedResult : [];
    }

    public function toArray(object $data)
    {
        return json_decode(json_encode($data), true);
    }
}
