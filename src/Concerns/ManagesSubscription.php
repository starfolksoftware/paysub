<?php

namespace StarfolkSoftware\Paysub\Concerns;

use StarfolkSoftware\Paysub\Models\Plan;
use StarfolkSoftware\Paysub\Models\Subscription;
use StarfolkSoftware\Paysub\SubscriptionBuilder;

trait ManagesSubscription
{
    /**
     * Get all of the subscriptions for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'subscriber_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get a subscription instance by name.
     *
     * @param  string  $name
     * @return \StarfolkSoftware\Paysub\Models\Subscription|null
     */
    public function subscription($name = 'default')
    {
        return $this->subscriptions->where('name', $name)->first();
    }

    /**
     * Get array of emails to send invoice to
     *
     * @return array
     */
    abstract public function invoiceMailables(): array;

    /**
     * Begin creating a new subscription.
     *
     * @param  string  $name
     * @param  Plan|Plan[]  $plan
     * @return \StarfolkSoftware\Paysub\SubscriptionBuilder
     */
    public function newSubscription($name = 'default', Plan $plan)
    {
        return new SubscriptionBuilder($this, $name, $plan);
    }

    /**
     * Determine if the model is on trial.
     * @param string $name
     * @param Plan|null $plan
     *
     * @return bool
     */
    public function onTrial($name = 'default', Plan $plan = null)
    {
        if (func_num_args() === 0 && $this->onGenericTrial()) {
            return true;
        }

        $subscription = $this->subscription($name);

        if (! $subscription || ! $subscription->onTrial()) {
            return false;
        }

        return $plan ? $subscription->hasPlan($plan) : true;
    }

    /**
     * Determine if the model is on a "generic" trial at the model level.
     *
     * @return bool
     */
    public function onGenericTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Get the ending date of the trial.
     *
     * @param string $name
     * @return \Illuminate\Support\Carbon|null
     */
    public function trialEndsAt($name = 'default')
    {
        $subscription = $this->subscription($name);

        if ($subscription) {
            return $subscription->trial_ends_at;
        }

        return $this->trial_ends_at;
    }

    /**
     * Determine if the model has a given subscription.
     *
     * @param string $name
     * @param Plan|null $plan
     * @return bool
     */
    public function subscribed($name = 'default', Plan $plan = null)
    {
        $subscription = $this->subscription($name);

        if (! $subscription || ($subscription && ! $subscription->valid())) {
            return false;
        }

        return $plan ? $subscription->hasPlan($plan) : true;
    }

    /**
     * Determine if the model is actively subscribed to one of the given plans.
     * @param  Plan|Plan[]  $plans
     * @return bool
     */
    public function subscribedToOneOf($plans)
    {
        $subscription = $this->subscription();
        
        if (! $subscription->valid()) {
            return false;
        }

        foreach ((array) $plans as $plan) {
            if ($subscription->hasPlan($plan)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the model is actively subscribed to one of the given plans.
     *
     * @param  Plan|Plan[]  $plans
     * @param  string  $name
     * @return bool
     */
    public function subscribedToPlan($plans, $name = 'default')
    {
        $subscription = $this->subscription($name);

        if (! $subscription || ! $subscription->valid()) {
            return false;
        }

        foreach ((array) $plans as $plan) {
            if ($subscription->hasPlan($plan)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the entity has a valid subscription on the given plan.
     *
     * @param  Plan  $plan
     * @return bool
     */
    public function onPlan(Plan $plan)
    {
        return ! is_null($this->subscriptions->first(function (Subscription $subscription) use ($plan) {
            return $subscription->valid() && $subscription->hasPlan($plan);
        }));
    }
}
