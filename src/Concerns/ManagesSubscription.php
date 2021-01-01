<?php

namespace StarfolkSoftware\Paysub\Concerns;

use StarfolkSoftware\Paysub\Models\Plan;
use StarfolkSoftware\Paysub\Models\Subscription;
use StarfolkSoftware\Paysub\SubscriptionBuilder;

trait ManagesSubscription
{
    /**
     * Get all of the subscriptions for the Stripe model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'subscriber_id')->orderBy('created_at', 'desc');
    }

    /**
     * Begin creating a new subscription.
     *
     * @param  string  $name
     * @param  Plan  $plan
     * @return \StarfolkSoftware\Paysub\SubscriptionBuilder
     */
    public function newSubscription(Plan $plan)
    {
        return new SubscriptionBuilder($this, $plan);
    }

    /**
     * Determine if the model is on trial.
     * @param Plan|null $plan
     *
     * @return bool
     */
    public function onTrial(Plan $plan = null)
    {
        if ($this->onGenericTrial()) {
            return true;
        }

        $subscription = $this->subscription();

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
     * @param  Subscription|null  $subscription
     * @return \Illuminate\Support\Carbon|null
     */
    public function trialEndsAt(Subscription $subscription = null)
    {
        if ($subscription) {
            return $subscription->trial_ends_at;
        }

        return $this->trial_ends_at;
    }

    /**
     * Determine if the Stripe model has a given subscription.
     *
     * @param  string|null  $plan
     * @return bool
     */
    public function subscribed(Plan $plan = null)
    {
        $subscription = $this->subscription();

        if (! $subscription || ! $subscription->valid()) {
            return false;
        }

        return $plan ? $subscription->hasPlan($plan) : true;
    }

    /**
     * Get a subscription instance by name.
     *
     * @param  string  $name
     * @return \StarfolkSoftware\Paysub\Models\Subscription|null
     */
    public function subscription()
    {
        return $this->subscriptions->active()->first();
    }

    /**
     * Determine if the Stripe model is actively subscribed to one of the given plans.
     *
     * @param  Plan|Plan[]  $plans
     * @return bool
     */
    public function subscribedToPlan($plans)
    {
        $subscription = $this->subscription();

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
    public function onPlan($plan)
    {
        return ! is_null($this->subscriptions->first(function (Subscription $subscription) use ($plan) {
            return $subscription->valid() && $subscription->hasPlan($plan);
        }));
    }
}
