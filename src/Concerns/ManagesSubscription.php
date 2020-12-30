<?php

namespace StarfolkSoftware\PaystackSubscription\Concerns;

use StarfolkSoftware\PaystackSubscription\Subscription;
use StarfolkSoftware\PaystackSubscription\SubscriptionBuilder;

trait ManagesSubscription
{
    /**
     * Begin creating a new subscription.
     *
     * @param  string  $name
     * @param  string  $plan
     * @return StarfolkSoftware\PaystackSubscription\SubscriptionBuilder
     */
    public function newSubscription($name, $plan)
    {
        return new SubscriptionBuilder($this, $name, $plan);
    }

    /**
     * Determine if the Paystack model has a given subscription.
     *
     * @param  string  $name
     * @param  string|null  $plan
     * @return bool
     */
    public function subscribed($paystackCode, $plan = null)
    {
        $subscription = $this->subscription($paystackCode);

        if (! $subscription || ! $subscription->valid()) {
            return false;
        }

        return $plan ? $subscription->hasPlan($plan) : true;
    }

    /**
     * Get a subscription instance by code.
     *
     * @param  string  $code
     * @return \StarfolkSoftware\PaystackSubscription\Subscription|null
     */
    public function subscription($code)
    {
        return $this->subscriptions->where('paystack_code', $code)->first();
    }

    /**
     * Get all of the subscriptions for the Paystack model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, $this->getForeignKey())->orderBy('created_at', 'desc');
    }

    /**
     * Determine if the Paystack model is actively subscribed to one of the given plans.
     *
     * @param  string  $plan
     * @param  string  $code
     * @return bool
     */
    public function subscribedToPlan($plan, $code)
    {
        $subscription = $this->subscription($code);

        if (! $subscription || ! $subscription->valid()) {
            return false;
        }

        if ($subscription->hasPlan($plan)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the entity has a valid subscription on the given plan.
     *
     * @param  string  $plan
     * @return bool
     */
    public function onPlan($plan)
    {
        return ! is_null($this->subscriptions->first(function (Subscription $subscription) use ($plan) {
            return $subscription->valid() && $subscription->hasPlan($plan);
        }));
    }
}
