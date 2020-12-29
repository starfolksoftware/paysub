<?php 

namespace Starfolksoftware\PaystackSubscription\Utilities;

use Curl\Curl;
use Starfolksoftware\PaystackSubscription\PaystackSubscription;
use Starfolksoftware\PaystackSubscription\Exceptions\{ApiKeyInvalid, FailedRequest};
use stdClass;

class CurlRequest extends Curl {
    public Curl $curlInstance;
    private array $paystackOptions;

    public function __construct() {
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
        $result = call_user_func(array(
            $this->curlInstance,
            $method
        ), $url, $fields);

        if (is_object($result) && !$result->status) {
            throw FailedRequest::default($result->message);
        }

        return is_object($result) ? $result->data : new stdClass();
    }
}
