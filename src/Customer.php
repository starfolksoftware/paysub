<?php

namespace Starfolksoftware\PaystackSubscription;

use Starfolksoftware\PaystackSubscription\Exceptions\{PaystackCustomerCodeIsEmpty, PaystackEmailIsNull};
use Starfolksoftware\PaystackSubscription\Utilities\CurlRequest;
use stdClass;

class Customer
{
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

    public function __construct() {
        $this->setAttributes(new stdClass());
    }

    public function setAttributes(object $object)
    {
        $this->email = $object->email ?? "";
        $this->code = $object->customer_code ?? "";
        $this->transactions = $object->transactions ?? [];
        $this->subscriptions = $object->subscriptions ?? [];
        $this->authorizations = $object->authorizations ?? [];
        $this->fist_name = $object->first_name ?? "";
        $this->last_name = $object->last_name ?? "";
        $this->phone = $object->phone ?? "";
        $this->metadata = $object->metadata ?? null;
        $this->domain = $object->domain ?? "";
        $this->risk_action = $object->risk_action ?? "";
        $this->id = $object->id ?? 0;
        $this->integration = $object->integration ?? 0;
        $this->created_at = \Carbon\Carbon::parse($object->createdAt ?? null, 'Africa/Lagos');
        $this->updated_at = \Carbon\Carbon::parse($object->updatedAt ?? null, 'Africa/Lagos');
        $this->identified = $object->identified ?? false;
        $this->identifications = $object->identifications ?? null;
    }

    public function firstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    public function lastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    public function email($email)
    {
        $this->email = $email;

        return $this;
    }

    public function phone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function code($code)
    {
        $this->code = $code;

        return $this;
    }

    public function create()
    {
        $result = (new CurlRequest())(
            'post', 
            'https://api.paystack.co/customer', [
                'email' => $this->email,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone' => $this->phone,
            ]
        );
        
        $this->setAttributes($result);

        return $this->id != 0 ? $this : NULL;
    }

    public function find()
    {
        if (! $this->email) {
            throw PaystackEmailIsNull::isNull();
        }

        $result = (new CurlRequest())(
            'get', 
            'https://api.paystack.co/customer/'.$this->email
        );

        
        $this->setAttributes($result);

        return $this->id != 0 ? $this : NULL;
    }

    public function update()
    {
        if (! $this->code) {
            throw PaystackCustomerCodeIsEmpty::isNotSet();
        }

        $result = (new CurlRequest())(
            'put', 
            'https://api.paystack.co/customer/'.$this->code, [
                'email' => $this->email,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone' => $this->phone,
            ]
        );

        
        $this->setAttributes($result);

        return $this->id != 0 ? $this : NULL;
    }
}
