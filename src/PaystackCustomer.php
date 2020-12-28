<?php

namespace Starfolksoftware\PaystackSubscription;

use Starfolksoftware\PaystackSubscription\Exceptions\{PaystackCustomerCodeIsEmpty};

class PaystackCustomer {
    public string $email;
    public string $code;
    public array $transactions;
    public array $subscriptions;
    public array $authorizations;
    public string $first_name;
    public string $last_name;
    public string $phone;
    public $metadata;
    public string $domain;
    public string $risk_action;
    public int $id;
    public int $integration;
    public \Carbon\Carbon $created_at;
    public \Carbon\Carbon $updated_at;
    public bool $identified;
    public $identifications;

    public string $api_key;

    public function __construct() {
        $this->setAttributes([]);
    }

    public function setAttributes(array $opts) {
        $this->email = $opts['email'] ?? "";
        $this->code = $opts['customer_code'] ?? "";
        $this->transactions = $opts['transactions'] ?? [];
        $this->subscriptions = $opts['subscriptions'] ?? [];
        $this->authorizations = $opts['authorizations'] ?? [];
        $this->fist_name = $opts['first_name'] ?? "";
        $this->last_name = $opts['last_name'] ?? "";
        $this->phone = $opts['phone'] ?? "";
        $this->metadata = $opts['metadata'] ?? null;
        $this->domain = $opts['domain'] ?? "";
        $this->risk_action = $opts['risk_action'] ?? "";
        $this->id = $opts['id'] ?? 0;
        $this->integration = $opts['integration'] ?? 0;
        $this->created_at = \Carbon\Carbon::parse($opts['createdAt'] ?? null, 'Africa/Lagos');
        $this->updated_at = \Carbon\Carbon::parse($opts['updatedAt'] ?? null, 'Africa/Lagos');
        $this->identified = $opts['identified'] ?? false;
        $this->identifications = $opts['identifications'] ?? null;
    }
    
    public function apiKey($apiKey) {
        $this->api_key = $apiKey;

        return $this;
    }

    public function firstName($firstName) {
        $this->first_name = $firstName;

        return $this;
    }

    public function lastName($lastName) {
        $this->last_name = $lastName;

        return $this;
    }

    public function email($email) {
        $this->email = $email;

        return $this;
    }

    public function phone($phone) {
        $this->phone = $phone;

        return $this;
    }

    public function code($code) {
        $this->code = $code;

        return $this;
    }

    public function create() {
        $fields = [
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, 'https://api.paystack.co/customer');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ".$this->api_key,
            "Cache-Control: no-cache",
        ));
        
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        
        $err = curl_error($ch);
        
        //execute post
        $result = json_decode(curl_exec($ch), true);

        if ($result) {
            $this->setAttributes($result['data']);
        }

        curl_close($ch);

        return $err ? null : $this;
    }

    public function find() {
        if (! $this->code && ! $this->email) {
            throw PaystackCustomerCodeIsEmpty::isNotSet();
        }

        $curl = curl_init();
  
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/customer/".($this->code ?? $this->email),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".$this->api_key,
                "Cache-Control: no-cache",
            ),
        ));
        
        $err = curl_error($curl);

        //execute post
        $result = json_decode(curl_exec($curl), true);

        if ($result) {
            $this->setAttributes($result['data']);
        }

        curl_close($curl);

        if ($err || ! $this->code) {
            return null;
        } else if ($this->code) {
            return $this;
        }
    }

    public function update() {
        $fields = [
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
        ];

        if (! $this->code) {
            throw PaystackCustomerCodeIsEmpty::isNotSet();
        }

        $url = "https://api.paystack.co/customer/".$this->code;

        $fields_string = http_build_query($fields);
        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ".$this->api_key,
            "Cache-Control: no-cache",
        ));
        
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        $err = curl_error($ch);
        
        //execute post
        $result = json_decode(curl_exec($ch), true);

        if ($result) {
            $this->setAttributes($result['data']);
        }

        curl_close($ch);

        if ($err || ! $this->code) {
            return null;
        } else if ($this->code) {
            return $this;
        }
    }
}

