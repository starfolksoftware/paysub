<?php

namespace StarfolkSoftware\PaystackSubscription\Concerns;

use StarfolkSoftware\PaystackSubscription\Actions\Customer\Create as PaystackCustomerCreate;
use StarfolkSoftware\PaystackSubscription\Actions\Customer\Retrieve as PaystackCustomerRetrieve;
use StarfolkSoftware\PaystackSubscription\Actions\Customer\Update as PaystackCustomerUpdate;
use StarfolkSoftware\PaystackSubscription\Exceptions\CustomerAlreadyCreated;
use StarfolkSoftware\PaystackSubscription\Exceptions\InvalidCustomer;
use StarfolkSoftware\PaystackSubscription\Exceptions\PaystackEmailIsNull;
use StarfolkSoftware\PaystackSubscription\PaystackSubscription;

trait ManagesCustomer
{
    /**
     * Retrieve the Paystack customer ID.
     *
     * @return string|null
     */
    public function paystackCode()
    {
        return $this->paystack_code;
    }

    /**
     * Determine if the entity has a Paystack customer ID.
     *
     * @return bool
     */
    public function hasPaystackCode()
    {
        return ! is_null($this->paystack_code);
    }

    /**
     * Determine if the entity has a Paystack customer ID and throw an exception if not.
     *
     * @return void
     *
     * @throws \StarfolkSoftware\PaystackSubscription\Exceptions\InvalidCustomer
     */
    protected function assertCustomerExists()
    {
        if (! $this->hasPaystackCode()) {
            throw InvalidCustomer::notYetCreated($this);
        }
    }

    /**
     * Create a Paystack customer for the given model.
     *
     * @param  array  $options
     * @return @mixed
     *
     * @throws \StarfolkSoftware\PaystackSubscription\Exceptions\CustomerAlreadyCreated
     */
    public function createAsPaystackCustomer(array $options = [])
    {
        $customerCreate = new PaystackCustomerCreate();

        if ($this->hasPaystackCode()) {
            throw CustomerAlreadyCreated::exists($this);
        }

        if (! array_key_exists('email', $options) && $email = $this->paystackEmail()) {
            $options['email'] = $email;
        }

        $customer = (new PaystackCustomerRetrieve())->execute($this->paystackEmail());

        if ($customer) {
            $this->paystack_code = $customer->customer_code;
            $this->save();

            throw CustomerAlreadyCreated::exists($this);
        }

        // Here we will create the customer instance on Paystack and store the ID of the
        // user from Paystack. This ID will correspond with the Paystack user instances
        // and allow us to retrieve users from Paystack later when we need to work.
        $customer = $customerCreate->execute($this->paystackEmail(), $options['fields']);

        $this->paystack_code = $customer->paystack_code;

        $this->save();

        return $customer;
    }

    /**
     * Update the underlying Paystack customer information for the model.
     *
     * @param  array  $options
     * @return @mixed
     */
    public function updatePaystackCustomer(array $fields, array $options = [])
    {
        $customerUpdate = new PaystackCustomerUpdate();

        if (! array_key_exists('email', $options) && $email = $this->paystackEmail()) {
            $options['email'] = $email;
        }

        if (! array_key_exists('fields', $options)) {
            $options['fields'] = [];
        }

        return $customerUpdate->execute(
            $this->paystackCode(),
            $fields
        );
    }

    /**
     * Get the Paystack customer instance for the current user or create one.
     *
     * @param  array  $options
     * @return @mixed
     */
    public function createOrGetPaystackCustomer(array $options = [])
    {
        if ($this->hasPaystackCode()) {
            return $this->asPaystackCustomer($this->paystackOptions($options));
        }

        return $this->createAsPaystackCustomer($this->paystackOptions($options));
    }

    /**
     * Get the Paystack customer for the model.
     *
     * @return @mixed
     */
    public function asPaystackCustomer(array $options = [])
    {
        $this->assertCustomerExists();

        $customerRead = new PaystackCustomerRetrieve();

        if (! array_key_exists('email', $options) && $email = $this->paystackEmail()) {
            $options['email'] = $email;
        }

        return $customerRead->execute($this->paystackCode());
    }

    /**
     * Get the email address used to create the customer in Paystack.
     *
     * @return string|null
     */
    public function paystackEmail()
    {
        if (! $this->paystack_email) {
            throw PaystackEmailIsNull::isNull();
        }

        return $this->paystack_email;
    }

    /**
     * Get the Paystack supported currency used by the entity.
     *
     * @return string
     */
    public function preferredCurrency()
    {
        return config('paystack-subscription.currency');
    }

    /**
     * Get the default Paystack API options for the current Billable model.
     *
     * @param  array  $options
     * @return array
     */
    public function paystackOptions(array $options = [])
    {
        return PaystackSubscription::paystackOptions($options);
    }
}
