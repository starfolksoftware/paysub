<?php

namespace StarfolkSoftware\PaystackSubscription;

use InvalidArgumentException;
use StarfolkSoftware\PaystackSubscription\Actions\Subscription\{
    Create as PaystackSubscriptionCreate
};

class SubscriptionBuilder
{
    /**
     * The model that is subscribing.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $owner;

    /**
     * The name of the plan being subscribed to.
     *
     * @var string
     */
    protected $item;

    /**
     * Create a new subscription builder instance.
     *
     * @param  mixed  $owner
     * @param  string  $name
     * @param  string  $plans
     * @return void
     */
    public function __construct($owner, $name, $plan = null)
    {
        $this->name = $name;
        $this->owner = $owner;

        $this->plan($plan);
    }

    /**
     * Set a plan on the subscription builder.
     *
     * @param  string  $plan
     * @param  int  $quantity
     * @return $this
     */
    public function plan($plan, $quantity = 1)
    {
        $options = [
            'plan' => $plan,
            'quantity' => $quantity,
        ];

        $this->item = $options;

        return $this;
    }

    /**
     * Specify the quantity of subscription item.
     *
     * @param  int  $quantity
     * @param  string  $plan
     * @return $this
     */
    public function quantity($plan, $quantity)
    {
        return $this->plan($plan, $quantity);
    }

    /**
     * Add a new Paystack subscription to the Paystack model.
     *
     * @param  array  $customerOptions
     * @param  array  $subscriptionOptions
     * @return \StarfolkSoftware\PaystackSubscription\Subscription
     *
     * @throws InvalidArgumentException
     */
    public function add(array $subscriptionOptions = [])
    {
        return $this->create($subscriptionOptions);
    }

    /**
     * Create a new Paystack subscription.
     *
     * @param  array  $subscriptionOptions
     * @return \StarfolkSoftware\PaystackSubscription\Subscription
     *
     * @throws InvalidArgumentException
     */
    public function create(array $subscriptionOptions = [])
    {
        if (! $subscriptionOptions['authorization']) {
            throw new InvalidArgumentException('authorization code must be provided');
        }

        $customer = $this->getPaystackCustomer();

        $payload = array_merge(
            ['customer' => $customer->customer_code],
            $this->buildPayload(),
            $subscriptionOptions
        );

        $paystackSubscription = (new PaystackSubscriptionCreate())->execute(
            $payload
        );

        /** @var \StarfolkSoftware\PaystackSubscription\Subscription $subscription */
        $subscription = $this->owner->subscriptions()->create([
            'paystack_code' => $paystackSubscription->subscription_code,
            'paystack_status' => $paystackSubscription->status,
            'paystack_plan' => $paystackSubscription->plan,
            'quantity' => $paystackSubscription->quantity,
            'ends_at' => null,
        ]);

        return $subscription;
    }

    /**
     * Get the Paystack customer instance for the current user and payment method.
     *
     * @return \StarfolkSofware\PaystackSubscription\Api\Customer
     */
    protected function getPaystackCustomer()
    {
        return $this->owner->createOrGetPaystackCustomer();
    }

    /**
     * Build the payload for subscription creation.
     *
     * @return array
     */
    protected function buildPayload()
    {
        $payload = array_filter([
            'plan' => $this->item['plan'],
            'quantity' => $this->item['quantity'],
        ]);

        return $payload;
    }
}
