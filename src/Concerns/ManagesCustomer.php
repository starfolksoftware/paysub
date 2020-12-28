<?php

namespace Starfolksoftware\PaystackSubscription\Concerns;

use Starfolksoftware\PaystackSubscription\PaystackSubscription;
use Starfolksoftware\PaystackSubscription\Exceptions\CustomerAlreadyCreated;
use Starfolksoftware\PaystackSubscription\Exceptions\InvalidCustomer;
use Starfolksoftware\PaystackSubscription\Exceptions\PaystackEmailIsNull;
use Starfolksoftware\PaystackSubscription\Actions\Customer\Create as PaystackCustomerCreate;
use Starfolksoftware\PaystackSubscription\Actions\Customer\Update as PaystackCustomerUpdate;
use Starfolksoftware\PaystackSubscription\Actions\Customer\Read as PaystackCustomerRead;

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
     * @throws \Starfolksoftware\PaystackSubscription\Exceptions\InvalidCustomer
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
     * @throws \Starfolksoftware\PaystackSubscription\Exceptions\CustomerAlreadyCreated
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

        $customer = (new PaystackCustomerRead())->execute($this->paystackOptions($options));

        if ($customer) {
            $this->paystack_code = $customer->code;
            $this->save();
            throw CustomerAlreadyCreated::exists($this);
        }

        // Here we will create the customer instance on Paystack and store the ID of the
        // user from Paystack. This ID will correspond with the Paystack user instances
        // and allow us to retrieve users from Paystack later when we need to work.
        $customer = $customerCreate->execute($this->paystackOptions($options));

        $this->paystack_code = $customer->code;

        $this->save();

        return $customer;
    }

    /**
     * Update the underlying Paystack customer information for the model.
     *
     * @param  array  $options
     * @return @mixed
     */
    public function updatePaystackCustomer(array $options = [])
    {
        $customerUpdate = new PaystackCustomerUpdate();

        if (! array_key_exists('email', $options) && $email = $this->paystackEmail()) {
            $options['email'] = $email;
        }

        if (! array_key_exists('fields', $options)) {
            $options['fields'] = [];
        }

        return $customerUpdate->execute(
            $this->paystackOptions($options), $this->paystack_code
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
    public function asPaystackCustomer(array $options)
    {
        $this->assertCustomerExists();

        $customerRead = new PaystackCustomerRead();

        if (! array_key_exists('email', $options) && $email = $this->paystackEmail()) {
            $options['email'] = $email;
        }

        return $customerRead->execute($this->paystackOptions($options), $this->paystack_code);
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
